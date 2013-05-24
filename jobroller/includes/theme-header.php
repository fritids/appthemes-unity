<?php

/**
 * Add header elements via the a hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the header of your theme
 *
 * @since 1.0
 * @uses wp_head or appthemes_header
 *
 */
 
// adds version number in the header for troubleshooting
function jr_version($app_version) {
    global $app_version;

    echo "\n\t" . '<!-- start wp_head -->' . "\n";
    echo "\n\t" .'<meta name="version" content="JobRoller '.$app_version.'" />' . "\n";
    echo "\n\t" . '<!-- end wp_head -->' . "\n\n";
}
add_action('wp_head', 'jr_version');


// enables the share buttons on job and blog posts
function jr_sharethis_head() {

    //fba9432a-d597-4509-800d-999395ce552a
    $pub_id = get_option('jr_sharethis_id');
    
    $http = (is_ssl()) ? 'https' : 'http';

    echo "\n\t" . '<script type="text/javascript" src="'.$http.'://w.sharethis.com/button/buttons.js"></script>' . "\n";
    echo "\n\t" . '<script type="text/javascript">stLight.options({publisher:"'.$pub_id.'"});</script>' . "\n";

}

// only enable sharethis if pub id is detected
if (get_option('jr_sharethis_id'))
    add_action('wp_head', 'jr_sharethis_head');
	
	
// remove the WordPress version meta tag
if (get_option('jr_remove_wp_generator') == 'yes')
	remove_action('wp_head', 'wp_generator');	
	
	
// remove the new 3.1 admin header toolbar visible on the website if logged in	
if (get_option('jr_remove_admin_bar') == 'yes')	
	add_filter('show_admin_bar', '__return_false');	

?>