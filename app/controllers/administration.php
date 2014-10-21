<?php

class Administration extends \Forge\Controller {
	
	protected static $module = 'administration';
	
	public static function index() {
		return Blade::make(self::$module.'._menu');
	}
}