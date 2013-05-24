<?php

class CLPR_Blog_Archive extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-blog.php', __( 'Blog', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}
}


class CLPR_Coupon_Categories extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-coupon-cats.php', __( 'Categories', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}
}


class CLPR_Coupon_Stores extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-stores.php', __( 'Stores', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}
}


class CLPR_Coupon_Submit extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-submit-coupon.php', __( 'Share Coupon', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {
		// redirect to dashboard if can't renew
		if ( isset( $_GET['renew'] ) )
			CLPR_Coupon_Submit::can_renew_coupon();

		// this is needed for IE to work with the go back button
		header( "Cache-control: private" );
		// add js files to wp_head. tiny_mce and validate
		add_action( 'wp_print_scripts', 'clpr_load_form_scripts' );

	}

	function can_renew_coupon() {
		if ( isset( $_GET['renew'] ) ) {
			if ( ! is_numeric( $_GET['renew'] ) || $_GET['renew'] != preg_replace( '/[^0-9]/', '', $_GET['renew'] ) )
				return CLPR_Coupon_Submit::redirect_dashboard('renew-invalid-id');

			$post = get_post( $_GET['renew'] );
			if ( ! $post )
				return CLPR_Coupon_Submit::redirect_dashboard('renew-invalid-id');

			if ( ! in_array( $post->post_status, array( 'draft' ) ) )
				return CLPR_Coupon_Submit::redirect_dashboard('renew-not-expired');

			$expire_date = clpr_get_expire_date($post->ID, 'time') + ( 24 * 3600 ); // + 24h, coupons expire in the end of day
			if ( $expire_date > current_time('timestamp') )
				return CLPR_Coupon_Submit::redirect_dashboard('renew-not-expired');

		}
	}

	function redirect_dashboard( $reason ) {
		$redirect_url = add_query_arg( array( $reason => 'true' ), CLPR_DASHBOARD_URL );
		wp_redirect( $redirect_url );
		exit();
	}

}


class CLPR_Edit_Item extends APP_View_Page {

	private $error;

	function __construct() {
		parent::__construct( 'tpl-edit-item.php', __( 'Edit Item', APP_TD ) );
		add_action( 'init', array( $this, 'update' ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function update() {
		if ( !isset( $_POST['action'] ) || 'clpr-edit-item' != $_POST['action'] || !current_user_can( 'edit_posts' ) )
			return;

		check_admin_referer( 'clpr-edit-item' );

		// needed for image uploading and deleting to work
		include_once (ABSPATH . 'wp-admin/includes/file.php');
		include_once (ABSPATH . 'wp-admin/includes/image.php');

		if ( isset( $_POST['clpr_expire_date'] ) && ! clpr_is_valid_expiration_date( $_POST['clpr_expire_date'] ) )
			$this->error[] = __( 'Invalid coupon expiration date.', APP_TD );

		$file_data = false;
		$is_printable = ( isset( $_POST['coupon_type_select'] ) && $_POST['coupon_type_select'] == 'printable-coupon' );
		if ( empty( $this->error ) && $is_printable && isset( $_FILES['coupon-upload'] ) && ! empty( $_FILES['coupon-upload']['name'] ) ) {

			// make sure the file uploaded is an approved type (i.e. jpg, png, gif, etc)
			$allowed = explode(',', get_option('clpr_submit_file_types') );
			$extension = strtolower( pathinfo( $_FILES['coupon-upload']['name'], PATHINFO_EXTENSION ) );
			if ( ! in_array( $extension, $allowed ) )
				$this->error[] = __( 'Invalid file type.', APP_TD );

			if ( empty( $this->error ) ) {
				$file = wp_handle_upload( $_FILES['coupon-upload'], array( 'test_form' => false ) );
				if ( ! isset( $file['error'] ) ) {
					$file_data['coupon-upload-url'] = $file['url'];
					$file_data['coupon-upload-type'] = $file['type'];
					$file_data['coupon-upload-file'] = $file['file'];
					$file_data['coupon-upload-name'] = $_FILES['coupon-upload']['name'];
					$name_parts = pathinfo( $_FILES['coupon-upload']['name'] );
					$file_data['coupon-upload-filename'] = trim( $name_parts['filename'] );
				} else {
					$this->error[] = sprintf( __( 'Error: %s', APP_TD ), $file['error'] );
				}
			}

		}

		$this->error = apply_filters( 'clpr_coupon_validate_fields', $this->error );

		// update the coupon
		$post_id = ( empty( $this->error ) ) ? clpr_update_listing() : false;
		if ( ! $post_id )
			$this->error[] = __( 'There was an error trying to update your coupon.', APP_TD );

		// if it's a printable coupon, upload image
		if ( empty( $this->error ) && $file_data ) {

			// Remove old assigned printable coupons
			clpr_remove_printable_coupon( $post_id );

			$title = $file_data['coupon-upload-filename'];
			$content = '';

			// use image exif/iptc data for title and caption defaults if possible
			if ( $image_meta = @wp_read_image_metadata( $file_data['coupon-upload-file'] ) ) {
				if ( trim( $image_meta['title'] ) )
					$title = $image_meta['title'];
				if ( trim( $image_meta['caption'] ) )
					$content = $image_meta['caption'];
			}

			$attachment = array(
				'post_mime_type' => $file_data['coupon-upload-type'],
				'guid' => $file_data['coupon-upload-url'],
				'post_parent' => $post_id,
				'post_title' => $title,
				'post_content' => $content,
			);

			$attachment_id = wp_insert_attachment($attachment, $file_data['coupon-upload-file'], $post_id);
			if ( ! is_wp_error( $attachment_id ) ) {
				wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file_data['coupon-upload-file'] ) );
        wp_set_object_terms( $attachment_id, 'printable-coupon', APP_TAX_IMAGE, false );
			} else {
				$this->error[] = __( 'There was an error while adding printable coupon.', APP_TD );
			}

		}

	}

	function template_redirect() {
		appthemes_auth_redirect_login(); // if not logged in, redirect to login page
		nocache_headers();
		// add js files to wp_head. tiny_mce and validate
		add_action('wp_print_scripts', 'clpr_load_form_scripts');

		add_action( 'appthemes_notices', array( $this, 'show_notice' ) );
	}

	function show_notice() {
		if ( ! empty( $this->error ) ) {
			foreach( $this->error as $error )
				appthemes_display_notice( 'error', $error );
		} elseif ( isset( $_POST['action'] ) && $_POST['action'] == 'clpr-edit-item' ) {
			appthemes_display_notice( 'success', __( 'Your coupon has been successfully updated.', APP_TD ) . ' <a href="' . CLPR_DASHBOARD_URL . '">' . __( 'Return to my dashboard', APP_TD ) . '</a>' );
		}
	}
}


class CLPR_User_Dashboard extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-dashboard.php', __( 'Dashboard', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {
		global $wpdb, $current_user;
		appthemes_auth_redirect_login(); // if not logged in, redirect to login page
		nocache_headers();

