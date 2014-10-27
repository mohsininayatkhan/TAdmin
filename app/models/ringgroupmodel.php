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
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}

		if (\AD\Pbx\RigngroupAD::update($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record updated successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while updating record!');
    }
	
	
    public static function create($data) {
        $validator = new Validator($data, self::$rules);
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		// Check if record already exists
		if (\AD\Pbx\RigngroupAD::check_numberinfo($data['customer_id'], $data['ringgroup_num'])) {
			return array('status' => 'ERROR', 'message' => 'The number '.$data['ringgroup_num'].' is already being used!');
		}

		if (\AD\Pbx\RigngroupAD::create($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record added successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while updating record!');
    }

	public static function delete($data) {
		if (!isset($data['customer_id']) || !$data['customer_id'] || !isset($data['ringgroup_id']) || !$data['ringgroup_id']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\RigngroupAD::getAll($data['customer_id'], $data['ringgroup_id']);
        if ($res['status'] != 'SUCCESS') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}
		
		if (\AD\Pbx\RigngroupAD::delete($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while deleting record!');
    }
	
    public static function getList($ringgroup_id) {
		if (!isset($ringgroup_id) || !$ringgroup_id) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		$res = \AD\Pbx\RigngroupAD::getList($ringgroup_id);
		
		if ($res['status'] == 'ERROR') {
			return $res;
		}
		
        $result = array(
			'status'	=> 'SUCCESS',
			'rows' 		=> $res['data'],
			'total' 	=> $res['total']
		);
		return $result;
	}
	
    public static function updateList($data) {
		$validator = new Validator($data, array('extentype' => 'required', 'dst_number' => 'number|extension', 'external_dst_number' => 'number'));
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		if ($data['extentype'] == 'EXTERNAL' && !$data['external_dst_number']) {
			return array('status' => 'ERROR', 'message' => 'Destination cannot be empty');
		}
		
		// Check if record exists
		$res = \AD\Pbx\RigngroupAD::getList($data['list_ringgroup_id']);
        if ($res['status'] != 'SUCCESS') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}
		
		if ($data['extentype'] == 'EXTERNAL') {
			$data['dst_number'] = $data['external_dst_number'];
		}

		if (\AD\Pbx\RigngroupAD::updateList($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record updated successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while updating record!');
    }
	
    public static function createList($data) {
		$validator = new Validator($data, array('list_ringgroup_id' => 'required', 'extentype' => 'required', 'dst_number' => 'number|extension', 'external_dst_number' => 'number'));
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		if ($data['extentype'] == 'EXTERNAL') {
			if (!$data['external_dst_number']) {
				return array('status' => 'ERROR', 'message' => 'Destination cannot be empty');
			}
			
			$data['dst_number'] = $data['external_dst_number'];
		}
		
		// Check if record already exists
		$res = \AD\Pbx\RigngroupAD::getList($data['list_ringgroup_id'], '', $data['extentype'], $data['dst_number']);
        if ($res['status'] != 'SUCCESS') {
			return $res;
		}
		if (isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'The list record is already being used!');
		}

		if ($id = \AD\Pbx\RigngroupAD::createList($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record added successfully!', 'id' => $id);
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while updating record!');
    }
	
	public static function deleteList($data) {
		if (!isset($data['ringgrouplist_id']) || !$data['ringgrouplist_id']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\RigngroupAD::getList($data['ringgroup_id'], $data['ringgrouplist_id']);
        if ($res['status'] != 'SUCCESS') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}
		
		if (\AD\Pbx\RigngroupAD::deleteListOne($data['ringgrouplist_id'])) {
            return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while deleting record!');
    }
}