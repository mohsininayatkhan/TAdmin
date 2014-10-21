<?php namespace Forge;

class CacheHandlerNotFound extends \Exception {};

/**
 * Cache handler
 * @author Ashley Wilson
 */
class Cache
{
   /**
    * Cache handler
    */
   private static $handle;
   
   /**
    * Instanciate the handler from the config
    */
   private static function startup()
   {
      $driver = Config::get('cache.driver', 'file');

      $handler = "\Forge\Drivers\Cache\Handler\\".ucfirst($driver);
      
      // Ask the autoloader to load our class, so we can test it exists before actually calling it!
      Autoload::load($handler);
      
      // If the framework version doesn't exist, look for a system wide version
      if (class_exists($handler, false) === false)
      {
         $handler = $driver;
         Autoload::load($handler);
         
         // Check it one more time..
         if (class_exists($handler, false) === false) throw new CacheHandlerNotFound("Cache handler does not exist [$driver]");
      }
      
      self::$handle = $handler;
      self::boot(); // Start the Cache driver
   }
   
   /**
    * Call methods on the handler instance here
    */
   public static function __callStatic($method, $params)
   {
      // Create our handler instance if it doesn't already exist
      if (is_null(self::$handle)) self::startup();
      
      return call_user_func_array(array(self::$handle, $method), $params);
   }
}
