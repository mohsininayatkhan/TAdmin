<?php

class Contacts extends \Forge\Controller {
	
	public static function index() {
		if (!AUTH::check()) {
			return \Forge\Response::redirect('/');
		}
		return Blade::make('contacts');
	}
	
}