<?php

/**
 * User Favorites
 */

?>

<?php bbp_set_query_name( 'bbp_user_profile_favorites' ); ?>

<div id="favorites" class="bbp-author-favorites tab-content">

	<h3 class="hidden"><?php _e( 'Favorite threads', 'cheerapp' ); ?></h3>

	<?php if ( bbp_get_user_favorites() ) :

		bbp_get_template_part( 'bbpress/pagination', 'topics' );
		bbp_get_template_part( 'bbpress/loop',       'topics' );
		bbp_get_template_part( 'bbpress/pagination', 'topics' );

	else : ?>

		<p><?php bbp_is_user_home() ? _e( 'You currently have no favorite threads.', 'cheerapp' ) : _e( 'This user has no favorite threads.', 'cheerapp' ); ?></p>

	<?php endif; ?>

</div><!-- #bbp-author-favorites -->

<?php bbp_reset_query_name(); ?>
