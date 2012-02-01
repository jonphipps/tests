<?php

use Laravel\Cookie;

class CookieTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Cookie::$jar = array();
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		Cookie::$jar = array();
	}

	/**
	 * Test Cookie::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodIndicatesIfCookieInSet()
	{
		Cookie::$jar['foo'] = array('value' => 'bar');
		$this->assertTrue(Cookie::has('foo'));
		$this->assertFalse(Cookie::has('bar'));

		Cookie::put('baz', 'foo');
		$this->assertTrue(Cookie::has('baz'));
	}

	/**
	 * Test the Cookie::get method.
	 *
	 * @group laravel
	 */
	public function testGetMethodCanReturnValueOfCookies()
	{
		Cookie::$jar['foo'] = array('value' => 'bar');
		$this->assertEquals('bar', Cookie::get('foo'));

		Cookie::put('bar', 'baz');
		$this->assertEquals('baz', Cookie::get('bar'));
	}

	/**
	 * Test the Cookie::get method respects signatures.
	 *
	 * @group laravel
	 */
	public function testTamperedCookiesAreReturnedAsNull()
	{
		$_COOKIE['foo'] = Cookie::sign('foo', 'bar');
		$this->assertEquals('bar', Cookie::get('foo'));

		$_COOKIE['foo'] .= '-baz';
		$this->assertNull(Cookie::get('foo'));

		$_COOKIE['foo'] = Cookie::sign('foo', 'bar');
		$_COOKIE['foo'] = 'aslk'.$_COOKIE['foo'];
		$this->assertNull(Cookie::get('foo'));
	}

	/**
	 * Test Cookie::forever method.
	 *
	 * @group laravel
	 */
	public function testForeverShouldUseATonOfMinutes()
	{
		Cookie::forever('foo', 'bar');
		$this->assertEquals('bar', Cookie::$jar['foo']['value']);
		$this->assertEquals(525600, Cookie::$jar['foo']['minutes']);

		Cookie::forever('bar', 'baz', 'path', 'domain', true);
		$this->assertEquals('path', Cookie::$jar['bar']['path']);
		$this->assertEquals('domain', Cookie::$jar['bar']['domain']);
		$this->assertTrue(Cookie::$jar['bar']['secure']);
	}

	/**
	 * Test the Cookie::forget method.
	 *
	 * @group laravel
	 */
	public function testForgetSetsCookieWithExpiration()
	{
		Cookie::forget('bar', 'path', 'domain', true);
		$this->assertEquals(-2000, Cookie::$jar['bar']['minutes']);
		$this->assertEquals('path', Cookie::$jar['bar']['path']);
		$this->assertEquals('domain', Cookie::$jar['bar']['domain']);
		$this->assertTrue(Cookie::$jar['bar']['secure']);
	}

}