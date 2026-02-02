<?php
/**
 * Goshen Dems functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Goshen_Dems
 */

if ( ! defined( '_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_VERSION', '1.1.1' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function goshendems_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Goshen Dems, use a find and replace
		* to change 'goshendems' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'goshendems', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'goshendems' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'goshendems_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'goshendems_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function goshendems_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'goshendems_content_width', 640 );
}
add_action( 'after_setup_theme', 'goshendems_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function goshendems_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'goshendems' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'goshendems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'goshendems_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function goshendems_scripts() {
	wp_enqueue_style( 'goshendems-style', get_stylesheet_uri(), array(), _VERSION );
	wp_style_add_data( 'goshendems-style', 'rtl', 'replace' );

	wp_enqueue_script( 'goshendems-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'goshendems_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Enforce 9 posts per page on Story archives
 */
function goshendems_stories_archive_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->is_post_type_archive( 'story' ) ) {
			$query->set( 'posts_per_page', 9 );
		}
	}
}
add_action( 'pre_get_posts', 'goshendems_stories_archive_query' );

function my_toolbars( $toolbars )
{
   $toolbars['Very Simple' ] = array();
   $toolbars['Very Simple' ][1] = array('formatselect', 'bold' , 'italic' , 'link', 'alignleft', 'aligncenter', 'alignright' );

   return $toolbars;
}

add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );