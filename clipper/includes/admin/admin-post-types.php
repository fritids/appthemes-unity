<?php
/**
 * Custom post types and taxonomies
 *
 *
 * @version 1.0
 * @author AppThemes
 *
 */



// Define the custom post types
add_action( 'init', 'clpr_post_type', 0 );

// remove_action('init', 'create_builtin_taxonomies', 0); // in case we want to remove all default WP taxonomies

// register all the custom taxonomies and custom post type
function clpr_post_type() {
	global $wpdb, $app_abbr; //need $wpdb!!

	// get the slug value for the ad custom post type & taxonomies
    if(get_option($app_abbr.'_coupon_permalink')) $post_type_base_url = get_option($app_abbr.'_coupon_permalink'); else $post_type_base_url = 'coupon';
    if(get_option($app_abbr.'_coupon_cat_tax_permalink')) $cat_tax_base_url = get_option($app_abbr.'_coupon_cat_tax_permalink'); else $cat_tax_base_url = 'coupon-category';
  	if(get_option($app_abbr.'_coupon_type_tax_permalink')) $type_tax_base_url = get_option($app_abbr.'_coupon_type_tax_permalink'); else $type_tax_base_url = 'coupon-type';
  	if(get_option($app_abbr.'_coupon_store_tax_permalink')) $store_tax_base_url = get_option($app_abbr.'_coupon_store_tax_permalink'); else $store_tax_base_url = 'store';
    if(get_option($app_abbr.'_coupon_tag_tax_permalink')) $tag_tax_base_url = get_option($app_abbr.'_coupon_tag_tax_permalink'); else $tag_tax_base_url = 'coupon-tag';
    if(get_option($app_abbr.'_coupon_image_tax_permalink')) $image_tax_base_url = get_option($app_abbr.'_coupon_image_tax_permalink'); else $image_tax_base_url = 'coupon-image';

    register_post_type( APP_POST_TYPE,
            array(	'labels' => array(
							'name' => __( 'Coupons', APP_TD ),
							'singular_name' => __( 'Coupons', APP_TD ),
							'add_new' => __( 'Add New', APP_TD ),
							'add_new_item' => __( 'Add New Coupon', APP_TD ),
							'edit' => __( 'Edit', APP_TD ),
							'edit_item' => __( 'Edit Coupon', APP_TD ),
							'new_item' => __( 'New Coupon', APP_TD ),
							'view' => __( 'View Coupons', APP_TD ),
							'view_item' => __( 'View Coupon', APP_TD ),
							'search_items' => __( 'Search Coupons', APP_TD ),
							'not_found' => __( 'No coupons found', APP_TD ),
							'not_found_in_trash' => __( 'No coupons found in trash', APP_TD ),
							'parent' => __( 'Parent Coupon', APP_TD ),
                    ),
                    'description' => __( 'This is where you can create new coupon listings on your site.', APP_TD ),
                    'public' => true,
                    'show_ui' => true,
                    'capability_type' => 'post',
                    'publicly_queryable' => true,
                    'exclude_from_search' => false,
                    'menu_position' => 8,
                    'menu_icon' => get_template_directory_uri() . '/images/site_icon.png',
                    'hierarchical' => false,
                    'rewrite' => array( 'slug' => $post_type_base_url, 'with_front' => false ), /* Slug set so that permalinks work when just showing post name */
                    'query_var' => true,
					'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky' )
            )
    );

    // register post status for unreliable coupons
    register_post_status( 'unreliable', 
            array(  'label' => __( 'Unreliable', APP_TD ),
                    'public' => true,
                    '_builtin' => true,
                    'label_count' => _n_noop( 'Unreliable <span class="count">(%s)</span>', 'Unreliable <span class="count">(%s)</span>', APP_TD ),
                    'show_in_admin_all_list' => true,
                    'show_in_admin_status_list' => true,
                    'capability_type' => APP_POST_TYPE,
                  )
            );

  	// register the newcategory taxonomy
    register_taxonomy( APP_TAX_CAT,
            array( APP_POST_TYPE ),
            array(	'hierarchical' => true,
                    'labels' => array(
							'name' => __( 'Categories', APP_TD ),
							'singular_name' => __( 'Coupon Category', APP_TD ),
							'search_items' =>  __( 'Search Coupon Categories', APP_TD ),
							'all_items' => __( 'All Coupon Categories', APP_TD ),
							'parent_item' => __( 'Parent Coupon Category', APP_TD ),
							'parent_item_colon' => __( 'Parent Coupon Category:', APP_TD ),
							'edit_item' => __( 'Edit Coupon Category', APP_TD ),
							'update_item' => __( 'Update Coupon Category', APP_TD ),
							'add_new_item' => __( 'Add New Coupon Category', APP_TD ),
							'new_item_name' => __( 'New Coupon Category Name', APP_TD )
                    ),
                    'show_ui' => true,
                    'query_var' => true,
					'update_count_callback' => '_update_post_term_count',
                    'rewrite' => array( 'slug' => $cat_tax_base_url, 'with_front' => false, 'hierarchical' => true ), 
            )
    );

	register_taxonomy( APP_TAX_TAG,
            array( APP_POST_TYPE ),
            array(	'hierarchical' => false,
                    'labels' => array(
                            'name' => __( 'Coupon Tags', APP_TD ),
                            'singular_name' => __( 'Coupon Tag', APP_TD ),
                            'search_items' =>  __( 'Search Coupon Tags', APP_TD ),
                            'all_items' => __( 'All Coupon Tags', APP_TD ),
                            'edit_item' => __( 'Edit Coupon Tag', APP_TD ),
                            'update_item' => __( 'Update Coupon Tag', APP_TD ),
                            'add_new_item' => __( 'Add New Coupon Tag', APP_TD ),
                            'add_or_remove_items' => __( 'Add or remove Coupon Tags', APP_TD ),
                            'separate_items_with_commas' => __( 'Separate Coupon Tags with commas', APP_TD ),
                            'choose_from_most_used' => __( 'Choose from the most common Coupon Tags', APP_TD ),
                            'new_item_name' => __( 'New Coupon Tag Name', APP_TD )
                    ),
                    'show_ui' => true,
                    'query_var' => true,
					'update_count_callback' => '_update_post_term_count',
                    'rewrite' => array( 'slug' => $tag_tax_base_url, 'with_front' => false, 'hierarchical' => true ), 
            )
    );

    register_taxonomy( APP_TAX_STORE,
            array( APP_POST_TYPE ),
            array(	'hierarchical' => true,
                    'labels' => array(
                            'name' => __( 'Stores', APP_TD ),
                            'singular_name' => __( 'Store', APP_TD ),
                            'search_items' =>  __( 'Search Stores', APP_TD ),
                            'all_items' => __( 'All Stores', APP_TD ),
                            'edit_item' => __( 'Edit Store', APP_TD ),
                            'update_item' => __( 'Update Store', APP_TD ),
                            'add_new_item' => __( 'Add New Store', APP_TD ),
                            'add_or_remove_items' => __( 'Add or remove Stores', APP_TD ),
                            'separate_items_with_commas' => __( 'Separate Stores with commas', APP_TD ),
                            'choose_from_most_used' => __( 'Choose from the most common Stores', APP_TD ),
                            'new_item_name' => __( 'New Store Name', APP_TD )
                    ),
                    'show_ui' => true,
                    'query_var' => true,
					'update_count_callback' => '_update_post_term_count',
                    'rewrite' => array( 'slug' => $store_tax_base_url, 'with_front' => false, 'hierarchical' => true ),
            )
    );   

    register_taxonomy( APP_TAX_TYPE,
            array( APP_POST_TYPE ),
            array(	'hierarchical' => true,
                    'labels' => array(
                            'name' => __( 'Coupon Types', APP_TD ),
                            'singular_name' => __( 'Coupon Type', APP_TD ),
                            'search_items' =>  __( 'Search Coupon Types', APP_TD ),
                            'all_items' => __( 'All Coupon Types', APP_TD ),
                            'parent_item' => __( 'Parent Coupon Type', APP_TD ),
                            'parent_item_colon' => __( 'Parent Coupon Type:', APP_TD ),
                            'edit_item' => __( 'Edit Coupon Type', APP_TD ),
                            'update_item' => __( 'Update Coupon Type', APP_TD ),
                            'add_new_item' => __( 'Add New Coupon Type', APP_TD ),
                            'new_item_name' => __( 'New Coupon Type Name', APP_TD )
                    ),
                    'show_ui' => true,
                    'query_var' => true,
					'update_count_callback' => '_update_post_term_count',
                    'rewrite' => array( 'slug' => $type_tax_base_url, 'with_front' => false, 'hierarchical' => true ),
            )
    );

    // register taxonomy for printable coupon images
    register_taxonomy( APP_TAX_IMAGE,
            array( 'attachment' ),
            array(	'hierarchical' => false,
                    'labels' => array(
                            'name' => __( 'Coupon Images', APP_TD ),
                            'singular_name' => __( 'Coupon Image', APP_TD ),
                            'search_items' =>  __( 'Search Coupon Images', APP_TD ),
                            'all_items' => __( 'All Coupon Images', APP_TD ),
                            'parent_item' => __( 'Parent Coupon Image', APP_TD ),
                            'parent_item_colon' => __( 'Parent Coupon Image:', APP_TD ),
                            'edit_item' => __( 'Edit Coupon Image', APP_TD ),
                            'update_item' => __( 'Update Coupon Image', APP_TD ),
                            'add_new_item' => __( 'Add New Coupon Image', APP_TD ),
                            'new_item_name' => __( 'New Coupon Image Name', APP_TD )
                    ),
                    'public' => false,
                    'show_ui' => false,
                    'query_var' => true,
          					'update_count_callback' => '_update_post_term_count',
                    'rewrite' => array( 'slug' => $image_tax_base_url, 'with_front' => false, 'hierarchical' => false ),
            )
    );


	$wpdb->storesmeta = $wpdb->clpr_storesmeta;

	// this needs to happen once after install script first runs
	if ( get_option( $app_abbr.'_rewrite_flush_flag' ) == 'true' ) {
		flush_rewrite_rules();
		delete_option( $app_abbr.'_rewrite_flush_flag' );
	}

}



