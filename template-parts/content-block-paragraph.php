<?php
/**
 * Template part for displaying a paragraph content block (Story body).
 *
 * @package Goshen_Dems
 */

$row = get_query_var( 'content_block_row' );
if ( ! $row || empty( $row['text'] ) ) {
	return;
}
?>
<div class="story-body__paragraph">
	<?php echo $row['text']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WYSIWYG content. ?>
</div>
