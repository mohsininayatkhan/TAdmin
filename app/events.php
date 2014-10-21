<?php

/**
 * load some other trigger events
 * 
 * trigger spy is very important
 * without this the ad models will fail
 */

Forge\Event::listen('error.404', function($event){
   return \Forge\Response::error(404, array('error' => $event));
});

Forge\Event::listen('error.500', function($event){
   return \Forge\Response::error(500, array('error' => $event));
});

Forge\Event::listen('maintenance.enabled', function($event){
   return \Forge\Response::error(500, array('error' => Forge\Config::get('app.maintenance.message')));
});

/**
 * SoapHandler error logger
 */
Forge\Event::listen('error.fire', function($locator, $pointer, $message, $type, $trace = null){
   Forge\Log::write(\Forge\Log::ERROR, $message .' - '. $locator .' @ '. $pointer .'::'. $type .(is_null($trace) ? '' : '->'. $trace));
});