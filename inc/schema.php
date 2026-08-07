<?php
/**
 * Structured data (JSON-LD) for candidates and stories.
 *
 * Complements SEOPress site-level Organization/WebSite schema with CPT-specific
 * Person and Article markup that SEOPress does not currently emit for these types.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print JSON-LD for the current singular candidate or story.
 */
function goshendems_output_cpt_schema() {
	if ( is_admin() || ! is_singular() ) {
		return;
	}

	$schema = null;

	if ( is_singular( 'candidate' ) ) {
		$schema = goshendems_get_candidate_schema( get_the_ID() );
	} elseif ( is_singular( 'story' ) ) {
		$schema = goshendems_get_story_schema( get_the_ID() );
	}

	if ( empty( $schema ) ) {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'goshendems_output_cpt_schema', 30 );

/**
 * Build Person schema for a candidate profile.
 *
 * @param int $post_id Candidate post ID.
 * @return array<string, mixed>|null
 */
function goshendems_get_candidate_schema( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || 'candidate' !== get_post_type( $post_id ) ) {
		return null;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$name = get_the_title( $post_id );
	if ( '' === $name ) {
		return null;
	}

	$race     = (string) get_field( 'race', $post_id );
	$district = (string) get_field( 'district', $post_id );
	$bio      = (string) get_field( 'bio', $post_id );
	$phone    = (string) get_field( 'phone', $post_id );
	$email    = (string) get_field( 'email', $post_id );
	$picture  = (int) get_field( 'picture', $post_id );
	$links    = get_field( 'links', $post_id );

	$job_title_parts = array_filter( array( $race, goshendems_format_candidate_district( $district ) ) );
	$description     = $bio ? wp_strip_all_tags( $bio ) : '';
	if ( strlen( $description ) > 300 ) {
		$description = wp_html_excerpt( $description, 300, '…' );
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Person',
		'name'     => $name,
		'url'      => get_permalink( $post_id ),
	);

	if ( ! empty( $job_title_parts ) ) {
		$schema['jobTitle'] = implode( ' — ', $job_title_parts );
	}

	if ( $description ) {
		$schema['description'] = $description;
	}

	if ( $picture > 0 ) {
		$image_url = wp_get_attachment_image_url( $picture, 'large' );
		if ( $image_url ) {
			$schema['image'] = $image_url;
		}
	}

	if ( $email && is_email( $email ) ) {
		$schema['email'] = sanitize_email( $email );
	}

	if ( $phone ) {
		$schema['telephone'] = $phone;
	}

	$same_as = array();
	if ( is_array( $links ) ) {
		foreach ( $links as $link ) {
			if ( empty( $link['url'] ) ) {
				continue;
			}
			$url = esc_url_raw( $link['url'] );
			if ( $url ) {
				$same_as[] = $url;
			}
		}
	}
	if ( ! empty( $same_as ) ) {
		$schema['sameAs'] = array_values( array_unique( $same_as ) );
	}

	$schema['worksFor'] = array(
		'@type' => 'Organization',
		'name'  => 'Goshen City Democratic Party',
		'url'   => home_url( '/' ),
	);

	return $schema;
}

/**
 * Build Article schema for a story.
 *
 * @param int $post_id Story post ID.
 * @return array<string, mixed>|null
 */
function goshendems_get_story_schema( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || 'story' !== get_post_type( $post_id ) ) {
		return null;
	}

	$headline = get_the_title( $post_id );
	if ( '' === $headline ) {
		return null;
	}

	$description = '';
	if ( function_exists( 'get_field' ) ) {
		$body_rows = get_field( 'body', $post_id );
		if ( is_array( $body_rows ) ) {
			foreach ( $body_rows as $row ) {
				if ( isset( $row['acf_fc_layout'] ) && 'paragraph' === $row['acf_fc_layout'] && ! empty( $row['text'] ) ) {
					$description = wp_strip_all_tags( (string) $row['text'] );
					break;
				}
			}
		}
	}
	if ( '' === $description ) {
		$description = wp_strip_all_tags( get_the_excerpt( $post_id ) );
	}
	if ( strlen( $description ) > 300 ) {
		$description = wp_html_excerpt( $description, 300, '…' );
	}

	$org = array(
		'@type' => 'Organization',
		'name'  => 'Goshen City Democratic Party',
		'url'   => home_url( '/' ),
	);

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => $headline,
		'datePublished'    => get_the_date( DATE_W3C, $post_id ),
		'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
		'author'           => $org,
		'publisher'        => $org,
	);

	if ( $description ) {
		$schema['description'] = $description;
	}

	$image_id = (int) get_post_thumbnail_id( $post_id );
	if ( ! $image_id && function_exists( 'get_field' ) ) {
		$image_id = (int) get_field( 'hero_image', $post_id );
	}
	if ( $image_id > 0 ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $image_url ) {
			$schema['image'] = array( $image_url );
		}
	}

	return $schema;
}
