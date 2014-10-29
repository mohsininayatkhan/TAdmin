<?php namespace AD\Pbx;
use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;


class QueueAd {

   /**
     * @var \PDO PDO instance.
     */
    private static $pdo;
	
	public static $response;
	
	private static $module = 'QUEUE';
	
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
			
			$where .= ' order by tp_queue.queue_id DESC';
			
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
	
	public static function create($data) {
		try {
			self::setPDO();
			
			// IF Service level feedback is set, save it first
			if (isset($data['chk_service']) && $data['chk_service'] && !self::createslf($data)) {
				return false;
			}
			
			$name = $data['customer_id'].'*'.$data['exten'];
			
			$thankyou = '';
			if (isset($data['thankyou']) && $data['thankyou']) {
				$qry = self::$pdo->prepare('SELECT `file`,`path` FROM tp_announcement WHERE customer_id=? and announcement_number=?');
				$values = array(
					$data['customer_id'], 
					$data['thankyou']
				);
				
				$qry->execute($values);
				$res = $qry->fetch();
				
				if ($res) {
					$thankyou = $res['path'].$res['file'];
				}
			}
			
			$announcement_file = '';
			if (isset($data['agent_announce']) && $data['agent_announce']) {
				$qry = self::$pdo->prepare('SELECT `file`,`path` FROM tp_announcement WHERE customer_id=? AND announcement_number=?');
				$values = array(
					$data['customer_id'], 
					$data['agent_announce']
				);
				
				$qry->execute($values);
				$res = $qry->fetch();
				
				if ($res) {
					$announcement_file = $res['path'].$res['file'];
				}
			}
			
			$sql = 'INSERT INTO tp_queue 
					(name, customer_id, musiconhold, exten, welcome_announce, announce, context, dsc, cli_name_prefix, timeout, monitor_format, queue_thankyou, queue_thereare, queue_callswaiting, queue_youarenext, announce_frequency, retry, strategy, weight, maxwait, failover_app, failover_appnumber, user_key, create_dttm) 
					VALUES (?, ?, ?, ?, ?, ?, \'default\', ?, ?, ?, \'wav\', ?, NULL, NULL, NULL, ?, ?, ?, ?, ?, ?, ?, ?, now())';

            $values = array(
				$name,
				$data['customer_id'],
				$data['musiconhold'],
				$data['exten'],
				$data['welcome_announce'],
				$announcement_file,
				$data['dsc'],
				$data['cli_name_prefix'],
				$data['timeout'],
				$thankyou,
				$data['announce_frequency'],
				$data['retry'],
				$data['strategy'],
				$data['weight'],
				$data['maxwait'],
				$data['failover_app'],
				$data['failover_appnumber'],
				$data['user_key']
            );
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			
			$qry = self::$pdo->prepare('SELECT LAST_INSERT_ID()');
			$qry->execute();
			$last_id = $qry->fetch();
			
			if (!isset($last_id[0]) || !$last_id[0]) {
				return false;
			}
			
			return \AD\Pbx\RigngroupAD::add_numberinfo($data['customer_id'], $data['exten'], self::$module, $last_id[0], $data['user_key']);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to add queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function update($data) {
		try {
			self::setPDO();
			
			$name = $data['customer_id'].'*'.$data['exten'];
			
			// IF Service level feedback is set, save it first
			if (isset($data['chk_service']) && $data['chk_service']) {
				if (!self::createslf($data)) {
					return false;
				}
				
			// Delete SLF
			} elseif (!self::deleteslf($data['customer_id'], $name)) {
				return false;
			}
			
			$thankyou = '';
			if (isset($data['thankyou']) && $data['thankyou']) {
				$qry = self::$pdo->prepare('SELECT `file`,`path` FROM tp_announcement WHERE customer_id=? AND announcement_number=?');
				$values = array(
					$data['customer_id'], 
					$data['thankyou']
				);
				
				$qry->execute($values);
				$res = $qry->fetch();
				
				if ($res) {
					$thankyou = $res['path'].$res['file'];
				}
			}
			
			$announcement_file = '';
			if (isset($data['agent_announce']) && $data['agent_announce']) {
				$qry = self::$pdo->prepare('SELECT `file`,`path` FROM tp_announcement WHERE customer_id=? AND announcement_number=?');
				$values = array(
					$data['customer_id'], 
					$data['agent_announce']
				);
				
				$qry->execute($values);
				$res = $qry->fetch();
				
				if ($res) {
					$announcement_file = $res['path'].$res['file'];
				}
			}
			
			$sql = 'UPDATE tp_queue 
					SET name=?, musiconhold=?, exten=?, welcome_announce=?, announce=?, dsc=?, cli_name_prefix=?, timeout=?, queue_youarenext=NULL, queue_thereare=NULL, queue_callswaiting=NULL, announce_frequency=?, retry=?, strategy=?, weight=?, queue_thankyou=?, maxwait=?, failover_app=?, failover_appnumber=? 
					WHERE customer_id=? AND queue_id=?';
		
			$values = array(
				$name,
				$data['musiconhold'],
				$data['exten'],
				$data['welcome_announce'],
				$announcement_file,
				$data['dsc'],
				$data['cli_name_prefix'],
				$data['timeout'],
				$data['announce_frequency'],
				$data['retry'],
				$data['strategy'],
				$data['weight'],
				$thankyou,
				$data['maxwait'],
				$data['failover_app'],
				$data['failover_appnumber'],
				$data['customer_id'],
				$data['queue_id']
            );
			
			$qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to update queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function delete($data) {
        try {
            self::setPDO();
			
			$name = $data['customer_id'].'*'.$data['exten'];
			
			// If SLF exist, delete it
			if (!self::check_slf($data['customer_id'], $name) && !self::deleteslf($data['customer_id'], $name)) {
				return false;
			}
			
            $sql = 'DELETE FROM tp_queue WHERE customer_id=? AND queue_id=?';
			
            $values = array(
				$data['customer_id'],
				$data['queue_id']
			);
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);

            return $qry->rowCount();
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete queue : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
    }
	
	public static function createslf($data) {
		try {
			self::setPDO();
			
			$name       = $data['customer_id'].'*'.$data['exten'];
			$validation = implode("", $data['slf_validate']);
			$active     = isset($data['active']) ? 1 : 0;
			
			// Check if SLF already exists, update instead
			if (!self::check_slf($data['customer_id'], $name)) {
				return self::updateslf($data);
			}
			
			$sql = 'INSERT INTO tp_servicelevel_config (queue_name, customer_id, description, start_announcement_no, end_announcement_no, validation, active, create_dttm) VALUES (?, ?, ?, ?, ?, ?, ?, now())';

            $values = array(
				$name,
				$data['customer_id'],
				$data['slf_desc'],
				$data['slf_str_rec'],
				$data['slf_end_rec'],
				$validation,
				$active
            );
            
            $qry = self::$pdo->prepare($sql);
            return $qry->execute($values);
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to add slf queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function updateslf($data) {
		try {
			self::setPDO();
			
			$name       = $data['customer_id'].'*'.$data['exten'];
			$validation = implode("", $data['slf_validate']);
			$active     = isset($data['active']) ? 1 : 0;
						
			$sql = 'UPDATE tp_servicelevel_config SET description=?, start_announcement_no=?, end_announcement_no=?, 
            validation=?, active=? WHERE customer_id=? AND queue_name=?';

            $values = array(
				$data['slf_desc'],
				$data['slf_str_rec'],
				$data['slf_end_rec'],
				$validation,
				$active,
				$data['customer_id'],
				$name				
            );
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to update slf queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function deleteslf($customer_id, $name) {
		try {
			self::setPDO();
			
			// Check if SLF not exists
			if (self::check_slf($customer_id, $name)) {
				return true;
			}
						
			$sql = 'DELETE FROM tp_servicelevel_config WHERE customer_id=? and queue_name=?';

			$values = array(
				$customer_id,
				$name
			);
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			
			// return $qry->rowCount();
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to update slf queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function check_slf($customer_id, $name) {
		try {
			self::setPDO();
			
			$sql = "SELECT * FROM tp_servicelevel_config WHERE customer_id=? AND queue_name=?";
			
			$values = array(
				$customer_id,
				$name
			);
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
            
			if ($qry->fetch()) {
				return false;
			}
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to check slf queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return true;
	}
	
	// Members list
	public static function getList($customer_id, $queue_name, $member_exten = '') {
		$response = array();
		try {
			self::setPDO();
			
			$sql = 'SELECT a.*,b.`name` FROM tp_queue_member a
					LEFT JOIN `tp_account` b ON a.member_exten=b.extennumber AND a.customer_id = b.customer_id 
					WHERE a.customer_id = :customer_id AND a.queue_name = :queue_name';
			
			$param = array(':customer_id' => $customer_id, ':queue_name' => $queue_name);
			
			if ($member_exten) {
				$sql .= ' AND a.member_exten = :member_exten';
				$param[':member_exten'] = $member_exten;
			}
			
			$sql .= ' ORDER BY queue_member_id DESC';
			

			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);

			$response['data'] = $qry->fetchAll();
			$response['status'] = 'SUCCESS';
			$response['total'] = $qry->rowCount();
	
		} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get queue members list: %s', $e->getMessage());
			//return $response;
			throw new \ADException(sprintf('PDOException was thrown when trying to get queue members list: %s', $e->getMessage()), 0, $e);
		}
		
		return $response;
	}
	
	public static function getAgentDetails($customer_id, $exten) {
		try {
			self::setPDO();
			
			$sql = 'SELECT account_id, sipuser, iaxuser FROM tp_account 
					WHERE customer_id=? AND extennumber=?';
			$values = array(
				$customer_id, 
                $exten
            );
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
            
			return $qry->fetch();
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to check slf queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return true;
	}
	
	// Update Agent Account
	// Update account agentexten_type to 1 if 
	// account is an agent else 0
	public static function updateAgentAccount($customer_id, $exten) {
		try {
			self::setPDO();
						
			$sql = 'SELECT member_exten FROM tp_queue_member WHERE customer_id=? AND member_exten=? LIMIT 1';
            $values = array(
				$customer_id,
				$exten				
            );
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			
			$agentexten_type = 0;
			if ($qry->fetch()) {
				$agentexten_type = 1;
			}
			
			$sql = 'UPDATE tp_account SET agentexten_type=? WHERE customer_id=? AND extennumber=?';
            $values = array(
				$agentexten_type,
				$customer_id,
				$exten				
            );
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to update slf queue : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function createList($data, $account_id, $member_name) {
		try {
			self::setPDO();

			$queue_name = $data['customer_id'].'*'.$data['queue_exten'];

			$sql = 'INSERT INTO tp_queue_member 
			(customer_id, account_id, membername, queue_name, interface, member_exten, penalty, agent_type, agent_level, user_key, create_dttm)
            SELECT customer_id, ?, ?, ?, ?, ?, ?, ?, ?, ?, now() FROM tp_queue WHERE customer_id=? AND exten=?';

            $values = array(
				$account_id, 
                $member_name,
                $queue_name,
                $member_name,
                $data['exten'],
                $data['penalty'], 
                $data['agent_type'], 
                $data['agent_level'], 
                $data['user_key'],
                $data['customer_id'],
                $data['queue_exten']
            );
            
            $qry = self::$pdo->prepare($sql);
            $res = $qry->execute($values);
			
			$qry = self::$pdo->prepare('SELECT LAST_INSERT_ID()');
			$qry->execute();
			$last_id = $qry->fetch();
			
			if (!isset($last_id[0]) || !$last_id[0]) {
				return false;
			}
			
			if ($data['agent_type'] == 'STATIC') {
				$sql = 'INSERT INTO as_queue_member
					(customer_id, membername, queue_name, interface, member_exten, penalty)
					SELECT customer_id, ?, ?, ?, ?, ? FROM tp_queue WHERE customer_id=? AND exten=?';

				$values = array(
					$member_name,
					$queue_name,
					$member_name,
					$data['exten'],
					$data['penalty'],
					$data['customer_id'],
					$data['queue_exten']
				);
				
				$qry = self::$pdo->prepare($sql);
				$qry->execute($values);
				
				return true;
			}
			
			if (self::updateAgentAccount($data['customer_id'], $data['exten'])) {
				return true;
			}
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to add ringgroup : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function deleteList($customer_id, $queue_name, $member_exten) {
        try {
            self::setPDO();
			
			$sql = 'DELETE FROM as_queue_member WHERE customer_id=? AND queue_name=? AND member_exten=?';
			$values = array(
				$customer_id,
				$queue_name,
				$member_exten
			);

            $qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			// if (!$qry->rowCount()) {
				// return false;
			// }
			
			$sql = 'DELETE FROM tp_queue_member WHERE customer_id=? AND queue_name=? AND member_exten=?';
			$values = array(
				$customer_id,
				$queue_name,
				$member_exten
			);
			
            $qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			if (!$qry->rowCount()) {
				return false;
			}
			
			if (self::updateAgentAccount($customer_id, $member_exten)) {
				return true;
			}
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete ringgroup : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
    }
}