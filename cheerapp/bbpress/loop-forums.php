<?php

/**
 * Forums Loop
 */

?>

	<?php do_action( 'bbp_template_before_forums_loop' ); ?>

	<table class="child-forums" cellpadding="0" cellspacing="0">

		<thead>
			<tr>
				<th class="forum-info"><?php _e( 'Forum', 'cheerapp' ); ?></th>
				<th class="forum-topic-count"><?php _e( 'Threads', 'cheerapp' ); ?></th>
				<th class="forum-reply-count"><?php bbp_show_lead_topic() ? _e( 'Replies', 'cheerapp' ) : _e( 'Posts', 'bbpress' ); ?></th>
				<th class="forum-freshness hidden-phone"><?php _e( 'Last post', 'cheerapp' ); ?></th>
			</tr>
		</thead>

		<tbody>

			<?php while ( bbp_forums() ) : bbp_the_forum(); ?>

				<?php bbp_get_template_part( 'bbpress/loop', 'single-forum' ); ?>

			<?php endwhile; ?>

		</tbody>

	</table>

	<?php do_action( 'bbp_template_after_forums_loop' ); ?>
