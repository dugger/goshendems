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
    <section class="calendar" aria-labelledby="calendar-heading">
      <h1 id="calendar-heading"><?php the_title(); ?></h1>
      <?php
      $calendar_field = get_field( 'calendar' );
      if ( is_array( $calendar_field ) && ! empty( $calendar_field[0] ) ) {
        echo do_shortcode( '[calendar id="' . absint( $calendar_field[0] ) . '"]' );
      }
      ?>
    </section>
</div>
</main>

<?php
// get_sidebar();
get_footer();
