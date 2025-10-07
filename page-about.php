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
        $hero = get_field('hero_image');
	      echo wp_get_attachment_image($hero, 'full');
      ?>
    </section>

		<div class="story-body">
    <h1><?php echo the_title(); ?></h1>
    <?php echo get_field('body'); ?>  
    </div>  
  </div>
</main>

<?php
// get_sidebar();
get_footer();
