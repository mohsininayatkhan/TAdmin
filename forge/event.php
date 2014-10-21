<?php namespace Forge;

/**
 * Wrapper for the Illuminate\Events classes
 * @author Ashley Wilson
 */
class Event
{
   /**
    * Events Instance
    */
   private static $container;
   
   /**
    * Subscribe to an event
    * <code>
    * Event::listen('error', function($event){
    *    // Do something here..
    * });
    * </code>
    * @param string $key Key to listen for
    * @param \Closure $call Callback
    * @param int $priority Listeners with a higher priority will get called first
    */
   public static function listen($key, $call, $priority = 0)
   {
      return self::$container->listen($key, $call, $priority);
   }
   
   /**
    * Publish an event
    * <code>
    * Event::fire('error', array('key1'=>'value'));
    * </code>
    * @param string $key Key to fire
    * @param array $args Key => value array that will be incorporated into the event
    * @return mixed
    */
   public static function fire($key, $args = array())
   {
      return self::$container->fire($key, $args);
   }
   
   /**
    * Create our event instance
    */
   public static function boot()
   {
      self::$container = new \Illuminate\Events\Dispatcher();
   }
   
   public static function getDispatcher() {
      return self::$container;
   }
}
