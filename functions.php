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
	define( '_VERSION', '1.3.7' );
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

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'goshendems' ),
			'menu-2' => esc_html__( 'Social', 'goshendems' ),
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
	/*
	 * Work Sans used to be pulled in by an @import at the top of style.css, which
	 * hid it behind a chained request. Enqueue it directly so the browser can find
	 * it in the initial HTML (see the preconnect hints in header.php).
	 */
	wp_enqueue_style(
		'goshendems-work-sans',
		'https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,400;0,500;0,700;1,500&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL is already versioned.
	);

	wp_enqueue_style( 'goshendems-style', get_stylesheet_uri(), array( 'goshendems-work-sans' ), _VERSION );
	wp_style_add_data( 'goshendems-style', 'rtl', 'replace' );

	wp_enqueue_script( 'goshendems-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _VERSION, true );

	if ( is_front_page() ) {
		wp_enqueue_script( 'goshendems-hero-cta-fit', get_template_directory_uri() . '/js/hero-cta-fit.js', array(), _VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'goshendems_scripts' );

/**
 * Add `defer` to theme scripts so they never block first render.
 *
 * These are already enqueued in the footer, but optimizer plugins sometimes hoist
 * scripts into <head>; `defer` keeps them non-blocking either way.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Registered script handle.
 * @return string
 */
function goshendems_defer_scripts( $tag, $handle ) {
	$defer_handles = array(
		'goshendems-navigation',
		'goshendems-hero-cta-fit',
		'goshendems-resources-filter',
		'goshendems-elected-positions-filter',
	);

	if ( ! in_array( $handle, $defer_handles, true ) ) {
		return $tag;
	}

	if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
		return $tag;
	}

	return str_replace( ' src=', ' defer src=', $tag );
}
add_filter( 'script_loader_tag', 'goshendems_defer_scripts', 10, 2 );

/**
 * Fallback meta description text for the current view.
 *
 * @return string
 */
function goshendems_default_meta_description() {
	$description = '';

	if ( is_front_page() ) {
		$description = __( 'Goshen City Democratic Party — connecting, volunteering, and voting for Goshen, Indiana.', 'goshendems' );
	} elseif ( is_post_type_archive( 'candidate' ) ) {
		$description = __( 'Meet the Democratic candidates running for office in Goshen, Indiana.', 'goshendems' );
	} elseif ( is_post_type_archive( 'story' ) ) {
		$description = __( 'News and stories from the Goshen City Democratic Party.', 'goshendems' );
	} elseif ( is_singular() ) {
		$description = get_the_excerpt();
	}

	if ( '' === $description ) {
		$description = get_bloginfo( 'description', 'display' );
	}

	$description = wp_strip_all_tags( (string) $description, true );

	return trim( wp_trim_words( $description, 30, '' ) );
}

/**
 * Supply a description to SEOPress when one has not been entered for the view.
 *
 * @param string $description SEOPress meta description.
 * @return string
 */
function goshendems_seopress_description_fallback( $description ) {
	if ( is_string( $description ) && '' !== trim( $description ) ) {
		return $description;
	}

	return goshendems_default_meta_description();
}
add_filter( 'seopress_titles_desc', 'goshendems_seopress_description_fallback' );

/**
 * Output a meta description when no SEO plugin is handling it.
 *
 * Guarded so the site never ends up with two description tags.
 */
function goshendems_meta_description() {
	if ( defined( 'SEOPRESS_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}

	$description = goshendems_default_meta_description();
	if ( '' === $description ) {
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'goshendems_meta_description', 1 );

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
 * ACF page slug location rule (slug-based field group locations).
 */
require get_template_directory() . '/inc/acf-page-slug-location.php';

/**
 * Primary navigation (WP Menus).
 */
require get_template_directory() . '/inc/nav-menus.php';

/**
 * Resources CPT helpers and archive behavior.
 */
require get_template_directory() . '/inc/resources.php';

/**
 * Candidates CPT helpers and Open Graph link thumbnails.
 */
require get_template_directory() . '/inc/candidates.php';

/**
 * Stories CPT — sync hero image to featured image for Open Graph.
 */
require get_template_directory() . '/inc/stories.php';

/**
 * Pages — sync ACF hero images to featured image for Open Graph.
 */
require get_template_directory() . '/inc/pages.php';

/**
 * CPT JSON-LD schema (Person / Article).
 */
require get_template_directory() . '/inc/schema.php';

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

/**
 * Redirect author archives to home to reduce username enumeration.
 */
function goshendems_disable_author_archives() {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'goshendems_disable_author_archives' );

/**
 * Block anonymous access to the users REST endpoint (username enumeration).
 *
 * @param mixed            $result  Response to replace the request result.
 * @param WP_REST_Server   $server  Server instance.
 * @param WP_REST_Request  $request Request used to generate the response.
 * @return mixed
 */
function goshendems_restrict_users_rest_endpoint( $result, $server, $request ) {
	if ( is_user_logged_in() ) {
		return $result;
	}

	$route = $request->get_route();
	if ( preg_match( '#^/wp/v2/users(?:/|$)#', $route ) ) {
		return new WP_Error(
			'rest_user_cannot_view',
			__( 'Sorry, you are not allowed to list users.', 'goshendems' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'goshendems_restrict_users_rest_endpoint', 10, 3 );