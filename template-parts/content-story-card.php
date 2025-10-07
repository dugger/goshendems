<?php
/**
 * Template part for displaying story cards
 *
 * @package Goshen_Dems
 */

// Get the post ID - either current post or passed parameter
$post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
$post = get_post($post_id);

if (!$post) {
    return;
}

// Setup post data
setup_postdata($post);
?>

<a class="story-card__link" href="<?php echo esc_url(get_permalink($post_id)); ?>">
    <div class="story-card">
        <?php 
        $hero = get_field('hero_image', $post_id);
        echo '<div class="story-card__hero">';
        if ($hero) {
            echo wp_get_attachment_image($hero, 'medium');
        } else {
            echo '<div class="story-card__placeholder">No Image</div>';
        }
        echo '</div>';
        ?>
        <article id="post-<?php echo $post_id; ?>" class="story-card__content">
            <h2 class="story-card__title"><?php echo get_the_title($post_id); ?></h2>
            <div class="story-card__meta">
                <time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>" class="story-card__date"><?php echo esc_html(get_the_date('', $post_id)); ?></time>
            </div>
            <div class="story-card__excerpt">
                <?php
                $excerpt_html = '';
                $body_rows = get_field('body', $post_id);
                if (is_array($body_rows)) {
                    foreach ($body_rows as $row) {
                        if (isset($row['acf_fc_layout']) && 'paragraph' === $row['acf_fc_layout'] && !empty($row['text'])) {
                            $text = (string) $row['text']; // WYSIWYG HTML
                            if (preg_match('/<p[^>]*>(.*?)<\/p>/si', $text, $m)) {
                                $excerpt_html = $m[0]; // First paragraph including tags
                            } else {
                                $excerpt_html = wpautop(wp_kses_post($text));
                            }
                            break;
                        }
                    }
                }
                if (empty($excerpt_html)) {
                    $excerpt_html = get_the_excerpt($post_id);
                }
                echo wp_kses_post($excerpt_html);
                ?>
            </div>
        </article>
    </div>
</a>

<?php
// Reset post data
wp_reset_postdata();
?>
