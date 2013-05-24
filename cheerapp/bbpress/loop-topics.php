<?php

/**
 * Topics Loop
 */

?>

	<?php do_action( 'bbp_template_before_topics_loop' ); ?>
	
	<?php if( bbp_is_favorites() || bbp_is_subscriptions() || bbp_is_topics_created() ) : ?>
	
		<table class="topics" cellpadding="0" cellspacing="0">
			
			<tbody>
	
				<?php while ( bbp_topics() ) : bbp_the_topic(); ?>
	
					<?php bbp_get_template_part( 'bbpress/loop', 'single-topic' ); ?>
	
				<?php endwhile; ?>
	
			</tbody>
			
		</table>
	
	<?php else : ?>

		<table class="topics" id="bbp-forum-<?php bbp_topic_id(); ?>" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<?php if( function_exists( 'royal_bbp_topic_status_tag' ) ) : ?>
						<th class="topic-status-info"><?php _e( 'Status', 'cheerapp' ); ?></th>
					<?php endif; ?>
					<th class="topic-title"><?php _e( 'Topic', 'cheerapp' ); ?></th>
					<th class="topic-reply-count"><?php bbp_show_lead_topic() ? _e( 'Replies', 'cheerapp' ) : _e( 'Posts', 'cheerapp' ); ?></th>
					<th class="topic-freshness"><?php bbp_show_lead_topic() ? _e( 'Last reply', 'cheerapp' ) : _e( 'Last post', 'cheerapp' ); ?></th>
				</tr>
			</thead>
	
			<tbody>
	
				<?php while ( bbp_topics() ) : bbp_the_topic(); ?>
	
					<?php bbp_get_template_part( 'bbpress/loop', 'single-topic' ); ?>
	
				<?php endwhile; ?>
	
			</tbody>
	
		</table><!-- #bbp-forum-<?php bbp_topic_id(); ?> -->
	
	<?php endif; ?>

	<?php do_action( 'bbp_template_after_topics_loop' ); ?>
