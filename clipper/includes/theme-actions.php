<?php
/**
 * Adds all action hooks for the theme
 *
 * @since 1.0
 * @uses add_action() calls to trigger the hooks.
 *
 */
 

 
// add the colorbox to blog post galleries
function clpr_colorbox_blog() { 
?>
	<script type="text/javascript">
	// <![CDATA[
		jQuery(document).ready(function($){
			$(".gallery").each(function(index, obj){
				var galleryid = Math.floor(Math.random()*10000);
				$(obj).find("a").colorbox({rel:galleryid, maxWidth:"95%", maxHeight:"95%"});
			});
			$("a.lightbox").colorbox({maxWidth:"95%", maxHeight:"95%"});
		});
	// ]]>
	</script>
<?php
}
// hook into the correct action
add_action('appthemes_before_blog_loop', 'clpr_colorbox_blog');

 
// add the post meta before the blog post content 
function clpr_blog_post_meta() {
	if(is_page()) return; // don't do post-meta on pages
?>		
	<div class="content-bar">
		<p class="meta"><?php echo get_the_date(get_option('date_format')); ?><?php the_category(', '); ?></p>
		<p class="comment-count"><?php comments_popup_link( __( '0 Comments', APP_TD ), __( '1 Comment', APP_TD ), __( '% Comments', APP_TD ) ); ?></p>
	</div>
<?php
}
// hook into the correct action
add_action('appthemes_before_blog_post_content', 'clpr_blog_post_meta');


// add the pagination to the coupon loop 
function clpr_coupon_pagination() {
	if(is_singular(APP_POST_TYPE)) return; // don't do post-meta on pages
?>		
	
	<?php if(function_exists('appthemes_pagination')) appthemes_pagination(); ?>
	
	<div class="top"><a href="#top"><?php _e( 'Top', APP_TD ); ?> &uarr;</a></div>
     
<?php
}
// hook into the correct action
add_action('appthemes_after_endwhile', 'clpr_coupon_pagination'); 
add_action('appthemes_after_search_endwhile', 'clpr_coupon_pagination'); 
 
 
// add the pagination to the blog loop  
function clpr_blog_pagination() {
	if(is_singular('post')) return; // don't do post-meta on pages
?>		
	<div class="content-box">

        <div class="box-t">&nbsp;</div>

        <div class="box-c">

            <div class="box-holder">

				<?php if(function_exists('appthemes_pagination')) appthemes_pagination(); ?>
				
				<div class="top"><a href="#top"><?php _e( 'Top', APP_TD ); ?> &uarr;</a></div>

            </div>

        </div>

        <div class="box-b">&nbsp;</div>

    </div>       
<?php
}
// hook into the correct action
add_action('appthemes_after_blog_endwhile', 'clpr_blog_pagination');


// add the share coupon sidebar button
function clpr_sidebar_share_button() {
?>

	<a href="<?php echo clpr_get_submit_coupon_url(); ?>" class="share-box">
		<img src="<?php bloginfo('template_url'); ?>/images/share_icon.png" title="" alt="" />
		<span class="lgheading"><?php _e( 'Share a Coupon', APP_TD ); ?></span>
		<span class="smheading"><?php _e( 'Spread the Savings with Everyone!', APP_TD ); ?></span>
	</a>

<?php
}
// hook into the correct action
//add_action('appthemes_before_sidebar_widgets', 'clpr_sidebar_share_button');


// add the post tags after the blog post content 
function clpr_blog_post_tags() {
	if(is_page()) return; // don't do post-meta on pages
	global $post;
?>		
	
	<div class="text-footer">
							
		<div class="tags"><?php _e( 'Tags:', APP_TD ); ?> <?php if(get_the_tags()) the_tags(' ', ', ', ''); else echo ' ' . __( 'None', APP_TD ); ?></div>
		
		<?php if (get_option('clpr_stats_all') == 'yes') { ?>
			<div class="stats"><?php appthemes_stats_counter($post->ID); ?></div> 
		<?php } ?>
	
		<div class="clear"></div>
	
	</div>
	
<?php
}
// hook into the correct action
add_action('appthemes_after_blog_post_content', 'clpr_blog_post_tags');


