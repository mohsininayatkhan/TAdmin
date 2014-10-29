<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class MusiconholdAD {

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

	public static function getAll($customer_id, $musiconhold_id='', $options=array()) {
	
		$response = array();
		try 
		{
			self::setPDO();
			$sql =  'SELECT * FROM as_musiconhold';
			$where = ' WHERE name not in (\'default\',\'custom\') AND customer_id=:customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
			if ($musiconhold_id!='') {
				$where .= ' AND musiconhold_id = :musiconhold_id';
				$param[':musiconhold_id'] = $musiconhold_id;
			}
			
			if (isset($options['keywords']) && $options['keywords']!='') {
				$where .= 	' AND (lower(name) like '.self::$pdo->quote(strtolower($options['keywords']).'% )');
			}
			
			$sql .= $where;
			
			$sql .= ' order by musiconhold_id DESC';
			
			if (isset($options['start']) && isset($options['limit'])) {
				$sql .= ' LIMIT '.$options['start'].', '.$options['limit'];
			}
			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);
			
			$response['data'] 		= $qry->fetchAll();
			$response['status'] 	= 'SUCCESS';
			
			if (isset($options['total']) && $options['total']) {
				$response['total'] = self::getTotalRows('as_musiconhold', $where, $param);
			}

		} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown while getting MOH : %s', $e->getMessage());
			//return $response;
			throw new \ADException(sprintf('PDOException was thrown while getting MOH : %s', $e->getMessage()), 0, $e);
		}
		return $response;
	}
	
	public static function getTotalRows($table, $where, $param) {
		
		$number_of_rows = 0;
		try 
		{
	        self::setPDO();
			$sql = "SELECT count(musiconhold_id) FROM $table $where";
			$qry = self::$pdo->prepare($sql); 
			$qry->execute($param); 
			$number_of_rows = $qry->fetchColumn(); 
		}catch (\PDOException $e) {
         	throw new \ADException(sprintf('PDOException was thrown when trying to get music on hold count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}

	public static function create($data) {
		try {
			self::setPDO();
			
			$sql = 'INSERT INTO as_musiconhold (customer_id, name, directory) VALUES (?, ?, ?)';

            $values = array(
				$data['customer_id'], 
                $data['name'], 
                $data['directory']
            );
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			
			$qry = self::$pdo->prepare('SELECT LAST_INSERT_ID()');
			$qry->execute();
			$last_id = $qry->fetch();
			
			if (!isset($last_id[0]) || !$last_id[0]) {
				return false;
			}
			
			return $last_id[0];
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown while creating new MOH group : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function delete($data) {
        try {
            self::setPDO();
			
			$sql = 'DELETE FROM as_musiconhold WHERE customer_id=? AND musiconhold_id=?';
			
			$values = array(
				$data['customer_id'],
				$data['musiconhold_id']
			);
			
            $qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			// return $qry->rowCount();

            return true;
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown while deleting MOH group : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
    }
}
