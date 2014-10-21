<?php namespace Forge\Drivers\Cache\Handler;

use \Forge\Drivers\Cache\CacheInterface;

class Database implements CacheInterface
{
   private static $table;
   
   public static function boot()
   {
      self::$table = \Forge\Config::get('cache.database.table');
      
      if (is_null(self::$table)) throw new \Exception("Database cache - No table defined");
      
      if (mt_rand(0, 100) > 80) self::gc();
      return true;
   }

   public static function all()
   {
      return \Forge\Database::table(self::$table)->get();
   }
   
   private static function gc()
   {
      \Forge\Database::table(self::$table)->where('expires_at', '<', time())->delete();
   }
   
   public static function has($key)
   {
      $query = \Forge\Database::table(self::$table)->where('key', '=', $key)->where('expires_at', '>', time())->first();
      return ! is_null($query);
   }
   
   public static function get($key, $default = null)
   {
      $query = \Forge\Database::table(self::$table)->where('key', '=', $key)->where('expires_at', '>', time())->pluck('data');
      
      if (is_null($query) === false)
      {
         $data = unserialize($query);
      } else {
         $data = is_callable($default) ? call_user_func($default) : $default;
      }
      
      return $data;
   }
   
   public static function put($key, $data, $mins = 0)
   {
      if ($mins == 0) $mins = 60 * 60 * 24 * 365 * 5; // 5 Years
      $time = time() + floor((int) $mins * 60);
      
      self::delete($key);
      
      \Forge\Database::table(self::$table)->insert(array('key' => $key, 'data' => serialize($data), 'expires_at' => $time));
      
      return true;
   }
   
   public static function remember($key, \Closure $call, $mins = 0)
   {
      $data = static::get($key);
      
      if (is_null($data))
      {
         $data = call_user_func($call);
         self::put($key, $data, $mins);
      }
      
      return $data;
   }
   
   public static function delete($key)
   {
       if (is_array($key)) {
          foreach($key as $entry) {
              self::delete($entry);
          }
      } else {
           $pattern = '/^\*|\*$/';
           $replacement = '%';

           $entry = preg_replace($pattern, $replacement, $key);
           \Forge\Database::table(self::$table)->where('key', 'like', $entry)->delete();
      }

      return true;
   }
   
   public static function deleteAll()
   {
       return (bool) \Forge\Database::table(self::$table)->delete();
   }
}
