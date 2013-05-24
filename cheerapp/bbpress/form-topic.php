<?php

/**
 * New/Edit Topic
 */

?>

	<?php if ( bbp_current_user_can_access_create_topic_form() ) : ?>

		<div id="new-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-form">

			<form id="new-post" name="new-post" method="post" action="" class="form-horizontal">

				<?php do_action( 'bbp_theme_before_topic_form' ); ?>
				
				<legend>
					<h3>
					
					<?php
						if ( bbp_is_topic_edit() )
							printf( __( 'Now Editing &ldquo;%s&rdquo;', 'cheerapp' ), bbp_get_topic_title() );
						else
							bbp_is_single_forum() ? printf( __( 'Create New Thread in &ldquo;%s&rdquo;', 'cheerapp' ), bbp_get_forum_title() ) : _e( 'Create New Thread', 'cheerapp' );
					?>
					
					</h3>
				</legend>

				<?php do_action( 'bbp_theme_before_topic_form_notices' ); ?>

				<?php if ( !bbp_is_topic_edit() && bbp_is_forum_closed() ) : ?>

					<div class="bbp-template-notice">
						<p><?php _e( 'This forum is marked as closed to new topics, however your posting capabilities still allow you to do so.', 'cheerapp' ); ?></p>
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

					<?php do_action( 'bbp_theme_before_topic_form_title' ); ?>

					<div class="control-group">
						<label for="bbp_topic_title" class="control-label"><?php _e( 'Title', 'cheerapp' ); ?></label>
						<div class="controls">
							<input type="text" id="bbp_topic_title" value="<?php bbp_form_topic_title(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_title" maxlength="<?php bbp_title_max_length(); ?>" />
							<span class="field-description">
								<?php printf( __( 'Maximum Length: %d', 'cheerapp' ), bbp_get_title_max_length() ); ?>
							</span>
						</div>
					</div>
					
					<?php do_action( 'bbp_theme_after_topic_form_title' ); ?>

					<?php do_action( 'bbp_theme_before_topic_form_content' ); ?>

					<div class="control-group">
						<label for="bbp_topic_content" class="control-label"><?php _e( 'Description', 'cheerapp' ); ?></label>
						<div class="controls">
							<textarea id="bbp_topic_content" tabindex="<?php bbp_tab_index(); ?>" name="bbp_topic_content" cols="60" rows="6"><?php bbp_form_topic_content(); ?></textarea>
							<?php if ( !current_user_can( 'unfiltered_html' ) ) : ?>
								<span class="form-allowed-tags field-description">
									<?php _e( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:','cheerapp' ); ?>
									<code><?php bbp_allowed_tags(); ?></code>
								</span>
							<?php endif; ?>
						</div>
					</div>

					<?php do_action( 'bbp_theme_after_topic_form_content' ); ?>

					<?php do_action( 'bbp_theme_before_topic_form_tags' ); ?>

					<div class="control-group">
						<label for="bbp_topic_tags" class="control-label"><?php _e( 'Tags', 'cheerapp' ); ?></label>
						<div class="controls">
							<input type="text" value="<?php bbp_form_topic_tags(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" <?php disabled( bbp_is_topic_spam() ); ?> />
						</div>
					</div>

					<?php do_action( 'bbp_theme_after_topic_form_tags' ); ?>

					<?php if ( !bbp_is_single_forum() ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_forum' ); ?>

						<div class="control-group">
							<label for="bbp_forum_id" class="control-label"><?php _e( 'Forum', 'cheerapp' ); ?></label>
							<div class="controls">
								<?php bbp_dropdown( array( 'selected' => bbp_get_form_topic_forum() ) ); ?>
							</div>
						</div>

						<?php do_action( 'bbp_theme_after_topic_form_forum' ); ?>

					<?php endif; ?>

					<?php if ( current_user_can( 'moderate' ) ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_type' ); ?>

						<div class="control-group">
							<label for="bbp_stick_topic" class="control-label"><?php _e( 'Thread Type', 'cheerapp' ); ?></label>
							<div class="controls">
								<?php bbp_topic_type_select(); ?>
							</div>
						</div>

						<?php do_action( 'bbp_theme_after_topic_form_type' ); ?>

					<?php endif; ?>

					<?php if ( bbp_is_subscriptions_active() && !bbp_is_anonymous() && ( !bbp_is_topic_edit() || ( bbp_is_topic_edit() && !bbp_is_topic_anonymous() ) ) ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_subscriptions' ); ?>

						<div class="control-group">
							<label class="control-label"><?php _e( 'Subscription', 'cheerapp' ); ?></label>
							<div class="controls">
								<label for="bbp_topic_subscription" class="checkbox block">
									<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe" <?php bbp_form_topic_subscribed(); ?> tabindex="<?php bbp_tab_index(); ?>" />
									<?php if ( bbp_is_topic_edit() && ( $post->post_author != bbp_get_current_user_id() ) ) : ?>
										<?php _e( 'Notify the author of follow-up replies via email', 'cheerapp' ); ?>
									<?php else : ?>
										<?php _e( 'Notify me of follow-up replies via email', 'cheerapp' ); ?>
									<?php endif; ?>
								</label>
							</div>
						</div>

						<?php do_action( 'bbp_theme_after_topic_form_subscriptions' ); ?>

					<?php endif; ?>
					
					<?php if ( bbp_allow_revisions() && bbp_is_topic_edit() ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_revisions' ); ?>

						<div class="control-group">
							<label class="control-label"><?php _e( 'Revision', 'cheerapp' ); ?></label>
							<div class="controls">
								<label for="bbp_log_topic_edit" class="checkbox block">
									<input name="bbp_log_topic_edit" id="bbp_log_topic_edit" type="checkbox" value="1" <?php bbp_form_topic_log_edit(); ?> tabindex="<?php bbp_tab_index(); ?>" />
									<?php _e( 'Keep a log of this edit', 'cheerapp' ); ?>
								</label>
								
								<input type="text" value="<?php bbp_form_topic_edit_reason(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_edit_reason" id="bbp_topic_edit_reason" placeholder="<?php printf( __( 'Optional reason for editing', 'cheerapp' ), bbp_get_current_user_name() ); ?>" />
							</div>
						</div>

						<?php do_action( 'bbp_theme_after_topic_form_revisions' ); ?>

					<?php endif; ?>
					
					<?php do_action( 'bbp_theme_before_topic_form_submit_wrapper' ); ?>

					<div class="bbp-submit-wrapper form-actions">

						<?php do_action( 'bbp_theme_before_topic_form_submit_button' ); ?>

						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_topic_submit" name="bbp_topic_submit" class="button submit"><?php _e( 'Submit', 'cheerapp' ); ?></button>

						<?php do_action( 'bbp_theme_after_topic_form_submit_button' ); ?>

					</div>

					<?php do_action( 'bbp_theme_after_topic_form_submit_wrapper' ); ?>

					<?php bbp_topic_form_fields(); ?>

				</fieldset>

				<?php do_action( 'bbp_theme_after_topic_form' ); ?>

			</form>
		</div>

	<?php elseif ( bbp_is_forum_closed() ) : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="bbp-template-notice">
				<p><?php printf( __( 'The forum &#8216;%s&#8217; is closed to new threads and replies.', 'cheerapp' ), bbp_get_forum_title() ); ?></p>
			</div>
		</div>

	<?php else : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="bbp-template-notice">
				<p><?php is_user_logged_in() ? _e( 'You cannot create new threads at this time.', 'cheerapp' ) : _e( 'You must be logged in to create new threads.', 'cheerapp' ); ?></p>
			</div>
		</div>

	<?php endif; ?>
