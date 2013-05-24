<?php

/**
 * User Registration Form
 */

?>

<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form">

	<div class="bbp-template-notice">
		<p><?php _e( 'Your password will be emailed to you. You will be able to change it later in your account settings.', 'cheerapp' ) ?></p>
	</div>

	<fieldset class="bbp-form">

		<div class="bbp-username">
			<label for="user_login"><?php _e( 'Username', 'cheerapp' ); ?>: </label>
			<input type="text" name="user_login" value="<?php bbp_sanitize_val( 'user_login' ); ?>" size="20" id="user_login" class="tooltip-focus" tabindex="<?php bbp_tab_index(); ?>" title="<?php _e( 'Your username must be unique and cannot be changed later.', 'cheerapp' ); ?>" />
		</div>

		<div class="bbp-email">
			<label for="user_email"><?php _e( 'Email', 'cheerapp' ); ?>: </label>
			<input type="text" name="user_email" value="<?php bbp_sanitize_val( 'user_email' ); ?>" size="20" id="user_email" class="tooltip-focus" tabindex="<?php bbp_tab_index(); ?>" title="<?php _e( 'We use your email address to email you a secure password and verify your account.', 'cheerapp' ) ?>" />
		</div>

		<?php do_action( 'register_form' ); ?>

		<div class="bbp-submit-wrapper clearfix">
		
			<?php
			if( function_exists( 'royal_bbp_user_register_fields' ) ) :
				royal_bbp_user_register_fields();
			else :
				bbp_user_register_fields();
			endif;
			?>

			<input type="submit" name="user-submit" tabindex="<?php bbp_tab_index(); ?>" class="button button-register submit user-submit" value="<?php _e( 'Register', 'cheerapp' ); ?>" />

		</div>
	</fieldset>
	
	<?php if( function_exists( 'royal_get_page_by_template' ) ) : ?>
	
		<?php
		$login_page = royal_get_page_by_template( 'user-login' );
		if( !empty( $login_page ) ) :
		?>
		
			<div class="divider"></div>
				<div class="login-form-meta clearfix">

					<?php
					$sign_in_url	=	get_permalink( $login_page->ID );
					$sign_in_text	=	__( 'Sign in', 'cheerapp' );
					$sign_in_link	= '<a class="more" href="' . $sign_in_url . '">' . $sign_in_text . '</a>';
					?>
					<p class="no-margin sign-in-text"><?php printf( __( 'Already a member? %s &rarr;', 'cheerapp' ), $sign_in_link ); ?></p>
				
				</div>		
		<?php endif; ?>
		
	<?php endif; ?>
	
</form>
