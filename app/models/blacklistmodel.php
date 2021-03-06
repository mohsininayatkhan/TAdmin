<?php
use Forge\Validator;

class BlacklistModel extends \Model\Base {

     private static $rules = array(
        'exten' 		=> 'required|extension', 
        'phonenumber' 	=> 'required|numeric'
    );
	
	private static $userkey = 'B288DDA5C9BB097E68C922518';	
	
    public static function getAll($customer, $blacklist_id = '', $page = NULL, $keywords = '') {

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

        $res = \AD\Pbx\BlacklistAD::getAll($customer, $blacklist_id, $options);

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
	
		if (isset($data['exten']) && $data['exten'] == 'ALL') {
			 unset(self::$rules['exten']);
			 $data['exten'] = 'NULL';
		}
		
		self::$rules['blacklist_id'] = 'required|numeric';
		
		$validator = new Validator($data, self::$rules);

        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		$data['userkey'] = self::$userkey;
		
		if (\AD\Pbx\BlacklistAD::update($data)) {
			return array('status' => 'SUCCESS', 'message' => 'Record updated successfully.');
		}
		return array('status' => 'ERROR', 'message' => 'Can\'t create update record.');
    }
	
	
    public static function create($data) {
	
		if (isset($data['exten']) && $data['exten'] == 'ALL') {
			 unset(self::$rules['exten']);
			 $data['exten'] = 'NULL';
		}
	
		$validator = new Validator($data, self::$rules);

        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		$data['userkey'] = self::$userkey;
		
		if (\AD\Pbx\BlacklistAD::create($data)) {
			return array('status' => 'SUCCESS', 'message' => 'Record created successfully.');
		}
		return array('status' => 'ERROR', 'message' => 'Can\'t create update record.');
    }

	public static function delete($data) {
	
		if (\AD\Pbx\BlacklistAD::delete($data['customer_id'], $data['blacklist_id'])) {
			return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!' );
		}
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while deleting record!');
    }
}
