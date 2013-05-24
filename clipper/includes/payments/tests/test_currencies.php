<?php

require_once APP_TESTS_LIB . '/testcase.php';

/**
 * @group payments
 */
class APP_CurrenciesTest extends APP_UnitTestCase {

	public function test_init(){

		APP_Currencies::init();

		// Check that default currencies are loaded
		$this->assertNotEmpty( APP_Currencies::get_currency( 'USD' ) );

		$this->assertEquals( 'US Dollars (&#36;)', APP_Currencies::get_currency_string( 'USD' ) );

		$string_array = APP_Currencies::get_currency_string_array();
		$this->assertNotEmpty( $string_array );
		$this->assertArrayHasKey( 'USD', $string_array );
	}

	public function test_defaults() {
		$code = strtoupper( __FUNCTION__ );

		APP_Currencies::add_currency( $code, array() );

		$this->assertEquals( $code, APP_Currencies::get_name( $code ), 'Currency Name' );

		$this->assertEquals( $code, APP_Currencies::get_symbol( $code ), 'Curreny Symbol' );

		$this->assertEquals( '{symbol}{price}', APP_Currencies::get_display( $code ), 'Currency Display' );

		$currency_array = APP_Currencies::get_currency( $code );
		$this->assertNotEmpty( $currency_array );

		$expected = array(
			'code' => $code,
			'name' => $code,
			'symbol' => $code,
			'display' => '{symbol}{price}'
		);

		$this->assertEquals( $expected, $currency_array, 'Currency Array' );
	}

	public function test_args() {
		$code = strtoupper( __FUNCTION__ );

		APP_Currencies::add_currency( $code, array(
			'name' => 'Test Name 123',
			'symbol' => '###',
			'display' => '{price}{symbol}'
		) );

		$this->assertEquals( 'Test Name 123', APP_Currencies::get_name( $code ), 'Name' );

		$this->assertEquals( '###', APP_Currencies::get_symbol( $code ), 'Symbol' );

		$this->assertEquals( '{price}{symbol}', APP_Currencies::get_display( $code ), 'Display' );

		$currency_array = APP_Currencies::get_currency( $code );
		$this->assertNotEmpty( $currency_array );

		$expected = array(
			'code' => $code,
			'name' => 'Test Name 123',
			'symbol' => '###',
			'display' => '{price}{symbol}'
		);

		$this->assertEquals( $expected, $currency_array, 'Currency Array' );
	}

	public function test_get_bad_currency(){

		$this->assertFalse( APP_Currencies::get_currency( 'not-a-real-currency' ) );

	}

	public function test_price(){

		$price = APP_Currencies::get_price( 5, 'USD' );
		$this->assertEquals( '&#36;5', $price );

	}
}

/**
 * @group payments
 */
class APP_Current_Currencies_Test extends APP_UnitTestCase {

	private static $old_support;

	public static function setUpBeforeClass(){
		self::$old_support = get_theme_support( 'app-price-format' );
		remove_theme_support( 'app-price-format' );
	}

	public static function tearDownAfterClass(){
		add_theme_support( 'app-price-format', self::$old_support );
	}

	public function setUp(){
		parent::setUp();
		add_theme_support( 'app-price-format', array() );
	}

	public function test_currency_defaults(){

		$currency_array = APP_Currencies::get_current_currency();
		$this->assertNotEmpty( $currency_array );

		$expected = array(
			'code' => 'USD',
			'name' => 'US Dollars',
			'symbol' => '&#36;',
			'display' => '{symbol}{price}'
		);

		$this->assertEquals( $expected, $currency_array, 'Currency Array' );

		$this->assertEquals( 'US Dollars', APP_Currencies::get_current_name( 'USD'  ), 'Currency Name' );
		$this->assertEquals( '&#36;', APP_Currencies::get_current_symbol( 'USD' ), 'Curreny Symbol' );
		$this->assertEquals( '{symbol}{price}', APP_Currencies::get_current_display( 'USD' ), 'Currency Display' );

	}

	
}

