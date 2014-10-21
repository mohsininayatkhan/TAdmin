<?php

class Home extends \Forge\Controller {
	
	// protected static $userData;
	
	public static function index() {
		return Blade::make('home');
	}
}