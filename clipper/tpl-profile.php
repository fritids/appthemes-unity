<?php
// Template Name: User Profile


$current_user = wp_get_current_user(); // grabs the user info and puts into vars
$display_user_name = clpr_get_user_name();
?>


<script type='text/javascript' src='<?php echo admin_url('js/password-strength-meter.js'); ?>'></script>

<script type="text/javascript">
// <![CDATA[
(function($){

	$(document).ready( function() {

		function check_pass_strength () {

			var pass = $('#pass1').val();
			var pass2 = $('#pass2').val();
			var user = $('#user_login').val();

			$('#pass-strength-result').removeClass('short bad good strong');
			if ( ! pass ) {
				$('#pass-strength-result').html( pwsL10n.empty );
				return;
			}

			var strength = passwordStrength(pass, user, pass2);

			if ( 2 == strength )
				$('#pass-strength-result').addClass('bad').html( pwsL10n.bad );
			else if ( 3 == strength )
				$('#pass-strength-result').addClass('good').html( pwsL10n.good );
			else if ( 4 == strength )
				$('#pass-strength-result').addClass('strong').html( pwsL10n.strong );
			else if ( 5 == strength )
				$('#pass-strength-result').addClass('short').html( pwsL10n.mismatch );
			else
				$('#pass-strength-result').addClass('short').html( pwsL10n.short );

		}

		$('#pass1, #pass2').val('').keyup( check_pass_strength );
	});
})(jQuery);


pwsL10n = {
	empty: "<?php _e( 'Strength indicator', APP_TD ); ?>",
	short: "<?php _e( 'Very weak', APP_TD ); ?>",
	bad: "<?php _e( 'Weak', APP_TD ); ?>",
	good: "<?php _e( 'Medium', APP_TD ); ?>",
	strong: "<?php _e( 'Strong', APP_TD ); ?>",
	mismatch: "<?php _e( 'Mismatch', APP_TD ); ?>"
}
try{convertEntities(pwsL10n);}catch(e){};

// ]]>
</script>


