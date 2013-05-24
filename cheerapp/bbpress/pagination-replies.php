<?php

/**
 * Pagination for pages of replies (when viewing a topic)
 */

?>

<?php if( bbp_get_topic_pagination_links() ) : ?>

	<div class="bbp-pagination">
	
		<?php do_action( 'bbp_template_before_pagination_loop' ); ?>
	
		<div class="bbp-pagination-links">
		
			<?php bbp_topic_pagination_links(); ?>
		
		</div>
	
		<?php do_action( 'bbp_template_after_pagination_loop' ); ?>
	
	</div><!-- end .bbp-pagination -->

<?php endif; ?>