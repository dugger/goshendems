<?php
/**
 * Primary navigation — WordPress menus integration.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ActBlue donate URL used in the default primary menu.
 */
define( 'GOSHENDEMS_DONATE_URL', 'https://secure.actblue.com/donate/goshen-city-democratic-party-1' );

/**
 * Default primary menu item definitions.
 *
 * @return array<int, array<string, mixed>>
 */
function goshendems_get_default_primary_menu_items() {
	return array(
		array(
			'title' => __( 'About', 'goshendems' ),
			'slug'  => 'about',
			'type'  => 'page',
		),
		array(
			'title' => __( 'Events', 'goshendems' ),
			'slug'  => 'calendar',
			'type'  => 'page',
		),
		array(
			'title' => __( 'Stories', 'goshendems' ),
			'url'   => home_url( '/stories/' ),
			'type'  => 'custom',
		),
		array(
			'title' => __( 'Contact', 'goshendems' ),
			'slug'  => 'contact-us',
			'type'  => 'page',
		),
		array(
			'title'  => __( 'Donate', 'goshendems' ),
			'url'    => GOSHENDEMS_DONATE_URL,
			'type'   => 'custom',
			'target' => '_blank',
			'classes'=> 'menu-item-donate',
		),
	);
}

/**
 * Create and assign the default Primary menu when none is configured.
 *
 * @return int|false Menu term ID or false on failure.
 */
function goshendems_setup_primary_menu() {
	if ( has_nav_menu( 'menu-1' ) && ! goshendems_primary_menu_is_placeholder() ) {
		return false;
	}

	$menu_name = 'Primary';
	$menu      = wp_get_nav_menu_object( $menu_name );

	if ( $menu ) {
		$menu_id = (int) $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $menu_name );
	}

	if ( is_wp_error( $menu_id ) || ! $menu_id ) {
		return false;
	}

	goshendems_seed_primary_menu_items( $menu_id );

	$locations           = get_theme_mod( 'nav_menu_locations', array() );
	$locations['menu-1'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	return $menu_id;
}

/**
 * Whether the assigned Primary menu is empty or still using placeholder items.
 *
 * @return bool
 */
function goshendems_primary_menu_is_placeholder() {
	$locations = get_nav_menu_locations();
	if ( empty( $locations['menu-1'] ) ) {
		return true;
	}

	$items = wp_get_nav_menu_items( $locations['menu-1'] );
	if ( empty( $items ) ) {
		return true;
	}

	$titles   = wp_list_pluck( $items, 'title' );
	$expected = array( 'About', 'Events', 'Stories', 'Contact', 'Donate' );
	$found    = array_intersect( $expected, $titles );

	return count( $found ) < 3;
}

/**
 * Replace all items in a menu with the default Primary links.
 *
 * @param int $menu_id Menu term ID.
 */
function goshendems_seed_primary_menu_items( $menu_id ) {
	$items = wp_get_nav_menu_items( $menu_id );
	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}

	foreach ( goshendems_get_default_primary_menu_items() as $item ) {
		$args = array(
			'menu-item-title'  => $item['title'],
			'menu-item-status' => 'publish',
		);

		if ( 'page' === $item['type'] ) {
			$page = get_page_by_path( $item['slug'] );
			if ( ! $page ) {
				continue;
			}
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $page->ID;
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = $item['url'];
			if ( ! empty( $item['target'] ) ) {
				$args['menu-item-target'] = $item['target'];
			}
			if ( ! empty( $item['classes'] ) ) {
				$args['menu-item-classes'] = $item['classes'];
			}
		}

		wp_update_nav_menu_item( $menu_id, 0, $args );
	}
}

/**
 * Seed or refresh the Primary menu when needed.
 */
function goshendems_maybe_setup_primary_menu() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	if ( ! has_nav_menu( 'menu-1' ) || goshendems_primary_menu_is_placeholder() ) {
		goshendems_setup_primary_menu();
	}
}
add_action( 'after_switch_theme', 'goshendems_setup_primary_menu' );
add_action( 'admin_init', 'goshendems_maybe_setup_primary_menu' );

/**
 * Prepend the light logo item shown at the top of the mobile menu drawer.
 *
 * @param string   $items Menu HTML.
 * @param stdClass $args  Menu arguments.
 * @return string
 */
function goshendems_prepend_mobile_logo_menu_item( $items, $args ) {
	if ( empty( $args->theme_location ) || 'menu-1' !== $args->theme_location ) {
		return $items;
	}

	$logo_url = esc_url( get_template_directory_uri() . '/assets/logo_light.png' );
	$home_url = esc_url( home_url( '/' ) );

	$logo_item  = '<li class="logo-menu-item menu-item menu-item-type-custom menu-item-home">';
	$logo_item .= '<a href="' . $home_url . '">';
	$logo_item .= '<img class="logo" src="' . $logo_url . '" alt="' . esc_attr__( 'Goshen Dems Logo', 'goshendems' ) . '">';
	$logo_item .= '</a></li>';

	return $logo_item . $items;
}
add_filter( 'wp_nav_menu_items', 'goshendems_prepend_mobile_logo_menu_item', 10, 2 );

/**
 * Add rel="noopener noreferrer" to menu links that open in a new tab.
 *
 * @param array    $atts Menu link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args Menu arguments.
 * @return array
 */
function goshendems_nav_menu_link_attributes( $atts, $item, $args ) {
	if ( ! empty( $atts['target'] ) && '_blank' === $atts['target'] ) {
		$atts['rel'] = 'noopener noreferrer';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'goshendems_nav_menu_link_attributes', 10, 3 );

/**
 * Fallback markup when no Primary menu is assigned.
 *
 * @param array $args wp_nav_menu() arguments.
 */
function goshendems_primary_menu_fallback( $args ) {
	if ( empty( $args['menu_id'] ) ) {
		return;
	}

	echo '<ul id="' . esc_attr( $args['menu_id'] ) . '">';

	$logo_url = esc_url( get_template_directory_uri() . '/assets/logo_light.png' );
	echo '<li class="logo-menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">';
	echo '<img class="logo" src="' . $logo_url . '" alt="' . esc_attr__( 'Goshen Dems Logo', 'goshendems' ) . '"></a></li>';

	foreach ( goshendems_get_default_primary_menu_items() as $item ) {
		$url = '';
		if ( 'page' === $item['type'] ) {
			$page = get_page_by_path( $item['slug'] );
			if ( $page ) {
				$url = get_permalink( $page );
			}
		} else {
			$url = $item['url'];
		}

		if ( empty( $url ) ) {
			continue;
		}

		$class = ! empty( $item['classes'] ) ? ' class="' . esc_attr( $item['classes'] ) . '"' : '';
		echo '<li' . $class . '><a href="' . esc_url( $url ) . '"';

		if ( ! empty( $item['target'] ) ) {
			echo ' target="' . esc_attr( $item['target'] ) . '" rel="noopener noreferrer"';
		}

		echo '>' . esc_html( $item['title'] ) . '</a></li>';
	}

	echo '</ul>';
}

/**
 * Output the primary navigation menu.
 */
function goshendems_primary_nav_menu() {
	$args = array(
		'theme_location' => 'menu-1',
		'menu_id'        => 'primary-menu',
		'menu_class'     => '',
		'container'      => false,
		'fallback_cb'    => 'goshendems_primary_menu_fallback',
		'depth'          => 1,
	);

	if ( goshendems_primary_menu_is_placeholder() ) {
		goshendems_primary_menu_fallback( $args );
		return;
	}

	wp_nav_menu( $args );
}
