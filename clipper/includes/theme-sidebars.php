<?php
/**
 * Clipper Sidebars
 * This file defines sidebars for widgets.
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package Clipper
 *
 */

// Initialize all the sidebars so they are widgetized
function clpr_sidebars_init() {
	if ( !function_exists( 'register_sidebars' ) )
		return;

	//Home Page
	register_sidebar( array(
		'name' => __( 'Home Page Sidebar', APP_TD ),
		'id' => 'sidebar_home',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="customclass"></div><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Page Sidebar
	register_sidebar( array(
		'name' => __( 'Page Sidebar', APP_TD ),
		'id' => 'sidebar_page',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Blog Sidebar
	register_sidebar( array(
		'name' => __( 'Blog Sidebar', APP_TD ),
		'id' => 'sidebar_blog',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Coupon Sidebar
	register_sidebar( array(
		'name' => __( 'Coupon Sidebar', APP_TD ),
		'id' => 'sidebar_coupon',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Store Sidebar
	register_sidebar( array(
		'name' => __( 'Store Sidebar', APP_TD ),
		'id' => 'sidebar_store',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Share Coupon Page Sidebar
	register_sidebar( array(
		'name' => __( 'Submit Coupon Sidebar', APP_TD ),
		'id' => 'sidebar_submit',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Share Coupon Page Sidebar
	register_sidebar( array(
		'name' => __( 'Login Sidebar', APP_TD ),
		'id' => 'sidebar_login',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//User Sidebar
	register_sidebar( array(
		'name' => __( 'User Sidebar', APP_TD ),
		'id' => 'sidebar_user',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="sidebox %2$s"><div class="sidebox-content">',
		'after_widget' => '</div><br clear="all" /><div class="sb-bottom"></div></div>',
		'before_title' => '<div class="sidebox-heading"><h2>',
		'after_title' => '</h2></div>',
	) );

	//Footer Sidebar
	register_sidebar( array(
		'name' => __( 'Footer', APP_TD ),
		'id' => 'sidebar_footer',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="box customclass %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	) );

}

// tell WordPress to add these to the theme
add_action( 'init', 'clpr_sidebars_init' );