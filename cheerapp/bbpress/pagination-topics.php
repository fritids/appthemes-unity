<?php

/**
 * Pagination for pages of topics (when viewing a forum)
 */

?>

<?php if( bbp_get_forum_pagination_links() ) : ?>

	<div class="forum-pagination clearfix">
	
		<?php do_action( 'bbp_template_before_pagination_loop' ); ?>
	
		<div class="forum-pagination-links">
		
			<?php bbp_forum_pagination_links(); ?>
		
		</div>
	
		<?php do_action( 'bbp_template_after_pagination_loop' ); ?>
	
	</div><!-- end .bbp-pagination -->

<?php endif; ?>