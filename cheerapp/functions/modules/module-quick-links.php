<?php

/**
 * Initializes module for quick links.
 *
 * @since 0.1
 *
 * @param array $args
 */
function add_quick_links_module( $args = array() ) {
	
	// Create Featured Post Type
	register_post_type('quick-links', array(
		'label' => __( 'Quick Links', 'cheerapp' ),
		'labels' => array(
			'name' => __( 'Quick Links', 'cheerapp' ),
			'singular_name' => __( 'Quick Link', 'cheerapp'),
			'add_new' => _x( 'Add New', 'quick link', 'cheerapp' ),
			'all_items' => __( 'All Quick Links', 'cheerapp' ),
			'add_new_item' => __( 'Add New Quick Link', 'cheerapp' ),
			'edit_item' => __( 'Edit Quick Link', 'cheerapp' ),
			'new_item' => __( 'New Quick Link', 'cheerapp' ),
			'view_item' => __( 'View Quick Link', 'cheerapp' ),
			'search_items' => __( 'Search Quick Links', 'cheerapp' ),
			'not_found' => __( 'No Quick Links found', 'cheerapp' ),
			'not_found_in_trash' => __( 'No Quick Links found in Trash', 'cheerapp' )
		),
		'public' => true,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'menu_position' => 5,
		'capability_type' => 'page',
		'hierarchical' => false,
		'rewrite' => array( 'slug' => 'quick-links' ),
		'supports' => array(
			'title',
			'page-attributes'
		),
		'has_archive' => false,
		'show_in_nav_menus' => false,
	) );
	
	// Add image sizes based on arguments
	$image_sizes = !empty( $args['image_sizes'] ) ? $args['image_sizes'] : null;
	if( $image_sizes ) {
		foreach( $image_sizes as $size ) {
			add_image_size( $size['name'], $size['width'], $size['height'], $size['crop'] );
		}
	}
	
	// Add custom meta boxes
	if( is_admin() ) {
		add_action( 'add_meta_boxes', 'royal_add_quick_links_meta_box' );
		add_action( 'save_post', 'royal_save_quick_links_postdata' );
	}
		
	function royal_add_quick_links_meta_box() {
		add_meta_box(
			'quick-links-custom-box',
			__( 'Link settings', 'cheerapp' ),
			'royal_quick_links_custom_box',
			'quick-links'
			);
		}
	
	function royal_quick_links_custom_box( $post ) {
		global $post;
 
		// using an underscore, prevents the meta variable
		// from showing up in the custom fields section
		$meta = get_post_meta( $post->ID, '_royal_meta', true);
		 
		// instead of writing HTML here, lets do an include
		include( 'metaboxes/metabox-quick-links.php' );
		 
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="metabox_nonce" value="' . wp_create_nonce( 'save_metadata' ) . '" />';
	}
	
	function royal_save_quick_links_postdata( $post_id ) {
		
		$keys = array( '_royal_meta' );
		$id = royal_save_custom_postdata( $post_id, $keys );
	 
		return $id;
	}

}

?>