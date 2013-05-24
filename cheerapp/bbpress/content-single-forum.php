<?php

/**
 * Single Forum Content
 */

?>

	<?php if ( post_password_required() ) : ?>
	
		<div class="entry-content">

		<?php bbp_get_template_part( 'bbpress/form', 'protected' ); ?>
		
		</div><!-- end .entry-content -->

	<?php else : ?>
	
		<div class="entry-content">

		<?php if ( bbp_get_forum_subforum_count() && bbp_has_forums() ) : ?>
		
			<div class="box">

				<?php bbp_get_template_part( 'bbpress/loop', 'forums' ); ?>
				
			</div>

		<?php endif; ?>

		<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>
		
			<div class="box">

				<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
	
				<?php bbp_get_template_part( 'bbpress/loop',       'topics'    ); ?>
	
				<?php bbp_get_template_part( 'bbpress/pagination', 'topics'    ); ?>
				
			</div>

			<?php bbp_get_template_part( 'bbpress/form',       'topic'     ); ?>

		<?php elseif( !bbp_is_forum_category() ) : ?>

			<?php bbp_get_template_part( 'bbpress/feedback',   'no-topics' ); ?>

			<?php bbp_get_template_part( 'bbpress/form',       'topic'     ); ?>

		<?php endif; ?>
		
		</div>

	<?php endif; ?>