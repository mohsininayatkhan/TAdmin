<?php
use Forge\Validator;

class CallpickupextensionModel extends \Model\Base {

    private static $rules = array(
        'callpickup_id' => 'required|numeric',
		'exten'			=> 'required|numeric'
    );
	
	private static $userkey = 'B288DDA5C9BB097E68C922518';	

    public static function getAll($callpickup_id, $callpickupexten_id = '', $page = NULL, $keywords = '') {

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

        $res = \AD\Pbx\CallpickupExtensionAD::getAll($callpickup_id, $callpickupexten_id, $options);

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
	
	public static function create($data) {
		
		$validator = new Validator($data, self::$rules);

        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		$data['userkey'] = self::$userkey;
		
		if (\AD\Pbx\CallpickupExtensionAD::create($data) !== false) {
			return array('status' => 'SUCCESS', 'message' => 'Record created successfully!');			
		}
		return array('status' => 'ERROR', 'message' => 'Uknown error accoured while creating record!');
	}
}