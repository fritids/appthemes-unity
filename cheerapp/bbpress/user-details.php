<?php

/**
 * User Details
 */

?>

	<div class="user-avatar clearfix">
		<?php echo get_avatar( bbp_get_displayed_user_field( 'user_email' ), 80 ); ?>
	</div><!-- end #author-avatar -->

	<h2 class="user-name vcard">
		<?php echo bbp_get_displayed_user_field( 'display_name' ); ?>
	</h2>

	<div class="user-info">
	
		<?php
		
		$displayed_user = get_user_by( 'email', bbp_get_displayed_user_field( 'user_email' ) ); ?>
		
		<?php $bio_title = bbp_is_user_home_edit() ? sprintf( __( 'Your bio', 'cheerapp' ), bbp_get_displayed_user_field( 'display_name' ) ) : sprintf( __( '%s&acute;s bio', 'cheerapp' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>
		<h6><?php echo $bio_title; ?></h6>
		<p><?php echo bbp_get_displayed_user_field( 'description' ); ?></p>
		
		<h6><?php _e( 'Joined', 'cheerapp' ); ?></h6>
		<?php $joined_ago = bbp_get_time_since( bbp_convert_date( $displayed_user->user_registered ) ); ?>
		<p><strong><?php echo $joined_ago; ?></strong></p>
		
		<h6><?php _e( 'Status', 'cheerapp' ); ?></h6>
		<?php
		$user_role = array_shift( $displayed_user->roles );
		if( $user_role == 'bbp_participant' ) {
			$user_role = __( 'Forum participant', 'cheerapp' );
		}
		elseif( $user_role == 'bbp_moderator' ) {
			$user_role = __( 'Forum moderator', 'cheerapp' );
		}
		?>
		<p class="user-status"><strong><?php echo $user_role; ?></strong></p>
		
		<?php
		$user_url = $displayed_user->user_url;
		if( !empty( $user_url ) ) :
		?>
			<h6><?php _e( 'Homepage', 'cheerapp' ); ?></h6>
			<p><strong><a href="<?php echo $user_url; ?>"><?php echo $user_url; ?></a></strong></p>
		<?php endif; ?>
		
	</div><!-- end .user-info -->
