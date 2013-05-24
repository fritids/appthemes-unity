<?php

$key = 'coupon_meta';

// add/remove meta boxes on the coupon edit admin page
function clpr_setup_meta_box() {
	global $key;

	add_meta_box( 'coupon-meta-box', __( 'Coupon Meta Fields', APP_TD ), 'clpr_custom_fields_meta_box', APP_POST_TYPE, 'normal', 'high' );
	add_meta_box( 'report-meta-box', __( 'Coupon Reports', APP_TD ), 'clpr_reports_meta_box', APP_POST_TYPE, 'normal', 'high' );
	
	// remove the stores metabox since we're using a drop-down instead
	remove_meta_box( 'tagsdiv-stores', APP_POST_TYPE, 'core' );	
	//remove_meta_box( 'storesdiv', APP_POST_TYPE, 'core' );
	remove_meta_box( 'coupon_typediv', APP_POST_TYPE, 'core' );
	
	//remove_meta_box( 'postcustom', APP_POST_TYPE, 'normal' ); 
	remove_meta_box( 'postexcerpt', APP_POST_TYPE, 'normal' ); 
	remove_meta_box( 'authordiv', APP_POST_TYPE, 'normal' ); 

  //custom post statuses
  //temporary hack until WP will fully support custom post statuses
	remove_meta_box( 'submitdiv', APP_POST_TYPE, 'core' );
  add_meta_box( 'submitdiv', __( 'Publish', APP_TD ), 'clpr_post_submit_meta_box', APP_POST_TYPE, 'side', 'high' );
		
}
add_action( 'admin_menu', 'clpr_setup_meta_box' );


