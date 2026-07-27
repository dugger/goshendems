<?php
/**
 * The template for displaying the candidates archive.
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
			<h1 class="page-title"><?php post_type_archive_title(); ?></h1>
		</header>

		<?php
		$intro = get_field( 'candidates_intro', 'option' );
		if ( ! empty( $intro ) ) :
			?>
			<div class="candidates-intro">
				<?php echo wp_kses_post( $intro ); ?>
			</div>
		<?php endif; ?>

		<div class="candidates-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'candidate-card' );
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( '&larr; Previous', 'goshendems' ),
				'next_text' => __( 'Next &rarr;', 'goshendems' ),
				'class'     => 'candidates-pagination',
			)
		);

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>
</main>

<?php
get_footer();
