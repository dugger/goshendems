<?php
/**
 * Template part for displaying a call-to-action content block (Story body).
 *
 * @package Goshen_Dems
 */

$row = get_query_var( 'content_block_row' );
if ( ! $row ) {
	return;
}
?>
<div class="story-body__cta">
	<?php if ( ! empty( $row['heading'] ) ) : ?>
		<h2 class="story-body__cta-heading"><?php echo esc_html( $row['heading'] ); ?></h2>
	<?php endif; ?>
	<?php if ( ! empty( $row['text'] ) ) : ?>
		<div class="story-body__cta-text"><?php echo esc_html( $row['text'] ); ?></div>
	<?php endif; ?>
	<?php if ( ! empty( $row['button_url'] ) && ! empty( $row['button_label'] ) ) : ?>
		<a href="<?php echo esc_url( $row['button_url'] ); ?>" class="story-body__cta-button"><?php echo esc_html( $row['button_label'] ); ?></a>
	<?php endif; ?>
</div>
