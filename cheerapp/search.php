<?php
/*
Search results template
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'search' ); ?>

<?php $sidebar_position	=	of_get_option( 'sidebar_position', 'right' ); ?>

<div id="main">
	<div class="inner-wrap">
	
		<div class="row">
		
			<?php $cp = $sidebar_position == 'left' ? 'right' : 'left'; ?>
			<div class="content span8 content-<?php echo $cp; ?>">
				
				<?php $count = 0; ?>
				
				<?php while( have_posts() ) : the_post(); ?>
				
					<?php if( $count != 0 ) : ?>
						<hr />
					<?php endif; ?>
					
					<div class="<?php post_class( array( 'post', 'post-archive' ) ); ?>">
					
						<h4 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						
						<?php the_excerpt(); ?>
						
						<a class="more" href="<?php the_permalink(); ?>" title="<?php printf( __( 'Continue reading %s', 'cheerapp' ), '&ldquo;' . get_the_title() . '&rdquo;' ); ?>"><?php _e( 'Read more', 'cheerapp' ); ?></a>
						
					</div><!-- end .post -->
					
					<?php $count++; ?>
		
				<?php endwhile; ?>
				
				<?php
				if( function_exists( 'royal_show_pagination' ) ) :
					if( royal_show_pagination() ) :
						echo '<hr />';
						get_template_part( 'pagination', 'knowledgebase' );
					endif;
				else :
					echo '<hr />';
					get_template_part( 'pagination', 'knowledgebase' );
				endif;
				?>
	
			</div><!-- end .content -->
			
			<?php get_sidebar(); ?>

		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>