// add the author box after the blog post content 
function clpr_author_box() {
	if( !is_singular('post') ) return; // only show on blog post single page
	
	if ( get_the_author_meta( 'description' ) ) : 
?>

		<div class="author-wrap">

			<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>

			<p class="author"><?php printf( esc_attr__( 'About %s', APP_TD ), get_the_author() ); ?></p>
			<p><?php the_author_meta( 'description' ); ?></p>

		</div>

	<?php 
	endif;

} 
// hook into the correct action
add_action('appthemes_after_blog_post_content', 'clpr_author_box');




// add the user bar box after the blog post content 
function clpr_user_bar_box() {
  global $post;

	if( !is_singular('post') ) return; // only show on blog post single page
	
	// assemble the text and url we'll pass into each social media share link
	$social_text = urlencode(strip_tags(get_the_title() . ' ' . __( 'post from', APP_TD ) . ' ' . get_bloginfo('name')));
	$social_url  = urlencode(get_permalink($post->ID));
?>
                        
	<div class="user-bar">

		<?php if (comments_open()) comments_popup_link( ('<span>' . __( 'Leave a comment', APP_TD ) . '</span>'), ('<span>' . __( 'Leave a comment', APP_TD ) . '</span>'), ('<span>' . __( 'Leave a comment', APP_TD ) . '</span>'), 'leave', '' ); ?>	

		<ul class="social">
			<li><a class="rss" href="<?php echo get_post_comments_feed_link(get_the_ID()); ?>" rel="nofollow"><?php _e( 'Post Comments RSS', APP_TD ); ?></a></li>
			<li><a class="twitter" href="http://twitter.com/home?status=<?php echo $social_text; ?>+-+<?php echo $social_url; ?>" target="_blank" rel="nofollow"><?php _e( 'Twitter', APP_TD ); ?></a></li>
			<li><a class="facebook" href="javascript:void(0);" onclick="window.open('http://www.facebook.com/sharer.php?t=<?php echo $social_text; ?>&amp;u=<?php echo $social_url; ?>','doc', 'width=638,height=500,scrollbars=yes,resizable=auto');" rel="nofollow"><?php _e( 'Facebook', APP_TD ); ?></a></li>
			<li><a class="digg" href="http://digg.com/submit?phase=2&amp;url=<?php echo $social_url; ?>&amp;title=<?php echo $social_text; ?>" target="_blank" rel="nofollow"><?php _e( 'Digg', APP_TD ); ?></a></li>
		</ul>

	</div>
						
<?php
}
// hook into the correct action
add_action('appthemes_after_blog_post_content', 'clpr_user_bar_box'); 


