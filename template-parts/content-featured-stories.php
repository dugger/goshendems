<?php
/**
 * Template part for displaying featured stories section
 *
 * @package Goshen_Dems
 */

// Get the ACF field name from args (required)
if (!isset($args['acf_field']) || empty($args['acf_field'])) {
  return;
}
$acf_field = $args['acf_field'];

// Get featured stories from ACF (maintains order from relationship field)
$featured_stories = get_field($acf_field); 

// Ensure we have an array
if (!is_array($featured_stories)) {
  $featured_stories = array();
}

// If we have less than 3 stories, fill with most recent stories
if (count($featured_stories) < 3) {
  $needed = 3 - count($featured_stories);
  
  // Get IDs of already selected stories to exclude duplicates
  $exclude_ids = !empty($featured_stories) ? $featured_stories : array();
  
  // Query for most recent stories, excluding already selected ones
  $recent_stories = get_posts(array(
    'post_type' => 'story',
    'post_status' => 'publish',
    'posts_per_page' => $needed,
    'post__not_in' => $exclude_ids,
    'orderby' => 'date',
    'order' => 'DESC'
  ));
  
  // Extract IDs from recent stories
  $recent_ids = array();
  foreach ($recent_stories as $story) {
    $recent_ids[] = $story->ID;
  }
  
  // Merge featured stories (in their ACF order) with recent stories
  $stories = array_merge($featured_stories, $recent_ids);
  
  // Limit to 3 total
  $stories = array_slice($stories, 0, 3);
} else {
  // Already have 3 or more, just use the featured ones (limit to 3)
  $stories = array_slice($featured_stories, 0, 3);
}

if (!empty($stories)):
?>

<!-- Featured Stories -->
<section class="stories-grid stories-grid--featured" aria-label="Featured Stories">
  <?php foreach ($stories as $story): ?>
    <?php get_template_part( 'template-parts/content', 'story-card', array('post_id' => $story) ); ?>
  <?php endforeach; ?>      
</section>
<?php endif; ?>

