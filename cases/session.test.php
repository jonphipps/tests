<?php

use Laravel\Session;

class DummyPayload {

	public function test() { return 'Foo'; }

}

class SessionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the testing environment.
	 */
	public function setUp()
	{
		Config::set('application.key', 'foo');
		Session::$instance = null;
	}

	/**
	 * Tear down the testing environment.
	 */
	public function tearDown()
	{
		Config::set('application.key', '');
		Session::$instance = null;
	}

	/**
	 * Test the __callStatic method.
	 *
	 * @group laravel
	 */
	public function testPayloadCanBeCalledStaticly()
	{
		Session::$instance = new DummyPayload;
		$this->assertEquals('Foo', Session::test());
	}

	/**
	 * Test the Session::started method.
	 *
	 * @group laravel
	 */
	public function testStartedMethodIndicatesIfSessionIsStarted()
	{
		$this->assertFalse(Session::started());
		Session::$instance = 'foo';
		$this->assertTrue(Session::started());
	}

	/**
	 * Get a mock driver instance.
	 *
	 * @return Driver
	 */
	protected function getMockDriver()
	{
		return $this->getMock('Laravel\\Session\\Drivers\\Driver');
	}

}