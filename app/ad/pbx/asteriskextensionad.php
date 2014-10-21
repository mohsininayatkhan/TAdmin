<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class AsteriskExtensionAD {

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
	
	public static function create($customer_id, $exten, $app, $appdata) {

        self::$response = array();

        try {
            self::setPDO();
            $qry = self::$pdo->prepare('INSERT INTO as_extensions (customer_id, context, exten, priority, app, appdata) 
								VALUES( :customer_id, :context, :exten, :priority, :app, :appdata)');

            $values = array(':customer_id' => $customer_id, ':context' => 'default', ':exten' => $exten, ':priority' => '1', ':app' => $app, ':appdata' => $appdata);

            $qry->execute($values);
            $id = self::$pdo->lastInsertId();
            self::$response['status'] = 'SUCCESS';
            return true;
        } catch (\PDOException $e) {
            self::$response['status'] = 'ERROR';
            self::$response['message'] = sprintf('PDOException was thrown when trying to add asterisk extension : %s', $e->getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to add asterisk extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function delete($customer_id, $extenm) {

        self::$response = array();

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM as_extensions WHERE customer_id = :customer_id AND exten = :exten");
            $qry->execute(array($customer_id, $extenm));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            $self::$response['status'] = 'ERROR';
            $self::$response['message'] = sprintf('PDOException was thrown when trying to delete asterisk extension : %s', $e->getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete asterisk extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }
}
