<?php 
class Announcement extends \Forge\Controller
{
	protected static $module = 'pbxmanagement';
	protected static $extension_management = 'extension_management';
	protected static $extra_telephone_features = 'extra_telephone_features';
	protected static $telephony_management = 'telephony_management';	
	
	public function index()
	{
		return Blade::make(self::$module.'.'.self::$telephony_management.'.announcement');
	}
	
	public static function render() {
		$page 	= Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $record = AnnouncementModel::getAll(5, '', $page, $keywords);
		echo json_encode($record);
	}
	
	public static function get() {
	
		$announcement_id 	= Input::post('announcement_id');
		$announcement_id  = (!empty($announcement_id) ? $announcement_id : '');
		
		if ($announcement_id == '') {
			return json_encode(array('status' => 'ERROR', 'message' => 'Announcement ID can\'t be empty'));
		}
		
		$record = AnnouncementModel::getAll(5, $announcement_id);
		echo json_encode($record);
	}
	
	public static function save() {
		
		$data = Input::post();
		$data['customer_id'] = 5;
		
		$file = Input::file('rec_file');
		
		if (isset($file) and !empty($file)) {
			$data['file'] = $file;
		}
		
		// update
		if (!empty($data['announcement_id'])) {
			
			$res = AnnouncementModel::update($data);
			echo json_encode($res);
			exit();
		}
		
		$res = AnnouncementModel::create($data);
		echo json_encode($res);
		exit();
	}
	
	public static function delete() { 
		$data = Input::post();
		$data['customer_id'] = 5;
		
		AnnouncementModel::delete($data);
	}
	
	public static function download() {
	
		$announcement_id 	= Input::get('announcement_id');
		$announcement_id  = (!empty($announcement_id) ? $announcement_id : '');
		
		if ($announcement_id == '') {
			return json_encode(array('status' => 'ERROR', 'message' => 'Announcement ID can\'t be empty'));
		}
		
		$record = AnnouncementModel::getAll(5, $announcement_id);
		
		if ($record['status'] == 'ERROR') {
			return json_encode(array('status' => 'ERROR', 'message' => 'Record not found'));
		}
		
		$row = $record['rows'][0];
		$filepath = $row['path'].'/'.$row['file'];		
		
		header("Content-type: application/octetstream");
		header('Content-Disposition: attachment; filename='.$row['file']);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-transfer-encoding: binary");
		header("Content-length: " . filesize($filepath) . "");

		$fp=fopen($filepath, "r");
		fpassthru($fp);
		fclose($fp);

		exit();
	}
}