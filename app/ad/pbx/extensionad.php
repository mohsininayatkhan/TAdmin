<?php namespace AD\Pbx;
use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class ExtensionAd extends \Model {
   
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
   
   	public static function getAll($customer_id, $account_id='', $options=array()) {
	    
		$response = array();
		try 
		{
	        self::setPDO();
			
			$param = array(':customer_id' => $customer_id);
			 
			$sql =  'SELECT * FROM tp_account ';
			
			$where = 'WHERE customer_id = :customer_id';
			
			if ($account_id!='') {
				$where .= ' AND account_id = :account_id';
				$param[':account_id'] = $account_id;
			}
			
			if (isset($options['keywords']) && $options['keywords']!='') {
				$where .= ' AND lower(name) like '.self::$pdo->quote(strtolower($options['keywords']).'%');
			}
			
			$sql .= $where;
			
			if (isset($options['start']) && isset($options['limit'])) {
				$sql .= ' LIMIT '.$options['start'].', '.$options['limit'];
			}
			//echo $sql;
			//die;
			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);
			
			$response['data'] 		= $qry->fetchAll();
			$response['status'] 	= 'SUCCESS';
			
			if (isset($options['total']) && $options['total']) {
				$response['total'] = self::getTotalRows('tp_account', $where, $param);
			}

      	} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get extensions: %s', $e->getMessage());
			//return $response;
         	throw new \RuntimeException(sprintf('PDOException was thrown when trying to get extensions: %s', $e->getMessage()), 0, $e);
      	}
		return $response;
   	}
	
	public static function getTotalRows($table, $where, $param) {
		
		$number_of_rows = 0;
		
		try 
		{
	        self::setPDO();
			$sql = "SELECT count(account_id) FROM $table $where";
			$qry = self::$pdo->prepare($sql); 
			$qry->execute($param); 
			$number_of_rows = $qry->fetchColumn(); 
		}catch (\PDOException $e) {
         	throw new \RuntimeException(sprintf('PDOException was thrown when trying to get count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}
}