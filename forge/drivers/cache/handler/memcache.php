<?php namespace Forge\Drivers\Cache\Handler;

use \Forge\Drivers\Cache\CacheInterface;

class Memcache implements CacheInterface {
    private static $memcache;
    
    private static $prefix;
    
    public static function boot() {
        $host = \Forge\Config::get('cache.memcache.host');
        $port = \Forge\Config::get('cache.memcache.port');
        $prefix = \Forge\Config::get('cache.memcache.prefix');
        
        if (is_null($host) || is_null($port) || is_null($prefix)) throw new \Exception("Memcache cache - No host/port/prefix defined");
        
        self::$memcache = new \Memcache;
        self::$memcache->addServer($host, $port);
        
        self::$prefix = $prefix .'-';
        
        if (mt_rand(0, 100) > 80) self::gc();
        
        return true;
    }
    
    private static function gc() {
        self::getAll();
    }
    
    public static function has($key) {
        $query = self::get($key);
        return ! is_null($query);
    }
    
    public static function all() {
        $list = self::getAll();
        
        $output = array();
        
        foreach ($list as $key => $detail) {
            if (preg_match("~^". self::$prefix ."([^/]+)$~", $key)) {
                $output[] = array(
                    'key' => str_replace(self::$prefix, '', $key),
                    'expires_at' => $detail['detail'][1]
                );
            }
        }
        
        return $output;
    }
    
    public static function get($key, $default = null) {
        $data = self::$memcache->get(self::$prefix . $key);
        
        if ($data !== false) {
            $data = unserialize($data);
        } else {
            $data = is_callable($default) ? call_user_func($default) : $default;
        }
        
        return $data;
    }
    
    public static function put($key, $data, $mins = 0) {
        $minutes = $mins == 0 ? 0 : $mins * 60;
        
        self::$memcache->set(self::$prefix . $key, serialize($data), false, $minutes);
        
        return true;
    }
    
    public static function remember($key, \Closure $call, $mins = 0) {
        $data = static::get($key);
        
        if (is_null($data)) {
            $data = call_user_func($call);
            self::put($key, $data, $mins);
        }
        
        return $data;
    }
    
    public static function delete($key) {
        if (is_array($key)) {
            foreach($key as $entry) {
                self::delete($entry);
            }
        } else {
            if (strstr($key, '*')) {
                return self::deleteComplex($key);
            } else {
                return self::deleteSimple($key);
            }
        }
    }
    
    /**
     * Remove all cache entries
     * @return bool
    */
    public static function deleteAll() {
        $list = self::getAll();
        
        foreach ($list as $index => $cache) {
            self::deleteSimple($cache['key']);
        }
    }
    
    private static function deleteSimple($key) {
        self::$memcache->delete(self::$prefix . $key);
        return true;
    }
    
    private static function deleteComplex($key) {
        $list = self::getAll();
        
        foreach ($list as $k => $v) {
            $p = preg_replace('/\*/', '([^/]+)', $k);
            $pattern = "~^{$p}$~";
            
            if (preg_match($pattern, self::$prefix . $key)) {
                self::deleteSimple($key);
            }
        }
        
        return true;
    }

    private static function getAll() {
        $list = array();
        $allSlabs = self::$memcache->getExtendedStats('slabs');
        $items = self::$memcache->getExtendedStats('items');
        
        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs as $slabId => $slabMeta) {
                if (is_numeric($slabId) === false) continue;
                
                $cdump = self::$memcache->getExtendedStats('cachedump', (int)$slabId);
                foreach ($cdump as $server => $entries) {
                    if ($entries) {
                        foreach ($entries as $eName => $eData) {
                            if (time() > $eData[1]) {
                                self::$memcache->delete($eName);
                                continue;
                            }
                            
                            $list[$eName] = array(
                                 'key' => $eName,
                                 'server' => $server,
                                 'slabId' => $slabId,
                                 'detail' => $eData,
                                 'age' => $items[$server]['items'][$slabId]['age'],
                             );
                        }
                    }
                }
            }
        }
        
        ksort($list);
        
        return $list;
    }
}
