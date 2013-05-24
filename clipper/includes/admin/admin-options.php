<?php

// include all the core admin files
require_once 'admin-values.php';

//###############################################################################
// Set up menus within the wordpress admin sections
//###############################################################################

function appthemes_admin_menu() {
	global $wpdb, $app_abbr, $app_theme;

	if ( !current_user_can( 'manage_options' ) ) return;

	add_menu_page( $app_theme, $app_theme, 'manage_options', basename( __FILE__ ), 'app_dashboard', FAVICON, THE_POSITION );
	add_submenu_page( basename( __FILE__ ), __( 'Dashboard', APP_TD ), __( 'Dashboard', APP_TD ), 'manage_options', basename( __FILE__ ), 'app_dashboard' );
	add_submenu_page( basename( __FILE__ ), __( 'General Settings', APP_TD ), __( 'Settings', APP_TD ), 'manage_options', 'settings', 'app_settings' );
	add_submenu_page( basename( __FILE__ ), __( 'Emails', APP_TD ), __( 'Emails', APP_TD ), 'manage_options', 'emails', 'app_emails' );
	add_submenu_page( basename( __FILE__ ), __( 'System Info', APP_TD ), __( 'System Info', APP_TD ), 'manage_options', 'sysinfo', 'app_system_info' );

	do_action( 'appthemes_add_submenu_page' );
}

add_action( 'admin_menu', 'appthemes_admin_menu' );


// update all the admin options on save
function appthemes_update_options( $options ) {
	global $app_abbr;

	if ( isset( $_POST['submitted'] ) && $_POST['submitted'] == 'yes' ) {

		foreach ( $options as $value ) {

			if ( isset( $_POST[$value['id']] ) ) {
				// echo $value['id'] . '<-- value ID | ' . $_POST[$value['id']] . '<-- $_POST value ID <br/><br/>'; // FOR DEBUGGING

				if ( $value['id'] == $app_abbr.'_rp_options' ) {
					$report_options = str_replace( "\n", "|", $_POST[$value['id']] );
					$report_options = str_replace( "\r", "", $report_options );
					$report_options = str_replace( "\t", "", $report_options );
					$report_options = appthemes_clean( $report_options );
					update_option( $value['id'], $report_options );

				} elseif ( $value['id'] == $app_abbr.'_votes_reset_count' && $_POST[$value['id']] == 1 ) {
					clpr_reset_votes(); // delete all votes from the db
				} else {
					update_option( $value['id'], appthemes_clean( $_POST[$value['id']] ) );
				}

			} else {
				@delete_option( $value['id'] );
			}
		}

		echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', APP_TD ) . '</strong></p></div>';

	}

}


