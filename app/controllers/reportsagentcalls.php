<?php

class ReportsAgentCalls extends \Forge\Controller {
	
	public static function data() {
		$data = array();

		for ($i = 1; $i <= 20; $i++) {
			$data["inbound_calls_count"][] = array(ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)), (int)substr(str_shuffle("123456789"), 0, 2));
			$data["outbound_calls_count"][] = array(ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)), (int)substr(str_shuffle("123456789"), 0, 2));
			$data["internal_calls_count"][] = array(ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)), (int)substr(str_shuffle("123456789"), 0, 2));
		}

		echo json_encode($data);
	}
	
}