/**
*
* start edit coupon stores page
*
*/

// display the custom url meta field for the stores taxonomy
function clpr_edit_stores( $tag, $taxonomy ) {
	$the_store_url = get_metadata($tag->taxonomy, $tag->term_id, 'clpr_store_url', true);
	$the_store_aff_url = get_metadata($tag->taxonomy, $tag->term_id, 'clpr_store_aff_url', true);
	$the_store_active = get_metadata($tag->taxonomy, $tag->term_id, 'clpr_store_active', true);
	$the_store_aff_url_clicks = get_metadata($tag->taxonomy, $tag->term_id, 'clpr_aff_url_clicks', true);
	// $clpr_store_image_url = get_metadata($tag->taxonomy, $tag->term_id, 'clpr_store_image_url', true);
	$clpr_store_image_id = get_metadata($tag->taxonomy, $tag->term_id, 'clpr_store_image_id', true);
	$clpr_store_image_preview = clpr_get_store_image_url($tag->term_id, 'term_id', 75);
?>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_url"><?php _e( 'Store URL', APP_TD ); ?></label></th>
		<td>
			<input type="text" name="clpr_store_url" id="clpr_store_url" value="<?php echo $the_store_url; ?>"/><br />
			<p class="description"><?php _e( 'The URL for the store (i.e. http://www.website.com)', APP_TD ); ?></p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_aff_url"><?php _e( 'Destination URL', APP_TD ); ?></label></th>
		<td>
			<input type="text" name="clpr_store_aff_url" id="clpr_store_aff_url" value="<?php echo $the_store_aff_url; ?>"/><br />
			<p class="description"><?php _e( 'The affiliate URL for the store (i.e. http://www.website.com/?affid=12345)', APP_TD ); ?></p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_aff_url_cloaked"><?php _e( 'Display URL', APP_TD ); ?></label></th>
		<td><?php echo clpr_get_store_out_url( $tag ); ?></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_aff_url_clicks"><?php _e( 'Clicks', APP_TD ); ?></label></th>
		<td><?php echo esc_attr( $the_store_aff_url_clicks ); ?></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_active"><?php _e( 'Store Active', APP_TD ); ?></label></th>
		<td>
			<select class="postform" id="clpr_store_active" name="clpr_store_active" style="min-width:125px;">
				<option value="yes" <?php if ($the_store_active == 'yes') echo 'selected = selected'; ?>><?php _e( 'Yes', APP_TD ); ?></option>
				<option value="no" <?php if ($the_store_active == 'no') echo 'selected = selected'; ?>><?php _e( 'No', APP_TD ); ?></option>
			</select>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_url"><?php _e( 'Store Screenshot', APP_TD ); ?></label></th>
		<td>     			
			<span class="thumb-wrap">
				<a href="<?php echo $the_store_url; ?>" target="_blank"><img class="store-thumb" src="<?php echo clpr_get_store_image_url($tag->term_id, 'term_id', '250'); ?>" alt="" /></a>
			</span>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_image_id"><?php _e( 'Store Image', APP_TD ); ?></label></th>
		<td>
			<div id="stores_image" style="float:left; margin-right:15px;"><img src="<?php echo $clpr_store_image_preview; ?>" width="75px" height="75px" /></div>
			<div style="line-height:75px;">
				<input type="hidden" name="clpr_store_image_id" id="clpr_store_image_id" value="<?php echo $clpr_store_image_id; ?>" />
				<button type="submit" class="button" id="button_add_image" rel="clpr_store_image_url"><?php _e( 'Add Image', APP_TD ); ?></button>
				<button type="submit" class="button" id="button_remove_image"><?php _e( 'Remove Image', APP_TD ); ?></button>
			</div>
			<div class="clear"></div>
			<p class="description"><?php _e( 'Choose custom image for the store.', APP_TD ); ?></p>
			<p class="description"><?php _e( 'Leave blank if you want use image generated by store URL.', APP_TD ); ?></p>
		</td>
	</tr>
	<script type="text/javascript">
	//<![CDATA[	
	jQuery(document).ready(function() {

	  var formfield;

		if ( ! jQuery('#clpr_store_image_id').val() ) {
			jQuery('#button_remove_image').hide();
		} else {
			jQuery('#button_add_image').hide();
		}

		jQuery('#button_add_image').live('click', function() {
			formfield = jQuery(this).attr('rel');
			tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
			return false;
		});

		jQuery('#button_remove_image').live('click', function() {
			jQuery('#stores_image img').attr('src', '<?php bloginfo('template_directory'); ?>/images/clpr_default.jpg');
			jQuery('#clpr_store_image_id').val('');
			jQuery('#button_remove_image').hide();
			jQuery('#button_add_image').show();
			return false;
		});

		window.original_send_to_editor = window.send_to_editor;

		window.send_to_editor = function(html) {
			if ( formfield ) {
  			var imageClass = jQuery('img', html).attr('class');
  			var imageID = parseInt(/wp-image-(\d+)/.exec(imageClass)[1], 10);
  			var imageURL = jQuery('img', html).attr('src');

  			jQuery('input[name=clpr_store_image_id]').val(imageID);
				jQuery('#stores_image img').attr('src', imageURL);
				jQuery('#button_remove_image').show();
				jQuery('#button_add_image').hide();
  			tb_remove();
  			formfield = null;
  		} else {
        window.original_send_to_editor(html);
      }
		}

	});
	//]]>
	</script>

<?php
}
add_action( 'stores_edit_form_fields', 'clpr_edit_stores', 10, 2 );


