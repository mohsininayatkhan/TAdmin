<?php 
use Forge\Validator;
class RinggroupModel extends \Model\Base {    
    
	private static $rules = array(
        'customer_id' => 'required', 
        'name' => 'required|name', 
        'ringtime' => 'number', 
        'ringgroup_num' => 'required|extension'
    );

    public static function getAll($customer_id, $ringgroup_id='', $page=NULL, $keywords='') {

		$result = array();
		$options = array();
		
		$limit = Config::get('app.REC_PER_PAGE');
		
		if ($page) {
            if (!$page || $page < 0) {
                $page = 1;
            }
            $start = ($page - 1) * $limit;
			$options['start'] = $start;
			$options['limit'] = $limit;
        }
		
		$options['total'] = true;
		$options['keywords'] = $keywords;
		
		$res = \AD\Pbx\RigngroupAD::getAll($customer_id, $ringgroup_id, $options);
		
		if ($res['status'] == 'ERROR') {
			return $res;
		}
		
        $result = array(
			'status'	=> 'SUCCESS',
			'rows' 		=> $res['data'], 
			'count' 	=> count($res['data']),
			'total' 	=> $res['total'],
			'start' 	=> (isset($options['start']) ? $options['start'] : 0),
			'num_pages' => ceil($res['total']/$limit)
		);
		return $result;
    }
	
    public static function getNextNum($param) {
		$res = \AD\Pbx\RigngroupAD::getNextNum($param);
		
		if ($res['status'] == 'ERROR') {
			return $res;
		}
		
        $result = array(
			'status'	=> 'SUCCESS',
			'rows' 		=> $res['data']
		);
		
		return $result;
	}
	
    public static function update($data) {
        self::$rules['ringgroup_id'] = 'required';

        $validator = new Validator($data, self::$rules);
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		// Check if record exists
		$res = \AD\Pbx\RigngroupAD::getAll($data['customer_id'], $data['ringgroup_id']);
        if ($res['status'] != 'SUCCESS') {
			return $res;
		}

		$row = $res['data'][0];
		if (!$row) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}

		if (\AD\Pbx\RigngroupAD::update($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record updated successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while updating record!');
    }
	
	
    public static function create($data) {
        $validator = new Validator($data, self::$rules);
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }

		if (\AD\Pbx\RigngroupAD::create($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record updated successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while updating record!');
    }

	public function delete($data) {
		
    }
}