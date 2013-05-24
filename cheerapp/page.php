<?php
/*
Regular page template
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'page' ); ?>

<?php $sidebar_position	=	of_get_option( 'sidebar_position', 'right' ); ?>

<div id="main">
	<div class="inner-wrap">
	
		<div class="row">
		
			<?php $cp = $sidebar_position == 'left' ? 'right' : 'left'; ?>
			<div class="content span8 content-<?php echo $cp; ?>">
	
				<?php while( have_posts() ) : the_post(); ?>
	
					<?php the_content(); ?>
		
				<?php endwhile; ?>
	
			</div><!-- end .content -->
			
			<?php get_sidebar(); ?>
		
		</div>
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>