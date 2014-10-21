<?php namespace Forge;

/**
 * Mass validator
 * <code>
 * $input = array('username' => 'Fred', 'password' => 'superduper');
 * $rules = array('username' => 'required|alpha', 'password' => 'alpha');
 * 
 * $validator = new \Forge\Validator($input, $rules);
 * if ($validator::fails)
 * {
 *    echo "Failed validation";
 * } else {
 *    echo "Passed validation";
 * }
 * </code>
 * @author Ashley Wilson
 */

class ValidatorNotFound extends \Exception {};

class Validator
{
   /**
    * The input being tested
    * @var array $input
    */
   private $input;
   
   /**
    * The validation list to apply
    * @var array $rules
    */
   private $rules;
   
   /**
    * List of validation errors
    * @var array $errors
    */
   private $errors = array();
   
   /**
    * List of error messages
    * @var array $messages
    */
   private $messages = array(
      'default' => 'The :attribute is invalid'
   );
   
   /**
    * Custom validators
    * @var array $custom
    */
   private static $custom = array();
   
   /**
    * Internal var to check if the validator passed/failed
    * @var bool $passed
    */
   private $passed = true;
   
   /**
    * The the input, rules and custom messages.
    * The class, custom and config messages will be merged at this point.
    * @param array $input
    * @param array $rules
    * @param array $messages
    */
   public function __construct($input, $rules, $messages = array())
   {
      $this->input = $input;
      $this->rules = $rules;
      $this->messages = array_merge($this->messages, (array) Config::get('validator'), $messages);

      $this->process();
   }
   
   /**
    * Get all errors for a specfic key.
    * Add formatting to $wrap to have output 'wrapped' (the original message will be replaced at :message):
    * <code>
    * // Example using required:
    * $validator->get('username', '<p>Failed: <b>:message</b></p>');
    * // Yields:
    * <p>Failed: <b>The username field is required</b></p>
    * </code>
    * @param string $key
    * @param string $wrap
    * @return array
    */
   public function get($key, $wrap = ':message')
   {
      $result = array();

      foreach (($this->has($key) ? $this->errors[$key] : array()) as $error)
      {
         $result[] = str_replace(':message', $error, $wrap);
      }

      return $result;
   }
   
   /**
    * Retrieve the first error for a given field
    * @param string $key
    * @param string $wrap
    * @return string
    */
   public function first($key, $wrap = ':message')
   {
      $res = $this->get($key, $wrap);
      return array_shift($res);
   }
   
   /**
    * Test if a specific input has failed
    * @param string $key
    * @return bool
    */
   public function has($key)
   {
      return array_key_exists($key, $this->errors);
   }
   
   /**
    * Retrieve all errors in a multi dimensional array
    * @param string $wrap
    * @return array
    */
   public function all($wrap = ':message')
   {
      $result = array();

      foreach ($this->errors as $key => $error)
      {
         $result[$key] = $this->get($key, $wrap);
      }

      return $result;
   }
   
   /**
    * Add a custom validator to be used
    * <code>
    * \Forge\Validator::register('json', function($value, $params, $validator){
    *    return is_null(json_decode($value));
    * });
    * </code>
    * @param string $key The validator name
    * @param \Closure $closure The callback to apply
    */
   public static function register($key, \Closure $closure)
   {
      self::$custom[$key] = $closure;
   }
   
   /**
    * Pushes error 'messsages' on an array and sets the validation to failed
    * @param string $key
    * @param string $type
    */
   public function set_error($key, $type = '')
   {
      $text = array_key_exists($type, $this->messages) ? $this->messages[$type] : $this->messages['default'];
      $value = (array_key_exists($key, $this->input) && is_array($this->input[$key]) === false) ? $this->input[$key] : null;

      $this->errors[$key][] = str_replace(array(':attribute', ':value'), array($key, $value), $text);

      $this->passed = false;
   }
   