// save the store url custom meta field
function clpr_save_stores( $term_id, $tt_id ) {
	if ( ! $term_id )
		return;

	if ( isset( $_POST['clpr_store_image_id'] ) && is_numeric( $_POST['clpr_store_image_id'] ) )
		update_metadata( $_POST['taxonomy'], $term_id, 'clpr_store_image_id', $_POST['clpr_store_image_id'] );

	if ( isset( $_POST['clpr_store_url'] ) )
		update_metadata( $_POST['taxonomy'], $term_id, 'clpr_store_url', $_POST['clpr_store_url'] );

	if ( isset( $_POST['clpr_store_aff_url'] ) )
		update_metadata( $_POST['taxonomy'], $term_id, 'clpr_store_aff_url', $_POST['clpr_store_aff_url'] );

	if ( isset( $_POST['clpr_store_active'] ) )
		update_metadata( $_POST['taxonomy'], $term_id, 'clpr_store_active', $_POST['clpr_store_active'] );

}
add_action( 'edited_stores', 'clpr_save_stores', 10, 2 );



/**
*
* start coupon stores create new page
*
*/

// setup the stores taxonomy headers
function clpr_stores_column_headers($columns){
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'clpr_store_image' => __( 'Image', APP_TD ),
		'name' => __( 'Name', APP_TD ),	
		'short_description' => __( 'Description', APP_TD ),
		'clpr_store_url' => __( 'Store URL', APP_TD ),
		'clpr_store_aff_url' => __( 'Destination URL', APP_TD ),
		'slug' => __( 'Slug', APP_TD ),
		'clpr_store_active' => __( 'Active', APP_TD ),
		'posts' => __( 'Coupons', APP_TD ),
		//'clpr_store_clicks' => __( 'Clicks', APP_TD )
	);	
	return $columns;	
}
add_filter('manage_edit-stores_columns', 'clpr_stores_column_headers', 10, 1);


