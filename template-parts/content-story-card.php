<?php
/**
 * Template part for displaying story cards
 *
 * @package Goshen_Dems
 */

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$post    = get_post( $post_id );

if ( ! $post ) {
	return;
}

setup_postdata( $post );

$permalink = get_permalink( $post_id );
$title_id  = 'story-title-' . $post_id;
?>

<article id="post-<?php echo esc_attr( $post_id ); ?>" class="story-card">
	<?php
	$hero = get_field( 'hero_image', $post_id );
	?>
	<div class="story-card__hero">
		<a class="story-card__hero-link" href="<?php echo esc_url( $permalink ); ?>" aria-hidden="true" tabindex="-1">
			<?php
			if ( $hero ) {
				echo wp_get_attachment_image( $hero, 'medium' );
			} else {
				echo '<div class="story-card__placeholder">' . esc_html__( 'No Image', 'goshendems' ) . '</div>';
			}
			?>
		</a>
	</div>
	<div class="story-card__content">
		<h2 id="<?php echo esc_attr( $title_id ); ?>" class="story-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
		</h2>
		<div class="story-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>" class="story-card__date"><?php echo esc_html( get_the_date( '', $post_id ) ); ?></time>
		</div>
		<div class="story-card__excerpt">
			<?php
			$excerpt_html = '';
			$body_rows    = get_field( 'body', $post_id );
			if ( is_array( $body_rows ) ) {
				foreach ( $body_rows as $row ) {
					if ( isset( $row['acf_fc_layout'] ) && 'paragraph' === $row['acf_fc_layout'] && ! empty( $row['text'] ) ) {
						$text = (string) $row['text'];
						if ( preg_match( '/<p[^>]*>(.*?)<\/p>/si', $text, $m ) ) {
							$excerpt_html = $m[0];
						} else {
							$excerpt_html = wpautop( wp_kses_post( $text ) );
						}
						break;
					}
				}
			}
			if ( empty( $excerpt_html ) ) {
				$excerpt_html = get_the_excerpt( $post_id );
			}
			echo wp_kses_post( $excerpt_html );
			?>
		</div>
	</div>
</article>

<?php
wp_reset_postdata();
