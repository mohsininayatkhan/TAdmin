<?php namespace Forge;

/**
 * Work with the URL
 *
 * @author Ashley Wilson
 */
class Url
{
   /**
    * Return the request url
    * @param bool $strip If true, query string will be stripped from URL
    * @return string
    */
   public static function full($strip = false)
   {
      $uri = trim(str_replace(array(Config::get('app.url'), 'index.php'), '', Server::get('REQUEST_URI')), '/');
      
      if ($strip && strstr($uri, '?'))
      {
         $uri = substr($uri, 0, strpos($uri, '?'));
      }
      
      return $uri;
   }
   
   /**
    * Base URL
    * @return string
    */
   public static function base()
   {
      return 'http://'. Server::get('HTTP_HOST') .'/'. Config::get('app.url');
   }
   
   /**
    * Get a segment from the URL
    * @param int $key Segment key
    * @param mixed $default Default value if segment not found
    * @return string
    */
   public static function segment($key = null, $default = null)
   {
      if (is_numeric($key) === false) return $default;
      

      $uri = self::full(true);

      $exp = explode('/', $uri);

      $exp = array_filter($exp, function($value){
         return ! empty($value);
      });
      
      $key = $key++;
      
      return array_key_exists($key, $exp) ? $exp[$key] : $default;
   }
   
   /**
    * Return array of all segments
    * @return array
    */
   public static function segments()
   {
      $url_path = Url::full(true);
      
      $segments = explode('/', $url_path);

      $segments = array_filter($segments, function($value){
         return ! empty($value);
      });
      
      return $segments;
   }
}
