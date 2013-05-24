<?php
/**
 * Core theme functions
 * This file is the backbone and includes all the core functions
 * Modifying this will void your warranty and could cause
 * problems with your instance. Proceed at your own risk!
 *
 *
 *
 * @author AppThemes
 * @package Clipper
 *
 */

// setup the custom post types and taxonomies as constants
// do not modify this after installing or it will break your theme!
define('APP_POST_TYPE', 'coupon');
define('APP_TAX_CAT', 'coupon_category');
define('APP_TAX_TAG', 'coupon_tag');
define('APP_TAX_TYPE', 'coupon_type');
define('APP_TAX_STORE', 'stores'); // also need to change in theme-scripts.js since it's hardcoded
define('APP_TAX_IMAGE', 'coupon_image');

define('THE_POSITION', 3);
define('FAVICON', get_bloginfo('template_directory').'/images/site_icon.png');

// set the base urls for click redirects
define('CLPR_COUPON_REDIRECT_BASE_URL', trailingslashit(get_option($app_abbr.'_coupon_redirect_base_url')));
define('CLPR_STORE_REDIRECT_BASE_URL', trailingslashit(get_option($app_abbr.'_store_redirect_base_url')));

// define the db tables we use
$app_db_tables = array($app_abbr.'_pop_daily', $app_abbr.'_pop_total' , $app_abbr.'_report', $app_abbr.'_report_comments', $app_abbr.'_search_recent', $app_abbr.'_search_total', $app_abbr.'_storesmeta', $app_abbr.'_votes', $app_abbr.'_votes_total');

// register the db tables
foreach ( $app_db_tables as $app_db_table )
		scb_register_table($app_db_table);

// execute theme actions on theme activation
function clpr_first_run() {
	if ( isset($_GET['firstrun']) )
		do_action('appthemes_first_run');
}
add_action('admin_notices', 'clpr_first_run', 9999);

include_once(TEMPLATEPATH.'/includes/views.php');
include_once(TEMPLATEPATH.'/includes/theme-hooks.php');
include_once(TEMPLATEPATH.'/includes/theme-enqueue.php');
include_once(TEMPLATEPATH.'/includes/appthemes-functions.php');
include_once(TEMPLATEPATH.'/includes/theme-actions.php');
include_once(TEMPLATEPATH.'/includes/theme-sidebars.php');
include_once(TEMPLATEPATH.'/includes/reports/reports-main.php');
include_once(TEMPLATEPATH.'/includes/theme-voting.php');
include_once(TEMPLATEPATH.'/includes/theme-security.php');
include_once(TEMPLATEPATH.'/includes/theme-emails.php');
include_once(TEMPLATEPATH.'/includes/theme-comments.php');
include_once(TEMPLATEPATH.'/includes/theme-profile.php');
include_once(TEMPLATEPATH.'/includes/theme-payments.php');

require_once APP_FRAMEWORK_DIR . '/admin/class-meta-box.php';

include_once(TEMPLATEPATH.'/includes/theme-widgets.php');
include_once(TEMPLATEPATH.'/includes/admin/admin-post-types.php');
include_once(TEMPLATEPATH.'/includes/theme-stats.php');
include_once(TEMPLATEPATH.'/includes/theme-links.php');
include_once(TEMPLATEPATH.'/includes/theme-deprecated.php');


// Admin Only Functions
if ( is_admin() ) {

	include_once(TEMPLATEPATH.'/includes/admin/admin-options.php');
	include_once(TEMPLATEPATH.'/includes/admin/write-panel.php');

	//temporary hack until WP will fully support custom post statuses
	include_once(TEMPLATEPATH.'/includes/admin/custom-post-status.php');

	include_once(TEMPLATEPATH.'/includes/admin/admin-enqueue.php');
	include_once(TEMPLATEPATH.'/includes/admin/admin-notices.php');
	include_once(TEMPLATEPATH.'/includes/admin/install-script.php');
	include_once(TEMPLATEPATH.'/includes/admin/admin-updates.php');

	// add AJAX functions
	// need both actions for each function in order to allow logged in and not logged in users to work
	add_action( 'wp_ajax_nopriv_ajax-tag-search-front', 'clpr_store_suggest' );
	add_action( 'wp_ajax_ajax-tag-search-front', 'clpr_store_suggest' );

	add_action( 'wp_ajax_nopriv_ajax-thumbsup', 'clpr_vote_update' );
	add_action( 'wp_ajax_ajax-thumbsup', 'clpr_vote_update' );

	add_action( 'wp_ajax_nopriv_comment-form', 'clpr_comment_form' );
	add_action( 'wp_ajax_comment-form', 'clpr_comment_form' );

	add_action( 'wp_ajax_nopriv_post-comment', 'clpr_post_comment_ajax' );
	add_action( 'wp_ajax_post-comment', 'clpr_post_comment_ajax' );

	add_action( 'wp_ajax_nopriv_email-form', 'clpr_email_form' );
	add_action( 'wp_ajax_email-form', 'clpr_email_form' );

	add_action( 'wp_ajax_nopriv_send-email', 'clpr_send_email_ajax' );
	add_action( 'wp_ajax_send-email', 'clpr_send_email_ajax' );

	add_action( 'wp_ajax_ajax-resetvotes', 'clpr_reset_coupon_votes_ajax' );

} else {
	// front-end includes
	include_once(TEMPLATEPATH.'/includes/theme-header.php');
	include_once(TEMPLATEPATH.'/includes/theme-footer.php');
	include_once(TEMPLATEPATH.'/includes/theme-search.php');

	clpr_load_all_page_templates();
}

new CLPR_Blog_Archive;
new CLPR_Coupon_Categories;
new CLPR_Coupon_Stores;
new CLPR_Coupon_Submit;
new CLPR_Coupon_Single;
new CLPR_Edit_Item;
new CLPR_User_Dashboard;
new CLPR_User_Profile;

// set global path variables
define( 'CLPR_DASHBOARD_URL', get_permalink( CLPR_User_Dashboard::get_id() ) );
define( 'CLPR_PROFILE_URL', get_permalink( CLPR_User_Profile::get_id() ) );
define( 'CLPR_EDIT_URL', get_permalink( CLPR_Edit_Item::get_id() ) );
define( 'CLPR_SUBMIT_URL', get_permalink( CLPR_Coupon_Submit::get_id() ) );

add_theme_support( 'app-versions', array(
	'update_page' => 'admin.php?page=settings&firstrun=1',
	'current_version' => $app_version,
	'option_key' => 'clpr_version',
) );

add_theme_support( 'app-wrapping' );

add_theme_support( 'app-login', array(
	'login' => 'tpl-login.php',
	'register' => 'tpl-registration.php',
	'recover' => 'tpl-password-recovery.php',
	'reset' => 'tpl-password-reset.php',
	'redirect' => ( get_option('clpr_disable_wp_login') == 'yes' ),
	'settings_page' => 'admin.php?page=settings&setTabIndex=3',
) );

