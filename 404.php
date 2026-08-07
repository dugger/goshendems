<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Goshen_Dems
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="container light-blue">
		<section class="error-404 not-found story-body">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Page not found', 'goshendems' ); ?></h1>
			</header>

			<div class="page-content">
				<p><?php esc_html_e( 'Sorry, we couldn\'t find that page. Try one of these links or search the site.', 'goshendems' ); ?></p>

				<nav class="error-404__links" aria-label="<?php esc_attr_e( 'Helpful links', 'goshendems' ); ?>">
					<a class="error-404__link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'goshendems' ); ?></a>
					<a class="error-404__link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'goshendems' ); ?></a>
					<a class="error-404__link" href="<?php echo esc_url( home_url( '/candidates/' ) ); ?>"><?php esc_html_e( 'Candidates', 'goshendems' ); ?></a>
					<a class="error-404__link" href="<?php echo esc_url( home_url( '/stories/' ) ); ?>"><?php esc_html_e( 'Stories', 'goshendems' ); ?></a>
					<a class="error-404__link" href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>"><?php esc_html_e( 'Events', 'goshendems' ); ?></a>
					<a class="error-404__link" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact', 'goshendems' ); ?></a>
				</nav>

				<?php get_search_form(); ?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
