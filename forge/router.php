<?php namespace Forge;

/**
 * Map urls to either Router closure's or controllers
 * 
 * @author Ashley Wilson and friends
 */
class Router
{
   /**
    * List of registered closures
    */
   private static $routes = array();
   
   /**
    * Triggers attached to defined routes
    */
   private static $route_triggers = array();
   
   /**
    * List of registered controllers
    */
   private static $controllers = array();
   
   /**
    * List of registered triggers
    */
   private static $triggers = array();
   
   /**
    * Create a new trigger
    * @param string $name Trigger name
    * @param \Closure $call Callback
    */
   public static function trigger($name, $call)
   {
      if (is_callable($call) === false) throw new \Exception("Invalid callback passed to router::trigger");
      
      self::$triggers[$name] = $call;
   }
   
   /**
    * Register a url pattern to a closure to allow custom routing logic may be applied:
    * - Use * for a dynamic field that is mandatory
    * - Use ? for a dynamic field that is optional
    * 
    * <code>
    * \Forge\Router::register('/', function(){
    *    echo "Home page";
    * });
    * 
    * \Forge\Router::register('user/profile/*', function($map){
    *    echo "User: ". $map[0];
    * });
    * 
    * \Forge\Router::register('home/?/?', function($map){
    *    echo "Home/". $map[0] ."/". $map[1];
    * });
    * </code>
    * @param string|array $pattern Either a string, or an array of strings for multi assign
    * @param \Closure $call Callback function
    */
   public static function register($pattern, $data)
   {
      $call = null;
      $before = null;
      $after = null;
      
      if (is_array($data))
      {
         $call = array_get($data, 0);
         $before = array_get($data, 'before');
         $after = array_get($data, 'after');
      } else {
         $call = $data;
      }
      
      if (is_null($call)) throw new \Exception("Invalid callback passed to router::register");

      foreach ((array) $pattern as $p)
      {
         $p = preg_replace(array('/\*/', '/\?/'), array('([^/]+)', '*([^/]*)'), $p);
         $patt = "~^{$p}$~";
         
         if ($before) self::$route_triggers[$patt]['before'] = $before;
         if ($after)  self::$route_triggers[$patt]['after'] = $after;
         
         self::$routes[$patt] = $call;
         
      }
      /*
echo "<pre>";
        print_r(self::$routes);
      echo "</pre>";
*/
   }
   
   /**
    * Register a url pattern to a controller/class
    * 
    * <code>
    * \Forge\Router::controller('admin/user', '\Admin\User');
    * </code>
    * @param string $pattern Url pattern
    * @param string $call Class name
    */
   public static function controller($pattern, $call)
   {
      self::$controllers["~^{$pattern}([/].+)?$~"] = $call;
   }
   
   /**
    * Map controller directory using namespace/class name for URL paths
    * @param string $path Start directory path
    */
   public static function map($path, $pattern = '', $namespace = '')
   {
      if (is_dir($path) === false) throw new \Exception("Not a valid directory [$path]");
      
      $pattern = ltrim(str_finish($pattern, '\\'), '\\');
      if (empty($namespace)) $namespace = $pattern;
      else $namespace = str_finish($namespace, '\\');
      
      $dir = new \DirectoryIterator(realpath($path));
      
      foreach ($dir as $file)
      {
         if ($file->isFile() || $file->isLink())
         {
            $full_path = $pattern . $file->getBaseName('.php');
            $ns_path = $namespace . $file->getBaseName('.php');
            self::controller(str_replace('\\', '/', $full_path), $ns_path);
         } elseif ($file->isDot() == false && $file->isDir())
         {
            // Recursive call in to sub-directory
            self::map($file->getPathName(), $pattern . $file->getBaseName() .'\\');
         }
      }
   }
   
   /**
    * Call a route and apply triggers
    * @access private
    * @param string $pattern
    * @param mixed $data
    */
   private static function route_page($pattern = '~^/$~', $data = null)
   {
      if (array_key_exists($pattern, self::$routes))
      {
         if ($before = self::route_before($pattern)) return $before;
         
         $output = call_user_func(self::$routes[$pattern], $data);
         
         if ($after = self::route_after($pattern, $output)) $output = $after;
         
         return Response::make($output);
      } else {
         Core::error(404, "The requested path does not exist [$url_path]");
      }
   }
   
