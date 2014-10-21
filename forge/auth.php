<?php namespace Forge;

class AuthHandlerNotFound extends \Exception {};

/**
 * auth model for basic user authentication
 *
 * @Author John Tipping <john.tipping@supanet.net.uk>
 */
class Auth {
   /*
    * stores the users credentials
    */
   private static $user = null;
   
   private static $handle;

   public static function user() {
      return self::$user;
   }

   public static function getDriver() {
      if (is_null(self::$handle))
      {
         $driver = Config::get('auth.driver', 'database');
   
         $handler = "\Forge\Drivers\Auth\Handler\\".ucfirst($driver);
         
         // Ask the autoloader to load our class, so we can test it exists before actually calling it!
         Autoload::load($handler);
         
         // If the framework version doesn't exist, look for a system wide version
         if (class_exists($handler, false) === false)
         {
            $handler = $driver;
            Autoload::load($handler);
            
            // Check it one more time..
            if (class_exists($handler, false) === false) throw new AuthHandlerNotFound("Auth handler does not exist [$driver]");
         }
         
         self::$handle = $handler;
      } else {
         $handler = self::$handle;
      }

      return $handler;
   }

   public static function attempt($credentials) {
       if (is_null($credentials['username']) || is_null($credentials['password']))
       {
           return false;
       }
       
       $handler = self::getDriver();
       
       if (!$handler::attempt($credentials)) {
           return false;
       }
       
       if (is_null($handler::user())) {
           self::$user = null;
           return false;
       }
       
       self::$user = $handler::user();
       return true;
   }

   public static function check() {
       $handler = self::getDriver();

       if (!$handler::check()) {
           return false;
       }
      
       self::$user = $handler::user();
       return true;
   }
   
   public static function __callStatic($method, $params) {
       $driver = self::getDriver();
       
       return call_user_func_array(array($driver, $method), $params);
   }
}
