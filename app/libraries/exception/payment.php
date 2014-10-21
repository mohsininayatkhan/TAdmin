<?php namespace Exception;

/**
 * Used for Secpay payments
 */
class Payment extends \Exception
{
   private $secpay;
   
   public function __construct($message, array $secpay = array(), Exception $previous = null)
   {
      // The response from secpay as an array
      $this->secpay = $secpay;
      
      if (\Config::get('app.payment.detailed')) $message .= " [". array_get($secpay, 'message', 'Unkown error') ."]";
      
      // Pass along the chain..
      parent::__construct($message, 0, $previous);
   }
   
   /**
    * Return the whole data array
    * @access public
    * @return array
    */
   public function getSecpay()
   {
      return $this->secpay;
   }
   
   /**
    * Quick access getter for the secpay array
    * @access public
    * @param string $param Property name
    * @return string
    */
   public function __get($param)
   {
      return $this->get($param);
   }
   
   public function get($param, $default = null)
   {
      return array_key_exists($param, $this->secpay) ? $this->secpay[$param] : $default;
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
         'message' => $this->getMessage()
      );
      
      if (Config::get('error.detail.payment')) $output['errors']['error'] = array($this->get('message', 'Unkown error'));
      
      return $output;
   }
}
