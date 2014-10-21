<?php

class Login extends \Forge\Controller {
	
	public static function index() {
		// check if logged in
		// if (AUTH::check()) {
			// return \Forge\Response::redirect('/');
			
		// } else {
			$data = array(
				'username' => \Input::post('username'),
				'password' => \Input::post('password')
			);
			
			if ($data['username'] && $data['password']) {
				if (AUTH::attempt(array('username' => $data['username'], 'password' => $data['password']))) {
					return \Forge\Response::redirect('/');
				}
				
				$data['error'] = 1;
			}
			
			return Blade::make('login', $data);
		// }
	}
	
	private static function logout() {
		AUTH::logout();
		return \Forge\Response::redirect('/');
	}
	
}