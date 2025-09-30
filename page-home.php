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
    <section class="about" aria-labelledby="about-heading">
      <div class="thumb" aria-hidden="true">photo</div>
      <div>
        <h2 id="about-heading">About Goshen Democrats</h2>
        <p>We’re a local group focused on supporting our community through civic engagement, local events, and volunteer efforts. Come to a meeting, lend a hand, or just learn more — every contribution helps.</p>
      </div>
    </section>

    <!-- Action cards -->
    <section class="cards" aria-label="Quick actions">
      <article class="card" aria-labelledby="card1">
        <h3 id="card1">Upcoming Meetings</h3>
        <div style="opacity:0.95;font-size:13px;">See dates & locations</div>
        <a href="#">Free and open →</a>
      </article>

      <article class="card" style="background:linear-gradient(180deg,#4fd26a,#3fb055)" aria-labelledby="card2">
        <h3 id="card2">Where can I do to help?</h3>
        <div style="opacity:0.95;font-size:13px;">Volunteer, canvass, host</div>
        <a href="#">Learn more →</a>
      </article>

      <article class="card" style="background:linear-gradient(180deg,#4bb46a,#2f8a3a)" aria-labelledby="card3">
        <h3 id="card3">Another Call to Action</h3>
        <div style="opacity:0.95;font-size:13px;">Take the next step</div>
        <a href="#">Sign up →</a>
      </article>
    </section>

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
