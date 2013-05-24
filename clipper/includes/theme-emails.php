<?php
/**
 *
 * Emails that get called and sent out 
 * @author AppThemes
 * @version 1.0
 * For wp_mail to work, you need the following:
 * settings SMTP and smtp_port need to be set in your php.ini
 * also, either set the sendmail_from setting in php.ini, or pass it as an additional header.
 *
 */

 
// send new coupon notification email to admin
function app_new_submission_email($post_id) {
	global $app_theme;

    // get the post values
    $the_post = get_post($post_id);
    $category = appthemes_get_custom_taxonomy($post_id, APP_TAX_CAT, 'name');
  	$store = appthemes_get_custom_taxonomy($post_id, APP_TAX_STORE, 'name');
  	$couponcode = get_post_meta($post_id, 'clpr_coupon_code', true);
	
    $the_title = stripslashes($the_post->post_title);
  	$the_code = stripslashes($couponcode);
    $the_cat = stripslashes($category);
  	$the_store = stripslashes($store);
    $the_author = stripslashes(clpr_get_user_name($the_post->post_author));
    $the_slug = get_permalink( $the_post->ID );
    $the_content = appthemes_filter(stripslashes($the_post->post_content));
  	$the_content = mb_substr($the_content, 0, 150).'.....';
    $adminurl = add_query_arg( array( 'action' => 'edit', 'post' => $post_id ), admin_url('post.php') );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = get_option('admin_email');
    $subject = __( 'New Coupon Submission', APP_TD );
		$headers = 'From: '. sprintf( __( '%s Admin', APP_TD ), $blogname ) .' <'. get_option( 'admin_email' ) .'>' . "\r\n";

    $message  = __( 'Dear Admin,', APP_TD ) . "\r\n\r\n";
    $message .= sprintf( __( 'The following coupon has just been submitted on your %s website.', APP_TD ), $blogname ) . "\r\n\r\n";
    $message .= __( 'Details', APP_TD ) . "\r\n";
    $message .= '-----------------' . "\r\n";
    $message .= __( 'Title: ', APP_TD ) . $the_title . "\r\n";
  	$message .= __( 'Coupon Code: ', APP_TD ) . $the_code . "\r\n";
    $message .= __( 'Category: ', APP_TD ) . $the_cat . "\r\n";
  	$message .= __( 'Store: ', APP_TD ) . $the_store . "\r\n";
    $message .= __( 'Author: ', APP_TD ) . $the_author . "\r\n";
  	$message .= __( 'Description: ', APP_TD ) . $the_content . "\r\n\r\n";
    $message .= '-----------------' . "\r\n\r\n";
    $message .= __( 'Preview: ', APP_TD ) . $the_slug . "\r\n";
    $message .= sprintf( __('Edit: %s', APP_TD ), $adminurl ) . "\r\n\r\n\r\n";
    $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
    $message .= $app_theme . "\r\n\r\n";

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);

}