// add the coupon submission form in submit-coupon-form.php
function clpr_do_coupon_form( $post ) {
	global $app_abbr;

	$form_fields = array(
		'post_title' => 'post',
		'clpr_store_name' => APP_TAX_STORE,
		'clpr_new_store_name' => 'none',
		'clpr_new_store_url' => 'none',
		'cat' => APP_TAX_CAT,
		'coupon_type_select' => APP_TAX_TYPE,
		'clpr_coupon_code' => 'postmeta',
		'clpr_coupon_aff_url' => 'postmeta',
		'clpr_expire_date' => 'postmeta',
		'coupon-upload' => 'upload',
		'tags_input' => APP_TAX_TAG,
		'post_content' => 'post',
	);

	foreach( $form_fields as $name => $type ) {
		if ( isset( $_POST[ $name ] ) ) {
			$field[ $name ] = trim( $_POST[ $name ] );
		} elseif ( $post && $type == 'post' ) {
			$field[ $name ] = $post->$name;
		} elseif ( $post && $type == 'postmeta' ) {
			$field[ $name ] = get_post_meta( $post->ID, $name, true );
		} elseif ( $post && in_array( $type, array( APP_TAX_STORE, APP_TAX_CAT, APP_TAX_TYPE ) ) ) {
			$field[ $name ] = appthemes_get_custom_taxonomy( $post->ID, $type, 'term_id' );
		} elseif ( $post && $type == APP_TAX_TAG ) {
			$term_list = wp_get_post_terms( $post->ID, APP_TAX_TAG, array( 'fields' => 'names' ) );
			$term_list = implode( ', ', (array) $term_list );
			$field[ $name ] = $term_list;
		} else {
			$field[ $name ] = '';
		}

	}

?>

<form action="" id="couponForm" method="post" class="post-form" enctype="multipart/form-data">

	<fieldset>

		<ol>
			<li>
				<label><?php _e( 'Coupon Title:', APP_TD ); ?> </label>
				<input type="text" class="text required" id="post_title" name="post_title" value="<?php echo esc_attr( $field['post_title'] ); ?>" />
			</li>

			<li>
				<label><?php _e( 'Store:', APP_TD ); ?></label>
				<select id="store_name_select" name="clpr_store_name" class="text required">
					<option value=""><?php _e( '-- Select One --', APP_TD ); ?></option>
					<option value="add-new"><?php _e( 'Add New Store', APP_TD ); ?></option>
					<?php
						$terms = get_terms( APP_TAX_STORE, array( 'hide_empty' => 0 ) );
						foreach( $terms as $term ) {
						  if ( get_metadata(APP_TAX_STORE, $term->term_id, 'clpr_store_active', true) != 'no' ) {
								$selected = selected( $term->term_id, $field['clpr_store_name'], false );
								echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . '</option>';
							}
						} 
					?>
				</select>
			</li>

			<li id="new-store-name" class="new-store">
				<label><?php _e( 'New Store Name:', APP_TD ); ?></label>
				<input type="text" class="text" name="clpr_new_store_name" value="<?php echo esc_attr( $field['clpr_new_store_name'] ); ?>"/>
			</li>

			<li id="new-store-url" class="new-store">
				<label><?php _e( 'New Store URL:', APP_TD ); ?> </label>
				<input type="text" class="text" id="clpr_new_store_url" name="clpr_new_store_url" value="<?php echo ( ! empty( $field['clpr_new_store_url'] ) ? esc_attr( $field['clpr_new_store_url'] ) : 'http://' ); ?>" />
			</li>

			<li>
				<label><?php _e( 'Coupon Category:', APP_TD ); ?> </label>
				<?php
					$args = array( 'taxonomy' => APP_TAX_CAT, 'selected' => $field['cat'], 'hierarchical' => 1, 'class' => 'text required', 'show_option_none' => __( '-- Select One --', APP_TD ), 'hide_empty' => 0, 'echo' => 0 );
					$select = wp_dropdown_categories( $args );
					$select = preg_replace('"-1"', "", $select); // remove the -1 for the "select one" option so jquery validation works
					echo $select;
				?>
			</li>

			<li>
				<label><?php _e( 'Coupon Type:', APP_TD ); ?> </label>
				<select id="coupon_type_select" name="coupon_type_select" class="text required">
					<option value=""><?php _e( '-- Select One --', APP_TD ); ?></option>
					<?php
						$terms = get_terms( APP_TAX_TYPE, array( 'hide_empty' => 0 ) );
						foreach( $terms as $term ) {
							$selected = selected( $term->term_id, $field['coupon_type_select'], false );
							echo '<option value="' . $term->slug . '" ' . $selected . '>' . $term->name . '</option>';
						}
					?>
				</select>
			</li>

			<li id="ctype-coupon-code" class="ctype">
				<label><?php _e( 'Coupon Code:', APP_TD ); ?> </label>
				<input type="text" class="text" id="ctype-coupon-code" name="clpr_coupon_code" value="<?php echo esc_attr( $field['clpr_coupon_code'] ); ?>"/>
			</li>

			<?php if ( $post && clpr_has_printable_coupon( $post->ID ) ) { ?>
				<li id="ctype-printable-coupon-preview" class="ctype">
					<label><?php _e( 'Current Coupon:', APP_TD ); ?> </label>
					<?php echo clpr_get_printable_coupon( $post->ID ); ?>
				</li>
			<?php } ?>

			<li id="ctype-printable-coupon" class="ctype">
				<label><?php _e( 'Printed Coupon:', APP_TD ); ?> </label>
				<input type="file" class="fileupload text" name="coupon-upload" value="<?php echo esc_attr( $field['coupon-upload'] ); ?>" />
			</li>

			<li>
				<label><?php _e( 'Destination URL:', APP_TD ); ?></label>
				<input type="text" class="text required" name="clpr_coupon_aff_url" value="<?php echo esc_attr( $field['clpr_coupon_aff_url'] ); ?>"/>
			</li>

			<li>
				<label><?php _e( 'Expiration Date:', APP_TD ); ?> </label>
				<input type="text" class="text required datepicker" name="clpr_expire_date" value="<?php echo esc_attr( $field['clpr_expire_date'] ); ?>" />
			</li>

			<li>
				<label><?php _e( 'Tags:', APP_TD ); ?> </label>
				<input type="text" class="text" name="tags_input" value="<?php echo esc_attr( $field['tags_input'] ); ?>" />
				<p class="tip"><?php _e( 'Separate tags with commas', APP_TD ); ?></p>
			</li>

			<li class="description">
				<label for="post_content"><?php _e( 'Full Description:', APP_TD ); ?> </label>
				<textarea class="required" id="post_content" cols="30" rows="5" name="post_content"><?php echo esc_textarea( $field['post_content'] ); ?></textarea>
			</li>
			<?php if ( get_option('clpr_allow_html') == 'yes' ) { ?>
				<script type="text/javascript"> <!--
					tinyMCE.execCommand('mceAddControl', false, 'post_content');
				--></script>
			<?php } ?>

			<li>
				<?php
					// include the spam checker if enabled
					appthemes_recaptcha();
				?>
			</li>

			<?php
				$button_text = ( clpr_payments_is_enabled() ) ? __( 'Continue', APP_TD ) : __( 'Share It!', APP_TD );
			?>

			<?php
				if ( clpr_payments_is_enabled() )
					do_action( 'appthemes_purchase_fields' );
			?>

			<li>
				<button type="submit" class="btn coupon" id="submitted" name="submitted" value="submitted"><?php echo $button_text; ?></button>
			</li>

		</ol>

	</fieldset>

	<!-- autofocus the field -->
	<script type="text/javascript">try{document.getElementById('post_title').focus();}catch(e){}</script>

</form>

<?php
}
// hook into the correct action
add_action( 'clipper_coupon_form', 'clpr_do_coupon_form', 10, 1 );


