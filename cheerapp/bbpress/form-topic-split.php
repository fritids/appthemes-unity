<?php

/**
 * Split Topic
 */

?>

	<?php if ( is_user_logged_in() && current_user_can( 'edit_topic', bbp_get_topic_id() ) ) : ?>

		<div id="split-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-split">

			<form id="split_topic" name="split_topic" method="post" action="" class="form-horizontal">
			
				<legend>
					<h3><?php printf( __( 'Split thread "%s"', 'cheerapp' ), bbp_get_topic_title() ); ?></h3>
				</legend>
				
				<div class="bbp-template-notice info">
					<p><?php _e( 'When you split a thread, you are slicing it in half starting with the reply you just selected. Choose to use that reply as a new thread with a new title, or merge those replies into an existing thread.', 'cheerapp' ); ?></p>
				</div>

				<div class="bbp-template-notice">
					<p><?php _e( 'If you use the existing thread option, replies within both threads will be merged chronologically. The order of the merged replies is based on the time and date they were posted.', 'cheerapp' ); ?></p>
				</div>
				
				<?php do_action( 'bbp_template_notices' ); ?>


				<legend><h3><?php _e( 'Split Method', 'cheerapp' ); ?></h3></legend>

				<fieldset class="bbp-form">

					<div class="control-group">
						<div class="controls">
							<label for="bbp_topic_split_option_reply">
								<input name="bbp_topic_split_option" id="bbp_topic_split_option_reply" type="radio" checked="checked" value="reply" tabindex="<?php bbp_tab_index(); ?>" />
								<?php printf( __( 'New thread in <strong>%s</strong> titled:', 'cheerapp' ), bbp_get_forum_title( bbp_get_topic_forum_id( bbp_get_topic_id() ) ) ); ?>
							</label>
							<input type="text" id="bbp_topic_split_destination_title" value="<?php printf( __( 'Split: %s', 'cheerapp' ), bbp_get_topic_title() ); ?>" tabindex="<?php bbp_tab_index(); ?>" size="35" name="bbp_topic_split_destination_title" />
						</div>
					</div>

					<?php if ( bbp_has_topics( array( 'show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ), 'post__not_in' => array( bbp_get_topic_id() ) ) ) ) : ?>

						<div class="control-group">
							<div class="controls">
								<label for="bbp_topic_split_option_existing">
									<input name="bbp_topic_split_option" id="bbp_topic_split_option_existing" type="radio" value="existing" tabindex="<?php bbp_tab_index(); ?>" />
									<?php _e( 'Use an existing thread in this forum:', 'cheerapp' ); ?>
								</label>
								<?php
									bbp_dropdown( array(
										'post_type'   => bbp_get_topic_post_type(),
										'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ),
										'selected'    => -1,
										'exclude'     => bbp_get_topic_id(),
										'select_id'   => 'bbp_destination_topic',
										'none_found'  => __( 'No other threads found!', 'cheerapp' )
									) );
								?>
							</div>
						</div>

					<?php endif; ?>
					
				</fieldset>
					
					
				<legend><h3><?php _e( 'Topic Extras', 'cheerapp' ); ?></h3></legend>
				
				<fieldset class="bbp-form">

					<div class="control-group">
					
						<div class="controls">

							<?php if ( bbp_is_subscriptions_active() ) : ?>
	
								<label for="bbp_topic_subscribers">
									<input name="bbp_topic_subscribers" id="bbp_topic_subscribers" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
									<?php _e( 'Copy subscribers to the new thread', 'cheerapp' ); ?>
								</label>
	
							<?php endif; ?>
	
							<label for="bbp_topic_favoriters">
								<input name="bbp_topic_favoriters" id="bbp_topic_favoriters" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
								<?php _e( 'Copy favorites to the new thread', 'cheerapp' ); ?>
							</label>
	
							<label for="bbp_topic_tags">
								<input name="bbp_topic_tags" id="bbp_topic_tags" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
								<?php _e( 'Copy thread tags to the new thread', 'cheerapp' ); ?>
							</label>
							
						</div>

					</div>

					<div class="bbp-template-notice error">
						<p><?php _e( '<strong>WARNING:</strong> This process cannot be undone.', 'cheerapp' ); ?></p>
					</div>

					<div class="bbp-submit-wrapper form-actions">
						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" class="button submit"><?php _e( 'Submit', 'cheerapp' ); ?></button>
					</div>

					<?php bbp_split_topic_form_fields(); ?>

				</fieldset>
			</form>
		</div>

	<?php else : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="entry-content"><?php is_user_logged_in() ? _e( 'You do not have the permissions to edit this thread!', 'cheerapp' ) : _e( 'You cannot edit this thread.', 'cheerapp' ); ?></div>
		</div>

	<?php endif; ?>
