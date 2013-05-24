<?php
require_once APP_TESTS_LIB . '/testcase.php';

/**
 * @group payments
 */
class APP_OrderTest extends APP_UnitTestCase {

	protected $order;

	public static function setUpBeforeClass(){

		appthemes_setup_orders();

	}

	public function setUp(){
		parent::setUp();
		$this->order = APP_Order::create();

	}

	public function test_retrieve_error(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		APP_Order::retrieve( 'not an id' );

	}
	
	public function test_create_order_defaults(){

		$this->assertEquals( 'Transaction', $this->order->get_description() );
		$this->assertEquals( APPTHEMES_ORDER_PENDING, $this->order->get_status() );
		$this->assertEquals( 0, $this->order->get_total() );
		$this->assertEmpty( $this->order->get_items() );
		$this->assertEmpty( $this->order->get_gateway() );

		return $this->order;

	}

	public function test_retrieve_order( ){

		$this->order_id = $this->order->get_id();

		$new_order = APP_Order::retrieve( $this->order_id );
		$this->assertEquals( $this->order, $new_order );

	}


	public function test_create_order_currencies(){

		// Create Mock Options Array
		add_theme_support( 'app-price-format', array(
			'currency_default' => 'GBP'
		) );

		$this->order = APP_Order::create();

		// Default currency should be the code in the options
		$this->assertEquals( 'GBP', $this->order->get_currency() );

	}

	function test_change_description(){

		// Default description should be 'Transaction' (unless theme is localized)
		$this->assertEquals( 'Transaction', $this->order->get_description() );

		// Setting a new description should cause it to be returned
		$this->order->set_description( 'Test Description' );
		$this->assertEquals( 'Test Description', $this->order->get_description() );

	}

	function test_change_description_error(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_warning' );
		$this->order->set_description( array( 'not-a-string' ) );

	}

	function test_change_currency(){

		// Setting a new currency should cause it to be returned
		$this->order->set_currency( 'EUR' );
		$this->assertEquals( 'EUR', $this->order->get_currency() );

		// Fail on Bad Currency Code
		$return_value = $this->order->set_currency( '543' );
		$this->assertFalse( $return_value );

		// Failed calls should retain old value		
		$this->assertEquals( 'EUR', $this->order->get_currency() );

	}

	function test_change_currency_error(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		$this->order->set_currency( array( 'not-a-string' ) );
	}

	function test_change_gateway(){

		// Default gateway should be empty
		$this->assertEmpty( $this->order->get_gateway() );

		// Setting a new gateway should cause it to be returned
		$this->order->set_gateway( 'paypal' );
		$this->assertEquals( 'paypal', $this->order->get_gateway() );

		// Fail on Bad Gateway ID
		$return_value = $this->order->set_gateway( 'non-existant-gateway' );
		$this->assertFalse( $return_value );

		// Failed calls should retain old value
		$this->assertEquals( 'paypal', $this->order->get_gateway() );

		// Clearing a gateway should set it to blank
		$this->order->clear_gateway();
		$this->assertEmpty( $this->order->get_gateway() );

		return true;
	}

	function test_change_gateway_error(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		$this->order->set_gateway( array( 'not-a-string' ) );

	}

	function test_add_item_basic(){

		$status = $this->order->add_item( 'payment-test', 5, $this->order->get_id() );
		$this->assertTrue( $status ); // FAILED

		// Adding an item should only increase the item count by 1
		$this->assertCount( 1, $this->order->get_items() );// FAILED

		// Adding an item should cause it to be returned
		$this->assertCount( 1, $this->order->get_items( 'payment-test') );			
		$item = $this->order->get_item();
		$this->assertNotEmpty( $item );

		// The correct values should be contained in the item array
		$this->assertArrayHasKey( 'price', $item );
		$this->assertEquals( 5, $item['price'] );

		$this->assertArrayHasKey( 'post_id', $item );
		$this->assertEquals( $this->order->get_id(), $item['post_id'] );

		$this->order->add_item( 'payment-test', 5, $this->order->get_id() );
		$this->order->add_item( 'payment-test', 5, $this->order->get_id() );
		$this->order->add_item( 'payment-test1', 5, $this->order->get_id() );

		// Adding additional items should increase the count
		$this->assertCount( 4, $this->order->get_items() );

		// APP_Order::get_items should filter items properly
		$this->assertCount( 3, $this->order->get_items( 'payment-test') );

		// Getting a non-existant item should return false
		$this->assertFalse( $this->order->get_item( 10 ) );

		// Using a numeric item type should not error
		$this->order->add_item( 123, 123, 123 );

	}

	function test_add_item_bad_type(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		$this->order->add_item( array( 'not-a-string' ), 100, 100 );

	}

	function test_add_item_bad_price(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		$this->order->add_item( 'test', 'not-a-number', 100 );

	}

	function test_add_item_bad_post(){
	
		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		$this->order->add_item( 'test', 100, 'not-a-number' );

	}

	function test_get_item_bad_type(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		$this->order->get_items( new stdClass() );

	}

	function test_get_total_basic(){

		$this->order = APP_Order::create();

		// By default, order's totals should be 0
		$this->assertEquals( 0, $this->order->get_total() );

		// Total should reflect added items prices
		$this->order->add_item( 'payment-test', 5, $this->order->get_id() );
		$this->assertEquals( 5, $this->order->get_total() ); // FAILED

		// Adding another item should increase the price
		$this->order->add_item( 'payment-test', 5, $this->order->get_id() );
		$this->assertEquals( 10, $this->order->get_total() );

		// Adding another item type should increase the price
		$this->order->add_item( 'payment-test1', 5, $this->order->get_id() );
		$this->assertEquals( 15, $this->order->get_total() );

		// Adding a float value as a price should convert to int
		$this->order->add_item( 'payment-test1', 5.99, $this->order->get_id() );
		$this->assertEquals( 20, $this->order->get_total() );

	}

	function test_get_status(){

		$this->assertEquals( APPTHEMES_ORDER_PENDING, $this->order->get_status() );
		$this->assertEquals( 'Pending', $this->order->get_display_status() );

	}

	// Disable until further expirmentation with events is figured out
	function disabled_complete_order(){
		
		$pending = new APP_Callback_Catcher( 'appthemes_transaction_pending' );
		$completed = new APP_Callback_Catcher( 'appthemes_transaction_completed' );
		$activated = new APP_Callback_Catcher( 'appthemes_transaction_activated' );
		$this->order->complete();

		$this->assertTrue( $completed->was_called() );

	}

	function disabled_activate_order(){
		
		$pending = new APP_Callback_Catcher( 'appthemes_transaction_pending' );
		$completed = new APP_Callback_Catcher( 'appthemes_transaction_completed' );
		$activated = new APP_Callback_Catcher( 'appthemes_transaction_activated' );
		$this->order->activate();

		$this->assertTrue( $activated->was_called() );

	}

}

add_filter( 'appthemes_order_item_posts_types', 'appthemes_payments_tests_add_order_types', 99 );

function appthemes_payments_tests_add_order_types( $types ){
	$types[] = 'transaction';
	return $types;
}
