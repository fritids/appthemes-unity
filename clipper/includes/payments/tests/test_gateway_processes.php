<?php

require_once APP_TESTS_LIB . '/testcase.php';

/**
 * @group payments
 */
class APP_Gateway_Processes_Test extends APP_UnitTestCase {

	public function test_gateway_creation(){

		appthemes_register_gateway( 'APP_Mock_Gateway' );
		$gateway = APP_Gateway_Registry::get_gateway( 'mock-gateway' );
		$this->assertNotEmpty( $gateway );

		// New gateways should not be enabled
		$this->assertFalse( APP_Gateway_Registry::is_gateway_enabled( 'mock-gateway' ) );

		return $gateway;
	}

	/**
	 * @depends test_gateway_creation
	 */
	public function test_anonymous_process_fail( $gateway ){

		$order = appthemes_new_order();
		$order->add_item( 'mock-process-test-item', 5, $order->get_id() );
		
		// Unenabled gateways should fail
		$status = appthemes_process_gateway( 'mock-gateway', $order );
		$this->assertFalse( $status );

		// Gateway should not have been called
		$this->assertFalse( $gateway->process_called() );

	}

	/**
	 * @depends test_gateway_creation
	 * @depends test_anonymous_process_fail
	 */
	public function test_anonymous_process_success( $gateway ){

		$order = appthemes_new_order();
		$order->add_item( 'mock-process-test-item', 5, $order->get_id() );

		$old_options = APP_Gateway_Registry::get_options();

		// Create new options object with gateway enabled
		$options = new stdClass;
		$options->gateways = array(
			'enabled' => array(
				'mock-gateway' => true
			)
		);
		APP_Gateway_Registry::register_options( $options );

		// Gateway should be enabled
		$this->assertTrue( APP_Gateway_Registry::is_gateway_enabled( 'mock-gateway' ) );

		// After enabled, it should pass
		$status = appthemes_process_gateway( 'mock-gateway', $order );
		$this->assertTrue( $status );

		// Processing should call the gateway
		$this->assertTrue( $gateway->process_called() );

		APP_Gateway_Registry::register_options( $old_options );
	}



}