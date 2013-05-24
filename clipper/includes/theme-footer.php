<?php

/**
 * Add footer elements via the wp_footer hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the footer of your theme
 *
 * @since 1.0
 * @uses clpr_footer_actions
 *
 */

// insert the google analytics tracking code in the footer
function clpr_google_analytics_code() {

    echo "\n\n" . '<!-- start wp_footer -->' . "\n\n";

    if (get_option('clpr_google_analytics') <> '')
        echo stripslashes(get_option('clpr_google_analytics'));

	?>
    <script type="text/javascript" >
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' );  ?>';
    </script>
    <?php

    echo "\n\n" . '<!-- end wp_footer -->' . "\n\n";

}

add_action('wp_footer', 'clpr_google_analytics_code');


// add the debug code to the footer
// must have the following added to the wp-config.php file in order to see queries
// define('SAVEQUERIES', true);
// NOTE: This will have a performance impact on your site, so make sure to turn this off when you aren't debugging.
function clpr_add_after_footer() {
	global $wpdb, $wp_query;

	if(get_option('clpr_debug_mode') == 'yes'):

		if (current_user_can('administrator')){
	?>
		<div class="clr"></div>
		<div class="debug">
			<h3><?php _e( 'Debug Mode On', APP_TD ); ?></h3>
			<br /><br />
			<h3>$wp_query->query_vars output</h3>
			<p><pre><?php print_r($wp_query->query_vars); ?></pre></p>
			<br /><br />
			<h3>$wpdb->queries output</h3>
			<p><pre><?php print_r($wpdb->queries); ?></pre></p>
		</div>

	<?php }

	endif;
}
// hook into the correct action
add_action('appthemes_after_footer', 'clpr_add_after_footer');
?>
