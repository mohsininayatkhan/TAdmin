<?php
class Pbxmanagement extends \Forge\Controller {
	
	protected static $module = 'pbxmanagement';
	protected static $extension_management = 'extension_management';
	protected static $extra_telephone_features = 'extra_telephone_features';
	protected static $telephony_management = 'telephony_management';	
	
	public static function index() {
		return Blade::make(self::$module.'._menu');
	}
	
	public static function extension() {
		return Blade::make(self::$module.'.'.self::$extension_management.'.extensions')->with('data', Extension::index());
	}
	
	public static function credit_management() {
		return Blade::make(self::$module.'.'.self::$extension_management.'.credit_management');
	}
	
	public static function phone_directory_management() {
		return Blade::make(self::$module.'.'.self::$extension_management.'.phone_directory_management');
	}
	
	// Extra Telephone Features
	public static function features_management() {
		return Blade::make(self::$module.'.'.self::$extra_telephone_features.'.features_management');
	}
	
	
	
	// Telephony Management
	public static function ring_group() {
		return Blade::make(self::$module.'.'.self::$telephony_management.'.ring_group');
	}
	
	
	// Announcement Management
	public static function announcement() {
		return Blade::make(self::$module.'.'.self::$telephony_management.'.announcement');
	}
	
	// Callgroup
	public static function callgroup() {
		 return Blade::make(self::$module.'.'.self::$extra_telephone_features.'.call_group')->with('data', Callgroup::index());
	}
	
	
	public static function ajax_announcement_list() {
		$page 	= Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
		$ObjAnnouncementModel = new AnnouncementModel();
        $record = $ObjAnnouncementModel->getAll(5, '', $page, $keywords);
		echo json_encode($record);
	}
	
	public static function getFailoverApp() {
	
		$type = Input::post('type');	
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
		} else if ($type == 'QUEUE') {
			$data = QueueModel::getAll(5);
			$res = $data['rows'];
		}
		echo json_encode($res);
	}
	
	// RingGroup
	public static function ringgroup() {
		return Blade::make(self::$module.'.ringgroup')->with('data', Ringgroup::index());
	}
	
	// Music On Hold
	public static function musiconhold() {
		return Blade::make(self::$module.'.musiconhold');
	}
}