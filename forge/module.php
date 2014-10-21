<?php namespace Forge;

class Module {
    private static $loaded = array();
    
    public static function start($module) {
        if (in_array($module, self::$loaded)) return;
        
        if (self::exists($module) === false) {
            throw new \Exception("Module does not exist [$module]");
        }
        
        $path = self::path($module);
        
        // Load the required files
        require_once $path . 'init.php';
        
        if (File::exists($path . 'routes.php')) {
            require_once $path . 'routes.php';
        }
        
        if (File::exists($path . 'events.php')) {
            require_once $path . 'events.php';
        }
        
        if (Config::get("modules.$module.map")) {
            Router::map($path ."controllers/", $module, "$module\\controllers");
        }
        
        if (Config::get("modules.$module.autoload")) {
            Autoload::dir($path, $module);
            Autoload::dir(Config::get("modules.$module.autoload.paths"));
            Autoload::map(Config::get("modules.$module.autoload.class"));
        }
        
        self::$loaded[] = $module;
    }
    
    public static function exists($module) {
        return File::exists(Core::path('module') . $module);
    }
    
    public static function path($module) {
        return Core::path('module') . $module . DIRECTORY_SEPARATOR;
    }
}
