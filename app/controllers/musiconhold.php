<?php 
class Musiconhold extends \Forge\Controller
{
	protected static $customer_id = 1;
	
	public static function index() {
		
	}
	
	public static function render() {
		$page = Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $res = MusiconholdModel::getAll(self::$customer_id, '', $page, $keywords);
		die(json_encode($res));
	}
	
    public static function save() {
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        $data['name'] = strtolower(str_replace(' ', '', $data['name']));
		
		// No update, create only
		$res = MusiconholdModel::create($data);
		die(json_encode($res));
    }
	
    public static function delete() {
        $data = Input::post();
        $data['customer_id'] = self::$customer_id;
        
        $res = MusiconholdModel::delete($data);
		die(json_encode($res));
    }
	
	// Files
    public static function getFiles() {
		$data = Input::post();
		$data['customer_id'] = self::$customer_id;
		
		$res = MusiconholdModel::getFiles($data);
		die(json_encode($res));
    }
	
	public static function uploadFile() {
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;

        // No update, create only
        $res = MusiconholdModel::uploadFile($data, $_FILES);
        die(json_encode($res));
    }
	
	public static function deleteFile() {
        $data = Input::post();
		$data['customer_id'] = self::$customer_id;
        
        $res = MusiconholdModel::deleteFile($data);
		die(json_encode($res));
    }
	
	public static function download() {
        $data = Input::all();
		$data['customer_id'] = self::$customer_id;
        
        $res = MusiconholdModel::download($data);
		die(json_encode($res));
    }
}
