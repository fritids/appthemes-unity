<?php
/**
 * JobRoller Confirm Job form
 * Function outputs the job confirmation form
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_confirm_job_form() {
	
	global $post, $posted, $wpdb;

	// TODO: standardize data processing (confirm-job-form, confirm-job-process, relist-job-process)

	$posted = json_decode(stripslashes($_POST['posted']), true);
	$cost = 0;
	$feat_job_offer_remain = 0;

	// Get Pack from previous step
	if ( isset($_POST['job_pack']) && !empty($_POST['job_pack']) ) :
		$posted['job_pack'] = stripslashes(trim($_POST['job_pack']));
		if (strstr($posted['job_pack'], 'user_')) :
			// This is a user's pack and has already been purchased		
			$user_pack_id = (int) str_replace('user_', '', $posted['job_pack']);
			$user_pack = new jr_user_pack( $user_pack_id );
			if ( ! $user_pack->get_valid_pack() ) {
				wp_die( __('Error: Invalid User Pack.', APP_TD));
			}
			$feat_job_offer_remain= $user_pack->feat_job_offer_remain;
		else :

			// Get pack price
			$job_pack = new jr_pack( $posted['job_pack'] );
			if ( ! $job_pack->id ) {
				wp_die( __('Error: Invalid Pack.', APP_TD));
			}
			$cost += $job_pack->pack_cost;
			$feat_job_offer_remain= $job_pack->feat_job_offers;
		endif;

	else :
		// No Packs

		// security check to avoid empty pack exploits
		if ( jr_get_job_packs() )
			wp_die( __('Error: No Pack Selected.', APP_TD));

		$posted['job_pack'] = '';
		$listing_cost = get_option('jr_jobs_listing_cost');
		$cost += $listing_cost;
	endif;
		
	// Get Featured from previous step
	if (isset($_POST['featureit']) && $_POST['featureit']) :
		$posted['featureit'] = 'yes';		
		$featured_cost = get_option('jr_cost_to_feature');
		$cost += $featured_cost;		
	else :
		$posted['featureit'] = '';
	endif;

	?>
	<form action="<?php echo get_permalink( $post->ID ); ?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">			
		<p><?php _e('Your job is ready to be submitted, check the details are correct and then click &ldquo;confirm&rdquo; to submit your listing', APP_TD); ?><?php 
			if (get_option('jr_jobs_require_moderation')=='yes') _e(' for approval', APP_TD);
		?>.</p>
		
		<blockquote>
			<h2><?php
				$job_type_name = get_term_by('slug',$posted['job_term_type'],APP_TAX_TYPE)->name;
				echo wptexturize($job_type_name).' &ndash; '; 
				echo wptexturize($posted['job_title']); 
			?></h2>
			<?php if ($posted['your_name']) : ?>
			<h3><?php _e('Company/Poster',APP_TD); ?></h3>
			<p><?php
				if ($posted['website'])
					echo '<a href="'. strip_tags($posted['website']).'">';
				echo strip_tags($posted['your_name']);
				if ($posted['website'])
					echo '</a>';
			?></p>
			<?php endif; ?>
			<h3><?php _e('Job description',APP_TD); ?></h3>
			<?php echo wpautop(wptexturize($posted['details'])); ?>
			<?php if (get_option('jr_submit_how_to_apply_display')=='yes') : ?>
				<h3><?php _e('How to apply',APP_TD); ?></h3>
				<?php echo wpautop(wptexturize($posted['apply'])); ?>
			<?php endif; ?>
		</blockquote>

		<?php
		if ( $feat_job_offer_remain > 0 && !$posted['featureit'] && get_option('jr_cost_to_feature') ):
		?>
			<div class="pack-offer-reminder"><?php _e('Just a reminder that the Pack you\'ve selected allows you to <em>Feature</em> this Job for <strong>Free</strong>. This offer is only valid for the remaining jobs and cannot be used if you reach your Pack jobs limit. To feature this job please click <strong>\'Go Back\'</strong> and check the <em>\'Feature\'</em> checkbox on the previous page.', APP_TD); ?></div>
		<?php
		endif;
		?>

		<?php
		if ($cost > 0) :

				if ( $feat_job_offer_remain > 0 && !empty($featured_cost) ):					
			?>
					<fieldset class="pack_offers">
						<h2><?php _e('Feature your Job as part of the Pack offer?',APP_TD); ?></h2>
						<p>
						   <input type="radio" name="featured_offer" value="yes" checked ><?php _e('Yes', APP_TD); ?>
						   <input type="radio" name="featured_offer" value="no" ><?php _e('No',APP_TD); ?>
						</p>
				   </fieldset>
		<?php
				endif;
		?>
		<?php
					$payment_message =  __('After confirming your submission you will be taken to the payment page and charged ', APP_TD)
										. '<strong>'.jr_get_currency($cost).'</strong>'.
										__(' &mdash; as soon as your payment clears your listing will become active.',APP_TD);		
		?>
			<p class="payment_message"><?php echo $payment_message; ?></p>
									
			<fieldset class="gateways_fieldset" style="display: none">				
				<legend><?php _e('Payment Gateway', APP_TD); ?></legend>	
				<select name="gateway" id="gateway" class="gateway">				
				
					<?php jr_order_gateway_options(); ?>
					
				</select>	
			</fieldset>
				
		<?php
		endif; //endif cost
		?>
		<p>
            <input type="submit" name="goback" class="goback" value="<?php _e('Go Back',APP_TD) ?>"  />
            <input type="submit" class="submit" name="confirm" value="<?php _e('Confirm &amp; Submit', APP_TD); ?>" />
            <input type="hidden" value='<?php echo htmlentities(json_encode($posted), ENT_QUOTES); ?>' name="posted" />
        </p>
		
		<div class="clear"></div>
	</form>	
	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery.noConflict();
		(function($) {
			
			$('input[name="featured_offer"]').live( 'click', function () {
				
				if ( $(this).val() == 'yes' )
					$('.payment_message').fadeOut();	
				else
					$('.payment_message').fadeIn();
				
			});	

			$('input[name="featured_offer"]:checked').trigger('click');
			
		})(jQuery);
		/* ]]> */
	</script>
				 	
	<?php
}