// generates admin fields based on array params passed in
function appthemes_admin_fields( $options ) {
?>

	<script type="text/javascript">
	jQuery(function() {
		jQuery("#tabs-wrap").tabs({
			fx: {
				opacity: 'toggle',
					duration: 200
			}
		});

		/* upload logo and images */
		jQuery('.upload_button').click(function() {
			formfield = jQuery(this).attr('rel');
			tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
			return false;
		});

		/* send the uploaded image url to the field */
		window.send_to_editor = function(html) {
			imgurl = jQuery('img',html).attr('src'); // get the image url
			imgoutput = '<img src="' + imgurl + '" />'; //get the html to output for the image preview
			jQuery('#' + formfield).val(imgurl);
			jQuery('#' + formfield).siblings('.upload_image_preview').slideDown().html(imgoutput);
			tb_remove();
		}
	});
	</script>


<div id="tabs-wrap">


<?php
	// first generate the page tabs
	$counter = 1;

	echo '<ul class="tabs">'. "\n";
	foreach ( $options as $value ) {

		if ( in_array( 'tab', $value ) ) :
			echo '<li><a href="#'.$value['type'].$counter.'">'.$value['tabname'].'</a></li>'. "\n";
		$counter = $counter + 1;
endif;

	}
	echo '</ul>'. "\n\n";



	// now loop through all the options
	$counter = 1;
	foreach ( $options as $value ) {

		switch ( $value['type'] ) {


		case 'tab':

			echo '<div id="'.$value['type'].$counter.'">'. "\n\n";
			echo '<table class="widefat fixed" style="width:850px; margin-bottom:20px;">'. "\n\n";

			break;

		case 'title':
?>

				<thead><tr><th scope="col" width="200px"><?php echo $value['name']; ?></th><th scope="col"><?php echo $value['desc']; ?>&nbsp;</th></tr></thead>

<?php
			break;

		case 'text':
?>

				<tr <?php if ( $value['vis'] == '0' ) { ?>id="<?php if ( $value['visid'] ) { echo $value['visid']; } else { echo 'drop-down'; } ?>" style="display:none;"<?php } ?>>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp"><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" style="<?php echo $value['css']; ?>" value="<?php if ( get_option( $value['id'] ) ) echo get_option( $value['id'] ); else echo $value['std']; ?>"<?php if ( $value['req'] ) { ?> class="required" <?php } ?> <?php if ( $value['min'] ) { ?> minlength="<?php echo $value['min'] ?>"<?php } ?> /><br /><small><?php echo $value['desc'] ?></small></td>
				</tr>


<?php
			break;

		case 'select':
?>

				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
					<td class="forminp"><select <?php if ( $value['js'] ) echo $value['js']; ?> name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="<?php echo $value['css']; ?>"<?php if ( $value['req'] ) { ?> class="required"<?php } ?>>

<?php
			foreach ( $value['options'] as $key => $val ) {
?>

							<option value="<?php echo $key; ?>" <?php if ( get_option( $value['id'] ) == $key ) { ?> selected="selected" <?php } ?>><?php echo ucfirst( $val ); ?></option>

<?php
			}
?>

					   </select><br /><small><?php echo $value['desc']; ?></small>
					</td>
				</tr>

<?php
			break;

		case 'checkbox':
?>

				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp"><input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" style="<?php echo $value['css']; ?>" <?php if ( get_option( $value['id'] ) ) { ?>checked="checked"<?php } ?> />
						<br /><small><?php echo $value['desc']; ?></small>
					</td>
				</tr>

<?php
			break;

		case 'textarea':
?>
				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp">
							<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="<?php echo $value['css']; ?>" <?php if ( $value['req'] ) { ?> class="required" <?php } ?><?php if ( $value['min'] ) { ?> minlength="<?php echo $value['min']; ?>"<?php } ?>><?php if ( get_option( $value['id'] ) ) echo stripslashes( get_option( $value['id'] ) ); else echo $value['std']; ?></textarea>
							<br /><small><?php echo $value['desc']; ?></small>
					</td>
				</tr>

<?php
			break;

		case 'upload':
?>

				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp">
						<input id="<?php echo $value['id']; ?>" class="upload_image_url" type="text" style="<?php echo $value['css']; ?>" name="<?php echo $value['id'] ?>" value="<?php if ( get_option( $value['id'] ) ) echo get_option( $value['id'] ); else echo $value['std']; ?>" />
						<input id="upload_image_button" class="upload_button button" rel="<?php echo $value['id']; ?>" type="button" value="<?php _e( 'Add Image', APP_TD ); ?>" />
						<br /><small><?php echo $value['desc']; ?></small>
						<div id="<?php echo $value['id']; ?>_image" class="<?php echo $value['id']; ?>_image upload_image_preview"><?php if ( get_option( $value['id'] ) ) echo '<img src="' .get_option( $value['id'] ) . '" />'; ?></div>
					</td>
				</tr>

<?php
			break;

		case 'report_options':
?>
				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp">
						<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" rows="5" style="<?php echo $value['css']; ?>" <?php if ( $value['req'] ) { ?> class="required" <?php } ?><?php if ( $value['min'] ) { ?> minlength="<?php echo $value['min']; ?>"<?php } ?>><?php if ( get_option( $value['id'] ) ) echo str_replace( "|", "\n", stripslashes( get_option( $value['id'] ) ) ); else echo str_replace( "|", "\n", $value['std'] ); ?></textarea>
						<br /><small><?php echo $value['desc']; ?></small>
					</td>
				</tr>

<?php
			break;

		case 'logo':
?>
				<tr>
					<td class="titledesc"><?php echo $value['name']; ?></td>
					<td class="forminp">&nbsp;</td>
				</tr>

<?php
			break;

		case 'page_list':
			$pages = get_pages();
?>

				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp"><select <?php echo isset( $value['js'] ) && $value['js'] ? $value['js'] : ''; ?> name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" style="<?php echo $value['css']; ?>"<?php if ( $value['req'] ) { ?> class="required"<?php } ?>>

<?php
			foreach ( $pages as $pagg ) {
?>

							<option value="<?php echo $pagg->ID; ?>" <?php if ( get_option( $value['id'] ) == $pagg->ID ) { ?> selected="selected" <?php } ?>><?php echo $pagg->post_title; ?></option>

<?php
			}
?>

					   </select><br /><small><?php echo $value['desc']; ?></small>
					</td>
				</tr>
<?php
			break;

		case 'info':
?>
				<tr>
					<td class="titledesc"><?php if ( $value['tip'] ) { ?><a href="#" tip="<?php echo esc_attr( $value['tip'] ); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name']; ?>:</td>
					<td class="forminp"><?php echo $value['desc']; ?></td>
				</tr>
<?php
			break;

		case 'tabend':

			echo '</table>'. "\n\n";
			echo '</div> <!-- #tab'.$counter.' -->'. "\n\n";
			$counter = $counter + 1;

			break;

		} // end switch


	} // end foreach
?>


</div> <!-- #tabs-wrap -->

<?php

}


do_action( 'appthemes_add_submenu_page_content' );


function app_dashboard() {
	global $wpdb, $app_rss_feed, $app_twitter_rss_feed, $app_forum_rss_feed, $options_dashboard;

	$count_live = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) FROM $wpdb->posts WHERE post_status IN ('publish', 'unreliable') AND post_type = %s", APP_POST_TYPE ) );
	$count_pending = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'pending' AND post_type = %s", APP_POST_TYPE ) );

?>

		<div class="wrap">
		<div class="icon32" id="icon-themes"><br /></div>
		<h2><?php _e( 'Clipper Dashboard', APP_TD ); ?></h2>

		<div class="dash-left metabox-holder">

			<div class="dash-wrap">

				<div class="postbox">

					<div class="statsico"></div>
					<h3 class="hndle"><span><?php _e( 'Clipper Info', APP_TD ); ?></span></h3>

					<div class="inside" id="boxy">

<?php
	$clpr_version = get_option( 'clpr_version' );
?>

					<ul id="stats">
						<li><?php _e( 'Total Live Coupons', APP_TD ); ?>: <a href="edit.php?post_status=publish&post_type=<?php echo APP_POST_TYPE; ?>"><strong><?php echo number_format_i18n( $count_live ); ?></strong></a></li>
						<li><?php _e( 'Total Pending Coupons', APP_TD ); ?>: <a href="edit.php?post_status=pending&post_type=<?php echo APP_POST_TYPE; ?>"><strong><?php echo $count_pending; ?></strong></a></li>
						<li><?php _e( 'Product Version', APP_TD ); ?>: <strong><?php echo $clpr_version; ?></strong></li>
						<li><?php _e( 'Product Support', APP_TD ); ?>:  <a href="http://forums.appthemes.com/" target="_new"><?php _e( 'Forum', APP_TD ); ?></a> | <a href="http://www.appthemes.com/support/docs/" target="_new"><?php _e( 'Documentation', APP_TD ); ?></a></li>
					</ul>

					</div>

				</div> <!-- postbox end -->


				<div class="postbox">

					<div class="newspaperico"></div><a target="_new" href="<?php echo $app_rss_feed; ?>"><div class="rssico"></div></a>
					<h3 class="hndle" id="poststuff"><span><?php _e( 'Latest News', APP_TD ); ?></span></h3>


					<div class="inside" id="boxy">

						<?php appthemes_dashboard_appthemes(); ?>

					</div> <!-- inside end -->

				</div> <!-- postbox end -->

			</div> <!-- dash-wrap end -->

		</div> <!-- dash-left end -->


	<div class="dash-right metabox-holder">

	<div class="dash-wrap">

		<div class="postbox">

			<div class="statsico"></div>
			<h3 class="hndle" id="poststuff"><span><?php _e( 'Stats - Last 30 Days', APP_TD ); ?></span></h3>

			<div class="inside" id="boxy">

				<?php clpr_dashboard_charts(); ?>

			</div> <!-- inside end -->

			</div> <!-- postbox end -->


			<div class="postbox">

				<div class="twitterico"></div><a target="_new" href="<?php echo $app_twitter_rss_feed; ?>"><div class="rssico"></div></a>
				<h3 class="hndle" id="poststuff"><span><?php _e( 'Latest Tweets', APP_TD ); ?></span></h3>

				<div class="inside" id="boxy">

					<?php appthemes_dashboard_twitter(); ?>

				</div> <!-- inside end -->

			</div> <!-- postbox end -->


			<div class="postbox">
				<div class="forumico"></div><a target="_new" href="<?php echo $app_forum_rss_feed; ?>"><div class="rssico"></div></a>
				<h3 class="hndle" id="poststuff"><span><?php _e( 'Support Forum', APP_TD ); ?></span></h3>

				<div class="inside" id="boxy">

					<?php appthemes_dashboard_forum(); ?>

				</div> <!-- inside end -->

			</div> <!-- postbox end -->

		</div> <!-- dash-right end -->

	</div> <!-- dash-wrap end -->

</div> <!-- /wrap -->

<?php
}


