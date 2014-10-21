<?php

/**
 * Handles SoapServer requests:
 * - Enters at the __call method
 * - xmlHandler or jsonHandler(deafult) are called to parse the incoming variables
 * - Handler uses auth to log the user in. Once logged in, access user model with Auth::user()
 * - Finally, call doAPI. Auth will check the current user has access to the requested class
 * Exception handlers are setup for the following:
 * - ValidationException: Contains status, message and errors (array of errors)
 * - SqlException: Contains status, message (message is default set by config) and errors (if enabled in config)
 * - Exception: Contains status and message
 */
class SoapHandler
{
   /**
    * The requested class
    * @access private
    * @var string
    */
   private $class;
   
   /**
    * The requested method on the @see $class
    * @access private
    * @var string
    */
   private $method;
   
   /**
    * Parsed data to pass to the called class
    * @access private
    * @var array
    */
   private $data;
   
   /**
    * Perform login check using Api\Auth driver
    * @access private
    * @param string $username System username
    * @param string $password System password
    * @throws Exception
    */
   private function auth($username, $password)
   {
      // Log our user in. This will populate the Auth class with the EkvUser model!
      if (Auth::attempt(compact('username','password'))) return true;
      
      throw new Exception("Incorrect Username or Password Specified");
   }
   
   /**
    * Call the requested class/method and return the data
    * @access private
    * @throws Exception
    */
   private function doAPI()
   {
      if (is_null($this->class) || is_null($this->method)) throw new Exception('Missing a required parameter in request');
      
      // Do we have access to this class/method
      if (Auth::cannot($this->class, $this->method)) throw new \Exception\PermissionDenied(Auth::user()->user_id, $this->class, $this->method);
      
      try {
         // Create the class instance
         $class = $this->class;
         $obj = new $class();
      } catch (Exception $e)
      {
         // Throw a 'prettier' exception
         throw new Exception("Class does not exist: {$this->class}");
      }
      
      if (Config::get('app.access_logging'))
      {
         // Log the request
         ErpCore\EkvAccessLog::create(array(
            'class' => $this->class,
            'method' => $this->method,
            'created_at' => DB::Raw('now()'),
            'created_by' => Auth::user()->user_id
         ));
      }
      
      // Finally, call the requested method
      return $obj->{$this->method}((array) $this->data);
   }
   
   /**
    * Handle XML requests - Use SimpleXML class to convert to array
    * @access protected
    * @param string $username System username
    * @param string $password System password
    * @param string $request XML payload
    * @return mixed
    * @throws Exception
    */
   protected function xmlHandler($username = null, $password = null, $request = null)
   {
      // Auth the request
      $this->auth($username, $password);
      
      try {
         // Parse the XML into an array
         $_xml = @new SimpleXML($request);
         
         $arr = $_xml->toArray();
         
         $request = array_get($arr, 'request', array());
      } catch (Exception $e)
      {
         // Throw a 'prettier' exception
         throw new Exception("Invalid XML in request");
      }
      
      // Separate the input
      $this->class = array_get($request, '@category');
      $this->method = array_get($request, '@type');
      $this->data = array_except($request, array('@category', '@type'));
      
      return $this->doAPI();
   }
   
   /**
    * Handle JSON requests - Decode data to array
    * @access protected
    * @param string $username System username
    * @param string $password System password
    * @param string $request JSON payload
    * @return mixed
    * @throws Exception
    */
   protected function jsonHandler($username = null, $password = null, $request = null)
   {
      // Auth the request
      $this->auth($username, $password);
      
      // Parse the JSON into an array
      $arr = json_decode($request, true);
      
      // Is the request valid JSON?
      if (is_array($arr) === false) throw new Exception('Invalid JSON in request');
      
      // Separate the input
      $this->class = array_get($arr, 'category');
      $this->method = array_get($arr, 'type');
      $this->data = array_get($arr, 'data', array());
      
      return $this->doAPI();
   }
   
