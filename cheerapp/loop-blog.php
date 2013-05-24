<?php
global $blog_thumbnail_lightbox, $blog_thumbnail_keep_ratio;
$post_classes = array( 'post', 'post-blog', 'clearfix' );
?>

<article <?php post_class( $post_classes ); ?> id="post-<?php the_ID(); ?>">
	<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

	<?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) { ?>
		<?php if ( '1' == $blog_thumbnail_lightbox ) { ?>
			
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
			
		<?php } else { ?>
		
			<a href="<?php the_permalink(); ?>" class="post-image">
				<?php
				if ( '1' == $blog_thumbnail_keep_ratio ) {
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
		<!-- <a class="more" href="<?php the_permalink(); ?>" title="<?php printf( __( 'Continue reading %s', 'cheerapp' ), '&ldquo;' . get_the_title() . '&rdquo;' ); ?>"><?php _e( 'Read more', 'cheerapp' ); ?></a> -->
		
	</div><!-- end .post-content -->

</article><!-- end .post -->

<hr />