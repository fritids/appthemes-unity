<?php

/**
 * Merge Topic
 */

?>

	<?php if ( is_user_logged_in() && current_user_can( 'edit_topic', bbp_get_topic_id() ) ) : ?>

		<div id="merge-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-merge">

			<form id="merge_topic" name="merge_topic" method="post" action="" class="form-horizontal">
			
				<legend><h3><?php printf( __( 'Merge topic "%s"', 'cheerapp' ), bbp_get_topic_title() ); ?></h3></legend>
				
				<div class="bbp-template-notice info">
					<p><?php _e( 'Select the topic to merge this one into. The destination topic will remain the lead topic, and this one will change into a reply.', 'cheerapp' ); ?></p>
				</div>
				
				<div class="bbp-template-notice info">
					<p><?php _e( 'To keep this topic as the lead, go to the other topic and use the merge tool from there instead.', 'cheerapp' ); ?></p>
				</div>

				<div class="bbp-template-notice">
					<p><?php _e( 'All replies within both topics will be merged chronologically. The order of the merged replies is based on the time and date they were posted. If the destination topic was created after this one, it\'s post date will be updated to second earlier than this one.', 'cheerapp' ); ?></p>
				</div>
				
				<?php do_action( 'bbp_template_notices' ); ?>

				<legend><h3><?php _e( 'Destination', 'cheerapp' ); ?></h3></legend>

				<fieldset class="bbp-form">
							
					<div class="control-group">
						<?php if ( bbp_has_topics( array( 'show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ), 'post__not_in' => array( bbp_get_topic_id() ) ) ) ) : ?>

							<label class="control-label" for="bbp_destination_topic"><?php _e( 'Merge with:', 'cheerapp' ); ?></label>
							<div class="controls">
								<?php
									bbp_dropdown( array(
										'post_type'   => bbp_get_topic_post_type(),
										'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ),
										'selected'    => -1,
										'exclude'     => bbp_get_topic_id(),
										'select_id'   => 'bbp_destination_topic',
										'none_found'  => __( 'No topics were found to which the topic could be merged to!', 'cheerapp' )
									) );
								?>
							</div>

						<?php else : ?>
							
							<div class="controls">
								<label><?php _e( 'There are no other topics in this forum to merge with.', 'cheerapp' ); ?></label>
							</div>
							
						<?php endif; ?>
					</div>
					
				</fieldset>

				
				<legend><h3><?php _e( 'Topic Extras', 'cheerapp' ); ?></h3></legend>
				
				<fieldset class="bbp-form">

					<div class="control-group">
						<div class="controls">
							<?php if ( bbp_is_subscriptions_active() ) : ?>
								
								<label for="bbp_topic_subscribers">
									<input name="bbp_topic_subscribers" id="bbp_topic_subscribers" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
									<?php _e( 'Merge topic subscribers', 'cheerapp' ); ?>
								</label>
	
							<?php endif; ?>
							
							<label for="bbp_topic_favoriters">
								<input name="bbp_topic_favoriters" id="bbp_topic_favoriters" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
								<?php _e( 'Merge topic favoriters', 'cheerapp' ); ?>
							</label>
							
							<label for="bbp_topic_tags">
								<input name="bbp_topic_tags" id="bbp_topic_tags" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
								<?php _e( 'Merge topic tags', 'cheerapp' ); ?>
							</label>
						</div>
					</div>

					<div class="bbp-template-notice error">
						<p><?php _e( '<strong>WARNING:</strong> This process cannot be undone.', 'cheerapp' ); ?></p>
					</div>

					<div class="bbp-submit-wrapper form-actions">
						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" class="button submit"><?php _e( 'Submit', 'cheerapp' ); ?></button>
					</div>

					<?php bbp_merge_topic_form_fields(); ?>

				</fieldset>
			</form>
		</div>

	<?php else : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="entry-content"><?php is_user_logged_in() ? _e( 'You do not have the permissions to edit this topic!', 'cheerapp' ) : _e( 'You cannot edit this topic.', 'cheerapp' ); ?></div>
		</div>

	<?php endif; ?>
