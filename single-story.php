<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Goshen_Dems
 */

get_header();
?>

	<main id="primary" class="site-main">

    <?php $hero = get_field( 'hero_image' ); ?>
    <?php if ( $hero ) : ?>
    <!-- Hero -->
    <section class="hero hero--short" aria-label="Hero banner">
      <?php echo wp_get_attachment_image( $hero, 'full' ); ?>
    </section>
    <?php endif; ?>

		<div class="story-body">
    <h1><?php the_title(); ?></h1>
    <div class="date"><?php echo esc_html( get_the_date() ); ?></div>

    <?php
    $body = get_field( 'body' );
    if ( is_array( $body ) && ! empty( $body ) ) {
      foreach ( $body as $row ) {
        set_query_var( 'content_block_row', $row );
        get_template_part( 'template-parts/content-block', $row['acf_fc_layout'] );
      }
    }
    ?>
		</div>

	</main>

<?php
get_footer();
