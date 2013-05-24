<?php

/**
* Update script.
* Only run if theme is being updated
* 
*
*/


function clpr_upgrade_all() {
	global $app_db_version, $app_version, $app_abbr;

	$current_db_version = get_option($app_abbr.'_db_version');

	if ( $current_db_version < 411 )
		clpr_upgrade_121();

	if ( $current_db_version < 412 )
		clpr_upgrade_122();

	if ( $current_db_version < 413 )
		clpr_upgrade_123();

	if ( $current_db_version < 414 )
		clpr_upgrade_124();

	if ( $current_db_version < 417 )
		clpr_upgrade_14();


	update_option($app_abbr.'_db_version', $app_db_version);
	//update_option($app_abbr.'_version', $app_version);
}
add_action('appthemes_first_run', 'clpr_upgrade_all');


/**
 * Execute changes made in Clipper 1.2.1.
 *
 * @since 1.2.1
 */
function clpr_upgrade_121(){
    global $wpdb, $app_abbr;
    
    if( get_option($app_abbr.'_upgrade_121') != 'done' ) {
      if( !$postids = get_option($app_abbr.'_upgrade_121') ){
        $qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts 
        WHERE $wpdb->posts.post_type = '".APP_POST_TYPE."'";

        $postids = $wpdb->get_col($qryToString);
      }
    } else {
      $postids = false;
    }

    if ($postids) {
      $i = 0;
      $left_posts = $postids;

      foreach ($postids as $key => $id) {
        $i++;
        unset($left_posts[$key]);

      	if( get_post_meta($id, 'clpr_votes_up') == false )
      		update_post_meta($id, 'clpr_votes_up', 0);

      	if( get_post_meta($id, 'clpr_votes_down') == false )
      		update_post_meta($id, 'clpr_votes_down', 0);

      	if( get_post_meta($id, 'clpr_expire_date') == false )
      		update_post_meta($id, 'clpr_expire_date', '');
      
        if( ($i > 100) || (count($left_posts) < 1) ){
          update_option($app_abbr.'_upgrade_121', $left_posts);

          if(count($left_posts) < 1)
            update_option($app_abbr.'_upgrade_121', 'done');

          wp_redirect( admin_url('admin.php?page=settings&firstrun=1') );
          exit;

        }
      }
    }else{
      update_option($app_abbr.'_db_version', '411');
    }
}

/**
 * Execute changes made in Clipper 1.2.2.
 *
 * @since 1.2.2
 */
function clpr_upgrade_122(){
    global $wpdb, $app_abbr;

    // create term for printable coupon images
  	$image_tax = ( array( 'slug' => 'printable-coupon' ) );
  	if (!get_term_by( 'slug', 'printable-coupon', APP_TAX_IMAGE))
      wp_insert_term('Printable Coupon', APP_TAX_IMAGE, $image_tax);

    // update old printable coupon images
    $term = get_term_by( 'slug', 'printable-coupon', APP_TAX_IMAGE);

    $qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts 
    INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
    WHERE 1=1 AND ( $wpdb->term_relationships.term_taxonomy_id IN ($term->term_id) )
    AND $wpdb->posts.post_type = '".APP_POST_TYPE."'";

    $postids = $wpdb->get_col($qryToString);

    if ($postids) foreach ($postids as $id) {

      $images = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );
      if ($images) {
     		// move over bacon
    		$image = array_shift($images);
        wp_set_object_terms($image->ID, 'printable-coupon', APP_TAX_IMAGE, false);
      }
    
    }

    update_option($app_abbr.'_db_version', '412');

}

/**
 * Execute changes made in Clipper 1.2.3.
 *
 * @since 1.2.3
 */
function clpr_upgrade_123() {
	global $wpdb, $app_abbr;

	if ( get_option($app_abbr.'_upgrade_123') != 'done' ) {
		if ( !$postids = get_option($app_abbr.'_upgrade_123') ) {
			$qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts 
			WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = '".APP_POST_TYPE."'";

			$postids = $wpdb->get_col($qryToString);
		}
	} else {
		$postids = false;
	}

	if ( $postids ) {
		$i = 0;
		$left_posts = $postids;

		foreach ($postids as $key => $id) {
			$i++;
			unset($left_posts[$key]);

			$t = time();
			$votes_down = get_post_meta($id, 'clpr_votes_down', true);
			$votes_percent = get_post_meta($id, 'clpr_votes_percent', true);
			$expire_date = get_post_meta($id, 'clpr_expire_date', true);
			if ( $expire_date != '' )
				$expire_date_time = strtotime( str_replace('-', '/', $expire_date) );
			else
				$expire_date_time = 0;

			if ( ($votes_percent < 50 && $votes_down != 0) || ($expire_date_time < $t && $expire_date != '') ) {
				$wpdb->update($wpdb->posts, array( 'post_status' => 'unreliable' ), array( 'ID' => $id ) );
			}

			if ( ($i > 100) || (count($left_posts) < 1) ) {
				update_option($app_abbr.'_upgrade_123', $left_posts);

				if ( count($left_posts) < 1 )
					update_option($app_abbr.'_upgrade_123', 'done');

				wp_redirect( admin_url('admin.php?page=settings&firstrun=1') );
				exit;

			}
		}
	} else {
		update_option($app_abbr.'_db_version', '413');
	}

}

/**
 * Execute changes made in Clipper 1.2.4.
 *
 * @since 1.2.4
 */
function clpr_upgrade_124() {
	global $app_abbr;
	// create term for promotional coupons without code
	$type_tax = ( array( 'slug' => 'promotion' ) );
	if ( !get_term_by( 'slug', 'promotion', APP_TAX_TYPE) )
		wp_insert_term('Promotion', APP_TAX_TYPE, $type_tax);

	update_option($app_abbr.'_db_version', '414');
}

/**
 * Execute changes made in Clipper 1.4.
 *
 * @since 1.4
 */
function clpr_upgrade_14() {
	global $wpdb, $app_abbr;

	// remove old table indexes
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	drop_index($wpdb->clpr_pop_daily, 'id');
	drop_index($wpdb->clpr_pop_total, 'id');

	// clean extra indexes
	add_clean_index($wpdb->clpr_storesmeta, 'stores_id');
	add_clean_index($wpdb->clpr_storesmeta, 'meta_key');
	add_clean_index($wpdb->clpr_report_comments, 'reportID');

	update_option($app_abbr.'_db_version', '417');
}

