<?php
/**
 * Template part for a heading plus a grid of location/info cards.
 *
 * @package Goshen_Dems
 */

$goshendems_row = get_query_var( 'content_block_row' );
if ( ! is_array( $goshendems_row ) ) {
	return;
}

$goshendems_cards = array();
if ( ! empty( $goshendems_row['cards'] ) && is_array( $goshendems_row['cards'] ) ) {
	foreach ( $goshendems_row['cards'] as $goshendems_card ) {
		if ( ! is_array( $goshendems_card ) ) {
			continue;
		}
		$goshendems_title = isset( $goshendems_card['title'] ) ? trim( (string) $goshendems_card['title'] ) : '';
		$goshendems_text  = isset( $goshendems_card['text'] ) ? trim( (string) $goshendems_card['text'] ) : '';
		$goshendems_url   = isset( $goshendems_card['link_url'] ) ? trim( (string) $goshendems_card['link_url'] ) : '';
		if ( '' === $goshendems_title && '' === $goshendems_text ) {
			continue;
		}
		$goshendems_action  = '';
		$goshendems_new_tab = false;
		if ( '' !== $goshendems_url ) {
			$goshendems_is_maps = ( false !== strpos( $goshendems_url, 'google.com/maps' ) || false !== strpos( $goshendems_url, 'maps.google.' ) );
			$goshendems_host    = wp_parse_url( $goshendems_url, PHP_URL_HOST );
			$goshendems_home    = wp_parse_url( home_url(), PHP_URL_HOST );
			$goshendems_new_tab = ! $goshendems_host || ! $goshendems_home || 0 !== strcasecmp( (string) $goshendems_host, (string) $goshendems_home );
			$goshendems_action  = $goshendems_is_maps
				? __( 'Get directions', 'goshendems' )
				: __( 'Learn more', 'goshendems' );
		}
		$goshendems_cards[] = array(
			'title'   => $goshendems_title,
			'text'    => $goshendems_text,
			'url'     => $goshendems_url,
			'action'  => $goshendems_action,
			'new_tab' => $goshendems_new_tab,
		);
	}
}

$goshendems_has_heading = ! empty( $goshendems_row['heading'] );
$goshendems_has_intro   = ! empty( $goshendems_row['intro'] );
if ( ! $goshendems_has_heading && ! $goshendems_has_intro && empty( $goshendems_cards ) ) {
	return;
}
?>
<section class="story-body__card-grid">
	<?php if ( $goshendems_has_heading ) : ?>
		<h2 class="story-body__card-grid-heading"><?php echo esc_html( $goshendems_row['heading'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $goshendems_has_intro ) : ?>
		<div class="story-body__card-grid-intro"><?php echo wp_kses_post( $goshendems_row['intro'] ); ?></div>
	<?php endif; ?>
	<?php if ( ! empty( $goshendems_cards ) ) : ?>
		<ul class="story-body__card-grid-list">
			<?php foreach ( $goshendems_cards as $goshendems_card ) : ?>
				<li class="story-body__card-grid-item">
					<?php if ( '' !== $goshendems_card['url'] ) : ?>
						<a class="story-body__card story-body__card--link" href="<?php echo esc_url( $goshendems_card['url'] ); ?>"<?php echo $goshendems_card['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<?php else : ?>
						<div class="story-body__card">
					<?php endif; ?>
						<?php if ( '' !== $goshendems_card['title'] ) : ?>
							<span class="story-body__card-title"><?php echo esc_html( $goshendems_card['title'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $goshendems_card['text'] ) : ?>
							<span class="story-body__card-text"><?php echo esc_html( $goshendems_card['text'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $goshendems_card['action'] ) : ?>
							<span class="story-body__card-action"><?php echo esc_html( $goshendems_card['action'] ); ?></span>
						<?php endif; ?>
					<?php if ( '' !== $goshendems_card['url'] ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>
