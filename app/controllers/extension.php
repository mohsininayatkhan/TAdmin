<?php 
class Extension extends \Forge\Controller
{
	protected static $module = 'pbxmanagement';
	protected static $extension_management = 'extension_management';
	protected static $extra_telephone_features = 'extra_telephone_features';
	protected static $telephony_management = 'telephony_management';	
	
	public static function index()
	{
		$res_dailplans 	 = DailplanModel::getAll(5);
		$res_callpickups = CallpickupModel::getAll(5);
		$res_phonemodels = PhoneModel::getAll(5);
		$res_musiconhold = MusiconholdModel::getAll(5);
		$res_callplans   = CallplanModel::getAll(5);
		
		$data = array();
		$data['dialplans'] 	 = $res_dailplans['rows'];
		$data['callpickups'] = $res_callpickups['rows'];
		$data['phonemodels'] = $res_phonemodels['rows'];
		$data['callplans']   = $res_callplans['rows'];
		$data['musiconhold'] = $res_musiconhold['rows'];
		
		return $data;
	}
	
	public static function render() {
		
		$page = Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $record = ExtensionModel::getAll(5, '', $page, $keywords);
		echo json_encode($record);
	}
	
	public static function getFailoverApp() {
	
		$type = Input::post('type');
		$ObjManagerModel = new ManagerModel();
		
		$res = array();
		
		if ($type == 'ANNOUNCEMENT') {
			$data = AnnouncementModel::getAll(5);
			$res = $data['rows'];
		} else if ($type == 'EXTEN') {
			$data = ExtensionModel::getAll(5);
			$res = $data['rows'];
		} else if ($type == 'IVR') {
			$data = IvrModel::getAll(5);
			$res = $data['rows'];
		} else if ($type == 'RINGGROUP') {
			$data = RinggroupModel::getAll(5);
			$res = $data['rows'];
		} else if ($type == 'VOICEMAIL') {
			$data = VoicemailModel::getAll(5);
			$res = $data['rows'];
		}
		echo json_encode($res);
	}
	
	public static function get() {
		
		$account_id = Input::post('account_id');
		$account_id  = (!empty($account_id) ? $account_id : '');
		
		if ($account_id == '') {
			return array('status' => 'ERROR', 'message' => 'Account ID can\'t be empty');
		}
		
		$record = ExtensionModel::getAll(5, $account_id);
		
		echo json_encode($record);
	}
	/*
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