   /**
    * Apply a before trigger to a route. If content is returned, route should stop and display this content
    * @access private
    * @param string $pattern
    * @return string
    */
   private static function route_before($pattern)
   {
      if ($before = array_get(self::$route_triggers, "$pattern.before"))
      {
         if ($ret = call_user_func(self::$triggers[$before])) return Response::make($ret);
      }
   }
   
   /**
    * Apply an after trigger to a route. If content is returned, this should be used in place of the output
    * @access private
    * @param string $pattern
    * @param string $output
    * @return string
    */
   private static function route_after($pattern, $output)
   {
      if ($after = array_get(self::$route_triggers, "$pattern.after"))
      {
         return call_user_func(self::$triggers[$after], $output);
      }
   }
   
   /**
    * Process the URL and call a closure/class to handle the request
    */
   public static function call()
   {
      $url_path = Url::full(true);
      
      if (strstr($url_path, '?'))
      {
         $url_path = substr($url_path, 0, strpos($url_path, '?'));
      }

      if (count(Url::segments()) == 0) return self::route_page();

      $match = false;
      
      // Routes
      foreach (self::$routes as $pattern => $call)
      {
         // Exit in case of no match in url_map. For example when this is secure IPN interface and no 404 error defined.
         if (!preg_match($pattern, $url_path, $regs))
         {
            continue;
         } else {
            // Filter empty value
            $regs = array_filter($regs, function($value) {
                if ($value == '') return false;
                else return true;
            });
             
            $match = true;
            break;
         }
      }

      if ($match === true)
      {
         array_shift($regs);

         if ($call instanceof \Closure)
         {
            return self::route_page($pattern, $regs);
         }
      }

      // Controllers
      foreach (self::$controllers as $pattern => $call)
      {
         if (!preg_match($pattern, $url_path, $regs))
         {
            continue;
         } else {
            $match = true;
            break;
         }
      }
      
      if ($match === true)
      {
         array_shift($regs);
         $regs = array_key_exists(0, $regs) ? $regs : array('index');

         $c_seg = explode('/', trim($regs[0], '/'));
         $method = array_shift($c_seg);

         $obj = new $call();
         
         // Before
         if (property_exists($obj, 'before') && $before = self::controller_before($method, $obj->before)) return $before;
         
         $output = $obj->{$method}($c_seg);
         
         // After
         if (property_exists($obj, 'after') && $after = self::controller_after($method, $output, $obj->after)) $output = $after;
         
         return Response::make($output);
      }
      
      /*
if ($match === true)
      {
         array_shift($regs);
         $regs = array_key_exists(0, $regs) ? $regs : array('index');

         $c_seg = explode('/', trim($regs[0], '/'));
         $method = array_shift($c_seg);

         //$obj = new $call();
         
         // Before
         //if (property_exists($obj, 'before') && $before = self::controller_before($method, $obj->before)) return $before;
         
         //$output = $obj->{$method}($c_seg);
         
         $output = $call($regs);
         
         // After
         //if (property_exists($obj, 'after') && $after = self::controller_after($method, $output, $obj->after)) $output = $after;
         
         return Response::make($output);
      }
*/

      Core::error(404, "The requested path does not exist [$url_path]");
   }

   /**
    * Apply a before trigger to controller action
    * @access private
    * @param string $method
    * @param mixed $before
    * @return string
    */
   private static function controller_before($method, $before = null)
   {
      $trigger = self::controller_trigger($before, $method);
      
      if (array_key_exists($trigger, self::$triggers))
      {
         if ($ret = call_user_func(self::$triggers[$trigger])) return Response::make($ret);
      }
   }
   
   /**
    * Lookup a trigger from an array
    * @access private
    * @param string $lookup
    * @param string $method
    * @return string
    */
   private static function controller_trigger($lookup, $method)
   {
      if (is_array($lookup))
      {
         $only = array_get($lookup, 'only');
         
         if (is_array($only) && in_array($method, $only)) $lookup = array_get($lookup, 'filter');
         
         $except = array_get($lookup, 'except');
         
         if (is_array($except) && in_array($method, $except) === false) $lookup = array_get($lookup, 'filter');
      }
      
      if (is_array($lookup)) $lookup = '';
      
      return $lookup;
   }
   
   /**
    * Apply an after trigger to a controller action
    * @access private
    * @param string $method
    * @param string $output
    * @param string $after
    * @return string
    */
   private static function controller_after($method, $output, $after = null)
   {
      $trigger = self::controller_trigger($after, $method);
      
      if (array_key_exists($trigger, self::$triggers))
      {
         return call_user_func(self::$triggers[$trigger], $output);
      }
   }
}