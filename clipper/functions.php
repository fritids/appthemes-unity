<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package Clipper
 * @author AppThemes
 */

// Define vars and globals
global $app_theme, $app_abbr, $app_version, $app_db_version, $app_edition, $app_form_results;

// current  version
$app_theme = 'Clipper';
$app_abbr = 'clpr';
$app_version = '1.4';
$app_db_version = 417;
$app_edition = '';
$app_stats = 'today';

// Define rss feed urls
$app_rss_feed = 'http://feeds2.feedburner.com/appthemes';
$app_twitter_rss_feed = 'http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=appthemes';
$app_forum_rss_feed = 'http://forums.appthemes.com/external.php?type=RSS2';

// define the custom fields used for custom search
$app_custom_fields = array($app_abbr.'_coupon_code', $app_abbr.'_expire_date', $app_abbr.'_featured', $app_abbr.'_id', $app_abbr.'_print_url');

define( 'APP_TD', 'clipper' );

// Framework
require( dirname(__FILE__) . '/framework/load.php' );

// Payments
require( dirname(__FILE__) . '/includes/payments/load.php' );

scb_register_table( 'app_pop_daily', $app_abbr . '_pop_daily' );
scb_register_table( 'app_pop_total', $app_abbr . '_pop_total' );

require( dirname(__FILE__) . '/framework/includes/stats.php' );

if ( is_admin() )
	require( dirname(__FILE__) . '/framework/admin/importer.php' );

// Theme-specific files
require( dirname(__FILE__) . '/includes/theme-functions.php' );

