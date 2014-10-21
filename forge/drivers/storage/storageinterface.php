<?php namespace Forge\Drivers\Storage;

interface StorageInterface {
    /**
     * Put startup logic here
     */
    public static function boot();
    
    /**
     * List available containers
     */
    public static function listContainers();
    
    /**
     * List contents of container
     * @param string $container Container name
     */
    public static function listContents($container);
    
    /**
     * Create a new container
     * @param string $container Container name
     */
    public static function createContainer($container);
    
    /**
     * Delete a container
     * @param string $container Container name
     */
    public static function deleteContainer($container);
    
    /**
     * Set default container
     * @param string $container Container name
     */
    public static function setContainer($container);
    
    /**
     * Return name of default container
     */
    public static function getContainer();
    
    /**
     * Check a container has the item
     * @param string $key Item name
     * @param string $container Container name
     */
    public static function has($key, $container);
    
    /**
     * Get an item from a container
     * @param string $key Item name
     * @param mixed $default Default value if no item
     * @param string $container Container name
     */
    public static function get($key, $default, $container);
    
    /**
     * Put an item into a container
     * @param string $key Item name
     * @param mixed $data Data to store
     * @param string $container Container name
     */
    public static function put($key, $data, $container);
    
    /**
     * Delete an item from a container
     * @param string $key Item name
     * @param string $container Container name
     */
    public static function delete($key, $container);
    
    /**
     * Upload a file
     * @param string $key Item name
     * @param string $file File path
     * @param string $container Container name
     */
    public static function upload($key, $file, $container);
    
    /**
     * Download a file
     * @param string $key Item name
     * @param string $container Container name
     */
    public static function download($key, $container);
    
    /**
     * Generate a URL for an item in a container
     * @param string $key Item name
     * @param string $container Container name
     */
    public static function generateUrl($key, $container);
}
