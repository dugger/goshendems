<?php
/**
 * Template part for displaying non-story search results
 *
 * @package Goshen_Dems
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result' ); ?>>
	<h2 class="search-result__title">
		<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
	</h2>
	<?php if ( get_the_excerpt() ) : ?>
		<div class="search-result__excerpt">
			<?php echo wp_kses_post( get_the_excerpt() ); ?>
		</div>
	<?php endif; ?>
</article>
