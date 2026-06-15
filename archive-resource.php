<?php
/**
 * The template for displaying the resources archive.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Goshen_Dems
 */

get_header();

$resources  = goshendems_get_ordered_resources();
$categories = goshendems_get_resource_categories_with_posts();
$intro      = get_field( 'resources_intro', 'option' );
?>

<main id="primary" class="site-main">
	<header class="page-header">
		<h1 class="page-title"><?php post_type_archive_title(); ?></h1>
	</header>

	<?php if ( ! empty( $intro ) ) : ?>
		<div class="resources-intro">
			<?php echo wp_kses_post( $intro ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $categories ) ) : ?>
		<div class="resources-filter" role="toolbar" aria-label="<?php esc_attr_e( 'Filter resources by category', 'goshendems' ); ?>">
			<button type="button" class="resources-filter__button is-active" data-resource-filter="all" aria-pressed="true">
				<?php esc_html_e( 'All', 'goshendems' ); ?>
			</button>
			<?php foreach ( $categories as $category ) : ?>
				<button
					type="button"
					class="resources-filter__button"
					data-resource-filter="<?php echo esc_attr( $category->slug ); ?>"
					aria-pressed="false"
				>
					<?php echo esc_html( $category->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $resources ) ) : ?>
		<div class="resources-list">
			<?php
			foreach ( $resources as $post ) {
				setup_postdata( $post );
				get_template_part( 'template-parts/content', 'resource-card' );
			}
			wp_reset_postdata();
			?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>

<?php
get_footer();
