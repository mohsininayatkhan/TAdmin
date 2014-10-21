<?php

class ForgeTestController extends \Forge\Controller {
	
	public static function sayHello() {

		$data = array(
			'content' => array(
				array(
					'content' => 'This page uses the blade templating engine.'
				),
				array(
					'content' => 'This content is being dynamically loaded by the templating engine.'
				)
			),
		);
		
		return Blade::make('forgebladetest', $data);
	}
	
}