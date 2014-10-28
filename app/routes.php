<?php
require_once('extraroutes.php');

Router::register(array('/', '*', '*/*'), function($method){
   return \Main::show($method);
});

Router::register('logger/*', function($method){
	return \Logger::displayLogs($method[0]);
});

Router::register('dbtest', function(){
    return \Dbtester::testDb();
});

Router::map(Core::path('app') .'controllers');
