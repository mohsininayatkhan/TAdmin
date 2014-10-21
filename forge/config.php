<?php namespace Forge;

/**
 * Set/get config items using dot notated strings
 * @author Ashley Wilson
 */
class Config
{
   /**
    * Store a cached version of the config array
    * @var array
    */
   private static $cache = array();

   /**
    * Config config item(s) from config arrays
    *
    * @param string $key
    * @param mixed $default
    * @param return mixed
    *
    */
   public static function get($key, $default = null)
   {
      $explode = explode('.', $key);
      $file = array_shift($explode);

      if (array_key_exists($file, self::$cache) === FALSE)
      {

         $file_path = Core::path('app').'config/'. $file .'.php';

         if (file_exists($file_path) === FALSE)
            throw new \Exception("Config file does not exist (" . $file . ")");

         $data = require_once ($file_path);

         // Environment checks
         $env = Core::environment();

         $env_path = Core::path('app') .'config/'. $env .'/'. $file .'.php';

         if ($env && file_exists($env_path)) {
             $env_data = require_once ($env_path);
             $data = self::merge($data, $env_data);
         }

         self::$cache[$file] = $data;
      }

      $search = self::$cache[$file];

      if (count($explode) == 0)
         return $search;

      for ($i = 0; $i < count($explode); $i++)
      {
         if (array_key_exists($explode[$i], $search))
         {
            $search = $search[$explode[$i]];
         } else
         {
            return $default;
         }
      }

      return $search;
   }

   /**
    * Merge 2 arrays, where the 2nd overrides values from the first
    *
    * @param array $array
    * @param array $new_arry
    * @return array
    */
   private static function merge($array, $new_array)
   {
      $merged = $array;

      foreach ($new_array as $key => &$value)
      {
         if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
         {
            $merged[$key] = self::merge($merged[$key], $value);
         } else {
            $merged[$key] = $value;
         }
      }

      return $merged;
   }

   /**
    * Override a config variable
    *
    * @param string $key
    * @param mixed $value
    */
   public static function set($str, $value)
   {
      // Create an array from a . noted string
      $explode = explode('.', $str);

      $file = array_shift($explode);
      $result = array(array_pop($explode) => $value);

      for ($i = (count($explode) - 1); $i >= 0; $i--)
      {
         $result = array($explode[$i] => $result);
      }

      self::$cache[$file] = self::merge(self::get($file), $result);

      return $result;
   }

   /**
    * Config config item(s) from config arrays
    *
    * @param string $key
    * @param mixed $default
    * @param return mixed
    *
    */
   public static function exists($key)
   {
      $explode = explode('.', $key);
      $file = array_shift($explode);

      if (array_key_exists($file, self::$cache) === FALSE)
      {

         $file_path = Core::path('app').'config/'. $file .'.php';

         if (file_exists($file_path) === FALSE) return false;


         $data = require_once ($file_path);

         // Environment checks
         $env = Core::environment();

         $env_path = Core::path('app') .'config/'. $env .'/'. $file .'.php';

         if ($env && file_exists($env_path)) {
             $env_data = require_once ($env_path);
             $data = self::merge($data, $env_data);
         }

         self::$cache[$file] = $data;
      }

      $search = self::$cache[$file];

      if (count($explode) == 0) return true;

      for ($i = 0; $i < count($explode); $i++)
      {
         if (array_key_exists($explode[$i], $search))
         {
            $search = $search[$explode[$i]];

         } else {
            return false;

         }
      }

      return true;
   }
}
