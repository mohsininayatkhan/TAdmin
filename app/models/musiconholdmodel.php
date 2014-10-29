<?php 
use Forge\Validator;
class MusiconholdModel extends \Model\Base {    
    private static $rules = array(
        'name' => 'required|name'
    );
	
	private static $iofile_folder = 'iofiles';

    public static function getAll($customer_id, $musiconhold_id='', $page=NULL, $keywords='') {

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
		
		$res = \AD\Pbx\MusiconholdAD::getAll($customer_id, $musiconhold_id, $options);
		
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
        $validator = new Validator($data, self::$rules);
        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }

		$upload_path = Core::path('root').'/'.self::$iofile_folder.'/moh/'.$data['customer_id'].'/';
		$path = $upload_path.$data['name'];
		
		if (file_exists($path) || $data['name'] == 'default'|| $data['name'] == 'custom') {
			die(json_encode(array('status' => 'ERROR', 'message' => 'MOH Group already exists')));
		}
		
		if(!file_exists($upload_path) ){
			if(is_writable(Core::path('root').'/'.self::$iofile_folder.'/moh/')){                        
				mkdir($upload_path);
				
			} else{
				die(json_encode(array('status' => 'ERROR', 'message' => 'Unable to create folder '.$path)));
			}
		}
		
		if(mkdir($path)){
			$data['directory'] = str_replace('\\', '/', $path);
			
			if (\AD\Pbx\MusiconholdAD::create($data)) {
				return array('status' => 'SUCCESS', 'message' => 'Record added successfully!');
			}
		}

