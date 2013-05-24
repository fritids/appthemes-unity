<?php

/**
 * Displays a formatted price. See appthemes_get_price
 * 
 * @param  int $price                The numerical value to format
 * @param  string $override_currency The currency the value is in (defaults to 'default_currency')
 * @return string                    The formatted price
 */
function appthemes_display_price( $price, $override_currency = '', $override_format = '' ){

	echo appthemes_get_price( $price, $override_currency, $override_format  );

}

/**
 * Returns the price given the arguments in add_theme_support for 'app-price-format'
 * Note: if hide_decimals is turned on, the amount will be rounded.
 * 
 * @param  int $price                The numerical value to format
 * @param  string $override_currency The currency the value is in (defaults to 'default_currency')
 * @return string                    The formatted price
 */
function appthemes_get_price( $price, $override_currency = '', $override_format = '' ){

	$args = appthemes_price_format_get_args();
	extract( $args, EXTR_SKIP );	

	$decimals = ( $hide_decimals ) ? 0 : 2;

	// Format Number
	$formatted_price = number_format_i18n( $price, $decimals );
	
	// Add Currency
	return APP_Currencies::get_price( $formatted_price, $override_currency, $override_format );
	
}

function appthemes_display_mixed_price( $mixed_prices ){

	$strings = array();
	foreach( $mixed_prices as $currency => $amount ){
		$strings[] = appthemes_get_price( $amount, $currency, 'code' );
	}

	echo join( '</br> ', $strings );

}