// check the column name then pull in the row data using get_metadata()
function clpr_stores_column_row( $row_content, $column_name, $term_id ) {
	global $taxonomy;

	switch( $column_name ) {

		case 'clpr_store_image':
			return '<img class="store-thumb" src="' . clpr_get_store_image_url($term_id, 'term_id', 75) . '" width="75px" height="75px" />';
			break;

		case 'short_description':
			$string = strip_tags( term_description( $term_id, $taxonomy ) );
			if ( strlen( $string ) > 250 )
				$string = mb_substr( $string, 0, 250 ) . '...';
			return $string;
			break;

		case 'clpr_store_url':
			return get_metadata(APP_TAX_STORE, $term_id, 'clpr_store_url', true);
			break;

		case 'clpr_store_aff_url':
			return get_metadata(APP_TAX_STORE, $term_id, 'clpr_store_aff_url', true);
			break;

		case 'clpr_store_active':
			$store_active = get_metadata(APP_TAX_STORE, $term_id, 'clpr_store_active', true);
			if ( $store_active == 'no' )
				return '<span class="active-no">' . __( 'No', APP_TD ) . '</span>';
			else
				return '<span class="active-yes">' . __( 'Yes', APP_TD ) . '</span>';
			break;

		case 'clpr_store_clicks':
			$clicks = get_metadata(APP_TAX_STORE, $term_id, 'clpr_aff_url_clicks', true);
			$clicks = $clicks ? $clicks : 0;
			return $clicks;
			break;

		default:
			break;

	}

}
add_filter( 'manage_stores_custom_column', 'clpr_stores_column_row', 10, 3 );