   /**
    * Is a value required
    * <code>
    * $rule = array('id' => 'required');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_required($value)
   {
      return (bool) ($value != "" || $value === 0);
   }
   
   /**
    * Does a value only contain alpha characters
    * <code>
    * $rule = array('username' => 'alpha');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_alpha($value)
   {
      return ! preg_match('/[^a-zA-Z]/', $value);
   }
   
   /**
    * Does a value only contain alphanumeric characters
    * <code>
    * $rule = array('username' => 'alphanum');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_alphanum($value)
   {
      return ! preg_match('/[^a-zA-Z0-9]/', $value);
   }
   
   /**
    * Does a value only contain alphanumeric characters and hyphen
    * <code>
    * $rule = array('username' => 'alphanumhyphen');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_alphanumhyphen($value)
   {
      return ! preg_match('/[^a-zA-Z0-9-]/', $value);
   }
   
   /**
    * Is the value an email address
    * <code>
    * $rule = array('email' => 'test@test.com');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_email($value)
   {
      return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
   }
   
   /**
    * Is the value equal to this value
    * <code>
    * $rule = array('status' => 'equal:active');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_equal($value, $param = null)
   {
      return $value == $param;
   }
   
   /**
    * Is a value numeric
    * <code>
    * $rule = array('id' => 'numeric');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_numeric($value)
   {
      return is_numeric($value);
   }
   
   /**
    * Perform a custom regex
    * <code>
    * $rule = array('name' => 'regex:/^[a-zA-Z0-9]+$/');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_regex($value, $param = null)
   {
      return (bool) preg_match($param, $value);
   }
   
   /**
    * Check an item exists in the database
    * <code>
    * $rule = array('user' => 'exists:system.user,user_id');
    * </code>
    * @param string $value
    * @param string $param
    * @return bool
    */
   private function process_exists($value, $param = null)
   {
      $fields = explode(',', $param, 3);
      
      if (count($fields) < 2 || count($fields) > 3) throw new \Exception("Exists validator: incorrect param count");
      
      if (count($fields) == 2) {
          // Use the default db connection
          $conn = Config::get('database.default');
          array_unshift($fields, $conn);
      }
      
      list($connection, $table, $field) = $fields;
      
      $result = Database::connection($connection)->table($table)->where($field, '=', $value)->count();
      
      return $result == 0 ? false : true;
   }
   
   /**
    * Check an item is at least a min value (inclusive)
    * <code>
    * $rule = array('count' => 'min:5');
    * </code>
    * @param string $value
    * @param string $min
    * @return bool
    */
   private function process_min($value, $min) {
       if (is_numeric($value) === false) {
           if (is_array($value)) {
               $value = count($value);
           } else {
               $value = strlen($value);
           }
       }
       
       if ($value < $min) {
           return false;
       } else {
           return true;
       }
   }
   
   /**
    * Check an item is below a max value (inclusive)
    * <code>
    * $rule = array('percentage' => 'max:75');
    * </code>
    * @param string $value
    * @param string $max
    * @return bool
    */
   private function process_max($value, $max) {
       if (is_numeric($value) === false) {
           if (is_array($value)) {
               $value = count($value);
           } else {
               $value = strlen($value);
           }
       }
       if ($value > $max) {
           return false;
       } else {
           return true;
       }
   }
   
   /**
    * Check a value falls between a min and max (inclusive)
    * <code>
    * $rule = array('share' => 'between:3,4');
    * </code>
    * @param string $value
    * @param string $param
    * @return bool
    */
   private function process_between($value, $param = null) {
       $fields = explode(',', $param, 2);
       
       if (count($fields) != 2) throw new \Exception("Between validator: incorrect param count");
       
       if ($value <= $fields[0] || $value >= $fields[1]) {
           return false;
       } else {
           return true;
       }
   }
   
   /**
    * Check a value is a given length (string or array)
    * <code>
    * $rule = array('contacts' => 'length:5');
    * $rule = array('password' => 'length:8');
    * </code>
    * @param string $value
    * @param int $count
    * @return bool
    */
   private function process_length($value, $count) {
       if (is_array($value)) {
           if (count($value) != $count) {
               return false;
           } else {
               return true;
           }
       } else {
           if (strlen($value) != $count) {
               return false;
           } else {
               return true;
           }
       }
   }
   
   /**
    * Wrapper for is_array
    * <code>
    * $rule = array('ids' => array('1','2','3');
    * </code>
    * @param string $value
    * @return bool
    */
   private function process_array($value) {
       return is_array($value);
   }
   
   /**
    * Is a value 'in' a given set of values
    * <code>
    * $rule = array('id' => 'in:1,2,3');
    * </code>
    * @param string $value
    * @param string $param
    * @return bool
    */
   private function process_in($value, $param = null)
   {
      return in_array($value, explode(',', $param));
   }
   
   private function process_date($value) {
       return (bool) strtotime($value);
   }
   
   private function process_url($value) {
       return (bool) filter_var($value, FILTER_VALIDATE_URL);
   }
   
   /**
    * Applies the validation rules to the input
    * @throws \Forge\ValidatorNotFound
    */
   private function process()
   {
      foreach ($this->rules as $key => $rule)
      {
         $exp = explode('|', $rule);

         foreach ($exp as $method)
         {
            if (strstr($method, ':')) list($method, $param) = explode(':', $method, 2);
            else $param = '';
            
            $input = array_get($this->input, $key);

            if (method_exists($this, "process_$method") === false)
            {
               if (!$input) continue;
                
               if (array_key_exists($method, self::$custom))
               {
                  if (call_user_func(self::$custom[$method], $input, $param, $this) !== true) $this->set_error($key, $method);
               } else {
                  throw new ValidatorNotFound("No validator exists for [$method]");
               }
            } else {
               if (($input || $method == 'required') && $this->{"process_$method"}($input, $param, $this) !== true) $this->set_error($key, $method);
            }
         }
      }
   }
   
   /**
    * Check if the validation failed
    */
   public function fails()
   {
      return ! $this->passed;
   }

   /**
    * Check if the validation passed
    */
   public function passed()
   {
      return $this->passed;
   }
}
