<?php

/**
 * initialize the validators
 */
require_once(\Forge\Core::path('app') . "validators.php");

//set error handlers
require_once Forge\Core::path('app') .'libraries/exceptions.php';


// Set up some soap functions
//Examples of how nest would use the soap engine, assuming the auth model uses nest loggon structures.

/*
Soap::register('xml', function($soap, $config, $data){
   $user = some_nest_username;
   $pass = some_nest_password;

   $xml = array_get($data, 0);

   if (is_array($xml))
   {
      $_xml = new SimpleXML('<root/>');
      $xml = $_xml->toXML($xml);
   }

   $result = $soap->xml_request($user, $pass, $xml);

   switch (array_get($data, 1))
   {
      case 'array':
         // Convert the output to an array
         $_xml = new SimpleXML($result);
         return $_xml->toArray();
         break;
      default:
         return $result;
   }
});
*/

/*
Soap::register('json', function($soap, $config, $data){
   $user = some_nest_username;
   $pass = some_nest_password;

   $json = array_get($data, 0);

   $result = $soap->json_request($user, $pass, (is_array($json) ? json_encode($json) : $json));

   switch (array_get($data, 1))
   {
      case 'array':
         // Convert the output to an array
         return json_decode($result, true);
         break;
      case 'object':
         // Convert the output to an object
         return json_decode($result);
         break;
      default:
         return $result;
   }
});
*/

//GUI Error handler over rides engine handler
$logging = Forge\Config::get('error.logging');
$vbose   = Forge\Config::get('error.vbose');

set_exception_handler(function($e) use ($logging, $vbose)
{
    switch (get_class($e)) {
        case 'ValidationException':

            if ($logging) Forge\Log::write(Forge\Log::DEBUG, $e->getMessage() .'->'.$e->getTraceAsString());
            return Forge\Response::json(array('status' => 'ERROR', 'message' => $e->getMessage()));
            break;

        case 'AccessException':
            if ($logging) Forge\Log::write(Forge\Log::DEBUG, $e->getMessage() .'->'.$e->getTraceAsString());
            return Forge\Response::json(array('status' => 'ERROR', 'message' => $e->getMessage()));
            break;

        case 'ADException':
            if ($logging) Forge\Log::write(Forge\Log::DEBUG, $e->getMessage() .'->'.$e->getTraceAsString());
            return Forge\Response::json(array('status' => 'ERROR', 'message' => $e->getMessage()));
            break;

        //case 'ErrorException':
        //    if ($logging) Forge\Log::write(Forge\Log::DEBUG, $e->getMessage() .'->'. $e->getFile() .'# '. $e->getLine() .' ->'.$e->getTraceAsString());
        //    return Forge\Response::json(array('status' => 'ERROR', 'message' => 'The system has encountered an error. Please try again if this error persists please inform the systems administrator.'));
        //    break;
        default:

            if ($logging) Forge\Log::write(Forge\Log::DEBUG, $e->getMessage() .'->'.$e->getTraceAsString());
            $error = ($vbose) ? array('error' => $e->getMessage() .' @ line #'. $e->getLine() .' in '. $e->getFile(), 'exception' => $e) : null;
            return Forge\Response::error(500, $error);
   }

});

// DOMPDF
define('DOMPDF_ENABLE_AUTOLOAD', false);
define("DOMPDF_ENABLE_PHP", true);
define("DOMPDF_ENABLE_CSS_FLOAT", true);

