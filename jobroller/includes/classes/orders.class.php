<?php
// TODO: Replace with payment framework

class jr_orders {

	var $orders;
	var $count;
	var $completed_count;
	var $pending_count;
	var $cancelled_count;
	
	function jr_orders() {
		global $wpdb;
		
		$this->orders = array();
		$this->pending_count = 0;
		$this->completed_count = 0;
		$this->cancelled_count = 0;
		$this->count = 0;
		
		$results = $wpdb->get_results("SELECT * FROM $wpdb->jr_orders;");
		if ($results) :
			foreach ($results as $result) :
				$order = &new jr_order();
				$order->set_order( $result );
				$this->orders[] = $order;
				
				switch ($order->status) :
					case "completed" :
						$this->completed_count++;
					break;
					case "pending_payment" :
						$this->pending_count++;
					break;
					case "cancelled" :
						$this->cancelled_count++;
					break;
				endswitch;
			endforeach;
		endif;
		$this->count = sizeof($this->orders);
	}

	function get_orders( $args ) {
		global $wpdb;

		$defaults = array (
			'user_id' => '',
			'type'    => 'all', //possible values all|packs
			'status'  => '',
			'offset'  => 0,
			'limit'	  => 20,
			'orderby' => 'id',
			'order'   => 'ASC',
		);
		$args = wp_parse_args($args, $defaults);

		$this->orders = array();

		$order_cols = array(
			'id',
			'user_id',
			'job_id',
			'pack_id',
			'featured',
			'cost',
			'order_date',
			'payment_date',
			'payment_type',
			'transaction_id',
			'approval_method'
		);

		// sanitize order columns
		if ( ! $args['orderby'] || ( $args['orderby'] && ! in_array($args['orderby'], $order_cols) ) ) {
			$args['orderby'] = 'id';
		}

		$sort_vals = array(
			'ASC',
			'DESC'
		);

		// sanitize sort column
		if ( ! $args['order'] || ( $args['order'] && ! in_array($args['order'], $sort_vals) ) ) {
			$args['order'] = 'DESC';
		}

		$and = '';
		if ( !empty($args['user_id']) ) $and .= " AND user_id = " . (int)$args['user_id'];
		if ( 'new_pack' == $args['type'] ) $and .= " AND pack_id > 0";

		if (!empty($args['status'])) :
			$query = $wpdb->prepare("SELECT * FROM $wpdb->jr_orders WHERE status = %s ".$and." ORDER BY ".$args['orderby']." ".$args['order']." LIMIT ".$args['offset'].", ". $args['limit'].";", $args['status']);
			$results = $wpdb->get_results($query);
		else :
			$results = $wpdb->get_results( "SELECT * FROM $wpdb->jr_orders WHERE 1 = 1 ".$and." ORDER BY ".$args['orderby']." ".$args['order']." LIMIT ".$args['offset']." ,". $args['limit'] );
		endif;

		if ($results) :
			foreach ($results as $result) :
				$order = &new jr_order();
				$order->set_order( $result );

				// for 'new pack' orders skip user packs
				if ( 'new_pack' == $args['type'] && 'user_pack' == $order->pack_type ) {
					unset( $order );
					continue;
				}
				$this->orders[] = $order;
			endforeach;
		endif;
	}
}

class jr_order {

	var $id;	 	 	 	 	 	 	
	var $user_id;		 	 	 	 	 	 	
	var $status;	 	 	 	 	 	 	 
	var $cost;			 	 	 	 	 	 	 
	var $job_id;	 	 	 	 	 	 	
	var $pack_id;
	var $pack_type;
	var $pack_type_desc;
	var $featured;	 	 	
	var $order_date;	 	 	 	 	 	 	
	var $payment_date;	 	 	 	
	var $payer_first_name;	 	 	 	 	 	 	 
	var $payer_last_name;	 	 	 	 	 	 	 
	var $payer_email;	 	 	 	 	 	 	 
	var $payment_type;		 	 	 	 	 	 	 
	var $approval_method;		 	 	 	 	 	 	 
	var $payer_address;		 	 	 	 	 	 	 
	var $transaction_id;
	var $order_key;
	
