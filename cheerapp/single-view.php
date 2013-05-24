<?php

/**
 * Single View
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum bbp-edit-page">
	<div class="inner-wrap">
	
		<div id="bbp-view-<?php bbp_view_id(); ?>" class="content row">
	
			<?php bbp_get_template_part( 'bbpress/content', 'single-view' ); ?>
	
		</div>
	
	</div>
</div>

<?php get_footer(); ?>
