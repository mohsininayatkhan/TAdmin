<?php namespace Exception;

/**
 * Helps handle/pass the validator around
 */
class Validation extends \Exception
{
   /**
    * An instance of the Validator class
    * @var \Forge\Validator
    */
   public $validation;
   
   public function __construct($message, \Forge\Validator $validation, \Exception $previous = null)
   {
      $this->validation = $validation;
      
      if (\Config::get('error.detail.validation'))
      {
         $errors = $this->validation->all();
         $error = array_shift($errors);
         $message .= " (". $error[0] .")";
      }
      
      // Pass along the chain..
      parent::__construct($message, 0, $previous);
   }
   
   /**
    * Shortcut to accessing the validator's properties
    * @access public
    * @param string $param Property name
    * @return mixed
    */
   public function __get($param)
   {
      return $this->validation->$param;
   }
   
   /**
    * Shortcut to accessing the validator's methods
    * @access public
    * @param string $method Method name
    * @param array $args Arguments
    * @return mixed
    */
   public function __call($method, $args)
   {
      return call_user_func_array(array($this->validation, $method), $args);
   }
   
   /**
    * Used by the XML/JSON SoapServer output handlers for quick formatting
    * @access public
    * @return array
    */
   public function toArray()
   {
      return array(
         'status' => 'ERROR',
         'message' => $this->getMessage(),
         'errors' => $this->validation->all()
      );
   }
}
