<?php

/**
 * Topic Tag
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
		
		<div id="topic-tag" class="content bbp-topic-tag bbp-topics-front forum-content row">
		
			<div class="span9">
			
				<?php do_action( 'bbp_template_notices' ); ?>

				<div class="entry-content box">
	
					<?php bbp_topic_tag_description(); ?>
	
					<?php do_action( 'bbp_template_before_topic_tag' ); ?>
	
					<?php if ( bbp_has_topics( array( bbp_get_topic_tag_tax_id() => bbp_get_topic_tag_slug() ) ) ) : ?>
	
						<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
	
						<?php bbp_get_template_part( 'bbpress/loop',       'topics'    ); ?>
	
						<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
	
					<?php else : ?>
	
						<?php bbp_get_template_part( 'bbpress/feedback',   'no-topics' ); ?>
	
					<?php endif; ?>
	
					<?php do_action( 'bbp_template_after_topic_tag' ); ?>
	
				</div>
			
			</div>

			<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>

		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>
