<?php namespace Forge;

/**
 * The Assets class lets you easily buld the listof assest that you page may require
 * sutch as css and javascript files
 *
 * @Author John Tipping <john.tipping@supanet.net.uk>
 */
class Asset {
   /**
    * a container to hold the css and js assets
    *
    * contains arrays js and css
    */
    private static $container = array('js' => array(), 'css' => array());
    
    public static function add($key, $asset = null) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::set($k, $v);
            }
        } else {
            if (preg_match("~^.*\.js$~", $asset)) {
                self::$container['js'][$key] = $asset;
            }

            if (preg_match("~^.*\.css$~", $asset))
            {
                self::$container['css'][$key] = $asset;
            }
        }
    }

    /**
     *  returns the script or style identified by it key
     *  ready for use
     */

    public static function get($key = '', $default = null) {
        if (array_key_exists($key, self::$container['js'])){
            return PHP_EOL . self::wrapScript(self::$container['js'][$key]) . PHP_EOL;
        }
        
        if (array_key_exists($key, self::$container['css'])){
            return PHP_EOL . self::wrapScript(self::$container['css'][$key]) .PHP_EOL;
        }

        return '';
    }

    public static function has($key = '') {
        return array_key_exists($key, self::$container);
    }

    /**
     *  return the javascripts ready for output
     */
    public static function scripts() {
        $output = null;
        
        foreach(self::$container['js'] as $key => $asset) {
            $output[] = self::wrapScript($asset);
        }
        
        return PHP_EOL. implode(PHP_EOL, $output) .PHP_EOL;
    }

    /**
     * return the style sheets formatted ready for output
     */
    public static function styles() {
        $output = array();
        
        foreach(self::$container['css'] as $key => $asset) {
            $output[] = self::wrapStyle($asset);
        }
        
        return PHP_EOL. implode(PHP_EOL, $output) .PHP_EOL;
    }

    /**
     * returns all the assets bot js and css formatted
     * ready for use
     */
    public static function all() {
        return PHP_EOL .self::styles() . self::scripts();
    }

    private static function addServerName($url) {
        $output = null;
        if (preg_match("~http://|https://(.*)?$~i", $url)) {
            return $url;
        }
        
        if (Server::has('HTTPS')) {
            $output = "https://";
        } else {
            $output = "http://";
        }

        return $output . Server::get('HTTP_HOST') . $url;
    }

    private static function wrapStyle($style) {
        return "<link type='text/css' rel='stylesheet' href='". self::addServerName($style) ."' />";
    }

    private static function wrapScript($script) {
        return "<script type='text/javascript' src='". self::addServerName($script) ."'></script>";
    }
}
