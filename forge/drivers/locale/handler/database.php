<?php namespace Forge\Drivers\Locale\Handler;

use \Forge\Drivers\Locale\LocaleInterface;
use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class Database implements LocaleInterface
{
   private static $pdo;
   
   public static function lookup($string, $locale)
   {
      if (is_null(self::$pdo))
      {
         $conn = Config::get('database.default');
         self::$pdo = DB::resolveConnection($conn)->getPdo();
      }
      
      try {
         $sql = "SELECT `string` FROM `locales` WHERE `locale` = :locale AND `original` = :original";
         
         $select = self::$pdo->prepare($sql);
         $select->bindParam(':locale', $locale, \PDO::PARAM_STR);
         $select->bindParam(':original', $string, \PDO::PARAM_STR);
         $select->execute();
         
         $rows = $select->fetchAll(\PDO::FETCH_NUM);
         
         if (count($rows) == 1)
         {
            return $rows[0][0];
         } else {
            $sql = "INSERT INTO `locales` (`locale`, `original`, `string`, `created_at`, `updated_at`) VALUES (:locale, :original, :string, now(), now())";
            
            $insert = self::$pdo->prepare($sql);
            $insert->bindParam(':locale', $locale, \PDO::PARAM_STR);
            $insert->bindParam(':original', $string, \PDO::PARAM_STR);
            $insert->bindParam(':string', $string, \PDO::PARAM_STR);
            $insert->execute();
            
            return $string;
         }
      } catch (\PDOException $e) {
         throw new \RuntimeException(sprintf('PDOException was thrown when trying to get locale data data: %s', $e->getMessage()), 0, $e);
      }
   }
}
