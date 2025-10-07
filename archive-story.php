<?php
/**
 * The template for displaying the stories archive page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Goshen_Dems
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					echo '<h1 class="page-title">Stories</h1>';
				?>
			</header><!-- .page-header -->

			<div class="stories-grid">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
				?>
				<a class="story-card__link" href="<?php echo esc_url( get_permalink() ); ?>">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'story-card' ); ?>>
						<h2 class="story-card__title"><?php the_title(); ?></h2>
						<div class="story-card__meta">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="story-card__date"><?php echo esc_html( get_the_date() ); ?></time>
						</div>
						<div class="story-card__excerpt">
							<?php
							$excerpt_html = '';
							$body_rows = get_field( 'body' );
							if ( is_array( $body_rows ) ) {
								foreach ( $body_rows as $row ) {
									if ( isset( $row['acf_fc_layout'] ) && 'paragraph' === $row['acf_fc_layout'] && ! empty( $row['text'] ) ) {
										$text = (string) $row['text']; // WYSIWYG HTML
										if ( preg_match( '/<p[^>]*>(.*?)<\\/p>/si', $text, $m ) ) {
											$excerpt_html = $m[0]; // First paragraph including tags
										} else {
											$excerpt_html = wpautop( wp_kses_post( $text ) );
										}
										break;
									}
								}
							}
							if ( empty( $excerpt_html ) ) {
								$excerpt_html = get_the_excerpt();
							}
							echo wp_kses_post( $excerpt_html );
							?>
						</div>
					</article>
					</a>
				<?php
			endwhile;
			?>
			</div><!-- .stories-grid -->

			<?php
			// Pagination
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => __( 'Previous', 'goshendems' ),
					'next_text' => __( 'Next', 'goshendems' ),
				)
			);

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php

get_footer();
