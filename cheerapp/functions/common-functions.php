<?php

/**
 * Cleans up shortcode content from unnecessary empty <p> and <br> tags
 *
 * @since 0.1
 */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);
add_filter( 'the_content', 'shortcode_unautop',100 );

/**
 * Enables shortcodes in widgets
 *
 * @since 0.1
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Prints contact form
 *
 * @since 0.1
 *
 * @param mixes $args Function arguments
 * @uses royal_get_contact_form() To get contact form HTML
 */
function royal_contact_form( $args = array() ) {
	echo royal_get_contact_form( $args );
}
	
	/**
	 * Returns HTML for contact form
	 *
	 * @since 0.1
	 *
	 * @param mixed $args Function arguments
	 * @return string Contact form HTML
	 */
	function royal_get_contact_form( $args = array() ) {
	
		$defaults = array(
			'classes'		=>	array( 'contact-form', 'form-horizontal' ),
			'id'			=>	'contact-form',
			'use_honeycomb'	=>	true,
			'subject'		=>	''
		);
		
		$r = wp_parse_args( $args, $defaults );
		extract( $r );
		
		$classes = implode( ' ', $classes );
		$out = '';
		
		$out .= '<form id="' . $id . '" class="' . $classes . '" method="post">';
		
		if( $use_honeycomb ) :
			$out .= '<p class="contact-form-name always">';
			$out .= '<label for="royal_name">' . __( 'Please leave this field epmty', 'cheerapp' ) . '</label>';
			$out .= '<input type="text" name="royal_name" id="royal_name" tabindex="1000" />';
		endif;
		
		$out .= '<div class="contact-form-author control-group">';
		$out .= '<label for="royal_author" class="control-label">' . __( 'Name', 'cheerapp' ) . ' *</label>';
		$out .= '<div class="controls">';
		$out .= '<input type="text" name="royal_author" id="royal_author" tabindex="1" required="required" /></div></div>';
		
		$out .= '<div class="contact-form-email control-group">';
		$out .= '<label for="royal_email" class="control-label">' . __( 'Email', 'cheerapp' ) . ' *</label>';
		$out .= '<div class="controls">';
		$out .= '<input type="email" name="royal_email" id="royal_email" tabindex="2" required="required" /></div></div>';
		
		$out .= '<div class="contact-form-subject control-group">';
		$out .= '<label for="royal_subject" class="control-label">' . __( 'Subject', 'cheerapp' ) . '</label>';
		$out .= '<div class="controls">';
		$out .= '<input type="text" name="royal_subject" id="royal_subject" tabindex="3"';
		if( $subject ) {
			$out .= ' value="' . $subject . '" disabled="disabled"';
		}
		$out .= ' /></div></div>';
		
		$out .= '<div class="contact-form-message control-group">';
		$out .= '<label for="royal_message" class="control-label">' . __( 'Message', 'cheerapp' ) . '</label>';
		$out .= '<div class="controls">';
		$out .= '<textarea name="royal_message" id="royal_message" tabindex="4" required="required"></textarea></div></div>';
		
		$out .= '<div id="response">';
	
		$email_sent = royal_send_email();
		if( $email_sent ) {
			$out .= $email_sent;
		}
		
		$out .= '</div>';
		
		$out .= '<div class="contact-form-submit-wrapper form-actions">';
		$out .= wp_nonce_field( 'contact', '_contact_nonce', false, false );
		if( is_single() || is_page() ) :
			$out .= '<input type="hidden" name="royal_permalink" value="' . get_permalink() . '" />';
		endif;
		$out .= '<input type="hidden" name="action" value="contact" />';
		$out .= '<button class="button icon button-mail" type="submit" id="submit" tabindex="5">' . __( 'Send message', 'cheerapp' ) . '</button>';
		$out .= '</div>';
		
		$out .= '</form>';
		
		return $out;
	}
	
/**
 * Send and email from contact from data
 *
 * @since 0.1
 *
 * @return string An HTML of response messages
 */
