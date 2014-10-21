<?php namespace Exception;

/**
 * Responsible for firing database error events
 */
class Sql extends \Exception
{
   public function __construct($message, $caller = null, Exception $previous = null)
   {
      // We want to know where the error was thrown, however we'll add a default if someone forgot to tell us!
      if (is_null($caller)) $caller = __METHOD__;
      
      $exp = explode('::', (string) $caller, 2);
      
      // Log the error!
      \Forge\Event::fire('error.fire', array(
         'locator' => (array_key_exists(0, $exp) ? $exp[0] : ''),
         'pointer' => (array_key_exists(1, $exp) ? $exp[1] : ''),
         'message' => $message,
         'type'    => 'sql'
      ));
      
      // Pass along the chain..
      parent::__construct($message, 0, $previous);
   }
   
   /**
    * Used by the XML/JSON SoapServer output handlers for quick formatting
    * @access public
    * @return array
    */
   public function toArray()
   {
      $output = array(
         'status' => 'ERROR',
         'message' => \Config::get('database.error_message')
      );
      
      if (\Config::get('error.detail.sql')) $output['errors']['error'] = array($this->getMessage());
      
      return $output;
   }
}
