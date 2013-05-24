<?php
/**
 * JobRoller Application Process
 * Processes a job application sent via the form in a post.
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_process_filter_form() {
	
	global $wp_query, $featured_job_cat_id, $find_posts_in, $search_result_ids; 
	
	if ( get_query_var('paged') ) {
	        $paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
	        $paged = get_query_var('page');
	} else {
	        $paged = 1;
	}
	
	$args = array(
		'post_type'	=> 'job_listing',
		'post_status' => 'publish',
		'paged' => $paged
	);
	
	$cats = array();
	$filter_args = array();

	if (isset($_GET['action']) && $_GET['action']=='Filter') {

		$job_types = get_terms( 'job_type', array( 'hide_empty' => '0' ) );
		if ($job_types && sizeof($job_types) > 0) {
			foreach ($job_types as $type) {
				if (isset($_GET[$type->slug])) {
					// Filter is ON
					$cats[] = $type->term_id;
				}
			}
		}
		
		if (sizeof($cats)==0) {
			$cats = array(0);
		}
		
		$post_ids = get_objects_in_term( $cats, 'job_type' );

		// If we are doing location search, find common ids
		if (isset($find_posts_in) && is_array($find_posts_in)) $post_ids = array_intersect($post_ids, $find_posts_in);
		if (isset($search_result_ids) && is_array($search_result_ids)) $post_ids = array_intersect($post_ids, $search_result_ids);
		
		$post_ids[] = 0;

		// Merge with query args
		$filter_args = array(
			'post__in'	=> $post_ids,
		);		
		$args = array_merge($filter_args, $args);

	} elseif (isset($find_posts_in) && is_array($find_posts_in)) {
		if (isset($search_result_ids) && is_array($search_result_ids)) $find_posts_in = array_intersect($find_posts_in, $search_result_ids);

		$find_posts_in[] = 0;

		$filter_args = array(
			'post__in'	=> $find_posts_in
		);		
		$args = array_merge($filter_args, $args);
	} elseif (isset($search_result_ids) && is_array($search_result_ids)) {
		$filter_args = array(
			'post__in'	=> $search_result_ids,
		);		
		$args = array_merge($filter_args, $args);
	}

	return $args;

}

add_filter('posts_orderby', 'jr_posts_orderby');

// sort location queries by the same order as the ID's on the found posts array ($find_posts_in)
function jr_posts_orderby( $orderby ) {
	global $find_posts_in;

	if ( !empty( $_GET['location'] ) ) {
		if ( is_array( $find_posts_in ) ) {
			$posts_in = implode(',', $find_posts_in );
			$orderby = 'FIELD(ID, ' . $posts_in . ')';
		}
	}
	return $orderby;
}
