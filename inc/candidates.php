<?php
/**
 * Candidates CPT — Open Graph link thumbnails and helpers.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Candidates shown per archive page.
 *
 * @return int
 */
function goshendems_get_candidates_per_page() {
	return 9;
}

/**
 * Register the Candidates ACF options sub-page.
 */
function goshendems_register_candidate_options_page() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Candidates Settings', 'goshendems' ),
			'menu_title'  => __( 'Settings', 'goshendems' ),
			'parent_slug' => 'edit.php?post_type=candidate',
			'menu_slug'   => 'candidate-settings',
			'capability'  => 'edit_posts',
		)
	);
}
add_action( 'acf/init', 'goshendems_register_candidate_options_page' );

/**
 * Published candidate IDs in manual order, then remaining by title.
 *
 * @return array<int, int>
 */
function goshendems_get_ordered_candidate_ids() {
	$ordered_ids = get_field( 'candidates_order', 'option' );
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
			'post_type'              => 'candidate',
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

	return array_merge( $ordered_ids, $unordered_ids );
}

/**
 * Get the adjacent candidate post in manual display order.
 *
 * @param int    $post_id   Current candidate ID.
 * @param string $direction `next` or `prev`.
 * @return WP_Post|null
 */
function goshendems_get_adjacent_candidate_post( $post_id, $direction = 'next' ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return null;
	}

	$ids   = goshendems_get_ordered_candidate_ids();
	$index = array_search( $post_id, $ids, true );

	if ( false === $index ) {
		return null;
	}

	$target_index = ( 'prev' === $direction ) ? $index - 1 : $index + 1;
	if ( ! isset( $ids[ $target_index ] ) ) {
		return null;
	}

	$post = get_post( $ids[ $target_index ] );

	return ( $post instanceof WP_Post ) ? $post : null;
}

/**
 * Hide the internal OG source URL field in the admin.
 *
 * @param array $field ACF field settings.
 * @return array|false
 */
function goshendems_hide_candidate_og_source_url_field( $field ) {
	if ( ! empty( $field['name'] ) && 'og_source_url' === $field['name'] ) {
		return false;
	}

	return $field;
}
add_filter( 'acf/prepare_field', 'goshendems_hide_candidate_og_source_url_field' );

/**
 * Fetch an Open Graph or Twitter Card image URL from a page.
 *
 * @param string $url Page URL.
 * @return string Image URL or empty string.
 */
function goshendems_get_open_graph_image_url( $url ) {
	$url = esc_url_raw( trim( $url ) );
	if ( empty( $url ) ) {
		return '';
	}

	$response = wp_remote_get(
		$url,
		array(
			'timeout'             => 10,
			'limit_response_size' => 512000,
			'user-agent'          => 'GoshenDemocratsBot/1.0 (WordPress; +https://goshendems.org)',
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	$html = wp_remote_retrieve_body( $response );
	if ( empty( $html ) ) {
		return '';
	}

	$head = substr( $html, 0, 50000 );
	$tags = array(
		'/property=["\']og:image(?::secure_url)?["\']\s+content=["\']([^"\']+)["\']/i',
		'/content=["\']([^"\']+)["\']\s+property=["\']og:image(?::secure_url)?["\']/i',
		'/name=["\']twitter:image(?::src)?["\']\s+content=["\']([^"\']+)["\']/i',
		'/content=["\']([^"\']+)["\']\s+name=["\']twitter:image(?::src)?["\']/i',
	);

	foreach ( $tags as $pattern ) {
		if ( preg_match( $pattern, $head, $matches ) ) {
			return goshendems_normalize_candidate_link_image_url(
				html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' ),
				$url
			);
		}
	}

	return '';
}

/**
 * Resolve relative Open Graph image URLs against the source page.
 *
 * @param string $image_url Raw image URL from markup.
 * @param string $page_url  Source page URL.
 * @return string
 */
function goshendems_normalize_candidate_link_image_url( $image_url, $page_url ) {
	$image_url = esc_url_raw( trim( $image_url ) );
	if ( empty( $image_url ) ) {
		return '';
	}

	if ( 0 === strpos( $image_url, '//' ) ) {
		return esc_url_raw( 'https:' . $image_url );
	}

	if ( 0 === strpos( $image_url, '/' ) ) {
		$parsed = wp_parse_url( $page_url );
		if ( ! empty( $parsed['scheme'] ) && ! empty( $parsed['host'] ) ) {
			return esc_url_raw( $parsed['scheme'] . '://' . $parsed['host'] . $image_url );
		}
	}

	return $image_url;
}

/**
 * Download an Open Graph image into the media library.
 *
 * @param string $image_url Remote image URL.
 * @param int    $post_id   Candidate post ID.
 * @return int Attachment ID or 0 on failure.
 */
function goshendems_sideload_candidate_link_image( $image_url, $post_id ) {
	$image_url = esc_url_raw( $image_url );
	if ( empty( $image_url ) || ! $post_id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$attachment_id = media_sideload_image( $image_url, $post_id, null, 'id' );

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	return (int) $attachment_id;
}

/**
 * Populate link thumbnails from Open Graph metadata when a candidate is saved.
 *
 * @param int|string $post_id Post ID.
 */
function goshendems_sync_candidate_link_thumbnails( $post_id ) {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

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

	if ( 'candidate' !== get_post_type( $post_id ) ) {
		return;
	}

	static $running = array();
	if ( isset( $running[ $post_id ] ) ) {
		return;
	}
	$running[ $post_id ] = true;

	$links = get_field( 'links', $post_id );
	if ( ! is_array( $links ) || empty( $links ) ) {
		unset( $running[ $post_id ] );
		return;
	}

	$updated = false;

	foreach ( $links as $index => $link ) {
		if ( ! is_array( $link ) ) {
			continue;
		}

		$url = ! empty( $link['url'] ) ? esc_url_raw( $link['url'] ) : '';
		if ( empty( $url ) ) {
			continue;
		}

		$stored_source = ! empty( $link['og_source_url'] ) ? esc_url_raw( $link['og_source_url'] ) : '';
		$has_thumbnail = ! empty( $link['thumbnail'] );

		if ( $has_thumbnail && $stored_source === $url ) {
			continue;
		}

		$og_image_url = goshendems_get_open_graph_image_url( $url );
		if ( empty( $og_image_url ) ) {
			if ( $stored_source !== $url ) {
				$links[ $index ]['og_source_url'] = $url;
				$links[ $index ]['thumbnail']     = '';
				$updated                          = true;
			}
			continue;
		}

		$attachment_id = goshendems_sideload_candidate_link_image( $og_image_url, $post_id );
		if ( $attachment_id ) {
			$links[ $index ]['thumbnail']     = $attachment_id;
			$links[ $index ]['og_source_url'] = $url;
			$updated                          = true;
		}
	}

	if ( $updated ) {
		update_field( 'links', $links, $post_id );
	}

	unset( $running[ $post_id ] );
}
add_action( 'acf/save_post', 'goshendems_sync_candidate_link_thumbnails', 20 );

/**
 * Paginate the candidates archive.
 *
 * @param WP_Query $query Main query.
 */
function goshendems_candidates_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'candidate' ) ) {
		$ordered_ids = goshendems_get_ordered_candidate_ids();

		$query->set( 'posts_per_page', goshendems_get_candidates_per_page() );
		$query->set( 'post__in', empty( $ordered_ids ) ? array( 0 ) : $ordered_ids );
		$query->set( 'orderby', 'post__in' );
	}
}
add_action( 'pre_get_posts', 'goshendems_candidates_archive_query' );
