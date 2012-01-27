<?php

class Auth_Controller extends Controller {

	public function __construct()
	{
		$this->filter('before', 'test-all-before');
	}

	public function action_index()
	{
		return __FUNCTION__;
	}

	public function action_login()
	{
		return __FUNCTION__;
	}

}