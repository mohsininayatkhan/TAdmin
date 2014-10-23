<?php 
class Queue extends \Forge\Controller
{

	public static function index()
	{
		// $res_dailplans 	 = DailplanModel::getAll(5);
		// $res_callpickups = CallpickupModel::getAll(5);
		// $res_phonemodels = PhoneModel::getAll(5);
		// $res_musiconhold = MusiconholdModel::getAll(5);
		// $res_callplans   = CallplanModel::getAll(5);
		
		// $data = array();
		// $data['dialplans'] 	 = $res_dailplans['rows'];
		// $data['callpickups'] = $res_callpickups['rows'];
		// $data['phonemodels'] = $res_phonemodels['rows'];
		// $data['callplans']   = $res_callplans['rows'];
		// $data['musiconhold'] = $res_musiconhold['rows'];
		
		return $data;
	}
	
	public static function render() {
		
		$page = Input::post('page');
		$keywords = Input::post('keywords');
		
		$page  	  = (!empty($page) ? $page : 1);
		$keywords = (!empty($keywords) ? $keywords : '');
		
        $record = RinggroupModel::getAll(5, '', $page, $keywords);
		echo json_encode($record);
	}
	
	public static function get() {
		
		$id = Input::post('id');
		$id = (!empty($id) ? $id : '');
		
		if ($id == '') {
			return array('status' => 'ERROR', 'message' => 'Account ID can\'t be empty');
		}
		
		$record = RinggroupModel::getAll(5, $id);
		
		echo json_encode($record);
	}
	
    public static function nextnum() {
		$data = Input::all();
		$data['customer_id'] = 5;
		$data['limit'] = 1;
		
		// create
        $res = RinggroupModel::getNextNum($data);
        echo json_encode($res);
	}
	
    public static function save() {
        
        $data = Input::post();
        
        $data['customer_id'] = 5;
                
        // update
        if (!empty($data['ringgroup_id'])) {
            $res = RinggroupModel::update($data);
            echo json_encode($res);
            return;
        }
        
        // create
        $res = RinggroupModel::create($data);
        echo json_encode($res);
    }
}
