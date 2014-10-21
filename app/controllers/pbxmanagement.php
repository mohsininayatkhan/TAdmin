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
	
	
	public static function ajax_announcement_list() {
		$page 	= Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
		$ObjAnnouncementModel = new AnnouncementModel();
        $record = $ObjAnnouncementModel->getAll(5, '', $page, $keywords);
		echo json_encode($record);
	}
}