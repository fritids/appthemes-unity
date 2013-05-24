<?php

// main comments callback function
function clpr_comment_template($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; 
	
	switch ( $comment->comment_type ) :
	
	case '' :
	?>
	
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

			<div class="items">

				<div class="r">

					<div class="lt">

						<div class="rt">

							<?php echo get_avatar($comment, 58 ); ?>

							<div class="bar">

								<?php comment_author_link(); ?>

								<span class="date-wrap"><span class="date"><?php comment_time(get_option('date_format')); ?></span><span class="time"><?php comment_time(get_option('time_format')); ?></span></span>

							</div> <!-- #bar -->

							<?php comment_text(); ?>
							
						</div> <!-- #rt -->

					</div> <!-- #lt -->

				</div> <!-- #r -->

			</div> <!-- #items -->

        <div class="items-b">

            <div class="items-b-holder">

            <?php comment_reply_link(array_merge( $args, array(
                    'reply_text' => '<span>' . __( 'Reply', APP_TD ) . '</span>',
                    'respond_id' => 'respond',
                    'before' => '',
                    'after' => '',
                    'depth' => $depth,
                    'max_depth' => $args['max_depth']
            ))); ?>

            </div> <!-- #items-b-holder -->

        </div> <!-- #items-b -->	
		
		<div id="comment-<?php comment_ID(); ?>"></div>
		
		<div class="clr"></div>
	
	<?php
	break;
	case 'pingback'  :
	case 'trackback' :
	?>
	
		<li class="post pingback"><?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', APP_TD ), ' ' ); ?></li>	
	
	<?php
	break;
	endswitch;
}


// main comments form 
function clpr_main_comment_form() {
	global $post; 
?>
<div id="respond">

	<form action="<?php echo site_url('wp-comments-post.php'); ?>" method="post" id="commentForm" class="post-form">
	
		<?php do_action( 'comment_form_top' ); ?>

		<div class="cancel-comment-reply"><?php cancel_comment_reply_link( __( 'Click here to cancel reply', APP_TD ) ); ?></div>
		
		<div class="clr">&nbsp;</div>
		
		<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
		
			<p><?php printf( __( 'You must be <a href="%s">logged in</a> to post a comment.', APP_TD ), wp_login_url( get_permalink() ) ); ?></p>
		
		<?php else : ?>
		
			<?php if ( is_user_logged_in() ) : global $user_identity; ?>
			
				<p><?php printf( __( 'Logged in as <a href="%1$s">%2$s</a>.', APP_TD ), CLPR_PROFILE_URL, $user_identity ); ?> <a href="<?php echo clpr_logout_url(get_permalink()); ?>"><?php _e( 'Log out &raquo;', APP_TD ); ?></a></p>
				
			<?php else : ?>
			
        <?php 
          $commenter = wp_get_current_commenter();
          $req = get_option( 'require_name_email' ); 
        ?>

				<p>
					<label><?php _e( 'Name:', APP_TD ); ?></label>
					<input type="text" class="text required" name="author" id="author" value="<?php echo esc_attr( $commenter['comment_author'] ); ?>" />
				</p>
				
				<p>
					<label><?php _e( 'Email:', APP_TD ); ?></label>
					<input type="text" class="text required" name="email" id="email" value="<?php echo esc_attr(  $commenter['comment_author_email'] ); ?>" />
				</p>
				
				<p>
					<label><?php _e( 'Website:', APP_TD ); ?></label>
					<input type="text" class="text" name="url" id="url" value="<?php echo esc_attr( $commenter['comment_author_url'] ); ?>" />
				</p>
				
			<?php endif; ?>
			
			<p>
				<textarea cols="30" rows="10" name="comment" class="commentbox required" id="comment"></textarea>						
			</p>
						
			<p>
				<button type="submit" class="btn submit" id="submitted" name="submitted" value="submitted"><?php _e( 'Submit', APP_TD ); ?></button>
			</p>
			
			<p>
			
			<?php
				comment_id_fields();
				do_action('comment_form', $post->ID);
			?>
			
			</p>
			
		<?php endif; ?>

	</form>	
	
	
	
</div> <!-- #respond -->	
<div class="clr">&nbsp;</div>
<?php
}
// use this comments form within the appthemes action hook
add_action('appthemes_blog_comments_form', 'clpr_main_comment_form');


