<?php

// include all the core admin files
require_once ('admin-values.php');
require_once ('admin-orders.php');
require_once ('admin-subscriptions.php');
require_once ('admin-jobpacks.php');
require_once ('admin-alerts-subscribers.php');


add_action( 'admin_footer', 'jr_settings_warning' );

// Remember user to set a price to the resume subscriptions
function jr_settings_warning(){
	$warn_resume_subscr = (bool) ( 'yes' == get_option('jr_resume_require_subscription') && !get_option('jr_resume_access_cost') );

	if ( $warn_resume_subscr ) {
		echo scb_admin_notice( sprintf ( __( '<strong>Warning</strong>: You have enabled \'Resume Subscriptions\' but you haven\'t set a price. Resumes will not be accessible until you set a valid <a href="%s">resume subscription price</a>.', APP_TD ), 'admin.php?page=pricing#tab2' ), 'error' );
	}
}

################################################################################
// Set up menus within the wordpress admin sections
################################################################################

function appthemes_admin_menu() {
    add_menu_page(__('JobRoller'), __('JobRoller'), 'manage_options', basename(__FILE__) , 'jr_dashboard', FAVICON, THE_POSITION);
    add_submenu_page(basename(__FILE__), __('Dashboard',APP_TD), __('Dashboard',APP_TD), 'manage_options', basename(__FILE__), 'jr_dashboard');
    add_submenu_page(basename(__FILE__), __('General Settings', APP_TD),  __('Settings', APP_TD) , 'manage_options', 'settings', 'jr_settings');
	add_submenu_page(basename(__FILE__), __('Emails',APP_TD), __('Emails',APP_TD), 'manage_options', 'emails', 'jr_emails');
	add_submenu_page(basename(__FILE__), __('Alerts',APP_TD), __('Alerts',APP_TD), 'manage_options', 'alerts', 'jr_alerts');

    add_submenu_page(basename(__FILE__), __('Pricing',APP_TD), __('Pricing',APP_TD), 'manage_options', 'pricing', 'jr_pricing');
    add_submenu_page(basename(__FILE__), __('Job Packs',APP_TD), __('Job Packs',APP_TD), 'manage_options', 'jobpacks', 'jr_job_packs_admin');
    add_submenu_page(basename(__FILE__), __('Integration',APP_TD), __('Integration',APP_TD), 'manage_options', 'integration', 'jr_integration');
	add_submenu_page(basename(__FILE__), __('Orders',APP_TD), __('Orders',APP_TD), 'manage_options', 'orders', 'jr_orders');
	add_submenu_page(basename(__FILE__), __('Subscriptions',APP_TD), __('Subscriptions',APP_TD), 'manage_options', 'subscriptions', 'jr_subscriptions');
	add_submenu_page(basename(__FILE__), __('Alerts Subscribers',APP_TD), __('Alerts Subscribers',APP_TD), 'manage_options', 'alerts_subscribers', 'jr_alerts_subscribers');	
	
    add_submenu_page(basename(__FILE__), __('System Info',APP_TD), __('System Info',APP_TD), 'manage_options', 'sysinfo', 'jr_system_info');

	do_action( 'appthemes_add_submenu_page' );
}

add_action('admin_menu', 'appthemes_admin_menu');






// update all the admin options on save
function appthemes_update_options($options) {

    if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

        foreach ($options as $value) {

            if(isset($value['id']) && isset($_POST[$value['id']])) {
                // echo $value['id'] . '<-- value ID | ' . $_POST[$value['id']] . '<-- $_POST value ID <br/><br/>'; // FOR DEBUGGING
                update_option($value['id'], appthemes_clean($_POST[$value['id']]));
            } else {
                @delete_option($value['id']);
            }
        }

        echo '<div id="message" class="updated fade"><p><strong>'.__('Your settings have been saved.',APP_TD).'</strong></p></div>';

    }

}


