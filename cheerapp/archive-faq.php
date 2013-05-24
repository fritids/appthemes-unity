<?php get_header(); ?>
<?php get_template_part( 'top', 'faq' ); ?>

<div id="main" class="archive">
	<div class="inner-wrap">
	
		<div class="row">
		
			<div class="content span8 content-right">
				
				<?php $count = 0; ?>
			
				<?php			
				$terms = get_terms( 'faq_category', array( 'fields' => 'all' ) );
				
				foreach( $terms as $term ) {
					$args = array(
						'post_type'		=>	'faq',
						'orderby'		=>	'date',
						'order'			=>	'DESC',
						'posts_per_page'=>	-1,
						'tax_query'		=>	array(
							array(
								'taxonomy'		=>	'faq_category',
								'field'			=>	'id',
								'terms'			=>	array( $term->term_id )
							)
						)
					);
					
					query_posts( $args );
					
					if( have_posts() ) :
					?>
					
						<?php if( $count != 0 ) echo '<hr />'; ?>
					
						<div class="faq">
						
							<h4> <?php echo $term->name; ?> </h4>
							
							<ul class="faq">
								<?php while( have_posts() ) : the_post(); ?>
							
									<?php get_template_part( 'loop', 'faq'); ?>
									
								<?php endwhile; ?>
							</ul><!-- end .faq -->
						
						</div>
						
					<?php
					endif;
					
					wp_reset_query();
					
					$count++;
				}
				?>
			
			</div><!-- end .content -->
			
			<?php get_template_part( 'sidebar', 'faq' ); ?>
			
		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>