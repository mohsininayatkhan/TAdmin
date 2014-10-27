<?php 
class Ringgroup extends \Forge\Controller
{
	protected static $customer_id = 1;
	
	public static function index()
	{
		$res_failovermsg = AnnouncementModel::getAll(self::$customer_id);
		$res_extensions = ExtensionModel::getAll(self::$customer_id);
		
		$data = array();
		$data['failovermsg'] = $res_failovermsg['rows'];
		$data['extensions'] = $res_extensions['rows'];
		
		return $data;
	}
	
	public static function render() {
		
		$page = Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $record = RinggroupModel::getAll(self::$customer_id, '', $page, $keywords);
		echo json_encode($record);
	}
	
	public static function get() {
		
		$id = Input::post('id');
		$id = (!empty($id) ? $id : '');
		
		if ($id == '') {
			return array('status' => 'ERROR', 'message' => 'No record selected');
		}
		
		$record = RinggroupModel::getAll(self::$customer_id, $id);
		
		echo json_encode($record);
	}
	
    public static function nextnum() {
		$data = Input::all();
		$data['customer_id'] = self::$customer_id;
		$data['limit'] = 1;
		
		// create
        $res = RinggroupModel::getNextNum($data);
        echo json_encode($res);
	}
	
    public static function save() {
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        $data['user_key'] = time(); // temporary
                
        // update
        if (!empty($data['ringgroup_id'])) {
            $res = RinggroupModel::update($data);
            die(json_encode($res));
        }
        
        // create
        $res = RinggroupModel::create($data);
        echo json_encode($res);
    }
	
    public static function delete() { 
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        
        $res = RinggroupModel::delete($data);
		echo json_encode($res);
    }
	
	// List
	public static function getList() {
		
		$id = Input::post('id');
		$id = (!empty($id) ? $id : '');
		
		$data['customer_id'] = self::$customer_id;
		
		if (!$id) {
			return array('status' => 'ERROR', 'message' => 'No record selected');
		}
		
		$record = RinggroupModel::getList($id);
		
		echo json_encode($record);
	}
    public static function saveList() { 
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;
        $data['user_key'] = time(); // temporary
                
        // update
        if (!empty($data['ringgrouplist_id'])) {
            $res = RinggroupModel::updateList($data);
            die(json_encode($res));
        }
        
        // create
        $res = RinggroupModel::createList($data);
        echo json_encode($res);
    }
	public static function deleteList() {
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;
        
        $res = RinggroupModel::deleteList($data);
		echo json_encode($res);
    }
}