// general settings admin page
function app_settings() {
	global $options_settings;

	appthemes_update_options( $options_settings );

	//print_r($options_settings);
	//die;
?>

	<div class="wrap">
			<div class="icon32" id="icon-tools"><br /></div>
		<h2><?php _e( 'General Settings', APP_TD ); ?></h2>

		<form method="post" id="mainform" action="">



				<?php appthemes_admin_fields( $options_settings ); ?>

			<p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e( 'Save changes', APP_TD ); ?>" /></p>
			<input name="submitted" type="hidden" value="yes" />
			<input name="setTabIndex" type="hidden" value="0" id="setTabIndex" />
		</form>
	</div>
<?php
}


function app_emails() {
	global $options_emails;

	appthemes_update_options( $options_emails );
?>

	<div class="wrap">
		<div class="icon32" id="icon-tools"><br /></div>
		<h2><?php _e( 'Email Settings', APP_TD ); ?></h2>

		<?php app_admin_info_box(); ?>

		<form method="post" id="mainform" action="">



			<?php appthemes_admin_fields( $options_emails ); ?>

			<p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e( 'Save changes', APP_TD ); ?>" /></p>
			<input name="submitted" type="hidden" value="yes" />
			<input name="setTabIndex" type="hidden" value="0" id="setTabIndex" />
		</form>
	</div>

<?php

}