function royal_send_email( $action = null ) {
	if ( ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'contact' ) || ( defined( 'DOING_AJAX' ) && $action == 'contact_ajax' ) ) {
		if( !empty( $_POST['royal_name'] ) ) {
			die( __( 'We don&acute; take kindly on robots here.', 'cheerapp' ) );
		}
		else {
			$out		= '';
			$name		= $_POST['royal_author'];
			$email		= $_POST['royal_email'];
			$subject	= $_POST['royal_subject'];
			$message	= $_POST['royal_message'];
			$permalink	= $_POST['royal_permalink'];
			$to			= get_option( 'admin_email' );
			
			if( !$name ) {
				$out .= '<p class="error">' . __( 'Please specify your name', 'cheerapp' ) . '</span>';
			}
			if( !is_email( $email ) ) {
				$out .= '<p class="error">' . __( 'Please specify a valid e-mail address', 'cheerapp' ) . '</span>';
			}
			if( !$message ) {
				$out .= '<p class="error">' . __( 'Please specify a message', 'cheerapp' ) . '</span>';
			}
			
			if( $name && $message && is_email( $email ) ) {				
				if( $subject ) {
					$sbj = '[' . get_option( 'blogname' ) . '] ' . $subject;
				}
				else {
					$sbj = '[' . get_option( 'blogname' ) . '] ' . __( 'No subject', 'cheerapp' );
				}
				
				$msg = sprintf( __( 'This message has been sent by %s.', 'cheerapp' ), $name );
				if( !empty( $permalink ) ) {
					$msg .= ' ' . __( 'via', 'cheerapp' ) . ' ' . $permalink;
				}
				$msg .= "\n" . __( 'You can reply to this message to respond.', 'cheerapp' )
				. "\n\n------------------\n\n" . $message;
				$headers = 'From: ' . $name . ' <' . $email . ">\r\n";
				wp_mail( $to, $sbj, $msg, $headers );
				
				$out .= '<p class="success">' . __( 'Your message has been sent. We will reply as soon as possible!', 'cheerapp' ) . '</span>';
			}
			
			return( $out );
		}
	}
	else {
		return false;
	}
}

/**
 * Saves data from custom meta boxes when post is being saved.
 *
 * @since 0.1
 * 
 * @uses royal_meta_cleanup
 * @param int $post_id ID of post being saved.
 * @param array $keys An array of meta keys being saved.
 */
function royal_save_custom_postdata( $post_id = null, $keys = array() ) {
	
	// Authentication checks
	 
	// Make sure data came from our meta box
	if ( empty( $_POST['metabox_nonce'] ) || !wp_verify_nonce( $_POST['metabox_nonce'], 'save_metadata' ) ) return $post_id;
 
	// Check user permissions
	if ($_POST['post_type'] == 'page') {
		if ( !current_user_can( 'edit_page', $post_id ) ) return $post_id;
	}
	else {
		if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
	}
 
	// authentication passed, save data
	foreach( $keys as $key ) {
		$current_data = get_post_meta( $post_id, $key, true );
		$new_data = $_POST[$key];
		
		royal_meta_cleanup( $new_data );
		
		if ( $current_data ) {
			if ( is_null( $new_data ) ) delete_post_meta( $post_id, $key );
			else update_post_meta( $post_id, $key, $new_data );
		}
		elseif ( !is_null( $new_data ) ) {
			add_post_meta( $post_id, $key, $new_data, true );
		}
	}
	
	return $post_id;
}

/**
 * Cleans up post meta array before saving
 *
 * @since 0.1
 *
 * @uses royal_meta_cleanup
 * @param mixed $arr
 */
function royal_meta_cleanup( &$arr ) {
	if( is_array( $arr ) )
	{
		foreach( $arr as $i => $v ) {
			if( is_array( $arr[$i] ) ) {
				royal_meta_cleanup( $arr[$i] );
 
				if ( !count( $arr[$i] ) ) 
				{
					unset( $arr[$i] );
				}
			}
			else {
				if ( trim($arr[$i]) == '' ) {
					unset( $arr[$i] );
				}
			}
		}
 
		if( !count( $arr ) ) {
			$arr = NULL;
		}
	}
}

/**
 * Tests if the pagination is needed for current query
 *
 * @since 0.1
 *
 * @return bool
 */
function royal_show_pagination( $wp_query = null ) {
	if( !$wp_query ) {
		global $wp_query;
	}
	return ( $wp_query->max_num_pages > 1 );
}

/**
 * Add 'likes' meta when the post is saved
 *
 * @since 0.1
 *
 * @param int $post_id
 */
function royal_add_like_meta( $post_id ) {
	global $post_id;

	$key = 'likes';
	$current = get_post_meta( $post_id, $key, true );
	
	if( !$current ) {
		add_post_meta( $post_id, $key, 0, true );
	}
}
add_action( 'save_post', 'royal_add_like_meta', 10, 1 );

/**
 * Returns the number of times a shortcode has been used within a post
 *
 * @since 0.1
 *
 * @param object $post A post object. If not supplied global $post will be used.
 * @param string $shortcode A shortcode to check.
 * @return int Number of matches
 */ 
