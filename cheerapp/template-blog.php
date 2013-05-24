<?php
/*
Template Name: Blog Page
*/
?>

<?php
/* ---- Get theme options that will be used by this template ---- */
$sidebar_position			=	of_get_option( 'sidebar_position', 'right' );
$blog_thumbnail_lightbox	=	of_get_option( 'blog_thumbnail_lightbox', '0' );
$blog_thumbnail_keep_ratio	=	of_get_option( 'blog_thumbnail_keep_ratio', '0' );
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'blog' ); ?>

<div id="main" class="blog">
	<div class="inner-wrap">
	
		<div class="row">
		
			<?php $cp = $sidebar_position == 'left' ? 'right' : 'left'; ?>
			<div class="content span8 content-<?php echo $cp; ?>">
	
				<?php
				$page	=	get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;;
				$args	=	array(
					'post_type'		=>	'post',
					'paged'			=>	$page
				);
	
				query_posts($args);	
				if( have_posts() ) : while( have_posts() ) : the_post();
				?>
	
					<?php get_template_part( 'loop', 'blog' ); ?>
		
				<?php endwhile; else : ?>
	
					<h4><?php _e( 'There are currently no blog posts to show', 'cheerapp' ) ?></h4>
	
				<?php
				endif;
				
				if( function_exists( 'royal_show_pagination' ) ) :
					royal_show_pagination() ? get_template_part( 'pagination', 'blog' ) : null;
				else :
					get_template_part( 'pagination', 'blog' );
				endif;
				
				wp_reset_query();
				?>
	
			</div><!-- end .content -->
			
			<?php get_sidebar(); ?>
			
		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>