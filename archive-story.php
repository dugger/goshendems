<?php
/**
 * The template for displaying the stories archive page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Goshen_Dems
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					echo '<h1 class="page-title">Stories</h1>';
				?>
			</header><!-- .page-header -->

			<div class="stories-grid">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'story-card' );
			endwhile;
			?>
			</div><!-- .stories-grid -->

			<?php
			// Pagination
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

	</main><!-- #main -->

<?php

get_footer();