function royal_get_shortcode_count( $post = null, $shortcode = '' ) {
	if( !$post ) {
		global $post;
	}
	
	$count;
	$pattern = get_shortcode_regex();
	preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );
	
	if( is_array( $matches ) && array_key_exists( 2, $matches ) && in_array( $shortcode, $matches[2] ) && $shortcode != '' ) {
		foreach( $matches[2] as $match ) {
			if( $match == $shortcode ) {
				$count++;
			}
		}
		return $count;
	}
	else {
		return 0;
	}
}

/** Constructs and echoes breadcrumb trail.
 *
 * @since 0.1
 *
 * @param mixed $args Function arguments
 * @uses royal_get_category_parents() To retrieve hierarchical list of parent categories
 * @uses royal_get_page_by_template() To retrieve page with specific page template
 * @uses royal_get_low_level_term() To retrieve lowest level term
 */
function royal_breadcrumb( $args = '' ) {

	$defaults = array(
		'show_home_link'	=>	true,
		'home_text'			=>	__( 'Home', 'cheerapp' ),
		'before'			=>	'<div class="breadcrumb">',
		'after'				=>	'</div>',
		'before_trail'		=>	'<span>',
		'after_trail'		=>	'</span>',
		'sep'				=>	' / '
	);
	
	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$post_type = is_single() ? get_post_type() : null;
	$current_term;
	$bc = '';

	$bc .= $before . $before_trail;
	
	// If current page corresponds to bbPress forum
	if( $post_type == 'forum' || $post_type == 'topic' || $post_type == 'reply' || is_post_type_archive( 'forum' ) || is_tax( 'topic-tag' ) ) {
		$args = array(
			'before'			=>	$before . $before_trail,
			'after'				=>	$after_trail,
			'sep'				=>	$sep,
			'include_home'		=>	$show_home_link,
			'home_text'			=>	$home_text,
			'include_current'	=>	false
		);
		if( is_post_type_archive( 'forum' ) ) $args['include_current'] = true;
		echo bbp_get_breadcrumb( $args );
		
		if( is_tax( 'topic-tag' ) ) :
			echo sprintf( __( 'Topic Tag: %s', 'bbpress' ), '<span>' . bbp_get_topic_tag_name() . '</span>' );
		elseif( $post_type == 'topic' || $post_type == 'reply' || is_post_type_archive( 'forum' ) ) :
			// Do nothing
		else :
			echo get_the_title();
		endif;
		
		echo $after;
	}
	else {
		if ( !is_front_page() && $show_home_link == true ) {
			$bc .= '<a href="' . home_url() . '">';
			$bc .= $home_text;
			$bc .= '</a>' . $sep;
		}
		
		// If it's a Blog post or blog archive
		if ( is_category()					||	'post' == $post_type ) {
			$page = royal_get_page_by_template( 'blog' );
			$bc .= '<a href="' . get_page_link( $page->ID ) . '">' . $page->post_title . '</a>' . $sep;
			
			$current_term = royal_get_low_level_term( 'category' );
			$bc .= royal_get_term_parents( $current_term->term_id, 'category', true, $sep, false, array(), array( $current_term->term_id ) );
		}
		// If it's a Knowledgebase post or Knowledgebase archive
		elseif ( is_tax( 'kb_category' )	||	'knowledgebase' == $post_type ) {
			
			$page = royal_get_page_by_template( 'knowledgebase' );
			$bc .= '<a href="' . get_page_link( $page->ID ) . '">' . $page->post_title . '</a>' . $sep;
			
			$current_term = royal_get_low_level_term( 'kb_category' );
			$bc .= royal_get_term_parents( $current_term->term_id, 'kb_category', true, $sep, false, array(), array( $current_term->term_id ) );
		}
		elseif( is_tax( 'faq_category' )	||	'faq' == $post_type ) {
			$page = royal_get_page_by_template( 'faq' );
			$bc .= '<a href="' . get_page_link( $page->ID ) . '">' . $page->post_title . '</a>' . $sep;
			
			$current_term = royal_get_low_level_term( 'faq_category' );
			$bc .= royal_get_term_parents( $current_term->term_id, 'faq_category', true, $sep, false, array(), array( $current_term->term_id ) );
		}
		
		if ( is_page() ) {
			global $post;
			$trail = array();
			$parent = $post;
			while ( $parent->post_parent ) {
				$parent = get_post( $parent->post_parent );
				array_push( $trail, $parent );
			}
			foreach ( $trail as $page ){
				if ( $page->ID != $post->ID ) {
					$bc .= '<a href="' . get_permalink( $page->ID ) . '">' . $page->post_title . '</a> / ';
				}
			}
		}
		
		$bc .= $after_trail;
	
		if( is_page() )
			$bc .= get_the_title();
			
		if( is_category() || 'post' == $post_type ) {
			$category_link = get_term_link( $current_term );
			$bc .= '<a href="' . $category_link . '">' . $current_term->name . '</a>';
		}
		elseif ( is_tax( 'kb_category' )	||	'knowledgebase' == $post_type ) {
			$category_link = get_term_link( $current_term );
			$bc .= '<a href="' . $category_link . '">' . $current_term->name . '</a>';
		}
		elseif ( is_tax( 'faq_category' )	||	'faq' == $post_type ) {
			$category_link = get_term_link( $current_term );
			$bc .= '<a href="' . $category_link . '">' . $current_term->name . '</a>';
		}
		
		if( is_tag() )
			$bc .= __( 'Tag', 'cheerapp' ) . ": " . single_tag_title( '', false );
			
		if( is_search() )
			$bc .= __( 'Search results', 'cheerapp' );
			
		if( is_year() )
			$bc .= get_the_time( 'Y' );
			
		if( is_month() )
			$bc .= get_the_time( 'F Y' );
			
		if( is_day() )
			$bc .= get_the_time( 'F j, Y' );
		
		// The is_404() check is in 'else' statement to fix a bug in bbPress which would cause the is_404() check returning 'true' when viewing user profile
		// If bbPress is not present (function bbp_get_displayed_user_id doesn't exist) we continue with 404 check as normal
		if( function_exists( 'bbp_get_displayed_user_id' ) ) {
			if( bbp_get_displayed_user_id() ) {
				$bc .= __( 'User profile', 'cheerapp' ) . ': ' . bbp_get_displayed_user_field( 'display_name' );
			}
			elseif( is_404() ) {
				$bc .= __( '404 - Page not Found', 'cheerapp' );
			}
		}
		elseif( is_404() ) {
			$bc .= __( '404 - Page not Found', 'cheerapp' );
		}
		
		$bc .= $after;
		
		echo $bc;
	}
}

