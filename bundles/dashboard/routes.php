<?php

Route::get('dashboard', array('name' => 'dashboard', function()
{
	//
}));

Route::controller('dashboard::panel');