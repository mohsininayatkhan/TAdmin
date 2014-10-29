<?php 
use Forge\Validator;
class QueueModel extends \Model\Base {    

	private static $rules = array(
        'customer_id' => 'required', 
        'exten' => 'required|number', 
        'dsc' => 'required',
        'timeout' => 'required|number',
        'retry' => 'required|number',
        'weight' => 'required|number',
        'musiconhold' => 'required'
    );

    public static function getAll($customer, $queue_id='', $page=NULL, $keywords='') {

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
		
		$res = \AD\Pbx\QueueAd::getAll($customer, $queue_id, $options);
		
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

	
    public static function create($data) {
		// If Queue Failover is set
		if (isset($data['chk_failover']) && $data['chk_failover']) {
			if ($data['maxwait'] < 10 || $data['maxwait'] > 9999) {
				return array('status' => 'ERROR', 'message' => 'Maximum Callwait should be >= 10 and <= 9999');
			}
			
			self::$rules['maxwait'] = 'required|number';
			self::$rules['failover_app'] = 'required';
			self::$rules['failover_appnumber'] = 'required|number';
		// Otherwise make sure it is empty
		} else {
			$data['maxwait'] = '';
			$data['failover_app'] = '';
			$data['failover_appnumber'] = '';
		}
		
		// IF Service level feedback is set
		if (isset($data['chk_service']) && $data['chk_service']) {
			self::$rules['slf_desc'] = 'required';
			self::$rules['slf_str_rec'] = 'required';
			self::$rules['slf_end_rec'] = 'required';
			self::$rules['slf_validate'] = 'required|array';
		}

        $validator = new Validator($data, self::$rules);
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		if (!\AD\Pbx\RigngroupAD::check_numberinfo($data['customer_id'], $data['exten'])) {
			return array('status' => 'ERROR', 'message' => 'The number '.$data['exten'].' is already being used!');
		}
		
		if (\AD\Pbx\QueueAd::create($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record added successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while adding record!');
		
    }
	
    public static function update($data) {
        // If Queue Failover is set
		if (isset($data['chk_failover']) && $data['chk_failover']) {
			if ($data['maxwait'] < 10 || $data['maxwait'] > 9999) {
				return array('status' => 'ERROR', 'message' => 'Maximum Callwait should be >= 10 and <= 9999');
			}
			
			self::$rules['maxwait'] = 'required|number';
			self::$rules['failover_app'] = 'required';
			self::$rules['failover_appnumber'] = 'required|number';
		// Otherwise make sure it is empty
		} else {
			$data['maxwait'] = '';
			$data['failover_app'] = '';
			$data['failover_appnumber'] = '';
		}
		
		// IF Service level feedback is set
		if (isset($data['chk_service']) && $data['chk_service']) {
			self::$rules['slf_desc'] = 'required';
			self::$rules['slf_str_rec'] = 'required';
			self::$rules['slf_end_rec'] = 'required';
			self::$rules['slf_validate'] = 'required|array';
		}
		
		self::$rules['queue_id'] = 'required';
		
        $validator = new Validator($data, self::$rules);
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		// Check if record exists
		$res = \AD\Pbx\QueueAd::getAll($data['customer_id'], $data['queue_id']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}

		if (\AD\Pbx\QueueAd::update($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record updated successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while updating record!');
    }
	
	public static function delete($data) {
		if (!isset($data['customer_id']) || !$data['customer_id'] || !isset($data['queue_id']) || !$data['queue_id']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\QueueAd::getAll($data['customer_id'], $data['queue_id']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}

		if (\AD\Pbx\QueueAd::delete($res['data'][0])) {
            return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while deleting record!');
    }
	
	// Members list
	public static function getList($customer_id, $queue_id) {
		if (!$customer_id || !$queue_id) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\QueueAd::getAll($customer_id, $queue_id);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}
		
		$res = \AD\Pbx\QueueAd::getList($customer_id, $res['data'][0]['name']);
		
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
	public static function createList($data) {
		$validator = new Validator($data, array('customer_id' => 'required', 'queue_exten' => 'required', 'exten' => 'required|number', 'agent_type' => 'required', 'agent_level' => 'required', 'penalty' => 'required'));
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		if ($data['penalty'] < 1 || $data['penalty'] > 25) {
			return array('status' => 'ERROR', 'message' => 'Penalty should be >= 1 and <= 25');
		}
		
		
		// Check if record already exists
		$res = \AD\Pbx\QueueAd::getList($data['customer_id'], $data['customer_id'].'*'.$data['queue_exten'], $data['exten']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		if (isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Extension '.$data['exten'].' is already being used!');
		}
		
		// Check if valid extension
		if (!$res = \AD\Pbx\QueueAd::getAgentDetails($data['customer_id'], $data['exten'])) {
			return array('status' => 'ERROR', 'message' => 'Extension '.$data['exten'].' not found');
		}
		
		$account_id = $res['account_id'];
		$member_name = $data['customer_id'].'*'.$data['exten'];
		if ($res['sipuser'] == 1){
			$member_name='SIP/'. $member_name;
		} elseif ($res['iaxuser'] == 1) {
			$member_name='IAX/'. $member_name;
		} else {
			return array('status' => 'ERROR', 'message' => 'Extension '.$data['exten'].' is not registered with SIP or IAX');
		}

		if (\AD\Pbx\QueueAd::createList($data, $account_id, $member_name)) {
			// Get agent name
			$res = \AD\Pbx\QueueAd::getList($data['customer_id'], $data['customer_id'].'*'.$data['queue_exten'], $data['exten']);
			if (!isset($res['data'][0])) {
				return array('status' => 'ERROR', 'message' => 'Failed to fetch record while it should be added');
			}
            return array('status' => 'SUCCESS', 'message' => 'Record added successfully!', 'name' => $res['data'][0]['name']);
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while adding record!');
    }
	public static function deleteList($data) {
		if (!isset($data['customer_id']) || !$data['customer_id'] || !isset($data['queue_exten']) || !$data['queue_exten'] || !isset($data['member_exten']) || !$data['member_exten']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		$queue_name = $data['customer_id'].'*'.$data['queue_exten'];
		
		// Check if record already exists
		$res = \AD\Pbx\QueueAd::getList($data['customer_id'], $queue_name, $data['member_exten']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		if (!isset($res['data'][0])) {
			return array('status' => 'ERROR', 'message' => 'Record not found!');
		}

		if (\AD\Pbx\QueueAd::deleteList($data['customer_id'], $queue_name, $data['member_exten'])) {
            return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while deleting record!');
    }
	
}