// register the short_description column as sortable
function clpr_column_stores_sortable( $columns ) {
	$columns['short_description'] = 'description';
	return $columns;
}
add_filter( 'manage_edit-stores_sortable_columns', 'clpr_column_stores_sortable' );


// save the store url on the edit-tags.php create page
function create_stores( $term_id, $tt_id ) {
	if ( ! $term_id )
		return;

	if ( isset( $_POST['clpr_store_image_id'] ) && is_numeric( $_POST['clpr_store_image_id'] ) )
		update_metadata( $_POST['taxonomy'], $term_id, 'clpr_store_image_id', $_POST['clpr_store_image_id'] );

	if ( isset( $_POST['clpr_store_url'] ) )
		update_metadata( $_POST['taxonomy'], $term_id, 'clpr_store_url', $_POST['clpr_store_url'] );

}
add_action( 'created_stores', 'create_stores', 10, 3 );

// end coupon stores create new page



/**
*
* start the coupon listing edit page
*
*/

// define columns for coupon listing on edit.php page
function clpr_edit_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Coupon Title', APP_TD ),
		'author' => __( 'Submitted By', APP_TD ),
		APP_TAX_STORE => __( 'Store Name', APP_TD ),
		APP_TAX_CAT => __( 'Categories', APP_TD ),
		APP_TAX_TYPE => __( 'Coupon Type', APP_TD ),
		'coupon_code' => __( 'Coupon', APP_TD ),
		'comments' => '<div class="vers"><img alt="" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>',
		'date' => __( 'Date', APP_TD ),
		//'expiration_date' => __( 'Expiration Date', APP_TD ),
		'votes' => __( 'Votes', APP_TD ),
		'clicks' => __( 'Clicks / Views', APP_TD ),
		'ctr' => __( 'CTR', APP_TD )
	);
	return $columns;
}
add_filter( 'manage_edit-coupon_columns', 'clpr_edit_columns' );


