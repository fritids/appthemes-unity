<?php

/**
 * Centers content on page
 *
 * @since 1.0
 */
function shortcode_center( $atts, $content = null ) {
	$content = do_shortcode( $content );

	return '<div class="aligncenter">' . $content . '</div>';
}
add_shortcode( 'center', 'shortcode_center' );

/**
 * Adds image frame
 *
 * @since 1.0
 */
function shortcode_frame( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'align'		=>	'none',
		'caption'	=>	'',
		'lightbox'	=>	false
	), $atts ) );
	
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<figure class="align' . $align;
	if( $lightbox ) $out .= ' lightbox';
	$out .= '">';
	$out .= $content;
	if( $caption ) {
		$out .= '<figcaption>' . $caption . '</figcaption>';
	}
	$out .= '</figure>';
	
	return $out;
}
add_shortcode( 'frame', 'shortcode_frame' );

/**
 * Prints a divider <hr> element
 *
 * @since 1.0
 */
function shortcode_divider() {
	return '<hr />';
}
add_shortcode( 'divider', 'shortcode_divider' );
add_shortcode( 'hr', 'shortcode_divider' );

/**
 * Warps content into column container
 *
 * @since 1.0
 */
function shortcode_columns( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="row">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'columns', 'shortcode_columns' );
add_shortcode( 'cols',	  'shortcode_columns' );

/**
 * Creates column with size of 12/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col12( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span12">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column12', 'shortcode_col12' );
add_shortcode( 'col12',	   'shortcode_col12' );

/**
 * Creates column with size of 11/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col11( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span11">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column11', 'shortcode_col11' );
add_shortcode( 'col11',	   'shortcode_col11' );

/**
 * Creates column with size of 10/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col10( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span10">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column10', 'shortcode_col10' );
add_shortcode( 'col10',	   'shortcode_col10' );

/**
 * Creates column with size of 9/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col9( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span9">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column9', 'shortcode_col9' );
add_shortcode( 'col9',	  'shortcode_col9' );

/**
 * Creates column with size of 8/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col8( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span8">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column8', 'shortcode_col8' );
add_shortcode( 'col8',	  'shortcode_col8' );

/**
 * Creates column with size of 7/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col7( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span7">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column7', 'shortcode_col7' );
add_shortcode( 'col7',	  'shortcode_col7' );

/**
 * Creates column with size of 6/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col6( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span6">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column6', 'shortcode_col6' );
add_shortcode( 'col6',	  'shortcode_col6' );

/**
 * Creates column with size of 5/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col5( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span5">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column5', 'shortcode_col5' );
add_shortcode( 'col5',	  'shortcode_col5' );

/**
 * Creates column with size of 4/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col4( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span4">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column4', 'shortcode_col4' );
add_shortcode( 'col4',	  'shortcode_col4' );

/**
 * Creates column with size of 3/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col3( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span3">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column3', 'shortcode_col3' );
add_shortcode( 'col3',	  'shortcode_col3' );

/**
 * Creates column with size of 2/12 of total body width
 *
 * @since 1.1
 */
function shortcode_col2( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span2">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column2', 'shortcode_col2' );
add_shortcode( 'col2',	  'shortcode_col2' );

/**
 * Creates column with size of 1/11 of total body width
 *
 * @since 1.1
 */
function shortcode_col1( $atts, $content ) {
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="span1">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'column1', 'shortcode_col1' );
add_shortcode( 'col1',	  'shortcode_col1' );

/**
 * Displays Quick Links panel
 *
 * @since 1.0
 *
 * @uses royal_get_quick_links() To get HTML for quick links
 */
function shortcode_quick_links() {
	$args = array(
		'before'		=>	'<hr /><div class="quick-links-wrap"><div id="quick-links">',
		'after'			=>	'</div><div class="clear"></div></div>',
		'class'			=>	array( 'clearfix' ),
		'link_class'	=>	array( 'tooltip' )
	);
	return royal_get_quick_links( $args );
}
add_shortcode( 'quick-links', 'shortcode_quick_links' );

/**
 * Prints pricing table
 *
 * @since 1.0
 *
 * @uses royal_get_pricing_table() To get HTML for pricing table
 */
function shortcode_pricing_table( $atts ) {
	extract( shortcode_atts( array(
		'show'		=>	6,
		'highlight'	=>	2,
		'category'	=>	''
	), $atts ) );
	
	return royal_get_pricing_table( $show, $highlight, $category );
}
add_shortcode( 'pricing-table', 'shortcode_pricing_table' );

/**
 * Prints info, warning or resource box
 *
 * @since 1.0
 */
function shortcode_box( $atts, $content ) {
	extract( shortcode_atts( array(
		'type'		=>	'info'
	), $atts ) );
	
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<div class="info-box box-' . $type . '">';
	$out .= $content;
	$out .= '</div>';
	
	return $out;
}
add_shortcode( 'box', 'shortcode_box' );

/**
 * Prints a button
 *
 * @since 1.0.2
 */
function shortcode_button( $atts, $content ) {
	extract( shortcode_atts( array(
		'url'		=>	'',
		'icon'		=>	'',
		'id'		=>	'',
		'class'		=>	''
	), $atts ) );
	
	$content = do_shortcode( $content );
	
	$out = '';
	
	$out .= '<a class="button';
	if( !empty( $icon ) ) {
		$out .= ' icon button-' . $icon;
	}
	if( !empty( $class ) ) {
		$out .= ' ' . $class;
	}
	$out .= '"';
	if( !empty( $id ) ) {
		$out .= ' id="' . $id . '"';
	}
	if( !empty( $url ) ) {
		$out .= ' href="' . $url . '"';
	}
	$out .= '>' . $content . '</a>';
	
	return $out;
}
add_shortcode( 'button', 'shortcode_button' );

?>