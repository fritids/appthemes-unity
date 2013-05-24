<?php

/**
 * User Topics Created
 */

?>

<?php bbp_set_query_name( 'bbp_user_profile_topics_created' ); ?>

<div id="topics-created" class="bbp-author-topics-started tab-content">

	<h3 class="hidden"><?php _e( 'Created threads', 'cheerapp' ); ?></h3>

	<?php if ( bbp_get_user_topics_started() ) :

		bbp_get_template_part( 'bbpress/pagination', 'topics' );
		bbp_get_template_part( 'bbpress/loop',       'topics' );
		bbp_get_template_part( 'bbpress/pagination', 'topics' );

	else : ?>

		<p><?php bbp_is_user_home() ? _e( 'You have not created any threads.', 'cheerapp' ) : _e( 'This user has not created any threads.', 'cheerapp' ); ?></p>

	<?php endif; ?>

</div><!-- #bbp-author-topics-started -->

<?php bbp_reset_query_name(); ?>
