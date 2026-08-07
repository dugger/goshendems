<?php
/**
 * Pages — sync ACF hero images to featured image for Open Graph.
 *
 * Home uses group field `hero` → `image`. About (and similar) use `hero_image`.
 * SEOPress reads the WordPress featured image for og:image.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the ACF hero attachment ID for a page, if any.
 *
 * @param int $post_id Page ID.
 * @return int Attachment ID or 0.
 */
function goshendems_get_page_hero_attachment_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return 0;
	}

	$hero_image = get_field( 'hero_image', $post_id );
	if ( is_numeric( $hero_image ) && (int) $hero_image > 0 ) {
		return (int) $hero_image;
	}
	if ( is_array( $hero_image ) && ! empty( $hero_image['ID'] ) ) {
		return (int) $hero_image['ID'];
	}

	$hero = get_field( 'hero', $post_id );
	if ( is_array( $hero ) && ! empty( $hero['image'] ) ) {
		if ( is_numeric( $hero['image'] ) ) {
			return (int) $hero['image'];
		}
		if ( is_array( $hero['image'] ) && ! empty( $hero['image']['ID'] ) ) {
			return (int) $hero['image']['ID'];
		}
	}

	return 0;
}

/**
 * Sync a page's ACF hero image to the WordPress featured image.
 *
 * Only sets a thumbnail when a hero image exists. Pages without a hero
 * (Contact, Calendar) are left unchanged.
 *
 * @param int $post_id Post ID.
 */
function goshendems_sync_page_hero_to_featured_image( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$hero_id = goshendems_get_page_hero_attachment_id( $post_id );
	if ( $hero_id <= 0 ) {
		return;
	}

	$current = (int) get_post_thumbnail_id( $post_id );
	if ( $current !== $hero_id ) {
		set_post_thumbnail( $post_id, $hero_id );
	}
}

/**
 * Keep featured image in sync when a page is saved via ACF.
 *
 * @param int|string $post_id Post ID.
 */
function goshendems_on_page_acf_save( $post_id ) {
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

	goshendems_sync_page_hero_to_featured_image( $post_id );
}
add_action( 'acf/save_post', 'goshendems_on_page_acf_save', 20 );

/**
 * One-time backfill: sync ACF heroes → featured images for existing pages.
 */
function goshendems_backfill_page_featured_images() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( get_option( 'goshendems_page_hero_featured_synced' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$page_ids = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $page_ids as $page_id ) {
		goshendems_sync_page_hero_to_featured_image( (int) $page_id );
	}

	update_option( 'goshendems_page_hero_featured_synced', 1, false );
}
add_action( 'admin_init', 'goshendems_backfill_page_featured_images' );

/**
 * Prefer the theme opengraph size for SEO social images on pages.
 *
 * @param string $size Image size.
 * @return string
 */
function goshendems_page_seopress_social_image_size( $size ) {
	if ( is_page() ) {
		return 'opengraph';
	}
	return $size;
}
add_filter( 'seopress_social_image_size', 'goshendems_page_seopress_social_image_size' );

/**
 * Prefer the theme opengraph size for Yoast OG images on pages.
 *
 * @param string|null $size Image size.
 * @return string|null
 */
function goshendems_page_wpseo_opengraph_image_size( $size ) {
	if ( is_page() ) {
		return 'opengraph';
	}
	return $size;
}
add_filter( 'wpseo_opengraph_image_size', 'goshendems_page_wpseo_opengraph_image_size' );
