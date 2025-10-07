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

	<!doctype html>

    <!-- Hero -->
    <section class="hero" aria-label="Hero banner">
      <?php $hero = get_field('hero'); ?>
	    <?php echo wp_get_attachment_image($hero['image'], 'full'); ?>
      <div class="cta">
        <h1><?php echo $hero['title']; ?></h1>
        <p><?php echo $hero['description']; ?></p>
        <?php if ($hero['button_url']): ?>
          <a class="cta-btn" href="<?php echo $hero['button_url']; ?>">
            <?php echo $hero['button_text']; ?>
          </a> 
        <?php endif; ?>
      </div>
      
    </section>


    <!-- About -->
    <?php $about = get_field('about_section'); ?>
    <section class="about" aria-labelledby="about-heading">
      <div class="thumb" aria-hidden="true"><?php echo wp_get_attachment_image($about['image'], 'full'); ?></div>
      <div>
        <h2 id="about-heading"><?php echo $about['title']; ?></h2>
        <p><?php echo $about['text']; ?></p>
      </div>
    </section>


    <?php
      $stories = get_field('featured_stories'); 
      if ($stories):
    ?>

    <!-- Featured Stories -->
    <section class="stories-grid" aria-label="Featured Stories">
      <?php foreach ($stories as $story): ?>
        <?php get_template_part( 'template-parts/content', 'story-card', array('post_id' => $story) ); ?>
      <?php endforeach; ?>      
    </section>
    <?php endif; ?>

    <!-- Testimonials -->
    <section class="testimonials" aria-label="Testimonials">
      <div id="testimonialText" class="quote" aria-live="polite">
        "This community group helped me find my voice — the events are welcoming and energizing."
      </div>
      <div style="margin-top:12px; color:#06305a; font-weight:700;">— A local neighbor</div>
      <div class="test-controls" aria-hidden="true">
        <button id="prev" title="Previous testimonial" aria-label="Previous">&lsaquo;</button>
        <button id="next" title="Next testimonial" aria-label="Next">&rsaquo;</button>
      </div>
    </section> 
    

  <script>
    // Simple testimonial carousel (3 sample quotes)
    (function(){
      const quotes = [
        "This community group helped me find my voice — the events are welcoming and energizing.",
        "I’ve met so many neighbors and learned how to take action on local issues.",
        "Friendly people, clear goals, and real results. Proud to support them."
      ];
      let idx = 0;
      const el = document.getElementById('testimonialText');
      function show(i){
        el.style.opacity = 0;
        setTimeout(()=> {
          el.textContent = '"' + quotes[i] + '"';
          el.style.opacity = 1;
        }, 220);
      }
      document.getElementById('prev').addEventListener('click', ()=>{
        idx = (idx - 1 + quotes.length) % quotes.length;
        show(idx);
      });
      document.getElementById('next').addEventListener('click', ()=>{
        idx = (idx + 1) % quotes.length;
        show(idx);
      });
      // auto-advance
      setInterval(()=>{ idx = (idx + 1) % quotes.length; show(idx); }, 6000);
    })();
  </script>

<?php
// get_sidebar();
get_footer();
