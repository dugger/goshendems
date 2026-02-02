<?php
/**
 * Template part for displaying a pull quote content block (Story body).
 *
 * @package Goshen_Dems
 */

$row = get_query_var( 'content_block_row' );
if ( ! $row || empty( $row['quote'] ) ) {
	return;
}
?>
<blockquote class="story-body__pull-quote">
	<?php echo esc_html( $row['quote'] ); ?>
	<?php if ( ! empty( $row['attribution'] ) ) : ?>
		<cite><?php echo esc_html( $row['attribution'] ); ?></cite>
	<?php endif; ?>
</blockquote>
