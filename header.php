<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Goshen_Dems
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php // Warm up the font origins before the stylesheets below request from them. ?>
	<link rel="preconnect" href="https://use.typekit.net" crossorigin>
	<link rel="preconnect" href="https://p.typekit.net" crossorigin>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<link rel="stylesheet" href="https://use.typekit.net/mvz2bfz.css">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'goshendems' ); ?></a>

	<div class="container">
		<header id="masthead" class="site-header">
			<div class="site-header__top">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle menu', 'goshendems' ); ?>"><img class="hamburger-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/hamburger.png' ); ?>" width="24" height="24" alt="" aria-hidden="true" /><span class="screen-reader-text"><?php esc_html_e( 'Menu', 'goshendems' ); ?></span></button>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/logo_wide.png' ); ?>" width="180" height="75" alt="Goshen Dems Logo" fetchpriority="high" decoding="async"></a>
				<?php goshendems_social_links(); ?>
			</div>
			<nav id="site-navigation" class="nav" aria-label="Primary">
				<?php goshendems_primary_nav_menu(); ?>
			</nav>
		</header><!-- #masthead -->
