<?php

/**
 * Anonymous User
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php if ( bbp_is_anonymous() || ( bbp_is_topic_edit() && bbp_is_topic_anonymous() ) || ( bbp_is_reply_edit() && bbp_is_reply_anonymous() ) ) : ?>

	<?php do_action( 'bbp_theme_before_anonymous_form' ); ?>

	<fieldset class="bbp-form">
	
		<div class="row">
		
			<h4><?php ( bbp_is_topic_edit() || bbp_is_reply_edit() ) ? _e( 'Author Information', 'cheerapp' ) : _e( 'Your information:', 'cheerapp' ); ?></h4>
	
			<?php do_action( 'bbp_theme_anonymous_form_extras_top' ); ?>
	
			<p class="span4">
				<label for="bbp_anonymous_author"><?php _e( 'Name (required):', 'cheerapp' ); ?></label>
				<input type="text" id="bbp_anonymous_author"  value="<?php bbp_is_topic_edit() ? bbp_topic_author()       : bbp_is_reply_edit() ? bbp_reply_author()       : bbp_current_anonymous_user_data( 'name' );    ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_name" />
			</p>
	
			<p class="span4">
				<label for="bbp_anonymous_email"><?php _e( 'Mail (will not be published) (required):', 'cheerapp' ); ?></label>
				<input type="text" id="bbp_anonymous_email"   value="<?php bbp_is_topic_edit() ? bbp_topic_author_email() : bbp_is_reply_edit() ? bbp_reply_author_email() : bbp_current_anonymous_user_data( 'email' );   ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_email" />
			</p>
	
			<p class="span4">
				<label for="bbp_anonymous_website"><?php _e( 'Website:', 'cheerapp' ); ?></label>
				<input type="text" id="bbp_anonymous_website" value="<?php bbp_is_topic_edit() ? bbp_topic_author_url()   : bbp_is_reply_edit() ? bbp_reply_author_url()   : bbp_current_anonymous_user_data( 'website' ); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_anonymous_website" />
			</p>
	
			<?php do_action( 'bbp_theme_anonymous_form_extras_bottom' ); ?>
		
		</div>

	</fieldset>

	<?php do_action( 'bbp_theme_after_anonymous_form' ); ?>

<?php endif; ?>