add_theme_support( 'app-feed', array(
	'post_type' => APP_POST_TYPE,
	'blog_template' => 'tpl-blog.php',
	'alternate_feed_url' => get_option('clpr_feedburner_url'),
) );

add_theme_support( 'app-payments', array(
	'items' => array(
		array(
			'type' => CLPR_COUPON_LISTING_TYPE,
			'title' => __( 'Coupon', APP_TD ),
			'meta' => array(
				'price' => get_option($app_abbr.'_coupon_price')
			)
		),
	),
	'items_post_types' => array( APP_POST_TYPE ),
) );

add_theme_support( 'app-price-format', array(
	'currency_default' => clpr_get_default_currency_code(),
	'currency_format' => 'symbol',
	'thousands_separator' => ',',
	'decimal_separator' => '.',
	'hide_decimals' => false,
) );

add_theme_support( 'app-term-counts', array(
	'post_type' => array( APP_POST_TYPE ),
	'post_status' => array( 'publish', 'unreliable' ),
	'taxonomy' => array( APP_TAX_CAT, APP_TAX_TAG, APP_TAX_TYPE, APP_TAX_STORE ),
) );


/*-----------------------------------------------------------------
* Short Codes
* place inside content of pages or posts
*------------------------------------------------------------------
*/
// register tag - use short code: [template-url]
function filter_template_url($text) {
    return str_replace('[template-url]',get_bloginfo('template_url'), $text);
}
add_filter('the_content', 'filter_template_url');
add_filter('get_the_content', 'filter_template_url');
add_filter('widget_text', 'filter_template_url');

// add_filter('the_content', 'filter_popular_coupons');  // was causing a problem on the blog template page DC 10/19/10
//add_filter('get_the_content', 'filter_popular_coupons'); // was causing problems on text widget DC 3/14/11
//add_filter('widget_text', 'filter_popular_coupons'); // was causing problems on text widget DC 3/14/11

//Register our WP 3 menus
if ( function_exists( 'register_nav_menus' ) ) {
    register_nav_menus( array(
        'primary' => __( 'Primary Navigation', APP_TD ),
        'secondary' => __( 'Footer Navigation', APP_TD ),
    ));
}

/* Replace Standard WP Menu Classes for cleaner CSS classes*/
function change_menu_classes($css_classes, $item) {
    $css_classes = str_replace("current-menu-item", "active", $css_classes);
    $css_classes = str_replace("current-menu-parent", "active", $css_classes);
    $css_classes = str_replace("current-menu-ancestor", "active", $css_classes);
    return $css_classes;
}
add_filter('nav_menu_css_class', 'change_menu_classes', 10, 2);

// add thumbnail support
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );

	// add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// create additional image sizes when images are uploaded
	set_post_thumbnail_size( 110, 90, true ); // blog post thumbnails
	add_image_size( 'thumb-small', 30, 30, true ); // used in the sidebar widget
	add_image_size( 'thumb-med', 75, 75, true ); // used on the admin coupon list view
	add_image_size( 'thumb-store', 150, 150, false ); // used on the store page
	add_image_size( 'thumb-featured', 160, 120, true ); // used in featured coupons slider
	add_image_size( 'thumb-large', 180, 180, true );
	add_image_size( 'thumb-large-preview', 250, 250, false ); // used on the admin edit store page
}

// Set the content width based on the theme's design and stylesheet.
// Used to set the width of images and content. Should be equal to the width the theme
// is designed for, generally via the style.css stylesheet.
if (!isset($content_width))
	$content_width = 600;


// localized text for the theme-scripts.js file
function clpr_theme_scripts_localization() {

	wp_localize_script( 'theme-scripts', 'theme_scripts_loc', array(
		'sendEmailHead' => __( 'Your email has been sent!', APP_TD ),
		'sendEmailTrue' => __( 'This coupon was successfully shared with', APP_TD ),
		'sendEmailFalse' => __( 'There was a problem sharing this coupon with', APP_TD )
	) );

}
add_filter( 'wp_print_scripts', 'clpr_theme_scripts_localization' );



// display the register link in the header if enabled
function clpr_register( $before = '<li>', $after = '</li>', $echo = true ) {

	if ( ! is_user_logged_in() ) {
		if ( get_option('users_can_register') )
			$link = $before . '<a href="' . appthemes_get_registration_url() . '">' . __( 'Register', APP_TD ) . '</a>' . $after;
		else
			$link = '';
	} else {
		$link = $before . '<a href="' . CLPR_DASHBOARD_URL . '">' . __( 'My Dashboard', APP_TD ) . '</a>' . $after;
	}

	if ( $echo )
		echo apply_filters('register', $link);
	else
		return apply_filters('register', $link);
}

// display the login message in the header
function clpr_login_head() {
    global $current_user;

    if (is_user_logged_in()) :
			$current_user = wp_get_current_user();
		  ?>
		  <li><a href="<?php echo CLPR_DASHBOARD_URL; ?>"><?php _e( 'My Dashboard', APP_TD ); ?></a></li><li><a href="<?php echo clpr_logout_url( home_url() ); ?>"><?php _e( 'Log out', APP_TD ); ?></a></li>
		<?php else : ?>
			<li><a href="<?php echo appthemes_get_registration_url(); ?>"><?php _e( 'Register', APP_TD ); ?></a></li><li><a href="<?php echo wp_login_url(); ?>"><?php _e( 'Login', APP_TD ); ?></a></li>
    <?php endif;

}

// return user name depend of account type
function clpr_get_user_name($user = false) {
	global $current_user;

	if (!$user && is_object($current_user))
		$user = $current_user;
	else if (is_numeric($user))
		$user = get_userdata($user);

	if (is_object($user)) {

		if ( 'fb-' == substr( $user->user_login, 0, 3 ) )
			$display_user_name = $user->display_name;
		else
			$display_user_name = $user->user_login;

		return $display_user_name;

	} else {
		return false;
	}
}

// return logout url depend of login type
function clpr_logout_url( $url = '' ) {
	global $app_abbr;

	if(!$url)
		$url = home_url();

	if( is_user_logged_in() ) :
		return wp_logout_url($url);
	else :
		return false;
	endif;

}

// correct logout url in admin bar
function clpr_admin_bar_render() {
  global $wp_admin_bar;

  if( is_user_logged_in() ) :
    $wp_admin_bar->remove_menu('logout');
  	$wp_admin_bar->add_menu( array(
  		'parent' => 'user-actions',
  		'id'     => 'logout',
  		'title'  => __( 'Log out', APP_TD ),
  		'href'   => clpr_logout_url(),
  	) );
  endif;

}
add_action( 'wp_before_admin_bar_render', 'clpr_admin_bar_render' );

