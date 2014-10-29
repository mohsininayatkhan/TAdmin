<?php 
class Blacklist extends \Forge\Controller
{
    protected static $module = 'pbxmanagement';
    protected static $extension_management = 'extension_management';
    protected static $extra_telephone_features = 'extra_telephone_features';
    protected static $telephony_management = 'telephony_management';	
    
    public static function index()
    {
        $res_extension   = ExtensionModel::getAll(5);
		
		$data = array();
		$data['extensions']  = $res_extension['rows'];
		return $data;
    }
	
    public static function render() {
        $page       = Input::post('page');
        $keywords   = Input::post('keywords');
        
        $page     = (!empty($page) ? $page : 1);
        $keywords = (!empty($keywords) ? $keywords : '');
		
        $record = BlacklistModel::getAll(6, '', $page, $keywords);
        echo json_encode($record);
    }
    
    public static function get() {
        $blacklist_id  = Input::post('blacklist_id');
        $blacklist_id  = (!empty($blacklist_id) ? $blacklist_id : '');
        
        if ($blacklist_id == '') {
            return array('status' => 'ERROR', 'message' => 'Blacklist ID can\'t be empty');
        }
        
        $record = BlacklistModel::getAll(6, $blacklist_id);
        echo json_encode($record);
    }
    
    public static function save() {
        
        $data = Input::post();
        $data['customer_id'] = 6;
		
        // update
        if (!empty($data['blacklist_id'])) {
            $res = BlacklistModel::update($data);
            echo json_encode($res);
            return;
        }
        // create
        $res = BlacklistModel::create($data);
        echo json_encode($res);
    }
    
    public static function delete() { 
        $data = Input::post();
        $data['customer_id'] = 6;
        
        BlacklistModel::delete($data);
    }	
}