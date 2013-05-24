<?php
/**
 * These are scripts used within the AppThemes admin pages
 *
 * @package AppThemes
 *
 */


// correctly load all the scripts so they don't conflict with plugins
function app_load_admin_scripts() {
	global $is_IE;

	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('media-upload'); // needed for image upload
	wp_enqueue_script('thickbox'); // needed for image upload
	wp_enqueue_style('thickbox'); // needed for image upload

	wp_enqueue_script('easytooltip', get_bloginfo('template_directory').'/includes/js/easyTooltip.js', array('jquery'), '1.0');
	// wp_enqueue_script('validate', get_bloginfo('template_directory').'/includes/js/validate/jquery.validate.pack.js', array('jquery'), '1.6');
	wp_enqueue_script( 'admin-scripts', get_bloginfo('template_directory') . '/includes/admin/admin-scripts.js', array( 'jquery', 'media-upload', 'thickbox', 'jquery-ui-tabs' ), '1.4' );
	wp_enqueue_script( 'jquery-ui-datepicker' );

	if ( $is_IE ) // only load this support js when browser is IE
		wp_enqueue_script('excanvas', get_bloginfo('template_directory').'/includes/js/flot/excanvas.min.js', array('jquery'), '1.2');

	wp_enqueue_script('flot', get_bloginfo('template_directory').'/includes/js/flot/jquery.flot.min.js', array('jquery'), '1.2');

	$setTabIndex = ( isset( $_REQUEST['setTabIndex'] ) ) ? $_REQUEST['setTabIndex'] : 0;
	wp_localize_script( 'admin-scripts', 'theme_scripts_admin', array(
		'setTabIndex' => $setTabIndex,
	) );


	// register the stylesheets
	wp_register_style( 'admin-style', get_bloginfo('template_directory') . '/includes/admin/admin-style.css', false, '3.0' );
	wp_enqueue_style( 'admin-style' );

	wp_register_style( 'jquery-ui-style', get_bloginfo('template_directory') . '/includes/js/jquery-ui/jquery-ui.css', false, '1.9.2' );
	wp_enqueue_style( 'jquery-ui-style' );

}


add_action( 'admin_enqueue_scripts', 'app_load_admin_scripts' );


?>