// generates admin fields based on array params passed in
function appthemes_admin_fields($options) {
    global $shortname;
?>

<script type="text/javascript">
jQuery(function() {
    jQuery("#tabs-wrap").tabs({
        fx: {
            opacity: 'toggle',
            duration: 200
        }
    });
});
</script>

<div id="tabs-wrap">


<?php

    // first generate the page tabs
    $counter = 1;

    echo '<ul>'. "\n";
    foreach ($options as $value) {

        if (in_array('tab', $value)) :
            echo '<li><a href="#'.$value['type'].$counter.'">'.$value['tabname'].'</a></li>'. "\n";
            $counter = $counter + 1;
        endif;

    }
    echo '</ul>'. "\n\n";


     // now loop through all the options
    $counter = 1;
    foreach ($options as $value) {

        switch($value['type']) {

            case 'tab':

                echo '<div id="'.esc_attr($value['type'].$counter).'">'. "\n\n";
                echo '<table class="widefat fixed" style="width:850px; margin-bottom:20px;">'. "\n\n";

            break;
            
            case 'title':
            ?>

                <thead><tr><th scope="col" width="200px"><?php echo $value['name'] ?></th><th scope="col"><?php if (isset($value['desc'])) echo $value['desc'] ?>&nbsp;</th></tr></thead>

            <?php
            break;

            case 'text':
            ?>

                <tr <?php if ($value['vis'] == '0') { ?>id="<?php if ($value['visid']) { echo esc_attr($value['visid']); } else { echo 'drop-down'; } ?>" style="display:none;"<?php } ?>>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']) ?>" type="<?php echo esc_attr($value['type']); ?>" style="<?php echo @$value['css'] ?>" value="<?php if (get_option( $value['id'])) echo esc_attr(get_option( $value['id'] )); else echo esc_attr($value['std']) ?>"<?php if (!empty($value['req'])) { ?> class="required" <?php } ?> <?php if ($value['min']) { ?> minlength="<?php echo esc_attr($value['min']); ?>"<?php } ?> /><?php echo isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small></td>
                </tr>


            <?php
            break;

            case 'select':
            ?>

                <tr>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><select <?php if (isset($value['js']) && $value['js']) echo $value['js']; ?> name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" style="<?php echo esc_attr($value['css']); ?>"<?php if (!empty($value['req'])) { ?> class="required"<?php } ?>>

                        <?php
                        foreach ($value['options'] as $key => $val) {
                        ?>

                            <option value="<?php echo esc_attr($key); ?>" <?php selected(get_option($value['id']),$key); ?>><?php echo ucfirst($val) ?></option>

                        <?php
                        }
                        ?>

                       </select><?php isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'checkbox':
            ?>

                <tr>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input type="checkbox" name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" value="true" style="<?php echo esc_attr($value['css']);?>" <?php if(get_option($value['id'])) { ?>checked="checked"<?php } ?> />
                        <?php isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'textarea':
            ?>
                <tr>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp">
                        <textarea name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" style="<?php echo esc_attr($value['css']); ?>" <?php if (!empty($value['req'])) { ?> class="required" <?php } ?><?php if ($value['min']) { ?> minlength="<?php echo esc_attr($value['min']); ?>"<?php } ?>><?php if (get_option($value['id'])) echo stripslashes(get_option($value['id'])); else echo $value['std']; ?></textarea>
                        <?php isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

			case 'upload':
			?>

				<tr>
					<td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
					<td class="forminp">
						<input id="<?php echo esc_attr($value['id']); ?>" class="upload_image_url" type="text" style="<?php echo esc_attr($value['css']); ?>" name="<?php echo esc_attr($value['id']); ?>" value="<?php if (get_option( $value['id'])) echo esc_attr(get_option( $value['id'] )); else echo esc_attr($value['std']) ?>" />
						<input id="upload_image_button" class="upload_button button" rel="<?php echo esc_attr($value['id']); ?>" type="button" value="<?php esc_attr_e('Upload Image', APP_TD) ?>" />
						<br /><small><?php echo $value['desc'] ?></small>
						<div id="<?php echo esc_attr($value['id']); ?>_image" class="<?php echo esc_attr($value['id']); ?>_image upload_image_preview"><?php if (get_option( $value['id'])) echo '<img src="' .esc_attr(get_option( $value['id'] )) . '" />'; ?></div>
					</td>
                </tr>

			<?php
			break;

            case 'logo':
            ?>
                <tr>
                    <td class="titledesc"><?php echo $value['name'] ?></td>
                    <td class="forminp">&nbsp;</td>
                </tr>

            <?php
            break;

            case 'tabend':

                echo '</table>'. "\n\n";
                echo '</div> <!-- #tab'.$counter.' -->'. "\n\n";
                $counter = $counter + 1;

            break;
			
			case 'html':
				
				echo $value['html'];
			
			break;

        } // end switch

    } // end foreach
?>

</div> <!-- #tabs-wrap -->

<?php
}


