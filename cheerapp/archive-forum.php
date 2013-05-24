<?php

/**
 * bbPress - Forum Archive
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
	
		<div id="forum-front" class="content bbp-forum-front forum-content row">
		
			<div class="span9">

				<?php do_action( 'bbp_template_notices' ); ?>
				
				<div class="entry-content box">
	
					<?php bbp_get_template_part( 'bbpress/content', 'archive-forum' ); ?>
	
				</div>
			
			</div>
			
			<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>

		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>