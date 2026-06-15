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
 * Social profile URLs shown in the site header.
 */
define( 'GOSHENDEMS_FACEBOOK_URL', 'https://www.facebook.com/groups/goshendems/' );
define( 'GOSHENDEMS_INSTAGRAM_URL', 'https://www.instagram.com/goshendems/' );

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
			'title' => __( 'Resources', 'goshendems' ),
			'url'   => home_url( '/resources/' ),
			'type'  => 'custom',
		),
		array(
			'title' => __( 'Candidates', 'goshendems' ),
			'url'   => home_url( '/candidates/' ),
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

	$titles = wp_list_pluck( $items, 'title' );

	// Re-seed when placeholder links are missing from the default menu.
	if ( ! in_array( 'Resources', $titles, true ) || ! in_array( 'Candidates', $titles, true ) ) {
		return true;
	}

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
 * Update the Resources menu link when it still points to the home page.
 */
function goshendems_maybe_fix_resources_menu_link() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$locations = get_nav_menu_locations();
	if ( empty( $locations['menu-1'] ) ) {
		return;
	}

	$items         = wp_get_nav_menu_items( $locations['menu-1'] );
	$resources_url = home_url( '/resources/' );

	if ( empty( $items ) ) {
		return;
	}

	foreach ( $items as $item ) {
		if ( 'Resources' !== $item->title || untrailingslashit( $item->url ) === untrailingslashit( $resources_url ) ) {
			continue;
		}

		if ( untrailingslashit( $item->url ) !== untrailingslashit( home_url( '/' ) ) ) {
			continue;
		}

		update_post_meta( $item->ID, '_menu_item_url', $resources_url );
	}
}
add_action( 'admin_init', 'goshendems_maybe_fix_resources_menu_link' );

/**
 * Update the Candidates menu link when it still points to the home page.
 */
function goshendems_maybe_fix_candidates_menu_link() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$locations = get_nav_menu_locations();
	if ( empty( $locations['menu-1'] ) ) {
		return;
	}

	$items          = wp_get_nav_menu_items( $locations['menu-1'] );
	$candidates_url = home_url( '/candidates/' );

	if ( empty( $items ) ) {
		return;
	}

	foreach ( $items as $item ) {
		if ( 'Candidates' !== $item->title || untrailingslashit( $item->url ) === untrailingslashit( $candidates_url ) ) {
			continue;
		}

		if ( untrailingslashit( $item->url ) !== untrailingslashit( home_url( '/' ) ) ) {
			continue;
		}

		update_post_meta( $item->ID, '_menu_item_url', $candidates_url );
	}
}
add_action( 'admin_init', 'goshendems_maybe_fix_candidates_menu_link' );

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
 * Social links for the site header.
 *
 * @return array<int, array<string, string>>
 */
function goshendems_get_social_links() {
	return array(
		array(
			'label' => __( 'Facebook', 'goshendems' ),
			'url'   => GOSHENDEMS_FACEBOOK_URL,
			'icon'  => 'facebook',
		),
		array(
			'label' => __( 'Instagram', 'goshendems' ),
			'url'   => GOSHENDEMS_INSTAGRAM_URL,
			'icon'  => 'instagram',
		),
	);
}

/**
 * Output an inline SVG icon for a social network.
 *
 * @param string $icon Icon slug (`facebook` or `instagram`).
 */
function goshendems_social_icon_svg( $icon ) {
	$icons = array(
		'facebook'  => '<svg class="header-social-links__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
		'instagram' => '<svg class="header-social-links__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
	);

	if ( empty( $icons[ $icon ] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup.
	echo $icons[ $icon ];
}

/**
 * Output header social media links.
 */
function goshendems_social_links() {
	$links = goshendems_get_social_links();
	if ( empty( $links ) ) {
		return;
	}

	echo '<div class="header-social-links" role="navigation" aria-label="' . esc_attr__( 'Social media', 'goshendems' ) . '">';

	foreach ( $links as $link ) {
		printf(
			'<a class="header-social-links__link header-social-links__link--%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s">',
			esc_attr( $link['icon'] ),
			esc_url( $link['url'] ),
			esc_attr( $link['label'] )
		);
		goshendems_social_icon_svg( $link['icon'] );
		echo '</a>';
	}

	echo '</div>';
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