do_action( 'appthemes_add_submenu_page_content' );


function jr_dashboard() {
	global $wpdb, $app_rss_feed, $app_twitter_rss_feed, $app_forum_rss_feed, $options_dashboard, $app_version;

    $date_today = date('Y-m-d');
    $date_yesterday = date('Y-m-d', strtotime('-1 days'));

	$job_count_live = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '".APP_POST_TYPE."'");
	$resume_count_live = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '".APP_POST_TYPE_RESUME."'");
	$job_count_pending = $wpdb->get_var("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'pending' AND post_type = '".APP_POST_TYPE."'");
	$job_rev_total = $wpdb->get_var("SELECT sum(cost) FROM $wpdb->jr_orders WHERE status = 'completed'");
	$job_seekers_today = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = 'wp_capabilities' AND ($wpdb->usermeta.meta_value LIKE '%job_seeker%') AND $wpdb->users.user_registered >= '$date_today'");
	$job_listers_today = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = 'wp_capabilities' AND ($wpdb->usermeta.meta_value LIKE '%job_lister%') AND $wpdb->users.user_registered >= '$date_today'");
	$job_seekers_yesterday = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = 'wp_capabilities' AND ($wpdb->usermeta.meta_value LIKE '%job_seeker%') AND $wpdb->users.user_registered BETWEEN '$date_yesterday' AND '$date_today'");
	$job_listers_yesterday = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = 'wp_capabilities' AND ($wpdb->usermeta.meta_value LIKE '%job_lister%') AND $wpdb->users.user_registered BETWEEN '$date_yesterday' AND '$date_today'");
	$total_users = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
   ?>

        <div class="wrap jobroller">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('JobRoller Dashboard', APP_TD) ?></h2>

        <div class="dash-left metabox-holder">

		<div class="postbox">
			<div class="statsico"></div>
			<h3 class="hndle"><span><?php _e('JobRoller Info', APP_TD) ?></span></h3>

			<div class="preloader-container">
				<div class="insider" id="boxy">

				<div class="stats-info">
					<ul>
						<li><?php _e('Total Live Jobs', APP_TD)?>: <a href="edit.php?post_status=publish&post_type=job_listing"><strong><?php echo number_format_i18n($job_count_live); ?></strong></a></li>
						<li><?php _e('Total Pending Jobs', APP_TD)?>: <a href="edit.php?post_status=pending&post_type=job_listing"><strong><?php echo number_format_i18n($job_count_pending); ?></strong></a></li>
						<li><?php _e('Total Live Resumes', APP_TD)?>: <a href="edit.php?post_status=publish&post_type=resume"><strong><?php echo number_format_i18n($resume_count_live); ?></strong></a></li>
						<li><?php _e('Total Users', APP_TD)?>: <a href="users.php"><strong><?php echo number_format_i18n($total_users); ?></strong></a></li>
						<li><?php _e('Total Revenue', APP_TD)?>: <strong><?php echo jr_get_currency($job_rev_total); ?></strong></li>
						<li><?php _e('Product Version', APP_TD); ?>: <strong><?php echo $app_version; ?></strong></li>
						<li><?php _e('Product Support', APP_TD)?>:  <a href="http://forums.appthemes.com/" target="_new"><?php _e('Forum',APP_TD)?></a> | <a href="http://www.appthemes.com/support/docs/" target="_new"><?php _e('Documentation',APP_TD)?></a></li>
					</ul>
				</div>


				<div class="stats_overview">
					<h3><?php _e('New Registrations', APP_TD) ?></h3>
					<div class="overview_today">
						<p class="overview_day"><?php _e('Today', APP_TD) ?></p>
						<p class="overview_count"><?php echo number_format_i18n($job_listers_today); ?></p>
						<p class="overview_type"><em><?php _e('Job Listers', APP_TD) ?></em></p>
						<p class="overview_count"><?php echo number_format_i18n($job_seekers_today); ?></p>
						<p class="overview_type_seek"><em><?php _e('Job Seekers', APP_TD) ?></em></p>
					</div>

					<div class="overview_previous">
						<p class="overview_day"><?php _e('Yesterday', APP_TD) ?></p>
						<p class="overview_count"><?php echo number_format_i18n($job_listers_yesterday); ?></p>
						<p class="overview_type"><em><?php _e('Job Listers', APP_TD) ?></em></p>
						<p class="overview_count"><?php echo number_format_i18n($job_seekers_yesterday); ?></p>
						<p class="overview_type_seek"><em><?php _e('Job Seekers', APP_TD) ?></em></p>
					</div>
				</div>

				<div class="clear"></div>

				</div>
			</div>

		</div> <!-- postbox end -->



		<div class="postbox">
			<div class="newspaperico"></div><a target="_new" href="<?php echo $app_rss_feed ?>"><div class="rssico"></div></a>
			<h3 class="hndle" id="poststuff"><span><?php _e('Latest News', APP_TD) ?></span></h3>

             <div class="preloader-container">

		<div class="insider" id="boxy">

			<?php appthemes_dashboard_appthemes(); ?>

		</div> <!-- inside end -->

             </div>


		</div> <!-- postbox end -->


	</div> <!-- dash-left end -->


	<div class="dash-right metabox-holder">


		<div class="postbox">
			<div class="statsico"></div>
			<h3 class="hndle" id="poststuff"><span><?php _e('Stats - Last 30 Days', APP_TD) ?></span></h3>

            <div class="preloader-container">
		<div class="insider" id="boxy">

			<?php jr_dashboard_charts(); ?>

		</div> <!-- inside end -->
            </div>
		</div> <!-- postbox end -->


		<div class="postbox">
			<div class="twitterico"></div><a target="_new" href="<?php echo $app_twitter_rss_feed ?>"><div class="rssico"></div></a>
			<h3 class="hndle" id="poststuff"><span><?php _e('Latest Tweets', APP_TD) ?></span></h3>

            <div class="preloader-container">
		<div class="insider" id="boxy">

			<?php appthemes_dashboard_twitter(); ?>

		</div> <!-- inside end -->
            </div>


		</div> <!-- postbox end -->


		<div class="postbox">
			<div class="forumico"></div><a target="_new" href="<?php echo $app_forum_rss_feed ?>"><div class="rssico"></div></a>
			<h3 class="hndle" id="poststuff"><span><?php _e('Support Forum', APP_TD) ?></span></h3>

            <div class="preloader-container">
		<div class="insider" id="boxy">

			<?php appthemes_dashboard_forum(); ?>

		</div> <!-- inside end -->
            </div>


		</div> <!-- postbox end -->


	</div> <!-- dash-right end -->
</div> <!-- /wrap -->

<?php
}


