<?php

/**
 * Single Forum
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
	
		<div id="forum-<?php bbp_forum_id(); ?>" class="content forum-content row">
		
			<div class="span9">

				<?php do_action( 'bbp_template_notices' ); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php if ( bbp_user_can_view_forum() ) : ?>

						<?php bbp_get_template_part( 'bbpress/content', 'single-forum' ); ?>

					<?php else : // Forum exists, user no access ?>

						<?php bbp_get_template_part( 'bbpress/feedback', 'no-access' ); ?>

					<?php endif; ?>

				<?php endwhile; ?>
				
			</div>
			
			<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>

		</div><!-- #forum-<?php bbp_forum_id(); ?> -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>
