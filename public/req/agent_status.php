<?php
$name = ["Alexandra", "Christine", "Ethan", "George", "Justin", "Hannah", "Seth", "Sydney", "Kyle", "Chris", "Trish", "Panda", "Effy", "Nicky", "Nohl", "Gabby", "Anthony", "Taylor"];
$status = ["On email", "On training", "Auto ACW", "ACW", "Available", "Admin", "Break", "Lunch", "On login"];
$data = array();

for ($i = 0; $i < count($name); $i++) {
	for ($ii = 0; $ii < count($status); $ii++) {
		$data[str_replace(" ", "_", $name[$i])][] = array($status[$ii], (int)substr(str_shuffle("123456789"), 0, 2));
	}
}

echo json_encode($data);