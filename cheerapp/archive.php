<?php
/* ---- Get theme options that will be used by this template ---- */
if ( function_exists( 'get_option_tree' ) ) {
	$sidebar_position		=	of_get_option( 'sidebar_position', 'right' );
	$thumbnail_link			= get_option_tree( 'blog_archive_link', $theme_options );
	$thumbnail_dimensions	= get_option_tree( 'blog_thumbnail_dimensions', $theme_options );
}
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'blog' ); ?>

<div id="main" class="blog">
	<div class="inner-wrap">
	
		<div class="row">
		
			<?php if ( 'Left' == $sidebar_position ) { $cp = 'right'; } else { $cp = 'left'; } ?>
			<div class="content span8 content-<?php echo $cp; ?>">
	
				<?php
				$count = 0;
				
				if( have_posts() ) : while( have_posts() ) : the_post();
				?>
	
					<?php
					if ( $count != 0 ) {
					?>
						<hr />
					<?php
					}
					$count++;
					?>
		
					<?php $post_classes = array( 'post', 'post-blog', 'clearfix' ); ?>
		
					<article <?php post_class( $post_classes ); ?> id="post-<?php the_ID(); ?>">
						<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			
						<?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) { ?>
							<?php if ( 'Lightbox' == $thumbnail_link ) { ?>
								
								<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?>
								<div class="post-image">
									<a rel="prettyPhoto[gallery]" href="<?php echo $thumbnail[0]; ?>">
										<?php
										if ( 'Keep original ratio' == $thumbnail_dimensions ) {
											the_post_thumbnail( 'medium' );
										} else {
											the_post_thumbnail( 'thumbnail-blog' );
										}
										?>
									</a>
								</div>
								
							<?php } else { ?>
							
								<a href="<?php the_permalink(); ?>" class="post-image">
									<?php
									if ( 'Keep original ratio' == $thumbnail_dimensions ) {
										the_post_thumbnail( 'medium' );
									} else {
										the_post_thumbnail( 'thumbnail-blog' );
									}
									?>
								</a>
								
							<?php } ?>
						<?php } ?>
			
						<div class="post-meta">
							<span class="date"><?php the_time( get_option( 'date_format' ) ); ?></span>
							<?php comments_popup_link( __( '0', 'cheerapp' ), __( '1', 'cheerapp' ), __( '%', 'cheerapp' ), 'comment-count' ); ?>
							<ul class="categories-list">
								<li><?php echo get_the_category_list( '</li><li>' ); ?></li>
							</ul>
						</div><!-- end .post-meta -->
			
						<div class="post-content">
							<?php the_excerpt(); ?>
							<!-- <a class="more" href="<?php the_permalink(); ?>" title="<?php printf( __( 'Continue reading %s', 'cheerapp' ), get_the_title() ); ?>"><?php _e( 'Read more', 'cheerapp' ); ?></a> -->
						</div><!-- end .post-content -->
			
					</article><!-- end .post -->
		
		
				<?php endwhile; else : ?>
	
					<h4><?php _e( 'There are currently no blog posts to show', 'cheerapp' ) ?></h4>
	
				<?php
				endif;
				if ( royal_show_pagination() ) {
					echo '<hr />';
					get_template_part( 'pagination', 'blog' );
				}
				wp_reset_query();
				?>
	
			</div><!-- end .content -->
			<?php get_sidebar(); ?>

		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>