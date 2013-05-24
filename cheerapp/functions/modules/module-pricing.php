<?php

/**
 * Initializes module for pricing tables.
 *
 * @since 0.1
 *
 * @param array $args
 */
function add_pricing_module( $args = array() ) {
	
	// Create Pricing Post Type
	register_post_type('pricing', array(
		'label' => __( 'Pricing Plans', 'cheerapp' ),
		'labels' => array(
			'name' => __( 'Pricing Plans', 'cheerapp' ),
			'singular_name' => __( 'Pricing Plan', 'cheerapp'),
			'add_new' => _x( 'Add New', 'Pricing plan', 'cheerapp' ),
			'all_items' => __( 'All Pricing Plans', 'cheerapp' ),
			'add_new_item' => __( 'Add New Pricing Plan', 'cheerapp' ),
			'edit_item' => __( 'Edit Pricing Plan', 'cheerapp' ),
			'new_item' => __( 'New Pricing Plan', 'cheerapp' ),
			'view_item' => __( 'View Pricing Plan', 'cheerapp' ),
			'search_items' => __( 'Search Pricing Plans', 'cheerapp' ),
			'not_found' => __( 'No Pricing Plans found', 'cheerapp' ),
			'not_found_in_trash' => __( 'No Pricing Plans found in Trash', 'cheerapp' )
		),
		'public' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'menu_position' => 5,
		'capability_type' => 'page',
		'hierarchical' => false,
		'rewrite' => array( 'slug' => 'plans' ),
		'supports' => array(
			'title',
			'page-attributes'
		),
		'has_archive' => false,
		'show_in_nav_menus' => false,
		'taxonomies'		=> array( 'pricing_category' )
	) );
	
	// Register Pricing Categories Taxonomy
	register_taxonomy(
		'pricing_category', 'pricing', array(
			'label'				=> __( 'Pricing Categories', 'cheerapp' ),
			'labels'			=> array(
				'name'				=>	__( 'Pricing Categories', 'cheerapp' ),
				'singular_name'		=>	__( 'Pricing Category', 'cheerapp' ),
				'search_items'		=>	__( 'Search Pricing Categories', 'cheerapp' ),
				'popular_items'		=>	__( 'Popular Pricing Categories', 'cheerapp' ),
				'all_items'			=>	__( 'All Pricing Categories', 'cheerapp' ),
				'parent_item'		=>	__( 'Parent Pricing Category', 'cheerapp' ),
				'parent_item_colon'	=>	__( 'Parent Pricing Category:', 'cheerapp' ),
				'edit_item'			=>	__( 'Edit Pricing Category', 'cheerapp' ),
				'update_item'		=>	__( 'Update Pricing Category', 'cheerapp' ),
				'add_new_item'		=>	__( 'Add new Pricing Category', 'cheerapp' )
			),
			'public'			=> true,
			'publicly_queryable'=> false,
			'exclude_from_search'=> true,
			'show_ui'			=> true,
			'hierarchical'		=> true,
			'rewrite'			=> array( 'slug' => 'pricing-plans', 'hierarchical' => true )
		)
	);
	
	// Add custom meta boxes
	if( is_admin() ) {
		add_action( 'add_meta_boxes', 'royal_add_pricing_meta_box' );
		add_action( 'save_post', 'royal_save_pricing_postdata' );
	}
		
	function royal_add_pricing_meta_box() {
		add_meta_box(
			'pricing-custom-box',
			__( 'Plan details', 'cheerapp' ),
			'royal_pricing_custom_box',
			'pricing'
			);
		}
	
	function royal_pricing_custom_box( $post ) {
		global $post;
 
		// using an underscore, prevents the meta variable
		// from showing up in the custom fields section
		$meta = get_post_meta( $post->ID, '_royal_meta', true);
		 
		// instead of writing HTML here, lets do an include
		include( 'metaboxes/metabox-pricing.php' );
		 
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="metabox_nonce" value="' . wp_create_nonce( 'save_metadata' ) . '" />';
	}
	
	function royal_save_pricing_postdata( $post_id ) {
		
		$keys = array( '_royal_meta' );
		$id = royal_save_custom_postdata( $post_id, $keys );
	 
		return $id;
	}

}

?>