		// check to see if we want to pause or restart the coupon
		if ( isset($_GET['action']) && !empty($_GET['action']) ) {
			$d = trim($_GET['action']);
			$aid = trim($_GET['aid']);

			// make sure author matches coupon. Prevents people from trying to hack other peoples coupons
			$sql = $wpdb->prepare("SELECT wposts.post_author FROM $wpdb->posts wposts WHERE ID = %d AND post_author = %d", $aid, $current_user->ID);
			$checkauthor = $wpdb->get_row($sql);

			if ( $checkauthor != null ) { // author check is ok. now update coupon status

				if ( $d == 'pause' ) {
						$my_ad = array();
						$my_ad['ID'] = $aid;
						$my_ad['post_status'] = 'draft';
						wp_update_post($my_ad);
						$redirect_url = add_query_arg( array( 'paused' => 'true' ), CLPR_DASHBOARD_URL );
						wp_redirect( $redirect_url );
						exit();

				} elseif ( $d == 'restart' ) {
						$my_ad = array();
						$my_ad['ID'] = $aid;
						$my_ad['post_status'] = 'publish';
						wp_update_post($my_ad);
						$redirect_url = add_query_arg( array( 'restarted' => 'true' ), CLPR_DASHBOARD_URL );
						wp_redirect( $redirect_url );
						exit();

				} elseif ( $d == 'delete' ) {
						clpr_delete_coupon($aid);
						$redirect_url = add_query_arg( array( 'deleted' => 'true' ), CLPR_DASHBOARD_URL );
						wp_redirect( $redirect_url );
						exit();

				}

			}

		}

		add_action( 'appthemes_notices', array( $this, 'show_notice' ) );
	}

	function show_notice() {
		if ( isset( $_GET['paused'] ) ) {
			appthemes_display_notice( 'success', __( 'Your coupon has been paused.', APP_TD ) );
		} elseif ( isset( $_GET['restarted'] ) ) {
			appthemes_display_notice( 'success', __( 'Your coupon has been restarted.', APP_TD ) );
		} elseif ( isset( $_GET['deleted'] ) ) {
			appthemes_display_notice( 'success', __( 'Your coupon has been deleted.', APP_TD ) );
		} elseif ( isset( $_GET['renew-invalid-id'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not relist this coupon. Invalid ID of coupon.', APP_TD ) );
		} elseif ( isset( $_GET['renew-not-expired'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not relist this coupon. Coupon is not expired.', APP_TD ) );
		}
	}
}


class CLPR_User_Profile extends APP_User_Profile {

	function __construct() {
		APP_View_Page::__construct( 'tpl-profile.php', __( 'Profile', APP_TD ) );
		add_action( 'init', array( $this, 'update' ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {
		parent::template_redirect();

		wp_enqueue_script('jquery');
	}
}


class CLPR_Coupon_Single extends APP_View {

	function condition() {
		return is_singular( APP_POST_TYPE );
	}

	function notices() {
		$status = get_post_status( get_queried_object() );

		if ( $status == 'pending' )
			appthemes_display_notice( 'success', __( 'This coupon listing is currently pending and must be approved by an administrator.', APP_TD ) );
	}
}

