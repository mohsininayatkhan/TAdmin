<?php 
class Musiconhold extends \Forge\Controller
{
	protected static $customer_id = 1;
	
	public static function index()
	{
		
	}
	
	public static function render() {
		
		$page = Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $record = MusiconholdModel::getAll(self::$customer_id, '', $page, $keywords);
		die(json_encode($record));
	}
	
	public static function get() {
		
		$id = Input::post('id');
		$id = (!empty($id) ? $id : '');
		
		if ($id == '') {
			return array('status' => 'ERROR', 'message' => 'No record selected');
		}
		
		$record = MusiconholdModel::getAll(self::$customer_id, $id);
		
		die(json_encode($record));
	}
	
    public static function save() {
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        $data['name'] = strtolower(str_replace(' ', '', $data['name']));
		
		// No update, create only
		$res = MusiconholdModel::create($data);
		die(json_encode($res));
		
		// die(json_encode(array('status' => 'ERROR', 'message' => 'Unable to create Music On Hold Group')));
    }
	
    public static function delete() {
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        
        $res = MusiconholdModel::delete($data);
		die(json_encode($res));
    }
	
	// Files
    public static function getFiles() {
		$id = Input::post('id');
		$id = (!empty($id) ? $id : '');
		
		$data['customer_id'] = self::$customer_id;
		$data['musiconhold_id'] = $id;
		
		if (!$id) {
			return array('status' => 'ERROR', 'message' => 'No record selected');
		}
		
		$record = MusiconholdModel::getFiles($data);
		
		die(json_encode($record));
    }
	
	public static function uploadFile() {
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;

        // No update, create only
        $res = MusiconholdModel::uploadFile($data, $_FILES);
        echo json_encode($res);
    }
	
	public static function deleteFile() {
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;
        
        $res = MusiconholdModel::deleteFile($data);
		echo json_encode($res);
    }
	
	public static function download() {
        $data = Input::all();
		$data['customer_id'] = self::$customer_id;
        
        $res = MusiconholdModel::download($data);
		echo json_encode($res);
    }
}
