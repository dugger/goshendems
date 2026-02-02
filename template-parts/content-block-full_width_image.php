<?php
/**
 * Template part for displaying a full-width image content block (Story body).
 *
 * @package Goshen_Dems
 */

$row = get_query_var( 'content_block_row' );
if ( ! $row || empty( $row['image'] ) ) {
	return;
}
?>
<div class="story-body__full-width-image">
	<figure>
		<?php echo wp_get_attachment_image( (int) $row['image'], 'full' ); ?>
		<?php if ( ! empty( $row['caption'] ) ) : ?>
			<figcaption><?php echo esc_html( $row['caption'] ); ?></figcaption>
		<?php endif; ?>
	</figure>
</div>