	function jr_order( $id='', $user_id='', $cost='', $job_id='', $pack_id='', $featured = 0, $status='pending_payment' ) {
		if ($id>0) :
			$this->id = $id;
			$this->get_order();
		elseif ($user_id) :
			$this->user_id = $user_id; 	 	 	 	 	 	
			$this->status = $status;	 	 	 	 
			$this->cost = $cost; 	 	 	 
			$this->job_id = $job_id; 	
			$this->set_pack_data( $pack_id );
			$this->featured = $featured;
		endif;
	}

	function find_order_for_job( $job_id ) {
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM $wpdb->jr_orders WHERE job_id = %s ORDER BY id DESC LIMIT 1", $job_id);
		$result = $wpdb->get_row($query);

		if ($result) :
			$this->set_order( $result );
			return true;
		endif;
		return false;
	}
	
	function get_order() {
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM $wpdb->jr_orders WHERE id = %s", $this->id);
		$result = $wpdb->get_row($query);
		
		if ($result) :
			$this->set_order( $result );
			return true;
		endif;
		return false;
	}
	
	function set_order( $order ) {
		$this->id = $order->id;
		$this->user_id = $order->user_id;
		$this->status = $order->status;
		$this->cost = $order->cost;
		$this->job_id = $order->job_id;
		$this->featured = $order->featured;
		$this->order_date = $order->order_date;
		$this->payment_date = $order->payment_date;
		$this->payer_first_name = $order->payer_first_name;
		$this->payer_last_name = $order->payer_last_name;
		$this->payer_email = $order->payer_email;
		$this->payment_type = $order->payment_type;
		$this->approval_method = $order->approval_method;
		$this->payer_address = $order->payer_address;
		$this->transaction_id = $order->transaction_id;
		$this->order_key = $order->order_key;
		$this->set_pack_data( $order->pack_id );
	}

	function set_pack_data( $pack_id ) {
		global $wpdb;

		if ( ! $pack_id  ) return;
		$this->pack_id = $pack_id;
		$user_id = $this->user_id;

		$result = $wpdb->get_var( $wpdb->prepare( "SELECT pack_id FROM $wpdb->jr_customer_packs WHERE id = %d AND user_id = %d", $pack_id, $user_id ) );
		if ( $result ) {
			$this->pack_type = 'user_pack';
			$this->pack_type_desc = __('User Pack ::', APP_TD);
		} else {
			$this->pack_type = 'new_pack';
			$this->pack_type_desc = __('New Pack ::', APP_TD);
		}
	}

	function generate_paypal_link( $item_name = '' ) {
		$paypal_email = get_option('jr_jobs_paypal_email');
		$currency = get_option('jr_jobs_paypal_currency');
		$notify_url = '';
		$return = '';
		$paypal_adr = '';
		$item_number = '';
		
		if (empty($item_name)) $item_name = urlencode('Order#' . $this->id);
		
		$item_name = strip_tags($item_name);
		
		if (get_option('jr_use_paypal_sandbox')=='yes') :
			$paypal_adr = 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&';
		else :
			$paypal_adr = 'https://www.paypal.com/webscr?';
		endif; 

		if(get_option('jr_enable_paypal_ipn') == 'yes') :
			$notify_url = '&notify_url=' . urlencode(trailingslashit(get_bloginfo('wpurl')).'?paypalListener=IPN'); // FOR IPN - notify_url
			$return = urlencode( get_permalink(get_option('jr_dashboard_page_id')) ); // Thank you page - return
		else :
			$return = urlencode( get_permalink(get_option('jr_add_new_confirm_page_id')) ); // Add new confirm page - return
		endif;
		
		return $paypal_adr.'cmd=_xclick&business='.$paypal_email.'&item_name='.$item_name.'&amount='.$this->cost.'&no_shipping=1&no_note=1&item_number='.$this->order_key.'&currency_code='.$currency.'&charset=UTF-8&return='.$return.''.$notify_url.'&rm=2&custom='.$this->id.'';
	}
	