// send new email to coupon owner
function clpr_owner_new_coupon_email($post_id) {
	global $app_abbr;
	
	if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

    // get the post values
    $the_post = get_post($post_id);
  	$couponcode = get_post_meta($post_id, 'clpr_coupon_code', true);
    $category = appthemes_get_custom_taxonomy($post_id, APP_TAX_CAT, 'name');
  	$store = appthemes_get_custom_taxonomy($post_id, APP_TAX_STORE, 'name');

    $the_title = stripslashes($the_post->post_title);
  	$the_code = stripslashes($couponcode);
    $the_cat = stripslashes($category);
  	$the_store = stripslashes($store);

  	$the_author = stripslashes(clpr_get_user_name($the_post->post_author));
  	$the_author_email = stripslashes(get_the_author_meta('user_email', $the_post->post_author));
    $the_slug = get_permalink( $the_post->ID );
    $the_content = appthemes_filter(stripslashes($the_post->post_content));
  	$the_content = mb_substr($the_content, 0, 150).'.....';

    $the_status = stripslashes($the_post->post_status);

    $dashurl = trailingslashit(CLPR_DASHBOARD_URL);
	
  	// variables that can be used by admin to dynamically fill in email content
  	$find = array('/%username%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i', '/%title%/i', '/%code%/i', '/%category%/i', '/%store%/i', '/%description%/i', '/%dashurl%/i');
  	$replace = array($the_author, get_option('blogname'), home_url('/'), wp_login_url(), $the_author_email, $the_title, $the_code, $the_cat, $the_store, $the_content, $dashurl);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = $the_author_email;
    //$mailto = 'tester@127.0.0.1'; // USED FOR TESTING	
	
	// email contents start				
	$from_name = strip_tags(get_option($app_abbr.'_nc_from_name'));
	$from_email = strip_tags(get_option($app_abbr.'_nc_from_email'));
			
	// search and replace any user added variable fields in the subject line
	$subject = stripslashes(get_option($app_abbr.'_nc_email_subject'));
	$subject = preg_replace($find, $replace, $subject);
	$subject = preg_replace("/%.*%/", "", $subject);	

	// search and replace any user added variable fields in the body
	$message = stripslashes(get_option($app_abbr.'_nc_email_body'));
	$message = preg_replace($find, $replace, $message);
	$message = preg_replace("/%.*%/", "", $message);
	
	// assemble the header
	$headers = "From: $from_name <$from_email> \r\n";
	$headers .= "Reply-To: $from_name <$from_email> \r\n";
	$headers .= "Content-Type: ". get_option($app_abbr.'_nc_email_type') . PHP_EOL;		
	
	// ok let's send the email
	wp_mail($mailto, $subject, $message, $headers);

}

// Send an email to coupon owner when the coupon has been approved
function clpr_notify_coupon_owner_email( $post ) {
    global $current_user, $wpdb;

    // Get Coupon Data
    $the_coupon = get_post($post->ID);
    $category = appthemes_get_custom_taxonomy($post->ID, APP_TAX_CAT, 'name');

    // Coupon Data
    $coupon_title = stripslashes($the_coupon->post_title);
    $coupon_content = appthemes_filter(stripslashes($the_coupon->post_content));
    $coupon_cat = stripslashes($category);
    $coupon_status = stripslashes($the_coupon->post_status);

    // Owner Data
    $coupon_author = stripslashes(clpr_get_user_name($the_coupon->post_author));
    $coupon_author_id = stripslashes(get_the_author_meta('ID', $the_coupon->post_author));
    $coupon_author_email = stripslashes(get_the_author_meta('user_email', $the_coupon->post_author));

    // check to see if ad is legacy or not
    if(get_post_meta($post->ID, 'email', true))
        $mailto = get_post_meta($post->ID, 'email', true);
    else
        $mailto = $coupon_author_email;

    // Site Data
    $siteurl = home_url('/');
    $dashurl = trailingslashit(CLPR_DASHBOARD_URL);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // Check that the coupon was not approved by the owner
    if ( $coupon_author_id != $current_user->ID ) {

        $subject = __( 'Your Coupon Has Been Approved', APP_TD );
        $headers = 'From: '. sprintf( __( '%s Admin', APP_TD ), $blogname ) .' <'. get_option( 'admin_email' ) .'>' . "\r\n";

        $message  = sprintf( __( 'Hi %s,', APP_TD ), $coupon_author) . "\r\n\r\n";
        $message .= sprintf( __( 'Your coupon, "%s" has been approved and is now live on our site.', APP_TD ), $coupon_title ) . "\r\n\r\n";

        $message .= __( 'You can view your coupon by clicking on the following link:', APP_TD ) . "\r\n";
        $message .= get_permalink( $post->ID ) . "\r\n\r\n\r\n\r\n";
        $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
        $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
        $message .= $siteurl . "\r\n\r\n\r\n\r\n";

        // ok let's send the email
        wp_mail($mailto, $subject, $message, $headers);
    }
}
add_action('pending_to_publish', 'clpr_notify_coupon_owner_email', 10, 1);
add_action('draft_to_publish', 'clpr_notify_coupon_owner_email', 10, 1);
 
 
// Send an email to coupon owner when the coupon don't need moderation
function clpr_owner_new_published_coupon_email( $postid ) {
    global $wpdb;

    // Get Coupon Data
    $the_coupon = get_post($postid);
    $category = appthemes_get_custom_taxonomy($postid, APP_TAX_CAT, 'name');

    // Coupon Data
    $coupon_title = stripslashes($the_coupon->post_title);
    $coupon_content = appthemes_filter(stripslashes($the_coupon->post_content));
    $coupon_cat = stripslashes($category);
    $coupon_status = stripslashes($the_coupon->post_status);

    // Owner Data
    $coupon_author = stripslashes(clpr_get_user_name($the_coupon->post_author));
    $coupon_author_id = stripslashes(get_the_author_meta('ID', $the_coupon->post_author));
    $coupon_author_email = stripslashes(get_the_author_meta('user_email', $the_coupon->post_author));

    // check to see if ad is legacy or not
    if(get_post_meta($postid, 'email', true))
        $mailto = get_post_meta($postid, 'email', true);
    else
        $mailto = $coupon_author_email;

    // Site Data
    $siteurl = home_url('/');
    $dashurl = trailingslashit(CLPR_DASHBOARD_URL);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // Check that the coupon was not submitted by admin
    if ( $coupon_author_id != 1 ) {

        $subject = sprintf( __( 'Your coupon submission on %s', APP_TD ), $blogname );
        $headers = 'From: '. $blogname .' <'. get_option( 'admin_email' ) .'>' . "\r\n";

        $message  = sprintf( __( 'Hi %s,', APP_TD ), $coupon_author) . "\r\n\r\n";
        $message .= __( 'Thank you for your recent submission.', APP_TD ) . "\r\n";
        $message .= sprintf( __( 'Your coupon, "%s" has been published and is now live on our site.', APP_TD ), $coupon_title ) . "\r\n\r\n";

        $message .= __( 'You can view your coupon by clicking on the following link:', APP_TD ) . "\r\n";
        $message .= get_permalink( $postid ) . "\r\n\r\n\r\n\r\n";
        $message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
        $message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
        $message .= $siteurl . "\r\n\r\n\r\n\r\n";

        // ok let's send the email
        wp_mail($mailto, $subject, $message, $headers);
    }
}



