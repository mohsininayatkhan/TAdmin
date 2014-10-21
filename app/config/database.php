<?php

return array(
    'profile' => false,

    // switch qery logging on or off
    // should always be off for the live environment
    'querylogging' => false,

    'fetch' => PDO::FETCH_CLASS,

    'default' => 'default',
	
	'prefix' => 'tp',

    'error_message' => 'A fatal error has occured',

    'connections' => array(
        'default' => array(
            'driver'   => 'mysql',
            'host'     => '10.10.7.120',
            'database' => 'tpadpbx6',
            'username' => 'root',
            'password' => 'tpad123',
            'charset'  => 'utf8',
            'collation'=> 'utf8_bin',
            'prefix'   => '',
        ),
    ),
);
