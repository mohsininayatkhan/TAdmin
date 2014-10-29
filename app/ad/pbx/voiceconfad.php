<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class VoiceconfAD {

    /**
     * @var \PDO PDO instance.
     */
    private static $pdo;

    public static $response;

    private static function setPDO() {
        if (is_null(self::$pdo)) {
            $conn = Config::get('database.default');
            self::$pdo = DB::resolveConnection($conn)->getPdo();
        }
    } 

	public static function getAll($customer_id, $meetme_id='', $options=array()) {
	
		$response = array();
		try 
		{
			self::setPDO();
			$sql = 'SELECT * FROM as_meetme';
			$where = ' WHERE customer_id=:customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
			if ($meetme_id) {
				$where .= ' AND meetme_id = :meetme_id';
				$param[':meetme_id'] = $meetme_id;
			}
			
			// if (isset($options['keywords']) && $options['keywords']!='') {
				// $where .= 	' AND (lower(ivr_number) like '.self::$pdo->quote(strtolower($options['keywords']).'% )');
			// }
			
			$sql .= $where;
			
			if (isset($options['start']) && isset($options['limit'])) {
				$sql .= ' LIMIT '.$options['start'].', '.$options['limit'];
			}
			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);
			
			$response['data'] 		= $qry->fetchAll();
			$response['status'] 	= 'SUCCESS';
			
			if (isset($options['total']) && $options['total']) {
				$response['total'] = self::getTotalRows('tp_phonemodel', $where, $param);
			}

		} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get ivr: %s', $e->getMessage());
			//return $response;
			throw new \RuntimeException(sprintf('PDOException was thrown when trying to get ivr: %s', $e->getMessage()), 0, $e);
		}
		return $response;
	}
	
	public static function getTotalRows($table, $where, $param) {
		
		$number_of_rows = 0;
		try 
		{
	        self::setPDO();
			$sql = "SELECT * FROM as_meetme $where";
			$qry = self::$pdo->prepare($sql); 
			$qry->execute($param); 
			$number_of_rows = $qry->fetchColumn(); 
		}catch (\PDOException $e) {
         	throw new \RuntimeException(sprintf('PDOException was thrown when trying to get ivr count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}
}
