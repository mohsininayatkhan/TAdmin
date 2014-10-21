<?php
use Forge\Validator;

class CallgroupModel extends \Model\Base {

     private static $rules = array(
        'customer_id' => 'required', 
        'code' => 'required', 
        'description' => 'required', 
        'name' => 'required'
    );

    public static function getAll($customer, $callpickup_id = '', $page = NULL, $keywords = '') {

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

        $res = \AD\Pbx\CallgroupAD::getAll($customer, $callpickup_id, $options);

        if ($res['status'] == 'ERROR') {
            return $res;
        }

        $result = array(
            'status' => 'SUCCESS', 
            'rows' => $res['data'], 
            'count' => count($res['data']), 
            'total' => $res['total'], 
            'start' => (isset($options['start']) ? $options['start'] : 0), 
            'num_pages' => ceil($res['total'] / $limit)
        );
        return $result;
    }

	
    public static function update($data) {

        self::$rules['callpickup_id'] = 'required';

        $validator = new Validator($data, self::$rules);

        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }

        if (!\AD\Pbx\NumberAD::alreadyExists($data['customer_id'], $data['code'], $data['callpickup_id'])) {
            return array('status' => "ERROR", 'message' => 'The number ' . $data['code'] . ' is already being used!');
        }

        $oldcode = '';

        $resCallgroup = \AD\Pbx\CallgroupAD::getAll($data['customer_id'], $data['callpickup_id']);

        if ($resCallgroup['status'] == 'SUCCESS') {
            
            $row = $resCallgroup['data'][0];

            if (!$row) {
                return array('status' => 'ERROR', 'message' => 'Record not found for call pickup.');
            }

            $oldcode = $row['callpickup_code'];
        } else {
            // in case of error
            return $resCallgroup;
        }

        if (!\AD\Pbx\AsteriskExtensionAD::delete($data['customer_id'], $data['extenm'])) {
            return array('status' => 'ERROR', 'message' => 'Unable to delete asteric extension.');
        }

        if (\AD\Pbx\CallgroupAD::update($data)) {
            \AD\Pbx\NumberAD::update($data['customer_id'], $data['code'], 'PICKUP', $data['callpickup_id']);
            \AD\Pbx\AsteriskExtensionAD::create($data['customer_id'], $data['code'], 'AGI', 'agi.php');
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

        if (!\Pbx\Ad\NumberAd::alreadyExists($data['customer_id'], $data['code'], $data['callpickup_id'])) {
            return array('status' => "ERROR", 'message' => 'The number ' . $data['code'] . ' is already being used!');
        }

        if (\AD\Pbx\CallgroupAD::create($data) !== false) {
           \AD\Pbx\NumberAD::update($data['customer_id'], $data['code'], 'PICKUP', $data['callpickup_id']);
            \AD\Pbx\AsteriskExtensionAD::create($data['customer_id'], $data['code'], 'AGI', 'agi.php');

            return array('status' => 'SUCCESS', 'message' => 'Record created successfully!');
        }
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while creating record!');
    }

	public function delete($data) {

        $code = '';

        $resCallgroup =\AD\Pbx\CallgroupAD::getAll($data['customer_id'], $data['callpickup_id']);

        if ($resCallgroup['status'] == 'SUCCESS') {
            $row = $resCallgroup['data'][0];
            if (!$row) {
                return array('status' => 'ERROR', 'message' => 'Record not found for call pickup.');
            }

            $code = $row['callpickup_code'];
        } else {
            // in case of error
            return $resCallgroup;
        }
        if (!\AD\Pbx\AsteriskExtensionAD::delete($data['customer_id'], $code)) {
            return array('status' => 'ERROR', 'message' => 'Unable to delete asteric extension.');
        }

        $row = \AD\Pbx\CallpickupAD::get($data['callpickup_id'], $data['customer_id']);

        if ($row) {
            $callpickup_id = $row['callpickup_id'];
        }

        \AD\Pbx\CallpickupExtensionAD::delete($callpickup_id);

        if ( \AD\Pbx\CallpickupAD::delete($callpickup_id, $data['customer_id'])) {
            \AD\Pbx\NumberAD::delete($data['customer_id'], $data['callpickup_id'], 'PICKUP');
            return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!');
        }
    }

}
