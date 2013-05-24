<?php

require_once APP_TESTS_LIB . '/testcase.php';

/**
 * @group payments
 */
class APP_Payments_Processes_Test extends APP_UnitTestCase {

	function test_default_payments_support(){

		add_theme_support( 'app-payments', array() );
		add_theme_support( 'app-price-format', array() );
	
		$this->assertEquals( appthemes_payments_get_args(), array(
			'items' => array(),
			'items_post_types' => array( 'post' ),
			'options' => false,
			'currency_default' => 'USD',
			'currency_format' => 'symbol',
			'hide_decimals' => false
		) );
	}

	function test_modified_payments_support(){

		add_theme_support( 'app-payments', array(
			'items' => array(
				'test' => array(
					'hello'
				)
		) ) );
		
		$this->assertEquals( appthemes_payments_get_args(), array(
			'items' => array( 'test' => array( 'hello' ) ),
			'items_post_types' => array( 'post' ),
			'options' => false,
			'currency_default' => 'USD',
			'currency_format' => 'symbol',
			'hide_decimals' => false
		) );
	}

	function test_format_support(){

		add_theme_support( 'app-payments' , array() );
		add_theme_support( 'app-price-format', array() );
		
		$this->assertEquals( appthemes_price_format_get_args(), array(
			'currency_default' => 'USD',
			'currency_format' => 'symbol',
			'hide_decimals' => false
		) );
	}

	function test_modified_format_support(){

		add_theme_support( 'app-price-format', array(
			'currency_default' => 'GBP'
		) );
		
		$this->assertEquals( appthemes_price_format_get_args(), array(
			'currency_default' => 'GBP',
			'currency_format' => 'symbol',
			'hide_decimals' => false
		) );
	}

}