// show the coupon meta fields in a custom meta box
function clpr_custom_fields_meta_box() {
	global $post, $meta_boxes, $key;
	
	// use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), $key . '_wpnonce', false, true );
?>

	<script type="text/javascript">	
	//<![CDATA[	
	jQuery(document).ready(function() {
	
	  var formfield;

		// upload printable coupon image
		jQuery('input#upload_image_button').click(function() {
		  formfield = jQuery(this).attr('rel');
			tb_show('', 'media-upload.php?post_id=<?php echo $post->ID; ?>&amp;type=image&amp;TB_iframe=true');
			return false;
		});

		window.original_send_to_editor = window.send_to_editor;

		// send the uploaded image url to the field 
		window.send_to_editor = function(html) {
  		if(formfield){
  			var s = jQuery('img',html).attr('class'); // get the class with the image id
  			var imageID = parseInt(/wp-image-(\d+)/.exec(s)[1], 10); // now grab the image id from the wp-image class
  			var imgurl = jQuery('img',html).attr('src'); // get the image url
  			var imgoutput = '<a href="' + imgurl + '" target="_blank"><img src="' + imgurl + '" /></a>'; //get the html to output for the image preview
			
  			jQuery('#clpr_print_url').val(imgurl); // return the image url to the field
  			jQuery('input[name=clpr_print_imageid]').val(imageID); // return the image url to the field			
  			jQuery('#clpr_print_url').siblings('.upload_image_preview').slideDown().html(imgoutput); // display the uploaded image thumbnail
  			tb_remove();
  			formfield = null;
  		}else{
        window.original_send_to_editor(html);
      }
		}
		
		// show the coupon code or upload coupon field based on type select box
		jQuery('select#coupon_meta_dropdown').change(function() {	
			if (jQuery(this).val() == 'coupon-code') {
				jQuery('tr#ctype-' + jQuery(this).val()).fadeIn('fast');
				jQuery('tr#ctype-coupon-code input').addClass('required');
				jQuery('tr#ctype-printable-coupon input').removeClass('required invalid');
				jQuery('tr#ctype-printable-coupon').hide();
			} else if (jQuery(this).val() == 'printable-coupon') {
				jQuery('tr#ctype-' + jQuery(this).val()).fadeIn('fast');
				jQuery('tr#ctype-printable-coupon input').addClass('required');
				jQuery('tr#ctype-coupon-code input').removeClass('required invalid');
				jQuery('tr#ctype-coupon-code').hide();
			} else {
				jQuery('tr.ctype').hide();
				jQuery('tr.ctype input').removeClass('required invalid');
			}		
		}).change(); 

	});	
	//]]>
	</script>	
	
	
	<table class="form-table coupon-meta-table">
		
			<tr>
				<th style="width:20%"><label for="cp_sys_ad_conf_id"><?php _e( 'Coupon Info', APP_TD ); ?>:</label></th>
				<td class="coupon-conf-id">
					<div id="coupon-id"><div id="keyico"></div><?php _e( 'Coupon ID', APP_TD ); ?>: <span>&nbsp;<?php echo esc_html(get_post_meta($post->ID, 'clpr_id', true)); ?>&nbsp;</span></div>
					<div id="coupon-stats"><div id="statsico"></div><?php _e( 'Views Today: ', APP_TD ); ?> <strong><?php echo esc_html( get_post_meta($post->ID, 'clpr_daily_count', true) ); ?></strong> | 
						<?php _e( 'Views Total: ', APP_TD ); ?> <strong><?php echo esc_html( $pvs = get_post_meta($post->ID, 'clpr_total_count', true) ); ?></strong>
					</div>
				
					<div id="coupon-stats"><div id="clicksico"></div>
						<?php _e( 'Clicks: ', APP_TD ); ?><strong><?php echo esc_html( $cts = get_post_meta( $post->ID, 'clpr_coupon_aff_clicks', true ) ); ?></strong> |
						<?php _e( 'CTR: ', APP_TD ); ?><strong><?php $ctr = ($pvs > 0 ? ($cts/$pvs*100) : 0); echo number_format_i18n($ctr, 2);  ?>%</strong>
					</div>
				</td>
			</tr>
			
			<tr>
				<th style="width:20%"><label><?php _e( 'Coupon Votes', APP_TD ); ?>:</label></th>
				<td><?php clpr_votes_chart(); ?></td>
			</tr>

			<tr>
				<th style="width:20%"><label><?php _e( 'Submitted By', APP_TD ); ?>:</label></th>
				<td style="line-height:3.4em;">
					<?php 
						// show the gravatar for the author
						echo get_avatar($post->post_author, $size = '48', $default = ''); 
						
						// show the author drop-down box 
						wp_dropdown_users(array(
							'who' => 'authors',
							'name' => 'post_author_override',
							'selected' => empty($post->ID) ? $user_ID : $post->post_author,
							'include_selected' => true
						));

						// display the author display name 
						$author = get_userdata($post->post_author);
						echo '<br/><a href="user-edit.php?user_id=' . $author->ID . '">' . $author->display_name . '</a>';
					?>
				</td>
			</tr>
			
			<tr>	
				<th colspan="2" style="padding:0px;">&nbsp;</th>
			</tr>				
			
			<tr>
				<th style="width:20%"><label><?php _e( 'Coupon Type', APP_TD ); ?>:</label></th>
				<td><input type="hidden" name="coupon_type" value="0" />
				<?php 
				// Get all taxonomy terms
				$terms = get_terms(APP_TAX_TYPE, 'hide_empty=0'); 
				$object_terms = wp_get_object_terms($post->ID, APP_TAX_TYPE);
				?>
				
				<select name="coupon_type" id="coupon_meta_dropdown">						
				<?php
				foreach ($terms as $term) {
					if (!is_wp_error($object_terms) && !empty($object_terms) && !strcmp($term->slug, $object_terms[0]->slug)) 
						echo "<option value='" . $term->slug . "' selected>" . $term->name . "</option>\n"; 
					else
						echo "<option value='" . $term->slug . "'>" . $term->name . "</option>\n"; 
				}
				?>					
				</select></td>
			</tr>
			
			<tr id="ctype-coupon-code" class="ctype">
				<th style="width:20%"><label><?php _e( 'Coupon Code', APP_TD ); ?>:</label></th>
				<td><input type="text" name="clpr_coupon_code" class="text" value="<?php echo get_post_meta($post->ID, 'clpr_coupon_code', true); ?>" /></td>
			</tr>
			
			<tr id="ctype-printable-coupon" class="ctype">
				<th style="width:20%"><label><?php _e( 'Printable Coupon URL', APP_TD ); ?>:</label></th>
				<td>
					<input type="text" readonly name="clpr_print_url" id="clpr_print_url" class="upload_image_url text" value="<?php clpr_get_coupon_image('thumb-med', 'url') ; ?>" />
					<input id="upload_image_button" class="upload_button button" rel="clpr_print_url" type="button" value="<?php _e( 'Add Image', APP_TD ); ?>" />							
					<p class="small"><?php _e( 'Click the "Add Image" button to upload or add from the "Media Library". Then click the "Insert into Post" button.', APP_TD ); ?></p>
					<div class="upload_image_preview"><?php clpr_get_coupon_image('thumb-large'); ?></div>
					<input type="text" class="hide" id="imageid" name="clpr_print_imageid" value="" />
				</td>
			</tr>
			
			<tr>
				<th style="width:20%"><label><?php _e( 'Destination URL', APP_TD ); ?>:</label></th>
				<td><input type="text" name="clpr_coupon_aff_url" class="text" value="<?php echo esc_attr( get_post_meta($post->ID, 'clpr_coupon_aff_url', true) ); ?>" /></td>
			</tr>
			
			<tr>
				<th style="width:20%"><label><?php _e( 'Display URL', APP_TD ); ?>:</label></th>
				<td><input type="text" readonly class="text" value="<?php echo esc_html( home_url( "coupon/$post->post_name/$post->ID"  ) ); ?>" /></td>
			</tr>
			
			<tr>
				<th style="width:20%"><label><?php _e( 'Expiration Date', APP_TD ); ?>:</label></th>
				<td><input type="text" name="clpr_expire_date" class="datepicker" value="<?php echo get_post_meta($post->ID, 'clpr_expire_date', true); ?>" /></td>
			</tr>
			
			<tr>
				<th style="width:20%"><label for="clpr_featured"><?php _e( 'Featured Coupon', APP_TD ); ?>:</label></th>
				<td><input type="hidden" name="clpr_featured" value="0" />
			<span class="checkbox-wrap"><input type="checkbox" name="clpr_featured" value="1" <?php if (get_post_meta($post->ID, 'clpr_featured', true)) { echo "checked"; }?> class="checkbox" /></span>	
			<p><?php _e(' Show this coupon in the home page slider', APP_TD ); ?></p></td>
			</tr>
			
			<tr>
				<th style="width:20%"><label><?php _e( 'Submitted from IP', APP_TD ); ?>:</label></th>
				<td><?php echo esc_html(get_post_meta($post->ID, 'clpr_sys_userIP', true)); ?></td>
			</tr>
			
		</table>	
	


<?php
}

