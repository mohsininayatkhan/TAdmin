<?php namespace AD\Pbx;
use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;


class QueueAd extends \Model {

   /**
     * @var \PDO PDO instance.
     */
    private static $pdo;
	
	private static function setPDO()
    {
      if (is_null(self::$pdo))
      {
         $conn = Config::get('database.default');
         self::$pdo = DB::resolveConnection($conn)->getPdo();
      }
   	}
   
   	public static function getAll($customer_id, $queue_id='', $options=array()) {
	    
		$response = array();
		
		try {
	        self::setPDO();
			
			$param = array(':customer_id' => $customer_id);
			
			$sql = "select tt.*, 
					cfg.description as slf_desc, cfg.start_announcement_no as slf_start, cfg.end_announcement_no as slf_end, cfg.validation as slf_valid, cfg.active as slf_stat, cfg.queue_name as slf_name
					FROM
					(SELECT tp_queue.*, 
						IF(tp_queue.queue_youarenext = '', '', (select urnext.announcement_number from tp_announcement as urnext where urnext.customer_id = $customer_id and concat(urnext.path, urnext.file) = tp_queue.queue_thankyou limit 1)) as thankyou,
						IF(tp_queue.queue_youarenext = '', '', (select urnext.announcement_number from tp_announcement as urnext where urnext.customer_id = $customer_id and concat(urnext.path, urnext.file) = tp_queue.welcome_announce limit 1)) as agent_announce
						FROM tp_queue";
			$where = " WHERE tp_queue.customer_id = :customer_id";
						
			if($queue_id){
				$where .= " AND tp_queue.queue_id = :queue_id";
				$param[':queue_id'] = $queue_id;
			}
			
			if (isset($options['start']) && isset($options['limit'])) {
				$where .= ' LIMIT '.$options['start'].', '.$options['limit'];
			}
			
			$sql .= $where;
			
			$sql .= ') as tt
				   LEFT JOIN tp_servicelevel_config cfg
				   on tt.name = cfg.queue_name
				   and cfg.customer_id = tt.customer_id';
				   
			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);
			
			$response['data'] 		= $qry->fetchAll();
			$response['status'] 	= 'SUCCESS';

			if (isset($options['total']) && $options['total']) {
				$response['total'] = self::getTotalRows($where, $param, $customer_id);
			}

      	} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get queue: %s', $e->getMessage());
			//return $response;
         	throw new \RuntimeException(sprintf('PDOException was thrown when trying to get queue: %s', $e->getMessage()), 0, $e);
      	}
		return $response;
   	}
	
	public static function getTotalRows($where, $param, $customer_id) {
		
		$number_of_rows = 0;
		try {
	        self::setPDO();
			$sql = "select count(tt.queue_id) FROM
					(SELECT tp_queue.*, 
						IF(tp_queue.queue_youarenext = '', '', (select urnext.announcement_number from tp_announcement as urnext where urnext.customer_id = $customer_id and concat(urnext.path, urnext.file) = tp_queue.queue_thankyou limit 1)) as thankyou,
						IF(tp_queue.queue_youarenext = '', '', (select urnext.announcement_number from tp_announcement as urnext where urnext.customer_id = $customer_id and concat(urnext.path, urnext.file) = tp_queue.welcome_announce limit 1)) as agent_announce
						FROM tp_queue $where) as tt
				   LEFT JOIN tp_servicelevel_config cfg
				   on tt.name = cfg.queue_name
				   and cfg.customer_id = tt.customer_id";
			$qry = self::$pdo->prepare($sql); 
			$qry->execute($param); 
			$number_of_rows = $qry->fetchColumn(); 
			
		} catch (\PDOException $e) {
         	throw new \RuntimeException(sprintf('PDOException was thrown when trying to get queue count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}
}