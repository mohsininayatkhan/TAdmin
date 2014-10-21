<?php
extract($_REQUEST);
$time = microtime(true);
// $time = time();
$data = array();
// $current = 1;
if (isset($current)) {
	$data = array(
		array(date("H:i:s",($time * 2000)), (int)substr(str_shuffle("123456789"), 0, 2)),
		array(date("H:i:s",($time * 2000)), (int)substr(str_shuffle("123456789"), 0, 2))
	);
} else {
	$data = array(
		array("name" => "Current Inbound Calls"),
		array("name" => "Current Outbound Calls")
	);
	for ($i=-14; $i<0; $i += 1) {
		$data[0]["data"][] = array(
			// ($time + $i * 2000), (int)substr(str_shuffle("123456789"), 0, 2)
			date("H:i:s",($time + $i * 2000)), (int)substr(str_shuffle("123456789"), 0, 2)
		);
		$data[1]["data"][] = array(
			date("H:i:s",($time + $i * 2000)), (int)substr(str_shuffle("123456789"), 0, 2)
		);
	}
}
// echo "<pre>";
// print_r($data);
// echo "</pre>";
echo json_encode($data);