// return link to user dashboard page
function clpr_get_dashboard_url( $context = 'display' ) {
	if ( defined('CLPR_DASHBOARD_URL') )
		$url = CLPR_DASHBOARD_URL;
	else
		$url = get_permalink( CLPR_User_Dashboard::get_id() );

	return esc_url( $url, null, $context );
}

// return link to user profile page
function clpr_get_profile_url( $context = 'display' ) {
	if ( defined('CLPR_PROFILE_URL') )
		$url = CLPR_PROFILE_URL;
	else
		$url = get_permalink( CLPR_User_Profile::get_id() );

	return esc_url( $url, null, $context );
}

// return link to submit coupon page
function clpr_get_submit_coupon_url( $context = 'display' ) {
	$url = get_permalink( CLPR_Coupon_Submit::get_id() );

	return esc_url( $url, null, $context );
}

// creates edit coupon link, use only in loop
function clpr_edit_coupon_link() {
  global $post, $current_user;
  if( is_user_logged_in() ) :
    if( current_user_can('manage_options') ) {
      edit_post_link( __( 'Edit Post', APP_TD ), '<p class="edit">', '</p>', $post->ID );
    } elseif( get_option('clpr_coupon_edit') == 'yes' && $post->post_author == $current_user->ID ) {
      $edit_link = add_query_arg('aid', $post->ID, CLPR_EDIT_URL);
      echo '<p class="edit"><a class="post-edit-link" href="' . $edit_link . '" title="' . __( 'Edit Coupon', APP_TD ).'">' . __( 'Edit Coupon', APP_TD ) . '</a></p>';
    }
  endif;
}


// returns a total count of all posts based on status and post type
function clpr_count_posts( $post_type, $status_type = 'publish' ) {
		$count_total = 0;
		$count_posts = wp_count_posts($post_type);
		if( is_array($status_type) )
			foreach($status_type as $status)
				$count_total += $count_posts->$status;
		else
			$count_total = $count_posts->$status_type;
		number_format($count_total);

		return $count_total;
}


// returns expire date of coupon
function clpr_get_expire_date( $post_id, $format = 'raw') {
		$expire_date = get_post_meta( $post_id, 'clpr_expire_date', true );
		if( !empty($expire_date) ) {
			switch( $format ) {
				case 'display':
					$expire_date = strtotime( str_replace('-', '/', $expire_date) );
					$expire_date = date_i18n( get_option('date_format'), $expire_date );
					return $expire_date;
				break;

				case 'time':
					$expire_date = strtotime( str_replace('-', '/', $expire_date) );
					return $expire_date;
				break;

				default://raw
					return $expire_date;
				break;
			}
		}
		return;
}


// display the coupon submission form
function clpr_show_coupon_form( $post = false ) {
	$errors = new WP_Error();
?>

<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	jQuery(document).ready(function() {

		jQuery(function() {
			jQuery(".datepicker").datepicker({
			dateFormat: 'mm-dd-yy',
			minDate: 0
			});
		});

		/* initialize the form validation */
		jQuery(function() {
			jQuery("#couponForm").validate({
				errorClass: "invalid",
				errorElement: "div"
			}).fadein;
		});

	});
	//-->!]]>
</script>


	<div class="blog">

		<h1><?php _e( 'Share a Coupon', APP_TD ); ?></h1>

		<div class="content-bar"></div>

		<div class="text-box-form">

			<p><?php _e( 'Complete the form below to share your coupon with us.', APP_TD ); ?></p>

		</div>

	</div> <!-- #blog -->

	<div class="post-box">

		<?php clipper_coupon_form( $post ); ?>

	</div> <!-- #post-box -->

<?php
}


// saves the coupon on the tpl-edit-item.php page template
function clpr_update_listing() {
	global $wpdb;

	// put the field names we expect into an array
	$fields = array(
		'cid',
		'post_title',
		'coupon_store',
		'store_url',
		'coupon_cat',
		'coupon_type_select',
		'clpr_coupon_code',
		'clpr_expire_date',
		'clpr_coupon_aff_url',
		'post_content',
		'tags_input'
	);

	if ( isset($_POST['clpr_store_id']) )
		$fields[] = 'clpr_store_id';


	// match the field names with the posted values
	// this process is to prevent unexpected field values from being passed in
	foreach( $fields as $field )
		$posted[ $field ] = isset( $_POST[ $field ] ) ? appthemes_clean( $_POST[ $field] ) : '';

	// check to see if html is allowed
	if ( get_option('clpr_allow_html') != 'yes' )
		$posted['post_content'] = appthemes_filter( $posted['post_content'] );

	// setup post array values
	$data = array(
		'ID' => trim( $posted['cid'] ),
		'post_title' => appthemes_filter( $posted['post_title'] ),
		'post_content' => trim( $posted['post_content'] ),

	);

	//print_r($update_item).' <- new ad array<br>'; // for debugging

	// update the item and return the id
	$post_id = wp_update_post($data);


	if ( $post_id ) {

		// now update the coupon store & url
		//wp_set_object_terms($post_id, $posted['coupon_store'], APP_TAX_STORE);
		//$term = get_term_by('name', $posted['coupon_store'], APP_TAX_STORE);
		//update_metadata(APP_TAX_STORE, $term->term_id, 'clpr_store_url', $posted['store_url']);

		// now update the coupon category
		// stupidly the cat id is passed in so we need to go back and grab the cat name before we can update it
		$cat_object = get_term_by('id', $posted['coupon_cat'], APP_TAX_CAT);
		wp_set_object_terms($post_id, $cat_object->name, APP_TAX_CAT);

		// set the coupon type
		if ( ! empty( $posted['coupon_type_select'] ) )
			wp_set_object_terms($post_id, $posted['coupon_type_select'], APP_TAX_TYPE, false);

		// update the tags
		if ( !empty($posted['tags_input']) ) {
			$new_tags = appthemes_clean_tags($posted['tags_input']);
			$new_tags = explode(',', $new_tags);
			wp_set_post_terms($post_id, $new_tags, APP_TAX_TAG, false);
		}

		// update meta data
		update_post_meta($post_id, 'clpr_coupon_code', $posted['clpr_coupon_code']);
		update_post_meta($post_id, 'clpr_coupon_aff_url', $posted['clpr_coupon_aff_url']);
		// check to see if pruning expired coupons is enabled
		if ( get_option('clpr_prune_coupons') != 'yes' )
			update_post_meta($post_id, 'clpr_expire_date', $posted['clpr_expire_date']);


		return $post_id;

	} else {
		// the ad wasn't updated so return false
		return false;

	}

}


