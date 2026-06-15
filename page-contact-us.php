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
  <div class="container light-blue">
    <section class="form">
      <h2><?php the_title(); ?></h2>
      <?php
      $form_intro = get_field( 'form_intro' );
      if ( $form_intro ) {
        echo wp_kses_post( wpautop( $form_intro ) );
      }
      ?>
      <?php
      $form_shortcode = get_field( 'form_shortcode' );
      if ( ! empty( $form_shortcode ) ) {
        echo do_shortcode( $form_shortcode );
      } else {
        echo do_shortcode( '[ninja_form id=1]' );
      }
      ?>
      </section>
  </div>
</main>

<?php
// get_sidebar();
get_footer();
