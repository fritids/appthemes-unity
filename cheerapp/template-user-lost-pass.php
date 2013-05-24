<?php

/**
 * Template Name: bbPress - User Lost Password
 */

// No logged in users
bbp_logged_in_redirect();
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'login' ); ?>
			
	<div id="main">
	
		<div class="inner-wrap">
		
			<div class="content">

				<?php do_action( 'bbp_template_notices' ); ?>

				<?php while( have_posts() ) : the_post(); ?>

					<?php the_content(); ?>

					<?php bbp_get_template_part( 'bbpress/form', 'user-lost-pass' ); ?>

				<?php endwhile; ?>

			</div><!-- end .content -->
								
		</div><!-- end .inner-wrap -->
	</div><!-- end #main -->
			
<?php get_footer(); ?>
