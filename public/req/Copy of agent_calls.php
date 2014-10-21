<?php
$cols = array(
	array(
		"id"      => "",
		"label"   => "Agent",
		"pattern" => "",
		"type"    => "string"
	),
	array(
		"id"      => "",
		"label"   => "Calls Count",
		"pattern" => "",
		"type"    => "number"
	)
);

$fixed = '{
  "cols": [
        {"id":"","label":"Agent","pattern":"","type":"string"},
        {"id":"","label":"Calls Count","pattern":"","type":"number"}
      ],
  "rows": [
        {"c":[{"v":"Mushrooms","f":null},{"v":3,"f":null}]},
        {"c":[{"v":"Onions","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Olives","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Zucchini","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Pepperoni","f":null},{"v":2,"f":null}]}
      ]
}';
// echo $fixed;
$name = ["Effy", "Alexandra", "Christine", "Ethan", "George", "Justin"];
$data = array();
// $inboundCallsCount = array();
// $outboundCallsCount = array();
// $internalCallsCount = array();
for ($i=0; $i<6; $i++) {
	$data["inboundCallsCount"][] = array($name[$i], substr(str_shuffle("123456789"), 0, 2));
	$data["outboundCallsCount"][] = array($name[$i], substr(str_shuffle("123456789"), 0, 2));
	$data["internalCallsCount"][] = array($name[$i], substr(str_shuffle("123456789"), 0, 2));
	// $inboundCallsCount[] = array(
		// "c" => array(
			// array(
				// "v" => $name[$i],
				// "f" => null
			// ),
			// array(
				// "v" => (int)substr(str_shuffle("123456789"), 0, 2),
				// "f" => null
			// )
		// )
	// );
	// $outboundCallsCount[] = array(
		// "c" => array(
			// array(
				// "v" => $name[$i],
				// "f" => null
			// ),
			// array(
				// "v" => (int)substr(str_shuffle("123456789"), 0, 2),
				// "f" => null
			// )
		// )
	// );
	// $internalCallsCount[] = array(
		// "c" => array(
			// array(
				// "v" => $name[$i],
				// "f" => null
			// ),
			// array(
				// "v" => (int)substr(str_shuffle("123456789"), 0, 2),
				// "f" => null
			// )
		// )
	// );
}

// $data = array(
	// "inboundCallsCount"  => array(
		// "cols" => $cols, "rows" => $inboundCallsCount
	// ),
	// "outboundCallsCount" => array(
		// "cols" => $cols, "rows" => $outboundCallsCount
	// ),
	// "internalCallsCount" => array(
		// "cols" => $cols, "rows" => $internalCallsCount
	// )
// );

echo json_encode($data);














?>