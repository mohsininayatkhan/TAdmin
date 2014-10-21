<?php

namespace Forge;

class Database {
    private static $capsule;

    public static function boot() {
        self::$capsule = new \Illuminate\Database\Capsule\Manager();
        
        foreach (Config::get('database.connections', array()) as $key => $conn) {
            self::$capsule->addConnection($conn, $key);
        }
        
        self::$capsule->bootEloquent();
        self::$capsule->setAsGlobal();

        \Illuminate\Database\Eloquent\Model::setEventDispatcher(Event::getDispatcher());
    }

    public static function logs($conn = null) {
        return self::$capsule->connection($conn)->getQueryLog();
    }

    public static function connection($conn = null) {
        return self::$capsule->connection($conn);
    }

    public static function __callStatic($method, $params) {
        $conn = Config::get('database.default');
        $db = self::connection($conn);

        return call_user_func_array(array($db, $method), $params);
    }
}
