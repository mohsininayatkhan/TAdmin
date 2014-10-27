<?php namespace AD\Pbx;
use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class ExtensionAd {
   
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
         	throw new \ADException(sprintf('PDOException was thrown when trying to get extensions: %s', $e->getMessage()), 0, $e);
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
         	throw new \ADException(sprintf('PDOException was thrown when trying to get count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}
	
	
	public static function create($aParam) {
	    
		$sql=sprintf(' CALL sp_account_insert( %d,\'%.20s\',\'%.50s\',\'%.100s\',\'%.20s\',\'%.3s\',		\'%d\',\'%.35s\',\'%.35s\',\'%.35s\',\'%.35s\',\'%.10s\',\'%.20s\',\'%.1s\',\'%.1s\',\'%.1s\',\'%.1s\',\'%.1s\',\'%d\',\'%.10s\',\'%.20s\',\'%.1s\',\'%.20s\',\'%.30s\',\'%.20s\',\'%.20s\',\'%.20s\',\'%.1s\',\'%.1s\',0,\'t\',\'1\', \'%s\', \'%s\', \'%s\', \'%s\',%d ,%d,%f,%f,\'%.1s\',\'%d\',%s) ',$aParam['customer'],$aParam['name'],$aParam['extension'],$aParam['email'],$aParam['secret'],$aParam['currency'],$aParam['callplan'],$aParam['allow'],$aParam['disallow'],$aParam['cidlocal'],$aParam['cidext'],		$aParam['nat'],$aParam['moh'],$aParam['b_vm'],$aParam['b_fax'],$aParam['sip'],$aParam['iax'],$aParam['b_rec'],$aParam['pickupgrp'],$aParam['dtmf'],$aParam['mac'],$aParam['b_autop'],$aParam['model'],$aParam['userkey'],$aParam['foapp'],$aParam['foappno'],$aParam['billtype'],$aParam['followme'],$aParam['forwarding'], $aParam['b_reception'], $aParam['guiuser'], $aParam['guipass'], $aParam['guigroup'], $aParam['hosted'],$aParam['dailpattern'],$aParam['urgentalert'],$aParam['warnalert'],$aParam['call_waiting'],$aParam['ring_time'],$aParam['v_password']);
		try {
            self::setPDO();
			/*$sql = 'CALL sp_account_insert(
				:customer, :name, :extension, :email, :secret, :currency, :callplan, :allow, :disallow, :cidlocal, :cidext, :nat,	:moh, :b_vm, :b_fax, :sip, 
				:iax, :b_rec, :pickupgrp, :dtmf, :mac, :b_autop, :model, :userkey, :foapp, :foappno, :billtype, :followme, :forwarding, :credit, :activated, :status, :pickupoverride, :b_reception, 
				:guiuser, :guipass, :guigroup, :hosted, :dailpattern, :urgentalert, :warnalert, :call_waiting, :ring_time)';
				
            $values = array(
				':customer' 	=> $data['customer'],
				':name' 		=> $data['name'],
				':extension' 	=> $data['extension'],
				':email' 		=> $data['email'],
				':secret' 		=> $data['secret'],
				':currency' 	=> $data['currency'],
				':callplan' 	=> $data['callplan'],
				':allow' 		=> $data['allow'],
				':disallow' 	=> $data['disallow'],
				':cidlocal' 	=> $data['cidlocal'],
				':cidext' 		=> $data['cidext'],
				':nat' 			=> $data['nat'],
				':moh' 			=> $data['moh'],
				':b_vm' 		=> $data['b_vm'],
				':b_fax' 		=> $data['b_fax'],
				':sip' 			=> $data['sip'],
				':iax' 			=> $data['iax'],
				':b_rec' 		=> $data['b_rec'],
				':pickupgrp' 	=> $data['pickupgrp'],
				':dtmf' 		=> $data['dtmf'],
				':mac' 			=> $data['mac'],
				':b_autop' 		=> $data['b_autop'],
				':model' 		=> $data['model'],
				':userkey' 		=> $data['userkey'],
				':foapp' 		=> $data['foapp'],
				':foappno' 		=> $data['foappno'],
				':billtype' 	=> $data['billtype'],
				':followme' 	=> $data['followme'],
				':forwarding' 	=> $data['forwarding'],
				':credit' 		=> 0,
				':activated' 	=> 't',
				':status' 		=> 1,
				':b_reception' 	=> $data['b_reception'],
				':guiuser' 		=> $data['guiuser'],
				':guipass' 		=> $data['guipass'],
				':guigroup' 	=> $data['guigroup'],
				':hosted' 		=> $data['hosted'],
				':dailpattern' 	=> $data['dailpattern'],
				':urgentalert' 	=> $data['urgentalert'],
				':warnalert' 	=> $data['warnalert'],
				':call_waiting' => $data['call_waiting'],
				':ring_time' 	=> $data['ring_time']
            );
			
			echo count($values);
			die;*/
            
            $qry = self::$pdo -> prepare($sql);
            $qry -> execute();
            $response = $qry->fetch();
            return $response;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to add extension : %s', $e -> getMessage()), 0, $e);
            return false;
        }
        return false;
    }
	
	public static function update($data) {
        
		try {
           
			self::setPDO();
			$sql = 'CALL sp_account_update(
				:account_id, :customer, :name, :extension, :email, :secret, :currency, :callplan, :allow, :disallow, :cidlocal, :cidext, :nat,	:moh, :b_vm, :b_fax, :sip, 
				:iax, :b_rec, :pickupgrp, :dtmf, :mac, :b_autop, :model, :userkey, :foapp, :foappno, :billtype, :followme, :forwarding, :credit, :activated, :status,:pickupoverride, :b_reception, 
				:guiuser, :guipass, :guigroup, :hosted, :dailpattern, :urgentalert, :warnalert, :call_waiting, :ring_time,:v_password)';

            $values = array(
				':account_id' 	=> $data['account_id'],
				':customer' 	=> $data['customer'],
				':name' 		=> $data['name'],
				':extension' 	=> $data['extension'],
				':email' 		=> $data['email'],
				':secret' 		=> $data['secret'],
				':currency' 	=> $data['currency'],
				':callplan' 	=> $data['callplan'],
				':allow' 		=> $data['allow'],
				':disallow' 	=> $data['disallow'],
				':cidlocal' 	=> $data['cidlocal'],
				':cidext' 		=> $data['cidext'],
				':nat' 			=> $data['nat'],
				':moh' 			=> $data['moh'],
				':b_vm' 		=> $data['b_vm'],
				':b_fax' 		=> $data['b_fax'],
				':sip' 			=> $data['sip'],
				':iax' 			=> $data['iax'],
				':b_rec' 		=> $data['b_rec'],
				':pickupgrp' 	=> $data['pickupgrp'],
				':dtmf' 		=> $data['dtmf'],
				':mac' 			=> $data['mac'],
				':b_autop' 		=> $data['b_autop'],
				':model' 		=> $data['model'],
				':userkey' 		=> $data['userkey'],
				':foapp' 		=> $data['foapp'],
				':foappno' 		=> $data['foappno'],
				':billtype' 	=> $data['billtype'],
				':followme' 	=> $data['followme'],
				':forwarding' 	=> $data['forwarding'],
				':credit' 		=> 0, // default value
				':activated' 	=> 't', // default value
				':status' 		=> 1, // default value
				':pickupoverride' 	=> $data['pickupoverride'],
				':b_reception' 	=> $data['b_reception'],
				':guiuser' 		=> $data['guiuser'],
				':guipass' 		=> $data['guipass'],
				':guigroup' 	=> $data['guigroup'],
				':hosted' 		=> $data['hosted'],
				':dailpattern' 	=> $data['dailpattern'],
				':urgentalert' 	=> $data['urgentalert'],
				':warnalert' 	=> $data['warnalert'],
				':call_waiting' => $data['call_waiting'],
				':ring_time' 	=> $data['ring_time'],
				':v_password' 	=> $data['v_password']
            );
			$qry = self::$pdo->prepare($sql);
            $qry->execute($values);
			$response = $qry->fetch();
			return $response;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to update extension : %s', $e -> getMessage()), 0, $e);
            return false;
        }
    }
	
	public static function isNameAlreadyExists($customer_id, $name, $existing_id='') {
		try {
			self::setPDO();
			
			$param = array(':name' => $name, ':customer_id' => $customer_id);

            $sql = 'SELECT * FROM tp_account ';

            $where = 'WHERE name = :name AND customer_id = :customer_id';

            if ($existing_id != '') {
                $where .= ' AND table_id <> :existing_id';
                $param[':existing_id'] = $existing_id;
            }

            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);

            $response['data'] = $qry->fetchAll();

            if (count($response['data'])) {
                return true;
            } else {
                return false;
            }
		} catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to check name : %s', $e -> getMessage()), 0, $e);
            return false;
        }
	}
	
	
	public static function delete($customer_id, $account_id, $extennumber) {
	
		try {
			self::setPDO();
			$sql = 'CALL sp_account_delete(:account_id, :customer_id, :extennumber)';

			$values = array(
				':account_id' 	=> $account_id,
				':customer_id' 	=> $customer_id,
				':extennumber' 	=> $extennumber
			);
			
			$qry = self::$pdo->prepare($sql);
			$qry->execute($values);
			$response = $qry->fetch();
			return $response;
		} catch (\ADException $e) {
			throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete extension : %s', $e -> getMessage()), 0, $e);
			return false;
		}
	}
}