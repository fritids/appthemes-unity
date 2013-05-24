<?php

/**
 * New/Edit Reply
 */

?>

	<?php if ( bbp_current_user_can_access_create_reply_form() ) : ?>

		<div id="new-reply-<?php bbp_topic_id(); ?>" class="bbp-reply-form">

			<form id="new-post" name="new-post" method="post" action="" class="form-horizontal">

				<?php do_action( 'bbp_theme_before_reply_form' ); ?>
				
				<legend>
					<?php if( !bbp_is_reply_edit() ) : ?>
						<h3><?php printf( __( 'Reply to &lsquo;%s&rsquo;', 'cheerapp' ), bbp_get_topic_title() ); ?></h3>
					<?php else : ?>
						<h3><?php _e( 'Edit reply', 'cheerapp' ); ?></h3>
					<?php endif; ?>
				</legend>
				
				<?php do_action( 'bbp_theme_before_reply_form_notices' ); ?>

				<?php if ( !bbp_is_topic_open() && !bbp_is_reply_edit() ) : ?>

					<div class="bbp-template-notice">
						<p><?php _e( 'This thread is marked as closed to new replies, however your posting capabilities still allow you to do so.', 'cheerapp' ); ?></p>
					</div>

				<?php endif; ?>

				<?php if ( current_user_can( 'unfiltered_html' ) ) : ?>

					<div class="bbp-template-notice">
						<p><?php _e( 'Your account has the ability to post unrestricted HTML content.', 'cheerapp' ); ?></p>
					</div>

				<?php endif; ?>

				<?php do_action( 'bbp_template_notices' ); ?>
				
				<?php bbp_get_template_part( 'bbpress/form', 'anonymous' ); ?>

				<fieldset class="bbp-form">

					<?php do_action( 'bbp_theme_before_reply_form_content' ); ?>

					<div class="control-group">
						<label for="bbp_reply_content" class="control-label"><?php _e( 'Reply', 'cheerapp' ); ?></label>
						<div class="controls">
							<textarea id="bbp_reply_content" tabindex="<?php bbp_tab_index(); ?>" name="bbp_reply_content" rows="6"><?php bbp_form_reply_content(); ?></textarea>
							<?php if ( !current_user_can( 'unfiltered_html' ) ) : ?>
								<span class="form-allowed-tags field-description">
									<?php _e( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:','cheerapp' ); ?>
									<code><?php bbp_allowed_tags(); ?></code>
								</span>
							<?php endif; ?>
						</div>
					</div>

					<?php do_action( 'bbp_theme_after_reply_form_content' ); ?>

					<?php do_action( 'bbp_theme_before_reply_form_tags' ); ?>

					<div class="control-group">
						<label for="bbp_topic_tags" class="control-label"><?php _e( 'Tags', 'cheerapp' ); ?></label>
						<div class="controls">
							<input type="text" value="<?php bbp_form_topic_tags(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" <?php disabled( bbp_is_topic_spam() ); ?> />
						</div>
					</div>

					<?php do_action( 'bbp_theme_after_reply_form_tags' ); ?>

					<?php if ( bbp_is_subscriptions_active() && !bbp_is_anonymous() && ( !bbp_is_reply_edit() || ( bbp_is_reply_edit() && !bbp_is_reply_anonymous() ) ) ) : ?>

						<?php do_action( 'bbp_theme_before_reply_form_subscription' ); ?>

						<div class="control-group">
							<label class="control-label"><?php _e( 'Subscription', 'cheerapp' ); ?></label>
							<div class="controls">
								<label for="bbp_topic_subscription" class="checkbox block">
									<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe"<?php bbp_form_topic_subscribed(); ?> tabindex="<?php bbp_tab_index(); ?>" />
									<?php if ( bbp_is_reply_edit() && $post->post_author != bbp_get_current_user_id() ) : ?>
										<?php _e( 'Notify the author of follow-up replies via email', 'cheerapp' ); ?>
									<?php else : ?>
										<?php _e( 'Notify me of follow-up replies via email', 'cheerapp' ); ?>
									<?php endif; ?>
								</label>
							</div>
						</div>

						<?php do_action( 'bbp_theme_after_reply_form_subscription' ); ?>

					<?php endif; ?>

					<?php if ( bbp_allow_revisions() && bbp_is_reply_edit() ) : ?>

						<?php do_action( 'bbp_theme_before_reply_form_revisions' ); ?>

						<div class="control-group">
							<label class="control-label"><?php _e( 'Revision', 'cheerapp' ); ?></label>
							<div class="controls">
								<label for="bbp_log_reply_edit" class="checkbox block">
									<input name="bbp_log_reply_edit" id="bbp_log_reply_edit" type="checkbox" value="1" <?php bbp_form_reply_log_edit(); ?> tabindex="<?php bbp_tab_index(); ?>" />
									<?php _e( 'Keep a log of this edit', 'cheerapp' ); ?>
								</label>
								
								<input type="text" value="<?php bbp_form_reply_edit_reason(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_reply_edit_reason" id="bbp_reply_edit_reason" placeholder="<?php printf( __( 'Optional reason for editing', 'cheerapp' ), bbp_get_current_user_name() ); ?>" />
							</div>
						</div>

						<?php do_action( 'bbp_theme_after_reply_form_revisions' ); ?>

					<?php endif; ?>
					
					<?php do_action( 'bbp_theme_before_reply_form_submit_wrapper' ); ?>

					<div class="bbp-submit-wrapper form-actions">

						<?php do_action( 'bbp_theme_before_reply_form_submit_button' ); ?>

						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_reply_submit" name="bbp_reply_submit" class="button submit"><?php _e( 'Submit', 'cheerapp' ); ?></button>

						<?php do_action( 'bbp_theme_after_reply_form_submit_button' ); ?>

					</div>

					<?php do_action( 'bbp_theme_after_reply_form_submit_wrapper' ); ?>

					<?php bbp_reply_form_fields(); ?>

				</fieldset>

				<?php do_action( 'bbp_theme_after_reply_form' ); ?>

			</form>
			
		</div>

	<?php elseif ( bbp_is_topic_closed() ) : ?>

		<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
			<div class="bbp-template-notice">
				<p><?php printf( __( 'The thread &lsquo;%s&rsquo; is closed to new replies.', 'cheerapp' ), bbp_get_topic_title() ); ?></p>
			</div>
		</div>

	<?php elseif ( bbp_is_forum_closed( bbp_get_topic_forum_id() ) ) : ?>

		<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
			<div class="bbp-template-notice">
				<p><?php printf( __( 'The forum &lsquo;%s&rsquo; is closed to new topics and replies.', 'cheerapp' ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?></p>
			</div>
		</div>

	<?php else : ?>

		<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
			<div class="bbp-template-notice">
				<p><?php is_user_logged_in() ? _e( 'You cannot reply in this thread.', 'cheerapp' ) : _e( 'You must be logged in to reply in this thread.', 'cheerapp' ); ?></p>
			</div>
		</div>

	<?php endif; ?>