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
	<link rel="stylesheet" href="https://use.typekit.net/mvz2bfz.css">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'goshendems' ); ?></a>

	<div class="container">
		<header id="masthead" class="site-header">
			<nav id="site-navigation" class="nav" aria-label="Primary">
				<a class="brand" href="/"><img class="logo" src="<?php echo get_template_directory_uri(); ?>/assets/logo_wide.png" alt="Goshen Dems Logo"></a>
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Toggle menu"><img class="hamburger-icon" src="<?php echo get_template_directory_uri(); ?>/assets/hamburger.png" alt="" aria-hidden="true" /><span class="screen-reader-text">Menu</span></button>
				<ul id="primary-menu">
					<li><a href="/about">About</a></li>
					<li><a href="/calendar">Events</a></li>
					<li><a href="/"><img class="logo" src="<?php echo get_template_directory_uri(); ?>/assets/logo_light.png" alt="Goshen Dems Logo"></a></li>
					<li><a href="/stories">Stories</a></li>
					<li><a href="#donate">Donate</a></li>
				</ul>
			</nav>
		</header><!-- #masthead -->
