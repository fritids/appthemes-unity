<?php

/**
 * Creates column with size of 1/3 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_third( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span4">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'third',		'shortcode_third' );
add_shortcode( 'one-third',	'shortcode_third' );

/**
 * Creates column with size of 2/3 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_two_thirds( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span8">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'two-thirds', 'shortcode_two_thirds' );

/**
 * Creates column with size of 1/2 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_half( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span6">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'half', 'shortcode_half' );

/**
 * Creates column with size of 1/4 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_fourth( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span3">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'fourth',		'shortcode_fourth' );
add_shortcode( 'one-fourth',	'shortcode_fourth' );

/**
 * Creates column with size of 3/4 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_three_fourths( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span9">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'three-fourths', 'shortcode_three_fourths' );

/**
 * Creates column with size of 1/5 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_fifth( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span2">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'fifth',		'shortcode_fifth' );
add_shortcode( 'one-fifth',	'shortcode_fifth' );

/**
 * Creates column with size of 2/5 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_two_fifths( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span4">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'two-fifths', 'shortcode_two_fifths' );

/**
 * Creates column with size of 3/5 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_three_fifths( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span6">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'three-fifths', 'shortcode_three_fifths' );

/**
 * Creates column with size of 4/5 of total body width
 *
 * @since 1.0
 *
 * @deprecated since version 1.1
 */
function shortcode_four_fifths( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span8">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'four-fifths', 'shortcode_four_fifths' );

?>