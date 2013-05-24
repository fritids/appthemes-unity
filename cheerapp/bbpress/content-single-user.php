<?php

/**
 * Single User Part
 */

?>

<div class="span3 bbp-user-details">

	<?php
	// Profile details
	bbp_get_template_part( 'bbpress/user', 'details' );
	?>

</div><!-- end .bbp-user-details -->

<div class="span9 bbp-user-tabs tabs">

	<div class="tabs-controls clearfix">
		<?php if ( bbp_is_subscriptions_active() && ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) ) : ?>
			<a href="#subscriptions" class="tab-control"><?php _e( 'Subscriptions', 'cheerapp' ); ?></a>
		<?php endif; ?>
		
		<a href="#favorites" class="tab-control"><?php _e( 'Favorite threads', 'cheerapp' ); ?></a>
		<a href="#topics-created" class="tab-control"><?php _e( 'Created threads', 'cheerapp' ); ?></a>
		
		<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) : ?>
			<a href="<?php bbp_user_profile_edit_url(); ?>" class="tab-control pseudo-tab-control"><?php _e( 'Edit', 'cheerapp' ); ?></a>
		<?php endif; ?>
	</div>
	
	<div class="tabs-content">
	
		<?php
		// Subscriptions
		bbp_get_template_part( 'bbpress/user', 'subscriptions'  );
		
		// Favorite topics
		bbp_get_template_part( 'bbpress/user', 'favorites'      );
		
		// Topics created
		bbp_get_template_part( 'bbpress/user', 'topics-created' );
		?>
	
	</div><!-- end .tabs-content -->

</div><!-- end .bbp-user-tabs -->