/**
 * Retrieves the lowest level term for current post
 * or current taxonomy term if on term archive
 *
 * @since 0.1
 *
 * @param string $taxonomy Taxonomy for which to retrieve the term
 * @return object Term object
 */
function royal_get_low_level_term( $taxonomy ) {
	$term = null;

	if( is_single() ) {
		global $post;
		$terms = get_the_terms( $post->ID, $taxonomy );
		
		if( count( $terms ) == 1 ) {
			foreach( $terms as $t ) {
				$term = $t;
			}
		}
		else {
			foreach( $terms as $t ) {
				if( $t->parent ) {
					$term = $t;
				}
			}
			if( !$term ) {
				$count = 0;
				foreach( $terms as $t ) {
					$term = $count == 0 ? $t : $term;
					$count ++;
				}
			}
		}
	}
	elseif( is_category() ) {		
		$term = get_term( get_query_var( 'cat' ), $taxonomy );
	}
	elseif( is_tax() ) {
		$term = get_term_by( 'slug', get_query_var( $taxonomy ), $taxonomy );
	}
	
	return $term;
}

/**
 * Retrieve category parents with separator.
 *
 * @since 0.1
 *
 * @param int $id Category ID.
 * @param string $taxonomy Taxonomy.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate categories.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to categories to prevent duplicates.
 * @param array $exclude Optional. IDs of categories to exclude.
 * @return string
 */
function royal_get_term_parents( $id, $taxonomy = 'category', $link = false, $separator = ' / ', $nicename = false, $visited = array(), $exclude = array() ) {
	$chain = '';
	$parent = get_term( $id, $taxonomy );
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename )
		$name = $parent->slug;
	else
		$name = $parent->name;

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= royal_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited, $exclude );
	}
	
	if( !in_array( $parent->term_id, $exclude ) ) {
		if ( $link )
			$chain .= '<a href="' . get_term_link( $parent ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'cheerapp' ), $parent->name ) ) . '">' . $name . '</a>' . $separator;
		else
			$chain .= $name.$separator;
	}
	return $chain;
}

/**
 * Returns first matched page that uses specified template.
 *
 * @since 0.1
 *
 * @param string $template_name The name of template to look for.
 * @return object Page object
 */
