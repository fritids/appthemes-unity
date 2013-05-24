<?php

require_once APP_TESTS_LIB . '/testcase.php';

/**
 * @group payments
 */
class APP_Gateway_RegistryTest extends APP_UnitTestCase {

	public function test_register(){

		APP_Gateway_Registry::register_gateway( 'APP_Mock_Gateway' );
		$this->assertTrue( APP_Gateway_Registry::is_gateway_registered( 'mock-gateway' ) );

		$gateway = APP_Gateway_Registry::get_gateway( 'mock-gateway' );
		$this->assertEquals( 'mock-gateway', $gateway->identifier() );

		$this->assertFalse( APP_Gateway_Registry::is_gateway_enabled( 'mock-gateway') );

	}

	public function test_register_error_bad_class(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		APP_Gateway_Registry::register_gateway( 'Non_Existant_Class' );

	}

	public function test_register_error_bad_value(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		APP_Gateway_Registry::register_gateway( array( 'not-a-string' ) );

	}

	public function test_get_gateway_bad_value(){

		$this->setExpectedException( 'PHPUnit_Framework_Error_Warning' );
		APP_Gateway_Registry::get_gateway( array( 'not-a-string' ) );

	}

	public function test_get_gateways(){

		$this->assertInternalType( 'array', APP_Gateway_Registry::get_gateways() );

	}

}
