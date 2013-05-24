<?php

/**
 * Constructs a like link object
 *
 * @since 0.1
 *
 * @uses royal_get_likes_count
 * @uses royal_get_like_url
 * @param int $post_id Post ID. If not supplied the ID of global $post is used.
 * @param bool $echo Weather echo or return the link.
 * @return string
 */
function royal_likes( $post_id = 0, $echo = true ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$likes_count = royal_get_likes_count( $post_id );
	$like_url = royal_get_like_url( $post_id );
	
	$like_link = '<a href="' . $like_url . '" class="likes like">' . $likes_count . '</a>';
	
	if( $echo = true ) : 
		echo $like_link;
	else :
		return $like_link;
	endif;
}

/**
 * Counts the number of likes on a post and echoes it
 *
 * @since 0.1
 *
 * @uses royal_get_likes_count
 * @param int $post_id Post ID. If not supplied the ID of global $post is used.
 */
function royal_likes_count( $post_id = 0 ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$likes_count = royal_get_likes_count( $post_id );
	
	echo $likes_count;
}

/**
 * Returns the number of likes on a post
 *
 * @since 0.1
 *
 * @param int $post_id Post ID. If not supplied the ID of global $post is used.
 * @return int Likes count
 */
function royal_get_likes_count( $post_id = 0 ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$key = 'likes';
	$likes_count = get_post_meta( $post_id, $key, true );
	
	if( !$likes_count ) {
		add_post_meta( $post_id, $key, 0, true );
		$likes_count = 0;
	}
	
	return $likes_count;
}

/**
 * Constructs a URL for like link/button
 *
 * @since 0.1
 *
 * @param int $post_id Post ID. If not supplied the ID of global $post is used.
 * @return string
 */
function royal_get_like_url( $post_id = 0 ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$like_url = esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=like&id=' . $post_id ), 'like' ) );
	
	return $like_url;
}

/**
 * Returns a string containing ip addresses of all people who liked a post.
 * If there is no 'like_ips' meta at all - add one.
 *
 * @since 0.1
 *
 * @param int $post_id Post ID. If not supplied the ID of global $post is used.
 * @return string
 */
function royal_get_likes_ips( $post_id = 0 ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$like_ips = get_post_meta( $post_id, 'like_ips', true );
	
	if( !$like_ips ) {
		add_post_meta( $post_id, 'like_ips', array(), true );
		$like_ips = get_post_meta( $post_id, 'like_ips', true );
	}
	
	return $like_ips;
}

/**
 * Prints Quick Links
 *
 * @since 0.1
 *
 * @uses royal_get_quick_links to get the HTML for Quick Links
 * @param mixed $args
 */
function royal_quick_links( $args = array() ) {
	echo royal_get_quick_links( $args );
}

	/**
	 * Returns Quick Links
	 *
	 * @since 0.1
	 *
	 * @param mixed $args
	 * @return string HTML for Quick Links
	 */
	function royal_get_quick_links( $args = array() ) {
	
		$defaults = array (
			'before'       	=>	'<div class="quick-links-wrap"><div id="quick-links" class="quick-links">',
			'after'			=>	'</div></div>',
			'class'			=>	array(),
			'link_class'	=>	array()
		);
	
		$r = wp_parse_args( $args, $defaults );
		extract( $r );
		
		$classes = implode( ' ', $class );
		$link_classes = implode( ' ', $link_class );
		$q = '';
	
		$a = array(
			'post_type' => 'quick-links',
			'orderby' => 'menu_order',
			'order' => 'ASC'
		);
		$quick_links = new WP_Query( $a );
		
		if( $quick_links->have_posts() ) :
		
			$q .= $before;
			$q .= '<ul class=" ' . $classes . '">';
			
			while( $quick_links->have_posts() ) : $quick_links->the_post();
			
				$post_id	=	get_the_ID();
				$meta		=	get_post_meta( $post_id, '_royal_meta', true );
				$link_url	=	$meta['url'];
				$link_class	=	!empty( $meta['icon'] ) ? 'quick-link-icon ' . $meta['icon'] : '';
				$link_title	=	!empty( $meta['description'] ) ? $meta['description'] : '';
				
				$q .= '<li>';
				$q .= '<a href="' . $link_url . '" class="quick-link ' . $link_class . ' ' . $link_classes . '" title="' . $link_title . '">';
				$q .= get_the_title();
				$q .= '</a></li>';
				
			endwhile;
			
			$q .= $after;
			
		endif;
		
		return $q;
	}
?>