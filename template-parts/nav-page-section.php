<?php
/**
 * One-level section navigation for child pages, shown under the primary nav.
 *
 * The parent lives in the primary menu (e.g. Election Info). This list is
 * the children only.
 *
 * @package Goshen_Dems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_page() ) {
	return;
}

$goshendems_section_children = goshendems_get_page_section_children( get_queried_object_id() );
if ( empty( $goshendems_section_children ) ) {
	return;
}

$goshendems_current_id = (int) get_queried_object_id();
$goshendems_section_id = goshendems_get_page_section_id( $goshendems_current_id );
$goshendems_nav_label  = sprintf(
	/* translators: %s: parent page title */
	__( '%s pages', 'goshendems' ),
	get_the_title( $goshendems_section_id )
);
?>

<nav class="page-section-nav" aria-label="<?php echo esc_attr( $goshendems_nav_label ); ?>">
	<ul class="page-section-nav__list">
		<?php foreach ( $goshendems_section_children as $goshendems_section_page ) : ?>
			<?php
			$goshendems_is_current = ( (int) $goshendems_section_page->ID === $goshendems_current_id );
			?>
		<li class="page-section-nav__item">
			<a
				class="page-section-nav__link<?php echo $goshendems_is_current ? ' page-section-nav__link--current' : ''; ?>"
				href="<?php echo esc_url( get_permalink( $goshendems_section_page ) ); ?>"
				<?php echo $goshendems_is_current ? ' aria-current="page"' : ''; ?>
			><?php echo esc_html( get_the_title( $goshendems_section_page ) ); ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</nav>