// updates coupon status
function clpr_status_update($post_id, $post_status = null) {
	global $wpdb;

	$t = strtotime(date('d-m-Y'));
	$votes_down = get_post_meta($post_id, 'clpr_votes_down', true);
	$votes_percent = get_post_meta($post_id, 'clpr_votes_percent', true);
	$expire_date = get_post_meta($post_id, 'clpr_expire_date', true);
	if ($expire_date != '')
		$expire_date_time = strtotime( str_replace('-', '/', $expire_date) );
	else
		$expire_date_time = 0;

	if ( !$post_status )
		$post_status = get_post_status($post_id);

	if ( ($votes_percent < 50 && $votes_down != 0) || ($expire_date_time < $t && $expire_date != '') ) {
		if ( $post_status == 'publish' )
			$wpdb->update($wpdb->posts, array( 'post_status' => 'unreliable' ), array( 'ID' => $post_id ) );
	} else {
		if ( $post_status == 'unreliable' )
			$wpdb->update($wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $post_id ) );
	}

}


// go get the taxonomy store url custom field
function clpr_store_url($post_id, $tax_name, $tax_arg) {
	$term_id = appthemes_get_custom_taxonomy($post_id, $tax_name, $tax_arg);
	$the_store_url = get_metadata($tax_name, $term_id, 'clpr_store_url', true);
	return $the_store_url;
}


// return store image url with specified size
function clpr_get_store_image_url( $id, $type = 'post_id', $width = 110 ) {
	$store_url = false;
	$store_image_id = false;

	$sizes = array( 75 => 'thumb-med', 110 => 'post-thumbnail', 150 => 'thumb-store', 160 => 'thumb-featured', 250 => 'thumb-large-preview' );
	$sizes = apply_filters( 'clpr_store_image_sizes', $sizes );

	if ( ! array_key_exists( $width, $sizes ) )
		$width = 110;

	if ( ! isset( $sizes[ $width ] ) )
		$sizes[$width] = 'post-thumbnail';

	if ( $type == 'term_id' && $id ) {
		$store_url = get_metadata(APP_TAX_STORE, $id, 'clpr_store_url', true);
		$store_image_id = get_metadata(APP_TAX_STORE, $id, 'clpr_store_image_id', true);
	}

	if ( $type == 'post_id' && $id ) {
		$term_id = appthemes_get_custom_taxonomy($id, APP_TAX_STORE, 'term_id');
		$store_url = get_metadata(APP_TAX_STORE, $term_id, 'clpr_store_url', true);
		$store_image_id = get_metadata(APP_TAX_STORE, $term_id, 'clpr_store_image_id', true);
	}

	if ( is_numeric( $store_image_id ) ) {
		$store_image_src = wp_get_attachment_image_src( $store_image_id, $sizes[ $width ] );
		if ( $store_image_src )
			return $store_image_src[0];
	}

	if ( ! empty( $store_url ) ) {
		$store_image_url = "http://s.wordpress.com/mshots/v1/" . urlencode($store_url) . "?w=" . $width;
		return apply_filters( 'clpr_store_image', $store_image_url, $width, $store_url );
	} else {
		$store_image_url = get_bloginfo('template_url') . '/images/clpr_default.jpg';
		return apply_filters( 'clpr_store_default_image', $store_image_url, $width );
	}

}

// sets the thumbnail pic on the WP admin post
function clpr_set_ad_thumbnail($post_id, $thumbnail_id) {
    $thumbnail_html = wp_get_attachment_image($thumbnail_id, 'thumbnail');
    if (!empty($thumbnail_html)) {
        update_post_meta($post_id, '_thumbnail_id', $thumbnail_id);
        //die( _wp_post_thumbnail_html($thumbnail_id));
    }
}