// display the coupon report meta box on the edit coupon page
function clpr_reports_meta_box($post_id) {
	global $wpdb, $post;
	
	$reports = new Report;
	$report = $reports->findReports('ORDER BY id DESC', 1,"WHERE postID=".$post->ID);
	if($report) {	
		$report = $report[0];
		$permalink = add_query_arg( array( 'action' => 'edit', 'post' => $report->postID ), admin_url('post.php') );
	}
	?>
	
	<p><?php if ($report && $report->stamp != '' ) { _e( 'First reported on', APP_TD ); ?> <?php echo date("F j, Y", $report->stamp); }?></p>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="border">
		<tr align="left">
			<th width="15%" align="left"><?php _e( 'IP Address', APP_TD ); ?></th>
			<th width="30%" align="left"><?php _e( 'Content', APP_TD ); ?></th>
			<th align="left"><?php _e( 'Time Stamp', APP_TD ); ?></th>
		</tr>
	<?php
	$comments = false;
	if ($report)
		$comments = $reports->getComments($report->id);

	$alt ='';
	if($comments) foreach($comments as $comment):
	$alt = ($alt=='')? 'class="alt"' : '';
	?>
		<tr <?php echo $alt;?> style="height:30px; padding: 5px;">
			<td align="left"><?php echo $comment->ip;?></td>
			<td align="left"><?php echo $comment->type;?></td>
			<td align="left"><?php echo date('F j, Y', $comment->stamp);?></td>
		</tr>
	<?php endforeach;?>
	</table>
	<?php
	
}


