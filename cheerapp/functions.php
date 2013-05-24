<?php

/* ---------------------------------------------------------------- */
/*						Load Options Framework						*/
/* ---------------------------------------------------------------- */

if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';
}

/* ---------------------------------------------------------------- */
/*						Register and load scripts					*/
/* ---------------------------------------------------------------- */

// Register and enqueue scripts
function royal_register_scripts() {
	if ( !is_admin() ) {		
		$themeversion = wp_get_theme()->Version;
		wp_register_script( 'prettyPhoto', get_template_directory_uri() . '/js/prettyPhoto.js', 'jquery', '3.1.2' );
		wp_register_script( 'scripts', get_template_directory_uri() . '/js/scripts.js', array( 'jquery', 'wp-ajax-response' ), $themeversion );
		wp_register_script( 'html5shiv', 'http://html5shiv.googlecode.com/svn/trunk/html5.js', array(), null, false );
		
		// IE stylesheet
		wp_register_style( 'ie-styles', get_template_directory_uri() . '/css/ie.css' );
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'prettyPhoto' );
		wp_enqueue_script( 'scripts' );
	}
}
add_action( 'init', 'royal_register_scripts' );

// Load scripts with priority of 1. These scripts will load before all other scripts.
function royal_priority_scripts() {
	if ( !is_admin() ) {
	}
}
add_action( 'init', 'royal_priority_scripts', 1 );

// Load scripts only on single post page
function royal_single_scripts() {
	if( is_singular() ) wp_enqueue_script( 'comment-reply' ); 
}
add_action( 'wp_print_scripts', 'royal_single_scripts' );



/* ---------------------------------------------------------------- */
/*							Register WP Menus						*/
/* ---------------------------------------------------------------- */

function royal_register_menus() {
	register_nav_menu( 'top-menu', __( 'Top Menu', 'cheerapp' ) );
	register_nav_menu( 'footer-menu', __( 'Footer Menu', 'cheerapp' ) );
}
add_action( 'init', 'royal_register_menus' );



/* ---------------------------------------------------------------- */
/*							Register Sidebars						*/
/* ---------------------------------------------------------------- */

if( function_exists( 'register_sidebar' ) ) {
	// Sidebar
	$args = array(
		'name'          => __( 'Sidebar', 'cheerapp' ),
		'id'            => 'sidebar',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6 class="widget-title">',
		'after_title'   => '</h6>'
	);
	register_sidebar( $args );
	
	// Homepage widgets (bottom-bar)
	$args = array(
		'name'          => __( 'Homepage Widgets', 'cheerapp' ),
		'id'            => 'bottombar',
		'description'   => __( 'These widgets will appear at the bottom of home page', 'cheerapp' ),
		'before_widget' => '<div id="%1$s" class="widget span3 %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>'
	);
	register_sidebar( $args );
	
	// Forum topic sidebar
	$args = array(
		'name'          => __( 'Knowledgebase & FAQ sidebar', 'cheerapp' ),
		'id'            => 'kb-sidebar',
		'description'   => __( 'This sidebar will appear on knowledgebase and FAQ pages', 'cheerapp' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6 class="widget-title">',
		'after_title'   => '</h6>'
	);
	register_sidebar( $args );
	
	if( function_exists( 'bbpress' ) ) {
		// Forum topic sidebar
		$args = array(
			'name'          => __( 'Forum sidebar', 'cheerapp' ),
			'id'            => 'forum-sidebar',
			'description'   => __( 'This sidebar will appear when viewing any forum page', 'cheerapp' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="widget-title">',
			'after_title'   => '</h6>'
		);
		register_sidebar( $args );
	}
}



/* ---------------------------------------------------------------- */
/*						Load Localization Domain					*/
/* ---------------------------------------------------------------- */

load_theme_textdomain( 'cheerapp', get_template_directory() . '/languages' );



/* ---------------------------------------------------------------- */
/*								Image sizes							*/
/* ---------------------------------------------------------------- */

if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 58, 58, true );
	add_image_size( 'large', 968, '', false );
	add_image_size( 'medium', 628, '', false );
	add_image_size( 'small', 288, '', false );
	add_image_size( 'thumbnail-blog', 628, 140, true ); // Blog thumbnails
}

// Update default WordPress media settings
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ){
	global $wpdb;
	
	$wpdb->query("UPDATE $wpdb->options
	SET option_value='58'
	WHERE option_name='thumbnail_size_w'");

    $wpdb->query("UPDATE $wpdb->options
	SET option_value='58'
	WHERE option_name='thumbnail_size_h'");
	
	$wpdb->query("UPDATE $wpdb->options
	SET option_value='628'
	WHERE option_name='medium_size_w'");

    $wpdb->query("UPDATE $wpdb->options
	SET option_value='0'
	WHERE option_name='medium_size_h'");
	
	$wpdb->query("UPDATE $wpdb->options
	SET option_value='968'
	WHERE option_name='large_size_w'");

    $wpdb->query("UPDATE $wpdb->options
	SET option_value='0'
	WHERE option_name='large_size_h'");
}



/* ---------------------------------------------------------------- */
/*							Content Width							*/
/* ---------------------------------------------------------------- */

if ( ! isset( $content_width ) ) {
	$content_width = 980;
}



/* ---------------------------------------------------------------- */
/*					Automatic Feed Links support					*/
/* ---------------------------------------------------------------- */

add_theme_support( 'automatic-feed-links' );



/* ---------------------------------------------------------------- */
/*					bbPress forum plugin support					*/
/* ---------------------------------------------------------------- */

add_theme_support( 'bbpress' );



/* ---------------------------------------------------------------- */
/*					Load Royal Framework Files						*/
/* ---------------------------------------------------------------- */

define( 'ROYAL_FILEPATH', get_template_directory() );
define( 'ROYAL_DIRECTORY', get_template_directory_uri() );

// Load admin functions on admin pages
if( is_admin() ) {
	require_once( ROYAL_FILEPATH . '/functions/admin/admin-functions.php' );
}

// Load a file with custom theme functions
require_once ( ROYAL_FILEPATH . '/functions/theme-functions.php' );
// Load a file with deprecated functions
require_once ( ROYAL_FILEPATH . '/functions/deprecated.php' );
// Load a file with shortcodes
require_once ( ROYAL_FILEPATH . '/functions/theme-shortcodes.php' );

// Load files that contain functions common to all themes based on Royal framework
require_once ( ROYAL_FILEPATH . '/functions/common-functions.php' );
require_once ( ROYAL_FILEPATH . '/functions/template-tags.php' );
require_once ( ROYAL_FILEPATH . '/functions/ajax/class.royal-ajax-frontend.php' );

// If bbPress is installed load some helper functions and shortcodes
if( function_exists( 'bbpress' ) && bbpress() ) {
	require_once ( ROYAL_FILEPATH . '/functions/bbp-functions.php' );
	require_once ( ROYAL_FILEPATH . '/functions/bbp-shortcodes.php' );
}

// Load modules
require_once ( ROYAL_FILEPATH . '/functions/modules.php' );

$args = array(
	'image_sizes'	=>	array(
		array( 'name' => 'featured-image-small', 'width' => 560, 'height' => 9999, 'crop' => false ),
		array( 'name' => 'featured-image-full', 'width' => 860, 'height' => 9999, 'crop' => false )
	)
);
init_module( 'featured', $args );

$args = array(
);
init_module( 'knowledgebase', $args );

$args = array(
);
init_module( 'quick-links', $args );

$args = array(
);
init_module( 'pricing', $args );

?>