// fire whenever a new coupon has been published
function clpr_do_publish_post($post) {
	global $wpdb;

	// get the store id
	$cat_id = appthemes_get_custom_taxonomy($post->ID, APP_TAX_STORE, 'term_id');

	// if the coupon has been approved then change the store to active
	if (isset($cat_id))
		update_metadata(APP_TAX_STORE, $cat_id, 'clpr_store_active', 'yes');

}
// hook into the correct action
add_filter('pending_to_publish', 'clpr_do_publish_post', 10, 3);


// add the subcategories box on category pages
function clpr_sidebar_subcategories_box() {
  if ( is_tax( APP_TAX_CAT ) ) {
		// go get the taxonomy category id so we can filter with it
		// have to use slug instead of name otherwise it'll break with multi-word cats
		if ( !isset( $filter ) )
			$filter = '';
			
		$coupon_category_array = get_term_by( 'slug', get_query_var(APP_TAX_CAT), APP_TAX_CAT, ARRAY_A, $filter );
    // show all subcategories if any 
		$subcats = wp_list_categories( 'hide_empty=0&orderby=name&show_count=1&title_li=&use_desc_for_title=1&echo=0&show_option_none=0&taxonomy='.APP_TAX_CAT.'&depth=1&child_of=' . $coupon_category_array['term_id'] );
		
		if ( !empty( $subcats ) ) : 
?>

			<div id="coupon-subcats" class="sidebox">
        <div class="cut"></div>

				<div class="sidebox-content">

				  <div class="sidebox-heading">
  					<h2><?php _e( 'Sub Categories', APP_TD ); ?></h2>
          </div>

          <div class="coupon-cats-widget">
  					<ul class="list">
  						<?php print_r( $subcats ); ?>
  					</ul>
          </div>

				</div><!-- /sidebox-content -->

				<br clear="all" />
				<div class="sb-bottom"></div>
			</div><!-- /sidebox -->

<?php
		endif;
  }
}
// hook into the correct action
add_action('appthemes_before_sidebar_widgets', 'clpr_sidebar_subcategories_box');


