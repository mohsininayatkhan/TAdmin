<?php namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class BlacklistAD
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
	
	public static function getAll($customer_id, $blacklist_id = '', $options = array()) {
        $response = array();
        try {
            self::setPDO();

            $param = array(':customer_id' => $customer_id);

            $sql = 'SELECT b.*,a.extennumber FROM `tp_blacklist` b LEFT JOIN `tp_account` a ON b.account_id=a.account_id';

            $where = ' WHERE b.customer_id = :customer_id';
			
			if ($blacklist_id != '') {
				$where .= ' AND b.blacklist_id = :blacklist_id';
				$param[':blacklist_id'] = $blacklist_id;
			}

            if (isset($options['keywords']) && $options['keywords'] != '') {
                $where .= ' AND lower(a.extennumber) like ' . self::$pdo->quote(strtolower($options['keywords']) . '%');
            }

            $sql .= $where;

            if (isset($options['start']) && isset($options['limit'])) {
                $sql .= ' LIMIT ' . $options['start'] . ', ' . $options['limit'];
            }
            
            //echo $sql;
			//print_r($param);
			//die;
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);

            $response['data'] = $qry->fetchAll();
            $response['status'] = 'SUCCESS';

            if (isset($options['total']) && $options['total']) {
                $response['total'] = self::getTotalRows('tp_blacklist', $where, $param);
            }

        } catch (\PDOException $e) {
            $response['status'] = 'ERROR';
            $response['total'] = 0;
            $response['message'] = sprintf('PDOException was thrown when trying to get black list: %s', $e -> getMessage());
            throw new \ADException(sprintf('PDOException was thrown when trying to get black list: %s', $e -> getMessage()), 0, $e);
        }
        return $response;
    }

    public static function getTotalRows($table, $where, $param) {

        $number_of_rows = 0;
        try {
            self::setPDO();
            $sql = "SELECT count(b.blacklist_id) FROM `tp_blacklist` b LEFT JOIN `tp_account` a ON b.account_id=a.account_id $where";
            $qry = self::$pdo -> prepare($sql);
            $qry -> execute($param);
            $number_of_rows = $qry -> fetchColumn();
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to get black list count: %s', $e -> getMessage()), 0, $e);
        }
        return $number_of_rows;
    }
	
	public static function create($data) {
        
        try {
            self::setPDO();
            
			$sql = 'INSERT INTO tp_blacklist (`customer_id`, `account_id`, `phonenumber`, `user_key`) 
				VALUES ( :customer_id, :account_id, :phonenumber, :user_key)';
			
            $values = array(
                ':customer_id' 	=> $data['customer_id'], 
                ':account_id'	=> $data['exten'], 
                ':phonenumber' 	=> $data['phonenumber'], 
                ':user_key' 	=> $data['userkey']
            );
            
            $qry = self::$pdo -> prepare($sql);
            $qry->execute($values);
			return true;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to add black list : %s', $e -> getMessage()), 0, $e);
            return false;
        }
        return false;
    }
	
	public static function update($data) {
        
        try {
            self::setPDO();
			
			 $values = array(
                $data['exten'], 
                $data['phonenumber'], 
                $data['userkey'],
				$data['blacklist_id'],
				$data['customer_id']
            );
			
			$sql = 'UPDATE tp_blacklist SET `account_id`=?, `phonenumber`=?, `user_key`=? WHERE `blacklist_id`=? AND `customer_id`=?';
			
            $qry = self::$pdo->prepare($sql);
            $qry -> execute($values);
            return true;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to update black list : %s', $e -> getMessage()), 0, $e);
            return false;
        }
    }
	
	public static function delete($blacklist_id, $customer_id) {

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM tp_blacklist WHERE blacklist_id = :blacklist_id AND customer_id = :customer_id");
            $qry->execute(array($blacklist_id, $customer_id));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete black list : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }
}