// mini comments add comment link generator
function clpr_comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	global $id;

    if ( false === $zero ) $zero = __( 'No Comments', APP_TD );
    if ( false === $one )  $one  = __( '1 Comment', APP_TD );
    if ( false === $more ) $more = __( '% Comments', APP_TD );
    if ( false === $none ) $none = __( 'Comments Off', APP_TD );

	$number = get_comments_number( $id );

	// show "No Comments" and no link if comments aren't open
	if ( 0 == $number && !comments_open() ) {
		echo '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';
		return;
	}

	if ( post_password_required() ) {
		echo __( 'Enter your password to view comments.', APP_TD );
		return;
	}

	echo '<a href="';
	echo admin_url( 'admin-ajax.php' ) . '?action=comment-form&amp;id=' . $id . '"';

	if ( !empty( $css_class ) ) {
		echo ' class="'.$css_class.'" ';
	}
	$title = the_title_attribute( array('echo' => 0 ) );

	echo apply_filters( 'comments_popup_link_attributes', '' );

	echo ' title="' . esc_attr( sprintf( __( 'Comment on %s', APP_TD ), $title ) ) . '" data-rel="' . $id . '" >';
	comments_number( $zero, $one, $more );
	echo '</a>';
}


// mini comments pop-up form  	
function clpr_comment_form() {

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

					<div class="head"><h3><?php comments_number( __( 'No Responses', APP_TD ), __( 'One Response', APP_TD ), __( '% Responses', APP_TD ) );?> <?php _e( 'to', APP_TD ); ?> &#8220;<?php the_title(); ?>&#8221;</h3></div>
					
					<div id="respond">
					
						<form action="<?php echo admin_url( 'admin-ajax.php' );  ?>?action=post-comment" method="post" id="commentform-<?php echo $post->ID; ?>" class="commentForm">
							
							<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
				
								<p><?php printf( __( 'You must be <a href="%s">logged in</a> to post a comment.', APP_TD ), wp_login_url( get_permalink() ) ); ?></p>
							
							<?php else : ?>

								<?php if ( is_user_logged_in() ) : global $user_identity; ?>

									<p><?php printf( __( 'Logged in as <a href="%1$s">%2$s</a>.', APP_TD ), CLPR_PROFILE_URL, $user_identity ); ?> <a href="<?php echo clpr_logout_url(get_permalink()); ?>"><?php _e( 'Log out &raquo;', APP_TD ); ?></a></p>

								<?php else : ?>

									<p>
										<label><?php _e( 'Name:', APP_TD ); ?></label>
										<input type="text" class="text required" name="author" id="author-<?php echo $post->ID; ?>" value="<?php echo esc_attr($comment_author); ?>" />
									</p>
									
									<p>
										<label><?php _e( 'Email:', APP_TD ); ?></label>
										<input type="text"  class="text required email" name="email" id="email-<?php echo $post->ID; ?>" value="<?php echo esc_attr($comment_author_email); ?>" />
									</p>
									
									<p>
										<label><?php _e( 'Website:', APP_TD ); ?></label>
										<input type="text"  class="text" name="url" id="url-<?php echo $post->ID; ?>" value="<?php echo esc_attr($comment_author_url); ?>" />
									</p>
									
								<?php endif; ?>
								
								<p>
									<textarea cols="30" rows="10" name="comment" class="commentbox required" id="comment-<?php echo $post->ID; ?>"></textarea>						
								</p>
											
								<p>
									<button type="submit" class="comment-submit btn submit" id="submitted" name="submitted" value="submitted"><?php _e( 'Submit', APP_TD ); ?></button>			
									<input type='hidden' name='comment_post_ID' value='<?php echo $post->ID; ?>' id='comment_post_ID' />
								</p>
								
								<?php do_action('comment_form', $post->ID); ?>
								
							<?php endif; ?>
							
						</form>
					
					</div> <!-- #respond -->
					
				</div> <!-- #post-box -->
				
			</div> <!-- #box-holder -->
			
		</div> <!-- #box-c -->
		
		<div class="box-b">&nbsp;</div>
		
	</div> <!-- #content-box -->

	<?php
	die;
}