// add the sub-stores box on store pages
function clpr_sidebar_substores_box() {
  global $wpdb;
  if ( is_tax( APP_TAX_STORE ) ) {
		// go get the taxonomy store id so we can filter with it
		// have to use slug instead of name otherwise it'll break with multi-word cats
		if ( !isset( $filter ) )
			$filter = '';
			
		$coupon_store_array = get_term_by( 'slug', get_query_var(APP_TAX_STORE), APP_TAX_STORE, ARRAY_A, $filter );

    // get ids of all hidden stores 
    $hidden_stores = clpr_hidden_stores();
    // we cant exclude current store
    if(in_array($coupon_store_array['term_id'], $hidden_stores))
    	$hidden_stores = array_diff($hidden_stores, array($coupon_store_array['term_id']));

    $hidden_stores_list = implode(",",$hidden_stores);

    // show all sub-stores if any 
		$substores = wp_list_categories( 'hide_empty=0&orderby=name&show_count=1&title_li=&use_desc_for_title=1&echo=0&show_option_none=0&exclude='.$hidden_stores_list.'&taxonomy='.APP_TAX_STORE.'&depth=1&child_of=' . $coupon_store_array['term_id'] );
		
		if ( !empty( $substores ) ) : 
?>

			<div id="coupon-substores" class="sidebox">
        <div class="cut"></div>

				<div class="sidebox-content">

				  <div class="sidebox-heading">
  					<h2><?php _e( 'Related Stores', APP_TD ); ?></h2>
          </div>

          <div class="store-widget">
  					<ul class="list">
  						<?php print_r( $substores ); ?>
  					</ul>
          </div>

				</div><!-- /sidebox-content -->

				<br clear="all" />
				<div class="sb-bottom"></div>
			</div><!-- /sidebox -->

<?php
		endif;
  }
}
// hook into the correct action
add_action('appthemes_before_sidebar_widgets', 'clpr_sidebar_substores_box');


// collect stats if are enabled, limits db queries
function clpr_cache_stats() {
  if( get_option('clpr_stats_all') == 'yes' && !is_singular(array(APP_POST_TYPE, 'post')) ) {
    add_action('appthemes_before_loop', 'appthemes_collect_stats');
    add_action('appthemes_before_search_loop', 'appthemes_collect_stats');
    add_action('appthemes_before_blog_loop', 'appthemes_collect_stats');
  }
}
// hook into the correct action
add_action('wp', 'clpr_cache_stats');


/**
 * modify Social Connect redirect to url
 * @since 1.3.1
 */
function clpr_social_connect_redirect_to( $redirect_to ) {
	if ( preg_match('#/wp-(admin|login)?(.*?)$#i', $redirect_to) )
		$redirect_to = home_url();

	if ( current_theme_supports( 'app-login' ) ) {
		if ( APP_Login::get_url('redirect') == $redirect_to || appthemes_get_registration_url('redirect') == $redirect_to )
			$redirect_to = home_url();
	}

	return $redirect_to;
}
add_filter( 'social_connect_redirect_to', 'clpr_social_connect_redirect_to', 10, 1 );


/**
 * process Social Connect request if App Login enabled
 * @since 1.3.2
 */
function clpr_social_connect_login() {
	if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'social_connect' ) {
		if ( current_theme_supports( 'app-login' ) && function_exists('sc_social_connect_process_login') )
			sc_social_connect_process_login( false );
	}
}
add_action( 'init', 'clpr_social_connect_login' );


/**
 * query coupons on homepage in main wp query, fixes pagination
 * @since 1.3.1
 */
