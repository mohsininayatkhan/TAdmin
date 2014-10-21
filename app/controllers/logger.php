<?php

class Logger extends \Forge\Controller {
	
	public static function displayLogs($method = null) {
	    $method = str_replace("/", "", $method);
		if(is_callable('self::'.$method)) {
		    return call_user_func('self::'.$method);
		} else {
			$data = array('message' => 'Unknown logging method called');
			return Blade::make('error.errormessage', $data);
		}
	}
	
	private static function simple() {
		$data = self::getData();
		return Blade::make('logging.simple', $data);
	}
	
	private static function verbose() {
		$data = self::getData();
		return Blade::make('logging.verbose', $data);
	}
	
	private static function getData() {
    	$data = array(
    	    'today' => self::getToday()
    	);
    	if(\Input::post('date')) {
            $rules = array(
                'date' => 'date'
            );
            $date = \Input::post('date');
            self::validate(array('date'=>$date), $rules);
            $data['log'] = self::getLog($date);
    	}
    	return $data;
	}
	
	private static function getLog($date) {
    	$log = preg_replace("/[^0-9]/", "", $date);
    	return \Forge\Log::open($log.'.log');
	}
	
	private static function getToday() {
    	$date = new \DateTime();
    	return $date->format('Y-m-d');
	}
	
}