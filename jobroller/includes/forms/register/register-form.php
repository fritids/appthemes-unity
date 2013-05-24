<?php
/**
 * JobRoller Registration Form
 * Function outputs the registration form
 *
 *
 * @version 1.6.3
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

add_action( 'jr_display_register_form', 'jr_register_form', 10, 2 );

function jr_register_form( $redirect = '', $role = 'job_lister' ) {
	
    global $posted;

    if ( get_option('users_can_register') ) {

		if ( ! $redirect ) $redirect = get_permalink( get_option('jr_dashboard_page_id') );
		
		$show_password_fields = apply_filters('show_password_fields', true);
    ?>

            <h2><?php _e('Create a free account', APP_TD); ?></h2>

            <form action="<?php echo appthemes_get_registration_url(); ?>" method="post" class="account_form" name="registerform" id="login-form">
				
				<?php 
					if ( 'yes' == get_option('jr_allow_job_seekers') ) :
						if (!$role) :
							?>
							<p class="role">
								<label><input type="radio" name="role" tabindex="5" value="job_lister" <?php if (isset($posted['role']) && $posted['role']=='job_lister') echo 'checked="checked"'; ?> /> <?php _e('I am an <strong>Employer</strong>', APP_TD); ?></label>
								<label class="alt"><input type="radio" tabindex="6" name="role" value="job_seeker" <?php if (isset($posted['role']) && $posted['role']=='job_seeker') echo 'checked="checked"'; ?> /> <?php _e('I am a <strong>Job Seeker</strong>', APP_TD); ?></label>
							</p>
							<?php
						elseif ($role=='job_lister') :
							echo '<div><input type="hidden" name="role" value="job_lister" /></div>';
						elseif ($role=='job_seeker') :
							echo '<div><input type="hidden" name="role" value="job_seeker" /></div>';
						endif;
					endif;
				?>
				
				<div class="account_form_fields">
				
	                <p>
	                    <label for="user_login"><?php _e('Username', APP_TD); ?></label><br/>
	                    <input type="text" class="text" tabindex="7" name="user_login" id="user_login" value="<?php if (isset($_POST['user_login'])) echo esc_attr(stripslashes($_POST['user_login'])); ?>" />
	                </p>
	
	                <p>
	                    <label for="user_email"><?php _e('Email', APP_TD); ?></label><br/>
	                    <input type="text" class="text" tabindex="8" name="user_email" id="user_email" value="<?php if (isset($_POST['user_email'])) echo esc_attr(stripslashes($_POST['user_email'])); ?>" />
	                </p>
					
					<?php if ( $show_password_fields ) : ?>
	                <p>
	                    <label for="your_password"><?php _e('Enter a password', APP_TD); ?></label><br/>
	                    <input type="password" class="text" tabindex="9" name="pass1" id="pass1" value="" />
	                </p>
	
	                <p>
	                    <label for="your_password_2"><?php _e('Enter password again', APP_TD); ?></label><br/>
	                    <input type="password" class="text" tabindex="10" name="pass2" id="pass2" value="" />
	                </p>

					<p>
						<div id="pass-strength-result" class="hide-if-no-js"><?php _e('Strength indicator'); ?></div>
						<p><span class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', APP_TD ); ?></span></p>
					</p>
	                <?php endif; ?>       
	                
	                <?php
					// include the spam checker if enabled();
					if ( current_theme_supports( 'app-recaptcha' ) )
						appthemes_recaptcha();
	                ?>
	                
					<?php if (get_option('jr_terms_page_id')>0) : ?><p>
						<input type="checkbox" name="terms" tabindex="11" value="yes" id="terms" <?php if (isset($_POST['terms'])) echo 'checked="checked"'; ?> /> <label for="terms"><?php _e('I accept the ', APP_TD); ?><a href="<?php echo get_permalink(get_option('jr_terms_page_id')); ?>" target="_blank"><?php _e('terms &amp; conditions', APP_TD); ?></a>.</label>
					</p><?php endif; ?>
					
					<?php do_action('register_form'); ?>
	                
					<p>
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>" />				
	                    <input type="submit" class="submit" tabindex="12" name="register" value="<?php _e('Create Account &rarr;', APP_TD); ?>" />
	                </p>

                </div>

				<!-- autofocus the field -->
				<script type="text/javascript">try{document.getElementById('user_login').focus();}catch(e){}</script>

            </form>
<?php
	};
}
