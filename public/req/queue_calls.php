<?php
$data = array();

for ($i = 1; $i <= 30; $i++) {
	$data["calls"][] = array(ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)), (int)substr(str_shuffle("123456789"), 0, 2));
	$data["service"][] = array(ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)), (int)substr(str_shuffle("123456789"), 0, 2));
}

echo json_encode($data);