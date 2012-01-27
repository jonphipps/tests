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
		$this->assertEquals('Admin_Panel_Index', Controller::call('admin.panel@index')->content);
		$this->assertEquals('Taylor', Controller::call('auth@profile', array('Taylor'))->content);
		$this->assertEquals('Dashboard_Panel_Index', Controller::call('dashboard::panel@index')->content);
	}

}