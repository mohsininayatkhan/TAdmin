<?php 
class Callgroup extends \Forge\Controller
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
		
        $record = CallgroupModel::getAll(5, '', $page, $keywords);
        echo json_encode($record);
    }
    
    public static function get() {
        $callpickup_id  = Input::post('callpickup_id');
        $callpickup_id  = (!empty($callpickup_id) ? $callpickup_id : '');
        
        if ($callpickup_id == '') {
            return array('status' => 'ERROR', 'message' => 'Call Pickup ID can\'t be empty');
        }
        
        $record = CallgroupModel::getAll(5, $callpickup_id);
        echo json_encode($record);
    }
    
    public static function save() {
        
        $data = Input::post();
        
        $data['customer_id'] = 5;
        $data['extenm'] = 15;
        
        // update
        if (!empty($data['callpickup_id'])) {
            $res = CallgroupModel::update($data);
            echo json_encode($res);
            return;
        }
        
        // create
        $res = CallgroupModel::create($data);
        echo json_encode($res);
    }
    
    public static function delete() { 
        $data = Input::post();
        $data['customer_id'] = 5;
        
        CallgroupModel::delete($data);
    }
	
	public static function getCallpickuplist() {
		 $callpickup_id = Input::post('callpickup_id'); 
		 
		 $res = CallpickupextensionModel::getAll($callpickup_id);
		 echo json_encode($res);	
	}
	
	public static function addExtension() {
		$data = Input::post();
        $data['customer_id'] = 5;
		
		$res = CallpickupextensionModel::create($data);
		echo json_encode($res);	
	}
}