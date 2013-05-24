<?php

/**
 * Template Name: bbPress - Topics (No Replies)
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
	
		<div class="content row">
		
			<div class="span9">
			
				<?php do_action( 'bbp_template_notices' ); ?>

				<?php while ( have_posts() ) : the_post(); ?>
	
					<div id="topics-front" class="bbp-topics-front forum-content">
						
						<?php the_content(); ?>
					
						<div class="entry-content box">
	
							<?php bbp_set_query_name( 'bbp_no_replies' ); ?>
	
							<?php if ( bbp_has_topics( array( 'meta_key' => '_bbp_reply_count', 'meta_value' => '1', 'meta_compare' => '<', 'orderby' => 'date', 'show_stickies' => false ) ) ) : ?>
	
								<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
	
								<?php bbp_get_template_part( 'bbpress/loop',       'topics'    ); ?>
	
								<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
	
							<?php else : ?>
	
								<?php bbp_get_template_part( 'bbpress/feedback',   'no-topics' ); ?>
	
							<?php endif; ?>
	
							<?php bbp_reset_query_name(); ?>
	
						</div>
						
					</div><!-- #topics-front -->
	
				<?php endwhile; ?>
			
			</div>

			<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>

		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>
