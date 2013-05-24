<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Make bbPress forums, topics and replies searchable
 *
 * @since 0.1
 */
function royal_add_bbp_to_search( $post_type ) {
     $post_type['exclude_from_search'] = false;

     return $post_type;
}
if( of_get_option( 'forum_search_topic', '1' ) ) add_filter( 'bbp_register_topic_post_type', 'royal_add_bbp_to_search' );
if( of_get_option( 'forum_search_reply', '0' ) ) add_filter( 'bbp_register_reply_post_type', 'royal_add_bbp_to_search' );
if( of_get_option( 'forum_search_forum', '1' ) ) add_filter( 'bbp_register_forum_post_type', 'royal_add_bbp_to_search' );


/* ---------- START REGISTER REDIRECT FIX ------------ */
/**
 * Redirect user to login page after successful registration
 *
 * @since 1.1
 *
 * @uses royal_get_page_by_template() To get login page object
 * @uses get_permalink() To get URL to login page
 */
function royal_register_redirect( $redirect_to ){
	$return_url = '';

	$login_page = function_exists( 'royal_get_page_by_template' ) ? royal_get_page_by_template( 'user-login' ) : '';
	
	if( !empty( $login_page ) ) {
		$return_url = get_permalink( $login_page->ID );
	}
	
	return $return_url;
}
add_filter( 'royal_bbp_user_register_redirect_to', 'royal_register_redirect' );

/**
 * Output the required hidden fields when registering
 *
 * @since 1.1
 *
 * @uses add_query_arg() To add query args
 * @uses bbp_login_url() To get the login url
 * @uses apply_filters() To allow custom redirection
 * @uses royal_bbp_redirect_to_field() To output the redirect to field
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function royal_bbp_user_register_fields() {
?>

		<input type="hidden" name="action"      value="register" />
		<input type="hidden" name="user-cookie" value="1" />

		<?php

		// Allow custom registration redirection
		$redirect_to = apply_filters( 'royal_bbp_user_register_redirect_to', '' );
		royal_bbp_redirect_to_field( add_query_arg( array( 'checkemail' => 'registered' ), $redirect_to ) );

		// Prevent intention hi-jacking of sign-up form
		wp_nonce_field( 'bbp-user-register' );
}

/**
 * Output hidden request URI field for user forms.
 *
 * The referer link is the current Request URI from the server super global. The
 * input name is '_wp_http_referer', in case you wanted to check manually.
 *
 * @since 1.1
 *
 * @param string $url Pass a URL to redirect to
 * @uses wp_get_referer() To get the referer
 * @uses esc_attr() To escape the url
 * @uses apply_filters() Calls 'bbp_redirect_to_field' with the referer field
 *                        and url
 */
function royal_bbp_redirect_to_field( $redirect_to = '' ) {

	// Rejig the $redirect_to
	if ( !isset( $_SERVER['REDIRECT_URL'] ) && ( !$redirect_to == home_url( $_SERVER['REDIRECT_URL'] ) ) )
		$redirect_to = wp_get_referer();

	// Make sure we are directing somewhere
	if ( empty( $redirect_to ) )
		$redirect_to = home_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );

	// Remove loggedout query arg if it's there
	$redirect_to    = (string) esc_attr( remove_query_arg( 'loggedout', $redirect_to ) );
	$redirect_field = '<input type="hidden" id="bbp_redirect_to" name="redirect_to" value="' . $redirect_to . '" />';

	echo apply_filters( 'royal_bbp_redirect_to_field', $redirect_field, $redirect_to );
}
/* ---------- END REGISTER REDIRECT FIX ------------ */


/**
 * Enable lead topic functionality in bbPress
 *
 * @since 0.1
 */
//add_filter( 'bbp_show_lead_topic', '__return_true' );

/**
 * Echoes HTML for 'sticky' icon.
 * If privilaged user is viewing the page
 * the function will display link to toggle
 * sticking the thread to top.
 *
 * @since 0.1
 *
 * @param array $args Function arguments
 */
function royal_bbp_sticky_icon( $args = array() ) {
	$defaults = array (
		'id'          	=> 0,
		'unstick_text'	=> __( 'Unstick this thread', 'cheerapp' ),
		'stick_text'   	=> __( 'Stick this thread',  'cheerapp' ),
		'is_sticky_text'=> __( 'Thread is sticky', 'cheerapp' ),
		'info_only'		=> false,
		'icon_only'		=> false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$link = '';

	$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

	if ( empty( $topic ) ) {
		return;
	}
	elseif ( ( !current_user_can( 'moderate', $topic->ID ) || $info_only == true ) && bbp_is_topic_sticky( $topic->ID ) ) {
		if( $icon_only ) {
			$link .= '<span class="sticky-icon forum-icon" title="' . $is_sticky_text . '"></span>';
		}
		else {
			$link .= '<span class="topic-info-item"><span class="sticky-icon forum-icon"></span>' . $is_sticky_text . '</span>';
		}
	}
	elseif ( current_user_can( 'moderate', $topic->ID ) && $info_only != true ) {
		$display = bbp_is_topic_sticky( $topic->ID ) ? $unstick_text : $stick_text;
		$class = bbp_is_topic_sticky( $topic->ID ) ? '' : ' inactive';

		$uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_stick', 'topic_id' => $topic->ID ) );
		$uri = esc_url( wp_nonce_url( $uri, 'stick-topic_' . $topic->ID ) );
		
		if( $icon_only ) {
			$link .= '<a href="' . $uri . '" class="sticky-icon forum-icon' . $class . '" title="' . $display . '"></a>';
		}
		else {
			$link .= '<a href="' . $uri . '" class="topic-info-item' . $class . '"><span class="sticky-icon forum-icon"></span>' . $display . '</a>';
		}
	}

	echo $link;
}

/**
 * Echoes HTML for 'closed' icon/link.
 * If privilaged user is viewing the page
 * the function will display link to toggle
 * closing/opening the thread.
 *
 * @since 0.1
 *
 * @param array $args Function arguments
 */
function royal_bbp_closed_icon( $args = array() ) {
	$defaults = array (
		'id'          	=> 0,
		'close_text'  	=> __( 'Close this thread', 'cheerapp' ),
		'open_text'   	=> __( 'Open this thread',  'cheerapp' ),
		'is_closed_text'=> __( 'Thread is closed for discussion', 'cheerapp' ),
		'info_only'		=> false,
		'icon_only'		=> false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$link = '';

	$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

	if ( empty( $topic ) ) {
		return;
	}
	elseif ( ( !current_user_can( 'moderate', $topic->ID ) || $info_only == true ) && !bbp_is_topic_open( $topic->ID ) ) {
		if( $icon_only ) {
			$link .= '<span class="closed-icon forum-icon" title="' . $is_closed_text . '"></span>';
		}
		else {
			$link .= '<span class="topic-info-item"><span class="closed-icon forum-icon"></span>' . $is_closed_text . '</span>';
		}
	}
	elseif ( current_user_can( 'moderate', $topic->ID ) && $info_only != true ) {
		$display = bbp_is_topic_open( $topic->ID ) ? $close_text : $open_text;
		$class = bbp_is_topic_open( $topic->ID ) ? ' inactive' : '';

		$uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_close', 'topic_id' => $topic->ID ) );
		$uri = esc_url( wp_nonce_url( $uri, 'close-topic_' . $topic->ID ) );
		
		if( $icon_only ) {
			$link .= '<a href="' . $uri . '" class="closed-icon forum-icon' . $class . '" title="' . $display . '"></a>';
		}
		else {
			$link .= '<a href="' . $uri . '" class="topic-info-item' . $class . '"><span class="closed-icon forum-icon"></span>' . $display . '</a>';
		}
	}

	echo $link;
}

/**
 * Return the link to subscribe/unsubscribe from a topic
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these arguments:
 *  - subscribe: Subscribe text
 *  - unsubscribe: Unsubscribe text
 *  - user_id: User id
 *  - topic_id: Topic id
 * @param int $user_id Optional. User id
 */
function royal_bbp_subscribe_icon( $args = array(), $user_id = 0 ) {
	if ( !bbp_is_subscriptions_active() )
		return;

	$defaults = array (
		'subscribe'   => __( 'Subscribe to replies via e-mail',   'cheerapp' ),
		'unsubscribe' => __( 'You&#8217;re subscribed to replies', 'cheerapp' ),
		'user_id'     => 0,
		'topic_id'    => 0,
		'icon_only'		=> false
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	// Try to get a user_id
	if ( !$user_id = bbp_get_user_id( $user_id, true, true ) )
		return false;

	// No link if you can't edit yourself
	if ( !current_user_can( 'edit_user', (int) $user_id ) )
		return false;

	// No link if not viewing a topic
	if ( !$topic_id = bbp_get_topic_id( $topic_id ) )
		return false;

	// Decine which link to show
	if ( $is_subscribed = bbp_is_user_subscribed( $user_id, $topic_id ) ) {
		$text = $unsubscribe;
		$query_args  = array( 'action' => 'bbp_unsubscribe', 'topic_id' => $topic_id );
	} else {
		$text = $subscribe;
		$query_args = array( 'action' => 'bbp_subscribe', 'topic_id' => $topic_id );
	}

	// Create the link based where the user is and if the user is subscribed already
	if ( bbp_is_subscriptions() )
		$permalink = bbp_get_subscriptions_permalink( $user_id );
	elseif ( is_singular( bbp_get_topic_post_type() ) )
		$permalink = bbp_get_topic_permalink( $topic_id );
	elseif ( bbp_is_query_name( 'bbp_single_topic' ) )
		$permalink = get_permalink();

	$ajax_url		= esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'topic_subscription_toggle', 'topic_id' => $topic_id ), admin_url( 'admin-ajax.php' ) ), 'toggle-subscription_' . $topic_id ) );
	$url			= esc_url( wp_nonce_url( add_query_arg( $query_args, $permalink ), 'toggle-subscription_' . $topic_id ) );
	$is_subscribed	= $is_subscribed ? '' : ' inactive';
	
	if( $icon_only ) {
		$html			= '<a href="' . $url . '" class="subscribe-icon forum-icon ' . $is_subscribed . '" title="' . $text . '" rel="' . $ajax_url . '"></a>';
	}
	else {
		$html			= '<a href="' . $url . '" rel="' . $ajax_url . '" class="topic-info-item subscribe-item ' . $is_subscribed . '"><span class="subscribe-icon forum-icon"></span><span class="text">' . $text . '</span></a>';
	}

	// Return the link
	echo apply_filters( 'royal_bbp_subscribe_icon', $html, $args, $user_id, $topic_id );
}

