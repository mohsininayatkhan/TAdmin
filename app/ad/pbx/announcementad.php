<?php namespace AD\Pbx;
use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;


class AnnouncementAd {

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
   
   	public static function getAll($customer_id, $announcement_id='', $options=array()) {
	    
		$response = array();
		try 
		{
	        self::setPDO();
			
			$param = array(':customer_id' => $customer_id);
			
			 
			$sql =  'SELECT * FROM tp_announcement';
			
			$where = ' WHERE customer_id = :customer_id AND `name` is not null and `name`<>\'system\' ';
			
			if ($announcement_id!='') {
				$where .= ' AND announcement_id = :announcement_id';
				$param[':announcement_id'] = $announcement_id;
			}
			
			if (isset($options['keywords']) && $options['keywords']!='') {
				$where .= 	' AND (lower(name) like '.self::$pdo->quote(strtolower($options['keywords']).'%');
				$where .=	' OR announcement_number like '.self::$pdo->quote(strtolower($options['keywords']).'%');
				$where .=	' OR extennumber like '.self::$pdo->quote(strtolower($options['keywords']).'%');
				$where .= ')';
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
				$response['total'] = self::getTotalRows('tp_announcement', $where, $param);
			}

      	} catch (\PDOException $e) {
			$response['status'] 	= 'ERROR';
			$response['total'] 		= 0;
			$response['message'] 	= sprintf('PDOException was thrown when trying to get call group: %s', $e->getMessage());
			//return $response;
         	throw new \ADException(sprintf('PDOException was thrown when trying to get call grouup: %s', $e->getMessage()), 0, $e);
      	}
		return $response;
   	}
	
	public static function getTotalRows($table, $where, $param) {
		
		$number_of_rows = 0;
		try 
		{
	        self::setPDO();
			$sql = "SELECT count(announcement_id) FROM $table $where";
			$qry = self::$pdo->prepare($sql); 
			$qry->execute($param); 
			$number_of_rows = $qry->fetchColumn(); 
		}catch (\PDOException $e) {
         	throw new \ADException(sprintf('PDOException was thrown when trying to announcement count: %s', $e->getMessage()), 0, $e);
      	}
		return $number_of_rows;
	}
	
	public static function update($data) {
	
		try {
            self::setPDO();
			
			 $values = array(
                $data['number'], 
                $data['name'], 
                $data['description']
            );
			
			$sql = 'UPDATE tp_announcement SET `announcement_number`=?, `name`=?, `desc`=?';
			
			if (isset($data['file'])) {
				$sql .= ' ,`file`=?, `path`=?';
				
				$values[] = $res['file'];
				$values[] = $res['path'];
			}
			
			$sql .= ' WHERE `announcement_id`=? AND `customer_id`=?';
			
			$values[] = $data['announcement_id'];
			$values[] = $data['customer_id'];
			
            $qry = self::$pdo->prepare($sql);
            $qry -> execute($values);
            return true;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to update announcement : %s', $e -> getMessage()), 0, $e);
            return false;
        }
	
	}
	
	
	public static function create($data) {
	
		try {
            self::setPDO();
			
			 $values = array(
                ':customer_id' 			=> $data['customer_id'],
				':announcement_number' 	=> $data['number'],
				':name' 				=> $data['name'],
				':file' 				=> $data['file'],
				':path' 				=> $data['path'],
				':desc' 				=> $data['description'],
				':user_key' 			=> $data['userkey'],
				':create_dttm' 			=> '2013-09-30 23:38:57',
				':extennumber' 			=> ''
            );
			
			$sql = 'INSERT INTO tp_announcement (`customer_id`, `announcement_number`, `name`, `file`, `path`, `desc`, `user_key`, `create_dttm`, `extennumber`	) 
				VALUES ( :customer_id, :announcement_number, :name, :file, :path, :desc, :user_key, :create_dttm, :extennumber)';
					
            $qry = self::$pdo->prepare($sql);
            $qry -> execute($values);
            return true;
        } catch (\PDOException $e) {
            throw new \ADException(sprintf('PDOException was thrown when trying to create announcement : %s', $e -> getMessage()), 0, $e);
            return false;
        }
	}
	
	public static function delete($customer_id, $announcement_id) {
		try {
			self::setPDO();
			$qry = self::$pdo->prepare("DELETE FROM tp_announcement WHERE customer_id = :customer_id AND announcement_id = :announcement_id");
			$qry->execute(array($customer_id, $announcement_id));
			$affected_rows = $qry->rowCount();
			return true;
		} catch (\PDOException $e) {
			throw new \ADException(sprintf('PDOException was thrown when trying to delete announcement information : %s', $e->getMessage()), 0, $e);
			return false;
		}
		return false;
	}
}