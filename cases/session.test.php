<?php

use Laravel\Session;
use Laravel\Session\Payload;

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
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodCreatesNewSessionWithNullIDGiven()
	{
		$payload = $this->getPayload();
		$payload->load(null);
		$this->verifyNewSession($payload);
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodCreatesNewSessionWhenSessionIsExpired()
	{
		$payload = $this->getPayload();

		$session = $this->getSession();
		$session['last_activity'] = time() - 10000;

		$payload->driver->expects($this->any())
						->method('load')
						->will($this->returnValue($session));

		$payload->load('foo');

		$this->verifyNewSession($payload);
		$this->assertTrue($payload->session['id'] !== $session['id']);
	}

	/**
	 * Assert that a session is new.
	 *
	 * @param  Payload  $payload
	 * @return void
	 */
	protected function verifyNewSession($payload)
	{
		$this->assertFalse($payload->exists);
		$this->assertTrue(isset($payload->session['id']));
		$this->assertEquals(array(), $payload->session['data'][':new:']);
		$this->assertEquals(array(), $payload->session['data'][':old:']);
		$this->assertTrue(isset($payload->session['data'][Session::csrf_token]));
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodSetsValidSession()
	{
		$payload = $this->getPayload();

		$session = $this->getSession();

		$payload->driver->expects($this->any())
						->method('load')
						->will($this->returnValue($session));

		$payload->load('foo');

		$this->assertEquals($session, $payload->session);
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodSetsCSRFTokenIfDoesntExist()
	{
		$payload = $this->getPayload();

		$session = $this->getSession();

		unset($session['data']['csrf_token']);

		$payload->driver->expects($this->any())
						->method('load')
						->will($this->returnValue($session));

		$payload->load('foo');

		$this->assertEquals('foo', $payload->session['id']);
		$this->assertTrue(isset($payload->session['data']['csrf_token']));
	}

	/**
	 * Test the various data retrieval methods.
	 *
	 * @group laravel
	 */
	public function testSessionDataCanBeRetrievedProperly()
	{
		$payload = $this->getPayload();

		$payload->session = $this->getSession();

		$this->assertTrue($payload->has('name'));
		$this->assertEquals('Taylor', $payload->get('name'));
		$this->assertFalse($payload->has('foo'));
		$this->assertEquals('Default', $payload->get('foo', 'Default'));
		$this->assertTrue($payload->has('votes'));
		$this->assertEquals(10, $payload->get('votes'));
		$this->assertTrue($payload->has('state'));
		$this->assertEquals('AR', $payload->get('state'));
	}

	/**
	 * Test the various data manipulation methods.
	 *
	 * @group laravel
	 */
	public function testDataCanBeSetProperly()
	{
		$payload = $this->getPayload();

		$payload->session = $this->getSession();

		// Test the "put" and "flash" methods.
		$payload->put('name', 'Weldon');
		$this->assertEquals('Weldon', $payload->session['data']['name']);
		$payload->flash('language', 'php');
		$this->assertEquals('php', $payload->session['data'][':new:']['language']);

		// Test the "reflash" method.
		$payload->session['data'][':new:'] = array('name' => 'Taylor');
		$payload->session['data'][':old:'] = array('age' => 25);
		$payload->reflash();
		$this->assertEquals(array('name' => 'Taylor', 'age' => 25), $payload->session['data'][':new:']);

		// Test the "keep" method.
		$payload->session['data'][':new:'] = array();
		$payload->keep(array('age'));
		$this->assertEquals(25, $payload->session['data'][':new:']['age']);
	}

	/**
	 * Get a session payload instance.
	 *
	 * @return Payload
	 */
	protected function getPayload()
	{
		return new Payload($this->getMockDriver());
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

	/**
	 * Get a dummy session.
	 *
	 * @return array
	 */
	protected function getSession()
	{
		return array(
			'id'            => 'foo',
			'last_activity' => time(),
			'data'          => array(
				'name'       => 'Taylor',
				'age'        => 25,
				'csrf_token' => 'bar',
				':new:'      => array(
						'votes' => 10,
				),
				':old:'      => array(
						'state' => 'AR',
				),
		));
	}

}