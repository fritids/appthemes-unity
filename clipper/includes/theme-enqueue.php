<?php
/**
 * These are scripts used within the Clipper theme
 * To increase speed and performance, we only want to
 * load them when needed
 *
 * @package Clipper
 *
 */


// correctly load all the jquery scripts so they don't conflict with plugins
function clpr_load_scripts() {
		global $app_abbr;

		$protocol = is_ssl() ? 'https' : 'http';
		// load google cdn hosted libraries if enabled
		if ( get_option( $app_abbr.'_google_jquery' ) == 'yes' ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', $protocol . '://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false, '1.8.3' );
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jqueryeasing', get_bloginfo( 'template_directory' ) . '/includes/js/easing.js', array( 'jquery' ), '1.3' );
		wp_enqueue_script( 'jcarousellite', get_bloginfo( 'template_directory' ) . '/includes/js/jcarousellite_1.0.1.js', array( 'jquery' ), '1.0.1' );
		wp_enqueue_script( 'smoothscroll', get_bloginfo( 'template_directory' ) . '/includes/js/smoothscroll.js', array( 'jquery' ), '' );
		wp_enqueue_script( 'flashdetect', get_bloginfo( 'template_directory' ) . '/includes/js/flashdetect/flash_detect_min.js', array( 'jquery' ), '1.0.4' );
		wp_enqueue_script( 'zeroclipboard', get_bloginfo( 'template_directory' ) . '/includes/js/zeroclipboard/ZeroClipboard.js', array( 'jquery' ), '1.0.4' );
		wp_enqueue_script( 'theme-scripts', get_bloginfo( 'template_directory' ) . '/includes/js/theme-scripts.js', array( 'jquery' ), '3.0' );
		wp_enqueue_script( 'colorbox', get_bloginfo( 'template_directory' ) . '/includes/js/colorbox/jquery.colorbox-min.js', array( 'jquery' ), '1.3.9' );
		wp_enqueue_script( 'validate', get_bloginfo( 'template_directory' ) . '/includes/js/validate/jquery.validate.pack.js', array( 'jquery' ), '1.7' );

		// only load the general.js if available in child theme
		if ( file_exists( STYLESHEETPATH . '/general.js' ) )
			wp_enqueue_script( 'general', get_bloginfo( 'stylesheet_directory' ) . '/general.js', array( 'jquery' ), '1.0' );

		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

}

// load scripts required on coupon submission
function clpr_load_form_scripts() {
		global $app_abbr;

		// only load the tinymce editor when html is allowed
		if ( get_option($app_abbr.'_allow_html') == 'yes' ) {
				wp_enqueue_script( 'tiny_mce', includes_url('js/tinymce/tiny_mce.js'), array( 'jquery' ), '3.0' );
				wp_enqueue_script( 'wp-langs-en', includes_url('js/tinymce/langs/wp-langs-en.js'), array( 'jquery' ), '3241-1141' );
		}

		wp_enqueue_script('validate', get_bloginfo('template_directory').'/includes/js/validate/jquery.validate.pack.js', array('jquery'), '1.7');

		// add the language validation file if not english
		if ( get_option($app_abbr.'_form_val_lang') ) {
				$lang_code = strtolower( get_option($app_abbr.'_form_val_lang') );
				wp_enqueue_script('validate-lang', get_bloginfo('template_directory')."/includes/js/validate/localization/messages_$lang_code.js", array('jquery'), '1.6');
		}

}

// correctly load all the jquery scripts so they don't conflict with plugins
function clpr_load_styles() {
    // Load theme stylesheets

    // Master (or child) Stylesheet
    wp_enqueue_style( 'at-main', get_bloginfo( 'stylesheet_url' ) );

    // turn off stylesheets if customers want to use child themes
    if ( get_option( 'clpr_disable_stylesheet' ) <> 'yes' ) {
        if ( get_option( 'clpr_stylesheet' ) ) {
	    	wp_enqueue_style( 'at-color', get_bloginfo( 'template_directory' ) . '/styles/' . get_option( 'clpr_stylesheet' ) );
        } else {
	    	wp_enqueue_style( 'at-color', get_bloginfo( 'template_directory' ) . '/styles/red.css' . get_option( 'clpr_stylesheet' ) );
        }
    }

    // include the custom stylesheet
    if ( file_exists( TEMPLATEPATH . '/styles/custom.css' ) )
		wp_enqueue_style( 'at-custom', get_bloginfo( 'template_directory' ) . '/styles/custom.css' );

    // Load plugin stylesheets
    wp_register_style( 'colorbox', get_bloginfo( 'template_directory' ) . '/includes/js/colorbox/colorbox.css', false, '1.3.9' );
    wp_enqueue_style( 'colorbox' );

    wp_register_style( 'jquery-ui-style', get_bloginfo( 'template_directory' ) . '/includes/js/jquery-ui/jquery-ui.css', false, '1.9.2' );
    wp_enqueue_style( 'jquery-ui-style' );
}

// enqueue login page styles
function clpr_login_styles() {

	if ( file_exists(STYLESHEETPATH . '/styles/login-style.css') )
		wp_enqueue_style( 'login-style', get_bloginfo( 'stylesheet_directory' ) . '/styles/login-style.css', false );
	else
		wp_enqueue_style( 'login-style', get_bloginfo( 'template_directory' ) . '/styles/login-style.css', false );

}


// to speed things up, don't load these scripts in the WP back-end (which is the default)
if ( !is_admin() ) {
    add_action( 'wp_print_scripts', 'clpr_load_scripts' );
    add_action( 'wp_print_styles', 'clpr_load_styles' );
}

?>