   /**
    * Convert any output to JSON
    * @access protected
    * @param string|array $arr An array or a string with the ':' shortcut
    * @return string
    */
   protected function wrapJSON($arr = '')
   {
      if (is_array($arr) === false)
      {
         $res = array();
         $exp = explode(":", $arr, 2);
         $res['status'] = array_key_exists(1, $exp) ? $exp[0] : 'ERROR';
         $res['message'] = $exp[1];
         $arr = $res;
      }
      
      return json_encode($arr);
   }
   
   /**
    * Convert any output to XML - Use SimpleXML class
    * @access protected
    * @param string|array $str An array or a string with the ':' shortcut
    */
   protected function wrapXML($str = '')
   {
      $_xml = new SimpleXML('<root/>');
      $response = array('response' => '');

      if (is_array($str))
      {
         $response['response'] = $str;
      } elseif (empty($str) === false)
      {
         $res = array();
         $exp = explode(":", $str, 2);
         $res['status'] = array_key_exists(1, $exp) ? $exp[0] : 'ERROR';
         $res['message'] = $exp[1];
         $response['response'] = $res;
      }

      return $_xml->toXML($response);
   }
   
   /**
    * The only method called by the Soap Server. We use this to wrap ALL output in the same way
    * @access public
    * @param string $method Called method name
    * @param array $args Array of arguments
    * @return string
    */
   public function __call($method, $args)
   {
      switch ($method)
      {
         case 'xml_request':
            try {
               $ret = call_user_func_array(array($this, 'xmlHandler'), $args);
               
               return $this->wrapXML($ret);
            } catch (Exception\PermissionDenied $e) {
                return $this->wrapXML("ERROR:".$e->getMessage());
            } catch (Exception\RouterNotFound $e) {
                return $this->wrapXML("ERROR:".$e->getMessage());
            } catch (Exception\Validation $e)
            {
               return $this->wrapXML($e->toArray());
            } catch (Exception\Sql $e)
            {
               return $this->wrapXML($e->toArray());
            } catch (Exception $e)
            {
                try {
                    Forge\Event::fire('error.fire', array(
                        'locator' => $e->getFile(),
                        'pointer' => $e->getLine(),
                        'message' => $e->getMessage(),
                        'type'    => get_class($e),
                        'trace'   => $e->getTraceAsString()
                    ));
                } catch (Exception $e2) {
                    //
                }
                
               return $this->wrapXML("ERROR:". $e->getMessage());
            }
            break;
         case 'json_request':
         default:
            try {
               $ret = call_user_func_array(array($this, 'jsonHandler'), $args);
               
               return $this->wrapJSON($ret);
            } catch (Exception\PermissionDenied $e) {
                return $this->wrapJSON("ERROR:".$e->getMessage());
            } catch (Exception\RouterNotFound $e) {
                return $this->wrapJSON("ERROR:".$e->getMessage());
            } catch (Exception\Validation $e)
            {
               return $this->wrapJSON($e->toArray());
            } catch (Exception\Sql $e)
            {
               return $this->wrapJSON($e->toArray());
            } catch (Exception $e)
            {
                try {
                    Forge\Event::fire('error.fire', array(
                        'locator' => $e->getFile(),
                        'pointer' => $e->getLine(),
                        'message' => $e->getMessage(),
                        'type'    => get_class($e),
                        'trace'   => $e->getTraceAsString()
                    ));
                } catch (Exception $e2) {
                    //
                }
                
               if (\Forge\Core::environment() != 'live') {
                  return $this->wrapJSON("ERROR:". $e->getMessage() ." ". $e->getFile() ." @ ". $e->getLine());
               } else {
                  return $this->wrapJSON("ERROR:". $e->getMessage());
               }
            }
      }
   }
   
   /**
    * As a cleanup, log or user out!
    */
   public function __desctruct()
   {
      //Auth::logout();
   }
}

