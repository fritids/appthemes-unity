<?php get_header(); ?>
<?php get_template_part( 'top', 'faq' ); ?>

<div id="main" class="wiki">
	<div class="inner-wrap">
	
		<div class="row">
		
			<div class="content span8 content-right">
			
				<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
			
					<article class="post post-wiki clearfix">
					
						<h1> <?php the_title(); ?> </h1>
						
						<div class="post-content">
						
							<?php the_content(); ?>
							
						</div><!-- end .post-content -->
						
					</article><!-- end .post -->
					
				<?php endwhile; endif; ?>
			
			</div><!-- end .content -->
			
			<?php get_template_part( 'sidebar', 'faq' ); ?>
		
		</div>
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>