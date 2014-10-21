<?php
namespace AD\Pbx;

use \Forge\Config;
use \Illuminate\Database\Eloquent\Model as DB;

class ManagerAd extends \Model {

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

    public static function deleteAsteriskExtension($customer_id, $extenm) {

        self::$response = array();

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM as_extensions WHERE customer_id = :customer_id AND exten = :exten");
            $qry->execute(array($customer_id, $extenm));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            $self::$response['status'] = 'ERROR';
            $self::$response['message'] = sprintf('PDOException was thrown when trying to delete asterisk extension : %s', $e->getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete asterisk extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function pbxCheckNumberinfo($customer_id, $number, $existing_id = '') {

        try {
            self::setPDO();

            $param = array(':number' => $number, ':customer_id' => $customer_id);

            $sql = 'SELECT * FROM tp_numberinfo ';

            $where = 'WHERE number_info = :number AND customer_id = :customer_id';

            if ($existing_id != '') {
                $where .= ' AND table_id <> :existing_id';
                $param[':existing_id'] = $existing_id;
            }

            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);

            $response['data'] = $qry->fetchAll();

            if (count($response['data'])) {
                return false;
            } else {
                return true;
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to check pbx number iformation: %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function pbxUpdateNumberinfo($customer_id, $number, $app, $table_id, $user_key = '') {

        self::$response = array();

        try {
            self::setPDO();

            $values = array();

            $values['number_info'] = $number;

            $sql = 'UPDATE tp_numberinfo SET number_info=?';

            if ($user_key != '') {
                $sql .= ', user_key=?';
                $values['user_key'] = $user_key;
            }

            $values['customer_id'] = $customer_id;
            $values['table_id'] = $table_id;
            $values['app'] = $app;

            $sql .= ' WHERE customer_id=? AND table_id=? AND app=?';

            $qry = self::$pdo->prepare($sql);

            $qry->execute($values);
            self::$response['status'] = 'SUCCESS';
            return true;
        } catch (\PDOException $e) {
            self::$response['status'] = 'ERROR';
            self::$response['message'] = sprintf('PDOException was thrown when trying to update number information : %s', $e->getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to update number information : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function createAsteriskExtension($customer_id, $exten, $app, $appdata) {

        self::$response = array();

        try {
            self::setPDO();
            $qry = self::$pdo->prepare('INSERT INTO as_extensions (customer_id, context, exten, priority, app, appdata) 
								VALUES( :customer_id, :context, :exten, :priority, :app, :appdata)');

            $values = array(':customer_id' => $customer_id, ':context' => 'default', ':exten' => $exten, ':priority' => '1', ':app' => $app, ':appdata' => $appdata);

            $qry->execute($values);
            $id = self::$pdo->lastInsertId();
            self::$response['status'] = 'SUCCESS';
            return true;
        } catch (\PDOException $e) {
            self::$response['status'] = 'ERROR';
            self::$response['message'] = sprintf('PDOException was thrown when trying to add asterisk extension : %s', $e->getMessage());
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to add asterisk extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function getCallpickupExtension($callpickup_id, $customer_id) {
        try {
            self::setPDO();
            $sql = 'SELECT callpickup_id FROM tp_callpickup WHERE callpickup_id=:callpickup_id AND customer_id=:customer_id LIMIT 1';
            $qry = self::$pdo->prepare($sql);
            $qry->execute(array(':callpickup_id' => $callpickup_id, ':customer_id' => $customer_id));
            return $qry->fetch();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get call pickup extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
    }

    public static function deleteCallpickupExten($callpickup_id) {

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM tp_callpickupexten WHERE callpickup_id = :callpickup_id");
            $qry->execute(array($callpickup_id));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete call pickup extension : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function deleteCallpickup($callpickup_id, $customer_id) {

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM tp_callpickup WHERE callpickup_id = :callpickup_id AND customer_id = :customer_id");
            $qry->execute(array($callpickup_id, $customer_id));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete call pickup : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }

    public static function deleteNumberInfo($customer_id, $table_id, $app) {

        try {
            self::setPDO();
            $qry = self::$pdo->prepare("DELETE FROM tp_numberinfo WHERE customer_id = :customer_id AND table_id = :table_id AND app = :app ");
            $qry->execute(array($customer_id, $table_id, $app));
            $affected_rows = $qry->rowCount();
            return true;
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to delete number information : %s', $e->getMessage()), 0, $e);
            return false;
        }
        return false;
    }
	
	public static function  listCallforward($customer_id, $account_id){
    	try {
            self::setPDO();
            $sql = 'SELECT * FROM tp_callforward WHERE customer_id=:customer_id AND account_id=:account_id';
            $qry = self::$pdo->prepare($sql);
            $qry->execute(array(':customer_id' => $customer_id, ':account_id' => $account_id));
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get call forward list: %s', $e->getMessage()), 0, $e);
            return false;
        }
		return false;
	}
	
	public static function  listSpeeddial($customer_id, $account_id){
    	try {
            self::setPDO();
            $sql = 'SELECT * FROM tp_speeddial WHERE customer_id=:customer_id AND account_id=:account_id';
            $qry = self::$pdo->prepare($sql);
            $qry->execute(array(':customer_id' => $customer_id, ':account_id' => $account_id));
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get speed dial list : %s', $e->getMessage()), 0, $e);
            return false;
        }
		return false;
	}
	
	public static function listDailplans(){
		try {
            self::setPDO();
            $sql = 'SELECT * FROM  '.Config::get('database.prefix').'_dialpatterngroup WHERE customer_id=:customer_id';
            $qry = self::$pdo->prepare($sql);
            $qry->execute(array(':customer_id' => Config::get('app._TENANT_')));
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get dial plans : %s', $e->getMessage()), 0, $e);
            return false;
        }
    }
	
	public static function listCallpickup($customer, $callpickup_id=''){
		try {
            self::setPDO();
            $sql = 'SELECT * FROM tp_callpickup WHERE customer_id=:customer_id';
			
			$values = array();
			$values[':customer_id'] = Config::get('app._TENANT_');
			
			if ($callpickup_id !='' ) {
				$sql .= ' AND callpickup_id= :callpickup_id';
				$values[':callpickup_id'] = $callpickup_id;
			}
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($values);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get dial plans : %s', $e->getMessage()), 0, $e);
            return false;
        }
    }
	
	public static function phoneModelStore($customer_id) {
		try {
            self::setPDO();
            $sql = 'SELECT * FROM  '.Config::get('database.prefix').'_phonemodel WHERE user_key=:customer_id ORDER BY name';
            $qry = self::$pdo->prepare($sql);
            $qry->execute(array(':customer_id' => $customer_id));
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get phone models : %s', $e->getMessage()), 0, $e);
            return false;
        }
	}
	
	 public static function listCallplan($customer_id, $callplan_id='') {	
        
		try {
            self::setPDO();
            $sql = 'SELECT * FROM  '.Config::get('database.prefix').'_callplan WHERE customer_id = :customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
			if ($callplan_id != '') {
				$sql .= ' AND callplan_id = :callplan_id';
				$param[':callplan_id'] = $callplan_id;
			}
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get phone plans : %s', $e->getMessage()), 0, $e);
            return false;
        }
    }
	
	public static function listAnnouncement($customer_id, $announcement_id='') {
		try {
            self::setPDO();
            $sql = 'SELECT * FROM '.Config::get('database.prefix').'_announcement WHERE name is not null and name <>\'system\' AND customer_id=:customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
			if ($announcement_id != '') {
				$sql .= ' AND announcement_id = :announcement_id';
				$param[':announcement_id'] = $announcement_id;
			}
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get dial plans : %s', $e->getMessage()), 0, $e);
            return false;
        }
	}
	
	public static function listIVR($customer_id) {
		try {
            self::setPDO();
            $sql = 'SELECT * FROM '.Config::get('database.prefix').'_ivr a JOIN '.Config::get('database.prefix').'_announcement b ON a.announcement_id=b.announcement_id WHERE a.customer_id=:customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get IVR : %s', $e->getMessage()), 0, $e);
            return false;
        }
	}
	
	public static function listRinggroup($customer_id, $ringgroup_id='') {
		try {
            self::setPDO();
            $sql = 'SELECT * FROM '.Config::get('database.prefix').'_ringgroup WHERE customer_id = :customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
			if ($ringgroup_id != '') {
				$sql .= ' AND account_id = :ringgroup_id';
				$param[':ringgroup_id'] = $ringgroup_id;
			}
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying get ring grouplist : %s', $e->getMessage()), 0, $e);
            return false;
        }
	
	}
	
	public static function listVoicemail($customer_id) {
		try {
            self::setPDO();
            $sql = 'SELECT * FROM as_voicemail WHERE customer_id=:customer_id';
			
			$param = array(':customer_id' => $customer_id);
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to get voice mail : %s', $e->getMessage()), 0, $e);
            return false;
        }
	}
	
	public static function listMusicOnHold($customer_id, $musiconhold_id='') {
		try {
            self::setPDO();
            $sql = 'SELECT * FROM as_musiconhold WHERE name not in (\'default\',\'custom\') AND customer_id=:customer_id';
			
			if ($musiconhold_id!='') {
				$sql .= ' AND musiconhold_id = :musiconhold_id';
				$param[':musiconhold_id'] = $musiconhold_id;
			}
			
			$param = array(':customer_id' => $customer_id);
			
            $qry = self::$pdo->prepare($sql);
            $qry->execute($param);
            return $qry->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to get music on hold : %s', $e->getMessage()), 0, $e);
            return false;
        }
	}
	
}
