<?php

/**
* Install script to insert default data.
* Only run if theme is being activated
* 
*
*/


function clpr_install_theme() {
	global $wpdb, $app_abbr, $app_db_version, $wp_rewrite;

	// run the table install script
	clpr_tables_install();

	// insert the default values
	clpr_default_values();

	// create pages and assign templates
	clpr_create_pages();

	// create the default taxonomies
	clpr_create_taxonomies();

	// create the first default coupon
	clpr_first_coupon();

	// insert the default menu container
	app_create_default_menu();

	// flush the rewrite rules, triggered in admin-post-types.php
	update_option($app_abbr.'_rewrite_flush_flag', 'true');
	
	// if fresh install, setup current database version, and do not process update
	if ( get_option($app_abbr.'_db_version') == false ) update_option($app_abbr.'_db_version', $app_db_version);
}
add_action('appthemes_first_run', 'clpr_install_theme');



// Create the theme database tables
function clpr_tables_install() {
	global $wpdb;


	// create the daily page view counter table

		$sql = "
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					time date NOT NULL DEFAULT '0000-00-00',
					postnum int(11) NOT NULL,
					postcount int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY  (id)";

	scb_install_table( 'clpr_pop_daily', $sql );


	// create the all-time page view counter table

		$sql = "
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					postnum int(11) NOT NULL,
					postcount int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY  (id)";

	scb_install_table( 'clpr_pop_total', $sql );


	// create the reports table

		$sql = "
					id int(11) NOT NULL AUTO_INCREMENT,
					postID int(11) NOT NULL,
					post_title text NOT NULL,
					stamp varchar(15) NOT NULL,
					status tinyint(1) NOT NULL DEFAULT '1',
					PRIMARY KEY  (id)";

	scb_install_table( 'clpr_report', $sql );


	// create the reports comments table

		$sql = "
					ind1 int(10) unsigned NOT NULL AUTO_INCREMENT,
					reportID int(11) NOT NULL,
					type varchar(200) NOT NULL,
					comment text NOT NULL,
					ip varchar(20) NOT NULL,
					stamp int(11) NOT NULL,
					PRIMARY KEY  (ind1),
					KEY reportID (reportID)";

	scb_install_table( 'clpr_report_comments', $sql );


	// create the recent search terms table

		$sql = "
					id int(11) NOT NULL AUTO_INCREMENT,
					terms varchar(50) NOT NULL,
					datetime datetime NOT NULL,
					hits int(11) NOT NULL,
					details text NOT NULL,
					PRIMARY KEY  (id),
					KEY datetimeindex (datetime)";

	scb_install_table( 'clpr_search_recent', $sql );


	// create the total search terms table

		$sql = "
					id int(11) NOT NULL AUTO_INCREMENT,
					terms varchar(50) NOT NULL,
					date date NOT NULL,
					count int(11) NOT NULL,
					last_hits int(11) NOT NULL,
					status tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY  (id,date)";

	scb_install_table( 'clpr_search_total', $sql );


	// create the meta table for the custom stores taxonomy

		$sql = "
					meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					stores_id bigint(20) unsigned NOT NULL default '0',
					meta_key varchar(255) DEFAULT NULL,
					meta_value longtext,
					PRIMARY KEY  (meta_id),
					KEY stores_id (stores_id),
					KEY meta_key (meta_key)";

	scb_install_table( 'clpr_storesmeta', $sql );


	// create the votes total table

		$sql = "
					id int(11) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					user_id int(11) NOT NULL,
					vote int(4) NOT NULL,
					ip_address varchar(15) NOT NULL,
					date_stamp datetime NOT NULL,
					PRIMARY KEY  (id)";

	scb_install_table( 'clpr_votes', $sql );


	// create the votes total table

		$sql = "
					id int(11) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					votes_up int(11) NOT NULL,
					votes_down int(11) NOT NULL,
					votes_total int(11) NOT NULL,
					last_update datetime NOT NULL,
					PRIMARY KEY  (id)";

	scb_install_table( 'clpr_votes_total', $sql );


}


