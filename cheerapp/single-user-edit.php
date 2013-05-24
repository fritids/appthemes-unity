<?php

/**
 * bbPress User Profile Edit
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
		
		<?php do_action( 'bbp_template_notices' ); ?>
	
		<div id="bbp-user-<?php bbp_current_user_id(); ?>" class="content bbp-single-user row">

			<?php bbp_get_template_part( 'bbpress/content', 'single-user-edit' ); ?>

		</div><!-- end #bbp-user-<?php bbp_current_user_id(); ?> -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>