<?php

/**
 * Fancy wrapper class
 * <code>
 *    // Single request with data
 *    $result = Soap::api_request('username', 'password', $xml);
 * 
 *    // Use a group other than the default
 *    $result = Soap::connection('group')->api_request('username', 'password', $xml);
 * 
 *    // For mutile use
 *    $_soap = new Soap('group');
 *    $result = $_soap->api_request('username', 'password', $xml);
 * </code>
 * @author Ashley Wilson
 */
class Soap {
   /**
    * Soap config group
    * @access private
    * @var string
    */
   private $conn;
   
   /**
    * Instance of Soap Client
    * @access private
    * @var SoapClient
    */
   private $_client;
   
   /**
    * Create an instance of the soap wrapper
    * @access public
    * @param string $conn Soap group in the config
    */
   public function __construct($conn = '')
   {
      $this->conn = empty($conn) ? Config::get('app.soap.default') : $conn;
      
      $wsdl = Config::get("app.soap.{$this->conn}.wsdl");
      
      if (is_null($wsdl)) throw new Exception("No WSDL file found for the connection {$this->conn}");
      
      $this->_client = new SoapClient($wsdl, Config::get("app.soap.{$this->conn}.options", array()));
   }
   
   /**
    * Call either a registered method, or pass the method call to the SoapClient instance to handle
    * @access public
    * @param string $method Method name
    * @param array $arguments Array of parameters
    * @return mixed
    */
   public function __call($method, $arguments)
   {
      if (($closure = array_get(Soap::$funcs, $method)))
      {
         return call_user_func_array($closure, array($this, Config::get("app.soap.{$this->conn}"), $arguments));
      } else {
         return call_user_func_array(array($this->_client, $method), $arguments);
      }
   }
   
   /**
    * Dynamically get properties from the SoapClient instance
    * @access public
    * @param string $property The property
    * @return mixed
    * @throws Exception
    */
   public function __get($property)
   {
      if (property_exists($this->_client, $property))
      {
         return $this->_client->$property;
      } else {
         throw new Exception("The requested property does not exist [$property]");
      }
   }
   
   // ----------------------
   // Static section
   // ----------------------
   /**
    * Array of registered closure's
    * @access private
    * @static
    * @var array
    */
   private static $funcs = array();
   
   /**
    * Static wrapper for creating new soap instance
    * @access public
    * @static
    * @param string $conn Soap config group
    * @return Soap
    */
   public static function connection($conn = '')
   {
      return new Soap($conn);
   }
   
   /**
    * Register a custom method against the class
    * <code>
    *    // Passed 3x Params: Soap instance, config (array), arguments passed to call (array)
    *    Soap::register('json', function($soap, $config, $params){
    *       return $soap->json_call($params[0]);
    *    });
    * 
    *    $result = Soap::json('[{"key1":"value1","key2":"value2"}]);
    * </code>
    * @access public
    * @static
    * @param string $name Method name
    * @param Closure $func The closure
    */
   public static function register($name = null, Closure $func)
   {
      if (is_null($name)) throw new Exception("Name cannot be empty");
      
      array_set(self::$funcs, $name, $func);
   }
   
   /**
    * Wapper to create a new Soap instance, then make the method call against it
    * @access public
    * @static
    * @param string $method Method name
    * @param array $arguments Array of parameters
    * @return mixed
    */
   public static function __callStatic($method, $arguments)
   {
      $soap = self::connection();
      
      return call_user_func_array(array($soap, $method), $arguments);
   }
}
