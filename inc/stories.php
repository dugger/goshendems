<?php
/**
 * Stories CPT — sync hero image to featured image for Open Graph.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sync a story's ACF hero_image field to the WordPress featured image.
 *
 * @param int $post_id Post ID.
 */
function goshendems_sync_story_hero_to_featured_image( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return;
	}

	if ( 'story' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$hero_id = (int) get_field( 'hero_image', $post_id );
	$current = (int) get_post_thumbnail_id( $post_id );

	if ( $hero_id > 0 ) {
		if ( $current !== $hero_id ) {
			set_post_thumbnail( $post_id, $hero_id );
		}
		return;
	}

	if ( $current > 0 ) {
		delete_post_thumbnail( $post_id );
	}
}

/**
 * Keep featured image in sync when a story is saved via ACF.
 *
 * @param int|string $post_id Post ID.
 */
function goshendems_on_story_acf_save( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	goshendems_sync_story_hero_to_featured_image( $post_id );
}
add_action( 'acf/save_post', 'goshendems_on_story_acf_save', 20 );

/**
 * Hide the Featured Image metabox on story edit screens.
 * Editors manage the image via the ACF Hero Image field.
 */
function goshendems_remove_story_featured_image_metabox() {
	remove_meta_box( 'postimagediv', 'story', 'side' );
}
add_action( 'do_meta_boxes', 'goshendems_remove_story_featured_image_metabox' );

/**
 * One-time backfill: sync hero_image → featured image for existing stories.
 */
function goshendems_backfill_story_featured_images() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ( get_option( 'goshendems_story_hero_featured_synced' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$story_ids = get_posts(
		array(
			'post_type'              => 'story',
			'post_status'            => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $story_ids as $story_id ) {
		goshendems_sync_story_hero_to_featured_image( (int) $story_id );
	}

	update_option( 'goshendems_story_hero_featured_synced', 1, false );
}
add_action( 'admin_init', 'goshendems_backfill_story_featured_images' );

/**
 * Whether the current front-end request is a single story.
 *
 * @return bool
 */
function goshendems_is_singular_story() {
	return is_singular( 'story' );
}

/**
 * Prefer the theme opengraph size for Yoast OG images on stories.
 *
 * @param string|null $size Image size.
 * @return string|null
 */
function goshendems_story_wpseo_opengraph_image_size( $size ) {
	if ( goshendems_is_singular_story() ) {
		return 'opengraph';
	}
	return $size;
}
add_filter( 'wpseo_opengraph_image_size', 'goshendems_story_wpseo_opengraph_image_size' );

/**
 * Prefer the theme opengraph size for SEOPress social images on stories.
 *
 * @param string $size Image size.
 * @return string
 */
function goshendems_story_seopress_social_image_size( $size ) {
	if ( goshendems_is_singular_story() ) {
		return 'opengraph';
	}
	return $size;
}
add_filter( 'seopress_social_image_size', 'goshendems_story_seopress_social_image_size' );
