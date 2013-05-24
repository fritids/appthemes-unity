<?php

/**
 * User Subscriptions
 */

?>

<?php if ( bbp_is_subscriptions_active() ) : ?>

	<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) : ?>

		<?php bbp_set_query_name( 'bbp_user_profile_subscriptions' ); ?>

		<div id="subscriptions" class="bbp-author-subscriptions tab-content">
		
			<h3 class="hidden"><?php _e( 'Subscriptions', 'cheerapp' ); ?></h3>

			<?php if ( bbp_get_user_subscriptions() ) :

				bbp_get_template_part( 'bbpress/pagination', 'topics' );
				bbp_get_template_part( 'bbpress/loop',       'topics' );
				bbp_get_template_part( 'bbpress/pagination', 'topics' );

			else : ?>

				<p><?php bbp_is_user_home() ? _e( 'You are not currently subscribed to any threads.', 'cheerapp' ) : _e( 'This user is not currently subscribed to any threads.', 'cheerapp' ); ?></p>

			<?php endif; ?>

		</div><!-- #subscriptions -->

		<?php bbp_reset_query_name(); ?>

	<?php endif; ?>

<?php endif; ?>
