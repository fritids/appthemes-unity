<?php
if ( !class_exists( 'royalAjaxFrontend' ) ) {
	class royalAjaxFrontend	{
		
		function royalAjaxFrontend() { $this->__construct(); }	
		function __construct() {
			add_action( 'wp_print_scripts', array( &$this,'add_ajax_scripts' ) );
			
			// actions for like functionality
			add_action('wp_ajax_like', array(&$this, 'like_ajax'));
			add_action('wp_ajax_nopriv_like', array(&$this, 'like_ajax'));
			
			// actions for live search functionality
			add_action('wp_ajax_live_search', array(&$this, 'search_ajax'));
			add_action('wp_ajax_nopriv_live_search', array(&$this, 'search_ajax'));
			
			// actions for contact form functionality
			add_action('wp_ajax_contact', array(&$this, 'contact_form_ajax'));
			add_action('wp_ajax_nopriv_contact', array(&$this, 'contact_form_ajax'));
		}
		
		/**
		 * Checks if user with current ip address has already liked a post. If not, updates post 'likes' meta
		 * and sends WP Ajax Response
		 *
		 * @since 1.0
		 *
		 * @uses royal_get_likes_count
		 * @uses royal_get_likes_ips
		 */
		function like_ajax() {
			// Nonce security check
			check_ajax_referer( 'like' );
			
			// Check if post ID was sent, if not - exit
			if ( isset( $_POST['id'] ) ) {
				$post_id = $_POST['id'];
			} else {
				exit;
			}
			
			$response		=	new WP_Ajax_Response();
			$likes_count	=	royal_get_likes_count( $post_id );
			$ips			=	royal_get_likes_ips( $post_id ) ;
			$current_ip		=	$_SERVER[ 'REMOTE_ADDR' ];
			$ip_array		=	explode( ", ", $ips );
			
			// If user ip address is already in array (user has already voted)
			if( in_array( $current_ip, $ip_array ) ) {
				$response -> add(array(
					'what' => 'already-voted'
				));
			}
			// If user hasn't voted yet
			else {
				array_push( $ip_array, $current_ip );
				update_post_meta( $post_id, 'like_ips', implode( ", ", $ip_array ), $ips );
				update_post_meta( $post_id, 'likes', $likes_count + 1, $likes_count );
				
				$response -> add(array(
					'what' => 'like',
					'supplemental' => array(
						'count' => $likes_count + 1
					)
				));
			}
			
			// Send ajax response
			$response->send();
			
			exit;
		}
		
		/**
		 * Perform AJAX search
		 *
		 * @since 1.0
		 *
		 * @uses royal_show_pagination
		 */
		function search_ajax() {
			// Nonce security check
			check_ajax_referer('live_search');
			
			// Create new WP_Ajax_Response object that will hold response data
			$r = new WP_Ajax_Response();
			
			// Query arguments
			$args = array(
				's'				=>	$_REQUEST['s'],
				'posts_per_page'=>	6
			);
			
			// If post type was specified in AJAX request
			if( isset( $_REQUEST['post_type'] ) ) {
				$post_types = array();
				foreach( $_REQUEST['post_type'] as $type ) {
					$post_types[] = $type;
				}
				// Set received post types as query argument
				$args['post_type'] = $post_types;
			}
			
			// Create search query and loop through the posts, if any found
			$search_query = new WP_Query($args);
			if( $search_query->have_posts() ) :
				
				$results = array();
				
				while( $search_query->have_posts() ) : $search_query->the_post();
					
					$post_id		=	get_the_ID();
					$id_string		=	'_' . $post_id;
					$post_type		=	get_post_type( $post_id );
					$terms			=	null;
					$permalink		=	get_permalink( $post_id );
					
					if( $post_type == 'knowledgebase' ) :
						$terms = get_the_term_list( $post_id, 'kb_category', '', ' / ', '' );
					elseif( $post_type == 'faq' ) :
						$terms = get_the_term_list( $post_id, 'faq_category', '', ' / ', '' );
					elseif( $post_type == 'post' ) :
						$terms = get_the_term_list( $post_id, 'category', '', ' / ', '' );
					elseif( $post_type == 'reply' ) :
						$permalink = bbp_get_reply_url( $post_id );
					endif;
					
					$post_data = '<li class="' . $post_id . ' live-search-result">';
					$post_data .= '<h6><a href="' . $permalink . '">';
					$post_data .= get_the_title();
					$post_data .= '</a></h6>';
					
					if( 'topic' == $post_type || 'reply' == $post_type ) :
						$post_data .= '<span class="post-meta">';
						$post_data .= sprintf( __( '<small>in</small> <a href="%1$s">%2$s</a>', 'cheerapp' ), bbp_get_forum_permalink( bbp_get_topic_forum_id( $post_id ) ), bbp_get_forum_title( bbp_get_topic_forum_id( $post_id ) ) );
						$post_data .= '</span>';						
					elseif( $terms ) :
						$post_data .= '<span class="post-meta">';
						$post_data .= sprintf( __( '<small>in</small> %s', 'cheerapp' ) , $terms );
						$post_data .= '</span>';
					endif;
					
					$post_data .= '</li>';
					
					$results[ $id_string ] = $post_data;
				
				endwhile;
				
				// If pagination is needed append 'show all' link as last result
				if( royal_show_pagination( $search_query ) ) :
					$results[ 'more' ] = '<a href="#" class="more-results">' . __( 'View all results', 'cheerapp' ) . '</a>';
				endif;
				
				$r->add( array(
					'what'			=>	'results',
					'supplemental'	=>	$results
				) );
			
			// If no posts were found
			else:
				
				$r->add( array(
					'what'			=>	'no-posts',
					'supplemental'	=>	array(
						'message'		=>	__( 'No posts matched your criteria. Please try a different search', 'cheerapp' )
					)
				) );
				
			endif;
			
			// Send response
			$r->send();
			
			exit;
		}
		
		function contact_form_ajax() {
			check_ajax_referer('contact');
			
			echo royal_send_email( 'contact_ajax' );
			exit();
		}
		
		function add_ajax_scripts() {
			if ( is_admin() ) return;
			wp_localize_script( 'scripts', 'ajaxVars', $this->get_js_vars());
		}
		
		function get_js_vars() {
			return array(
				'ajax_url' => admin_url('admin-ajax.php'),
				// like localization
				'thanksForVoting'	=>	__( 'Thanks for voting!', 'cheerapp' ),
				'alreadyVoted'		=>	__( 'You have already liked this entry', 'cheerapp' ),
				// contact form messages
				'sent'				=>	__( 'Sent', 'cheerapp' ),
				// other
				'navigationTitle'	=>	__( 'Navigation', 'cheerapp' ),
				'categoryBrowserTitle'=>__( 'Browse by category', 'cheerapp' )
			);
		}
		
	}
}

if ( class_exists( 'royalAjaxFrontend' ) ) {
		$royal_ajax = new royalAjaxFrontend();
}
?>