function clpr_query_coupons_on_homepage( $query ) {
	if( $query->is_main_query() && $query->is_home() )
		$query->set( 'post_type', APP_POST_TYPE );
}
if ( version_compare($wp_version, '3.4', '>=') )
	add_action( 'pre_get_posts', 'clpr_query_coupons_on_homepage' );


/**
 * adds reCaptcha support
 * @since 1.3.2
 */
function clpr_recaptcha_support() {
	global $app_abbr;
	if ( get_option($app_abbr.'_captcha_enable') == 'yes' ) {
		add_theme_support( 'app-recaptcha', array(
			'file' => TEMPLATEPATH . '/includes/lib/recaptchalib.php',
			'theme' => get_option($app_abbr.'_captcha_theme'),
			'public_key' => get_option($app_abbr.'_captcha_public_key'),
			'private_key' => get_option($app_abbr.'_captcha_private_key'),
		) );
	}
}
add_action( 'appthemes_init', 'clpr_recaptcha_support' );


/**
 * replaces default registration email
 * @since 1.4
 */
function clpr_custom_registration_email() {
	remove_action( 'appthemes_after_registration', 'wp_new_user_notification', 10, 2 );
	add_action( 'appthemes_after_registration', 'app_new_user_notification', 10, 2 );
}
add_action( 'after_setup_theme', 'clpr_custom_registration_email', 1000 );


/**
 * 336 x 280 ad box
 * @since 1.4
 */
function clpr_adbox_336x280() {
	global $app_abbr;

	if ( get_option($app_abbr.'_adcode_336x280_enable') == 'yes' ) {
		if ( get_option($app_abbr.'_adcode_336x280') != '' ) {
			echo stripslashes( get_option($app_abbr.'_adcode_336x280') );
		} else {
			if ( get_option($app_abbr.'_adcode_336x280_url') ) {
				$img = html( 'img', array( 'src' => get_option($app_abbr.'_adcode_336x280_url'), 'border' => '0', 'alt' => '' ) );
				echo html( 'a', array( 'href' => get_option($app_abbr.'_adcode_336x280_dest'), 'target' => '_blank' ), $img );
			}
		}
	}
}


/**
 * adds advertise to single blog page 
 * @since 1.4
 */
function clpr_adbox_single_page() {
	global $app_abbr, $post;

	if ( !is_singular( array( 'post' ) ) )
		return;

	if ( get_option($app_abbr.'_adcode_336x280_enable') == 'yes' ) {
	?>
		<div class="content-box">

			<div class="box-t">&nbsp;</div>

			<div class="box-c">

				<div class="box-holder">

					<div class="post-box">

						<div class="head">

							<h3><?php _e( 'Sponsored Ads', APP_TD ); ?></h3>

						</div>

						<div class="text-box">

							<?php clpr_adbox_336x280(); ?>

						</div>

					</div>

				</div>

			</div>

			<div class="box-b">&nbsp;</div>

		</div>
	<?php
	}

}
add_action( 'appthemes_advertise_content', 'clpr_adbox_single_page' );


/**
 * adds advertise to taxonomy store page 
 * @since 1.4
 */
function clpr_adbox_taxonomy_page() {

	if ( !is_tax( array( APP_TAX_STORE ) ) )
		return;

	clpr_adbox_336x280();
}
add_action( 'appthemes_advertise_content', 'clpr_adbox_taxonomy_page' );


/**
 * adds coupon listing price into submission form
 * @since 1.4
 */
function clpr_display_listing_price_field() {
	$price = appthemes_get_price( get_option('clpr_coupon_price') );
?>
	<li id="coupon-listing-fee">
		<label><?php _e( 'Listing Fee', APP_TD ); ?></label>
		<p class="info"><?php printf( __( '%s for submitting coupon.', APP_TD ), $price ); ?></p>
	</li>
<?php
}
add_action( 'appthemes_purchase_fields', 'clpr_display_listing_price_field', 9 );


// DO NOT PUT A CLOSING  "? >" at the end of this file. Causes strange issues with category creation in the admin console.				