<?php

/**
 * Template Name: bbPress - Create Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
	
		<div class="content">

			<?php do_action( 'bbp_template_notices' ); ?>

			<?php while ( have_posts() ) : the_post(); ?>
			
				<?php the_content(); ?>

				<?php bbp_get_template_part( 'bbpress/form', 'topic' ); ?>

			<?php endwhile; ?>

		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>