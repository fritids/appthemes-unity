<?php
/**
 *
 * Holding Deprecated functions oldest at the bottom (delete and clean as needed)
 * @package Clipper
 * @author AppThemes
 *
 */


/**
 * Feed url related to currently browsed page.
 *
 * @deprecated 1.4
 * @deprecated Use appthemes_get_feed_url()
 * @see appthemes_get_feed_url()
 */
if ( ! function_exists( 'clpr_get_feed_url' ) ) {
	function clpr_get_feed_url() {
		_deprecated_function( __FUNCTION__, '1.4', 'appthemes_get_feed_url()' );

		return appthemes_get_feed_url();
	}
}


/**
 * Return store image url with specified size.
 *
 * @deprecated 1.4
 * @deprecated Use clpr_get_store_image_url()
 * @see clpr_get_store_image_url()
 */
if ( ! function_exists( 'clpr_store_image' ) ) {
	function clpr_store_image( $post_id, $tax_name, $tax_arg, $width, $store_url ) {
		_deprecated_function( __FUNCTION__, '1.4', 'clpr_get_store_image_url()' );

		if ( ! $post_id && is_tax( APP_TAX_STORE ) ) {
			$term = get_queried_object();
			return clpr_get_store_image_url( $term->term_id, 'term_id', $width );
		} else {
			return clpr_get_store_image_url( $post_id, 'post_id', $width );
		}

	}
}


/**
 * Return coupon outgoing url.
 *
 * @deprecated 1.4
 * @deprecated Use clpr_get_coupon_out_url()
 * @see clpr_get_coupon_out_url()
 */
if ( ! function_exists( 'get_clpr_coupon_url' ) ) {
	function get_clpr_coupon_url( $post ) {
		_deprecated_function( __FUNCTION__, '1.4', 'clpr_get_coupon_out_url()' );

		return clpr_get_coupon_out_url( $post );
	}
}


?>