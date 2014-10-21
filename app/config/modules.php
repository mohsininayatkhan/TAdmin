<?php

return array(
    // Modules definitions list
    'example' => array(
        // Should this module be autostarted?
        'autostart' => false,
        
        // Enable routing to controllers
        'map' => false,
        
        'autoload' => array(
           'paths' => array(
           ),
           'class' => array(
           )
       ),
    ),
	'rate' => array(
        // Should this module be autostarted?
        'autostart' => false,

        // Enable default routing to controllers
        'map' => true,

        'autoload' => array(
           'paths' => array(
              Forge\Module::path('rate').'models/',
              Forge\Module::path('rate').'ad/',
           ),

           'class' => array()
       ),
    ),
);