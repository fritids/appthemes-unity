<?php

/**
 * Prints a link to the login page (if bbPress is active)
 *
 * @since 0.1
 *
 * @uses royal_get_page_by_template() To get the login page object
 */
function shortcode_login_link( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$page = royal_get_page_by_template( 'user-login' );
	
	if( !empty( $page ) ) :
		$out .= '<a href="' . get_permalink( $page->ID ) . '">';
		$out .= $content;
		$out .= '</a>';
		
		return $out;
	else :
		return $content;
	endif;
}
add_shortcode( 'login-link', 'shortcode_login_link' );

/**
 * Prints a link to the user registration page (if bbPress is active)
 *
 * @since 0.1
 *
 * @uses royal_get_page_by_template() To get the login page object
 */
function shortcode_register_link( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$page = royal_get_page_by_template( 'user-register' );
	
	if( !empty( $page ) ) :
		$out .= '<a href="' . get_permalink( $page->ID ) . '">';
		$out .= $content;
		$out .= '</a>';
		
		return $out;
	else :
		return $content;
	endif;
}
add_shortcode( 'register-link', 'shortcode_register_link' );

?>