// system information page
function app_system_info() {
	global $system_info, $wpdb, $app_version;
?>

		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br /></div>
			<h2><?php _e( 'System Info', APP_TD ); ?></h2>

			<?php // clpr_admin_info_box(); ?>

<?php
	// delete all the db tables if the button has been pressed.
	if ( isset( $_POST['deletetables'] ) )
		appthemes_delete_db_tables();

	// delete all the config options from the wp_options table if the button has been pressed.
	if ( isset( $_POST['deleteoptions'] ) )
		appthemes_delete_all_options();
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
					<ul class="tabs">
						<li><a href="#tab1"><?php _e( 'Debug Info', APP_TD ); ?></a></li>
						<li><a href="#tab2"><?php _e( 'Cron Jobs', APP_TD ); ?></a></li>
						<li><a href="#tab3"><?php _e( 'Uninstall', APP_TD ); ?></a></li>
					</ul>
					<div id="tab1">

					<table class="widefat fixed" style="width:850px;">

						<thead>
							<tr>
								<th scope="col" width="200px"><?php _e( 'Debug Info', APP_TD ); ?></th>
								<th scope="col">&nbsp;</th>
							</tr>
						</thead>

						<tbody>
							<tr>
								<td class="titledesc"><?php _e( 'Clipper Version', APP_TD ); ?></td>
								<td class="forminp"><?php echo $app_version; ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'WordPress Version', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'bloginfo' ) ) echo bloginfo( 'version' ); ?> <?php if ( is_multisite() ) echo '(Multisite)'; ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'PHP Version', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'phpversion' ) ) echo phpversion(); ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'Server Software', APP_TD ); ?></td>
								<td class="forminp"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'UPLOAD_MAX_FILESIZE', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'phpversion' ) ) echo ini_get( 'upload_max_filesize' ); ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'DISPLAY_ERRORS', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'phpversion' ) ) echo ini_get( 'display_errors' ); ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'FSOCKOPEN Check', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'fsockopen' ) ) echo '<font color="green">' . __( 'Your server supports fsockopen so PayPal IPN should work. If not, make sure your server is SSL enabled and port 443 is open on the firewall.', APP_TD ). '</font>'; else echo '<font color="red">' . __( 'Your server does not support fsockopen so PayPal IPN will not work.', APP_TD ). '</font>'; ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'OPENSSL Check', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'openssl_open' ) ) echo '<span style="color:green">' . __( 'Your server has Open SSL enabled which is needed for PayPal IPN to work. Also make sure port 443 is open on the firewall.', APP_TD ). '</span>'; else echo '<span style="color:red">' . __( 'Your server does not have Open SSL enabled so PayPal IPN will not work. Contact your host provider to have it enabled.', APP_TD ). '</span>'; ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'Theme Path', APP_TD ); ?></td>
								<td class="forminp"><?php if ( function_exists( 'bloginfo' ) ) { echo bloginfo( 'template_url' ); } ?></td>
							</tr>

							<tr>
								<td class="titledesc"><?php _e( 'Image Upload Path', APP_TD ); ?></td>
								<td class="forminp"><?php if ( !esc_attr( get_option( 'upload_path' ) ) ) echo 'wp-content/uploads'; else echo esc_attr( get_option( 'upload_path' ) ); ?><?php printf( ' - <a href="%s">' . __( '(change this)', APP_TD ) . '</a>', 'options-media.php' ); ?></td>                        </tr>

					</tbody>

					</table>

				</div> <!-- # tab1 -->

				<div id="tab2">

					<table class="widefat fixed" style="width:850px;">
						<thead>
							<tr>
								<th scope="col"><?php _e( 'Next Run Date', APP_TD ); ?></th>
								<th scope="col"><?php _e( 'Frequency', APP_TD ); ?></th>
								<th scope="col"><?php _e( 'Hook Name', APP_TD ); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$cron = _get_cron_array();
	$schedules = wp_get_schedules();
	$date_format = _x( 'M j, Y @ G:i', 'cron jobs date', APP_TD );
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
		?><em><?php _e( 'One-off event', APP_TD ); ?></em><?php
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

				</div> <!-- # tab2 -->

				<div id="tab3">

					<table class="widefat fixed" style="width:850px;">

					<thead>
						<tr>
							<th scope="col" width="200px"><?php _e( 'Uninstall Theme', APP_TD ); ?></th>
							<th scope="col">&nbsp;</th>
						</tr>
					</thead>

					<form method="post" id="mainform" action="">
						<tr>
							<td class="titledesc"><?php _e( 'Delete Database Tables', APP_TD ); ?></td>
							<td class="forminp">
								<p class="submit"><input class="button-secondary" onclick="return confirmBeforeDeleteTbls();" name="save" type="submit" value="<?php _e( 'Delete Clipper Database Tables', APP_TD ); ?>" /><br /><br />
							<?php _e( 'Do you wish to completely delete all theme database tables? Once you do this you will lose any custom fields, stores meta, etc that you have created.', APP_TD ); ?>
								</p>
								<input name="deletetables" type="hidden" value="yes" />
							</td>
						</tr>
					</form>


					<form method="post" id="mainform" action="">
						<tr>
							<td class="titledesc"><?php _e( 'Delete Config Options', APP_TD ); ?></td>
							<td class="forminp">
								<p class="submit"><input class="button-secondary" onclick="return confirmBeforeDeleteOptions();" name="save" type="submit" value="<?php _e( 'Delete Clipper Config Options', APP_TD ); ?>" /><br /><br />
							<?php _e( 'Do you wish to completely delete all theme configuration options? This will delete all values saved on the settings, pricing, etc admin pages from the wp_options database table.', APP_TD ); ?>
								</p>
								<input name="deleteoptions" type="hidden" value="yes" />
							</td>
						</tr>
					</form>

					</table>

				</div> <!-- # tab3 -->

			</div><!-- #tab-wrap -->



	<script type="text/javascript">
	/* <![CDATA[ */
	function confirmBeforeDeleteTbls() { return confirm("<?php _e( 'WARNING: You are about to completely delete all Clipper database tables. Are you sure you want to proceed? (This cannot be undone)', APP_TD ); ?>"); }
		function confirmBeforeDeleteOptions() { return confirm("<?php _e( 'WARNING: You are about to completely delete all Clipper configuration options from the wp_options database table. Are you sure you want to proceed? (This cannot be undone)', APP_TD ); ?>"); }
		/* ]]> */
		</script>


		</div><!-- #wrap -->


