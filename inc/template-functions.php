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
