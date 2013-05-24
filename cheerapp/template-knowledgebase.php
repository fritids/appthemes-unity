<?php
/*
Template Name: Knowledgebase Page
*/
?>

<?php get_header(); ?>
<?php get_template_part( 'top', 'knowledgebase' ); ?>

<div id="main" class="archive">
	<div class="inner-wrap">
	
		<div class="row">
		
			<div class="content span8 content-right">
			
				<?php while( have_posts() ) : the_post();
					
					the_content();
					
				endwhile; ?>
			
				<?php
				$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				$args = array(
					'post_type'		=>	'knowledgebase',
					'orderby'		=>	'date',
					'order'			=>	'DESC',
					'posts_per_page'=>	5,
					'paged'			=>	$page
				);
				query_posts( $args );
				
				if( have_posts() ) : while( have_posts() ) : the_post();
				?>
				
					<?php get_template_part( 'loop', 'knowledgebase'); ?>
					
				<?php
				endwhile;
				else :
				?>
				
					<h3><?php _e( 'There are no knowledgebase articles available right now.', 'cheerapp' ); ?></h3>
				
				<?php
				endif;
				
				if( function_exists( 'royal_show_pagination' ) ) :
					royal_show_pagination() ? get_template_part( 'pagination', 'knowledgebase' ) : null;
				else :
					get_template_part( 'pagination', 'knowledgebase' );
				endif;
				
				wp_reset_query();
				?>
			
			</div><!-- end .content -->
			
			<?php get_template_part( 'sidebar', 'knowledgebase' ); ?>

		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>