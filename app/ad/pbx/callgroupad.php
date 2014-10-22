<?php namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class CallgroupAD
{
	/**
     * @var \PDO PDO instance.
     */
    private static $pdo;

    /**
     * @var array Database options.
     */
    private $dbOptions;
	
	private static function setPDO()
    {
      if (is_null(self::$pdo))
      {
         $conn = Config::get('database.default');
         self::$pdo = DB::resolveConnection($conn)->getPdo();
      }
   	}
	
	public static function getAll($customer_id, $callpickup_id = '', $options = array()) {
        $response = array();
        try {
            self::setPDO();

            $param = array(':customer_id' => $customer_id);

            $sql = 'SELECT * FROM tp_callpickup ';

            $where = 'WHERE customer_id = :customer_id';

            if ($callpickup_id != '') {
                $where .= ' AND callpickup_id = :callpickup_id';
                $param[':callpickup_id'] = $callpickup_id;
            }

            if (isset($options['keywords']) && $options['keywords'] != '') {
                $where .= ' AND (lower(name) like ' . self::$pdo->quote(strtolower($options['keywords']) . '%') . 'OR callpickup_code like ' . self::$pdo->quote(strtolower($options['keywords']) . '%') . ')';
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
                $response['total'] = self::getTotalRows('tp_callpickup', $where, $param);
            }

        } catch (\PDOException $e) {
            $response['status'] = 'ERROR';
            $response['total'] = 0;
            $response['message'] = sprintf('PDOException was thrown when trying to get call group: %s', $e -> getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to get call grouup: %s', $e -> getMessage()), 0, $e);
        }
        return $response;
    }

    public static function getTotalRows($table, $where, $param) {

        $number_of_rows = 0;
        try {
            self::setPDO();
            $sql = "SELECT count(callpickup_id) FROM $table $where";
            $qry = self::$pdo -> prepare($sql);
            $qry -> execute($param);
            $number_of_rows = $qry -> fetchColumn();
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to get call group count: %s', $e -> getMessage()), 0, $e);
        }
        return $number_of_rows;
    }
	
	 public static function update($data) {

        try {
            self::setPDO();

            $qry = self::$pdo -> prepare('UPDATE tp_callpickup SET callpickup_code=?, name=?, dsc=? WHERE callpickup_id=? AND customer_id=?');

            $values = array(
                $data['code'], 
                $data['name'], 
                $data['description'], 
                $data['callpickup_id'], 
                $data['customer_id']
            );
            $qry -> execute($values);
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to update call group : %s', $e -> getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function create($data) {
        
        try {
           
            self::setPDO();
            $sql = 'CALL sp_tpadinbox_callgroup(:customerid, :code, :name, :description, :userkey)';

            $values = array(
                ':customerid' => $data['customer_id'], 
                ':code' => $data['code'], 
                ':name' => $data['name'], 
                ':description' => $data['description'], 
                'userkey' => 'userkey'
            );
            
            $qry = self::$pdo -> prepare($sql);
            $qry -> execute($values);
            $response = $qry -> fetch();
            return $response;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to add customer : %s', $e -> getMessage()), 0, $e);
            return false;
        }
        return false;
    }

}
