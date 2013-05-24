<?php

/**
 * bbPress User Profile Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form id="bbp-user-edit-form" action="<?php bbp_user_profile_edit_url( bbp_get_displayed_user_id() ); ?>" method="post">

	<h3 class="entry-title"><?php _e( 'Name', 'cheerapp' ) ?></h3>

	<?php do_action( 'bbp_user_edit_before' ); ?>

	<fieldset class="bbp-form row">

		<?php do_action( 'bbp_user_edit_before_name' ); ?>

		<p class="half">
			<label for="first_name"><?php _e( 'First Name', 'cheerapp' ) ?></label>
			<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'first_name' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<p class="half">
			<label for="last_name"><?php _e( 'Last Name', 'cheerapp' ) ?></label>
			<input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'last_name' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<p class="half">
			<label for="nickname"><?php _e( 'Nickname', 'cheerapp' ); ?></label>
			<input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'nickname' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<p class="half">
			<label for="display_name"><?php _e( 'Display name publicly as', 'cheerapp' ) ?></label>

			<?php bbp_edit_user_display_name(); ?>

		</p>

		<?php do_action( 'bbp_user_edit_after_name' ); ?>

	</fieldset>

	<h3 class="entry-title"><?php _e( 'Contact Info', 'cheerapp' ) ?></h3>

	<fieldset class="bbp-form row">

		<?php do_action( 'bbp_user_edit_before_contact' ); ?>

		<p class="half">
			<label for="url"><?php _e( 'Website', 'cheerapp' ) ?></label>
			<input type="text" name="url" id="url" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'user_url' ) ); ?>" class="regular-text code" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<?php foreach ( bbp_edit_user_contact_methods() as $name => $desc ) : ?>

			<p class="half">
				<label for="<?php echo $name; ?>"><?php echo apply_filters( 'user_'.$name.'_label', $desc ); ?></label>
				<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'name' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
			</p>

		<?php endforeach; ?>

		<?php do_action( 'bbp_user_edit_after_contact' ); ?>

	</fieldset>

	<h3 class="entry-title"><?php bbp_is_user_home() ? _e( 'About Yourself', 'cheerapp' ) : _e( 'About the user', 'cheerapp' ); ?></h3>

	<fieldset class="bbp-form">

		<?php do_action( 'bbp_user_edit_before_about' ); ?>

		<div>
			<label for="description"><?php _e( 'Biographical Info', 'cheerapp' ); ?></label>
			<textarea name="description" id="description" rows="5" cols="30" tabindex="<?php bbp_tab_index(); ?>"><?php echo esc_attr( bbp_get_displayed_user_field( 'description' ) ); ?></textarea>
			<span class="field-description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'cheerapp' ); ?></span>
		</div>

		<?php do_action( 'bbp_user_edit_after_about' ); ?>

	</fieldset>

	<h3 class="entry-title"><?php _e( 'Account', 'cheerapp' ); ?></h3>

	<fieldset class="bbp-form row">

		<?php do_action( 'bbp_user_edit_before_account' ); ?>

		<p class="half">
			<label for="user_login"><?php _e( 'Username', 'cheerapp' ); ?></label>
			<input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'user_login' ) ); ?>" disabled="disabled" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
			<span class="field-description"><?php _e( 'Usernames cannot be changed.', 'cheerapp' ); ?></span>
		</p>

		<p class="half">
			<label for="email"><?php _e( 'Email', 'cheerapp' ); ?></label>

			<input type="text" name="email" id="email" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'user_email' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />

			<?php

			// Handle address change requests
			$new_email = get_option( bbp_get_displayed_user_id() . '_new_email' );
			if ( $new_email && $new_email != bbp_get_displayed_user_field( 'user_email' ) ) : ?>

				<span class="updated inline">

					<?php printf( __( 'There is a pending email address change to <code>%1$s</code>. <a href="%2$s">Cancel</a>', 'cheerapp' ), $new_email['newemail'], esc_url( self_admin_url( 'user.php?dismiss=' . bbp_get_current_user_id()  . '_new_email' ) ) ); ?>

				</span>

			<?php endif; ?>

		</p>
		
	</fieldset>
	
	<fieldset class="bbp-form">

		<div id="password" class="row">
			<label for="pass1" class="full"><?php _e( 'New Password', 'cheerapp' ); ?></label>
			<p class="half">
				<input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
				<span class="field-description"><?php _e( 'If you would like to change the password type a new one. Otherwise leave this blank.', 'cheerapp' ); ?></span>
			</p>
			
			<p class="half">
				<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
				<span class="field-description"><?php _e( 'Type your new password again.', 'cheerapp' ); ?></span><br />
			</p>
			
			<label class="full" style="clear:both; text-transform: none;"><?php _e( 'Password strength', 'cheerapp' ); ?></label>
			
			<p class="half">
				<span class="field-description indicator-hint"><?php _e( 'The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', 'cheerapp' ); ?></span>
			</p>
			
			<p id="pass-strength-result" class="half"></p>
		</div>
		
		<div class="row">

			<?php if ( current_user_can( 'edit_users' ) ) : ?>

				<p class="half">
					<label for="role"><?php _e( 'Role:', 'cheerapp' ) ?></label>

					<?php bbp_edit_user_role(); ?>

				</p>

			<?php endif; ?>

			<?php if ( is_multisite() && is_super_admin() && current_user_can( 'manage_network_options' ) ) : ?>

				<p class="half">
					<label for="super_admin">
						<input type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( bbp_get_displayed_user_id() ) ); ?> tabindex="<?php bbp_tab_index(); ?>" />
						<?php _e( 'Super Admin', 'cheerapp' ); ?>
					</label>
					<span class="field-description"><?php _e( 'Grant this user super admin privileges for the Network.', 'cheerapp' ); ?></span>
				</p>

			<?php endif; ?>
				
		</div>	

		<?php do_action( 'bbp_user_edit_after_account' ); ?>

	</fieldset>

	<?php do_action( 'bbp_user_edit_after' ); ?>

	<fieldset class="submit">
		<div>
			<?php bbp_edit_user_form_fields(); ?>

			<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_user_edit_submit" name="bbp_user_edit_submit" class="button submit user-submit"><?php bbp_is_user_home_edit() ? _e( 'Update Profile', 'cheerapp' ) : _e( 'Update User', 'cheerapp' ); ?></button>
		</div>
	</fieldset>

</form>