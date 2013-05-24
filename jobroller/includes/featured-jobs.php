<?php
	global $featured_job_cat_id;

	$args = array();
	if ( $feat_term = get_term_by('id', $featured_job_cat_id, 'job_cat') ) {
		$args = array(
			'post_type'				=> 'job_listing',
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'tax_query'				=> array (
											'relation' => 'AND',
											array(
												'taxonomy' 	=> APP_TAX_CAT,
												'field' 	=> 'slug',
												 // featured job category slug
												'terms' 	=> $feat_term->slug
											),
									   ),
			'posts_per_page'		=> -1,
			// disable 'post_where' filter for featured jobs
			'suppress_filters' 		=> TRUE
		);

		// query featured jobs for the current taxonomy (if any)
		if ( is_tax() ) {
			$term = get_queried_object();
			$tax = array(
					'taxonomy' 	=> $term->taxonomy,
					'field' 	=> 'slug',
					'terms' 	=> $term->slug
			);
			$args['tax_query'][] = $tax;
		}
	};

	$my_query = new WP_Query($args);
	$found = false;
	ob_start();
?>	
<?php if ($my_query->have_posts()) : $alt = 1; echo '<div class="section"><h2 class="pagetitle"><small class="rss"><a href="'.get_term_feed_link($featured_job_cat_id, 'job_cat').'"><img src="'.get_bloginfo('template_url').'/images/feed.png" title="'.__('Featured Jobs RSS Feed',APP_TD).'" alt="'.__('Featured Jobs RSS Feed',APP_TD).'" /></a></small> '.__('Featured Jobs',APP_TD).'</h2><ol class="jobs">'; while ($my_query->have_posts()) : $my_query->the_post(); 
	
	$post_class = array('job', 'job-featured');
	
	$expired = jr_check_expired($my_query->post); 
	if ($expired) :
		continue;
	endif;
	
	$found = true;
	
	$alt=$alt*-1; 
	
	if ($alt==1) $post_class[] = 'job-alt';
	if ( is_object_in_term( $my_query->post->ID, 'job_cat', array($featured_job_cat_id) ) ) $post_class[] = 'job-featured';
	
	?>
	
	<li class="<?php echo implode(' ', $post_class); ?>"><dl>
		<dt><?php _e('Type',APP_TD); ?></dt>
		<dd class="type"><?php				
			$job_types = get_terms( 'job_type', array( 'hide_empty' => '0' ) );
			if ($job_types && sizeof($job_types) > 0) {
				foreach ($job_types as $type) {
					if ( is_object_in_term( $my_query->post->ID, 'job_type', array( $type->term_id ) ) ) {
						echo '<span class="ftype '.$type->slug.'">'.$type->name.'</span>';
						break;
					}
				}
			}				
		?>&nbsp;</dd>
		<dt><?php _e('Job',APP_TD); ?></dt>
		<dd class="title"><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>			
			<?php if (get_post_meta($post->ID, '_Company', true)) : ?>
						
				<?php if ($compurl = get_post_meta($my_query->post->ID, '_CompanyURL', true)) { ?>
					<a href="<?php echo wptexturize(get_post_meta($my_query->post->ID, '_CompanyURL', true)); ?>" rel="nofollow"><?php echo wptexturize(get_post_meta($post->ID, '_Company', true)); ?></a>
				<?php } else { ?>
					<?php echo wptexturize(get_post_meta($my_query->post->ID, '_Company', true)); ?>
				<?php } ?>
				
				<?php 
					$author = get_user_by('id', $my_query->post->post_author);
					if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) echo sprintf( __(' &ndash; Posted by <a href="%s">%s</a>', APP_TD), $link, $author->display_name );
				?> 
			
			<?php else : ?>
			
				<?php 
					$author = get_user_by('id', $my_query->post->post_author);
					if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) echo sprintf( __('<a href="%s">%s</a>', APP_TD), $link, $author->display_name );
				?> 
			
			<?php endif; ?>
			
		</dd>
		<dt><?php _e('Location', APP_TD); ?></dt>
        <dd class="location"><strong><?php if ($address = get_post_meta($my_query->post->ID, 'geo_short_address', true)) echo wptexturize($address); else _e('Anywhere',APP_TD); ?></strong> <?php echo wptexturize(get_post_meta($my_query->post->ID, 'geo_short_address_country', true)); ?></dd>
		<dt><?php _e('Date Posted',APP_TD); ?></dt>
		<dd class="date"><strong><?php echo date_i18n(__('j M',APP_TD), strtotime($my_query->post->post_date)); ?></strong> <span class="year"><?php echo date_i18n(__('Y',APP_TD), strtotime($my_query->post->post_date)); ?></span></dd>
	</dl></li>
	
<?php 
	endwhile; 
		echo '</ol></div><!-- End section -->'; 	
	endif; 
	
	// Prevents empty list
	if ($found) {
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
	} else {
		ob_end_clean();
	}
