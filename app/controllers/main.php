<?php

class Main extends \Forge\Controller {
	
	// protected static $userData;
	
	public static function call($method = null) {
		if(is_callable('self::'.$method[0])) {
		
			if (preg_match('/^(index|)$/', $method[0])) {
				$method[0] = 'Home';
			}

			return call_user_func('\\'.$method[0].'::'.(isset($method[1]) ? str_replace("-", "_", $method[1]): 'index'));
			
		} else {
			$data = array('message' => 'Unknown logging method called');
			return Blade::make('error.errormessage', $data);
		}
	}
	
	public static function show($method = null) {
		
		// check if valid page and if logged in
		$method[0] = str_replace("-", "", $method[0]);
		if (preg_match('/^(|index|administration|pbx-management|reports)$/', $method[0]) && !AUTH::check()) {
			// return \Forge\Response::redirect('login');
		}
		
		return self::call($method);
	}
	
	// public static function index() {
		// if (!AUTH::check()) {
			// return \Login::index();
		// }
		
		// $agentsDbModel = new \DbAgentsModel();
        // $record = $agentsDbModel->getAll();

        // $data = array(
            // 'record' => $record,
			// "user" => json_decode(json_encode(AUTH::user()),true)
        // );
		
		// return Blade::make('home', $data);
	// }	
}