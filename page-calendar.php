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
  <section class="hero" aria-label="Calendar">
    <?php 
      $calendar = get_field('calendar')[0];
      echo do_shortcode('[calendar id="' . $calendar . '"]');
    ?>
    </section>
</div>
</main>

<?php
// get_sidebar();
get_footer();
