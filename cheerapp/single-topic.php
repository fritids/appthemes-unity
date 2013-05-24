<?php

/**
 * Single Topic
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
	
		<div class="content">

			<?php do_action( 'bbp_template_notices' ); ?>

			<?php if ( bbp_user_can_view_forum( array( 'forum_id' => bbp_get_topic_forum_id() ) ) ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					
					<h1 class="topic-title"><?php bbp_topic_title(); ?></h1>
					
					<div class="visible-phone topic-meta-info">
					
						<?php bbp_get_template_part( 'bbpress/topic-meta' ); ?>
					
					</div>

					<?php bbp_get_template_part( 'bbpress/content', 'single-topic' ); ?>
					
				<?php endwhile; ?>

			<?php elseif ( bbp_is_forum_private( bbp_get_topic_forum_id(), false ) ) : ?>

				<?php bbp_get_template_part( 'bbpress/feedback', 'no-access' ); ?>

			<?php endif; ?>
				
		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>
