<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class NumberAD {

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

    public static function alreadyExists($customer_id, $number, $existing_id = '') {

        try {
            self::setPDO();

            $param = array(':number' => $number, ':customer_id' => $customer_id);

            $sql = 'SELECT * FROM tp_numberinfo ';

            $where = 'WHERE number_info = :number AND customer_id = :customer_id';

            if ($existing_id != '') {
                $where .= ' AND table_id <> :existing_id';
                $param[':existing_id'] = $existing_id;
            }

            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);

            $response['data'] = $qry->fetchAll();

            if (count($response['data'])) {
                return false;
            } else {
                return true;
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to check pbx number iformation: %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function update($customer_id, $number, $app, $table_id, $user_key = '') {

        self::$response = array();

        try {
            self::setPDO();

            $values = array();

            $values['number_info'] = $number;

            $sql = 'UPDATE tp_numberinfo SET number_info=?';

            if ($user_key != '') {
                $sql .= ', user_key=?';
                $values['user_key'] = $user_key;
            }

            $values['customer_id'] = $customer_id;
            $values['table_id'] = $table_id;
            $values['app'] = $app;

            $sql .= ' WHERE customer_id=? AND table_id=? AND app=?';

            $qry = self::$pdo->prepare($sql);

            $qry->execute($values);
            self::$response['status'] = 'SUCCESS';
            return true;
        } catch (\PDOException $e) {
            self::$response['status'] = 'ERROR';
            self::$response['message'] = sprintf('PDOException was thrown when trying to update number information : %s', $e->getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to update number information : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function delete($customer_id, $table_id, $app) {

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM tp_numberinfo WHERE customer_id = :customer_id AND table_id = :table_id AND app = :app ");
            $qry->execute(array($customer_id, $table_id, $app));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete number information : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }
}
