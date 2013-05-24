<?php

/**
 * Single View Content Part
 */

?>
		
<div class="span9">

	<?php do_action( 'bbp_template_notices' ); ?>

	<div id="topics-front" class="bbp-topics-front forum-content">
		
		<?php the_content(); ?>
	
		<div class="entry-content box">

			<?php bbp_set_query_name( 'bbp_view' ); ?>

			<?php if ( bbp_view_query() ) : ?>
		
				<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
		
				<?php bbp_get_template_part( 'bbpress/loop',       'topics'    ); ?>
		
				<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
		
			<?php else : ?>
		
				<?php bbp_get_template_part( 'bbpress/feedback',   'no-topics' ); ?>
		
			<?php endif; ?>

			<?php bbp_reset_query_name(); ?>

		</div>
		
	</div><!-- #topics-front -->

</div>

<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>