<?php
}

function clpr_csv_importer() {
	$fields = array(
		'coupon_title'       => 'post_title',
		'coupon_description' => 'post_content',
		'coupon_excerpt'     => 'post_excerpt',
		'coupon_status'      => 'post_status',
		'author'             => 'post_author',
		'date'               => 'post_date',
		'slug'               => 'post_name'
	);

	$args = array(
		'taxonomies'     => array( 'coupon_category', 'coupon_tag', 'coupon_type', 'stores' ),

		'custom_fields'  => array(
			'coupon_code'        => 'clpr_coupon_code',
			'expire_date'        => 'clpr_expire_date',
			'print_url'          => 'clpr_print_url',
			'id'                 => 'clpr_id',
			'coupon_aff_url'     => 'clpr_coupon_aff_url',
			'clpr_votes_down'    => array( 'default' => '0' ),
			'clpr_votes_up'      => array( 'default' => '0' ),
			'clpr_votes_percent' => array( 'default' => '100' )
		),

		'tax_meta' => array(
			'stores' => array(
				'store_aff_url' => 'clpr_store_aff_url',
				'store_url'     => 'clpr_store_url',
				'store_desc'    => 'clpr_store_desc',
			)
		)
	);

	$args = apply_filters( 'clpr_csv_importer_args', $args );

	new CLPR_Importer( 'coupon', $fields, $args );
}


class CLPR_Importer extends APP_Importer {

	function setup() {
		parent::setup();

		$this->args['parent'] = basename( __FILE__ );
	}
}

clpr_csv_importer();