// register the columns as sortable
function clpr_column_sortable( $columns ) {
	$columns['coupon_code'] = 'coupon_code';
	return $columns;
}
add_filter( 'manage_edit-coupon_sortable_columns', 'clpr_column_sortable' );


// set how the columns sorting should work
function clpr_column_orderby( $vars ) {

	if ( isset( $vars['orderby'] ) ) {
		switch ( $vars['orderby'] ) {
			case 'coupon_code' :
				$vars = array_merge( $vars, array( 'meta_key' => 'clpr_coupon_code', 'orderby' => 'meta_value' ) );
				break;
		}
	}

	return $vars;
}
add_filter( 'request', 'clpr_column_orderby' );


// return the values for each coupon column on edit.php page
function clpr_custom_columns( $column ) {
	global $post;

	$coupon_type = appthemes_get_custom_taxonomy($post->ID, APP_TAX_TYPE, 'slug_name');
	switch ( $column ) {
		// Store and type for WP to store
		case APP_TAX_STORE :
			echo get_the_term_list($post->ID, APP_TAX_STORE, '', ', ', '');
			break;

		case APP_TAX_CAT :
			echo get_the_term_list($post->ID, APP_TAX_CAT, '', ', ', '');
			break;

		case APP_TAX_TYPE :
			echo get_the_term_list($post->ID, APP_TAX_TYPE, '', ', ', '');
			break;

		//describe the other fields for WP to store
		case 'coupon_code':
			if ( $coupon_type == 'coupon-code' )
				echo esc_html( get_post_meta($post->ID, 'clpr_coupon_code', true) );
			elseif ( $coupon_type == 'printable-coupon' )
				clpr_get_coupon_image( 'thumb-med' );
			else
				_e( 'No code', APP_TD );
			break;

		case 'expiration_date':
			echo esc_html( get_post_meta($post->ID, 'clpr_expire_date', true) );
			break;

		case 'votes':
			clpr_votes_chart();
			break;

		case 'clicks':
			$clicks = (int) get_post_meta($post->ID, 'clpr_coupon_aff_clicks', true);
			$views = (int) get_post_meta($post->ID, 'clpr_total_count', true);
			echo number_format_i18n($clicks) . ' / <strong>' . number_format_i18n($views). '</strong>';
			break;

		case 'ctr':
			$clicks = (int) get_post_meta($post->ID, 'clpr_coupon_aff_clicks', true);
			$views = (int) get_post_meta($post->ID, 'clpr_total_count', true);
			$ctr = ($views > 0 ? ($clicks/$views*100) : 0);
			echo number_format_i18n($ctr, 2).'%';
			break;
	}
}
add_action( 'manage_posts_custom_column', 'clpr_custom_columns' );

