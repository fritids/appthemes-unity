<?php
/*
Error 404 template
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', '404' ); ?>

<?php $sidebar_position	=	of_get_option( 'sidebar_position', 'right' ); ?>

<div id="main">
	<div class="inner-wrap">
	
		<div class="row">
		
			<?php $cp = $sidebar_position == 'left' ? 'right' : 'left'; ?>
			<div class="content span8 content-<?php echo $cp; ?>">
				
				<h3><?php _e( 'Page not found', 'cheerapp' ); ?></h3>
				<p><?php printf( __( 'It looks like the page you are looking for doesn&acute;t exist. Please try using the search form or go back to <a href="%s">home page</a>.', 'cheerapp' ), get_home_url() ); ?></p>
	
			</div><!-- end .content -->
			
			<?php get_sidebar(); ?>
		
		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>