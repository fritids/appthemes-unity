<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "", strtolower($themename) );
	
	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);
	
	// echo $themename;
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {

	// Pull all the categories into an array
	$options_categories = array();  
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
    	$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
    	$options_pages[$page->ID] = $page->post_title;
	}
		
	// If using image radio buttons, define a directory path
	$imagepath =  get_stylesheet_directory_uri() . '/images/';
		
	$options = array();
	
	// GENERAL SETTINGS
	$options[] = array( "name" => __( 'General Settings', 'cheerapp' ),
						"type" => "heading" );
						
	$options[] = array( "name" => __( 'Top Logo', 'cheerapp' ),
						"desc" => __( 'Upload your logo image that will be used in page header', 'cheerapp' ),
						"id" => "logo_top",
						"type" => "upload");
						
	$options[] = array( "name" => __( 'Top Logo - HiDPI', 'cheerapp' ),
						"desc" => __( 'Header logo for HiDPI (&quot;Retina&quot;) screens', 'cheerapp' ),
						"id" => "logo_top_hidpi",
						"type" => "upload");
						
	$options[] = array( "name" => __( 'Top HiDPI logo width', 'cheerapp' ),
						"desc" => __( 'The width of your HiDPI top logo image (in pixels)', 'cheerapp' ),
						"id" => "logo_top_hidpi_width",
						"std" => '',
						"type" => "text");
						
	$options[] = array( "name" => __( 'Footer Logo', 'cheerapp' ),
						"desc" => __( 'Upload your logo image that will be used in the footer', 'cheerapp' ),
						"id" => "logo_footer",
						"type" => "upload");
						
	$options[] = array( "name" => __( 'Footer Logo - HiDPI', 'cheerapp' ),
						"desc" => __( 'Footer logo for HiDPI (&quot;Retina&quot;) screens', 'cheerapp' ),
						"id" => "logo_footer_hidpi",
						"type" => "upload");
						
	$options[] = array( "name" => __( 'Footer HiDPI logo width', 'cheerapp' ),
						"desc" => __( 'The width of your HiDPI footer logo image (in pixels)', 'cheerapp' ),
						"id" => "logo_footer_hidpi_width",
						"std" => '',
						"type" => "text");
						
	$options[] = array( "name" => __( 'Breadcrumb Home Link', 'cheerapp' ),
						"desc" => __( 'Display link to home page in breadcrumb trail', 'cheerapp' ),
						"id" => "breadcrumb_show_home_link",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Quick links', 'cheerapp' ),
						"desc" => __( 'Automatically place quick links widget on home page (if you choose not to, you can place the quick links panel manually using <code>[quick-links]</code> shortcode)', 'cheerapp' ),
						"id" => "show_home_quick_links",
						"std" => "1",
						"type" => "checkbox");
						
	$data_array = array( "right" => __( 'Right', 'cheerapp' ), "left" => __( 'Left', 'cheerapp' ) );
	$options[] = array( "name" => __( 'Sidebar position', 'cheerapp' ),
						"desc" => __( 'Choose the position of sidebar.', 'cheerapp' ),
						"id" => "sidebar_position",
						"std" => "right",
						"type" => "radio",
						"options" => $data_array );
						
	$options[] = array( "name" => __( 'Footer text', 'cheerapp' ),
						"desc" => __( 'A small text that appears under footer navigation', 'cheerapp' ),
						"id" => "footer_text",
						"std" => "CheerApp HTML template by <a href='http://themeforest.net/user/pogoking?ref=pogoking'>Mateusz Hajdziony</a>",
						"type" => "text");
	
	// SLIDER SETTINGS				
	/*
	$options[] = array( "name" => __( 'Slider Settings', 'cheerapp' ),
						"type" => "heading" );
						
	$options[] = array( "name" => __( 'Enable slider autoplay', 'cheerapp' ),
						"desc" => __( 'Slides will advance automatically.', 'cheerapp' ),
						"id" => "slider_autoplay",
						"std" => "0",
						"type" => "checkbox");
	*/
				
	// CALL TO ACTION SETTINGS
	$options[] = array( "name" => __( 'Call to Action Settings', 'cheerapp' ),
						"type" => "heading" );
						
	$options[] = array( "name" => __( 'Enable call to action panel on home page', 'cheerapp' ),
						"desc" => __( 'Enables call to action panel. You can adjust it&acute;s content below.', 'cheerapp' ),
						"id" => "cta_enable",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Heading', 'cheerapp' ),
						"desc" => __( 'The text displayed in heading of call to action panel', 'cheerapp' ),
						"id" => "cta_heading",
						"std" => 'CheerApp is now available on ThemeForest for as low as $35',
						"type" => "text");
						
	$options[] = array( "name" => __( 'Text', 'cheerapp' ),
						"desc" => __( 'The text below heading in call to action panel', 'cheerapp' ),
						"id" => "cta_text",
						"std" => "Get this WordPress theme for ridiculously low price and give your awesome app some great exposure!",
						"type" => "text");
						
	$options[] = array( "name" => __( 'Button URL', 'cheerapp' ),
						"desc" => __( 'The URL that call to action button links to', 'cheerapp' ),
						"id" => "cta_button_url",
						"std" => "http://themeforest.net/user/pogoking/portfolio?ref=pogoking",
						"type" => "text");
						
	$options[] = array( "name" => __( 'Button text', 'cheerapp' ),
						"desc" => __( 'The text displayed on call to action button', 'cheerapp' ),
						"id" => "cta_button_text",
						"std" => "Get CheerApp",
						"type" => "text");
	
	// BLOG SETTINGS
	$options[] = array( "name" => __( 'Blog Settings', 'cheerapp' ),
						"type" => "heading" );
						
	$options[] = array( "name" => __( 'Lightbox for single blog archive thumbnail', 'cheerapp' ),
						"desc" => __( 'Link post thumbnails on <strong>blog and blog archive pages</strong> to Lightbox gallery. If you leave this unchecked, post thumbnails will link to article instead.', 'cheerapp' ),
						"id" => "blog_thumbnail_lightbox",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Lightbox for single blog entry thumbnail', 'cheerapp' ),
						"desc" => __( 'Link post thumbnails on <strong>single blog entry</strong> page to Lightbox gallery. If you leave this unchecked, post thumbnails will link to nowhere instead.', 'cheerapp' ),
						"id" => "blog_single_thumbnail_lightbox",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Keep original image ratio of blog thumbnails', 'cheerapp' ),
						"desc" => __( 'Keep original ratio of thumbnails on blog pages and archives. Leave unchecked to automatically crop your images to <strong>615</strong>&nbsp;x&nbsp;<strong>140</strong>&nbsp;px', 'cheerapp' ),
						"id" => "blog_thumbnail_keep_ratio",
						"std" => "0",
						"type" => "checkbox");
	
	// FORUM SETTINGS
	$options[] = array( "name" => __( 'Forum Settings', 'cheerapp' ),
						"type" => "heading" );
						
	$options[] = array( "name" => __( 'Searchable topics', 'cheerapp' ),
						"desc" => __( 'Make forum topics searchable (only the leading post in the topic will be taken into account)', 'cheerapp' ),
						"id" => "forum_search_topic",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Searchable replies', 'cheerapp' ),
						"desc" => __( 'Make forum replies searchable', 'cheerapp' ),
						"id" => "forum_search_reply",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Searchable forums', 'cheerapp' ),
						"desc" => __( 'Users will be able to see forums and sub-forums in search results', 'cheerapp' ),
						"id" => "forum_search_forum",
						"std" => "0",
						"type" => "checkbox");
						
	// COLOR SETTINGS
	$options[] = array( "name" => __( 'Color Settings', 'cheerapp' ),
						"type" => "heading" );
						
	$options[] = array( "name" => __( 'Use custom color settings', 'cheerapp' ),
						"desc" => __( 'Check to make use of custom colors defined below in this section', 'cheerapp' ),
						"id" => "use_custom_colors",
						"std" => "0",
						"type" => "checkbox");
						
	$options[] = array( "name" => __( 'Main color', 'cheerapp' ),
						"desc" => __( 'A color used for slider and page top backgrounds', 'cheerapp' ),
						"id" => "color_main",
						"std" => "#79ba73",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Link', 'cheerapp' ),
						"desc" => __( 'Color used for links in default state', 'cheerapp' ),
						"id" => "color_link",
						"std" => "#71a66c",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Link - hover', 'cheerapp' ),
						"desc" => __( 'Color used for links when mouse hovers over them', 'cheerapp' ),
						"id" => "color_link_hover",
						"std" => "#82bf7c",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Link - pressed', 'cheerapp' ),
						"desc" => __( 'Color used for links that are being clicked', 'cheerapp' ),
						"id" => "color_link_active",
						"std" => "#5f8c5b",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - text color', 'cheerapp' ),
						"desc" => __( 'Color used for button text', 'cheerapp' ),
						"id" => "color_button_text",
						"std" => "#736840",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - gradient top color', 'cheerapp' ),
						"desc" => __( 'A starting color of button gradient', 'cheerapp' ),
						"id" => "color_button_top",
						"std" => "#ffeeb2",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - gradient bottom color', 'cheerapp' ),
						"desc" => __( 'An ending color of button gradient', 'cheerapp' ),
						"id" => "color_button_bottom",
						"std" => "#ebcf6a",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - border color', 'cheerapp' ),
						"desc" => __( 'Color used for button border', 'cheerapp' ),
						"id" => "color_button_border",
						"std" => "#ccb972",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - gradient fallback color', 'cheerapp' ),
						"desc" => __( 'Color used for button background if browser doesn&acute;t support CSS gradients', 'cheerapp' ),
						"id" => "color_button_fallback",
						"std" => "#f5d86e",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - hovered - text color', 'cheerapp' ),
						"desc" => __( 'Color used for button text when button is hovered', 'cheerapp' ),
						"id" => "color_button_hover_text",
						"std" => "#665c39",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - hovered - gradient top color', 'cheerapp' ),
						"desc" => __( 'A starting color of button gradient when button is hovered', 'cheerapp' ),
						"id" => "color_button_hover_top",
						"std" => "#ffeeb2",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - hovered - gradient bottom color', 'cheerapp' ),
						"desc" => __( 'An ending color of button gradient when button is hovered', 'cheerapp' ),
						"id" => "color_button_hover_bottom",
						"std" => "#f7da6f",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - hovered - gradient fallback color', 'cheerapp' ),
						"desc" => __( 'Color used for button background when button is hovered if browser doesn&acute;t support CSS gradients', 'cheerapp' ),
						"id" => "color_button_hover_fallback",
						"std" => "#ffe173",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - pressed - text color', 'cheerapp' ),
						"desc" => __( 'Color used for button text when button is pressed', 'cheerapp' ),
						"id" => "color_button_active_text",
						"std" => "#665c39",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - pressed - gradient top color', 'cheerapp' ),
						"desc" => __( 'A starting color of button gradient when button is pressed', 'cheerapp' ),
						"id" => "color_button_active_top",
						"std" => "#f5e5ab",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - pressed - gradient bottom color', 'cheerapp' ),
						"desc" => __( 'An ending color of button gradient when button is pressed', 'cheerapp' ),
						"id" => "color_button_active_bottom",
						"std" => "#ebcf6a",
						"type" => "color");
						
	$options[] = array( "name" => __( 'Button - pressed - gradient fallback color', 'cheerapp' ),
						"desc" => __( 'Color used for button background when button is pressed if browser doesn&acute;t support CSS gradients', 'cheerapp' ),
						"id" => "color_button_active_fallback",
						"std" => "#ebcf6a",
						"type" => "color");
	
	return $options;
}

?>