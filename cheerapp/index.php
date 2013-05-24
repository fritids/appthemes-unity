<?php
/*
Template Name: Home
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'index' ); ?>
			
	<div id="main">
	
		<div class="inner-wrap">
		
			<?php if( is_page_template( 'index.php' ) && of_get_option( 'cta_enable', '1' ) ) get_template_part( 'action' ); ?>
		
			<?php
			if(have_posts()) : while(have_posts()) : the_post();
			
			the_content();
			
			endwhile; endif;
			?>
			
			<?php
			if( of_get_option( 'show_home_quick_links', '1' ) == true && function_exists( 'royal_quick_links' ) ) :
				
				$args = array(
					'before'		=>	'<hr /><div class="quick-links-wrap"><div id="quick-links">',
					'after'			=>	'</div><div style="clear:both"></div></div>',
					'class'			=>	array( 'clearfix' ),
					'link_class'	=>	array( 'tooltip' )
				);
				royal_quick_links( $args );
				
			endif;
			
			if( of_get_option( 'show_home_bottombar', '1' ) == true ) get_template_part( 'bottom', 'index' );
			?>
								
		</div><!-- end .inner-wrap -->
	</div><!-- end #main -->
			
<?php get_footer(); ?>