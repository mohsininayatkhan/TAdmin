<?php namespace Forge;

/**
 * forge static core version 1.0
 *
 * @author John Tipping <john.tipping@supanet.net.uk>
 * @author Ashley Wilson <awilson@supanet.net.uk>
 */

class Core
{
    private static $inst;
    private static $started = false;

    private static $paths = array();
    
    private static $environemnts = array();
    
    private static $environment;

    public static function getInstance()
    {

        if (!isset(static::$inst)) {
            static::$inst = new Core();

        }
        return static::$inst;
    }

    private function __construct()
    {
       // private constructor so no one can instanciate the class

    }

    private function __clone()
    {
       // private clone to stop class cloning

    }

    public static function error($int = '404', $data = null)
    {
       $fire = Event::fire("error.$int", array('error' => $data));

       //if ($fire->isStopped() === false) throw new \Exception("An erorr $int has occuured [$data]");
    }

    public static function startup()
    {
       if (static::$started) return true;
       
       // Encryption
       if (Config::get('app.encryption_key')) {
           Encryption::seed(Config::get('app.encryption_key'));
       }

       // Alias
       foreach (Config::get('app.alias', array()) as $key => $alias)
       {
          class_alias($key, $alias);
       }

       // Events - Startup (needs starting early for DB)
       Event::boot();
       
       // Events - Register (allows setup in init)
       require_once Core::path('app') .'events.php';
       
       // Check for downtime
       if (Config::get('app.maintenance.enabled')) {
           return Event::fire('maintenance.enabled');
       }
       
       // Database
       Database::boot();

       // Session
       if (Config::get('session.type') != ''){
           Session::Load(Config::get('session.type'));
       }
       
       // App startup
       require_once Core::path('app') .'init.php';
       
       // Routes
       require_once Core::path('app') .'routes.php';
	   
	   // Triggers
	   require_once Core::path('app') .'triggers.php';
       
       // Modules
       foreach (Config::get('modules', array()) as $module => $detail) {
           if (array_get($detail, 'autostart') || Url::segment(0) == $module) {
               Module::start($module);
           }
       }

       Router::call();

       static::$started = true;
    }

    private static function setupEnvironment() {
        require_once(static::path('sys') .'server.php');
        
        if (Server::has('HTTPS')) {
            $host = "https://". Server::get('HTTP_HOST');
        } else {
            $host = "http://". Server::get('HTTP_HOST');
        }
        
        foreach (static::$environemnts as $key => $env) {
            $lookup = is_array($env) ? $env : array($env);
            
            foreach ($lookup as $p) {
                $p = str_replace('.', '\\.', $p);
                $p = str_replace('*', '^.+', $p);
                
                if (preg_match("~$p~", $host)) {
                    static::setEnvironment($key);
                    return;
                }
            }
        }
    }
    
    public static function setEnvironment($env) {
        if (array_key_exists($env, static::$environemnts) === false) {
            throw new \Exception("Environment not found [$env]");
        }
        
        static::$environment = $env;
    }

    public static function environment() {
        return static::$environment;
    }

    public static function setPaths($paths)
    {
       static::$paths = $paths;
    }
    
    public static function setEnvironments($environments) {
        static::$environemnts = $environments;
        
        static::setupEnvironment();
    }

    public static function path($key = null)
    {
       if (is_null($key) || empty($key)) return null;

       if (array_key_exists($key, static::$paths))
       {
          return static::$paths[$key];
       }

       return null;
    }

    public static function allPaths($key = null)
    {
       return static::$paths;
    }

}
