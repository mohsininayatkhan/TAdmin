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
	
	/*public static function get() {
	
		/*$callpickup_id 	= Input::post('callpickup_id');
		$callpickup_id  = (!empty($callpickup_id) ? $callpickup_id : '');
		
		if ($callpickup_id == '') {
			return array('status' => 'ERROR', 'message' => 'Call Pickup ID can\'t be empty');
		}
		
		$record = CallgroupModel::getAll(5, $callpickup_id);
		echo json_encode($record);*/
	/*}
	
	public static function save() {
		
		/*$data = Input::post();
		
		$data['customer_id'] = 5;
		$data['extenm'] = 15;
		
		$ObjCallgroupModel = new CallgroupModel();
		
		// update
		if (!empty($data['callpickup_id'])) {
			$res = CallgroupModel::update($data);
			echo json_encode($res);
			return;
		}
		
		// create
		$res = CallgroupModel::create($data);
		echo json_encode($res);*/
	/*}
	
	public static function delete() { 
		/*$data = Input::post();
		$data['customer_id'] = 5;
		
		$ObjCallgroupModel = new CallgroupModel();
		$ObjCallgroupModel->delete($data);*/
	//}
}