// checks if coupon listing have printable coupon
function clpr_has_printable_coupon( $post_id ) {
	// go see if any images are associated with the coupon and grab the first one
	$images = get_children( array( 'post_parent' => $post_id, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', APP_TAX_IMAGE => 'printable-coupon', 'order' => 'ASC', 'orderby' => 'ID' ) );

	if ( $images )
		return true;

	$image_url = get_post_meta($post_id, 'clpr_print_url', true);
	if ( ! empty( $image_url ) )
		return true;

	return false;
}


// get the printable coupon image associated to the coupon
function clpr_get_printable_coupon( $post_id, $size = 'thumb-large', $return = 'html' ) {
	// go see if any images are associated with the coupon and grab the first one
	$images = get_children( array( 'post_parent' => $post_id, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', APP_TAX_IMAGE => 'printable-coupon', 'order' => 'ASC', 'orderby' => 'ID' ) );

	if ( $images ) {

		// move over bacon
		$image = array_shift( $images );

		// get the coupon image
		$couponimg = wp_get_attachment_image( $image->ID, $size );

		// grab the large image for onclick
		$adlargearray = wp_get_attachment_image_src( $image->ID, 'large' );
		$img_large_url_raw = $adlargearray[0];

		if ( $couponimg ) {
			if ( $return == 'url' ) {
				return $img_large_url_raw;
			} elseif( $return == 'id' ) {
				return $image->ID;
			} else {
				return '<a href="'. $img_large_url_raw .'" target="_blank" title="'. the_title_attribute('echo=0') .'" class="preview" rel="'. $img_large_url_raw .'">'. $couponimg .'</a>';
			}
		}

	// if no image found, try to find in meta (coupons from importer)
	} else {
		$image_url = get_post_meta($post_id, 'clpr_print_url', true);
		if ( ! empty( $image_url ) ) {
			if ( $size == 'thumb-med' ) {
				$size_out = 'width="75" height="75" class="attachment-thumb-med"';
			} else {
				$size_out = 'width="180" height="180" class="attachment-thumb-large"';
			}

			if ( $return == 'url' ) {
				return $image_url;
			} elseif( $return == 'id' ) {
				return 'postmeta';
			} else {
				$post = get_post( $post_id );
				return '<a href="'. $image_url .'" target="_blank" title="'. $post->post_title .'" class="preview" rel="'. $image_url .'"><img '. $size_out .' title="'. $post->post_title .'" alt="'. $post->post_title .'" src="'. $image_url .'" /></a>';
			}
		}
	}

	return false;
}


// removes assigned to post printable coupons
function clpr_remove_printable_coupon( $post_id ) {
	// go see if any images are associated with the coupon
	$images = get_children( array( 'post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', APP_TAX_IMAGE => 'printable-coupon', 'order' => 'ASC', 'orderby' => 'ID' ) );

	if ( $images ) {
		foreach( $images as $image ) {
			wp_set_object_terms( $image->ID, NULL, APP_TAX_IMAGE, false );
			wp_delete_attachment( $image->ID, true );
		}
	}

	delete_post_meta( $post_id, 'clpr_print_url' );
	delete_post_meta( $post_id, 'clpr_print_imageid' );

	return true;
}


// validate coupon expiration date, return bool
function clpr_is_valid_expiration_date( $date ) {
	if ( empty( $date ) )
		return false;

	if ( ! preg_match( "/^(\d{2})-(\d{2})-(\d{4})$/", $date, $date_parts ) ) // month, day, year
		return false;

	if ( ! checkdate( $date_parts[1], $date_parts[2], $date_parts[3] ) )
		return false;

	$timestamp = strtotime( str_replace( '-', '/', $date ) ) + ( 24 * 3600 ); // + 24h, coupons expire in the end of day
	if ( current_time('timestamp') > $timestamp )
		return false;

	return true;
}


// get the printable coupon image associated to the coupon, use only in loop
function clpr_get_coupon_image( $size = 'thumb-large', $return = 'html' ) {
	global $post;

	echo clpr_get_printable_coupon( $post->ID, $size, $return );
}


// get the coupon upload directory path
function clpr_upload_path( $pathdata ) {
	$subdir = '/coupons'.$pathdata['subdir'];
	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
	$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
	return $pathdata;
}


function clpr_primary_nav_menu() {
		global $post;

		$active = '';
		$submit_page_id = CLPR_Coupon_Submit::get_id();
		$pages = get_pages( array('number' => 20) ); // set limit for a case that user have a lot of pages
		$pages_list = array('share-coupon', 'home', 'stores', 'categories', 'about', 'blog', 'contact');

		echo '<ul id="nav" class="menu">';
		foreach ($pages_list as $slug) {
			foreach ($pages as $page) {
				if ($page->post_name == $slug) {
					if ($page->ID == $submit_page_id) $submit_page_class = 'menu-arrow'; else $submit_page_class = '';
					if ($post) { $active = (($page->ID == $post->ID || ($page->ID == get_option('page_for_posts') && $post->ID == 1))? 'active' : ''); }
					echo '<li id="menu-item-'.$page->ID.'" class="menu-item '.$active.' '.$submit_page_class.'"><a href="'.get_permalink($page->ID).'">' . $page->post_title . '</a></li>';
				}
			}
		}
		echo '</ul>';

}

function clpr_footer_nav_menu() {
		global $post;

		$pages = get_pages( array('number' => 20) ); // set limit for a case that user have a lot of pages
		$pages_list = array('home', 'stores', 'categories', 'about', 'blog', 'contact');

		echo '<ul id="menu-footer-menu" class="menu">';
		foreach ($pages_list as $slug) {
			foreach ($pages as $page) {
				if ($page->post_name == $slug) {
					echo '<li id="footer-menu-item-'.$page->ID.'" class="menu-item"><a href="'.get_permalink($page->ID).'">'.$page->post_title.'</a></li>';
				}
			}
		}
		echo '</ul>';

}


// adds custom css class for submit coupon menu item
function clpr_add_css_class_submit_menu_item( $items, $args ) {
	$submit_page_id = CLPR_Coupon_Submit::get_id();
	foreach ($items as $key => $item){
		if($item->object_id == $submit_page_id){
			$item->classes[] = 'menu-arrow';
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'clpr_add_css_class_submit_menu_item', 10, 2 );


// return array of hidden stores ids
function clpr_hidden_stores() {
		global $wpdb, $hidden_stores;

		if(!isset($hidden_stores) || !is_array($hidden_stores)) {
				// get ids of all hidden stores
				$hidden_stores_query = "SELECT $wpdb->clpr_storesmeta.stores_id FROM $wpdb->clpr_storesmeta WHERE $wpdb->clpr_storesmeta.meta_key = %s AND $wpdb->clpr_storesmeta.meta_value = %s";
				$hidden_stores = $wpdb->get_col( $wpdb->prepare($hidden_stores_query, 'clpr_store_active', 'no') );
		}

		return $hidden_stores;
}


// print store links by most popular
function clpr_popular_stores($the_limit = 5, $before = '', $after = '') {
		global $wpdb;

		$hidden_stores = clpr_hidden_stores();
		$stores_array = get_terms( APP_TAX_STORE, array('orderby' => 'count', 'hide_empty' => 1, 'number' => $the_limit, 'exclude' => $hidden_stores ) );

		if ($stores_array && is_array($stores_array)):
				foreach ( $stores_array as $store ) {
						$link = get_term_link($store, APP_TAX_STORE);
						echo $before . '<a class="tax-link" href="'.$link.'">'.$store->name.'</a>'. $after;
				}
		endif;
}


// ajax auto-complete search for store name
function clpr_store_suggest() {
	global $wpdb;

	if ( !isset($_GET['tax']) )
		die('0');

	$taxonomy = $_GET['tax'];
	if ( !taxonomy_exists( $taxonomy ) )
		die('0');

	$s = $_GET['term']; // is this slashed already?

	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = end( $s );
	}

	$s = trim( $s );
	if ( strlen( $s ) < 2 )
		die; // require 2 chars for matching

	$sql = "SELECT t.slug FROM $wpdb->term_taxonomy AS tt INNER JOIN
		$wpdb->terms AS t ON tt.term_id = t.term_id
		WHERE tt.taxonomy = %s
		AND t.name LIKE ('%%" . esc_sql( like_escape( $s ) ) . "%%')
		LIMIT 50
		";


	$sql = $wpdb->prepare( $sql, $taxonomy );

	$terms = $wpdb->get_col($sql);


	// return the term details via json
	if(empty($terms)){
		echo json_encode($terms);
		die;
	} else {
		$i = 0;
		$results = array();
		foreach ($terms as $term) {

			$obj = get_term_by( 'slug', $term, $taxonomy );

			// Don't return stores with no active coupons or hidden stores
			if( ($obj->count < 1) || (get_metadata($obj->taxonomy, $obj->term_id, 'clpr_store_active', true) == 'no') )
				continue;

			$results[$i] = $obj;
			$results[$i]->clpr_store_url = get_metadata($results[$i]->taxonomy, $results[$i]->term_id, 'clpr_store_url', true);
			$results[$i]->clpr_store_image_url = clpr_get_store_image_url( $results[$i]->term_id, 'term_id', '110' );
			$i++;

			// Limit to 5 search results
			if($i == 5){
				break;
			}
		}
		echo json_encode($results);
		die;
	}
}


// creates the charts on the dashboard
function clpr_dashboard_charts() {
	global $wpdb;

	$sql = $wpdb->prepare( "SELECT COUNT(post_title) as total, post_date FROM $wpdb->posts WHERE post_type = %s AND post_date > %s GROUP BY DATE(post_date) DESC", APP_POST_TYPE, date('Y-m-d', strtotime('-30 days')) );
	$results = $wpdb->get_results($sql);

	$listings = array();

	// put the days and total posts into an array
	foreach ($results as $result) {
		$the_day = date('Y-m-d', strtotime($result->post_date));
		$listings[$the_day] = $result->total;
	}

	// setup the last 30 days
	for($i = 0; $i < 30; $i++) {
		$each_day = date('Y-m-d', strtotime('-'. $i .' days'));

		// if there's no day with posts, insert a goose egg
		if (!in_array($each_day, array_keys($listings))) $listings[$each_day] = 0;
	}

	// sort the values by date
	ksort($listings);

?>

<div id="placeholder"></div>

<script type="text/javascript">
// <![CDATA[
jQuery(function () {

    var posts = [
		<?php
		foreach ($listings as $day => $value) {
			$sdate = strtotime($day);
			$sdate = $sdate * 1000; // js timestamps measure milliseconds vs seconds
			$newoutput = "[$sdate, $value],\n";
			//$theoutput[] = $newoutput;
			echo $newoutput;
		}
		?>
	];

	// var sales = [
		 <?php
		// foreach ($sales as $day => $value) {
			// $sdate = strtotime($day);
			// $sdate = $sdate * 1000; // js timestamps measure milliseconds vs seconds
			// $newoutput = "[$sdate, $value],\n";
			////////$theoutput[] = $newoutput;
			// echo $newoutput;
		// }
		 ?>
	// ];


	var placeholder = jQuery("#placeholder");

	var output = [
		{
			data: posts,
			label: "<?php _e( 'New Coupons', APP_TD ); ?>",
			symbol: ''
		}
	];

	var options = {
       series: {
		   lines: { show: true },
		   points: { show: true }
	   },
	   grid: {
		   tickColor:'#f4f4f4',
		   hoverable: true,
		   clickable: true,
		   borderColor: '#f4f4f4',
		   backgroundColor:'#FFFFFF'
	   },
       xaxis: { mode: 'time',
				timeformat: "%m/%d"
	   },
	   yaxis: { min: 0 },
	   y2axis: { min: 0, tickFormatter: function (v, axis) { return "$" + v.toFixed(axis.tickDecimals) }},
	   legend: { position: 'nw' }
    };

	jQuery.plot(placeholder, output, options);

	// reload the plot when browser window gets resized
	jQuery(window).resize(function() {
		jQuery.plot(placeholder, output, options);
	});

	function showChartTooltip(x, y, contents) {
		jQuery('<div id="charttooltip">' + contents + '</div>').css( {
		position: 'absolute',
		display: 'none',
		top: y + 5,
		left: x + 5,
		opacity: 1
		}).appendTo("body").fadeIn(200);
	}

	var previousPoint = null;
	jQuery("#placeholder").bind("plothover", function (event, pos, item) {
		jQuery("#x").text(pos.x.toFixed(2));
		jQuery("#y").text(pos.y.toFixed(2));
		if (item) {
			if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;

				jQuery("#charttooltip").remove();
				var x = new Date(item.datapoint[0]), y = item.datapoint[1];
				var xday = x.getDate(), xmonth = x.getMonth()+1; // jan = 0 so we need to offset month
				showChartTooltip(item.pageX, item.pageY, xmonth + "/" + xday + " - <b>" + item.series.symbol + y + "</b> " + item.series.label);
			}
		} else {
			jQuery("#charttooltip").remove();
			previousPoint = null;
		}
	});
});
// ]]>
</script>

<?php // print_r($theoutput); ?>

<?php
}


// email coupon social pop-up form
function clpr_email_form() {

    $comment_author = '';
    $comment_author_email = '';
    $comment_author_url = '';

    global $id;
    global $post;
    $post = get_post( $_GET['id'] );


    if ( isset($_COOKIE['comment_author_'.COOKIEHASH]) ) {
        $comment_author = apply_filters('pre_comment_author_name', $_COOKIE['comment_author_'.COOKIEHASH]);
        $comment_author = stripslashes($comment_author);
        $comment_author = esc_attr($comment_author);
        $_COOKIE['comment_author_'.COOKIEHASH] = $comment_author;
    }

    if ( isset($_COOKIE['comment_author_email_'.COOKIEHASH]) ) {
        $comment_author_email = apply_filters('pre_comment_author_email', $_COOKIE['comment_author_email_'.COOKIEHASH]);
        $comment_author_email = stripslashes($comment_author_email);
        $comment_author_email = esc_attr($comment_author_email);
        $_COOKIE['comment_author_email_'.COOKIEHASH] = $comment_author_email;
    }

    if ( isset($_COOKIE['comment_author_url_'.COOKIEHASH]) ) {
        $comment_author_url = apply_filters('pre_comment_author_url', $_COOKIE['comment_author_url_'.COOKIEHASH]);
        $comment_author_url = stripslashes($comment_author_url);
        $_COOKIE['comment_author_url_'.COOKIEHASH] = $comment_author_url;
    }

?>

<div class="content-box comment-form">

    <div class="box-t">&nbsp;</div>

    <div class="box-c">

        <div class="box-holder">

            <div class="post-box">

                <div class="head"><h3><?php _e( 'Email to a Friend:', APP_TD ); ?> &#8220;<?php the_title(); ?>&#8221;</h3></div>

				<div id="respond" class="email-wrap">

					<form action="<?php echo admin_url('admin-ajax.php'); ?>?action=send-email" method="post" id="commentform-<?php echo $post->ID; ?>" class="commentForm">

						<?php if ( is_user_logged_in() ) : global $user_identity; ?>

							<p><?php printf( __( 'Logged in as <a href="%1$s">%2$s</a>.', APP_TD ), CLPR_PROFILE_URL, $user_identity ); ?> <a href="<?php echo clpr_logout_url(get_permalink()); ?>"><?php _e( 'Log out &raquo;', APP_TD ); ?></a></p>

						<?php endif; ?>

						<p>
							<label><?php _e( 'Your Name:', APP_TD ); ?></label>
							<input type="text" class="text required" name="author" id="author-<?php echo $post->ID; ?>" value="<?php echo esc_attr($comment_author); ?>" />
						</p>

						<p>
							<label><?php _e( 'Your Email:', APP_TD ); ?></label>
							<input type="text" class="text required email" name="email" id="email-<?php echo $post->ID; ?>" value="<?php echo esc_attr($comment_author_email); ?>" />
						</p>

						<p>
							<label><?php _e( 'Recipients Email:', APP_TD ); ?></label>
							<input type="text" class="text required email" name="recipients" id="recipients-<?php echo $post->ID; ?>" value="" />
						</p>

						<p>
							<label><?php _e( 'Your Message:', APP_TD ); ?></label>
							<textarea cols="30" rows="10" name="message" class="commentbox required" id="message-<?php echo $post->ID; ?>"></textarea>
						</p>

						<p>
							<button type="submit" class="send-email btn submit" id="submit-<?php echo $_GET['id']; ?>" name="submitted" value="submitted"><?php _e( 'Send Email', APP_TD ); ?></button>
							<input type='hidden' name='post_ID' value='<?php echo $post->ID; ?>' class='post_ID' />
							<input type='hidden' name='submitted' value='submitted' />
						</p>

						<?php do_action('comment_form', $post->ID); ?>

					</form>

				</div>

            </div>

        </div>

    </div>

    <div class="box-b">&nbsp;</div>

</div>

<?php
die;
}


function clpr_send_email_ajax() {
	global $wpdb, $app_abbr;

	nocache_headers();

	$post_ID = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;
	$post = get_post($post_ID);

	$errors = new WP_Error();

	$fields = array(
		'author',
		'email',
		'recipients',
		'message',
		'post_ID'
	);

	if ( isset($_POST['checking']) ) {
		$fields[] = 'checking';
	}

	// Get (and clean) data
	foreach ( $fields as $field ) {
		$posted[ $field ] = stripslashes( trim( $_POST[ $field ] ) );
	}

	// Check required fields
	$required = array(
		'author' => __( 'Your Name', APP_TD ),
		'email' => __( 'Your Email', APP_TD ),
		'recipients' => __( 'Recipients', APP_TD ),
	);


	foreach ( $required as $field => $name ) {
		if ( empty( $posted[ $field ] ) ) {
			$errors->add( 'submit_error', sprintf( __( '<strong>ERROR</strong>: &ldquo;%s&rdquo; is a required field.', APP_TD ), $name ) );
		}
	}

	//If there is no error, send the email
	if ( $errors && sizeof( $errors ) > 0 && $errors->get_error_code() ) {

		wp_die( __( 'Sorry, there was a problem.', APP_TD ) );

	} else {

		$from_name = $posted['author'];
		$from_email = $posted['email'];
		$the_message = $posted['message'];
		$post = get_post( $posted['post_ID'] );
		$posted['recipients'] = str_replace(' ', '', $posted['recipients']);
		$recipients = explode(',', $posted['recipients']);
		$link = get_permalink($post_ID);
		$blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
		$results = array();

		foreach ( $recipients as $recipient ) {

			if ( ! is_email( $recipient ) ) {
				$errors->add( 'submit_error', __( '<strong>ERROR</strong>: Please enter a valid email address.', APP_TD ) );
			} else {

				$mailto = $recipient;
				$subject = sprintf( __( '%s shared a coupon with you from %s', APP_TD ), $from_name, $blogname );
				$headers = "From: $from_name <$from_email> \r\n";
				$headers .= "Reply-To: $from_name <$from_email> \r\n";

				$message  = __( 'Hi,', APP_TD ) . "\r\n\r\n";
				$message .= sprintf( __( '%s thought you might be interested in the following coupon.', APP_TD ), $from_name ) . "\r\n\r\n";
				$message .= sprintf( __( 'View coupon: %s', APP_TD ), $link ) . "\r\n\r\n";
				$message .= sprintf( __( 'Message: %s', APP_TD ), $the_message ) . "\r\n\r\n\r\n";
				$message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
				$message .= sprintf( __( 'Your %s Team', APP_TD ), $blogname ) . "\r\n";
				$message .= home_url( '/' ) . "\r\n\r\n";

				wp_mail($mailto, $subject, $message, $headers);

				$results[$recipient]['success'] = true;
				$results[$recipient]['recipient'] = $recipient;
			}
		}

		echo json_encode( $results );

	}
	die;
}

// Provides joins for expired coupon filters
function clpr_expired_coupons_joins( $join, $wp_query ){
    global $wpdb;
    if ( $wp_query->get( 'not_expired_coupons' ) || $wp_query->get('filter_unreliable') ) {
	    $join .= " INNER JOIN $wpdb->postmeta AS exp1 ON ($wpdb->posts.ID = exp1.post_id) ";
	    $join .= " INNER JOIN $wpdb->postmeta AS exp2 ON ($wpdb->posts.ID = exp2.post_id) ";

	    // Only provide second join to queries that need it
	    $join .= " INNER JOIN $wpdb->postmeta AS exp3 ON ($wpdb->posts.ID = exp3.post_id) ";

    }
    return $join;

}
add_filter( 'posts_join', 'clpr_expired_coupons_joins', 10, 2);

// Filters out anything that isn't unreliable or expired
function clpr_filter_unreliable_coupons( $where ){
    global $wp_query;
    global $wpdb;

    if(!$wp_query->get("filter_unreliable"))
	    return $where;

    $not_zero = " ( exp1.meta_key = 'clpr_votes_down' AND CAST( exp1.meta_value AS SIGNED) NOT BETWEEN '0' AND '0' ) ";

    $low_percent = " ( exp2.meta_key = 'clpr_votes_percent' AND CAST( exp2.meta_value AS SIGNED ) BETWEEN '0' AND '50' ) ";

    $votes_match = " ( $low_percent AND $not_zero ) ";

    $expired = " ( exp3.meta_key = 'clpr_expire_date' AND STR_TO_DATE( exp3.meta_value, '%c-%d-%Y') < CURRENT_DATE() ) ";

    $not_empty = " ( exp3.meta_key = 'clpr_expire_date' AND exp3.meta_value != '' ) ";

    $expired_match = " ( $expired AND $not_empty ) ";

    $meta_matches = " ( $votes_match OR $expired_match )";

    $where .= "
		AND ( $meta_matches ) ";

    return $where;
}
add_filter("posts_where", "clpr_filter_unreliable_coupons");

// Filters out expired coupons
function clpr_not_expired_coupons_filter( $where, $wp_query ){
    global $wpdb;
    if ( $wp_query->get( 'not_expired_coupons' ) ) {
	    $where .= " AND ( (exp1.meta_key = 'clpr_expire_date' AND STR_TO_DATE( exp1.meta_value, '%c-%d-%Y') >= CURRENT_DATE()) OR ( exp1.meta_key = 'clpr_expire_date' AND exp1.meta_value = '') )";
    }
    return $where;

}
add_filter( 'posts_where', 'clpr_not_expired_coupons_filter', 10, 2);

// Filters out non-expired coupons
function clpr_expired_coupons_filter( $where, $wp_query ){
    global $wpdb;
    if ( $wp_query->get( 'expired_coupons' ) ) {
	    $where .= " AND ($wpdb->postmeta.meta_key = 'clpr_expire_date' AND STR_TO_DATE($wpdb->postmeta.meta_value, '%c-%d-%Y') < CURRENT_DATE())";
    }
    return $where;

}
add_filter( 'posts_where', 'clpr_expired_coupons_filter', 10, 2);

function clpr_coupon_prune(){

    if( get_option('clpr_prune_coupons') == 'no' || get_option('clpr_prune_coupons') == false)
    	return;

    // Get all coupons with an expired date that have expired
    $args = array(
	    'post_type' => APP_POST_TYPE,
	    'expired_coupons' => true,
	    'posts_per_page' => -1,
	    'fields' => 'ids',
	    'meta_query' => array(
  		    array(
        			'key' => 'clpr_expire_date',
              'value' => '',
              'compare' => '!='
  		    )
	    )
    );
    $expired = new WP_Query($args);
    $messageDetails = '';

    if(isset($expired->posts) && is_array($expired->posts))
      foreach($expired->posts as $post_id){
        wp_update_post( array('ID' => $post_id, 'post_status' => 'draft') );
        $messageDetails .= add_query_arg( array( 'p' => $post_id ), home_url('/') ) . "\r\n";
      }

    if($messageDetails == '')
      $messageDetails = __( 'No expired coupons were found.', APP_TD );
    else
      $messageDetails = __( 'The following coupons expired and have been taken down from your website: ', APP_TD ) . "\r\n" . $messageDetails;

    $message = __( 'Your cron job has run successfully. ', APP_TD ) . "\r\n" . $messageDetails . "\r\n" . __( 'Regards', APP_TD ) . ", \r\n" . __( 'Clipper', APP_TD );

    if(get_option('clpr_prune_coupons_email') == 'yes'){
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
      $headers = 'From: '. $blogname .' <'. get_option( 'admin_email' ) .'>' . "\r\n";
      wp_mail(get_option('admin_email'), __(' Clipper Coupons Expired', APP_TD ), $message, $headers);
    }

}
add_action('clpr_coupon_prune', 'clpr_coupon_prune');


// Schedules a daily event to prune coupons who have expired
function clpr_schedule_coupon_prune() {
	if (!wp_next_scheduled('clpr_coupon_prune'))
		wp_schedule_event(time(), 'daily', 'clpr_coupon_prune');
}
add_action('init', 'clpr_schedule_coupon_prune');


// tinyMCE text editor
function clpr_tinymce( $width = 420, $height = 300 ) {
?>
<script type="text/javascript">
		tinyMCEPreInit = {
			base : "<?php echo includes_url('js/tinymce'); ?>",
			suffix : "",
			mceInit : {
				mode : "specific_textareas",
				editor_selector : "mceEditor",
				theme : "advanced",
				plugins : "inlinepopups",
				skin : "default",
				theme_advanced_buttons1 : "formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor",
				theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,cleanup,code",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				theme_advanced_resize_horizontal : false,
				content_css : "<?php echo get_bloginfo('stylesheet_directory'); ?>/style.css",
				languages : 'en',
				disk_cache : true,
				width : "<?php echo $width; ?>",
				height : "<?php echo $height; ?>",
				language : 'en',
				setup : function(editor) {
					editor.onKeyUp.add(function(editor, e) {
						tinyMCE.triggerSave();
						jQuery("#" + editor.id).valid();
					});
				}

			},
			load_ext : function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
		(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.mceInit.language,th=t.mceInit.theme;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');})();
		tinyMCE.init(tinyMCEPreInit.mceInit);
</script>

<?php
}

// Displays coupon type/code box
if ( !function_exists('clpr_coupon_code_box') ) :
  function clpr_coupon_code_box( $coupon_type = null ) {
    global $post;

    if(!$coupon_type)
      $coupon_type = appthemes_get_custom_taxonomy($post->ID, APP_TAX_TYPE, 'slug_name');

    switch($coupon_type) {
      case 'printable-coupon':
?>
				  <h5><?php _e( 'Code:', APP_TD ); ?></h5>
					<div class="couponAndTip">
              <div class="link-holder">
    							<a href="<?php clpr_get_coupon_image('thumb-med', 'url'); ?>" id="coupon-link-<?php echo $post->ID; ?>" class="coupon-code-link" title="<?php _e( 'Click to Print', APP_TD ); ?>" target="_blank" data-rel="<?php _e( 'Print Coupon', APP_TD ); ?>"><span><?php _e( 'Print Coupon', APP_TD ); ?></span></a>
  						</div> <!-- #link-holder -->
  						<p class="link-popup"><span><?php _e( 'Click to print coupon', APP_TD ); ?></span></p>
          </div><!-- /couponAndTip -->
<?php
        break;

      case 'coupon-code':
?>
					<h5><?php _e( 'Code:', APP_TD ); ?></h5>
					<div class="couponAndTip">
							<div class="link-holder">
								<?php if( get_option('clpr_coupon_code_hide') == 'yes' ) $button_text = __( 'Show Coupon Code', APP_TD ); else $button_text = wptexturize( get_post_meta( $post->ID, 'clpr_coupon_code', true ) ); ?>
									<a href="<?php echo clpr_get_coupon_out_url( $post ); ?>" id="coupon-link-<?php echo $post->ID; ?>" class="coupon-code-link" title="<?php _e( 'Click to copy &amp; open site', APP_TD ); ?>" target="_blank" data-rel="<?php echo wptexturize( get_post_meta( $post->ID, 'clpr_coupon_code', true ) ); ?>"><span><?php echo $button_text; ?></span></a>
							</div> <!-- #link-holder -->
							<p class="link-popup"><span><?php _e( 'Click to copy &amp; open site', APP_TD ); ?></span></p>
					</div><!-- /couponAndTip -->
<?php
        break;

      default:
?>
				  <h5><?php _e( 'Promo:', APP_TD ); ?></h5>
					<div class="couponAndTip">
              <div class="link-holder">
    							<a href="<?php echo clpr_get_coupon_out_url( $post ); ?>" id="coupon-link-<?php echo $post->ID; ?>" class="coupon-code-link" title="<?php _e( 'Click to open site', APP_TD ); ?>" target="_blank" data-rel="<?php _e( 'Click to Redeem', APP_TD ); ?>"><span><?php _e( 'Click to Redeem', APP_TD ); ?></span></a>
  						</div> <!-- #link-holder -->
  						<p class="link-popup"><span><?php _e( 'Click to open site', APP_TD ); ?></span></p>
          </div><!-- /couponAndTip -->
<?php
        break;
    } // end switch
  }
endif;


// load all page templates, setup cache, limits db queries
function clpr_load_all_page_templates() {
	$pages = get_posts( array(
		'post_type' => 'page',
		'meta_key' => '_wp_page_template',
		'posts_per_page' => -1,
	) );

}


// updates post status
function clpr_update_post_status( $post_id, $new_status ) {
	wp_update_post( array(
		'ID' => $post_id,
		'post_status' => $new_status
	) );
}


// deletes coupon listing together with associated attachments, votes, stats, reports
function clpr_delete_coupon( $post_id ) {
	global $wpdb;

	$attachments_query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type='attachment'", $post_id );
	$attachments = $wpdb->get_results( $attachments_query );

	// delete all associated attachments
	if ( $attachments )
		foreach( $attachments as $attachment )
			wp_delete_attachment( $attachment->ID, true );

	// delete all votes from tables
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_votes_total WHERE post_id = '%d'", $post_id ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_votes WHERE post_id = '%d'", $post_id ) );

	// delete all stats from tables
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_pop_total WHERE postnum = '%d'", $post_id ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_pop_daily WHERE postnum = '%d'", $post_id ) );

	// delete all reports from tables
	$report = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->clpr_report WHERE postID = '%d'", $post_id ) );
	if ( $report != null ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_report_comments WHERE reportID = '%d'", $report->id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_report WHERE postID = '%d'", $post_id ) );
	}

	// delete post and it's revisions, comments, meta
	if ( wp_delete_post( $post_id, true ) )
		return true;
	else
		return false;
}


// run the appthemes_init() action hook
appthemes_init();

?>
