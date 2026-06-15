<?php
/**
 * ACF custom location rule: match pages by slug instead of ID.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the page slug location rule with ACF.
 */
function goshendems_register_acf_page_slug_location() {
	if ( ! function_exists( 'acf_register_location_type' ) ) {
		return;
	}

	acf_register_location_type( 'Goshendems_ACF_Location_Page_Slug' );
}
add_action( 'acf/init', 'goshendems_register_acf_page_slug_location' );

/**
 * ACF location rule for page slug matching.
 */
class Goshendems_ACF_Location_Page_Slug extends ACF_Location {

	/**
	 * Initialize location rule metadata.
	 */
	public function initialize() {
		$this->name     = 'page_slug';
		$this->label    = __( 'Page Slug', 'goshendems' );
		$this->category = 'page';
	}

	/**
	 * Match the current screen against a location rule value.
	 *
	 * @param array $rule        Location rule.
	 * @param array $screen      Screen args.
	 * @param array $field_group Field group.
	 * @return bool
	 */
	public function match( $rule, $screen, $field_group ) {
		if ( empty( $screen['post_id'] ) ) {
			return false;
		}

		$post = get_post( (int) $screen['post_id'] );
		if ( ! $post || 'page' !== $post->post_type ) {
			return false;
		}

		$result = ( $post->post_name === $rule['value'] );

		if ( '!=' === $rule['operator'] ) {
			return ! $result;
		}

		return $result;
	}

	/**
	 * Return page slug choices for the location rule dropdown.
	 *
	 * @param array $rule Location rule.
	 * @return array
	 */
	public function get_values( $rule ) {
		$choices = array();
		$pages   = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $pages as $page ) {
			$choices[ $page->post_name ] = $page->post_title;
		}

		return $choices;
	}
}
