<?php
/**
 * Template part for displaying candidate cards on the archive.
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
$title_id  = 'candidate-title-' . $post_id;
$picture   = get_field( 'picture', $post_id );
$race      = get_field( 'race', $post_id );
$bio       = get_field( 'bio', $post_id );
?>

<article id="post-<?php echo esc_attr( $post_id ); ?>" class="candidate-card">
	<a class="candidate-card__photo-link" href="<?php echo esc_url( $permalink ); ?>" aria-hidden="true" tabindex="-1">
		<div class="candidate-card__photo">
			<?php
			if ( $picture ) {
				echo wp_get_attachment_image( $picture, 'medium' );
			} else {
				echo '<div class="candidate-card__placeholder">' . esc_html__( 'No Photo', 'goshendems' ) . '</div>';
			}
			?>
		</div>
	</a>
	<div class="candidate-card__content">
		<h2 id="<?php echo esc_attr( $title_id ); ?>" class="candidate-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
		</h2>
		<?php if ( ! empty( $race ) ) : ?>
			<p class="candidate-card__race"><?php echo esc_html( $race ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $bio ) ) : ?>
			<div class="candidate-card__bio">
				<?php echo wp_kses_post( wpautop( wp_trim_words( wp_strip_all_tags( $bio ), 30, '…' ) ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</article>

<?php
wp_reset_postdata();
