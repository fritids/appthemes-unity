<?php
/*
	# REPORT POST CLASS
	This class handles all Requests to Database
	Table Names
		wpreport
		wpreport_comments
		wpreport_archive
*/

class Report {
	var $last_error;
	var $totalRows;
	var $insert_id;

	# Construct
	function __construct() {
		$this->last_error = '';
		$this->totalRows = 0;
		$this->insert_id=0;
	}

	# Function to Add New Report to Database
	function add($postID, $type, $comment, $stamp = '', $ip='')	{
		global $wpdb, $app_abbr;

		// EMPTY
		if ( empty($stamp) )
			$stamp = time();

		// CHECK For Existing Report
		$sql = "SELECT `id` FROM $wpdb->clpr_report WHERE `postID` = %s AND `status` != 2 LIMIT 1";
		$sql = $wpdb->prepare($sql, $postID);
		$reportID = $wpdb->get_var($sql);

		$this->insert_id = $reportID;

		// Add New to Get Report ID
		if ( !$reportID || !is_numeric($reportID) || $reportID <= 0 ) {
			// Get the POST
			$post = get_post($postID); // This uses Wordpress Functionality
			if ( !$post || $post->ID <= 0 ) {
				$this->last_error = __( 'Unable to retrieve post details!', APP_TD );
				return false;
			}

			// Add New Report
			$data = array(
				'postID' => $postID,
				'post_title' => $post->post_title,
				'stamp' => $stamp,
				'status' => '1',
			);
			$wpdb->insert( $wpdb->clpr_report, $data );

			// Check for Error and Insert Success
			if($wpdb->rows_affected <= 0) {
				$this->last_error = sprintf( __( 'Error, Unable to add new report: %s', APP_TD ), $wpdb->last_error );
				return false;
			}

			// Assign new reportID
			$reportID = $wpdb->insert_id;

			$this->insert_id = $reportID;

			$sendMail = '';
			if ( intval(get_option($app_abbr.'_rp_send_email')) > 0 )
				$sendMail = get_option($app_abbr.'_rp_email_address');

			// Send Email
			if ( !empty($sendMail) )
				$this->sendMail($sendMail, $post, $type, $comment);
		}
		// CHK IP provided
		if ( empty($ip) )
			$ip = $this->get_ipaddress();


		// Add Comment to the Report Comments Table
		$data = array(
			'reportID' => $reportID,
			'type' => $type,
			'comment' => $comment,
			'ip' => $ip,
			'stamp' => $stamp,
		);
		$wpdb->insert( $wpdb->clpr_report_comments, $data );

		// Check for Error and Insert Success
		if ( $wpdb->rows_affected <= 0 ) {
			$this->last_error = sprintf( __( 'Error, Unable to add new report: %s', APP_TD ), $wpdb->last_error );
			return false;
		}

		return true; // A Sucess
	}

	function delete($reportID) {
		global $wpdb;
		// Delete as Requested From reports & archives

		# Delete From archive
		$sql = "DELETE FROM $wpdb->clpr_report_archive WHERE `reportID` = %s;";
		$sql = $wpdb->prepare($sql, $reportID);
		$wpdb->query($sql);

		// Check for Error and Update
		if ( $wpdb->rows_affected <= 0 && !empty($wpdb->last_error) ) {
			$this->last_error = sprintf( __( 'Error, Unable to delete report: %s', APP_TD ), $wpdb->last_error );
			return false;
		}

		# Delete From Comments
		$sql = "DELETE FROM $wpdb->clpr_report_comments WHERE `reportID` = %s;";
		$sql = $wpdb->prepare($sql, $reportID);
		$wpdb->query($sql);

		// Check for Error and Update
		if ( $wpdb->rows_affected <= 0 && !empty($wpdb->last_error) ) {
			$this->last_error = sprintf( __( 'Error, Unable to delete report: %s', APP_TD ), $wpdb->last_error );
			return false;
		}

		# DELETE FROM REPORT
		$sql = "DELETE FROM $wpdb->clpr_report WHERE `id` = %s;";
		$sql = $wpdb->prepare($sql, $reportID);
		$wpdb->query($sql);

		// Check for Error and Update
		if ( $wpdb->rows_affected <= 0 && !empty($wpdb->last_error) ) {
			$this->last_error = sprintf( __( 'Error, Unable to delete report: %s', APP_TD ), $wpdb->last_error );
			return false;
		}


		return true; // Finally 
	}

	# Get List of Reports
	function findReports($order = '', $limit = 20, $where = '', $offset = 0 ) {
		global $wpdb;

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->clpr_report $where $order LIMIT %d, %d;";
		$sql = $wpdb->prepare($sql, $offset, $limit);
		$results = $wpdb->get_results($sql, OBJECT);

		$sql = "SELECT FOUND_ROWS();";
		$this->totalRows = $wpdb->get_var($sql);

		if ( $this->totalRows <= 0 )
			return NULL;

		return $results;
	}

	# Get Comments
	function getComments($reportID) {
		global $wpdb;

		$sql = "SELECT * FROM $wpdb->clpr_report_comments WHERE `reportID` = %s";
		$sql = $wpdb->prepare($sql, $reportID);

		return $wpdb->get_results($sql, OBJECT);
	}

	# Get IP of USER
	function get_ipaddress() {
		if ( empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if ( strpos($ip_address, ',') !== false ) {
			$ip_address = explode(',', $ip_address);
			$ip_address = $ip_address[0];
		}
		return $ip_address;
	}

	# Send Mail
	function sendMail($to, $post, $type, $comment) {
		global $app_theme;

		$mailto = get_option('admin_email');
		// $mailto = 'tester@127.0.0.1'; // USED FOR TESTING
		$subject = __( 'Coupon Reported', APP_TD );
		$headers = 'From: '. sprintf( __( '%s Admin', APP_TD ), $app_theme ) .' <'. get_option('admin_email') .'>' . "\r\n";


		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message  = __( 'Dear Admin,', APP_TD ) . "\r\n\r\n";
		$message .= sprintf( __( 'The following coupon has just been reported as %s.', APP_TD ), $type ) . "\r\n\r\n";
		$message .= __( 'Details', APP_TD ) . "\r\n";
		$message .= '-----------------' . "\r\n";
		$message .= sprintf( __( 'Title: %s', APP_TD ), $post->post_title ) . "\r\n";
		$message .= sprintf( __( 'Edit: %s', APP_TD ), get_edit_post_link( $post->ID, '' ) ) . "\r\n\r\n";
		$message .= __( 'You will not receive further notification for this coupon until it has been archived or deleted. However all future reports will be logged and can be viewed on each coupon admin page.', APP_TD ) . "\r\n\r\n\r\n";

		$message .= __( 'Regards,', APP_TD ) . "\r\n\r\n";
		$message .= $app_theme . "\r\n\r\n";

		// ok let's send the email
		wp_mail($mailto, $subject, $message, $headers);
	}
} // Class

?>