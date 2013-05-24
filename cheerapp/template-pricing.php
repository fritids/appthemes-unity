<?php
/*
Template Name: Pricing Page
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'index' ); ?>
			
	<div id="main">
	
		<div class="inner-wrap">
		
			<div class="content pricing">
		
				<?php if(have_posts()) : while(have_posts()) : the_post();
				
				the_content();
				
				endwhile; endif; ?>
				
				<?php if( function_exists( 'royal_pricing_table' ) ) royal_pricing_table( 6, 2 ); ?>
				
			</div><!-- end .content -->
								
		</div><!-- end .inner-wrap -->
	</div><!-- end #main -->
			
<?php get_footer(); ?>