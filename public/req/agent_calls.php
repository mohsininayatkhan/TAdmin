<?php
$name = array("Alexandra", "Christine", "Ethan", "George", "Justin", "Hannah", "Seth", "Sydney", "Kyle", "Chris", "Trish", "Panda", "Effy", "Nicky", "Nohl", "Gabby", "Anthony", "Taylor");
$data = array();

for ($i = 0; $i < count($name); $i++) {
	$data["inbound_calls_count"][] = array($name[$i], (int)substr(str_shuffle("123456789"), 0, 2));
	$data["outbound_calls_count"][] = array($name[$i], (int)substr(str_shuffle("123456789"), 0, 2));
	$data["internal_calls_count"][] = array($name[$i], (int)substr(str_shuffle("123456789"), 0, 2));
}

echo json_encode($data);