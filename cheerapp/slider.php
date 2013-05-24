<?php
/*$autoplay = of_get_option( 'slider_autoplay', '0' );*/
$args = array(
	'post_type'		=>	'featured',
	'posts_per_page'=>	-1,
	'orderby'		=>	'menu_order',
	'order'			=>	'ASC'
);
$featured = null;
$featured = new WP_Query( $args );

if( $featured->have_posts() ) :
?>

<div id="slider-container" class="flexslider">


	<ul class="slides">
	
	<?php while( $featured->have_posts() ) : $featured->the_post(); ?>
	
		<?php
		$meta				=	get_post_meta( $post->ID, '_royal_meta', true);
		$slide_layout		=	!empty( $meta['layout'] ) ? $meta['layout'] : 'left';
		$slide_tagline		=	!empty( $meta['tagline'] ) ? $meta['tagline'] : null;
		$use_video			=	!empty( $meta['use_video'] ) ? $meta['use_video'] : false;
		$video				=	!empty( $meta['video'] ) ? $meta['video'] : null;
		$video_class		=	'';
		
		if( $use_video == 'after' || $use_video == 'only' ) $use_video = false;
		
		if( $use_video )
			$video_class = 'video-lightbox';
		?>
	
		<li class="slide text-<?php echo $slide_layout; ?> <?php echo $video_class; ?>">
		
			<div class="inner-wrap clearfix">
			
				<div class="post-content">
			
					<?php if( $slide_layout == 'center' ) { ?>
						<h2 class="no-margin">
							<?php the_title(); ?>
						</h2>
					<?php } else { ?>
						<h2>
							<?php if( $slide_tagline ) { ?>
								<small><?php echo $slide_tagline; ?></small>
							<?php } ?>
							<?php the_title(); ?>
						</h2>
					<?php } ?>
					
					<div class="hidden-phone">
						<?php the_content(); ?>
					</div>
					
				</div><!-- end .slide-content -->
				
				
				<div class="post-image-container">
					
					<?php if( has_post_thumbnail() && ( !$use_video || $use_video == 'lightbox' ) ) : ?>
						
						<?php if( $use_video == 'lightbox' && $video ) : ?>
						<a href="<?php echo $video; ?>" rel="prettyPhoto[video]" class="block-link play-button">
						<?php endif; ?>
						
						<?php
						$attr = array(
							'class'		=>	'featured-image'
						);
						
						if( $slide_layout == 'center' ) :
							the_post_thumbnail( 'featured-image-full', $attr );
						else :
							the_post_thumbnail( 'featured-image-small', $attr );
						endif;
						?>
						
						<?php if( $use_video == 'lightbox' && $video ) : ?>
						<span class="play-button-overlay"></span>
						</a>
						<?php endif; ?>
						
					<?php endif; ?>
					
				</div><!-- end .post-image-container -->
			
			</div>
								
		</li><!-- end .slide -->
		
	<?php endwhile; ?>
	
	</ul><!-- end .slides -->

</div><!-- end #slider-container -->

<?php endif; $featured = null; ?>