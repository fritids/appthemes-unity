<?php

/**
 * Single Topic Content Part
 */

?>

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'bbpress/form', 'protected' ); ?>

	<?php else : ?>

		<?php if ( bbp_show_lead_topic() ) : ?>
		
			<div class="bbp-lead-post-content">

				<?php bbp_get_template_part( 'bbpress/content', 'single-topic-lead' ); ?>
				
			</div><!-- end .bbp-lead-post-content -->

		<?php endif; ?>

		<?php if ( bbp_get_query_name() || bbp_has_replies() ) : ?>
		
			<article id="bbp-topic-wrapper-<?php bbp_topic_id(); ?>" class="bbp-topic-wrapper row">
			
				<div class="bbp-topic-content-wrapper span9">
				
					<div class="bbp-topic-content box" id="topic-<?php bbp_topic_id(); ?>-replies">
	
						<?php bbp_get_template_part( 'bbpress/pagination', 'replies' ); ?>
			
						<?php bbp_get_template_part( 'bbpress/loop',       'replies' ); ?>
			
						<?php bbp_get_template_part( 'bbpress/pagination', 'replies' ); ?>
					
					</div><!-- end .bbp-topic-content -->
					
					<?php bbp_get_template_part( 'bbpress/form', 'reply' ); ?>
				
				</div><!-- end .bbp-topic-content-wrapper -->
				
				<?php bbp_get_template_part( 'bbpress/sidebar', 'single-topic' ); ?>
				
			</article><!-- end .bbp-topic-wrapper -->

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>