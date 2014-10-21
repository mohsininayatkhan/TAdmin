<?php namespace Forge\Drivers\Cache;

interface CacheInterface
{

  /**
    * Get all cache data
  */
  public static function all();
   /**
    * Called when the Cache class instanciates this interface. Put startup logic here
    */
   public static function boot();
   
   /**
    * Check if the cache exists
    * @param string $key
    */
   public static function has($key);
   
   /**
    * Get data from the cache. If no cache exists, return the default
    * @param string $key
    * @param mixed $default
    */
   public static function get($key, $default);
   
   /**
    * Store cache data for a length of time (minutes?)
    * @param string $key
    * @param mixed $data
    * @param int $time
    */
   public static function put($key, $data, $time);
   
   /**
    * Combination of get/put. If the cache exists, it will be returned. If not the closure is run, the new value stored and then returned
    * @param string $key
    * @param \Closure $call
    * @param int $time
    */
   public static function remember($key, \Closure $call, $time);
   
   /**
    * Remove cache entry/ies
    * @param array $key
    */
   public static function delete($key);
   
   /**
    * Remove all cache entries
    */
   public static function deleteAll();
}
