<?php
/**
 * Resource card for the archive list.
 *
 * @package Goshen_Dems
 */

$resource_url = goshendems_get_resource_url( get_the_ID() );
if ( ! $resource_url ) {
	return;
}

$terms      = get_the_terms( get_the_ID(), 'resource-category' );
$term_slugs = array();
if ( is_array( $terms ) && ! is_wp_error( $terms ) ) {
	$term_slugs = wp_list_pluck( $terms, 'slug' );
}

$anchor_id = goshendems_get_resource_anchor_id( get_post() );
?>

<article
	id="<?php echo esc_attr( $anchor_id ); ?>"
	class="resource-card"
	data-resource-categories="<?php echo esc_attr( implode( ' ', $term_slugs ) ); ?>"
>
	<div class="resource-card__body">
		<h2 class="resource-card__title">
			<a
				class="resource-card__link"
				href="<?php echo esc_url( $resource_url ); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<?php the_title(); ?>
			</a>
		</h2>
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<div class="resource-card__badges" aria-label="<?php esc_attr_e( 'Categories', 'goshendems' ); ?>">
				<?php foreach ( $terms as $term ) : ?>
					<span class="resource-card__badge"><?php echo esc_html( $term->name ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
