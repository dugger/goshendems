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

    <?php $hero = get_field( 'hero' ); ?>
    <?php if ( is_array( $hero ) && ! empty( $hero['image'] ) ) : ?>
    <!-- Hero -->
    <section class="hero" aria-label="Hero banner">
	    <?php echo wp_get_attachment_image( $hero['image'], 'full' ); ?>
      <div class="cta">
        <?php if ( ! empty( $hero['title'] ) ) : ?>
          <h1><?php echo esc_html( $hero['title'] ); ?></h1>
        <?php endif; ?>
        <?php if ( ! empty( $hero['description'] ) ) : ?>
          <p><?php echo esc_html( $hero['description'] ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $hero['button_url'] ) && ! empty( $hero['button_text'] ) ) : ?>
          <a class="cta-btn" href="<?php echo esc_url( $hero['button_url'] ); ?>">
            <?php echo esc_html( $hero['button_text'] ); ?>
          </a>
        <?php endif; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php $about = get_field( 'about_section' ); ?>
    <?php if ( is_array( $about ) && ( ! empty( $about['image'] ) || ! empty( $about['title'] ) || ! empty( $about['text'] ) ) ) : ?>
    <!-- About -->
    <section class="about" aria-labelledby="about-heading">
      <?php if ( ! empty( $about['image'] ) ) : ?>
        <div class="thumb" aria-hidden="true"><?php echo wp_get_attachment_image( $about['image'], 'medium' ); ?></div>
      <?php endif; ?>
      <div>
        <?php if ( ! empty( $about['title'] ) ) : ?>
          <h2 id="about-heading"><?php echo esc_html( $about['title'] ); ?></h2>
        <?php endif; ?>
        <?php if ( ! empty( $about['text'] ) ) : ?>
          <p><?php echo wp_kses_post( $about['text'] ); ?></p>
        <?php endif; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php get_template_part( 'template-parts/content', 'featured-stories', array( 'acf_field' => 'featured_stories' ) ); ?>

	</main>

<?php
get_footer();
