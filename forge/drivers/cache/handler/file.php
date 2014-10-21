<?php namespace Forge\Drivers\Cache\Handler;

use \Forge\Drivers\Cache\CacheInterface;

class File implements CacheInterface
{
   public static function boot()
   {
      return true;
   }

   public static function all()
   {
      $directory = new \DirectoryIterator(realpath(\Forge\Core::path('storage') .'cache'));
      $data = array();

      foreach($directory as $file) {
         if (!$file->isReadable()) continue;
         
         if ($file->isFile()) {

            $key = $file->getFilename();
            $path = $file->getPathname();
            $handle = \Forge\File::get($path);
            
            if ($handle) {
               $data[] = array('key' => $key, 'expires_at' => substr($handle, 0, 10));
            }
         }
      }

      return $data;
   
   }
   
   private static function getFile($file)
   {
      return \Forge\Core::path('storage') .'cache'. DIRECTORY_SEPARATOR . $file;
   }
   
   private static function parse($key)
   {
      $file = self::getFile($key);
      if (\Forge\File::exists($file))
      {
         $content = \Forge\File::get($file);
         $time = substr($content, 0, 10);
         $content = substr($content, 10, strlen($content));
         
         if (time() < $time) return unserialize($content);
         else self::delete($key);
      }
      
      return false;
   }
   
   public static function has($key)
   {
      return (bool) self::parse($key);
   }
   
   public static function get($key, $default = null)
   {
      if (($data = self::parse($key)) === false)
      {
         $data = is_callable($default) ? call_user_func($default) : $default;
      }
      
      return $data;
   }
   
   public static function put($key, $data, $mins = 0)
   {
      if ($mins == 0) $mins = 60 * 60 * 24 * 365 * 5; // 5 Years
      $time = time() + floor((int) $mins * 60);
      
      $file = self::getFile($key);
      \Forge\File::put($file, $time . serialize($data));
      
      return true;
   }
   
   public static function remember($key, \Closure $call, $mins = 0)
   {
      if (($data = self::parse($key)) === false)
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
           $file = self::getFile($key);

           if (\Forge\File::exists($file) ) {
               \Forge\File::delete($file);
           }
       }

       return true;
   }
   
   /**
     * Remove all cache entries
     * @return bool
    */
    public static function deleteAll() {
        $path = \Forge\Core::path('storage') .'cache');
        
        \Folder::remove($path, true);
    }

}
