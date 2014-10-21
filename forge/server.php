<?php namespace Forge;

/**
 * Server handler: retrieves the server variables
 *
 * @author John Tipping
 */
class Server
{
   public static function get($key = null, $default = null)
   {
      if (empty($key)) return $_SERVER;

      return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $default;
   }

   public static function has($key)
   {
      return array_key_exists($key, $_SERVER);
   }

}