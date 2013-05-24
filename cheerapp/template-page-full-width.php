<?php
/*
Template Name: Page - Full-width
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'page-full-width' ); ?>
			
	<div id="main">
	
		<div class="inner-wrap">
		
			<div class="content">
		
				<?php if(have_posts()) : while(have_posts()) : the_post();
				
				the_content();
				
				endwhile; endif; ?>
								
			</div><!-- end .content -->
								
		</div><!-- end .inner-wrap -->
	</div><!-- end #main -->
			
<?php get_footer(); ?>