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
  <div class="container">
    <!-- Hero -->
    <section class="hero hero--short" aria-label="Hero banner">
      <?php
      $hero = get_field( 'hero_image' );
      if ( $hero ) {
        echo wp_get_attachment_image( $hero, 'hero_image' );
      }
      ?>
    </section>

		<div class="story-body">
    <h1><?php the_title(); ?></h1>
    <?php
    $body = get_field( 'body' );
    if ( $body ) {
      echo wp_kses_post( $body );
    }
    ?>
    </div>  
  </div>
</main>

<?php
// get_sidebar();
get_footer();