// general settings admin page
function jr_settings() {
    global $options_settings;

    appthemes_update_options($options_settings);
    ?>

	<div class="wrap jobroller">
            <div class="icon32" id="icon-tools"><br/></div>
		<h2><?php _e('General Settings',APP_TD); ?></h2>

                <form method="post" id="mainform" action="">
                    <p class="submit btop"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>

                        <?php appthemes_admin_fields($options_settings); ?>

                    <p class="submit bbot"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
                    <input name="submitted" type="hidden" value="yes" />
		</form>
	</div>
	<?php
}

// theme styles
// populates the theme dropdown with the default styles and adds any custom .css styles found on the styles path
// styles must be placed under the child folder \styles\ (fallback to the parent /styles folder if directory does not exist)
// the resulting styles array is filterable to allow adding custom theme styles
function jr_settings_theme_styles() {

	$styles_path = get_stylesheet_directory() . '/styles/';
	if ( ! file_exists( $styles_path ) ) $styles_path = get_template_directory(). '/styles/';

	$styles_pattern = $styles_path . 'style*.css';

	$styles = array (
			'style-default.css'    => __('Default Theme', APP_TD),
			'style-pro-blue.css'   => __('Blue Pro Theme', APP_TD),
			'style-pro-green.css'  => __('Green Pro Theme', APP_TD),
			'style-pro-orange.css' => __('Orange Pro Theme', APP_TD),
			'style-pro-gray.css'   => __('Gray Pro Theme', APP_TD),
			'style-pro-red.css'    => __('Red Pro Theme', APP_TD),
			'style-basic.css'      => __('Basic Plain Theme', APP_TD)
	);

	// get all the available theme styles and append them to the defaults
	foreach (glob($styles_pattern) as $filename)
		if ( !array_key_exists (basename($filename), $styles) )
			$styles[basename($filename)] = __('Custom Theme',APP_TD) . ' (' . basename($filename) . ')';

	return apply_filters('jr_theme_styles', $styles);
}

