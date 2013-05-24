<?php

/**
 * Initializes module for knowledgebase.
 *
 * @since 0.1
 *
 * @param array $args
 */
function add_knowledgebase_module( $args = array() ) {
	
	// Create Knowledgebase Post Type
	register_post_type(
		'knowledgebase', array(
			'label'				=> __( 'Knowledgebase', 'cheerapp' ),
			'labels'			=> array(
				'name'				=> __( 'Knowledgebase', 'cheerapp' ),
				'singular_name'		=> __( 'Knowledgebase Article', 'cheerapp'),
				'add_new'			=> _x( 'Add New', 'Knowledgebase article', 'cheerapp' ),
				'all_items'			=> __( 'All Knowledgebase Articles', 'cheerapp' ),
				'add_new_item'		=> __( 'Add New Knowledgebase Article', 'cheerapp' ),
				'edit_item'			=> __( 'Edit Knowledgebase Article', 'cheerapp' ),
				'new_item'			=> __( 'New Knowledgebase Article', 'cheerapp' ),
				'view_item'			=> __( 'View Knowledgebase Article', 'cheerapp' ),
				'search_items'		=> __( 'Search Knowledgebase Articles', 'cheerapp' ),
				'not_found'			=> __( 'No Knowledgebase Articles found', 'cheerapp' ),
				'not_found_in_trash'=> __( 'No Knowledgebase Articles found in Trash', 'cheerapp' )
			),
			'public'			=> true,
			'publicly_queryable'=> true,
			'exclude_from_search'=> false,
			'show_ui'			=> true,
			'menu_position'		=> 5,
			'capability_type'	=> 'post',
			'hierarchical'		=> false,
			'rewrite'			=> array( 'slug' => 'kb' ),
			'supports'			=> array(
				'title',
				'editor',
				'excerpt',
				'author',
				'revisions'
			),
			'has_archive'		=> true,
			'show_in_nav_menus'	=> true,
			'taxonomies'		=> array( 'kb_category' )
		)
	);
	
	// Register Knowledgebase Categories Taxonomy
	register_taxonomy(
		'kb_category', 'knowledgebase', array(
			'label'				=> __( 'Knowledgebase Categories', 'cheerapp' ),
			'labels'			=> array(
				'name'				=>	__( 'Knowledgebase Categories', 'cheerapp' ),
				'singular_name'		=>	__( 'Knowledgebase Category', 'cheerapp' ),
				'search_items'		=>	__( 'Search Knowledgebase Categories', 'cheerapp' ),
				'popular_items'		=>	__( 'Popular Knowledgebase Categories', 'cheerapp' ),
				'all_items'			=>	__( 'All Knowledgebase Categories', 'cheerapp' ),
				'parent_item'		=>	__( 'Parent Knowledgebase Category', 'cheerapp' ),
				'parent_item_colon'	=>	__( 'Parent Knowledgebase Category:', 'cheerapp' ),
				'edit_item'			=>	__( 'Edit Knowledgebase Category', 'cheerapp' ),
				'update_item'		=>	__( 'Update Knowledgebase Category', 'cheerapp' ),
				'add_new_item'		=>	__( 'Add new Knowledgebase Category', 'cheerapp' )
			),
			'public'			=> true,
			'hierarchical'		=> true,
			'rewrite'			=> array( 'slug' => 'topics', 'hierarchical' => true )
		)
	);
	
	// Create FAQ Post Type
	register_post_type(
		'faq', array(
			'label'				=> __( 'FAQ', 'cheerapp' ),
			'labels'			=> array(
				'name'				=> __( 'FAQs', 'cheerapp' ),
				'singular_name'		=> __( 'FAQ', 'cheerapp'),
				'add_new'			=> _x( 'Add New', 'FAQ', 'cheerapp' ),
				'all_items'			=> __( 'All FAQs', 'cheerapp' ),
				'add_new_item'		=> __( 'Add New FAQ', 'cheerapp' ),
				'edit_item'			=> __( 'Edit FAQ', 'cheerapp' ),
				'new_item'			=> __( 'New FAQ', 'cheerapp' ),
				'view_item'			=> __( 'View FAQ', 'cheerapp' ),
				'search_items'		=> __( 'Search FAQs', 'cheerapp' ),
				'not_found'			=> __( 'No FAQs found', 'cheerapp' ),
				'not_found_in_trash'=> __( 'No FAQs found in Trash', 'cheerapp' )
			),
			'public'			=> true,
			'publicly_queryable'=> true,
			'exclude_from_search'=> false,
			'show_ui'			=> true,
			'menu_position'		=> 5,
			'capability_type'	=> 'post',
			'hierarchical'		=> false,
			'rewrite'			=> array( 'slug' => 'faqs' ),
			'supports'			=> array(
				'title',
				'editor'
			),
			'has_archive'		=> true,
			'show_in_nav_menus'	=> true,
			'taxonomies'		=> array( 'faq_category' )
		)
	);
	
	// Register FAQ Categories Taxonomy
	register_taxonomy(
		'faq_category', 'faq', array(
			'label'				=> __( 'FAQ Groups', 'cheerapp' ),
			'labels'			=> array(
				'name'				=>	__( 'FAQ Groups', 'cheerapp' ),
				'singular_name'		=>	__( 'FAQ Group', 'cheerapp' ),
				'search_items'		=>	__( 'Search FAQ Groups', 'cheerapp' ),
				'popular_items'		=>	__( 'Popular FAQ Groups', 'cheerapp' ),
				'all_items'			=>	__( 'All FAQ Groups', 'cheerapp' ),
				'parent_item'		=>	__( 'Parent FAQ Group', 'cheerapp' ),
				'parent_item_colon'	=>	__( 'Parent FAQ Group:', 'cheerapp' ),
				'edit_item'			=>	__( 'Edit FAQ Group', 'cheerapp' ),
				'update_item'		=>	__( 'Update FAQ Group', 'cheerapp' ),
				'add_new_item'		=>	__( 'Add new FAQ Group', 'cheerapp' )
			),
			'public'			=> true,
			'hierarchical'		=> true,
			'rewrite'			=> array( 'slug' => 'faq-topics', 'hierarchical' => true ),
			'has_archive'		=> true
		)
	);
	
	// Add image sizes based on arguments
	$image_sizes = !empty( $args['image_sizes'] ) ? $args['image_sizes'] : null;
	if( $image_sizes ) {
		foreach( $image_sizes as $size ) {
			add_image_size( $size['name'], $size['width'], $size['height'], $size['crop'] );
		}
	}
	
	// Add custom meta boxes
	if( is_admin() ) {
		add_action( 'add_meta_boxes', 'royal_add_kb_meta_box' );
		add_action( 'save_post', 'royal_save_kb_postdata' );
	}
		
	function royal_add_kb_meta_box() {
		add_meta_box(
			'kb-custom-box',
			__( 'Article Options', 'cheerapp' ),
			'royal_kb_custom_box',
			'knowledgebase',
			'side'
			);
		}
	
	function royal_kb_custom_box( $post ) {
		global $post;
 
		// using an underscore, prevents the meta variable
		// from showing up in the custom fields section
		$meta = get_post_meta( $post->ID, '_royal_meta', true);
		 
		// instead of writing HTML here, lets do an include
		include( 'metaboxes/metabox-knowledgebase.php' );
		 
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="metabox_nonce" value="' . wp_create_nonce( 'save_metadata' ) . '" />';
	}
	
	function royal_save_kb_postdata( $post_id ) {
		
		$keys = array( '_royal_meta' );
		$id = royal_save_custom_postdata( $post_id, $keys );
	 
		return $id;
	}

}

?>