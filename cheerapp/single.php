<?php
/* ---- Get theme options that will be used by this template ---- */
$sidebar_position				=	of_get_option( 'sidebar_position', 'right' );
$blog_single_thumbnail_lightbox	=	of_get_option( 'blog_single_thumbnail_lightbox', '0' );
$blog_thumbnail_keep_ratio		=	of_get_option( 'blog_thumbnail_keep_ratio', '0' );
?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'blog' ); ?>
	
<div id="main" class="blog">
	<div class="inner-wrap">
	
		<div class="row">
	
			<?php $cp = $sidebar_position == 'left' ? 'right' : 'left'; ?>
			<div class="content span8 content-<?php echo $cp; ?>">
				
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post() ; ?>
	
					<?php $post_classes = array( 'post', 'post-blog', 'clearfix' ); ?>
	
					<article <?php post_class( $post_classes ); ?> id="post-<?php the_ID(); ?>">
					
						<h1 class="post-title"><?php the_title(); ?></h1>
		
						<?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) { ?>
							<?php if ( '0' == $blog_single_thumbnail_lightbox ) { ?>
								
								<div class="post-image">
									<?php
									if ( '1' == $blog_thumbnail_keep_ratio ) {
										the_post_thumbnail( 'medium' );
									} else {
										the_post_thumbnail( 'thumbnail-blog' );
									}
									?>
								</div>
								
							<?php } else { ?>
							
								<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?>
								<div class="post-image">
									<a rel="prettyPhoto[gallery]" href="<?php echo $thumbnail[0]; ?>">
										<?php
										if ( '1' == $blog_thumbnail_keep_ratio ) {
											the_post_thumbnail( 'medium' );
										} else {
											the_post_thumbnail( 'thumbnail-blog' );
										}
										?>
									</a>
								</div>
								
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
						
							<?php the_content(); ?>
							
						</div><!-- end .post-content -->
						
						<?php wp_link_pages(); ?>
		
					</article><!-- end .post -->
					
					<?php if( comments_open() || have_comments() ) { ?>
						
						<hr />
						
						<?php comments_template( '/comments.php' ); ?>
						
					<?php } ?>
					
				<?php endwhile; endif; ?>
	
			</div><!-- end .content -->
			
			<?php get_sidebar(); ?>
			
		</div><!-- end .row -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>