<?php

/**
 * User Login Form
 */

?>

<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form">

	<fieldset class="bbp-form">

		<div class="bbp-username">
			<label for="user_login"><?php _e( 'Username', 'cheerapp' ); ?>: </label>
			<input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="user_login" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div class="bbp-password">
			<label for="user_pass"><?php _e( 'Password', 'cheerapp' ); ?>: </label>
			<input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div class="bbp-remember-me">
			<label for="rememberme">
				<input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ) ); ?> id="rememberme" tabindex="<?php bbp_tab_index(); ?>" />
				<?php _e( 'Remember me', 'cheerapp' ); ?>
			</label>
		</div>

		<div class="bbp-submit-wrapper clearfix">

			<?php do_action( 'login_form' ); ?>

			<input type="submit" name="user-submit" value="<?php _e( 'Sign In', 'cheerapp' ); ?>" tabindex="<?php bbp_tab_index(); ?>" class="user-submit submit button button-sign-in" />

			<?php bbp_user_login_fields(); ?>

		</div>
	</fieldset>
	
	<?php if( function_exists( 'royal_get_page_by_template' ) ) : ?>
	
		<?php
		$register_page	=	royal_get_page_by_template( 'user-register' );
		$lost_pass_page	=	royal_get_page_by_template( 'user-lost-pass' );
		if( !empty( $register_page ) || !empty( $lost_pass_page ) ) :
		?>
		
			<div class="divider"></div>
			<div class="login-form-meta clearfix">
				
				<?php if( !empty( $register_page ) ) : ?>
					<?php
					$register_url	=	get_permalink( $register_page->ID );
					$register_text	=	__( 'Register', 'cheerapp' );
					$register_link	=	'<a class="more" href="' . $register_url . '">' . $register_text . '</a>';
					?>
					<p class="no-margin register-text"><?php printf( __( 'Don&acute;t have an account yet? %s &rarr;', 'cheerapp' ), $register_link ); ?></p>
				<?php endif; ?>
				
				<?php if( !empty( $lost_pass_page ) ) : ?>
					<?php
					$lost_pass_url	=	get_permalink( $lost_pass_page->ID );
					$lost_pass_text	=	__( 'Lost your password?', 'cheerapp' );
					$lost_pass_link	=	'<a class="more" href="' . $lost_pass_url . '">' . $lost_pass_text . '</a>';
					?>
					<p class="no-margin lost-pass-text"><?php echo $lost_pass_link; ?></p>
				<?php endif; ?>
			
			</div>		
		<?php endif; ?>
		
	<?php endif; ?>
	
</form>