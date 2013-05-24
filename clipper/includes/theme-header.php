<?php

/**
 * Add header elements via hooks
 *
 * Anything you add to this file will be dynamically
 * inserted in the header of your theme
 *
 * @since 1.0
 * @uses wp_head
 *
 */

// remove the WordPress version meta tag
if (get_option('clpr_remove_wp_generator') == 'yes') remove_action('wp_head', 'wp_generator');

// remove the new 3.1 admin header toolbar visible on the website if logged in
if (get_option('clpr_remove_admin_bar') == 'yes') add_filter('show_admin_bar', '__return_false');

// adds version number in the header for troubleshooting
function appthemes_version($app_version) {
    global $app_version, $app_theme;
    echo "\n\t" .'<meta name="version" content="'.$app_theme.' '.$app_version.'" />' . "\n";
}
add_action('wp_head', 'appthemes_version');


function clpr_add_header() {
    global $app_abbr;

    $favicon_url = "";

    // see if the favicon exists for a child-theme
    if (file_exists(TEMPLATEPATH . '/favicon.ico')) {
	    $favicon_url = get_bloginfo('template_directory') .'/favicon.ico';
    } else {
	    // only show a favicon if it's been set
	    if(get_option($app_abbr.'_favicon_url') != '')
		$favicon_url = get_option($app_abbr.'_favicon_url');
    }

    if(!empty($favicon_url)){
	echo html( "link", array(
		"href" => $favicon_url,
		"type" => "image/x-icon",
		"rel" => "shortcut icon"
	));
    }

}
add_action('wp_head', 'clpr_add_header');


// adds CSS3 support for IE
function clpr_pie_styles() {
?>
    <!-- PIE active classes -->
    <style type="text/css">
        #nav .active, #nav li { behavior: url(<?php echo get_bloginfo('template_directory'); ?>/includes/js/pie.htc); }
    </style>
    <!-- end PIE active classes -->

<?php
}
add_action('wp_head', 'clpr_pie_styles');

