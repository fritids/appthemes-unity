<?php

/**
 * Topics Loop - Single
 */

?>

	<?php
	/* Set some variables */
	$reply_count			=	bbp_show_lead_topic() ? bbp_get_topic_reply_count() : bbp_get_topic_post_count();
	$thread_author_link		=	bbp_get_topic_author_link( array( 'type' => 'name', 'sep' => '' ) );
	$thread_time_ago		=	function_exists( 'royal_bbp_get_topic_time_ago' ) ? royal_bbp_get_topic_time_ago() : bbp_get_topic_time_ago();
	?>
	
	<?php
	// If it's a user profile page
	if( bbp_is_favorites() || bbp_is_subscriptions() || bbp_is_topics_created() ) :
	?>
	
		<tr id="topic-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>
			
			<td class="topic-title">
			
				<?php if( function_exists( 'royal_bbp_sticky_icon' ) ) royal_bbp_sticky_icon( array( 'info_only' => true, 'icon_only' => true ) ); ?>
			
				<?php if( function_exists( 'royal_bbp_closed_icon' ) ) royal_bbp_closed_icon( array( 'info_only' => true, 'icon_only' => true ) ); ?>
	
				<?php do_action( 'bbp_theme_before_topic_title' ); ?>
	
				<a href="<?php bbp_topic_permalink(); ?>" title="<?php bbp_topic_title(); ?>"><?php bbp_topic_title(); ?></a>
	
				<?php do_action( 'bbp_theme_after_topic_title' ); ?>
				
				<?php if ( !bbp_is_single_forum() || ( bbp_get_topic_forum_id() != bbp_get_forum_id() ) ) : ?>
	
					<?php do_action( 'bbp_theme_before_topic_meta' ); ?>
		
					<p class="topic-meta">
		
						<?php do_action( 'bbp_theme_before_topic_started_in' ); ?>
		
						<span class="bbp-topic-started-in"><?php printf( __( '<small>in</small> <a href="%1$s">%2$s</a>', 'cheerapp' ), bbp_get_forum_permalink( bbp_get_topic_forum_id() ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?></span>
		
						<?php do_action( 'bbp_theme_after_topic_started_in' ); ?>
		
					</p>
		
					<?php do_action( 'bbp_theme_after_topic_meta' ); ?>
					
				<?php endif; ?>
	
			</td>
			
			<?php if ( bbp_is_user_home() ) : ?>
	
				<?php if ( bbp_is_favorites() ) : ?>
	
					<td class="topic-action">
	
						<?php do_action( 'bbp_theme_before_topic_favorites_action' ); ?>
	
						<?php bbp_user_favorites_link( array( 'mid' => '+', 'post' => '' ), array( 'pre' => '', 'mid' => '&times;', 'post' => '' ) ); ?>
	
						<?php do_action( 'bbp_theme_after_topic_favorites_action' ); ?>
	
					</td>
	
				<?php elseif ( bbp_is_subscriptions() ) : ?>
	
					<td class="topic-action">
	
						<?php do_action( 'bbp_theme_before_topic_subscription_action' ); ?>
	
						<?php bbp_user_subscribe_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>
	
						<?php do_action( 'bbp_theme_after_topic_subscription_action' ); ?>
	
					</td>
	
				<?php endif; ?>
	
			<?php endif; ?>
			
		</tr><!-- #topic-<?php bbp_topic_id(); ?> -->
	
	<?php
	// If it's not a user profile page, that is regular forum page
	else : ?>
	
		<tr id="topic-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>
			
			<?php if( function_exists( 'royal_bbp_topic_status_tag' ) ) : ?>
			
				<td class="topic-status-info">
					<?php royal_bbp_topic_status_tag(); ?>
				</td>
				
			<?php endif; ?>
	
			<td class="topic-title">
			
				<?php if( function_exists( 'royal_bbp_sticky_icon' ) ) royal_bbp_sticky_icon( array( 'info_only' => true, 'icon_only' => true ) ); ?>
			
				<?php if( function_exists( 'royal_bbp_closed_icon' ) ) royal_bbp_closed_icon( array( 'info_only' => true, 'icon_only' => true ) ); ?>
	
				<?php do_action( 'bbp_theme_before_topic_title' ); ?>
	
				<a href="<?php bbp_topic_permalink(); ?>" title="<?php bbp_topic_title(); ?>"><?php bbp_topic_title(); ?></a>
	
				<?php do_action( 'bbp_theme_after_topic_title' ); ?>
	
				<?php do_action( 'bbp_theme_before_topic_meta' ); ?>
	
				<p class="topic-meta">
				
					<?php do_action( 'bbp_theme_before_topic_started_by' ); ?>

						<span class="topic-started-by"><?php printf( __( '<small>started by</small> %s', 'cheerapp' ), $thread_author_link ); ?></span>
		
					<?php do_action( 'bbp_theme_after_topic_started_by' ); ?>
				
					<?php if ( !bbp_is_single_forum() || ( bbp_get_topic_forum_id() != bbp_get_forum_id() ) ) : ?>
	
						<?php do_action( 'bbp_theme_before_topic_started_in' ); ?>
		
						<span class="bbp-topic-started-in"><?php printf( __( '<small>in</small> <a href="%1$s">%2$s</a>', 'cheerapp' ), bbp_get_forum_permalink( bbp_get_topic_forum_id() ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?></span>
		
						<?php do_action( 'bbp_theme_after_topic_started_in' ); ?>
						
					<?php endif; ?>
	
				</p>
	
				<?php do_action( 'bbp_theme_after_topic_meta' ); ?>
	
			</td>
	
			<td class="topic-reply-count"><?php bbp_show_lead_topic() ? bbp_topic_reply_count() : bbp_topic_post_count(); ?></td>
	
			<td class="topic-freshness">
	
				<?php do_action( 'bbp_theme_before_topic_freshness_link' ); ?>
	
				<?php royal_bbp_topic_freshness_link( '', 'author', 'tooltip', 'time' ); ?>
	
				<?php do_action( 'bbp_theme_after_topic_freshness_link' ); ?>
				
			</td>
	
			<?php if ( bbp_is_user_home() ) : ?>
	
				<?php if ( bbp_is_favorites() ) : ?>
	
					<td class="topic-action">
	
						<?php do_action( 'bbp_theme_before_topic_favorites_action' ); ?>
	
						<?php bbp_user_favorites_link( array( 'mid' => '+', 'post' => '' ), array( 'pre' => '', 'mid' => '&times;', 'post' => '' ) ); ?>
	
						<?php do_action( 'bbp_theme_after_topic_favorites_action' ); ?>
	
					</td>
	
				<?php elseif ( bbp_is_subscriptions() ) : ?>
	
					<td class="topic-action">
	
						<?php do_action( 'bbp_theme_before_topic_subscription_action' ); ?>
	
						<?php bbp_user_subscribe_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>
	
						<?php do_action( 'bbp_theme_after_topic_subscription_action' ); ?>
	
					</td>
	
				<?php endif; ?>
	
			<?php endif; ?>
	
		</tr><!-- #topic-<?php bbp_topic_id(); ?> -->
		
	<?php endif; ?>