// mini comments post via ajax
function clpr_post_comment_ajax() {
	global $wpdb;

    if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
            header('Allow: POST');
            header('HTTP/1.1 405 Method Not Allowed');
            header('Content-Type: text/plain');
            exit;
    }

    nocache_headers();

    $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
    $post = get_post($comment_post_ID);

    if ( empty($post->comment_status) ) {
            do_action('comment_id_not_found', $comment_post_ID);
            exit;
    }

    // get_post_status() will get the parent status for attachments.
    $status = get_post_status($post);

    $status_obj = get_post_status_object($status);

    if ( !comments_open($comment_post_ID) ) {
            do_action('comment_closed', $comment_post_ID);
            wp_die( __( 'Sorry, comments are closed for this item.', APP_TD ) );
    } elseif ( 'trash' == $status ) {
            do_action('comment_on_trash', $comment_post_ID);
            exit;
    } elseif ( !$status_obj->public && !$status_obj->private ) {
            do_action('comment_on_draft', $comment_post_ID);
            exit;
    } elseif ( post_password_required($comment_post_ID) ) {
            do_action('comment_on_password_protected', $comment_post_ID);
            exit;
    } else {
            do_action('pre_comment_on_post', $comment_post_ID);
    }

    $comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
    $comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
    $comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
    $comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;

    // If the user is logged in
    $user = wp_get_current_user();
    if ( $user->ID ) {
            if ( empty( $user->display_name ) )
            $user->display_name=$user->user_login;
            $comment_author       = $wpdb->escape($user->display_name);
            $comment_author_email = $wpdb->escape($user->user_email);
            $comment_author_url   = $wpdb->escape($user->user_url);
            if ( current_user_can('unfiltered_html') ) {
                    if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
                            kses_remove_filters(); // start with a clean slate
                            kses_init_filters(); // set up the filters
                    }
            }
    } else {
            if ( get_option('comment_registration') || 'private' == $status )
                    wp_die( __( 'Sorry, you must be logged in to post a comment.', APP_TD ) );
    }

    $comment_type = '';

    if ( get_option('require_name_email') && !$user->ID ) {
            if ( 6 > strlen($comment_author_email) || '' == $comment_author )
                    wp_die( __( 'Error: please fill the required fields (name, email).', APP_TD ) );
            elseif ( !is_email($comment_author_email))
                    wp_die( __( 'Error: please enter a valid email address.', APP_TD ) );
    }

    if ( '' == $comment_content )
            wp_die( __( 'Error: please type a comment.', APP_TD ) );

    $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

    $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

	// create the new comment in the db and get the comment id
    $comment_id = wp_new_comment( $commentdata );

	// go back and get the full comment so we can return it via ajax
    $comment = get_comment($comment_id);
	
    if ( !$user->ID ) {
            $comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
            setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
            setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
            setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
    }

    $results['success'] = true;
    
    $GLOBALS['comment'] = $comment;
	
	$results['comment'] = '';
	$results['comment'] .= '<li>';
	$results['comment'] .= get_avatar($comment, 32 );
	$results['comment'] .= '<p>' . get_comment_text() . '</p>';
	$results['comment'] .= '<p class="comment-meta">';
	$results['comment'] .= '<span class="author"></span>' . __( 'Posted by', APP_TD ) . ' ' . get_comment_author_link() . '<span class="date-wrap"><span class="date"></span>' . get_comment_time(get_option('date_format')) . '<span class="time"></span>' . get_comment_time(get_option('time_format')) . '</span>';
	$results['comment'] .= '</p>';
	$results['comment'] .= '</li>';

	// get the comment count so we can update via ajax
	$comment_count = $wpdb->get_var( $wpdb->prepare( "SELECT comment_count FROM $wpdb->posts WHERE post_status IN ('publish', 'unreliable') AND ID = %d", $post->ID ) );
	$results['count'] = $comment_count;    

	echo json_encode($results);
	die;

}


// mini comments callback display
function clpr_mini_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>

    <li>
        <?php echo get_avatar($comment, 32 ); ?>

        <?php comment_text(); ?>

        <p class="comment-meta">
		
            <span class="author"></span><?php _e( 'Posted by', APP_TD ); ?> <?php comment_author_link(); ?> <span class="date-wrap"><span class="date"></span><?php comment_time(get_option('date_format')); ?><span class="time"></span><?php comment_time(get_option('time_format')); ?></span>
			
        </p>
		
	

<?php
}
?>