        return array('status' => 'ERROR', 'message' => 'Uknown error occured while updating record!');
    }
	
	public static function delete($data) {
		if (!isset($data['musiconhold_id']) || !$data['musiconhold_id'] || !isset($data['customer_id']) || !$data['customer_id']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\MusiconholdAD::getAll($data['customer_id'], $data['musiconhold_id']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		$dir = $res['data'][0]['directory'];
		if (!isset($dir)) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}
		
		if (is_dir($dir)) {
			if (!is_writable($dir)) {
				return array('status' => 'ERROR', 'message' => 'Permission denied to delete MOH file folder');
			}

			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) != "dir") { 
						unlink($dir."/".$object);
					}
				}
			}
			
			reset($objects);
			
			if(!rmdir($dir)){
				return array('status' => 'ERROR', 'message' => 'Unable to delete MOH group');
			}
		}
		
		if (\AD\Pbx\MusiconholdAD::delete($data)) {
            return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!');
        }
		
        return array('status' => 'ERROR', 'message' => 'Uknown error occured while deleting record!');
    }
	
	public static function getFiles($data) {
		if (!isset($data['musiconhold_id']) || !$data['musiconhold_id'] || !isset($data['customer_id']) || !$data['customer_id']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\MusiconholdAD::getAll($data['customer_id'], $data['musiconhold_id']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		$dir = $res['data'][0]['directory'];
		if (!isset($dir)) {
			return array('status' => 'ERROR', 'message' => 'Record not found');
		}
		
		$result = array(
			'status'	=> 'SUCCESS',
			'rows' 		=> array(),
			'total' 	=> 0
		);
		if (file_exists($dir) && $handle = opendir($dir)) {
			$id = 0;
			while (false !== ($file = readdir($handle))) {
				if($file!='.' && $file!='..'){
					$id++;
					$result['rows'][] = array('id' => $id, 'filename' => $file);
				}
			}
			closedir($handle);
			
			$result['total'] = $id;
		}
		
		return $result;
	}
	
	public static function uploadFile($data, $file) {
		if (!isset($data['customer_id']) || !$data['customer_id'] || !isset($data['musiconhold_id']) || !$data['musiconhold_id'] || !isset($file['moh_file']) || !$file['moh_file']['name']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// $ext = explode(".", $file['moh_file']['name']);
		// $ext = strtolower(array_pop($ext));
		// if(!preg_match('/wav|mp3|WAV|MP3/', $ext)){
		$filename = $file['moh_file']['name'];
		$tail = explode(".", $filename);
		$tail = end($tail);
		
		if ($tail != 'wav' && $tail != 'mp3') {
			return array('status' => 'ERROR', 'message' => 'MOH file should be of wav or mp3 format');
		}

		// Check if record exists
		$res = \AD\Pbx\MusiconholdAD::getAll($data['customer_id'], $data['musiconhold_id']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		$dir = $res['data'][0]['directory'];
		if (!isset($dir)) {
			return array('status' => 'ERROR', 'message' => 'Unable to get MOH group details');
		}
		
		$filepath = $dir.'/';
		if (!file_exists($filepath)) {
			return array('status' => 'ERROR', 'message' => 'File not found');
		}
		if(!is_writable($filepath)){
			return array('status' => 'ERROR', 'message' => 'Unable to write file to disk');
		}
		
		if ($tail == 'wav') {
			$filename = str_replace('.wav','',strtolower(str_replace(' ','_',$filename))).'_'. time().'.wav';
		} else {
			$filename = str_replace('.mp3','',strtolower(str_replace(' ','_',$filename))).'_'. time().'.mp3';
		}
		
		if(!move_uploaded_file($file['moh_file']['tmp_name'], $filepath.$filename)){
			return array('status' => 'ERROR', 'message' => 'Unable to write file to disk');
		}
		
		if (file_exists($filepath.$filename)) {
			return array('status' => 'SUCCESS', 'message' => 'File uploaded successfully', 'filename' => $filename);
		}
		
		return array('status' => 'ERROR', 'message' => 'Uknown error occured while uploading file');
	}
	
	public static function deleteFile($data) {
		if (!isset($data['customer_id']) || !$data['customer_id'] || !isset($data['musiconhold_id']) || !$data['musiconhold_id'] || !isset($data['filename']) || !$data['filename']) {
			return array('status' => 'ERROR', 'message' => 'Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\MusiconholdAD::getAll($data['customer_id'], $data['musiconhold_id']);
        if ($res['status'] == 'ERROR') {
			return $res;
		}
		$dir = $res['data'][0]['directory'];
		if (!isset($dir)) {
			return array('status' => 'ERROR', 'message' => 'Unable to get MOH group details');
		}
		
		$filepath = $dir.'/'.$data['filename'];		
		if (!file_exists($filepath)) {
			return array('status' => 'ERROR', 'message' => 'File not found');
		}
		if(!is_writable($filepath)){
			return array('status' => 'ERROR', 'message' => 'Permission denied to delete file');
		}
		
		if (unlink($filepath)) {
			return array('status' => 'SUCCESS', 'message' => 'File deleted successfully');
		}
		
		return array('status' => 'ERROR', 'message' => 'Uknown error occured while deleting file');
    }
	
	public static function download($data) {
		if (!isset($data['customer_id']) || !$data['customer_id'] || !isset($data['musiconhold_id']) || !$data['musiconhold_id'] || !isset($data['filename']) || !$data['filename']) {
			die('Invalid parameter passed');
		}
		
		// Check if record exists
		$res = \AD\Pbx\MusiconholdAD::getAll($data['customer_id'], $data['musiconhold_id']);
        if ($res['status'] == 'ERROR') {
			die('Error occured while downloading file');
		}
		$dir = $res['data'][0]['directory'];
		if (!isset($dir)) {
			die('Error occured while downloading file');
		}
		
		$filepath = $dir.'/'.$data['filename'];		
		
		header("Content-type: application/octetstream");
		header('Content-Disposition: attachment; filename='.$data['filename']);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-transfer-encoding: binary");
		header("Content-length: " . filesize($filepath) . "");

		$fp=fopen($filepath, "r");
		fpassthru($fp);
		fclose($fp);

		exit();
	}
	
}