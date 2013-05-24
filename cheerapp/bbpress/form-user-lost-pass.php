<?php

/**
 * User Lost Password Form
 */

?>

	<form method="post" action="<?php bbp_wp_login_action( array( 'action' => 'lostpassword', 'context' => 'login_post' ) ); ?>" class="bbp-login-form">
		<fieldset class="bbp-form">

			<div class="bbp-username">
				<p>
					<label for="user_login" class="hide"><?php _e( 'Username or Email', 'cheerapp' ); ?>: </label>
					<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="<?php bbp_tab_index(); ?>" />
				</p>
			</div>

			<div class="bbp-submit-wrapper clearfix">

				<?php do_action( 'login_form', 'resetpass' ); ?>

				<input type="submit" name="user-submit" value="<?php _e( 'Reset password', 'cheerapp' ); ?>" class="user-submit button submit button-submit" tabindex="<?php bbp_tab_index(); ?>" />

				<?php bbp_user_lost_pass_fields(); ?>

			</div>
		</fieldset>
	</form>
