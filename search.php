<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Goshen_Dems
 */

get_header();
?>

<main id="primary" class="site-main">

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Search Results for: %s', 'goshendems' ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
		</header>

		<div class="stories-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( 'story' === get_post_type() ) {
					get_template_part( 'template-parts/content', 'story-card' );
				} else {
					get_template_part( 'template-parts/content', 'search' );
				}
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( 'Previous', 'goshendems' ),
				'next_text' => __( 'Next', 'goshendems' ),
			)
		);

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>

</main>

<?php
get_footer();
