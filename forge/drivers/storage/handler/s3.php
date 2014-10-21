<?php namespace Forge\Drivers\Storage\Handler;

use \Forge\Drivers\Storage\StorageInterface;
use \Forge\Config;
use \Forge\Core;
use \Forge\File;

/**
 * Wrapper for Amazon S3 class
 * @author Ashley Wilson
 * @see http://ceph.com/docs/master/radosgw/s3/php/
 */
class S3 implements StorageInterface {
    private static $defaultContainer;
    
    private static $instance;
    
    public static function boot() {
        $settings = 'storage.settings.s3.';
        
        self::$instance = new \AmazonS3(array(
            'key'    => Config::get($settings .'key'),
            'secret' => Config::get($settings .'secret'),
            'canonical_id' => Config::get($settings .'canonical_id'),
            'canonical_name' => Config::get($settings .'canonical_name')
        ));
        
        if (Config::get($settings .'host')) self::$instance->set_hostname(Config::get($settings .'host'));
        
        self::$instance->allow_hostname_override(false);
        
        if (Config::get($settings .'enable_path_style')) self::$instance->enable_path_style();
        if (!Config::get($settings .'use_ssl')) self::$instance->disable_ssl();
        
        self::$defaultContainer = Config::get($settings .'default_container');
    }
    
    public static function listContainers() {
        $list = self::$instance->list_buckets();
        $buckets = object_get($list, 'body.Buckets.Bucket');
        
        $output = array();
        
        foreach ($buckets as $bucket) {
            $output[] = array(
                'name' => (string) $bucket->Name,
                'created_at' => (string) $bucket->CreationDate
            );
        }
        
        return $output;
    }
    
    public static function listContents($container = null) {
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        $list = self::$instance->list_objects($container);
        $objects = object_get($list, 'body.Contents', array());
        
        $output = array();
        
        foreach ($objects as $object) {
            $output[] = array(
                'name' => (string) $object->Key,
                'size' => (string) $object->Size,
                'modified_at' => (string) $object->LastModified
            );
        }
        
        return $output;
    }
    
    public static function createContainer($container) {
        self::$instance->create_bucket($container, \AmazonS3::REGION_US_E1);
        
        return true;
    }
    
    public static function deleteContainer($container) {
        // Force delete
        self::$instance->delete_all_objects($container);
        $res = self::$instance->delete_bucket($container, 1);
        
        if (object_get($res, 'status', '500') != '200') {
            throw new \Exception("Error deleting bucket [". object_get($res, 'body.Code', 'Unknown') ."]");
        }
        
        return true;
    }
    
    public static function setContainer($container) {
        self::$defaultContainer = $container;
    }
    
    public static function getContainer() {
        return self::$defaultContainer;
    }
    
    public static function has($key, $container = null) {
        $exists = false;
        
        try {
            self::get($key, false, $container);
            $exists = true;
        } catch (\Exception $e) {
            // Does not exist
        }
        
        return $exists;
    }
    
    public static function get($key, $default = null, $container = null) {
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        $object = self::$instance->get_object($container, $key);
        
        if (object_get($object, 'status', '500') != '200') {
            throw new \Exception("Error getting item [". object_get($object, 'body', 'Unknown') ."]");
        }
        
        $content = object_get($object, 'body');
        
        if (is_null($content)) {
            $output = is_callable($default) ? call_user_func($default) : $default;
        } else {
            $output = unserialize($content);
        }
        
        return $output;
    }
    
    public static function put($key, $data, $container = null) {
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        $data = serialize($data);
        
        $res = self::$instance->create_object($container, $key, array('body' => $data));
        
        if (object_get($res, 'status', '500') != '200') {
            throw new \Exception("Error putting item [". object_get($res, 'body.Code', 'Unknown') ."]");
        }
        
        return true;
    }
    
    public static function delete($key, $container = null) {
        if (strpos($key, '@') !== false) {
            list($key, $container) = explode('@', $key, 2);
        }
        
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        self::$instance->delete_object($container, $key);
        
        return true;
    }
    
    public static function upload($key, $file, $container = null) {
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        $res = self::$instance->create_object($container, $key, array('body' => File::encode($file)));
        
        if (object_get($res, 'status', '500') != '200') {
            throw new \Exception("Error uploading file [". object_get($res, 'body.Code', 'Unknown') ."]");
        }
        
        return $key .'@'. $container;
    }
    
    public static function download($key, $path = null, $container = null) {
        if (strpos($key, '@') !== false) {
            list($key, $container) = explode('@', $key, 2);
        }
        
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        if (is_null($path)) $path = Core::path('storage') .'downloads/'. $key;
        
        if (is_writable(Core::path('storage') .'downloads/') !== true) {
            throw new \Exception("Cannot download to path [{$path}]");
        }
        
        $object = self::$instance->get_object($container, $key);
        
        if (object_get($object, 'status', '500') != '200') {
            throw new \Exception("Error downloading item [". object_get($object, 'body.Code', 'Unknown') ."]");
        }
        
        $content = object_get($object, 'body');
        
        File::decode($path, $content);
        
        return $path;
    }
    
    public static function generateUrl($key, $container = null) {
        $container = is_null($container) ? self::$defaultContainer : $container;
        
        return self::$instance->get_object_url($container, $key);
    }
}
