<?php

return array(
   'url' => '',
   
   '_TENANT_' => 5, 
   'REC_PER_PAGE' => 15, 

   'maintenance' => array(
      'enabled' => false,
      'message' => 'Application is currently down for maintenance. Please try again later.'
   ),

   'application_name' => 'Forge Application',

   'encryption_key' => 'aAaAaAaAaAaAaAaAa',

   'autoload' => array(
      'paths' => array(
         Forge\Core::path('app').'libraries/',
         Forge\Core::path('app').'models/',
         Forge\Core::path('app').'controllers/',
         Forge\Core::path('app').'api/'
      ),

      'class' => array(

      ),
   ),

   'timezone' => 'Europe/London',

   'locale' => array(
      'driver' => 'database',
      'language' => 'en'
   ),

   'alias' => array(
      '\Forge\Log' => 'Log',
      '\Forge\Router' => 'Router',
      '\Forge\View' => 'View',
      '\Forge\Core' => 'Core',
      '\Forge\Url' => 'URL',
      '\Forge\Input' => 'Input',
      '\Forge\Server' => 'Server',
      '\Forge\Config' => 'Config',
      '\Forge\Event' => 'Event',
      '\Forge\Auth' => 'Auth',
      '\Forge\Database' => 'DB',
      '\Api\Model' => 'Model'
      //'\Blade' => 'Blade'
   ),

   'mail' => array(
   ),

	'soap' => array(
		'example' => array(
			'base' => 'http://hastings.com/',
	        'wsdl' => 'http://example.com/wsdl',
	        'username' => 'username',
	        'password' => 'password',
	        'options' => array(
	            'connection_timeout' => 6000
	        )
		),
   ),

   'holidays' => array(
       /* 2014 */ '2014-01-01','2014-04-18','2014-04-21','2014-05-05','2014-05-26','2014-08-25','2014-12-25','2014-12-26',
       /* 2015 */ '2015-01-01','2015-04-03','2015-04-06','2015-05-04','2015-05-25','2015-08-31','2015-12-25','2015-12-28'
   )

);
