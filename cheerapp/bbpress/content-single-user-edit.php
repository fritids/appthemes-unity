<?php

/**
 * Single User Edit Part
 */

?>

<div class="span3 bbp-user-details">

	<?php
	// Profile details
	bbp_get_template_part( 'bbpress/user', 'details'        );
	?>

</div><!-- end .bbp-user-details -->

<div class="span9 bbp-user-tabs tabs pseudo-tabs">

	<div class="tabs-controls clearfix">
		<?php if ( bbp_is_subscriptions_active() && ( bbp_is_user_home_edit() || current_user_can( 'edit_users' ) ) ) : ?>
			<a href="<?php echo bbp_get_user_profile_url(); ?>#subscriptions" class="tab-control pseudo-tab-control"><?php _e( 'Subscriptions', 'cheerapp' ); ?></a>
		<?php endif; ?>
		
		<a href="<?php echo bbp_get_user_profile_url(); ?>#favorites" class="tab-control pseudo-tab-control"><?php _e( 'Favorite threads', 'cheerapp' ); ?></a>
		<a href="<?php echo bbp_get_user_profile_url(); ?>#topics-created" class="tab-control pseudo-tab-control"><?php _e( 'Created threads', 'cheerapp' ); ?></a>
		
		<?php if ( bbp_is_user_home_edit() || current_user_can( 'edit_users' ) ) : ?>
			<a href="<?php bbp_user_profile_edit_url(); ?>" class="tab-control pseudo-tab-control current"><?php _e( 'Edit', 'cheerapp' ); ?></a>
		<?php endif; ?>
	</div>
	
	<div class="tabs-content">
	
		<?php
		// User edit form
		bbp_get_template_part( 'bbpress/form', 'user-edit' );
		?>
	
	</div><!-- end .tabs-content -->

</div><!-- end .bbp-user-tabs -->