// save all meta values on the coupon
function clpr_save_meta_box( $post_id ) {
	global $wpdb, $post, $key;
	
	// make sure something has been submitted from our nonce
	if (!isset($_POST[$key . '_wpnonce'])) 
		return $post_id;
		
	// verify this came from the our screen and with proper authorization, 
	// because save_post can be triggered at other times	
	if (!wp_verify_nonce($_POST[$key . '_wpnonce'], plugin_basename(__FILE__))) 
		return $post_id;
	
	// verify if this is an auto save routine. 
	// if it is our form and it has not been submitted, dont want to do anything
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		return $post_id;

	// lastly check to make sure this user has permissions to save post fields
	if (!current_user_can('edit_post', $post_id)) 
		return $post_id;
		
		
	// enter each field name here so we can update on save (except for taxonomies)
	$metafields = array(	
		'clpr_coupon_code' => $_POST['clpr_coupon_code'],
		'clpr_print_url' => $_POST['clpr_print_url'],
		'clpr_expire_date' => $_POST['clpr_expire_date'],
		'clpr_featured' => $_POST['clpr_featured'],
		'clpr_print_imageid' => $_POST['clpr_print_imageid'],
		'clpr_coupon_aff_url' => $_POST['clpr_coupon_aff_url']
	);

	
  // if printable coupon then clear coupon code
  if ($_POST['coupon_type'] == 'printable-coupon')
    $metafields['clpr_coupon_code'] = '';
  
	// loop through all custom meta fields and update values
	foreach ($metafields as $name => $value)
		update_post_meta($post_id, $name, $value);
		
	// now update the coupon store & type drop-downs
	// wp_set_object_terms( $post_id, $_POST['coupon_store'], APP_TAX_STORE );		
	wp_set_object_terms( $post_id, $_POST['coupon_type'], APP_TAX_TYPE );
	
	// there's a new printable coupon image so let's delete the old and associate the new
	if ($attach_id = $_POST['clpr_print_imageid']) {
	
		// get all the print coupons associated with the coupon. there should only be one
		$images = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', APP_TAX_IMAGE => 'printable-coupon') );

		// now removes object term in any existing attachments for this coupon
		if ($images) {		
			foreach ($images as $attachment_id => $attachment)
        wp_set_object_terms($attachment_id, NULL, APP_TAX_IMAGE, false);	
		}
	
		// associate it to the coupon. set the image post_parent column to the coupon post id
		wp_update_post( array( 'ID' => $attach_id, 'post_parent' => $post_id ) );
		wp_set_object_terms($attach_id, 'printable-coupon', APP_TAX_IMAGE, false);	

	}
	
	
	// give the coupon a unique ID if it's a new coupon
	if (!$clpr_id = get_post_meta($post->ID, 'clpr_id', true)) {	
		$clpr_item_id = uniqid(rand(10,1000), false);
		add_post_meta($post_id, 'clpr_id', $clpr_item_id, true);
	}
	
	// set the IP address if it's a new coupon
	if (!$clpr_ip = get_post_meta($post->ID, 'clpr_sys_userIP', true)) {	
		add_post_meta($post_id, 'clpr_sys_userIP', appthemes_get_ip(), true);
	}
	
	// set stats to zero so we at least have some data
	if (!$clpr_dcount = get_post_meta($post->ID, 'clpr_daily_count', true)) {
		add_post_meta($post_id, 'clpr_daily_count', '0', true);
	}
	
	if (!$clpr_tcount = get_post_meta($post->ID, 'clpr_total_count', true)) {
		add_post_meta($post_id, 'clpr_total_count', '0', true);
	}
	
	if (!$clpr_affcount = get_post_meta($post->ID, 'clpr_coupon_aff_clicks', true)) {
		add_post_meta($post_id, 'clpr_coupon_aff_clicks', '0', true);
	}
	
	if (!$clpr_voteper = get_post_meta($post->ID, 'clpr_votes_percent', true)) {
		add_post_meta($post_id, 'clpr_votes_percent', '100', true);	
	}

	if (!get_post_meta($post->ID, 'clpr_votes_down', true)) {
		add_post_meta($post_id, 'clpr_votes_down', '0', true);
	}

	if (!get_post_meta($post->ID, 'clpr_votes_up', true)) {
		add_post_meta($post_id, 'clpr_votes_up', '0', true);
	}
}
add_action( 'save_post', 'clpr_save_meta_box' );

// removes media library tab to escape assign second time the same printable coupon
function clpr_remove_media_library_tab( $tabs ) {
  if (isset($_REQUEST['post_id'])) {
    $post_type = get_post_type($_REQUEST['post_id']);
    if (APP_POST_TYPE == $post_type)
      unset($tabs['library']);
  }
  return $tabs;
}
add_filter('media_upload_tabs', 'clpr_remove_media_library_tab');

?>