<div id="content">

	<div class="content-box">

		<div class="box-t">&nbsp;</div>

		<div class="box-c">

			<div class="box-holder">

				<div class="blog">

					<h1><?php printf( __( '%s\'s Profile', APP_TD ), ucwords( $display_user_name )); ?></h1>

					<div class="text-box">

						<?php do_action( 'appthemes_notices' ); ?>

						<p><?php _e( 'Update your user profile below.', APP_TD ); ?></p>

					</div>

					<div class="post-box">

						<form name="profile" id="loginForm" action="" method="post" class="loginForm">
						<?php wp_nonce_field( 'app-edit-profile' ); ?>

						<input type="hidden" name="from" value="profile" />
						<input type="hidden" name="checkuser_id" value="<?php echo $user_ID; ?>" />


						<fieldset>

								<ol>
									<li>
										<label><?php _e( 'Username:', APP_TD ); ?></label>
										<input type="text" name="user_login" class="text regular-text" id="user_login" value="<?php echo $current_user->user_login; ?>" maxlength="100" disabled />
									</li>

									<li>
										<label for="first_name"><?php _e( 'First Name:', APP_TD ); ?></label>
										<input type="text" name="first_name" class="text regular-text" id="first_name" value="<?php echo $current_user->first_name; ?>" maxlength="100" />
									</li>
									<li>
										<label for="last_name"><?php _e( 'Last Name:', APP_TD ); ?></label>
										<input type="text" name="last_name" class="text regular-text" id="last_name" value="<?php echo $current_user->last_name; ?>" maxlength="100" />
									</li>
									<li>
										<label for="nickname"><?php _e( 'Nickname:', APP_TD ); ?></label>
										<input type="text" name="nickname" class="text regular-text" id="nickname" value="<?php echo $current_user->nickname; ?>" maxlength="100" />
									</li>
									<li>
										<label for="display_name"><?php _e( 'Display Name:', APP_TD ); ?></label>

											<select name="display_name" class="text regular-text" id="display_name">
											<?php
													$public_display = array();
													$public_display['display_displayname'] = $current_user->display_name;
													$public_display['display_nickname'] = $current_user->nickname;
													$public_display['display_username'] = $current_user->user_login;
													$public_display['display_firstname'] = $current_user->first_name;
													$public_display['display_firstlast'] = $current_user->first_name.' '.$current_user->last_name;
													$public_display['display_lastfirst'] = $current_user->last_name.' '.$current_user->first_name;
													$public_display = array_unique(array_filter(array_map('trim', $public_display)));
													foreach($public_display as $id => $item) {
											?>
													<option id="<?php echo $id; ?>" value="<?php echo $item; ?>"><?php echo $item; ?></option>
											<?php
													}
											?>
											</select>

									</li>

									<li>
										<label for="email"><?php _e( 'Email:', APP_TD ); ?></label>
										<input type="text" name="email" class="text regular-text" id="email" value="<?php echo $current_user->user_email; ?>" maxlength="100" />
									</li>


									<li>
										<label for="url"><?php _e( 'Website:', APP_TD ); ?></label>
										<input type="text" name="url" class="text regular-text" id="url" value="<?php echo $current_user->user_url; ?>" maxlength="100" />
									</li>


									<li>
										<label for="description"><?php _e( 'About Me:', APP_TD ); ?></label>
										<textarea name="description" class="text regular-text" id="description" rows="10" cols="50"><?php echo $current_user->description; ?></textarea>
									</li>

								<?php foreach ( _wp_get_user_contactmethods( $current_user ) as $name => $desc ) : ?>
									<li>
										<label for="<?php echo $name; ?>"><?php echo apply_filters('user_'.$name.'_label', $desc); ?>:</label>
										<input type="text" name="<?php echo $name; ?>" class="text regular-text" id="<?php echo $name; ?>" value="<?php echo esc_attr($current_user->$name); ?>" />
										<?php echo clpr_profile_fields_description( $name ); ?>
									</li>
								<?php endforeach; ?>

									<?php
									$show_password_fields = apply_filters('show_password_fields', true);
									if ( $show_password_fields ) :
									?>

										<li>
											<label for="pass1"><?php _e( 'New Password:', APP_TD ); ?></label>
											<input type="password" name="pass1" class="text regular-text" id="pass1" maxlength="50" value="" /><br />
											<span class="description"><?php _e( 'Leave this field blank unless you would like to change your password.', APP_TD ); ?></span>
										</li>

										<li>
											<label for="pass1"><?php _e( 'Password Again:', APP_TD ); ?></label>
											<input type="password" name="pass2" class="text regular-text" id="pass2" maxlength="50" value="" /><br />
											<span class="description"><?php _e( 'Type your new password again.', APP_TD ); ?></span>
										</li>

										<li>
											<label for="pass1">&nbsp;</label>
											<div id="pass-strength-result"><?php _e( 'Strength indicator', APP_TD ); ?></div><br /><br /><br />
											<span class="description"><?php _e( 'Your password should be at least seven characters long.', APP_TD ); ?></span>
										</li>

									<?php endif; ?>

									<li>
										<button type="submit" class="btn profile" id="submit" name="submit" value="submit"><?php _e( 'Update Profile &raquo;', APP_TD ); ?></button>
									</li>

								</ol>

						</fieldset>

							<input type="hidden" name="action" value="app-edit-profile" />
							<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_ID; ?>" />
							<input type="hidden" name="admin_color" value="<?php echo esc_attr( $current_user->admin_color ); ?>" />
							<input type="hidden" name="rich_editing" value="<?php echo esc_attr( $current_user->rich_editing ); ?>" />
							<input type="hidden" name="comment_shortcuts" value="<?php echo esc_attr( $current_user->comment_shortcuts ); ?>" />

							<?php if ( _get_admin_bar_pref( 'front', $user_ID ) ) { ?>
								<input type="hidden" name="admin_bar_front" value="true" />
							<?php } ?>

						</form>

					</div> <!-- #post-box -->


				</div> <!-- #blog -->

			</div> <!-- #box-holder -->

		</div> <!-- #box-c -->

		<div class="box-b">&nbsp;</div>

	</div> <!-- #content-box -->



</div>

<?php get_sidebar('user'); ?>