// end coupon listing page



// add a thumbnail column to the blog edit posts screen
function clpr_post_thumbnail_column( $cols ) {
	$cols['thumbnail'] = __( 'Thumbnail', APP_TD );
	return $cols;
}
add_filter( 'manage_edit-' . APP_POST_TYPE . '_columns', 'clpr_post_thumbnail_column' );


// go get the attached images for the post thumbnail columns
function clpr_thumbnail_value( $column_name, $post_id ) {
	if ( 'thumbnail' == $column_name ) {
		if ( has_post_thumbnail( $post_id ) )
			echo get_the_post_thumbnail( $post_id, 'post-thumbnail' );
	}
}
add_action( 'manage_posts_custom_column', 'clpr_thumbnail_value', 10, 2 );


// add extra fields to the create store admin page
function add_store_extra_fields( $tag ) {
?>

	<div class="form-field">
		<label for="clpr_store_url"><?php _e( 'Store URL', APP_TD ); ?></label>
		<input type="text" name="clpr_store_url" id="clpr_store_url" value="" />
		<p class="description"><?php _e( 'The URL for the store (i.e. http://www.website.com)', APP_TD ); ?></p>
	</div>

	<div class="form-field">
		<label for="clpr_store_image_id"><?php _e( 'Store Image', APP_TD ); ?></label>
		<div id="stores_image" style="float:left; margin-right:15px;"><img src="<?php bloginfo('template_directory'); ?>/images/clpr_default.jpg" width="75px" height="75px" /></div>
		<div style="line-height:75px;">
			<input type="hidden" name="clpr_store_image_id" id="clpr_store_image_id" value="" />
			<button type="submit" class="button" id="button_add_image" rel="clpr_store_image_url"><?php _e( 'Add Image', APP_TD ); ?></button>
			<button type="submit" class="button" id="button_remove_image"><?php _e( 'Remove Image', APP_TD ); ?></button>
		</div>
		<div class="clear"></div>
		<p class="description"><?php _e( 'Choose custom image for the store.', APP_TD ); ?></p>
		<p class="description"><?php _e( 'Leave blank if you want use image generated by store URL.', APP_TD ); ?></p>
	</div>
	<script type="text/javascript">
	//<![CDATA[	
	jQuery(document).ready(function() {

	  var formfield;

		if ( ! jQuery('#clpr_store_image_id').val() ) {
			jQuery('#button_remove_image').hide();
		} else {
			jQuery('#button_add_image').hide();
		}

		jQuery('#button_add_image').live('click', function() {
			formfield = jQuery(this).attr('rel');
			tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
			return false;
		});

		jQuery('#button_remove_image').live('click', function() {
			jQuery('#stores_image img').attr('src', '<?php bloginfo('template_directory'); ?>/images/clpr_default.jpg');
			jQuery('#clpr_store_image_id').val('');
			jQuery('#button_remove_image').hide();
			jQuery('#button_add_image').show();
			return false;
		});

		window.original_send_to_editor = window.send_to_editor;

		window.send_to_editor = function(html) {
			if ( formfield ) {
  			var imageClass = jQuery('img', html).attr('class');
  			var imageID = parseInt(/wp-image-(\d+)/.exec(imageClass)[1], 10);
  			var imageURL = jQuery('img', html).attr('src');

  			jQuery('input[name=clpr_store_image_id]').val(imageID);
				jQuery('#stores_image img').attr('src', imageURL);
				jQuery('#button_remove_image').show();
				jQuery('#button_add_image').hide();
  			tb_remove();
  			formfield = null;
  		} else {
        window.original_send_to_editor(html);
      }
		}

	});
	//]]>
	</script>

<?php
}
add_action( 'stores_add_form_fields', 'add_store_extra_fields', 10, 2 );


