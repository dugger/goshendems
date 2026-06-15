<?php
/**
 * Resources CPT — helpers, options page, redirects, and queries.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Resources ACF options sub-page.
 */
function goshendems_register_resource_options_page() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Resources Settings', 'goshendems' ),
			'menu_title'  => __( 'Settings', 'goshendems' ),
			'parent_slug' => 'edit.php?post_type=resource',
			'menu_slug'   => 'resource-settings',
			'capability'  => 'edit_posts',
		)
	);
}
add_action( 'acf/init', 'goshendems_register_resource_options_page' );

/**
 * Get published resources in manual order (options relationship), then remaining by title.
 *
 * @return array<int, WP_Post>
 */
function goshendems_get_ordered_resources() {
	$ordered_ids = get_field( 'resources_order', 'option' );
	if ( ! is_array( $ordered_ids ) ) {
		$ordered_ids = array();
	}

	$ordered_ids = array_values(
		array_unique(
			array_filter(
				array_map( 'intval', $ordered_ids )
			)
		)
	);

	$all_ids = get_posts(
		array(
			'post_type'              => 'resource',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$unordered_ids = array_values( array_diff( $all_ids, $ordered_ids ) );
	$final_ids     = array_merge( $ordered_ids, $unordered_ids );

	if ( empty( $final_ids ) ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'resource',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__in'       => $final_ids,
			'orderby'        => 'post__in',
		)
	);
}

/**
 * Resolve the front-end link URL for a resource.
 *
 * @param int $post_id Resource post ID.
 * @return string|false URL or false when not configured.
 */
function goshendems_get_resource_url( $post_id ) {
	$type = get_field( 'resource_type', $post_id );

	if ( 'external_link' === $type ) {
		$url = get_field( 'external_url', $post_id );
		return ! empty( $url ) ? $url : false;
	}

	$file = get_field( 'file', $post_id );
	if ( is_array( $file ) && ! empty( $file['url'] ) ) {
		return $file['url'];
	}

	return false;
}

/**
 * Resource categories that have at least one published resource.
 *
 * @return array<int, WP_Term>
 */
function goshendems_get_resource_categories_with_posts() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'resource-category',
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	return $terms;
}

/**
 * Archive anchor ID for a resource post.
 *
 * @param int|WP_Post $post Resource post object or ID.
 * @return string
 */
function goshendems_get_resource_anchor_id( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	return 'resource-' . $post->post_name;
}

/**
 * Redirect single resource URLs to the archive anchored to that resource.
 */
function goshendems_redirect_resource_singles() {
	if ( ! is_singular( 'resource' ) ) {
		return;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$archive_url = get_post_type_archive_link( 'resource' );
	if ( ! $archive_url ) {
		return;
	}

	wp_safe_redirect( $archive_url . '#' . goshendems_get_resource_anchor_id( $post ), 301 );
	exit;
}
add_action( 'template_redirect', 'goshendems_redirect_resource_singles' );

/**
 * Show all resources on the archive (no pagination).
 *
 * @param WP_Query $query Main query.
 */
function goshendems_resources_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'resource' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
}
add_action( 'pre_get_posts', 'goshendems_resources_archive_query' );

/**
 * Enqueue archive filter script.
 */
function goshendems_resources_scripts() {
	if ( ! is_post_type_archive( 'resource' ) ) {
		return;
	}

	wp_enqueue_script(
		'goshendems-resources-filter',
		get_template_directory_uri() . '/js/resources-filter.js',
		array(),
		_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'goshendems_resources_scripts' );
