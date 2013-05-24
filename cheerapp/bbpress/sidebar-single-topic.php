<?php

/**
 * Single Topic Sidebar Part
 */

?>

<div class="bbp-topic-sidebar span3">
	
	<div class="hidden-phone topic-meta-info">
	
		<?php bbp_get_template_part( 'bbpress/topic-meta' ); ?>
	
		<hr />
	
	</div>
	
	<div class="forum-widgets">
	
		<?php dynamic_sidebar( 'forum-sidebar' ); ?>
	
	</div>
	
</div><!-- end .bbp-topic-sidebar -->