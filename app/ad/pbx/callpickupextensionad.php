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
	
	
	public static function getAll($callpickup_id, $callpickupexten_id ='', $options = array()) {
	
        $response = array();
        try {
            self::setPDO();

            $param = array(':callpickup_id' => $callpickup_id);

            $sql = 'SELECT * FROM tp_callpickupexten ';

            $where = 'WHERE callpickup_id = :callpickup_id';

            if ($callpickupexten_id != '') {
                $where .= ' AND callpickupexten_id = :callpickupexten_id';
                $param[':callpickupexten_id'] = $callpickupexten_id;
            }

            if (isset($options['keywords']) && $options['keywords'] != '') {
                $where .= ' AND lower(exten) like ' . self::$pdo->quote(strtolower($options['keywords']) . '%');
            }
			
			$sql .= $where;

            if (isset($options['start']) && isset($options['limit'])) {
                $sql .= ' LIMIT ' . $options['start'] . ', ' . $options['limit'];
            }            
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);

            $response['data'] = $qry->fetchAll();
            $response['status'] = 'SUCCESS';

            if (isset($options['total']) && $options['total']) {
                $response['total'] = self::getTotalRows('tp_callpickupexten', $where, $param);
            }

        } catch (\PDOException $e) {
            $response['status'] = 'ERROR';
            $response['total'] = 0;
            $response['message'] = sprintf('PDOException was thrown when trying to get call group: %s', $e -> getMessage());
            throw new \ADException(sprintf('PDOException was thrown when trying to get call grouup: %s', $e -> getMessage()), 0, $e);
        }
        return $response;
    }

    public static function getTotalRows($table, $where, $param) {

        $number_of_rows = 0;
        try {
            self::setPDO();
            $sql = "SELECT count(callpickupexten_id) FROM $table $where";
            $qry = self::$pdo -> prepare($sql);
            $qry -> execute($param);
            $number_of_rows = $qry -> fetchColumn();
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to get call group count: %s', $e -> getMessage()), 0, $e);
        }
        return $number_of_rows;
    }
	
	
	 public static function create($data) {
        
        try {
           
            self::setPDO();
            
			$sql = 'INSERT INTO tp_callpickupexten (`callpickup_id`, `exten_type`, `exten`, `user_key`, `create_dttm`) 
				VALUES ( :callpickup_id, :exten_type, :exten, :user_key, :create_dttm)';
			
            $values = array(
                ':callpickup_id' => $data['callpickup_id'], 
                ':exten_type'	 => $data['exten_type'], 
                ':exten' 		 => $data['exten'], 
                ':user_key' 	 => $data['userkey'],
				':create_dttm'   => 'NOW()'
            );
            
            $qry = self::$pdo -> prepare($sql);
            $qry->execute($values);
			return true;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to add call pickup extension : %s', $e -> getMessage()), 0, $e);
            return false;
        }
        return false;
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
