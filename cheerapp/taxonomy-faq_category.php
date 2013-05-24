<?php get_header(); ?>
<?php get_template_part( 'top', 'faq' ); ?>

<div id="main" class="archive">
	<div class="inner-wrap">
	
		<div class="row">
		
			<div class="content span8 content-right">
				
				<ul class="faq">
				
					<?php
					global $wp_query;
					$args = array_merge( $wp_query->query, array( 'posts_per_page' => -1 ) );
					query_posts( $args );
					?>
				
					<?php while( have_posts() ) : the_post(); ?>
					
							<?php get_template_part( 'loop', 'faq' ); ?>
						
					<?php endwhile; ?>
				
				</ul><!-- end .faq -->
			
			</div><!-- end .content -->
			
			<?php get_template_part( 'sidebar', 'faq' ); ?>
		
		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>