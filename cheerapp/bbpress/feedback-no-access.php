<?php

/**
 * No access
 */

?>

<div id="forum-private" class="bbp-forum-content">
	<h1 class="entry-title"><?php _e( 'Private', 'bbpress' ); ?></h1>
	
	<div class="row">
	
		<div class="span9">
		
			<div class="entry-content">
				<div class="bbp-template-notice info">
					<p><?php _e( 'You do not have permission to view this forum.', 'bbpress' ); ?></p>
				</div>
			</div>
		
		</div>
		
		<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>
	
	</div>
	
</div><!-- #forum-private -->
