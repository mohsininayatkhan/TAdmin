<?php namespace Forge\Drivers\Auth\Handler;

use \Forge\Session;
use \Forge\Config;
use \Forge\Drivers\Auth\AuthInterface;
use \Illuminate\Database\Eloquent\Model as DB;

class Database implements AuthInterface
{
   /*
    * database access object
    */
   private static $pdo;

   /**
    * holds the user name
    */
   private static $user;

   /**
    * user table name
    */
   private static $table;

   /**
    * column name of the id
    */
   private static $column; 


   private static function setPDO()
   {
      if (is_null(self::$pdo))
      {
         $conn = Config::get('database.default');
         self::$pdo = DB::resolveConnection($conn)->getPdo();
      }
      // self::$table = Config::get('auth.table', 'users');
	  // self::$column = Config::get('auth.id_column', 'id');
      self::$table = Config::get('auth.table', 'blf_livestatus');
      self::$column = Config::get('auth.id_column', 'blf_id');
   }

   public static function user()
   {
      return self::$user;
   }
 

   public static function attempt(array $credentials = array())
   {
      if ( is_null($credentials['username']) || is_null($credentials['password']) )
      {
         return false;
      }

      try {
         self::setPDO();
         // $sql = "SELECT * FROM `". self::$table ."` WHERE `username` = :username AND `password` = :password;";
         $sql = "SELECT 
					`blf_id`,
					`server_id` ,
					`account_id`,
					`customer_id`,
					`extension`,
					`name`,
					0 as `guigroup`,
					concat('SIP/',`exten_fullname`) as `iface`,
					accountcode,
					`f_status`
				FROM  `". self::$table ."`
				WHERE `guiuser`=:username
				AND `guisecret`=:password";

         $select = self::$pdo->prepare($sql);
         $select->bindParam(':username', $credentials['username'], \PDO::PARAM_STR);
         $select->bindParam(':password', $credentials['password'], \PDO::PARAM_STR);
         $select->execute();

         $user = $select->fetchAll(\PDO::FETCH_ASSOC);

      } catch (\PDOException $e) {
         throw new \RuntimeException(sprintf('PDOException was thrown when trying to get User data: %s', $e->getMessage()), 0, $e);
      } 
      if (empty($user)){
         self::$user = null;
         return false;
      }

      self::$user = $user[0];
      Session::set(array('forge-user-id' => self::$user[self::$column]));

      return true;
   }

   public static function check()
   {
      if (!Session::get('forge-user-id')) return false;
      try {
         self::setPDO();
         $userId = Session::get('forge-user-id');
         $sql = "SELECT * FROM `". self::$table ."` WHERE `". self::$column ."` = :user_id";
         $select = self::$pdo->prepare($sql);
         $select->bindParam(':user_id', $userId, \PDO::PARAM_STR);
         $select->execute();

         $user = $select->fetchObject();
         self::$user = $user;

      } catch (\PDOException $e) {
         throw new \RuntimeException(sprintf('PDOException was thrown when trying to get User data: %s', $e->getMessage()), 0, $e);
      }

      return true;

   }

    public static function logout() {
        Session::destroy();
    }
}
