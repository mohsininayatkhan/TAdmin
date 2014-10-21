<?php namespace Forge;

/**
 * Class/directory autoloader.
 * Uses PSR-0 (@see https://github.com/php-fig/fig-standards/blob/master/README.md) standard autoloader
 *
 * @author Ashley Wilson
 */
class Autoload
{
    /**
     * Directories to scan
     */
    private static $dirs = array(array('dir' => '', 'ns' => ''));

    /**
     * Map class names to a specific location
     */
    private static $maps = array();

    /**
     * The autoloader function as called by PHP
     */
    public static function load($class_full) {
        if (substr($class_full, 0, 1) == '\\') {
            $class_full = ltrim($class_full, '\\');
        }
        
        // The simple way - Class map
        if (array_key_exists($class_full, self::$maps)) {
            require_once self::$maps[$class_full];
            return;
        }
        
        $namespace = explode('\\', $class_full);
        $class = array_pop($namespace);
        
        if (strstr($class, '_')) {
            $exp = explode('_', $class);
            $class = array_pop($exp);
            $namespace = array_merge($namespace, $exp);
        }
        
        $dir = implode(\DIRECTORY_SEPARATOR, $namespace);
        
        // The hard way - Directory scans
        foreach (self::$dirs as $map) {
            $ns = $dir;
            
            if (empty($map['ns']) === false && strtolower(substr($ns, 0, strlen($map['ns']))) == $map['ns']) {
                $len = strlen($map['ns']);
                $ns = substr($ns, $len, strlen($ns) - $len);
            }
            
            $path = $map['dir'] . strtolower($ns) .'/'. strtolower($class) .'.php';
            
            if (file_exists($path)) {
                require_once $path;
                break;
            }
        }
    }

    /**
     * Map a class to a location
     * <code>
     * \Forge\Autoload::map('user', '/var/www/html/folder/user_class.php');
     *
     * \Forge\Autoload::map(array(
     *    'user' => '/var/www/html/folder/user_class.php',
     *    'auth' => '/var/www/html/folder/auth_class.php'
     * ));
     * </code>
     * @param string|array $class Class to map, or array of classes
     * @param string $location
     */
    public static function map($class, $location = null) {
        if (is_array($class)) {
            foreach ($class as $key => $value) {
                self::map($key, $value);
            }
        } else {
            if (!empty($location)) {
                self::$maps[ltrim($class, '\\')] = $location;
            }
        }
    }

    /**
     * Map a directory to begin namespace/class scans
     * <code>
     * \Forge\Autoload::dir('/var/www/html/folder/');
     * </code>
     * @param string|array $dir Path to folder, or array of paths
     */
    public static function dir($dir = null, $namespace = '') {
        foreach ((array) $dir as $map) {
            if (empty($dir)) continue;
            self::$dirs[] = array('dir' => $map, 'ns' => $namespace);
        }
    }

    /**
     * Register this autoloader
     */
    public static function register()
    {
        spl_autoload_register(__NAMESPACE__ .'\Autoload::load');
    }

    /**
     * Disable this autoloader
     */
    public static function unregister()
    {
        spl_autoload_unregister(__NAMESPACE__ .'\Autoload::load');
    }
}