	function complete_order( $approval_method = '' ) {
	global $jr_log;
		
		$jobs_last = '';
		if ($this->status == 'completed')  return;

		if ($this->pack_id > 0) :

			// Get pack details
			$new_pack = false;
			if ( 'user_pack' == $this->pack_type ) {
				$pack = new jr_user_pack( $this->pack_id );
				if ( ! $pack->get_valid_pack( $this->user_id, $pack_status = 'any' ) ) {
					$jr_log->write_log( sprintf( __("Warning: User Pack #%1s not found for user #%2s. A New Pack will be given.", APP_TD), $this->pack_id, $this->user_id ) );
					$new_pack = true;
				}
			}

			if ( $new_pack || 'user_pack' != $this->pack_type ) {
				$pack = new jr_pack( $this->pack_id );
				// Add pack to user's account and subtract jobs count if a job was submitted
				$pack->give_to_user( $this->user_id, $jobs_count = ($this->job_id == 0 ? 0 : 1), ($this->featured ? $this->featured : 0) );
			}

			$jobs_last = $pack->job_duration;
		else:
			$jobs_last = get_option('jr_jobs_default_expires'); // 30 day default

			if ( ! $jobs_last ) $jobs_last = 30; // 30 day default
		endif;

		// Caculate expirey date from today
		$date = strtotime('+'.$jobs_last.' day', current_time('timestamp'));

		if ($this->job_id > 0) :

			// Update post
			$job_post = get_post($this->job_id); 
			if ($job_post->post_status !== 'private') :

	    		$job_post = array();
	        	$job_post['ID'] = $this->job_id;
	        	$job_post['post_status'] = 'publish';
	        	$job_post['post_date'] = date('Y-m-d H:i:s');
	        	$job_post['post_date_gmt'] = get_gmt_from_date(date('Y-m-d H:i:s'));

				wp_update_post( $job_post );

				// Update expiry date
				update_post_meta( $this->job_id, '_expires', $date);
				
			endif;
		endif;
		
		// Change order status to completed
		$this->status = 'completed';
		$this->payment_date = date('Y-m-d H:i:s');
		$this->approval_method = $approval_method;
		
		// Send out mail
		jr_order_complete( $this );

		$this->update_order();
		return true;
	}
	
	function cancel_order() {
		
		if ($this->job_id > 0) :
			$job_post = array();
        	$job_post['ID'] = $this->job_id;
        	$job_post['post_status'] = 'trash';
			wp_update_post( $job_post );
		endif;

		$this->status = 'cancelled';
		
		// Send out mail
		jr_order_cancelled( $this );
		
		$this->update_order();
		return true;
	}
	
	function add_payment( $payment_data ) {

		$this->payment_date 	= $payment_data['payment_date'];
		$this->payer_first_name = $payment_data['payer_first_name'];
		$this->payer_last_name 	= $payment_data['payer_last_name'];
		$this->payer_email 		= $payment_data['payer_email'];
		$this->payment_type 	= $payment_data['payment_type'];
		$this->approval_method 	= $payment_data['approval_method'];
		$this->payer_address 	= $payment_data['payer_address'];
		$this->transaction_id 	= $payment_data['transaction_id'];

		$this->update_order();
	}
	
	function insert_order() {
		global $wpdb;
		
		$this->order_key = uniqid('order_'.$this->id.'_');
		
		$wpdb->insert( $wpdb->jr_orders, array( 
			'user_id' 		=> $this->user_id,
			'status' 		=> $this->status,
			'cost' 			=> $this->cost,
			'job_id' 		=> $this->job_id,
			'pack_id' 		=> $this->pack_id,
			'featured'		=> $this->featured,
			'order_key'		=> $this->order_key
		), array( '%s','%s','%s','%s','%s','%s','%s' ) );
		
		jr_new_order( $this );
		
		$this->id = $wpdb->insert_id;

		// notify job pack buyers
		if (get_option('jr_new_order_email') == 'yes') jr_owner_new_order($this);

	}
	
	function update_order() {
		global $wpdb; 
		$wpdb->update( $wpdb->jr_orders, array( 
			'status' 			=> $this->status, 
			'payment_date' 		=> $this->payment_date,
			'payer_first_name' 	=> $this->payer_first_name,
			'payer_last_name' 	=> $this->payer_last_name,
			'payer_email' 		=> $this->payer_email,
			'payment_type' 		=> $this->payment_type,
			'approval_method'	=> $this->approval_method,
			'payer_address' 	=> $this->payer_address,
			'transaction_id' 	=> $this->transaction_id
		), array( 'id' => $this->id ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d' ) );
	}
}	
?>