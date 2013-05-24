<?php

/**
 * Replies Loop - Single Reply
 */

?>

<div class="bbp-post clearfix" id="post-<?php bbp_reply_id(); ?>">
	
	<div class="bbp-post-author-avatar">
	
		<?php bbp_reply_author_link( array( 'sep' => '', 'size' => 80, 'type' => 'avatar' ) ); ?>
		
		<?php if ( is_super_admin() ) : ?>
			<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>
		
			<div class="bbp-reply-ip visible-desktop"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>
			
			<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>
		<?php endif; ?>
		
	</div><!-- end .bbp-post-author-avatar -->
	
	<div class="bbp-post-content">
	
		<div class="bbp-post-info clearfix">
	
			<div class="bbp-post-author">
				<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>
			
				<?php bbp_reply_author_link( array( 'sep' => '', 'type' => 'name' ) ); ?>
				
				<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>
			</div>
			
			<div class="bbp-post-date">
				<?php printf( __( '%1$s <small>at</small> %2$s', 'cheerapp' ), get_the_date(), esc_attr( get_the_time() ) ); ?>
				&nbsp;/&nbsp;
				<a href="<?php bbp_reply_url(); ?>" title="<?php _e( 'Permalink', 'cheerapp' ); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>
			</div>
		
		</div><!-- end .bbp-post-info -->
	
		<?php do_action( 'bbp_theme_before_reply_content' ); ?>

		<?php bbp_reply_content(); ?>

		<?php do_action( 'bbp_theme_after_reply_content' ); ?>
		
		<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>

		<?php
		if ( bbp_is_topic( $post->ID ) ) {
			$args = array(
				'before'		=>	'',
				'after'			=>	'',
				'sep'			=>	''
			);
			$args['links'] = array(
				'edit'	=>	royal_bbp_get_topic_edit_link ( $args ),
				'close'	=>	'',
				'stick'	=>	'',
				'merge'	=>	royal_bbp_get_topic_merge_link( $args ),
				'trash'	=>	royal_bbp_get_topic_trash_link( $args ),
				'spam'	=>	royal_bbp_get_topic_spam_link ( $args )
			);
			$args['before'] = '<div class="forum-post-admin-links clearfix">';
			$args['after'] = '</div>';
			bbp_topic_admin_links( $args );
		}
		else {
			$args = array(
				'before'		=>	'',
				'after'			=>	'',
				'sep'			=>	''
			);
			$args['links'] = array(
				'edit'	=>	royal_bbp_get_reply_edit_link ( $args ),
				'split'	=>	royal_bbp_get_topic_split_link( $args ),
				'trash'	=>	royal_bbp_get_reply_trash_link( $args ),
				'spam'	=>	royal_bbp_get_reply_spam_link ( $args )
			);
			$args['before'] = '<div class="forum-post-admin-links clearfix">';
			$args['after'] = '</div>';
			bbp_reply_admin_links( $args );
		}
		?>
		
		<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
		
	</div><!-- end .bbp-post-content -->
									
</div><!-- end #post-<?php bbp_reply_id(); ?> -->