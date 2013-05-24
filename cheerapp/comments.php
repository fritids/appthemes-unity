<?php
/**
 * Comments template for blog posts
 */
?>

<?php if( post_password_required() ) : ?>
	<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'cheerapp' ); ?></p>
<?php return; endif; ?>

<?php if( comments_open() ) : ?>
		
	<?php
	$fields =  array(
		'author' => '<div class="comment-form-author control-group">' . '<label class="control-label" for="author">' . __( 'Name', 'cheerapp' ) . ' *</label>' .
					'<div class="controls"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" required="required" /></div></div>',
		'email'  => '<div class="comment-form-email control-group">' . '<label class="control-label" for="email">' . __( 'Email', 'cheerapp' ) . ' *</label>' .
					'<div class="controls"><input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" required="required" /></div></div>',
		'url'    => '<div class="comment-form-url control-group">' . '<label class="control-label" for="url">' . __( 'Website', 'cheerapp' ) . '</label>' .
					'<div class="controls"><input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div></div>',
	);

	$args = array(
		'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field'        => '<div class="comment-form-comment textarea control-group"><label class="control-label" for="comment">' . __( 'Comment', 'cheerapp' ) . ' *</label><div class="controls"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required="required"></textarea></div></div>',
		'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'cheerapp' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>',
		'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'cheerapp' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>',
		'comment_notes_before' => '',
		'comment_notes_after'  => '<p class="field-description">' . __( 'You may use these HTML tags and attributes', 'cheerapp' ) . ': <code>' . allowed_tags() . '</code></p>',
		'id_form'              => 'comment-form',
		'id_submit'            => 'submit',
		'title_reply'          => __( 'Leave a Reply', 'cheerapp' ),
		'title_reply_to'       => __( 'Leave a Reply to %s', 'cheerapp' ),
		'cancel_reply_link'    => __( 'Cancel reply', 'cheerapp' ),
		'label_submit'         => __( 'Submit', 'cheerapp' ),
	);

	comment_form($args); ?>

<?php endif; ?>

<?php if( have_comments() ) : ?>

	<hr />
	
	<h3 class="comments-title">
	<?php printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number() ),
	number_format_i18n( get_comments_number() ), '&ldquo;' . get_the_title() . '&rdquo;' ); ?>
	</h3>
	
	<ol class="comment-list">
		<?php wp_list_comments( array( 'callback' => 'cheerapp_blog_comment', 'max_depth' => 2 ) ); ?>
	</ol>
	
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // If comments pagination is needed ?>
	
		<hr />
	
		<div class="pagination comments-pagination clearfix">
			<div class="nav-previous alignleft"><?php previous_comments_link( __( '&laquo; Older Comments', 'cheerapp' ) ); ?></div>
			<div class="nav-next alignright"><?php next_comments_link( __( 'Newer Comments &raquo;', 'cheerapp' ) ); ?></div>
		</div><!-- .navigation -->
		
	<?php endif; // check for comment navigation ?>

<?php else: ?>

	<h3><?php _e( 'No comments', 'cheerapp' ); ?></h3>
	
<?php endif; ?>