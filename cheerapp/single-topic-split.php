<?php

/**
 * Split topic page
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum bbp-edit-page">
	<div class="inner-wrap">
	
		<div class="content row">
		
			<div class="span9">
			
				<?php do_action( 'bbp_template_notices' ); ?>

				<?php while ( have_posts() ) the_post(); ?>
				
					<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<hr class="small-margin" />
					
					<div class="entry-content">
	
						<?php bbp_get_template_part( 'bbpress/form', 'topic-split' ); ?>
	
					</div>
			
			</div>

			<?php bbp_get_template_part( 'bbpress/sidebar', 'forum' ); ?>
				
		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>