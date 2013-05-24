<?php

/**
 * Initializes module for featured posts (homepage slider).
 *
 * @since 0.1
 *
 * @param array $args
 */
function add_featured_posts_module( $args = array() ) {
	
	// Create Featured Post Type
	register_post_type('featured', array(
		'label' => __( 'Featured Posts', 'cheerapp' ),
		'labels' => array(
			'name' => __( 'Featured Posts', 'cheerapp' ),
			'singular_name' => __( 'Featured Post', 'cheerapp'),
			'add_new' => __( 'Add New', 'cheerapp' ),
			'all_items' => __( 'All Featured Posts', 'cheerapp' ),
			'add_new_item' => __( 'Add New Featured Post', 'cheerapp' ),
			'edit_item' => __( 'Edit Featured Post', 'cheerapp' ),
			'new_item' => __( 'New Featured Post', 'cheerapp' ),
			'view_item' => __( 'View Featured Post', 'cheerapp' ),
			'search_items' => __( 'Search Featured Posts', 'cheerapp' ),
			'not_found' => __( 'No Featured Posts found', 'cheerapp' ),
			'not_found_in_trash' => __( 'No Featured Posts found in Trash', 'cheerapp' )
		),
		'public' => true,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'menu_position' => 5,
		'capability_type' => 'page',
		'hierarchical' => false,
		'rewrite' => array( 'slug' => 'featured' ),
		'supports' => array(
			'title',
			'editor',
			'thumbnail',
			'page-attributes'
		),
		'has_archive' => false,
		'show_in_nav_menus' => false,
	) );
	
	// Add image sizes based on arguments
	$image_sizes = $args['image_sizes'];
	if( $image_sizes ) {
		foreach( $image_sizes as $size ) {
			add_image_size( $size['name'], $size['width'], $size['height'], $size['crop'] );
		}
	}
	
	// Add custom meta boxes
	if( is_admin() ) {
		add_action( 'add_meta_boxes', 'royal_add_featured_meta_box' );
		add_action( 'save_post', 'royal_save_featured_postdata' );
	}
		
	function royal_add_featured_meta_box() {
		add_meta_box(
			'featured-custom-box',
			__( 'Slide settings', 'cheerapp' ),
			'royal_featured_custom_box',
			'featured'
			);
		}
	
	function royal_featured_custom_box( $post ) {
		global $post;
 
		// using an underscore, prevents the meta variable
		// from showing up in the custom fields section
		$meta = get_post_meta( $post->ID, '_royal_meta', true);
		 
		// instead of writing HTML here, lets do an include
		include( 'metaboxes/metabox-featured-posts.php' );
		 
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="metabox_nonce" value="' . wp_create_nonce( 'save_metadata' ) . '" />';
	}
	
	function royal_save_featured_postdata( $post_id ) {
		
		$keys = array( '_royal_meta' );
		$id = royal_save_custom_postdata( $post_id, $keys );
	 
		return $id;
	}

}

?>