<?php
/**
 * Function to prevent visitors without admin permissions
 * to access the wordpress backend. If you wish to permit
 * others besides admins acces, change the user_level
 * to a different number.
 *
 * http://codex.wordpress.org/Roles_and_Capabilities#level_8
 *
 * @global <type> $user_level
 *
 * in order to use this for wpmu, you need to follow the comment
 * instructions below in all locations and make the changes
 */

function app_security_check() {
	global $app_abbr;

    // if there's no value set yet, then give everyone access
    if(get_option($app_abbr.'_admin_security') == false) update_option($app_abbr.'_admin_security', 'read');

    $access_level = get_option($app_abbr.'_admin_security');

    if (!current_user_can($access_level)) {

    // comment out the above two lines and uncomment this line if you are using
    // wpmu and want to block back office access to everyone except admins
    // if (!is_site_admin()) {

?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>

            <head>
                <title><?php _e( 'Access Denied.', APP_TD ); ?></title>
                <link rel="stylesheet" href="<?php echo admin_url('css/install.css'); ?>" type="text/css" />
            </head>

            <body id="error-page">

                <p><?php _e( 'Access Denied. Your site administrator has blocked access to the WordPress back-office.', APP_TD ); ?></p>

            </body>

        </html>

<?php
        exit();

    }

}


// if people are having trouble with this option, they can disable it
if ( get_option($app_abbr.'_admin_security') != 'disable' ) {

	// check and make sure security option is enabled and the request is not ajax which is used for search auto-complete
	if ( ! defined( 'DOING_AJAX' ) )
		add_action( 'admin_init', 'app_security_check', 1 );

}
?>
