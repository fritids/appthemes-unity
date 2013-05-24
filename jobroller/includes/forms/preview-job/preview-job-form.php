<?php
/**
 * JobRoller Preview Job form
 * Function outputs the job preview form
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */

function jr_preview_job_form() {
	
	global $post, $posted;
	?>
	<form action="<?php echo get_permalink( $post->ID ); ?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">			
		<p><?php _e('Below is a preview of what your job listing will look like when published:', APP_TD); ?></p>				
		
		<ol class="jobs">
			<li class="job" style="padding-left:0; padding-right:0;"><dl>
				<dt><?php _e('Type',APP_TD); ?></dt>
				<dd class="type"><?php
					$job_type = get_term_by( 'slug', sanitize_title($posted['job_term_type']), 'job_type');		
					echo '<span class="'.$job_type->slug.'">'.wptexturize($job_type->name).'</span>';			
				?>&nbsp;</dd>
				<dt><?php _e('Job', APP_TD); ?></dt>
				<dd class="title"><strong><?php echo $posted['job_title']; ?> </strong><?php
					
					$author = get_user_by('id', get_current_user_id());
					
					if ($posted['your_name']) :
						echo $posted['your_name'];
						if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) :
							echo sprintf( __(' &ndash; Posted by <a href="%s">%s</a>', APP_TD), $link, $author->display_name );
						endif;
					else :
						if ($author && $link = get_author_posts_url( $author->ID, $author->user_nicename )) :
							echo sprintf( __('<a href="%s">%s</a>', APP_TD), $link, $author->display_name );
						endif;
					endif;
					
					?>
				</dd>
				<dt><?php _e('_Location', APP_TD); ?></dt>
				<dd class="location"><?php
				
					$latitude = jr_clean_coordinate($posted['jr_geo_latitude']);
					$longitude = jr_clean_coordinate($posted['jr_geo_longitude']);

					if ($latitude && $longitude) :
						$address = jr_reverse_geocode($latitude, $longitude);
						
						echo '<strong>'.wptexturize($address['short_address']).'</strong> '.wptexturize($address['short_address_country']).'';
					else :
						echo '<strong>'.__('Anywhere',APP_TD).'</strong>';
					endif;
				?></dd>
				<dt><?php _e('Date Posted', APP_TD); ?></dt>
				<dd class="date"><strong><?php echo date_i18n(__('j M',APP_TD)); ?></strong> <span class="year"><?php echo date_i18n(__('Y',APP_TD)); ?></span></dd>
			</dl></li>
		</ol>
		
		<p><?php _e('The job listing&rsquo;s page will contain the following information:', APP_TD); ?></p>
		
		<blockquote>
			<h2><?php _e('Job description',APP_TD); ?></h2>
			<?php echo wpautop(wptexturize($posted['details'])); ?>
			<?php if (get_option('jr_submit_how_to_apply_display')=='yes') : ?>
				<h2><?php _e('How to apply',APP_TD); ?></h2>
				<?php echo wpautop(wptexturize($posted['apply'])); ?>
			<?php endif; ?>
		</blockquote>
		
		<?php
			// If enabled, let the user select a Job Pack - display all types of job packs (free, paid, user)
			jr_job_pack_select('preview');
		?>

		<?php
			$featured_cost = get_option('jr_cost_to_feature');
			if ($featured_cost && is_numeric($featured_cost) && $featured_cost > 0) :
				
				// Featuring is an option
				echo '<h2>'.__('Feature your listing for ', APP_TD).jr_get_currency($featured_cost).__('?', APP_TD).'</h2>';
				
				echo '<p>'.__('Featured listings are displayed on the homepage and are also highlighted in all other listing pages.', APP_TD).'</p>';
				
				echo '<p><input type="checkbox" name="featureit" id="featureit" /> <label for="featureit" style="float:none">'.__('Yes please, feature my listing.', APP_TD).'</label></p>';
				
			endif;
		?>
		
		<p>
            <input type="submit" name="goback" class="goback" value="<?php _e('Go Back',APP_TD) ?>"  /> 
            <input type="submit" class="submit" name="preview_submit" value="<?php _e('Next &rarr;', APP_TD); ?>" />
            <input type="hidden" value='<?php echo htmlentities(json_encode($posted), ENT_QUOTES); ?>' name="posted" />
        </p>
		
		<div class="clear"></div>
	</form>
	<?php
}
