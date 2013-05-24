<?php get_header(); ?>
<?php get_template_part( 'top', 'knowledgebase' ); ?>

<div id="main" class="archive">
	<div class="inner-wrap">
	
		<div class="row">
		
			<div class="content span8 content-right">
			
				<?php			
				if( have_posts() ) : while( have_posts() ) : the_post();
				?>
				
					<?php get_template_part( 'loop', 'knowledgebase'); ?>
					
				<?php
				endwhile;
				endif;
				
				if( function_exists( 'royal_show_pagination' ) ) :
					royal_show_pagination() ? get_template_part( 'pagination', 'knowledgebase' ) : null;
				else :
					get_template_part( 'pagination', 'knowledgebase' );
				endif;
				?>
			
			</div><!-- end .content -->
			
			<?php get_template_part( 'sidebar', 'knowledgebase' ); ?>
		
		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>