<?php

/**
 * Edit handler for replies
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum bbp-edit-page">
	<div class="inner-wrap">
	
		<div class="content row">
		
			<div class="span9">
			
				<?php while ( have_posts() ) : the_post(); ?>
			
					<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<hr class="small-margin" />
					
					<div id="bbp-reply-wrapper-<?php bbp_reply_id(); ?>" class="bbp-reply-content-wrapper clearfix">
	
						<?php bbp_get_template_part( 'bbpress/form', 'reply' ); ?>
	
					</div><!-- end .bbp-reply-content-wrapper -->
					
					<?php bbp_get_template_part( 'bbpress/sidebar', 'single-reply' ); ?>
	
				<?php endwhile; ?>
			
			</div>

			<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>
				
		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>