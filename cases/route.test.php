<?php

use Laravel\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the testing environment.
	 */
	public static function tearDownAfterClass()
	{
		unset($_SERVER['REQUEST_METHOD']);
		unset(Filter::$filters['test-after']);
		unset(Filter::$filters['test-before']);
	}

	/**
	 * Destroy the testing environment.
	 */
	public function tearDown()
	{
		Request::$route = null;
	}

	/**
	 * Tests the Route::handles method.
	 *
	 * @group laravel
	 */
	public function testHandlesIndicatesIfTheRouteHandlesAGivenURI()
	{
		$route = new Route('GET /', array('handles' => array('GET /foo/bar')));

		$this->assertTrue($route->handles('foo/*'));
		$this->assertTrue($route->handles('foo/bar'));
		$this->assertFalse($route->handles('/'));
		$this->assertFalse($route->handles('baz'));
		$this->assertFalse($route->handles('/foo'));
		$this->assertFalse($route->handles('foo'));

		$route = new Route('GET /', array('handles' => array('GET /', 'GET /home')));

		$this->assertTrue($route->handles('/'));
		$this->assertTrue($route->handles('home'));
		$this->assertFalse($route->handles('foo'));
	}

	/**
	 * Tests the Route::is method.
	 *
	 * @group laravel
	 */
	public function testIsMethodIndicatesIfTheRouteHasAGivenName()
	{
		$route = new Route('GET /', array('name' => 'profile'));

		$this->assertTrue($route->is('profile'));
		$this->assertFalse($route->is('something'));
	}

	/**
	 * Test the basic execution of a route.
	 *
	 * @group laravel
	 */
	public function testBasicRoutesCanBeExecutedProperly()
	{
		$route = new Route('', array(function() { return 'Route!'; }));

		$this->assertEquals('Route!', $route->call()->content);
		$this->assertInstanceOf('Laravel\\Response', $route->call());
	}

	/**
	 * Test that route parameters are passed into the handlers.
	 *
	 * @group laravel
	 */
	public function testRouteParametersArePassedIntoTheHandler()
	{
		$route = new Route('', array(function($var) { return $var; }), array('Taylor'));

		$this->assertEquals('Taylor', $route->call()->content);
		$this->assertInstanceOf('Laravel\\Response', $route->call());
	}

	/**
	 * Test that calling a route calls the global before and after filters.
	 *
	 * @group laravel
	 */
	public function testCallingARouteCallsTheBeforeAndAfterFilters()
	{
		$route = new Route('', array(function() { return 'Hi!'; }));

		$_SERVER['before'] = false;
		$_SERVER['after'] = false;

		$route->call();

		$this->assertTrue($_SERVER['before']);
		$this->assertTrue($_SERVER['after']);
	}

	/**
	 * Test that before filters override the route response.
	 *
	 * @group laravel
	 */
	public function testBeforeFiltersOverrideTheRouteResponse()
	{
		Filter::register('test-before', function()
		{
			return 'Filtered!';
		});

		$route = new Route('', array('before' => 'test-before', function() {
			return 'Route!';
		}));

		$this->assertEquals('Filtered!', $route->call()->content);
	}

	/**
	 * Test that after filters do not affect the route response.
	 *
	 * @group laravel
	 */
	public function testAfterFilterDoesNotAffectTheResponse()
	{
		$_SERVER['test-after'] = false;

		Filter::register('test-after', function()
		{
			$_SERVER['test-after'] = true;
			return 'Filtered!';
		});

		$route = new Route('', array('after' => 'test-after', function()
		{
			return 'Route!';
		}));

		$this->assertEquals('Route!', $route->call()->content);
		$this->assertTrue($_SERVER['test-after']);
	}

	/**
	 * Test that the route calls the appropriate controller method when delegating.
	 *
	 * @group laravel
	 */
	public function testControllerActionCalledWhenDelegating()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$route = new Route('', array('uses' => 'auth@index'));

		$this->assertEquals('action_index', $route->call()->content);
	}

}