// email that gets sent out to new users once they register
function app_new_user_notification($user_id, $plaintext_pass = '') {
	global $app_abbr;

	if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	//$user_email = 'tester@127.0.0.1'; // USED FOR TESTING

	// variables that can be used by admin to dynamically fill in email content
	$find = array('/%username%/i', '/%password%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i');
	$replace = array($user_login, $plaintext_pass, get_option('blogname'), home_url('/'), wp_login_url(), $user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	// send the site admin an email everytime a new user registers
	if (get_option($app_abbr.'_nu_admin_email') == 'yes') {
		$message  = sprintf( __( 'New user registration on your site %s:', APP_TD ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s', APP_TD ), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'E-mail: %s', APP_TD ), $user_email ) . "\r\n";

		@wp_mail(get_option('admin_email'), sprintf( __( '[%s] New User Registration', APP_TD ), $blogname ), $message);
	}

	if ( empty($plaintext_pass) )
		return;

	// check and see if the custom email option has been enabled
	// if so, send out the custom email instead of the default WP one
	if (get_option($app_abbr.'_nu_custom_email') == 'yes') {

		// email sent to new user starts here
		$from_name = strip_tags(get_option($app_abbr.'_nu_from_name'));
		$from_email = strip_tags(get_option($app_abbr.'_nu_from_email'));

		// search and replace any user added variable fields in the subject line
		$subject = stripslashes(get_option($app_abbr.'_nu_email_subject'));
		$subject = preg_replace($find, $replace, $subject);
		$subject = preg_replace("/%.*%/", "", $subject);

		// search and replace any user added variable fields in the body
		$message = stripslashes(get_option($app_abbr.'_nu_email_body'));
		$message = preg_replace($find, $replace, $message);
		$message = preg_replace("/%.*%/", "", $message);

		// assemble the header
		$headers = "From: $from_name <$from_email> \r\n";
		$headers .= "Reply-To: $from_name <$from_email> \r\n";
		$headers .= "Content-Type: ". get_option($app_abbr.'_nu_email_type') . PHP_EOL;

		// ok let's send the new user an email
		wp_mail($user_email, $subject, $message, $headers);

	// send the default email to debug
	} else {

		$message  = sprintf( __( 'Username: %s', APP_TD ), $user_login ) . "\r\n";
		$message .= sprintf( __( 'Password: %s', APP_TD ), $plaintext_pass ) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail($user_email, sprintf( __( '[%s] Your username and password', APP_TD ), $blogname ), $message);

	}

}


// sends email with receipt to customer after completed purchase
function clpr_send_receipt( $order ) {
	global $app_abbr;

	$recipient = get_user_by( 'id', $order->get_author() );

	$item = '';
	foreach ( $order->get_items() as $item ) {
		$item = html( 'p', html_link( get_permalink( $item['post']->ID ), $item['post']->post_title ) );
		break;
	}

	$table = new APP_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();

	$content = '';
	$content .= html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );
	$content .= html( 'p', __( 'This email confirms that you have purchased the following coupon listing:', APP_TD ) );
	$content .= $item;
	$content .= html( 'p', __( 'Order Summary:', APP_TD ) );
	$content .= $table_output;

	$blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
	$from_email = get_option('admin_email');

	$subject = sprintf( __( '[%s] Receipt for your order', APP_TD ), $blogname );

	$headers = "From: $blogname <$from_email> \r\n";
	$headers .= "Reply-To: $blogname <$from_email> \r\n";
	$headers .= "Content-Type: text/HTML \r\n";

	wp_mail( $recipient->user_email, $subject, $content, $headers );
}
add_action( 'appthemes_transaction_completed', 'clpr_send_receipt' );


// sends email with receipt to admin after completed purchase
function clpr_send_admin_receipt( $order ) {
	global $app_abbr;

	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;

	$moderation = ( get_option($app_abbr.'_coupons_require_moderation') == 'yes' );

	$item = '';
	foreach ( $order->get_items() as $item ) {
		$item = html( 'p', html_link( get_permalink( $item['post']->ID ), $item['post']->post_title ) );
		break;
	}

	$table = new APP_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();

	$content = '';
	$content .= html( 'p', __( 'Dear Admin,', APP_TD ) );
	$content .= html( 'p', __( 'You have received payment for the following coupon listing:', APP_TD ) );
	$content .= $item;
	if ( $moderation )
		$content .= html( 'p', __( 'Please review submitted coupon listing, and approve it.', APP_TD ) );
	$content .= html( 'p', __( 'Order Summary:', APP_TD ) );
	$content .= $table_output;

	$blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
	$admin_email = get_option('admin_email');

	$subject = sprintf( __( '[%s] Received payment for order', APP_TD ), $blogname );

	$headers = "From: $blogname <$admin_email> \r\n";
	$headers .= "Reply-To: $blogname <$admin_email> \r\n";
	$headers .= "Content-Type: text/HTML \r\n";

	wp_mail( $admin_email, $subject, $content, $headers );
}
add_action( 'appthemes_transaction_completed', 'clpr_send_admin_receipt' );


// sends email notification to admin if payment failed
function clpr_send_admin_failed_transaction( $order ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;

	$subject = sprintf( __( '[%s] Failed Order #%s', APP_TD ), get_bloginfo( 'name' ), $order->get_id() );

	$content = '';
	$content .= html( 'p', sprintf( __( 'Payment for the order #%s has failed.', APP_TD ), $order->get_id() ) );
	$content .= html( 'p', sprintf( __( 'Please <a href="%s">review this order</a>, and if necessary disable assigned services.', APP_TD ), get_edit_post_link( $order->get_id() ) ) );

	appthemes_send_email( get_option( 'admin_email' ), $subject, $content );
}
add_action( 'appthemes_transaction_failed', 'clpr_send_admin_failed_transaction' );

?>