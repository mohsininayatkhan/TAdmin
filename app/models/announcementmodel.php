<?php 
use Forge\Validator;
class AnnouncementModel extends \Model\Base {    
    
	 private static $rules = array(
        'name' => 'required|alpha', 
        'number' => 'required|numeric', 
        'description' => 'required'
    );
	private static $userkey = 'B288DDA5C9BB097E68C922518';	

    public static function getAll($customer, $announcement_id='', $page=NULL, $keywords='') {

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
		
		$res = \AD\Pbx\AnnouncementAd::getAll($customer, $announcement_id, $options);
		
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

	
    public static function update($data) {
		
		self::$rules['announcement_id'] = 'required|numeric';
		
		if (isset($data['file'])) {
			$res = self::upload($data['file']);
			
			if ($res['status'] == 'ERROR') {
				return $res;
			}
			$data['file'] = $res['file'];
			$data['path'] = $res['path'];
		}
		$data['userkey'] = self::$userkey;
		
		if (\AD\Pbx\NumberAD::alreadyExists($data['customer_id'], $data['number'], $data['announcement_id'])) {
            return array('status' => "ERROR", 'message' => 'The number ' . $data['number'] . ' is already being used!');
        }
		
		if (\AD\Pbx\AnnouncementAd::update($data)) {
			return array('status' => 'SUCCESS', 'message' => 'Record updated successfully.');
		}
		return array('status' => 'ERROR', 'message' => 'Can\'t create update record.');
    }
	
	

    public static function create($data) {
	
		if (isset($data['file'])) {
			$res = self::upload($data['file']);
			
			if ($res['status'] == 'ERROR') {
				return $res;
			}
			$data['file'] = $res['file'];
			$data['path'] = $res['path'];
		}
		$data['userkey'] = self::$userkey;
		
		
		
		if (\AD\Pbx\NumberAD::alreadyExists($data['customer_id'], $data['number'])) {
            return array('status' => "ERROR", 'message' => 'The number ' . $data['number'] . ' is already being used!');
        }
		
		if (\AD\Pbx\AnnouncementAd::create($data)) {
			return array('status' => 'SUCCESS', 'message' => 'Record created successfully.');
		}
		return array('status' => 'ERROR', 'message' => 'Can\'t create new record.');
       
    }

	
	public static function upload($file) {
		
		$upload = $file[0];
		$filename = $upload['name'];
		$tail = explode(".", $filename);
		$tail = end($tail);
		
		if (strtolower($tail) != 'wav') {
			return array('status' => 'ERROR', 'message' => 'Recording file should be of wav format');
		}
		
		$name = uniqid().'.wav';
		
		$path = \Forge\Core::path('iofiles') .'annc/'.Config::get('app._TENANT_');;
		if (!file_exists($path)) {
			mkdir($path);
		}
		$des = $path.'/'.$name;			
		
		try {
			\Forge\File::move($upload['tmp_name'], $des, true);
		} catch(Exception $e) {
			return array('status' => 'ERROR', 'message' => $e->getMessage());
		}
		return array('status' => 'SUCCESS', 'message' => 'file uploaded successfully', 'file' => $name, 'path' => $path);
	}
	
	
	public static function delete($data) {
		
		$response = \AD\Pbx\AnnouncementAd::delete($data['customer_id'], $data['announcement_id']);
		
		if ($response) {
			if ($response['status'] != 'ok') {
				return array('status' => 'ERROR', 'message' => $res['status']);
			}
			return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!' );
		}
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while deleting record!');
    }
}