function royal_get_page_by_template( $template_name ) {
	$page;
	$allpages = get_pages();
	foreach( $allpages as $pagg ) {
		$template = get_post_meta( $pagg->ID, '_wp_page_template', true );
		if( $template == 'template-' . $template_name . '.php' ) {
			$page = $pagg;
		}
	}
	
	return $page;
}

/**
 * Prints pricing table
 *
 * @since 0.1
 *
 * @param int $show Number of pricing plans to display
 * @param int $highlight Pricing plan to be highlighted initially
 * @param int $category Term ID from which to retrieve pricing plans
 * @uses royal_get_pricing_table() To get pricing table HTML
 */
function royal_pricing_table( $show = '', $highlight = 2, $category = '' ) {
	echo royal_get_pricing_table( $show, $highlight, $category );
}
	
	/**
	 * Returns HTML for pricing table
	 *
	 * @since 0.1
	 *
	 * @param mixed @atts
	 * @return string Pricing table HTML
	 */
	function royal_get_pricing_table( $show = '', $highlight = 2, $category = '' ) {
		
		$show = !empty( $show ) && $show < 6 ? $show : 6;
		
		$args = array (
			'post_type' => 'pricing',
			'showposts'	=>	$show,
			'orderby'	=>	'menu_order',
			'order'		=>	'ASC'
		);
		if( !empty( $category ) && intval( $category ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'		=>	'pricing_category',
					'field'			=>	'id',
					'terms'			=>	array( $category )
				)
			);
		}
		$plans = get_posts( $args );
		
		if( $plans ) :
		
			$out = '<table id="pricing" class="pricing" cellpadding="0" cellspacing="0"><tr>';
			
			$count = 0;
				
			foreach( $plans as $plan ) {
						
				$meta = get_post_meta( $plan->ID, '_royal_meta', true );
				$count++;
				
				$out .= '<td';
				if( $count == $highlight ) $out .= ' class="highlight"';
				$out .= '><div class="pricing-plan">';
				
				$out .= '<h2>' . $plan->post_title . '</h2>';
				
				if( $meta['price'] ) :
							
					$out .= '<div class="pricing-plan-price visible-phone">';
					$out .= '<span>' . $meta['price'] . '</span>';
					
					if( $meta['pricing_info'] ) :
						$out .= '<span class="pricing-price-detail"> / ' . $meta['pricing_info'] . '</span>';
					endif;
					
					$out .= '</div>';
								
				endif;
								
				if( $meta['url'] ) :
					$button_text = !empty( $meta['button_text'] ) ? $meta['button_text'] : __( 'Choose plan', 'cheerapp' );
					$out .= '<a class="button" href="' . $meta['url'] . '">' . $button_text . '</a>';
				endif;
								
				$out .= '</div>';
							
				if( $meta['plan_features'] ) :
							
					$out .= '<div class="pricing-plan-details">';
					$out .= '<ul class="pricing-plan-details-list">';
									
					foreach( $meta['plan_features'] as $f ) {
										
						$out .= '<li>';
						
						if( $f['value'] ) :
							$out .= '<span class="pricing-detail-value">' . $f['value'] . '</span>';
						endif;
						if( $f['key'] ) :
						$out .= ' <span class="pricing-detail-key"> ' . $f['key'] . '</span>';
						endif;
						if( !empty( $f['detail'] ) ) :
						$out .= ' <span class="pricing-detail-more tooltip-white" title="' . $f['detail'] . '"></span>';
						endif;
						
						$out .= '</li>';
										
					}
										
					$out .= '</ul>';
					$out .= '</div>';
								
				endif;
							
				if( $meta['price'] ) :
							
					$out .= '<div class="pricing-plan-price hidden-phone">';
					$out .= '<span>' . $meta['price'] . '</span>';
					
					if( $meta['pricing_info'] ) :
						$out .= '<span class="pricing-price-detail"> / ' . $meta['pricing_info'] . '</span>';
					endif;
					
					$out .= '</div>';
								
				endif;
				
				$out .= '</td>';
						
			}
										
			$out .= '</tr>';
			$out .= '</table>';
		
		endif;
		
		return $out;
		
	}


class Royal_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Overwrites WordPress' default display_element function.
	 * Adds a 'parent' class to each menu item that has
	 * a sub-menu.
	 *
	 * @since 0.1
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		
		//Adds the 'parent' class to the current item if it has children		
		if( ! empty( $children_elements[$element->$id_field] ) )
			array_push($element->classes,'parent');
		
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		
		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}

?>