/**
 * Return the link that toggles adding a thread to user favorites
 * @since 0.1
 *
 * @param mixed $args Function arguments
 * @param int $user_id Optional. User id
 */
function royal_bbp_favorite_icon( $args = array(), $user_id = 0 ) {
	$defaults = array (
		'fav_text'		=> __( 'Add this thread to favorites',   'cheerapp' ),
		'unfav_text'	=> __( 'Your favorite', 'cheerapp' ),
		'user_id'		=> 0,
		'icon_only'		=> false
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if ( !bbp_is_favorites_active() )
		return false;

	if ( !$user_id = bbp_get_user_id( $user_id, true, true ) )
		return false;

	if ( !current_user_can( 'edit_user', (int) $user_id ) )
		return false;

	if ( !$topic_id = bbp_get_topic_id() )
		return false;

	if ( $is_fav = bbp_is_user_favorite( $user_id, $topic_id ) ) {
		$url  = esc_url( bbp_get_favorites_permalink( $user_id ) );
		$favs = array( 'action' => 'bbp_favorite_remove', 'topic_id' => $topic_id );
		$text = $unfav_text;
	} else {
		$url  = esc_url( bbp_get_topic_permalink( $topic_id ) );
		$favs = array( 'action' => 'bbp_favorite_add', 'topic_id' => $topic_id );
		$text = $fav_text;
	}

	// Create the link based where the user is and if the topic is already the user's favorite
	if ( bbp_is_favorites() )
		$permalink = bbp_get_favorites_permalink( $user_id );
	elseif ( is_singular( bbp_get_topic_post_type() ) )
		$permalink = bbp_get_topic_permalink( $topic_id );
	elseif ( bbp_is_query_name( 'bbp_single_topic' ) )
		$permalink = get_permalink();
	
	$ajax_url	= esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'topic_favorite_toggle', 'topic_id' => $topic_id ), admin_url( 'admin-ajax.php' ) ), 'toggle-favorite_' . $topic_id ) );
	$url		= esc_url( wp_nonce_url( add_query_arg( $favs, $permalink ), 'toggle-favorite_' . $topic_id ) );
	$is_fav		= $is_fav ? '' : ' inactive';
	
	if( $icon_only ) {
		$html			= '<a href="' . $url . '" class="fav-icon forum-icon ' . $is_fav . '" title="' . $text . '" rel="' . $ajax_url . '"></a>';
	}
	else {
		$html			= '<a href="' . $url . '" rel="' . $ajax_url . '" class="topic-info-item fav-item ' . $is_fav . '"><span class="fav-icon forum-icon"></span><span class="text">' . $text . '</span></a>';
	}

	echo apply_filters( 'royal_bbp_favorite_icon', $html, $user_id, $topic_id );
}

/**
 * Return the 'edit' link of the topic
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these args:
 *  - id: Optional. Topic id
 *  - edit_text: Edit text
 *  - class: An array of classes
 *
 * @return string Topic edit link
 */
function royal_bbp_get_topic_edit_link( $args = array() ) {
	$defaults = array (
		'id'			=>	0,
		'edit_text'		=>	__( 'Edit', 'cheerapp' ),
		'class'			=>	array( 'forum-admin-icon', 'edit-icon' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

	// Bypass check if user has caps
	if ( !current_user_can( 'edit_others_topics' ) ) {

		// User cannot edit or it is past the lock time
		if ( empty( $topic ) || !current_user_can( 'edit_topic', $topic->ID ) || bbp_past_edit_lock( $topic->post_date_gmt ) )
			return;
	}

	// No uri to edit topic
	if ( !$uri = bbp_get_topic_edit_url( $id ) )
		return;
		
	$class = implode( ' ', $class );

	return apply_filters( 'royal_bbp_get_topic_edit_link', '<a title="' . $edit_text . '" href="' . $uri . '" class="' . $class . '">' . $edit_text . '</a>', $args );
}

/**
 * Return the 'merge' link of the topic
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these args:
 *  - id: Optional. Topic id
 *  - merge_text: Merge text
 *  - class: An array of classes
 *
 * @return string Topic merge link
 */
function royal_bbp_get_topic_merge_link( $args = array() ) {
	$defaults = array (
		'id'           => 0,
		'merge_text'    => __( 'Merge', 'cheerapp' ),
		'class'			=>	array( 'forum-admin-icon', 'merge-icon' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

	if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
		return;

	$uri = esc_url( add_query_arg( array( 'action' => 'merge' ), bbp_get_topic_edit_url( $topic->ID ) ) );
	
	$class = implode( ' ', $class );

	return apply_filters( 'royal_bbp_get_topic_merge_link', '<a title="' . $merge_text . '" href="' . $uri . '" class="' . $class . '">' . $merge_text . '</a>', $args );
}

/**
 * Return the 'trash' link of the topic
 *
 * @since bbPress 0.1
 *
 * @param mixed $args This function supports these args:
 *  - id: Optional. Topic id
 *  - trash_text: Trash text
 *  - restore_text: Restore text
 *  - delete_text: Delete text
 *  - trash_class: Array of classes for 'trash' link
 *  - restore_class: Array of classes for 'restore' link
 *  - delete_class: Array of classes for 'delete' link
 *
 * @return string Topic trash link
 */
function royal_bbp_get_topic_trash_link( $args = array() ) {

	$defaults = array (
		'id'			=>	0,
		'trash_text'	=>	__( 'Trash',   'cheerapp' ),
		'restore_text'	=>	__( 'Restore', 'cheerapp' ),
		'delete_text'	=>	__( 'Delete',  'cheerapp' ),
		'trash_class'	=>	array( 'forum-admin-icon', 'trash-icon' ),
		'restore_class'	=>	array( 'forum-admin-icon', 'restore-icon' ),
		'delete_class'	=>	array( 'forum-admin-icon', 'delete-icon' )
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$actions = array();
	$topic   = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

	if ( empty( $topic ) || !current_user_can( 'delete_topic', $topic->ID ) ) {
		return;
	}

	if ( bbp_is_topic_trash( $topic->ID ) ) {
		$restore_class = implode( ' ', $restore_class );
		$actions['untrash'] = '<a title="' . esc_html( $restore_text ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'untrash', 'topic_id' => $topic->ID ) ), 'untrash-' . $topic->post_type . '_' . $topic->ID ) ) . '" class="' . $restore_class . '">' . esc_html( $restore_text ) . '</a>';
	} elseif ( EMPTY_TRASH_DAYS ) {
		$trash_class = implode( ' ', $trash_class );
		$actions['trash']   = '<a title="' . esc_html( $trash_text ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'trash', 'topic_id' => $topic->ID ) ), 'trash-' . $topic->post_type . '_' . $topic->ID ) ) . '" class="' . $trash_class . '">' . esc_html( $trash_text ) . '</a>';
	}

	if ( bbp_is_topic_trash( $topic->ID ) || !EMPTY_TRASH_DAYS ) {
		$delete_class = implode( ' ', $delete_class );
		$actions['delete']  = '<a title="' . esc_attr( __( 'Delete permanently', 'cheerapp' ) ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'delete', 'topic_id' => $topic->ID ) ), 'delete-' . $topic->post_type . '_' . $topic->ID ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete that permanently?', 'cheerapp' ) ) . '\' );" class="' . $delete_class . '">' . esc_html( $delete_text ) . '</a>';
	}

	// Process the admin links
	$actions = implode( '', $actions );

	return apply_filters( 'royal_bbp_get_topic_trash_link', $actions, $args );
}

