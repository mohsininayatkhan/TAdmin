<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class RigngroupAD {

    /**
     * @var \PDO PDO instance.
     */
    private static $pdo;

    public static $response;

    /**
     * @var array Database options.
     */
    private $dbOptions;
	
	private static $module = 'RINGGROUP';

    private static function setPDO() {
        if (is_null(self::$pdo)) {
            $conn = Config::get('database.default');
            self::$pdo = DB::resolveConnection($conn)->getPdo();
        }
    } 

	public static function getAll($customer_id, $ringgroup_id='', $options=array()) {
		$response = array();
		try {
			self::setPDO();
			$sql =  'SELECT * FROM tp_ringgroup';			
			$where = ' WHERE customer_id = :customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
			if ($ringgroup_id!='') {
				$where .= ' AND ringgroup_id = :ringgroup_id';
				$param[':ringgroup_id'] = $ringgroup_id;
			}
			
			if (isset($options['keywords']) && $options['keywords']) {
				$where .= ' AND (lower(name) like '.self::$pdo->quote(strtolower($options['keywords']).'%');
				$where .= ' OR lower(cli_name_prefix) like '.self::$pdo->quote(strtolower($options['keywords']).'%');
				$where .= ' OR lower(ringgroup_num) like '.self::$pdo->quote(strtolower($options['keywords']).'%');
				$where .= ' OR lower(strategy) like '.self::$pdo->quote(strtolower($options['keywords']).'%').')';
			}
			
			$sql .= $where;
			$sql .= ' order by ringgroup_id DESC';
			
			if (isset($options['start']) && isset($options['limit'])) {
				$sql .= ' LIMIT '.$options['start'].', '.$options['limit'];
			}

			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);
			
			$response['data'] 		= $qry->fetchAll();
			$response['status'] 	= 'SUCCESS';
			
			if (isset($options['total']) && $options['total']) {
				$response['total'] = self::getTotalRows('tp_ringgroup', $where, $param);
			}

		} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get ringgroup: %s', $e->getMessage());
			//return $response;
			throw new \ADException(sprintf('PDOException was thrown when trying to get ringgroup: %s', $e->getMessage()), 0, $e);
		}
		return $response;
	}
	
	public static function getTotalRows($table, $where, $param) {
		
		$number_of_rows = 0;
		try 
		{
	        self::setPDO();
			$sql = "SELECT count(ringgroup_id) FROM $table $where";
			$qry = self::$pdo->prepare($sql); 
			$qry->execute($param); 
			$number_of_rows = $qry->fetchColumn(); 
		}catch (\PDOException $e) {
         	throw new \ADException(sprintf('PDOException was thrown when trying to get ringgroup count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}
	
	public static function getNextNum($param) {
		
		$response = array();	
		try {
			self::setPDO();
			
			$sql = 'SELECT seq.SeqValue as num 
					FROM ( 
					SELECT (HUNDREDS.SeqValue + TENS.SeqValue + ONES.SeqValue+ THOUSANDS.SeqValue) SeqValue 
					FROM ( SELECT 0 SeqValue UNION ALL SELECT 1 SeqValue UNION ALL SELECT 2 SeqValue UNION ALL SELECT 3 SeqValue UNION ALL SELECT 4 SeqValue UNION ALL SELECT 5 SeqValue UNION ALL SELECT 6 SeqValue UNION ALL SELECT 7 SeqValue UNION ALL SELECT 8 SeqValue UNION ALL SELECT 9 SeqValue ) ONES 
					CROSS JOIN ( SELECT 0 SeqValue UNION ALL SELECT 10 SeqValue UNION ALL SELECT 20 SeqValue UNION ALL SELECT 30 SeqValue UNION ALL SELECT 40 SeqValue UNION ALL SELECT 50 SeqValue UNION ALL SELECT 60 SeqValue UNION ALL SELECT 70 SeqValue UNION ALL SELECT 80 SeqValue UNION ALL SELECT 90 SeqValue ) TENS 
					CROSS JOIN ( SELECT 0 SeqValue UNION ALL SELECT 100 SeqValue UNION ALL SELECT 200 SeqValue UNION ALL SELECT 300 SeqValue UNION ALL SELECT 400 SeqValue UNION ALL SELECT 500 SeqValue UNION ALL SELECT 600 SeqValue UNION ALL SELECT 700 SeqValue UNION ALL SELECT 800 SeqValue UNION ALL SELECT 900 SeqValue ) HUNDREDS
					CROSS JOIN ( SELECT 0 SeqValue UNION ALL SELECT 1000 SeqValue UNION ALL SELECT 2000 SeqValue UNION ALL SELECT 3000 SeqValue UNION ALL SELECT 4000 SeqValue UNION ALL SELECT 5000 SeqValue UNION ALL SELECT 6000 SeqValue UNION ALL SELECT 7000 SeqValue UNION ALL SELECT 8000 SeqValue UNION ALL SELECT 9000 SeqValue ) THOUSANDS 
					) seq 
					LEFT JOIN tp_numberinfo num
					ON seq.SeqValue=num.number_info AND num.customer_id=:customer_id
					WHERE num.number_info IS NULL';
				
			if(isset($param['minvalue']) && $param['minvalue']){	 
				 $sql .= ' AND seq.SeqValue >= '.$param['minvalue'];
			}
			
			if(isset($param['maxvalue']) && $param['maxvalue']){
				$sql .= ' AND seq.SeqValue <= '.$param['maxvalue'];
			}
			
			if(isset($param['limit']) && $param['limit']){
				$sql .= ' LIMIT '.$param['limit'];

			}

			$param = array(':customer_id' => $param['customer_id']);

			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);
			
			$response['data']   = $qry->fetchAll();
			$response['status'] = 'SUCCESS';

		} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get ringgroup: %s', $e->getMessage());
			throw new \ADException(sprintf('PDOException was thrown when trying to get ringgroup: %s', $e->getMessage()), 0, $e);
		}
		
		return $response;
	}

	public static function update($data) {
		try {
			self::setPDO();

			$sql = 'UPDATE tp_ringgroup 
					SET ringgroup_num=?, name=?, strategy=?, failover_app=?, failover_appnumber=?, failover_announcement_no=?, ringtime=?, cli_name_prefix=?
					WHERE ringgroup_id=? AND customer_id=?';
					
			$values = array(
				$data['ringgroup_num'], 
				$data['name'], 
				$data['strategy'], 
				$data['failover_app'], 
				isset($data['failover_appnumber']) ? $data['failover_appnumber'] : $data['external_failover_appnumber'],
				$data['failover_announcement_no'],
				$data['ringtime'],
				$data['cli_name_prefix'],
				$data['ringgroup_id'],
				$data['customer_id']
			);
			
			$qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to update ringgroup : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function create($data) {
		try {
			self::setPDO();
			$sql = 'INSERT INTO tp_ringgroup 
					(customer_id, ringgroup_num, name, strategy, failover_app, failover_appnumber, failover_announcement_no, ringtime, cli_name_prefix, user_key, create_dttm) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())';

            $values = array(
				$data['customer_id'], 
                $data['ringgroup_num'], 
                $data['name'], 
                $data['strategy'], 
                $data['failover_app'], 
                isset($data['failover_appnumber']) ? $data['failover_appnumber'] : $data['external_failover_appnumber'], 
                $data['failover_announcement_no'], 
                $data['ringtime'], 
                $data['cli_name_prefix'],
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
			
			return self::add_numberinfo($data['customer_id'], $data['ringgroup_num'], self::$module, $last_id[0], $data['user_key']);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to add ringgroup : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function delete($data) {
        try {
            self::setPDO();
            
			$sql = 'DELETE FROM tp_ringgroup WHERE ringgroup_id=? AND customer_id=?';
			
			$values = array(
				$data['ringgroup_id'],
				$data['customer_id']
			);
			
			$qry = self::$pdo->prepare($sql);
            $qry->execute($values);

			if ($qry->rowCount()) {
				self::deleteList($data['customer_id'], $data['ringgroup_id']);
				self::delete_numberinfo($data['customer_id'], $data['ringgroup_id'], self::$module);
			}

            return true;
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete ringgroup : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
    }	

	public static function check_numberinfo($customer_id, $ringgroup_num, $userkey = '') {
		try {
            self::setPDO();
			
			$sql = "SELECT * FROM tp_numberinfo WHERE customer_id=? AND number_info=?";
			
			if ($userkey) {
				$sql .= ' AND table_id<>'.$userkey;
			}
			
			 $values = array(
				$customer_id,
				$ringgroup_num
			);
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);

			if ($qry->fetch()) {
				return false;
			}
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete ringgroup : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return true;
	}
	
	public static function add_numberinfo($customer_id, $ringgroup_num, $module, $table_id, $userkey = '') {
		try {
			self::setPDO();
			
			$sql = 'INSERT INTO tp_numberinfo (customer_id, number_info, app, table_id, user_key, create_dttm) VALUES (?, ?, ?, ?, ?, now())';

            $values = array(
				$customer_id, 
                $ringgroup_num, 
                $module, 
                $table_id, 
                $userkey
            );
            
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
            
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to add ringgroup : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function delete_numberinfo($customer_id, $table_id, $module) {
		try {
            self::setPDO();
			
            $sql = 'DELETE FROM tp_numberinfo WHERE customer_id=? AND table_id=? AND app=?';
			
			$values = array(
				$customer_id,
				$table_id,
				$module
			);
			
			$qry = self::$pdo->prepare($sql);
            $qry->execute($values);
            // $affected_rows = $qry->rowCount();
			
            return true;
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete ringgroup : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
	}
	
	// List
	public static function getList($ringgroup_id, $ringgrouplist_id = '', $extentype = '', $dst_number = '') {
		$response = array();
		try {
			self::setPDO();
			$sql = 'SELECT * FROM tp_ringgrouplist WHERE ringgroup_id = :ringgroup_id ';
			
			$param = array(':ringgroup_id' => $ringgroup_id);
			
			if ($ringgrouplist_id) {
				$sql .= ' AND ringgrouplist_id = :ringgrouplist_id';
				$param[':ringgrouplist_id'] = $ringgrouplist_id;
			}
			if ($extentype) {
				$sql .= ' AND extentype = :extentype';
				$param[':extentype'] = $extentype;
			}
			if ($dst_number) {
				$sql .= ' AND dst_number = :dst_number';
				$param[':dst_number'] = $dst_number;
			}
			
			$sql .= ' ORDER BY ringgrouplist_id DESC';
			

			$qry = self::$pdo->prepare($sql);
			$qry->execute($param);

			$response['data'] = $qry->fetchAll();
			$response['status'] = 'SUCCESS';
			$response['total'] = $qry->rowCount();
	
		} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get ringgroup: %s', $e->getMessage());
			//return $response;
			throw new \ADException(sprintf('PDOException was thrown when trying to get ringgroup: %s', $e->getMessage()), 0, $e);
		}
		
		return $response;
	}
	
	public static function updateList($data) {
		try {
			self::setPDO();

			$sql = 'UPDATE tp_ringgrouplist SET extentype=?, dst_number=? 
					WHERE ringgrouplist_id=? AND ringgroup_id= (SELECT ringgroup_id FROM tp_ringgroup WHERE ringgroup_id=? AND customer_id=?)';
					
			$values = array(
				$data['extentype'], 
				$data['dst_number'], 
				$data['ringgrouplist_id'], 
				$data['ringgroup_id'], 
				$data['customer_id']
			);
			
			$qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			
			return true;
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to update ringgroup : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function createList($data) {
		try {
			self::setPDO();
			
			$sql = 'INSERT INTO tp_ringgrouplist (ringgroup_id, extentype, dst_number, user_key, create_dttm) VALUES (?, ?, ?, ?, now())';

            $values = array(
				$data['ringgroup_id'], 
                $data['extentype'],
                $data['dst_number'], 
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
			
			return $last_id[0];
			
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to add ringgroup : %s', $e -> getMessage()), 0, $e);
			return false;
		}
		
		return false;
	}
	
	public static function deleteList($customer_id, $ringgroup_id) {
        try {
            self::setPDO();
			
			$sql = 'DELETE FROM tp_ringgrouplist 
					WHERE ringgroup_id=(SELECT ringgroup_id FROM tp_ringgroup WHERE customer_id=? AND ringgroup_id=? LIMIT 1)';
			
			$values = array(
				$customer_id,
				$ringgroup_id				
            );
			
            $qry = self::$pdo->prepare($sql);
			$qry->execute($values);
            // return $qry->rowCount();
			
			return true;
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete ringgroup : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
    }
	
	public static function deleteListOne($ringgrouplist_id) {
        try {
            self::setPDO();
			
			$sql = 'DELETE FROM tp_ringgrouplist WHERE ringgrouplist_id=?';
			
			$values = array($ringgrouplist_id);
			
            $qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			// return $qry->rowCount();

            return true;
			
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to delete ringgroup : %s', $e->getMessage()), 0, $e);
            return false;
        }
		
        return false;
    }
}