// show fields in quick edit store mode. 
function clpr_quick_edit_values($column_name, $screen, $name = null) {

  if($name != APP_TAX_STORE && ($column_name != 'clpr_store_url' || $column_name != 'clpr_store_aff_url' || $column_name != 'clpr_store_active')) 
    return false;

  if($column_name == 'clpr_store_url'){
?>
	<fieldset>
		<div class="inline-edit-col">
		
			<label>
				<span class="title"><?php _e( 'Store URL', APP_TD ); ?></span>
				<span class="input-text-wrap"><input type="text" name="clpr_store_url" class="ptitle" value="" /></span>
			</label>
		
		</div>
	</fieldset>
<?php
  }
  if($column_name == 'clpr_store_aff_url'){
?>
	<fieldset>
		<div class="inline-edit-col">
		
			<label>
				<span class="title"><?php _e( 'Destination URL', APP_TD ); ?></span>
				<span class="input-text-wrap"><input type="text" name="clpr_store_aff_url" class="ptitle" value="" /></span>
			</label>
		
		</div>
	</fieldset>
<?php
  }
  if($column_name == 'clpr_store_active'){
?>
	<fieldset>
		<div class="inline-edit-col">
		
			<label>
				<span class="title"><?php _e( 'Active', APP_TD ); ?></span>
				<span class="input-text-wrap">
					<select class="postform" id="clpr_store_active" name="clpr_store_active" style="min-width:125px;">
						<option value="yes"><?php _e( 'Yes', APP_TD ); ?></option>
						<option value="no"><?php _e( 'No', APP_TD ); ?></option>
					</select>
				</span>
			</label>
			
		</div>
	</fieldset>
<?php
  }

}
add_action( 'quick_edit_custom_box', 'clpr_quick_edit_values', 10, 3 );


// enqueue script for quick edit stores
function stores_quick_edit_script() {
	global $pagenow;

	if ( $pagenow == 'edit-tags.php' && ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == APP_TAX_STORE ) && !isset( $_GET['action'] ) ) {
		wp_register_script( 'quick-edit-stores-js', get_bloginfo('template_directory') . '/includes/js/quick-edit-stores.js', array( 'jquery' ) );
		wp_enqueue_script( 'quick-edit-stores-js' );
	}
}
add_action( 'admin_enqueue_scripts', 'stores_quick_edit_script', 10, 1 );


class CLPR_Listing_Publish_Moderation extends APP_Meta_Box {

    public function __construct(){

        if( !isset( $_GET['post'] ) || get_post_status( $_GET['post'] ) != 'pending' )
            return;

        parent::__construct( 'listing-publish-moderation', __( 'Moderation Queue', APP_TD ), APP_POST_TYPE, 'side', 'high' );
    }

    function display( $post ){

        echo html( 'p', array(), __( 'You must approve this coupon before it can be published.', APP_TD ) );

        echo html( 'input', array(
            'type' => 'submit',
            'class' => 'button-primary',
            'value' => __( 'Accept', APP_TD ),
            'name' => 'publish',
            'style' => 'padding-left: 30px; padding-right: 30px; margin-right: 20px; margin-left: 15px;',
        ));

        echo html( 'a', array(
            'class' => 'button',
            'style' => 'padding-left: 30px; padding-right: 30px;',
            'href' => get_delete_post_link($post->ID),
        ), __( 'Reject', APP_TD ) );

        echo html( 'p', array(
                'class' => 'howto'
            ), __( 'Rejecting a Coupon sends it to the trash.', APP_TD ) );

    }

}
new CLPR_Listing_Publish_Moderation;
?>