function clpr_default_values() {
	global $wpdb, $app_abbr, $app_version, $wp_rewrite;


	//////////////////////////////////////////////
	// insert default values if they don't already exist.
	//////////////////////////////////////////////

	// update_option($app_abbr.'_version', $app_version);

	// set the permalink structure, only when first time installed
	if ( get_option('permalink_structure') == '' && get_option($app_abbr.'_version') == false )
		$wp_rewrite->set_permalink_structure( '/%postname%/' );

	// home page layout
	if(get_option($app_abbr.'_stylesheet') == false) update_option($app_abbr.'_stylesheet', 'red.css');
	if(get_option($app_abbr.'_use_logo') == false) update_option($app_abbr.'_use_logo', 'yes');

	// security settings
	if(get_option($app_abbr.'_admin_security') == false) update_option($app_abbr.'_admin_security', 'read');

	if(get_option($app_abbr.'_nu_admin_email') == false) update_option($app_abbr.'_nu_admin_email', 'yes');

	// set default new user registration email values
	if(get_option($app_abbr.'_nu_custom_email') == false) update_option($app_abbr.'_nu_custom_email', 'no');
	if(get_option($app_abbr.'_nu_from_name') == false) update_option($app_abbr.'_nu_from_name', wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
	if(get_option($app_abbr.'_nu_from_email') == false) update_option($app_abbr.'_nu_from_email', get_option('admin_email'));
	if(get_option($app_abbr.'_nu_email_subject') == false) update_option($app_abbr.'_nu_email_subject', 'Thank you for registering, %username%');
	if(get_option($app_abbr.'_nu_email_type') == false) update_option($app_abbr.'_nu_email_type', 'text/plain');

	if(get_option($app_abbr.'_nu_email_body') == false) update_option($app_abbr.'_nu_email_body', '
Hi %username%,

Welcome to %blogname%!

Below you will find your username and password which allows you to login to your user account.

--------------------------
Username: %username%
Password: %password%

%loginurl%
--------------------------

If you have any questions, please just let us know.

Best regards,


Your %blogname% Team
%siteurl%		
');

	if(get_option($app_abbr.'_new_ad_email') == false) update_option($app_abbr.'_new_ad_email', 'yes');

	// set new coupon email values
	if(get_option($app_abbr.'_nc_custom_email') == false) update_option($app_abbr.'_nc_custom_email', 'yes');
	if(get_option($app_abbr.'_nc_from_name') == false) update_option($app_abbr.'_nc_from_name', wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
	if(get_option($app_abbr.'_nc_from_email') == false) update_option($app_abbr.'_nc_from_email', get_option('admin_email'));
	if(get_option($app_abbr.'_nc_email_subject') == false) update_option($app_abbr.'_nc_email_subject', 'Your coupon submission on %blogname%');
	if(get_option($app_abbr.'_nc_email_type') == false) update_option($app_abbr.'_nc_email_type', 'text/plain');

	if(get_option($app_abbr.'_nc_email_body') == false) update_option($app_abbr.'_nc_email_body', '
Hi %username%,

Thank you for your recent submission. Your coupon has been received and will not appear live on our site until it has been approved. Below you will find a summary of your submission.

Coupon Details
--------------------------
Title: %title%
Coupon Code: %code%
Category: %category%
Store: %store%
Description: %description%
--------------------------

You may check the status of your coupon(s) at anytime by logging into your dashboard.
%dashurl%

Best regards,


Your %blogname% Team
%siteurl%		
');

	// reCaptcha default values
	if(get_option($app_abbr.'_captcha_enable') == false) update_option($app_abbr.'_captcha_enable', 'no');
	if(get_option($app_abbr.'_captcha_theme') == false) update_option($app_abbr.'_captcha_theme', 'red');	

	if(get_option($app_abbr.'_disable_stylesheet') == false) update_option($app_abbr.'_disable_stylesheet', 'no');
	if(get_option($app_abbr.'_remove_wp_generator') == false) update_option($app_abbr.'_remove_wp_generator', 'no');
	if(get_option($app_abbr.'_remove_admin_bar') == false) update_option($app_abbr.'_remove_admin_bar', 'no');
	if(get_option($app_abbr.'_google_jquery') == false) update_option($app_abbr.'_google_jquery', 'no');
	if(get_option($app_abbr.'_debug_mode') == false) update_option($app_abbr.'_debug_mode', 'no');

	if(get_option($app_abbr.'_coupons_require_moderation') == false) update_option($app_abbr.'_coupons_require_moderation', 'yes');
	if(get_option($app_abbr.'_allow_html') == false) update_option($app_abbr.'_allow_html', 'no');
	if(get_option($app_abbr.'_coupon_edit') == false) update_option($app_abbr.'_coupon_edit', 'yes');
	if(get_option($app_abbr.'_coupon_code_hide') == false) update_option($app_abbr.'_coupon_code_hide', 'no');
	if(get_option($app_abbr.'_prune_coupons') == false) update_option($app_abbr.'_prune_coupons', 'no');
	if(get_option($app_abbr.'_prune_coupons_email') == false) update_option($app_abbr.'_prune_coupons_email', 'no');
	// if(get_option($app_abbr.'_no_image') == false) update_option($app_abbr.'_no_image', 'red');
	if(get_option($app_abbr.'_submit_file_types') == false) update_option($app_abbr.'_submit_file_types', 'png,gif,jpg,jpeg');

	if(!get_option($app_abbr.'_rp_send_email')) update_option($app_abbr.'_rp_send_email', '1');		
	if(!get_option($app_abbr.'_rp_email_address')) update_option($app_abbr.'_rp_email_address', get_option('admin_email'));		
	if(!get_option($app_abbr.'_rp_display_text')) update_option($app_abbr.'_rp_display_text', 'Report a Problem');		
	if(!get_option($app_abbr.'_rp_options')) update_option($app_abbr.'_rp_options', 'Invalid Coupon Code|Expired Coupon|Offensive Content|Invalid Link|Spam|Other');

	// only registered users can report coupons	
	if(!get_option($app_abbr.'_rp_registeronly')) update_option('rp_registeronly', '0');

	// anyone can submit a new coupon
	if(get_option($app_abbr.'_reg_required') == false) update_option($app_abbr.'_reg_required', 'no');

	// set the default new WP user role only if it's currently subscriber
	if(get_option('default_role') == 'subscriber') update_option('default_role', 'contributor');

	// check the "membership" box to enable wordpress registration
	if(get_option('users_can_register') == 0) update_option('users_can_register', 1);

	// update_option('show_on_front', 'page');

	// keep track of all searches done on the site
	if(get_option($app_abbr.'_search_stats') == false) update_option($app_abbr.'_search_stats', 'yes');
	if(get_option($app_abbr.'_search_ex_pages') == false) update_option($app_abbr.'_search_ex_pages', 'yes');
	if(get_option($app_abbr.'_search_ex_blog') == false) update_option($app_abbr.'_search_ex_blog', 'yes');

	if(get_option($app_abbr.'_stats_all') == false) update_option($app_abbr.'_stats_all', 'yes');
	if(get_option($app_abbr.'_adcode_336x280_enable') == false) update_option($app_abbr.'_adcode_336x280_enable', 'no');
	if(get_option($app_abbr.'_votes_reset_count') == false) update_option($app_abbr.'_votes_reset_count', '0');

	// moderate new store submissions
	if(get_option($app_abbr.'_stores_require_moderation') == false) update_option($app_abbr.'_stores_require_moderation', 'no');	


	// important default setting path variables
	if(get_option($app_abbr.'_author_url') == false) update_option($app_abbr.'_author_url', 'author');

	// set the default custom post type and taxomoy base urls
	if(get_option($app_abbr.'_coupon_permalink') == false) update_option($app_abbr.'_coupon_permalink', 'coupons');
	if(get_option($app_abbr.'_coupon_cat_tax_permalink') == false) update_option($app_abbr.'_coupon_cat_tax_permalink', 'coupon-category');
	if(get_option($app_abbr.'_coupon_type_tax_permalink') == false) update_option($app_abbr.'_coupon_type_tax_permalink', 'coupon-type');
	if(get_option($app_abbr.'_coupon_tag_tax_permalink') == false) update_option($app_abbr.'_coupon_tag_tax_permalink', 'coupon-tag');
	if(get_option($app_abbr.'_coupon_store_tax_permalink') == false) update_option($app_abbr.'_coupon_store_tax_permalink', 'stores');
	if(get_option($app_abbr.'_coupon_image_tax_permalink') == false) update_option($app_abbr.'_coupon_image_tax_permalink', 'coupon-image');

	if(get_option($app_abbr.'_coupon_redirect_base_url') == false) update_option($app_abbr.'_coupon_redirect_base_url', 'go');
	if(get_option($app_abbr.'_store_redirect_base_url') == false) update_option($app_abbr.'_store_redirect_base_url', 'go-store');

	// Pricing
	if(get_option($app_abbr.'_charge_coupons') == false) update_option($app_abbr.'_charge_coupons', 'no');
	if(get_option($app_abbr.'_coupon_price') == false) update_option($app_abbr.'_coupon_price', '5');

}


// Create the Clipper pages and assign the templates to them
function clpr_create_pages() {
		global $wpdb, $app_abbr;

		// About page
		// first check and make sure this page doesn't already exist
		$sql = "SELECT ID FROM $wpdb->posts WHERE post_name = %s LIMIT 1";
		$pagefound = $wpdb->get_var( $wpdb->prepare($sql, 'about') );

		if ( $wpdb->num_rows == 0 ) {

				// then create the edit item page
				$my_page = array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_author' => 1,
				'post_name' => 'about',
				'post_title' => 'About'
				);

				// Insert the page into the database
				$page_id = wp_insert_post($my_page);

		}

}


// create coupon types, coupon category, coupon tags, and store default values
function clpr_create_taxonomies() {
	global $wpdb;

	$coupon_types = array(
		'Coupon Code',
		'Printable Coupon',
    'Promotion'
	);
	
	if ($coupon_types) foreach($coupon_types as $type) {
		if (!$type_id = get_term_by( 'slug', sanitize_title($type), APP_TAX_TYPE))
			$ins_id = wp_insert_term($type, APP_TAX_TYPE);
	}
	
	
	// create the category
	$category_tax = ( 
		array( 
			'description'=> 'This is the default coupon category included with Clipper.',
			'slug' => 'electronics'
		)
	);	
	wp_insert_term('Electronics', APP_TAX_CAT, $category_tax);
	
	
	// create some default coupon tags
	wp_insert_term('Books', APP_TAX_TAG);
	wp_insert_term('Online Store', APP_TAX_TAG);
	wp_insert_term('Electronics', APP_TAX_TAG);
	
	
	// create the store
	$store_tax = ( 
		array( 
			'description'=> 'This is the default store included with Clipper.',
			'slug' => 'amazon'
		)
	);
	wp_insert_term('Amazon.com', APP_TAX_STORE, $store_tax);
	
	// now add the custom url field default value
	$term = get_term_by('name', 'Amazon.com', APP_TAX_STORE);
	update_metadata(APP_TAX_STORE, $term->term_id, 'clpr_store_url', 'http://www.amazon.com');
	
  // create term for printable coupon images
	$image_tax = ( 
		array( 
			'slug' => 'printable-coupon'
		)
	);
	if (!get_term_by( 'slug', 'printable-coupon', APP_TAX_IMAGE))
    wp_insert_term('Printable Coupon', APP_TAX_IMAGE, $image_tax);

}


// create a default coupon for demo purposes
function clpr_first_coupon(){
	global $wpdb;

	// check to see if this coupon already exists
	$sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '10-off-amazon' LIMIT 1";

	$pagefound = $wpdb->get_var($sql);

	if($wpdb->num_rows == 0) {

		$data = array(
			'post_content' => $wpdb->escape('<p>Great coupon from Amazon.com that gives 10% off any purchase. Can be used multiple times so make sure to take advantage of this deal often.</p><p>This is the default coupon created when Clipper is first installed. It is for demonstration purposes only and is not actually a 10% off Amazon.com coupon.</p>')
			, 'post_title' => $wpdb->escape('10% Off Amazon')
			, 'post_status' => 'publish'
			, 'post_author' => 1
			, 'post_type' => APP_POST_TYPE // custom post type since WP 3.0+
		);

		$post_id = wp_insert_post($data);

		if ($post_id==0 || is_wp_error($post_id)) wp_die('Error: Unable to create coupon.');

		// add meta data and category
		add_post_meta( $post_id, 'clpr_coupon_code', 'AMAZON10', true );
		add_post_meta( $post_id, 'clpr_expire_date', '07-04-2015', true );
		add_post_meta( $post_id, 'clpr_coupon_aff_url', 'http://www.amazon.com/?tag=20-ebt', true );
		add_post_meta( $post_id, 'clpr_votes_percent', '100', true );

		// give it an id number		
		$clpr_item_id = uniqid( rand( 10,1000 ), false );
		add_post_meta( $post_id, 'clpr_id', $clpr_item_id, true );

		// set the default coupon type
		wp_set_object_terms($post_id, 'coupon-code', APP_TAX_TYPE);

		// set the default store taxonomy
		wp_set_object_terms($post_id, 'Amazon.com', APP_TAX_STORE);

		// set the default category taxonomy
		wp_set_object_terms($post_id, 'Electronics', APP_TAX_CAT);

		// set some default tags
		wp_set_object_terms($post_id, array('Books', 'Online Store', 'Electronics'), APP_TAX_TAG);

	}

}

function app_create_default_menu() {
	global $wpdb, $app_theme;

	// create the default menu container. no dups will be created if already exists
	wp_update_nav_menu_object( 0, array( 'menu-name' => $app_theme.' Main Menu' ));

}



?>