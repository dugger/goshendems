<?php
/**
 * The template for displaying all pages
 *
 * Default pages use the same ACF flexible content blocks as stories.
 * Named templates (home, about, calendar, contact) override this file.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Goshen_Dems
 */

get_header();
?>

	<main id="primary" class="site-main">

	<?php
	while ( have_posts() ) :
		the_post();

		$goshendems_hero = get_field( 'hero_image' );
		if ( $goshendems_hero ) :
			?>
	<section class="hero hero--short" aria-label="<?php esc_attr_e( 'Hero banner', 'goshendems' ); ?>">
			<?php echo wp_get_attachment_image( $goshendems_hero, 'hero_image' ); ?>
	</section>
			<?php
		endif;
		?>

	<div class="story-body">
		<h1><?php the_title(); ?></h1>

		<?php
		$goshendems_body = get_field( 'body' );
		if ( is_array( $goshendems_body ) && ! empty( $goshendems_body ) ) {
			foreach ( $goshendems_body as $goshendems_row ) {
				if ( empty( $goshendems_row['acf_fc_layout'] ) ) {
					continue;
				}
				set_query_var( 'content_block_row', $goshendems_row );
				get_template_part( 'template-parts/content-block', $goshendems_row['acf_fc_layout'] );
			}
		} else {
			?>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
			<?php
		}
		?>
	</div>

		<?php
	endwhile;
	?>

	</main>

<?php
get_footer();
