<?php
/**
 * The template for displaying single candidate profiles.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Goshen_Dems
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();

		$post_id = get_the_ID();
		$picture  = get_field( 'picture', $post_id );
		$race     = get_field( 'race', $post_id );
		$district = get_field( 'district', $post_id );
		$bio      = get_field( 'bio', $post_id );
		$phone    = get_field( 'phone', $post_id );
		$email    = get_field( 'email', $post_id );
		$links    = get_field( 'links', $post_id );
		$archive  = get_post_type_archive_link( 'candidate' );
		?>

		<p class="candidate-back-link">
			<a href="<?php echo esc_url( $archive ); ?>">&larr; <?php esc_html_e( 'All Candidates', 'goshendems' ); ?></a>
		</p>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'candidate-profile' ); ?>>
			<div class="candidate-profile__media">
				<?php if ( $picture ) : ?>
					<div class="candidate-profile__photo">
						<?php echo wp_get_attachment_image( $picture, 'large' ); ?>
					</div>
				<?php else : ?>
					<div class="candidate-profile__photo candidate-profile__photo--placeholder" aria-hidden="true">
						<span><?php esc_html_e( 'No Photo', 'goshendems' ); ?></span>
					</div>
				<?php endif; ?>
			</div>

			<div class="candidate-profile__body">
				<header class="candidate-profile__header">
					<h1 class="candidate-profile__name"><?php the_title(); ?></h1>
					<?php if ( ! empty( $race ) || ! empty( $district ) ) : ?>
						<div class="candidate-profile__meta">
							<?php if ( ! empty( $race ) ) : ?>
								<p class="candidate-profile__race"><?php echo esc_html( $race ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $district ) ) : ?>
								<p class="candidate-profile__district"><?php echo esc_html( goshendems_format_candidate_district( $district ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $phone ) || ! empty( $email ) ) : ?>
						<div class="candidate-profile__contact">
							<?php if ( ! empty( $phone ) ) : ?>
								<p class="candidate-profile__phone">
									<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
										<?php echo esc_html( $phone ); ?>
									</a>
								</p>
							<?php endif; ?>
							<?php if ( ! empty( $email ) && is_email( $email ) ) : ?>
								<p class="candidate-profile__email">
									<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>">
										<?php echo esc_html( $email ); ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</header>

				<?php if ( ! empty( $bio ) ) : ?>
					<div class="candidate-profile__bio">
						<h2 class="candidate-profile__section-title"><?php esc_html_e( 'About', 'goshendems' ); ?></h2>
						<?php echo wp_kses_post( wpautop( $bio ) ); ?>
					</div>
				<?php endif; ?>

				<?php if ( is_array( $links ) && ! empty( $links ) ) : ?>
					<section class="candidate-links" aria-labelledby="candidate-links-heading">
						<h2 id="candidate-links-heading" class="candidate-profile__section-title"><?php esc_html_e( 'Links', 'goshendems' ); ?></h2>
						<ul class="candidate-links__grid">
							<?php foreach ( $links as $link ) : ?>
								<?php
								if ( empty( $link['url'] ) || empty( $link['title'] ) ) {
									continue;
								}
								?>
								<li>
									<a
										class="candidate-link-card"
										href="<?php echo esc_url( $link['url'] ); ?>"
										target="_blank"
										rel="noopener noreferrer"
									>
										<span class="candidate-link-card__thumb">
											<?php
											if ( ! empty( $link['thumbnail'] ) ) {
												echo wp_get_attachment_image( $link['thumbnail'], 'medium' );
											} else {
												echo '<span class="candidate-link-card__thumb-placeholder">' . esc_html( $link['title'] ) . '</span>';
											}
											?>
										</span>
										<span class="candidate-link-card__title"><?php echo esc_html( $link['title'] ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
			</div>
		</article>

		<?php
		$prev = goshendems_get_adjacent_candidate_post( $post_id, 'prev' );
		$next = goshendems_get_adjacent_candidate_post( $post_id, 'next' );
		if ( $prev || $next ) :
			?>
			<nav class="candidate-nav" aria-label="<?php esc_attr_e( 'Candidate navigation', 'goshendems' ); ?>">
				<?php if ( $prev ) : ?>
					<a class="candidate-nav__link candidate-nav__link--prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
						<span class="candidate-nav__label"><?php esc_html_e( 'Previous', 'goshendems' ); ?></span>
						<span class="candidate-nav__title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
					</a>
				<?php else : ?>
					<span class="candidate-nav__spacer" aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ( $next ) : ?>
					<a class="candidate-nav__link candidate-nav__link--next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
						<span class="candidate-nav__label"><?php esc_html_e( 'Next', 'goshendems' ); ?></span>
						<span class="candidate-nav__title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
					</a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>

	<?php endwhile; ?>
</main>

<?php
get_footer();
