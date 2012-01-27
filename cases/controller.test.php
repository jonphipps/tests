<?php

class ControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the testing environment.
	 */
	public function setUp()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
	}

	/**
	 * Tear down the testing environment.
	 */
	public function tearDown()
	{
		unset($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Test the Controller::call method.
	 *
	 * @group laravel
	 */
	public function testBasicControllerActionCanBeCalled()
	{
		$this->assertEquals('action_index', Controller::call('auth@index')->content);
	}

}