<?php

/**
 * Prorates a given length and price to a new length
 * @param  float $original_price  The original price
 * @param  int $original_length   The original length
 * @param  int $new_length        The new length
 * @return int                    The price prorated for the new length
 */
function appthemes_prorate( $original_price, $original_length, $new_length ){

	if( ! is_numeric( $original_price ) )
		trigger_error( 'Original Price must be numeric', E_USER_WARNING );

	if( ! is_numeric( $original_length ) || ! is_numeric( $new_length ) )
		trigger_error( 'Original Length and New Length must be numeric', E_USER_WARNING );

	$original_length = absint( $original_length );
	$new_length = absint( $new_length );

	$price_per_day = $original_price / $original_length;
	$new_price = $price_per_day * $new_length;
	
	return number_format( $new_price, 2 );

}
