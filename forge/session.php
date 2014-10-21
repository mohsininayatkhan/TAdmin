<?php namespace Forge;

class Session
{
    /**
    *  stat flag
    */
    protected static $started = false;

    public static function started()
    {
        return static::$started;
    }

    public static function load()
    {
       if (static::$started) return;
       session_name(Config::get('session.name'));
       session_set_cookie_params(Config::get('session.lifetime'), '/', Config::get('session.domain'));
       session_regenerate_id(true);

       switch(Config::get('session.type')){
           case 'database':
                $handler = new \Forge\Drivers\Session\PdoHandler(\Illuminate\Database\Eloquent\Model::resolveConnection(Config::get('database.default'))->getPdo(),
                                                                array('db_table' => Config::get('session.table')));
               session_set_save_handler(array($handler, 'open'),
                                        array($handler, 'close'),
                                        array($handler, 'read'),
                                        array($handler, 'write'),
                                        array($handler, 'destroy'),
                                        array($handler, 'gc'));
               break;
       }
       session_start();
       //this is a work around for a bug in the session_set_cookie_params
       //the lifetime is only set the first time and the session will time out
       //after the set time no matter how many time the session is set.
       //documented on the php.net web site so you manual set the cookie after the session is started
       setcookie(session_name(),session_id(),time()+Config::get('session.lifetime'));
       static::$started = true;
    }

    public static function set($key, $value = null)
    {
       if (is_array($key))
       {
          foreach ($key as $k => $v)
          {
             self::set($k, $v);
          }
       } else {
          $_SESSION[$key] = $value;
       }
    }

    public static function merge(array $data = array())
    {
      foreach ($data as $key => &$value)
      {
         if (is_array($value) && isset($_SESSION[$key]) && is_array($_SESSION[$key]))
         {
            $_SESSION[$key] = self::merge($_SESSION[$key], $value);
         } else {
            $_SESSION[$key] = $value;
         }
      }
    }

    public static function get($key = '', $default = null)
    {
       return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    public static function has($key = '')
    {
       return array_key_exists($key, $_SESSION);
    }

    public static function destroy()
    {
        session_destroy();
    }

}
