<?php
/**
 * Template part for displaying a video embed content block (Story body).
 *
 * @package Goshen_Dems
 */

$row = get_query_var( 'content_block_row' );
if ( ! $row || empty( $row['url'] ) ) {
	return;
}

$embed = wp_oembed_get( $row['url'] );
if ( ! $embed ) {
	return;
}
?>
<div class="story-body__video">
	<?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oEmbed output. ?>
</div>