/**
 * Return the spam link of the topic
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these args:
 *  - id: Optional. Topic id
 *  - spam_text: Spam text
 *  - unspam_text: Unspam text
 *  - spam_class: Array of classes for 'spam' link
 *  - unspam_class: Arrat of classes for 'unspam' link
 *
 * @return string Topic spam link
 */
function royal_bbp_get_topic_spam_link( $args = array() ) {
	$defaults = array (
		'id'			=>	0,
		'spam_text'		=>	__( 'Spam',   'cheerapp' ),
		'unspam_text'	=>	__( 'Unspam', 'cheerapp' ),
		'spam_class'	=>	array( 'forum-admin-icon', 'spam-icon' ),
		'unspam_class'	=>	array( 'forum-admin-icon', 'unspam-icon' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$spam_class		= implode( ' ', $spam_class );
	$unspam_class	= implode( ' ', $unspam_class );

	$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

	if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
		return;

	$display = bbp_is_topic_spam( $topic->ID ) ? $unspam_text : $spam_text;
	$class = bbp_is_topic_spam( $topic->ID ) ? $unspam_class : $spam_class;

	$uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_spam', 'topic_id' => $topic->ID ) );
	$uri = esc_url( wp_nonce_url( $uri, 'spam-topic_' . $topic->ID ) );

	return apply_filters( 'royal_bbp_get_topic_spam_link', '<a title="' . $display . '" href="' . $uri . '" class="' . $class . '">' . $display . '</a>', $args );
}

/**
 * Return the 'edit' link of the reply
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these arguments:
 *  - id: Reply id
 *  - edit_text: Edit text. Defaults to 'Edit'
 *  - class: An array of classes
 *
 * @return string Reply edit link
 */
function royal_bbp_get_reply_edit_link( $args = array() ) {
	$defaults = array (
		'id'			=>	0,
		'edit_text'		=>	__( 'Edit', 'cheerapp' ),
		'class'			=>	array( 'forum-admin-icon', 'edit-icon' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$reply = bbp_get_reply( bbp_get_reply_id( (int) $id ) );

	// Bypass check if user has caps
	if ( !current_user_can( 'edit_others_replies' ) ) {

		// User cannot edit or it is past the lock time
		if ( empty( $reply ) || !current_user_can( 'edit_reply', $reply->ID ) || bbp_past_edit_lock( $reply->post_date_gmt ) )
			return;
	}

	// No uri to edit reply
	if ( !$uri = bbp_get_reply_edit_url( $id ) )
		return;
		
	$class = implode( ' ', $class );

	return apply_filters( 'royal_bbp_get_reply_edit_link', '<a title="' . $edit_text . '" href="' . $uri . '" class="' . $class . '">' . $edit_text . '</a>', $args );
}

/**
 * Return the 'trash' link of the reply
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these arguments:
 *  - id: Reply id
 *  - trash_text: Trash text
 *  - restore_text: Restore text
 *  - delete_text: Delete text
 *  - trash_class: Array of classes for 'trash' link
 *  - restore_class: Array of classes for 'restore' link
 *  - delete_class: Array of classes for 'delete' link
 *
 * @return string Reply trash link
 */
function royal_bbp_get_reply_trash_link( $args = array() ) {

	$defaults = array (
		'id'			=>	0,
		'trash_text'	=>	__( 'Trash',   'cheerapp' ),
		'restore_text'	=>	__( 'Restore', 'cheerapp' ),
		'delete_text'	=>	__( 'Delete',  'cheerapp' ),
		'trash_class'	=>	array( 'forum-admin-icon', 'trash-icon' ),
		'restore_class'	=>	array( 'forum-admin-icon', 'restore-icon' ),
		'delete_class'	=>	array( 'forum-admin-icon', 'delete-icon' )
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$actions = array();
	$reply   = bbp_get_reply( bbp_get_reply_id( (int) $id ) );

	if ( empty( $reply ) || !current_user_can( 'delete_reply', $reply->ID ) ) {
		return;
	}

	if ( bbp_is_reply_trash( $reply->ID ) ) {
		$restore_class = implode( ' ', $restore_class );
		$actions['untrash'] = '<a title="' . esc_html( $restore_text ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_reply_trash', 'sub_action' => 'untrash', 'reply_id' => $reply->ID ) ), 'untrash-' . $reply->post_type . '_' . $reply->ID ) ) . '" class="' . $restore_class . '">' . esc_html( $restore_text ) . '</a>';
	} elseif ( EMPTY_TRASH_DAYS ) {
		$trash_class = implode( ' ', $trash_class );
		$actions['trash']   = '<a title="' . esc_html( $trash_text ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_reply_trash', 'sub_action' => 'trash', 'reply_id' => $reply->ID ) ), 'trash-' . $reply->post_type . '_' . $reply->ID ) ) . '" class="' . $trash_class . '">' . esc_html( $trash_text ) . '</a>';
	}

	if ( bbp_is_reply_trash( $reply->ID ) || !EMPTY_TRASH_DAYS ) {
		$delete_class = implode( ' ', $delete_class );
		$actions['delete']  = '<a title="' . esc_attr( __( 'Delete permanently', 'cheerapp' ) ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_reply_trash', 'sub_action' => 'delete', 'reply_id' => $reply->ID ) ), 'delete-' . $reply->post_type . '_' . $reply->ID ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete that permanently?', 'cheerapp' ) ) . '\' );" class="' . $delete_class . '">' . esc_html( $delete_text ) . '</a>';
	}

	// Process the admin links
	$actions = implode( '', $actions );

	return apply_filters( 'royal_bbp_get_reply_trash_link', $actions, $args );
}

/**
 * Return the 'spam' link of the reply
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these arguments:
 *  - id: Reply id
 *  - spam_text: Spam text
 *  - unspam_text: Unspam text
 *  - spam_class: Array of classes for 'spam' link
 *  - unspam_class: Arrat of classes for 'unspam' link
 *
 * @return string Reply spam link
 */
function royal_bbp_get_reply_spam_link( $args = array() ) {
	$defaults = array (
		'id'			=>	0,
		'spam_text'		=>	__( 'Spam',   'cheerapp' ),
		'unspam_text'	=>	__( 'Unspam', 'cheerapp' ),
		'spam_class'	=>	array( 'forum-admin-icon', 'spam-icon' ),
		'unspam_class'	=>	array( 'forum-admin-icon', 'unspam-icon' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$spam_class		= implode( ' ', $spam_class );
	$unspam_class	= implode( ' ', $unspam_class );

	$reply = bbp_get_reply( bbp_get_reply_id( (int) $id ) );

	if ( empty( $reply ) || !current_user_can( 'moderate', $reply->ID ) )
		return;

	$display  = bbp_is_reply_spam( $reply->ID ) ? $unspam_text : $spam_text;
	$class = bbp_is_reply_spam( $reply->ID ) ? $unspam_class : $spam_class;

	$uri = add_query_arg( array( 'action' => 'bbp_toggle_reply_spam', 'reply_id' => $reply->ID ) );
	$uri = esc_url( wp_nonce_url( $uri, 'spam-reply_' . $reply->ID ) );

	return apply_filters( 'royal_bbp_get_reply_spam_link', '<a title="' . $display . '" href="' . $uri . '" class="' . $class . '">' . $display . '</a>', $args );
}

/**
 * Get 'split' topic link
 *
 * Return the split link of the topic (but is bundled with each reply)
 *
 * @since 0.1
 *
 * @param mixed $args This function supports these arguments:
 *  - id: Reply id
 *  - split_text: Split text
 *  - class: array of classes
 *
 * @return string Reply split link
 */
function royal_bbp_get_topic_split_link( $args = array() ) {
	$defaults = array (
		'id'			=>	0,
		'split_text'	=>	__( 'Split', 'cheerapp' ),
		'class'			=>	array( 'forum-admin-icon', 'split-icon' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$class = implode( ' ', $class );

	$reply_id = bbp_get_reply_id( $id );
	$topic_id = bbp_get_reply_topic_id( $reply_id );

	if ( empty( $reply_id ) || !current_user_can( 'moderate', $topic_id ) )
		return;

	$uri = esc_url(
		add_query_arg(
			array(
				'action'   => 'split',
				'reply_id' => $reply_id
			),
		bbp_get_topic_edit_url( $topic_id )
	) );

	return apply_filters( 'royal_bbp_get_topic_split_link', '<a href="' . $uri . '" title="' . $split_text . '" class="' . $class . '">' . $split_text . '</a>', $args );
}

/* ---------------------------------------------- */
/* --------    TOPIC STATUS FUNCTIONS    -------- */
/* ---------------------------------------------- */

/**
 * Toggle a topic's status. Used to mark a topic as resolved
 * or not resolved. Mirrors other bbPress toggle functionality.
 *
 * @since 0.1
 */
function royal_bbp_toggle_topic_handler() {
	// Only proceed if GET is a topic toggle action
	if ( 'GET' == $_SERVER['REQUEST_METHOD'] && !empty( $_GET['action'] ) && in_array( $_GET['action'], array( 'bbp_toggle_topic_status' ) ) && !empty( $_GET['topic_id'] ) ) {

		$action    = $_GET['action'];            // What action is taking place?
		$topic_id  = (int) $_GET['topic_id'];    // What's the topic id?
		$success   = false;                      // Flag
		$post_data = array( 'ID' => $topic_id ); // Prelim array

		// Make sure topic exists
		if ( !$topic = bbp_get_topic( $topic_id ) )
			return;
		
		// Make sure topic is a support question
		if ( !royal_bbp_is_topic_support_question( $topic_id ) )
			return;

		// What is the user doing here?
		if ( !current_user_can( 'edit_topic', $topic->ID ) || ( 'bbp_toggle_topic_trash' == $action && !current_user_can( 'delete_topic', $topic->ID ) ) ) {
			bbpress()->errors->add( 'bbp_toggle_topic_permission', __( '<strong>ERROR:</strong> You do not have the permission to do that!', 'bbpress' ) );
			
			return;
		}

		check_ajax_referer( 'status-topic_' . $topic_id );

		$is_resolved = royal_bbp_is_topic_resolved( $topic_id );

		$success = $is_resolved ? royal_bbp_mark_unresolved( $topic_id ) : royal_bbp_mark_resolved( $topic_id );
		$failure =  __( '<strong>ERROR</strong>: There was a problem changing this topic&acute;s status.', 'cheerapp' );

		// Check for errors
		if ( false != $success && !is_wp_error( $success ) ) {
			wp_redirect( bbp_get_topic_permalink( $topic_id ) );

			exit();
		} else {
			bbpress()->errors->add( 'bbp_toggle_topic', $failure );
		}
	}
}
add_action( 'template_redirect', 'royal_bbp_toggle_topic_handler', 1 );

/**
 * Mark a topic not resolved.
 *
 * @since 0.1
 */
function royal_bbp_mark_unresolved( $topic_id = 0 ) {

	if ( !$topic = wp_get_single_post( $topic_id, ARRAY_A ) )
		return $topic;

	update_post_meta( $topic_id, '_royal_bbp_topic_status', 'no' );

	return $topic_id;
}

/**
 * Mark a topic resolved.
 *
 * @since 0.1
 */
function royal_bbp_mark_resolved( $topic_id = 0 ) {

	if ( !$topic = wp_get_single_post( $topic_id, ARRAY_A ) )
		return $topic;

	update_post_meta( $topic_id, '_royal_bbp_topic_status', 'yes' );

	return $topic_id;
}

/**
 * Mark a topic as not a support question.
 *
 * @since 0.1
 */
function royal_bbp_mark_not_support( $topic_id = 0 ) {

	if ( !$topic = wp_get_single_post( $topic_id, ARRAY_A ) )
		return $topic;

	update_post_meta( $topic_id, '_royal_bbp_topic_status', 'not_support' );

	return $topic_id;
}

/**
 * Mark a topic as announcement.
 *
 * @since 0.1
 */
function royal_bbp_mark_announcement( $topic_id = 0 ) {

	if ( !$topic = wp_get_single_post( $topic_id, ARRAY_A ) )
		return $topic;

	update_post_meta( $topic_id, '_royal_bbp_topic_status', 'announcement' );

	return $topic_id;
}

function royal_bbp_create_unresolved( $topid_id ) {

	if ( !$topic = wp_get_single_post( $topic_id, ARRAY_A ) )
		return $topic;

	add_post_meta( $topic_id, '_royal_bbp_topic_status', 'no' );	

	return $topic_id;
}
//add_action( 'save_post', 'royal_bbp_create_unresolved' );

/**
 * Output the status link of the topic.
 *
 * @since 0.1
 *
 * @param mixed $args See {@link royal_bbp_get_topic_status_link()}
 * @uses royal_bbp_get_topic_status_link() To get the topic status link
 */
function royal_bbp_topic_status_link( $args = '' ) {
	echo royal_bbp_get_topic_status_link( $args );
}

	/**
	 * Return the status link of the topic
	 *
	 * @since 0.1
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - resolved_text: Close text
	 *  - unresolved_text: Open text
	 * @uses royal_bbp_is_topic_support_question
	 * @uses royal_bbp_get_topic_status_tag
	 * @uses royal_bbp_is_topic_resolved
	 * @uses royal_bbp_topic_status
	 * @uses royal_bbp_get_topic_status_link
	 *
	 * @return string Topic status link
	 */
	function royal_bbp_get_topic_status_link( $args = '' ) {
		$defaults = array (
			'id'					=> 0,
			'resolved_text'			=>	__( 'Resolved',							'cheerapp' ),
			'unresolved_text'		=>	__( 'Not Resolved',						'cheerapp' ),
			'not_support_text'		=>	__( 'Not Support',						'cheerapp' ),
			'announcement_text'		=>	__( 'Announcement',						'cheerapp' ),
			'mark_resolved_title'	=>	__( 'Mark this thread as resolved',		'cheerapp' ),
			'mark_unresolved_title'	=>	__( 'Mark this thread as not resolved',	'cheerapp' )
		);
		
		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		if ( empty( $topic ) )
			return;
			
		if( !current_user_can( 'edit_topic', $topic->ID ) || !royal_bbp_is_topic_support_question( $topic->ID ) || ( bbp_is_topic_closed( $topic->ID ) && !current_user_can( 'delete_post' ) ) )
			return royal_bbp_get_topic_status_tag( $r );
			
		if( current_user_can( 'edit_topic', $topic->ID ) ) :
			$display	=	royal_bbp_is_topic_resolved( $topic->ID ) ? $resolved_text 			: $unresolved_text;
			$title		=	royal_bbp_is_topic_resolved( $topic->ID ) ? $mark_unresolved_title	: $mark_resolved_title;

			$uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_status', 'topic_id' => $topic->ID ) );
			$uri = esc_url( wp_nonce_url( $uri, 'status-topic_' . $topic->ID ) );
	
			return apply_filters( 'royal_bbp_get_topic_status_link', '<a title="' . $title . '" href="' . $uri . '" class="topic-status ' . royal_bbp_topic_status( $topic->ID ) . '">' . $display . '</a>', $args );
		endif;
	}

/**
 * Output the status of a topic in flat text.
 *
 * @since 0.1
 *
 * @param mixed $args See {@link royal_bbp_get_topic_status_tag()}
 * @uses royal_bbp_get_topic_status_tag() To get the topic status link
 */
function royal_bbp_topic_status_tag( $args = '' ) {
	echo royal_bbp_get_topic_status_tag( $args );
}

	/**
	 * Return the status of the topic, no link.
	 *
	 * @since 0.1
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - resolved_text
	 *  - unresolved_text
	 *  - not_support_text
	 *  - announcement_text
	 * @uses royal_bbp_is_topic_not_resolved
	 * @uses royal_bbp_is_topic_resolved
	 * @uses royal_bbp_is_topic_announcement
	 * @uses royal_bbp_is_topic_not_support
	 * @uses royal_bbp_topic_status
	 * @uses royal_bbp_get_topic_status_tag
	 *
	 * @return string Topic status tag
	 */
	function royal_bbp_get_topic_status_tag( $args = '' ) {
		$defaults = array (
			'id'				=>	0,
			'resolved_text'		=>	__( 'Resolved',		'cheerapp' ),
			'unresolved_text'	=>	__( 'Not Resolved',	'cheerapp' ),
			'not_support_text'	=>	__( 'Not Support',	'cheerapp' ),
			'announcement_text'	=>	__( 'Announcement',	'cheerapp' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		$display;
		
		if( royal_bbp_is_topic_not_resolved ( $topic->ID ) )
			$display = $unresolved_text;
		if( royal_bbp_is_topic_resolved     ( $topic->ID ) )
			$display = $resolved_text;
		if( royal_bbp_is_topic_not_support  ( $topic->ID ) )
			$display = $not_support_text;
		if( royal_bbp_is_topic_announcement ( $topic->ID ) )
			$display = $announcement_text;

		return apply_filters( 'royal_bbp_get_topic_status_tag', '<span class="topic-status ' . royal_bbp_topic_status( $topic->ID ) . '">' . $display . '</span>' );
	}

/**
 * Check to see if a topic has been resolved.
 *
 * @since 0.1
 *
 * @param int $topic_id The ID of the current topic.
 * @return boolean True if resolved.
 */	
function royal_bbp_is_topic_resolved( $topic_id ) {
	$resolved = get_post_meta( $topic_id, '_royal_bbp_topic_status', true );

	if ( $resolved != "" && $resolved == 'yes' )
		return true;		

	return false;
}

/**
 * Check to see if a topic has a not resolved status.
 *
 * @since 0.1
 *
 * @param int $topic_id The ID of the current topic.
 * @return boolean True if not resolved.
 */	
function royal_bbp_is_topic_not_resolved( $topic_id ) {
	$not_resolved = get_post_meta( $topic_id, '_royal_bbp_topic_status', true );

	if ( $not_resolved != "" && $not_resolved == 'no' )
		return true;		

	return false;
}

/**
 * Check to if a topic is a support question.
 *
 * @since 0.1
 *
 * @param int $topic_id The ID of the current topic.
 * @return boolean True if topic is a support question.
 */	
function royal_bbp_is_topic_support_question( $topic_id ) {
	$status = get_post_meta( $topic_id, '_royal_bbp_topic_status', true );
	
	if( $status != "" && ( $status == 'yes' || $status == 'no' ) )
		return true;
		
	return false;
}

/**
 * Check to see if a topic has an announcement status.
 *
 * @since 0.1
 *
 * @param int $topic_id The ID of the current topic.
 * @return boolean
 */	
function royal_bbp_is_topic_announcement( $topic_id ) {
	$announcement = get_post_meta( $topic_id, '_royal_bbp_topic_status', true );

	if ( $announcement != "" && $announcement == 'announcement' )
		return true;		

	return false;
}

/**
 * Check to see if a topic is not a support question.
 *
 * @since 0.1
 *
 * @param int $topic_id The ID of the current topic.
 * @return boolean
 */	
function royal_bbp_is_topic_not_support( $topic_id ) {
	$not_support = get_post_meta( $topic_id, '_royal_bbp_topic_status', true );

	if ( $not_support != "" && $not_support == 'not_support' )
		return true;		

	return false;
}

/**
 * Return a slug of topic status
 *
 * @since 0.1
 *
 * @param int $topic_id The ID of the current topic.
 * @return string Slug of current topic status.
 */	
function royal_bbp_topic_status( $topic_id ) {
	if ( royal_bbp_is_topic_resolved( $topic_id ) ) :
		return 'resolved';
	elseif ( royal_bbp_is_topic_announcement( $topic_id ) ) :
		return 'announcement';
	elseif ( royal_bbp_is_topic_not_support( $topic_id ) ) :
		return 'not-support';
	endif;

	return 'unresolved';
}

/**
 * When a new topic is added or edited set topic status.
 *
 * @since 0.1
 */	
function royal_bbp_set_topic_status_from_topic_form( $topic_id, $forum_id, $anonymous_data, $topic_author ) {

	$allowed_statuses	=	array( 'yes', 'no', 'announcement', 'not_support', 'support' );
	$action				=	$_POST['action'];
	
	if( !isset( $_POST['royal_bbp_topic_status'] ) || !in_array( $_POST['royal_bbp_topic_status'], $allowed_statuses ) )
		return;

	if( current_user_can( 'edit_topic', $topic_id ) && $action == 'bbp-new-topic' ) :
	
		$status = $_POST['royal_bbp_topic_status'] ? $_POST['royal_bbp_topic_status'] : 'not_support';
		
		if( $status == 'support' || ( current_user_can( 'moderate' ) && $status == 'no' ) ) {
			add_post_meta( $topic_id, '_royal_bbp_topic_status', 'no' );
		}
		elseif( $status == 'yes' && current_user_can( 'moderate' ) ) {
			add_post_meta( $topic_id, '_royal_bbp_topic_status', 'yes' );
		}
		elseif( $status == 'announcement' && current_user_can( 'moderate' ) ) {
			add_post_meta( $topic_id, '_royal_bbp_topic_status', 'announcement' );
		}
		else {
			add_post_meta( $topic_id, '_royal_bbp_topic_status', $status );
		}
		
		return $topic_id;
		
	elseif( current_user_can( 'edit_topic', $topic_id ) && $action == 'bbp-edit-topic' && ( !bbp_is_topic_closed( $topic_id ) || current_user_can( 'moderate' ) ) ) :
		
		$status = $_POST['royal_bbp_topic_status'];
		
		if( $status == 'support' || $status == 'no' ) {
			royal_bbp_mark_unresolved( $topic_id );
		}
		elseif( $status == 'yes' ) {
			royal_bbp_mark_resolved( $topic_id );
		}
		elseif( $status == 'not_support' ) {
			royal_bbp_mark_not_support( $topic_id );
		}
		elseif( $status == 'announcement' && current_user_can( 'moderate' ) ) {
			royal_bbp_mark_announcement( $topic_id );
		}
		
		return $topic_id;
		
	else :
	
		return;
		
	endif;
	
}
add_action( 'bbp_new_topic',  'royal_bbp_set_topic_status_from_topic_form', 10, 4 );
add_action( 'bbp_edit_topic', 'royal_bbp_set_topic_status_from_topic_form', 10, 4 );

/**
 * When a new reply is added set topic status.
 *
 * @since 0.1
 */	
function royal_bbp_set_topic_status_from_reply_form( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author ) {

	$allowed_statuses	=	array( 'yes', 'no', 'announcement', 'not_support', 'support' );
	$action				=	$_POST['action'];
	
	if( !isset( $_POST['royal_bbp_topic_status'] ) || !in_array( $_POST['royal_bbp_topic_status'], $allowed_statuses ) )
		return;
		
	if( current_user_can( 'edit_topic', $topic_id ) && $action == 'bbp-new-reply' && ( !bbp_is_topic_closed( $topic_id ) || current_user_can( 'moderate' ) ) ) :
		
		$status = $_POST['royal_bbp_topic_status'];
		
		if( $status == 'support' || $status == 'no' ) {
			royal_bbp_mark_unresolved( $topic_id );
		}
		elseif( $status == 'yes' ) {
			royal_bbp_mark_resolved( $topic_id );
		}
		elseif( $status == 'not_support' ) {
			royal_bbp_mark_not_support( $topic_id );
		}
		elseif( $status == 'announcement' && current_user_can( 'moderate' ) ) {
			royal_bbp_mark_announcement( $topic_id );
		}
		
		return $topic_id;
		
	else :
	
		return;
		
	endif;
	
}
add_action( 'bbp_new_reply',  'royal_bbp_set_topic_status_from_reply_form', 10, 6 );

/** Adds 'Thread status' field to topic and reply forms
 *
 * @since 0.1
 *
 * @param mixed $args Function arguments
 *		- id Post ID
 * 		- resolved_text
 *		- unresolved_text
 *		- support_text
 *		- not_support_text
 *		- announcement_text
 */
function royal_bbp_add_status_field( $args = '' ) {
	
	$defaults = array (
		'id'				=>	0,
		'resolved_text'		=>	__( 'Resolved',			'cheerapp' ),
		'unresolved_text'	=>	__( 'Not resolved',		'cheerapp' ),
		'support_text'		=>	__( 'Support question',	'cheerapp' ),
		'not_support_text'	=>	__( 'General question',	'cheerapp' ),
		'announcement_text'	=>	__( 'Announcement',		'cheerapp' ),
		'field_label'		=>	__( 'Thread status',	'cheerapp' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );
	
	// Bail if it's an edit reply page
	if( bbp_is_reply_edit() )
		return;
	
	if( !empty( $topic ) ) {
		$topic_status = royal_bbp_topic_status( $topic->ID );
		if( $topic_status == 'unresolved' )		$topic_status = 'no';
		if( $topic_status == 'resolved' )		$topic_status = 'yes';
		if( $topic_status == 'not-support' )	$topic_status = 'not_support';
	}
	$statuses = array();
	
	// If admin or moderator is viewing the page
	if( current_user_can( 'moderate' ) ) {
		$statuses['not_support']	=	$not_support_text;
		$statuses['no']				=	$unresolved_text;
		$statuses['yes']			=	$resolved_text;
		$statuses['announcement']	=	$announcement_text;
	}
	// If topic author is viewing the page and topic is a support question
	elseif( current_user_can( 'edit_topic', $topic->ID ) && !bbp_is_topic_closed() && royal_bbp_is_topic_support_question( $topic->ID ) ) {
		$statuses['no']				=	$unresolved_text;
		$statuses['yes']			=	$resolved_text;
		$statuses['not_support']	=	$not_support_text;
	}
	// If anonymous user is viewing the page and it is not a topic page
	elseif( bbp_is_anonymous() && empty( $topic ) ) {
		$statuses['not_support']	=	$not_support_text;
	}
	// If the topic is not a support question and topic author is viewing the page or if it's a create new topic form
	elseif( empty( $topic ) || ( current_user_can( 'edit_topic', $topic->ID ) && !empty( $topic ) ) ) {
		$statuses['not_support']	=	$not_support_text;
		$statuses['support']		=	$support_text;
	}
	
	if( $statuses ) {
		$field = '<div class="control-group"><label for="royal_topic_status" class="control-label">' . $field_label . '</label><div class="controls">';
		$field .= '<select id="royal_topic_status" name="royal_bbp_topic_status">';
		
		foreach( $statuses as $value => $display ) {
			$field .= '<option value="' . $value . '"';
			if( !empty( $topic ) ) {
				if( $value == $topic_status || ( $value == 'support' && ( $topic_status == 'yes' || $topic_status == 'no' ) ) ) {
					$field .= ' selected="selected"';
				}
			}
			$field .= '>' . $display . '</option>';
		}
		
		$field .= '</select></div></div>';
	}
	
	echo $field;
}
add_action( 'bbp_theme_before_topic_form_content', 'royal_bbp_add_status_field' );
add_action( 'bbp_theme_before_reply_form_content', 'royal_bbp_add_status_field' );

/**
 * Outputs topic date in human readable format
 *
 * @since 0.1
 *
 * @param int $topic_id An ID of topic
 * @uses royal_bbp_get_topic_time_ago
 */
function royal_bbp_topic_time_ago( $topic_id = 0 ) {
	echo royal_bbp_get_topic_time_ago( $topic_id );
}

	/**
	 * Returns topic date in human readable format, like '2 hours ago'.
	 *
	 * @since 0.1
	 *
	 * @param int $topic_id An ID of topic
	 * @return string
	 */
	function royal_bbp_get_topic_time_ago( $topic_id = 0 ) {
		$topic_id	=	bbp_get_topic_id( $topic_id );
		$topic_time	=	get_the_time( 'U', $topic_id );
		$time_ago	=	human_time_diff( $topic_time, current_time('timestamp') );
		
		if( $time_ago == '1 min' ) :
			$time_ago = __( 'Just now', 'cheerapp' );
		else :
			$time_ago .= __( ' ago', 'cheerapp' );
		endif;

		return apply_filters( 'royal_bbp_get_topic_time_ago', $time_ago );
	}
	
/**
 * Output link to the most recent activity inside a topic, complete with link
 * attributes and content.
 *
 * @since 0.1
 *
 * @param int $topic_id Optional. Topic id
 * @uses royal_bbp_get_topic_freshness_link
 */
function royal_bbp_topic_freshness_link( $topic_id = 0, $display = 'time', $class = '', $title = 'author' ) {
	echo royal_bbp_get_topic_freshness_link( $topic_id, $display, $class, $title );
}
	/**
	 * Returns link to the most recent activity inside a topic, complete
	 * with link attributes and content.
	 *
	 * @since 0.1
	 *
	 * @param int $topic_id Optional. Topic id
	 * @return string Topic freshness link
	 */
	function royal_bbp_get_topic_freshness_link( $topic_id = 0, $display = 'time', $class = '', $title = 'author' ) {
		$topic_id		=	bbp_get_topic_id( $topic_id );
		$last_reply_id	=	bbp_get_topic_last_active_id( $topic_id );
		$link_url		=	bbp_get_topic_last_reply_url( $topic_id );
		$reply_count	=	bbp_show_lead_topic() ? bbp_get_topic_reply_count( $topic_id ) : bbp_get_topic_post_count( $topic_id );
		
		$author_name	=	bbp_get_topic_author_display_name( $last_reply_id );
		$time_ago		=	bbp_get_topic_last_active_time( $topic_id );
		
		$link_title		=	$title == 'author' ? $author_name : $time_ago;
		$link_display	=	$display == 'time' ? $time_ago : $author_name;

		if ( !empty( $reply_count ) )
			$anchor = '<a href="' . $link_url . '" title="' . esc_attr( $link_title ) . '" class="' . $class . '">' . $link_display . '</a>';
		else
			$anchor = __( 'No Replies', 'cheerapp' );

		return apply_filters( 'royal_bbp_get_topic_freshness_link', $anchor, $topic_id, $display, $class, $title );
	}
	
/**
 * Output link to the most recent activity inside a topic, complete with link
 * attributes and content.
 *
 * @since 0.1
 *
 * @param int $topic_id Optional. Topic id
 * @uses royal_bbp_get_topic_freshness_link
 */
function royal_bbp_forum_freshness_link( $forum_id = 0, $display = 'time', $class = '', $title = 'author' ) {
	echo royal_bbp_get_forum_freshness_link( $forum_id, $display, $class, $title );
}
	/**
	 * Returns link to the most recent activity inside a topic, complete
	 * with link attributes and content.
	 *
	 * @since 0.1
	 *
	 * @param int $topic_id Optional. Topic id
	 * @return string Topic freshness link
	 */
	function royal_bbp_get_forum_freshness_link( $forum_id = 0, $display = 'time', $class = '', $title = 'author' ) {
		$active_id = bbp_get_forum_last_active_id( $forum_id );

		if ( empty( $active_id ) )
			$active_id = bbp_get_forum_last_reply_id( $forum_id );

		if ( empty( $active_id ) )
			$active_id = bbp_get_forum_last_topic_id( $forum_id );

		if ( bbp_is_topic( $active_id ) ) {
		
			$link_url	=	bbp_get_forum_last_topic_permalink( $forum_id );
			$title		=	bbp_get_forum_last_topic_title( $forum_id );
			
			$time_ago		=	bbp_get_forum_last_active_time( $forum_id );
			$author_name	=	bbp_get_topic_author_display_name( $active_id );
			
			$link_display	=	$display == 'time' ? $time_ago : $author_name;
			$link_title		=	$title == 'author' ? $author_name : $time_ago;
			
		} elseif ( bbp_is_reply( $active_id ) ) {
		
			$link_url		=	bbp_get_forum_last_reply_url( $forum_id );
			$title			=	bbp_get_forum_last_reply_title( $forum_id );
			
			$time_ago		=	bbp_get_forum_last_active_time( $forum_id );
			$author_name	=	bbp_get_reply_author_display_name( $active_id );
			
			$link_display	=	$display == 'time' ? $time_ago : $author_name;
			$link_title		=	$title == 'author' ? $author_name : $time_ago;
			
		}

		if ( !empty( $display ) && !empty( $link_url ) )
			$anchor = '<a href="' . $link_url . '" title="' . esc_attr( $link_title ) . '" class="' . $class . '">' . $link_display . '</a>';
		else
			$anchor = __( 'No Topics', 'bbpress' );

		return apply_filters( 'bbp_get_forum_freshness_link', $anchor, $forum_id );
	}
	
/**
 * Outputs a login form or user login info/logout link if user is already logged in
 *
 * @since 0.1
 *
 * @param mixed args
 * @uses royal_bbp_get_user_profile_link
 * @uses royal_bbp_get_edit_profile_link
 * @uses royal_bbp_get_logout_link
 */
function royal_login( $args = '' ) {
	
	$defaults = array (
		'container_classes'	=>	array( 'login' )
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$classes = implode( ' ', $container_classes );
?>
	
	<div class="<?php echo $classes; ?>">
	
	<?php if( !is_user_logged_in() ) : ?>
	
		<?php
		$register_page			=	royal_get_page_by_template( 'user-register' );
		$register_page_url		=	!empty( $register_page) ? get_permalink( $register_page->ID ) : null;
		$register_page_title	=	!empty( $register_page) ? $register_page->post_title : null;
		$login_page				=	royal_get_page_by_template( 'user-login' );
		$login_page_url			=	!empty( $login_page ) ? get_permalink( $login_page->ID ) : '#';
		$login_page_title		=	!empty( $login_page ) ? $login_page->post_title : __( 'Sign in', 'cheerapp' );
		?>
		
		<?php // Desktop navigation ?>
		<ul class="dropdown hidden-phone clearfix">
		
			<?php if( !empty( $register_page ) ) : ?>
			<li><a class="register-link button" href="<?php echo $register_page_url; ?>"><?php echo $register_page_title; ?></a></li>
			<?php endif; ?>
			
			<li class="parent">
				<a class="login-link" href="<?php echo $login_page_url; ?>"><?php echo $login_page_title; ?></a>
				<ul class="sub-menu login-form">
					<li>
						<form id="login-form" method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>">
		
							<p>
								<label for="top_user_login"><?php _e( 'Username', 'cheerapp' ); ?></label>
								<input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="top_user_login" placeholder="<?php _e( 'Username', 'cheerapp' ); ?>" tabindex="<?php bbp_tab_index(); ?>" />
							</p>
							
							<p>
								<label for="top_user_pass"><?php _e( 'Password', 'cheerapp' ); ?></label>
								<input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="top_user_pass" placeholder="<?php _e( 'Password', 'cheerapp' ); ?>" tabindex="<?php bbp_tab_index(); ?>" />
							</p>
							
							<p class="bbp-remember-me">
								<label for="top_rememberme">
									<input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ), true, true ); ?> id="top_rememberme" tabindex="<?php bbp_tab_index(); ?>" />
									<?php _e( 'Remember me', 'cheerapp' ); ?>
								</label>
							</p>
									
							<div class="bbp-submit-wrapper">
				
								<?php do_action( 'login_form' ); ?>
				
								<input type="submit" name="user-submit" value="<?php _e( 'Sign in', 'cheerapp' ); ?>" tabindex="<?php bbp_tab_index(); ?>" class="user-submit login-submit button" />
				
								<?php bbp_user_login_fields(); ?>
				
							</div>
						
						</form>
					</li>
				</ul>	
			</li>
			
		</ul>
		
		<?php // Phone navigation ?>
		<select name="login-menu" class="visible-phone phone-nav" id="login-menu">
			<option></option>
			<?php if( !empty( $login_page ) ) : ?>
				<option value="<?php echo $login_page_url; ?>"><?php echo $login_page_title; ?></option>
			<?php endif; ?>
			<?php if( !empty( $register_page ) ) : ?>
				<option value="<?php echo $register_page_url; ?>"><?php echo $register_page_title; ?></option>
			<?php endif; ?>
		</select>
		
	<?php else : ?>
	
		<?php
		$user_id			=	bbp_get_current_user_id();
		$user_profile_url	=	bbp_get_user_profile_url( bbp_get_current_user_id() );
		$user				=	get_userdata( $user_id );
		$name				=	esc_attr( $user->display_name );
		?>
		
		<?php // Desktop navigation ?>	
		<ul class="user-info hidden-phone dropdown clearfix">
			<li class="parent">
				<?php echo royal_bbp_get_user_profile_link( array( 'user_id' => $user_id, 'avatar' => true, 'avatar_size' => '30') ); ?>
				<ul class="user-links sub-menu">
					<li class="user-link"><a href="<?php echo $user_profile_url; ?>"><?php _e( 'Your profile', 'cheerapp' ); ?></a></li>
					<?php if( bbp_is_subscriptions_active() ) : ?>
					<li class="user-link"><a href="<?php echo $user_profile_url; ?>#subscriptions"><?php _e( 'Subscribed threads', 'cheerapp' ); ?></a></li>
					<?php endif; ?>
					<?php if( bbp_is_favorites_active() ) : ?>
					<li class="user-link"><a href="<?php echo $user_profile_url; ?>#favorites"><?php _e( 'Favorite threads', 'cheerapp' ); ?></a></li>
					<?php endif; ?>
					<li class="user-link"><a href="<?php echo $user_profile_url; ?>#topics-created"><?php _e( 'Threads started', 'cheerapp' ); ?></a></li>
					<li class="user-link"><?php echo royal_bbp_get_edit_profile_link(); ?></li>
					<li class="user-link"><?php echo royal_bbp_get_logout_link(); ?></li>
				</ul>
			</li>
		</ul><!-- end .user-info -->
		
		<?php // Phone navigation ?>
		<select name="login-menu" class="visible-phone phone-nav" id="login-menu">
			<option><?php printf( __( 'Signed in as %s', 'cheerapp' ), $name ); ?></option>
			<option value="<?php echo $user_profile_url; ?>"><?php _e( 'Your profile', 'cheerapp' ); ?></option>
			<?php if( bbp_is_subscriptions_active() ) : ?>
				<option value="<?php echo $user_profile_url; ?>#subscriptions"><?php _e( 'Subscribed threads', 'cheerapp' ); ?></option>
			<?php endif; ?>
			<?php if( bbp_is_favorites_active() ) : ?>
				<option value="<?php echo $user_profile_url; ?>#favorites"><?php _e( 'Favorite threads', 'cheerapp' ); ?></option>
			<?php endif; ?>
			<option value="<?php bbp_user_profile_edit_url( $user_id ); ?>"><?php _e( 'Edit your profile', 'cheerapp' ); ?></option>
			<option value="<?php echo wp_logout_url(); ?>"><?php _e( 'Sign out', 'cheerapp' ); ?></option>
		</select>
						
	<?php endif; ?>			
	
	</div>
					
<?php
}

/**
 * Returns a link to user profile
 *
 * @since 0.1
 *
 * @param mixed $args Function arguments
 *		- $user_id ID of user for whom to retrieve link
 *		- $avatar bool Include avatar?
 *
 * @return string Link to user profile
 */
function royal_bbp_get_user_profile_link( $args = '' ) {
	$defaults = array (
		'user_id'		=>	'',
		'class'			=>	'user-profile-link',
		'avatar'		=>	true,
		'avatar_size'	=>	'15'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	if ( !$user_id = bbp_get_user_id( $user_id ) )
		return false;

	$user      = get_userdata( $user_id );
	$name      = esc_attr( $user->display_name );
	$user_link = '<a href="' . bbp_get_user_profile_url( $user_id ) . '" class="' . $class . '">';
	if( $avatar ) $user_link .= get_avatar( $user_id, $avatar_size );
	$user_link .= $name . '</a>';

	return $user_link;
}

/**
 * Returns a link to edit user profile
 *
 * @since 0.1
 *
 * @param mixed $args Function arguments
 *
 * @return string Edit profile link
 */
function royal_bbp_get_edit_profile_link( $args = '' ) {
	$defaults = array (
		'user_id'		=>	''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	if ( !$user_id = bbp_get_user_id( $user_id ) ) {
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
	}
	
	$link = '<a href="' . bbp_get_user_profile_edit_url( $user_id ) . '" class="edit-profile-link">' . __( 'Edit your profile', 'cheerapp' ) . '</a>';
	
	return $link;
}

/** Returns a logout link
 *
 * @since 0.1
 *
 * @param string $redirect_to Redirect to url
 *
 * @return string Logout link
 */
function royal_bbp_get_logout_link( $redirect_to = '' ) {
	return apply_filters( 'royal_bbp_get_logout_link', '<a href="' . wp_logout_url( $redirect_to ) . '" class="logout-link">' . __( 'Sign out', 'cheerapp' ) . '</a>', $redirect_to );
}

if ( !class_exists( 'Royal_BBP_Ajax' ) ) :
/**
 * Contains some functions and actions
 * necessary to perform Ajax user interactions.
 *
 * @since 0.1
 */
class Royal_BBP_Ajax {

	/**
	 * The main class constructor.
	 *
	 * @since 0.1
	 * @uses Royal_BBP_Ajax::setup_actions()
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup the theme hooks
	 *
	 * @since 0.1
	 * @access private
	 */
	private function setup_actions() {

		// Enqueue theme JS
		add_action( 'bbp_enqueue_scripts',      array( $this, 'enqueue_scripts'       ) );

		// Enqueue theme script localization
		add_filter( 'bbp_enqueue_scripts',      array( $this, 'localize_topic_script' ) );

		// Output some extra JS in the <head>
		add_action( 'wp_head',                 array( $this, 'head_scripts'          ) );

		// Handles the ajax favorite/unfavorite
		add_action( 'wp_ajax_topic_favorite_toggle',     array( $this, 'ajax_favorite'         ) );

		// Handles the ajax subscribe/unsubscribe
		add_action( 'wp_ajax_topic_subscription_toggle', array( $this, 'ajax_subscription'     ) );
	}

	/**
	 * Enqueue the required Javascript files
	 *
	 * @since 0.1
	 */
	public function enqueue_scripts() {
	
		if( bbp_is_single_topic() ) {
			wp_deregister_script( 'bbpress-topic' );
			wp_register_script( 'bbpress-topic', get_template_directory_uri() . '/js/bbp-ajax.js', array( 'jquery', 'wp-ajax-response' ), '1.0.5', true );
			wp_enqueue_script( 'bbpress-topic' );
		}
	
		if ( bbp_is_single_user_edit() )
			wp_enqueue_script( 'user-profile' );
	}
	
	/**
	 * Put some scripts in the header.
	 *
	 * @since 0.1
	 */
	public function head_scripts() {
		if ( bbp_is_single_user_edit() ) : ?>

		<script type="text/javascript" charset="utf-8">
			if ( window.location.hash == '#password' ) {
				document.getElementById('pass1').focus();
			}
		</script>

		<?php
		endif;
	}

	/**
	 * Load localizations for topic script
	 *
	 * These localizations require information that may not be loaded even by init.
	 *
	 * @since 0.1
	 */
	public function localize_topic_script() {

		// Bail if not viewing a single topic
		if ( !bbp_is_single_topic() )
			return;

		// Bail if user is not logged in
		if ( !is_user_logged_in() )
			return;

		$user_id = bbp_get_current_user_id();

		$localizations = array(
			'currentUserId' => $user_id,
			'topicId'       => bbp_get_topic_id(),
		);

		// Favorites
		if ( bbp_is_favorites_active() ) {
			$localizations['favoritesActive'] = 1;
			$localizations['favoritesLink']   = bbp_get_favorites_permalink( $user_id );
			$localizations['isFav']           = __( 'Your favorite',				'cheerapp' );
			$localizations['favDel']          = __( 'Remove from favorites?',		'cheerapp' );
			$localizations['favAdd']          = __( 'Add this thread to favorites',	'cheerapp' );
		} else {
			$localizations['favoritesActive'] = 0;
		}

		// Subscriptions
		if ( bbp_is_subscriptions_active() ) {
			$localizations['subsActive']   = 1;
			$localizations['isSubscribed'] = __( 'You&#8217;re subscribed to replies',				'cheerapp' );
			$localizations['subDel']       = __( 'Unsubscribe?',				'cheerapp' );
			$localizations['subAdd']       = __( 'Subscribe to replies via e-mail',	'cheerapp' );
		} else {
			$localizations['subsActive'] = 0;
		}

		wp_localize_script( 'bbpress-topic', 'bbpTopicVars', $localizations );
	}

	/**
	 * Add or remove a topic from a user's favorites
	 *
	 * @since 0.1
	 */
	public function ajax_favorite() {
		$user_id	=	bbp_get_current_user_id();
		$id			=	intval( $_REQUEST['id'] );
		$response	=	new WP_Ajax_Response();

		if ( !current_user_can( 'edit_user', $user_id ) ) {
			$response->add( array( 'what' => 'no_priv' ) );
			$response->send();
			exit;
		}

		if ( !$topic = bbp_get_topic( $id ) ) {
			$response->add( array( 'what' => 'no_topic' ) );
			$response->send();
			exit;
		}

		check_ajax_referer( 'toggle-favorite_' . $topic->ID );

		if ( bbp_is_user_favorite( $user_id, $topic->ID ) ) {
			if ( bbp_remove_user_favorite( $user_id, $topic->ID ) ) {
				$response->add( array( 'what' => 'removed' ) );
			}
		} elseif ( !bbp_is_user_favorite( $user_id, $topic->ID ) ) {
			if ( bbp_add_user_favorite( $user_id, $topic->ID ) ) {
				$response->add( array( 'what' => 'added' ) );
			}
		}
		
		$response->send();
		exit;
	}

	/**
	 * Subscribe/Unsubscribe a user from a topic
	 *
	 * @since 0.1
	 */
	public function ajax_subscription() {
		if ( !bbp_is_subscriptions_active() )
			return;

		$user_id	=	bbp_get_current_user_id();
		$id			=	intval( $_REQUEST['id'] );
		$response	=	new WP_Ajax_Response();

		if ( !current_user_can( 'edit_user', $user_id ) ) {
			$response->add( array( 'what' => 'no_priv' ) );
			$response->send();
			exit;
		}

		if ( !$topic = bbp_get_topic( $id ) ) {
			$response->add( array( 'what' => 'no_topic' ) );
			$response->send();
			exit;
		}

		check_ajax_referer( 'toggle-subscription_' . $topic->ID );

		if ( bbp_is_user_subscribed( $user_id, $topic->ID ) ) {
			if ( bbp_remove_user_subscription( $user_id, $topic->ID ) ) {
				$response->add( array( 'what' => 'removed' ) );
			}
		} elseif ( !bbp_is_user_subscribed( $user_id, $topic->ID ) ) {
			if ( bbp_add_user_subscription( $user_id, $topic->ID ) ) {
				$response->add( array( 'what' => 'added' ) );
			}
		}

		$response->send();
		exit;
	}
}

/**
 * Instantiate a new Royal_BBP_Ajax class.
 */
$royal_bbp_ajax = new Royal_BBP_Ajax();

endif;

?>