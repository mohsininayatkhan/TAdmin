<?php 
class Queue extends \Forge\Controller
{
	protected static $customer_id = 1;
	
	public static function index() {
		$res_annc = AnnouncementModel::getAll(self::$customer_id);
		$res_moh = MusiconholdModel::getAll(self::$customer_id);
		$res_extension = ExtensionModel::getAll(self::$customer_id);
		
		$data = array();
		$data['annc'] = $res_annc['rows'];
		$data['moh'] = $res_moh['rows'];
		$data['extension'] = $res_extension['rows'];
		
		return $data;
	}
	
	public static function render() {
		$page = Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $record = QueueModel::getAll(self::$customer_id, '', $page, $keywords);
		die(json_encode($record));
	}
	
	public static function get() {
		$id = Input::post('id');
		if (!$id) {
			return array('status' => 'ERROR', 'message' => 'No record selected');
		}
		
		$record = QueueModel::getAll(self::$customer_id, $id);
		die(json_encode($record));
	}
	
    public static function save() {
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
		$data['user_key'] = time(); // temporary
                
        // update
        if (!empty($data['queue_id'])) {
            $res = QueueModel::update($data);
            die(json_encode($res));
        }
        
        // create
        $res = QueueModel::create($data);
        die(json_encode($res));
    }
	
	public static function delete() { 
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        
        $res = QueueModel::delete($data);
		die(json_encode($res));
    }
	
	// Members List
	public static function getList() {
		$id = Input::post('id');
		if (!$id) {
			return array('status' => 'ERROR', 'message' => 'No record selected');
		}
		
		$record = QueueModel::getList(self::$customer_id, $id);
		die(json_encode($record));
	}
	public static function saveList() {
		$data = Input::post();
		$data['customer_id'] = self::$customer_id;
        $data['user_key'] = time(); // temporary
                
        // update
        if (!empty($data['member_exten'])) {
			$res = QueueModel::deleteList($data);
			if ($res['status'] == 'ERROR') {
				die(json_encode($res));
			}
        }
        
        // create
        $res = QueueModel::createList($data);
        die(json_encode($res));
	}
	public static function deleteList() {
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;
        
        $res = QueueModel::deleteList($data);
		die(json_encode($res));
    }
}