// feed settings admin page
function jr_integration() {
    global $options_integration;

    appthemes_update_options($options_integration);
    ?>

	<div class="wrap jobroller">
            <div class="icon32" id="icon-tools"><br/></div>
		<h2><?php _e('3rd Party Integration',APP_TD); ?></h2>

                <form method="post" id="mainform" action="">
                    <p class="submit btop"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>

                        <?php appthemes_admin_fields($options_integration); ?>

                    <p class="submit bbot"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
                    <input name="submitted" type="hidden" value="yes" />
		</form>
	</div>
	<?php
}


function jr_emails() {
    global $options_emails;

    appthemes_update_options($options_emails);
    ?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('Email Settings',APP_TD) ?></h2>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>

            <?php appthemes_admin_fields($options_emails); ?>

            <p class="submit bbot"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php

}

function jr_alerts() {
    global $options_alerts, $error_email, $user_ID;

    appthemes_update_options($options_alerts);
    
    // validate test emails 
	if ( isset($_POST['testalerts']) ):		

		$args = array(
			'post_type'				=> APP_POST_TYPE,
			'post_status' 			=> 'publish',
			'ignore_sticky_posts' 	=> 1,
			'posts_per_page' 		=> 5,
		);
		$jobs = query_posts($args);		
		
		$errors = 0;
		$result = jr_job_alerts_send_email( $user_ID, $jobs );
		if (!$result) $errors++; 
					
		if ( $errors > 0 ) $notice = 'error|' . __('There were errors sending the test email. Please check your log file for more details.',APP_TD);
		else $notice = 'updated|' . __('Test email sent succesfully!',APP_TD);	
		
		$notice = explode('|',$notice);
    	echo '<div class="'.$notice[0].'">
    	   			<p>'.$notice[1].'</p>
    		 </div>';
    	    	
    endif;	
	
    ?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('Alert Settings',APP_TD) ?></h2>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>

            <?php appthemes_admin_fields($options_alerts); ?>

            <p class="submit bbot"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>

		<table class="widefat fixed" style="width:850px;">

             <thead>
                <tr>
                    <th scope="col" width="200px"><?php _e('Test Job Alerts',APP_TD)?></th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
					               	
           <form method="post" id="mainform" action="">
                <tr>
                    <td class="titledesc"><?php _e('Test Email',APP_TD); ?></td>
                    <td class="forminp">
                        <input class="button"  style="float: none" name="save" type="submit" value="<?php _e('Send Test Email',APP_TD); ?>"/>
						<p><?php _e('Use this button to test your job alert emails and make any necessary tweaks or template changes. 
						The last 5 jobs will be sent to your email.',APP_TD)?></p>							
                        <input name="testalerts" type="hidden" value="yes" />
                    </td>
                </tr>
           </form>	        
		</table>   
		
    </div>

<?php
}



// pricing options admin page
function jr_pricing() {
    global $options_pricing;

    appthemes_update_options($options_pricing);
    ?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-options-general"><br/></div>
        <h2><?php _e('Pricing &amp; Payment Settings',APP_TD) ?></h2>

        <?php // jr_admin_info_box(); ?>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>

            <?php appthemes_admin_fields($options_pricing); ?>

            <p class="submit bbot"><input class="button-secondary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php
}

// system information page
function jr_system_info() {
    global $system_info, $wpdb, $app_version;
?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-options-general"><br/></div>
        <h2><?php _e('System Info',APP_TD) ?></h2>

        <?php // jr_admin_info_box(); ?>

        <?php
        // delete all the db tables if the button has been pressed.
        if (isset($_POST['deletetables']))
            jr_delete_db_tables();

        // delete all the config options from the wp_options table if the button has been pressed.
        if (isset($_POST['deleteoptions']))
            jr_delete_all_options();
        ?>
		<script type="text/javascript">
		jQuery(function() {
		    jQuery("#tabs-wrap").tabs({
		        fx: {
		            opacity: 'toggle',
		            duration: 200
		        }
		    });
		});
		</script>
        <div id="tabs-wrap">
            <ul>
            	<li><a href="#tab1"><?php _e('Debug Info',APP_TD)?></a></li>
            	<li><a href="#tab2"><?php _e('Cron Jobs',APP_TD)?></a></li>
            	<li><a href="#tab3"><?php _e('Uninstall',APP_TD)?></a></li>
            </ul>
			<div id="tab1">
                <table class="widefat fixed" style="width:850px;">

                    <thead>
                        <tr>
                            <th scope="col" width="200px"><?php _e('Debug Info',APP_TD)?></th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="titledesc"><?php _e('JobRoller Version',APP_TD)?></td>
                            <td class="forminp"><?php echo $app_version; ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('WordPress Version',APP_TD)?></td>
                            <td class="forminp"><?php if (appthemes_is_wpmu()) echo 'WPMU'; else echo 'WP'; ?> <?php if(function_exists('bloginfo')) echo bloginfo('version'); ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('PHP Version',APP_TD)?></td>
                            <td class="forminp"><?php if(function_exists('phpversion')) echo phpversion(); ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Server Software',APP_TD)?></td>
                            <td class="forminp"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('UPLOAD_MAX_FILESIZE',APP_TD)?></td>
                            <td class="forminp"><?php
                            	if(function_exists('phpversion')) //echo ini_get('upload_max_filesize');
                            		echo (let_to_num(ini_get('upload_max_filesize'))/(1024*1024))."MB";
                            ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('POST_MAX_SIZE',APP_TD)?></td>
                            <td class="forminp"><?php
                            	if(function_exists('phpversion')) //echo ini_get('upload_max_filesize');
                            		echo (let_to_num(ini_get('post_max_size'))/(1024*1024))."MB";
                            ?></td>
                        </tr>

                         <tr>
                            <td class="titledesc"><?php _e('WordPress Memory Limit',APP_TD)?></td>
                            <td class="forminp"><?php
                            	echo (let_to_num(WP_MEMORY_LIMIT)/(1024*1024))."MB";
                            ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('DISPLAY_ERRORS',APP_TD)?></td>
                            <td class="forminp"><?php if(function_exists('phpversion')) echo ini_get('display_errors'); ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('FSOCKOPEN Check',APP_TD)?></td>
                            <td class="forminp"><?php if(function_exists('fsockopen')) echo '<span style="color:green">' . __('Your server has fsockopen enabled which is needed for PayPal IPN to work.', APP_TD). '</span>'; else echo '<span style="color:red">' . __('Your server does not have fsockopen enabled so PayPal IPN will not work. Contact your host provider to have it enabled.', APP_TD). '</span>'; ?></td>
                        </tr>

						<tr>
                            <td class="titledesc"><?php _e('OPENSSL Check',APP_TD)?></td>
                            <td class="forminp"><?php if(function_exists('openssl_open')) echo '<span style="color:green">' . __('Your server has Open SSL enabled which is needed for PayPal IPN to work. Also make sure port 443 is open on the firewall.', APP_TD). '</span>'; else echo '<span style="color:red">' . __('Your server does not have Open SSL enabled so PayPal IPN will not work. Contact your host provider to have it enabled.', APP_TD). '</span>'; ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('WP Remote Post Check',APP_TD)?></td>
                            <td class="forminp"><?php
								$paypal_adr = 'https://www.paypal.com/cgi-bin/webscr';
								$request['cmd'] = '_notify-validate';
								$params = array(
									'timeout' 	 => 30,
									'user-agent' => 'JobRoller/' . $app_version,
									'body' 	  	 => $request,
								);
								$response = wp_remote_post( $paypal_adr, $params );

								// Retry
								if ( is_wp_error($response) ) {
									$params['sslverify'] = false;
									$response = wp_remote_post( $paypal_adr, $params );
								}

                            	if ( !is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) echo '<span style="color:green">' . __('wp_remote_post() was successful so PayPal IPN should work fine for you!', APP_TD). '</span>'; else echo '<span style="color:red">' . __('wp_remote_post() failed. Sorry, PayPal IPN won\'t work with your server.', APP_TD). '</span>';
                            ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Log File Check',APP_TD)?></td>
                            <td class="forminp">
                            	<?php
                            		$logging_enabled = get_option('jr_enable_log');

									if ($logging_enabled=='yes') :
										$fp = fopen(TEMPLATEPATH . '/log/jobroller_log.txt', 'a');
										if ($fp) :
											 echo '<span style="color:green">' . __('Log file is writable.', APP_TD). '</span>';
										else :
											echo '<span style="color:red">' . __('Log file is not writable. Edit file permissions (jobroller/log/jobroller_log.txt)', APP_TD). '</span>';
										endif;
										fclose($fp);
									else :
										echo 'Logging is disabled - <a href="admin.php?page=settings">' . __('(change this)', APP_TD) . '</a>';
									endif;
								?>
                            </td>
                        </tr>



                        <tr>
                            <td class="titledesc"><?php _e('Theme Path',APP_TD)?></td>
                            <td class="forminp"><?php if(function_exists('bloginfo')) { echo bloginfo('template_url'); } ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Image Upload Path',APP_TD)?></td>
                            <td class="forminp"><?php if(!get_option('upload_path')) echo 'wp-content/uploads'; else echo esc_attr(get_option('upload_path')); ?><?php printf( ' - <a href="%s">' . __('(change this)', APP_TD) . '</a>', 'options-media.php' ); ?></td>                        </tr>

                </tbody>

                </table>
            </div>
            <div id="tab2">
				<table class="widefat fixed" style="width:850px;">
					<thead>
						<tr>
							<th scope="col"><?php _e('Next Run Date',APP_TD)?></th>
							<th scope="col"><?php _e('Frequency',APP_TD)?></th>
							<th scope="col"><?php _e('Hook Name',APP_TD)?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$cron = _get_cron_array();
							$schedules = wp_get_schedules();
							$date_format = _x( 'M j, Y @ G:i', 'Cron Schedules Format', APP_TD);
							foreach ( $cron as $timestamp => $cronhooks ) {
								foreach ( (array) $cronhooks as $hook => $events ) {
									foreach ( (array) $events as $key => $event ) {
										$cron[ $timestamp ][ $hook ][ $key ][ 'date' ] = date_i18n( $date_format, $timestamp );
									}
								}
							}
						?>
						<?php foreach ( $cron as $timestamp => $cronhooks ) { ?>
							<?php foreach ( (array) $cronhooks as $hook => $events ) { ?>
								<?php foreach ( (array) $events as $event ) { ?>
									<tr>
										<th scope="row"><?php echo $event[ 'date' ]; ?></th>
										<td>
											<?php
												if ( $event[ 'schedule' ] ) {
													echo $schedules [ $event[ 'schedule' ] ][ 'display' ];
												} else {
													?><em><?php _e('One-off event',APP_TD)?></em><?php
												}
											?>
										</td>
										<td><?php echo $hook; ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
            </div>
            <div id="tab3">
                <table class="widefat fixed" style="width:850px;">

                     <thead>
                        <tr>
                            <th scope="col" width="200px"><?php _e('Uninstall JobRoller',APP_TD)?></th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>

                <form method="post" id="mainform" action="">
                    <tr>
                        <td class="titledesc"><?php _e('Delete Database Tables',APP_TD)?></td>
                        <td class="forminp">
                            <p class="submit"><input onclick="return confirmBeforeDeleteTbls();" name="save" type="submit" value="<?php _e('Delete JobRoller Database Tables',APP_TD) ?>" /><br />
                        <?php _e('Do you wish to completely delete all JobRoller database tables? Once you do this you will lose any transaction data you have stored.',APP_TD)?>
                            </p>
                            <input name="deletetables" type="hidden" value="yes" />
                        </td>
                    </tr>
                </form>

                <form method="post" id="mainform" action="">
                    <tr>
                        <td class="titledesc"><?php _e('Delete Config Options',APP_TD)?></td>
                        <td class="forminp">
                            <p class="submit"><input onclick="return confirmBeforeDeleteOptions();" name="save" type="submit" value="<?php _e('Delete JobRoller Config Options',APP_TD) ?>" /><br />
                        <?php _e('Do you wish to completely delete all JobRoller configuration options? This will delete all values saved on the settings, pricing, gateways, etc admin pages from the wp_options database table.',APP_TD)?>
                            </p>
                            <input name="deleteoptions" type="hidden" value="yes" />
                        </td>
                    </tr>
                </form>

                </table>
            </div>
		</div>

    </div>

    <script type="text/javascript">
    /* <![CDATA[ */
        function confirmBeforeDeleteTbls() { return confirm("<?php _e('WARNING: You are about to completely delete all JobRoller database tables. Are you sure you want to proceed? (This cannot be undone)', APP_TD); ?>"); }
        function confirmBeforeDeleteOptions() { return confirm("<?php _e('WARNING: You are about to completely delete all JobRoller configuration options from the wp_options database table. Are you sure you want to proceed? (This cannot be undone)', APP_TD); ?>"); }
    /* ]]> */
    </script>


<?php
}
