<div class="sidebar span4">
	
		<ul id="kb-categories" class="box">
			<li class="kb-categories-title">
				<?php _e( 'Browse by category', 'cheerapp' ); ?>
			</li>
			<?php
			$args = array(
				'orderby'		=>	'name',
				'title_li'		=>	'',
				'taxonomy'		=>	'faq_category'
			);
			
			if( is_single() ) {
				$terms = get_the_terms( $post->ID, 'faq_category' );
				$term_id = 0;
				
				foreach( $terms as $term ) {
					if( $term->parent != 0 ) $term_id = $term->term_id;
				}
				
				if( $term_id == 0 ) {
					foreach( $terms as $term ) {
						$term_id = $term->term_id;
					}
				}
				
				$args['current_category'] = $term_id;
			}
			
			wp_list_categories( $args );
			?>
		</ul>
	
	<?php if( is_active_sidebar( 'kb-sidebar' ) ) : ?>
	
		<hr />
		
		<div class="kb-widgets">
		
			<?php dynamic_sidebar( 'kb-sidebar' ); ?>
		
		</div>
	
	<?php endif; ?>

</div><!-- end .sidebar -->