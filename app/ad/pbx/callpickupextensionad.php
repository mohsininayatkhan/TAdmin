<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class CallpickupExtensionAD {

    /**
     * @var \PDO PDO instance.
     */
    private static $pdo;

    public static $response;

    /**
     * @var array Database options.
     */
    private $dbOptions;

    private static function setPDO() {
        if (is_null(self::$pdo)) {
            $conn = Config::get('database.default');
            self::$pdo = DB::resolveConnection($conn)->getPdo();
        }
    }
	
    public static function delete($callpickup_id) {

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM tp_callpickupexten WHERE callpickup_id = :callpickup_id");
            $qry->execute(array($callpickup_id));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete call pickup extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }
}
