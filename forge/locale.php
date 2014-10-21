<?php namespace Forge;

class LocaleHandlerNotFound extends \Exception {};

/**
 * Localize a string using locale drivers.
 * Requires locale table!
 * 
 * @author Ashley Wilson
 */
class Locale
{
   private static $handle;
   
   /**
    * Translate a string into the language as defined in the app config
    * @param string $string
    * @return string
    * @throws LocaleHandleNotFound
    */
   public static function translate($string)
   {
      $locale = Config::get('app.locale.language', 'en');
      
      if (is_null(self::$handle))
      {
         $driver = Config::get('app.locale.driver', 'database');
   
         $handler = "\Forge\Drivers\Locale\Handler\\".ucfirst($driver);
         
         // Ask the autoloader to load our class, so we can test it exists before actually calling it!
         Autoload::load($handler);
         
         // If the framework version doesn't exist, look for a system wide version
         if (class_exists($handler, false) === false)
         {
            $handler = $driver;
            Autoload::load($handler);
            
            // Check it one more time..
            if (class_exists($handler, false) === false) throw new LocaleHandlerNotFound("Locale handler does not exist [$driver]");
         }
         
         self::$handle = $handler;
      } else {
         $handler = self::$handle;
      }
      
      return $handler::lookup($string, $locale);
   }
}
