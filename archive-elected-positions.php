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
		<?php
		// Get all elected positions
		$args = array(
			'post_type'      => 'elected-positions',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);
		$all_positions = new WP_Query( $args );

		if ( $all_positions->have_posts() ) :
		?>

			<header class="page-header">
				<?php
					echo '<h1 class="page-title">Elected Positions</h1>';
				?>
			</header>

			<?php
			// Get all level terms for filter buttons
			$all_levels = get_terms( array(
				'taxonomy'   => 'level',
				'hide_empty' => false,
			) );

			// Get the active filter from URL query parameter
			$active_level = isset( $_GET['level'] ) ? sanitize_text_field( $_GET['level'] ) : 'all';

			// Display filter buttons
			if ( ! is_wp_error( $all_levels ) && ! empty( $all_levels ) ) :
			?>
				<div class="elected-positions-filters">
					<button class="elected-positions-filter-btn <?php echo ( 'all' === $active_level ) ? 'active' : ''; ?>" data-level="all" aria-label="Show all positions">
						All
					</button>
					<?php foreach ( $all_levels as $level_term ) : ?>
						<?php
						$is_active = ( $active_level === $level_term->slug ) ? 'active' : '';
						?>
						<button class="elected-positions-filter-btn <?php echo esc_attr( $is_active ); ?>" data-level="<?php echo esc_attr( $level_term->slug ); ?>" aria-label="Filter by <?php echo esc_attr( $level_term->name ); ?>">
							<?php echo esc_html( $level_term->name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			// Group positions by level
			$positions_by_level = array();
			while ( $all_positions->have_posts() ) :
				$all_positions->the_post();
				$level_terms = wp_get_post_terms( get_the_ID(), 'level' );
				
				if ( ! empty( $level_terms ) && ! is_wp_error( $level_terms ) ) {
					$level_id = $level_terms[0]->term_id;
					$level_name = $level_terms[0]->name;
					
					if ( ! isset( $positions_by_level[ $level_id ] ) ) {
						$positions_by_level[ $level_id ] = array(
							'name'      => $level_name,
							'positions' => array(),
						);
					}
					
					$positions_by_level[ $level_id ]['positions'][] = get_the_ID();
				} else {
					// Positions without a level go into "Uncategorized"
					if ( ! isset( $positions_by_level[0] ) ) {
						$positions_by_level[0] = array(
							'name'      => 'Uncategorized',
							'positions' => array(),
						);
					}
					$positions_by_level[0]['positions'][] = get_the_ID();
				}
			endwhile;
			wp_reset_postdata();

			// Get all level terms to maintain order
			$all_levels = get_terms( array(
				'taxonomy'   => 'level',
				'hide_empty' => false,
			) );

			// Display positions grouped by level
			if ( ! empty( $positions_by_level ) ) :
			?>
				<div class="elected-positions">
					<?php
					// First, display positions for each level term (in term order)
					if ( ! is_wp_error( $all_levels ) && ! empty( $all_levels ) ) {
						foreach ( $all_levels as $level_term ) {
							if ( isset( $positions_by_level[ $level_term->term_id ] ) ) {
								$level_data = $positions_by_level[ $level_term->term_id ];
								// Hide group initially if it doesn't match the active filter
								$initial_style = ( 'all' !== $active_level && $active_level !== $level_term->slug ) ? ' style="display: none;"' : '';
								?>
								<div class="elected-positions-group" data-level="<?php echo esc_attr( $level_term->slug ); ?>"<?php echo $initial_style; ?>>
									<h2 class="elected-positions-group__title"><?php echo esc_html( $level_data['name'] ); ?></h2>
									<div class="elected-positions-group__positions">
										<?php
										foreach ( $level_data['positions'] as $position_id ) {
											get_template_part( 'template-parts/elected-positions', null, $position_id );
										}
										?>
									</div>
								</div>
								<?php
							}
						}
					}

					// Display uncategorized positions if any
					if ( isset( $positions_by_level[0] ) ) {
						// Hide group initially if it doesn't match the active filter
						$initial_style = ( 'all' !== $active_level && 'uncategorized' !== $active_level ) ? ' style="display: none;"' : '';
						?>
						<div class="elected-positions-group" data-level="uncategorized"<?php echo $initial_style; ?>>
							<h2 class="elected-positions-group__title"><?php echo esc_html( $positions_by_level[0]['name'] ); ?></h2>
							<div class="elected-positions-group__positions">
								<?php
								foreach ( $positions_by_level[0]['positions'] as $position_id ) {
									get_template_part( 'template-parts/elected-positions', null, $position_id );
								}
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			<?php endif; ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

	</main><!-- #main -->

<?php

get_footer();
