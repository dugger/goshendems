<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Goshen_Dems
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function goshendems_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'goshendems_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function goshendems_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'goshendems_pingback_header' );

add_image_size("hero_image", 1267, 600, array('center', 'center') );
add_image_size("hero_short", 266, 150, array('center', 'center') );
add_image_size("highlight", 570, 541, array('center', 'center') );
add_image_size("opengraph", 1200, 630, array('center', 'center') );

/**
 * Get OpenGraph image URL with fallback to full size.
 *
 * @param int $image_id Attachment ID.
 * @return string|false Image URL or false if not found.
 */
function goshendems_get_og_image_url( $image_id ) {
	if ( ! $image_id ) {
		return false;
	}

	// Try to get opengraph size first
	$image = wp_get_attachment_image_src( $image_id, 'opengraph' );
	if ( $image && ! empty( $image[0] ) ) {
		return $image[0];
	}

	// Fallback to full size
	$image = wp_get_attachment_image_src( $image_id, 'full' );
	if ( $image && ! empty( $image[0] ) ) {
		return $image[0];
	}

	return false;
}

/**
 * Extract description from story body field.
 *
 * @param int $post_id Post ID.
 * @return string Description text.
 */
function goshendems_get_story_description( $post_id ) {
	$body = get_field( 'body', $post_id );

	if ( ! $body || ! is_array( $body ) ) {
		return '';
	}

	// Get first paragraph from body flexible content
	foreach ( $body as $row ) {
		if ( $row['acf_fc_layout'] == 'paragraph' && ! empty( $row['text'] ) ) {
			// Strip HTML tags and get first ~160 characters
			$text = wp_strip_all_tags( $row['text'] );
			$text = wp_trim_words( $text, 25, '' ); // ~160 chars
			return $text;
		}
	}

	return '';
}

/**
 * Output OpenGraph meta tags in the head.
 */
function goshendems_opengraph_tags() {
	$og_title = '';
	$og_description = '';
	$og_image = '';
	$og_type = 'website';
	$og_url = '';

	// Get current URL
	$og_url = ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	// Home page
	if ( is_front_page() || is_page_template( 'page-home.php' ) ) {
		$hero = get_field( 'hero' );

		if ( $hero ) {
			$og_title = ! empty( $hero['title'] ) ? $hero['title'] : get_bloginfo( 'name' );
			$og_description = ! empty( $hero['description'] ) ? $hero['description'] : get_bloginfo( 'description' );

			if ( ! empty( $hero['image'] ) ) {
				$og_image = goshendems_get_og_image_url( $hero['image'] );
			}
		} else {
			$og_title = get_bloginfo( 'name' );
			$og_description = get_bloginfo( 'description' );
		}
	}
	// Story posts
	elseif ( is_singular( 'story' ) ) {
		$post_id = get_the_ID();
		$og_type = 'article';
		$og_title = get_the_title();

		// Try to get description from body field
		$og_description = goshendems_get_story_description( $post_id );

		// Fallback to post excerpt if no description from body
		if ( empty( $og_description ) ) {
			$og_description = get_the_excerpt();
		}

		// Fallback to site description if still empty
		if ( empty( $og_description ) ) {
			$og_description = get_bloginfo( 'description' );
		}

		// Get hero image
		$hero_image = get_field( 'hero_image', $post_id );
		if ( $hero_image ) {
			$og_image = goshendems_get_og_image_url( $hero_image );
		}

		// Fallback to featured image
		if ( ! $og_image && has_post_thumbnail( $post_id ) ) {
			$og_image = goshendems_get_og_image_url( get_post_thumbnail_id( $post_id ) );
		}
	}
	// Other pages/posts
	else {
		$post_id = get_the_ID();
		$og_title = get_the_title();
		$og_description = get_the_excerpt();

		// Fallback to site description if no excerpt
		if ( empty( $og_description ) ) {
			$og_description = get_bloginfo( 'description' );
		}

		// Try featured image
		if ( has_post_thumbnail( $post_id ) ) {
			$og_image = goshendems_get_og_image_url( get_post_thumbnail_id( $post_id ) );
		}
	}

	// Final fallbacks
	if ( empty( $og_title ) ) {
		$og_title = get_bloginfo( 'name' );
	}

	if ( empty( $og_description ) ) {
		$og_description = get_bloginfo( 'description' );
	}

	// Fallback to site logo if no image found
	if ( empty( $og_image ) ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$og_image = goshendems_get_og_image_url( $custom_logo_id );
		}
	}

	// Ensure image URL is absolute
	if ( $og_image && strpos( $og_image, 'http' ) !== 0 ) {
		$og_image = home_url( $og_image );
	}

	// Output meta tags
	if ( ! empty( $og_title ) ) {
		echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
	}

	if ( ! empty( $og_description ) ) {
		echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '" />' . "\n";
	}

	if ( ! empty( $og_image ) ) {
		echo '<meta property="og:image" content="' . esc_url( $og_image ) . '" />' . "\n";
	}

	if ( ! empty( $og_url ) ) {
		echo '<meta property="og:url" content="' . esc_url( $og_url ) . '" />' . "\n";
	}

	echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
}
add_action( 'wp_head', 'goshendems_opengraph_tags', 5 );