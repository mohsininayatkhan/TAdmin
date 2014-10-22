<?php 
use Forge\Validator;
class ExtensionModel extends \Model\Base {
	
	private static $rules = array(
        'name' => 'required', 
        'extension' => 'required', 
        'password' => 'required', 
        'callplan' => 'required'
    );
	
	private static $userkey = 'B288DDA5C9BB097E68C922518';
    
    public static function getAll($customer, $account_id='', $page=NULL, $keywords='') {
        
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
		
		$res = \AD\Pbx\ExtensionAd::getAll($customer, $account_id, $options);
		
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
	
	 public static function create($post_data) {
	
        $validator = new Validator($post_data, self::$rules);

        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		$data = self::setData($post_data);
		
		// if number already exists
		if (\AD\Pbx\NumberAD::alreadyExists($data['customer'], $data['extension'])) {
            return array('status' => "ERROR", 'message' => 'The number ' . $data['code'] . ' is already being used!');
        }
		
		// if extension already exists
		if (\AD\Pbx\ExtensionAd::isNameAlreadyExists($data['customer'], $data['extension'])) {
            return array('status' => "ERROR", 'message' => 'The extension name ' . $data['extension'] . ' is already being used!');
        }
		
		$res = \AD\Pbx\ExtensionAd::create($data);
		
		if ($res) {
			if ($res['err_status'] < 0) {
				return array('status' => 'ERROR', 'message' => $res['status']);
			}
			return array('status' => 'SUCCESS', 'message' => 'Record created successfully!' );
		}
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while creating record!');
    }
	
	
	 public static function update($post_data) {

        self::$rules['account_id'] = 'required';

        $validator = new Validator($post_data, self::$rules);

        if ($validator->fails()) {
            throw new \ValidationException($validator->all());
            return array('status' => 'ERROR', 'message' => 'Validation error');
        }
		
		$data = self::setData($post_data);
		$data['account_id'] = $post_data['account_id'];
		
		// if number already exists
		if (\AD\Pbx\NumberAD::alreadyExists($data['customer'], $data['extension'], $data['account_id'])) {
            return array('status' => "ERROR", 'message' => 'The number ' . $data['code'] . ' is already being used!');
        }
		
		// if extension already exists
		if (\AD\Pbx\ExtensionAd::isNameAlreadyExists($data['customer'], $data['extension'], $data['account_id'])) {
            return array('status' => "ERROR", 'message' => 'The extension name ' . $data['extension'] . ' is already being used!');
        }
		
		$res = \AD\Pbx\ExtensionAd::update($data);
		
       if ($res) {
			if ($res['err_status'] < 0) {
				return array('status' => 'ERROR', 'message' => $res['status']);
			}
			return array('status' => 'SUCCESS', 'message' => 'Record updated successfully!' );
		}
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while creating record!');
    }
	
	public static function delete($data) {
		
		$result = \AD\Pbx\ExtensionAd::getAll($data['customer_id'], $data['account_id']);
		
		if ($result['status'] == 'ERROR') {
			 return array('status' => 'ERROR', 'message' => 'Sorry! Don\'t have permession to delete the extension.');
		}
		
		$row = $result['data'][0];
		
		$response = \AD\Pbx\ExtensionAd::delete($data['customer_id'], $data['account_id'], $row['extennumber']);
		
		if ($response) {
			if ($response['status'] != 'ok') {
				return array('status' => 'ERROR', 'message' => $res['status']);
			}
			return array('status' => 'SUCCESS', 'message' => 'Record deleted successfully!' );
		}
        return array('status' => 'ERROR', 'message' => 'Uknown error accoured while deleting record!');
	}
	
	public static function setData($data) {
		
		$aParam['customer']		= 5;
		$aParam['name']			= $data['name'];
		$aParam['extension']	= $data['extension'];
		$aParam['email']		= $data['voicemail_email']; 
		$aParam['secret']		= $data['password'];
		$aParam['currency']		= $data['currency'];
		$aParam['callplan']		= $data['callplan'];
		
		$allcodecs=array('alaw','ulaw','gsm','g729');	
		$n_allcodecs=count($allcodecs);		
		$acodecs=(isset($data['codecs'])&&is_array($data['codecs']))?$data['codecs']:array();
		$dcodecs=array_diff($allcodecs,$acodecs);	
		$n_acodecs=count($acodecs);
		
		$aParam['allow']		= ($n_acodecs==$n_allcodecs)?'all':implode(',',$acodecs); 
		$aParam['disallow']		= ($n_acodecs==0)?'all':implode(',',$dcodecs);	
		
		$aParam['cidlocal']		= $data['callerid_local'];
		$aParam['cidext']		= $data['callerid_external'];
		$aParam['nat']			= $data['nat'];
		$aParam['moh'] 			= $data['music_on_hold'];
		$aParam['b_vm']			= isset($data['voicemail'])?'1':'0';
		$aParam['b_fax']		= $data['fax']; 
		$aParam['b_rec']		= isset($data['recording'])?'1':'0';
		$aParam['b_reception'] 	= isset($data['reception'])?'1':'0';
		$aParam['guiuser']		= $data['gui_username'];
		$aParam['guipass']		= $data['gui_password'];
		$aParam['guigroup']		= 1;//$_POST['exten_cboguigroup'];
		$aParam['dailpattern']	= $data['dialplan'];
		$aParam['warnalert']	= $data['warning_alert'];
		$aParam['urgentalert'] 	= $data['critical_alert'];
			
		if ($data['extension_type']=='IAX'){
			$aParam['sip']='0';
			$aParam['iax']='1';
		}
		else if($data['extension_type']=='SIP'){
			$aParam['sip']='1';
			$aParam['iax']='0';
		} else{
			$aParam['sip']='0';
			$aParam['iax']='0';
		}
		
		if($data['forwarding']=='fm'){
			$aParam['followme']='1';
			$aParam['forwarding']='0';
		} else if($data['forwarding']=='fwd'){
			$aParam['followme']='0';
			$aParam['forwarding']='1';
		} else {
			$aParam['followme']='0';
			$aParam['forwarding']='0';
		}
		
		$aParam['userkey']		= self::$userkey;
		$aParam['pickupgrp']	= $data['call_group'];
		$aParam['dtmf']			= $data['dtmf_mode'];
		$aParam['mac'] 			= $data['mac_address'];
		$aParam['b_autop']		= isset($data['autoprovisioning'])?'1':'0';
		$aParam['model']		= $data['phone_model'];
		$aParam['foapp']		= $data['failover_app'];
		$aParam['hosted']		= $data['extension_location'];
		$aParam['call_waiting']	= isset($data['call_waiting'])?'1':'0';
	   	$aParam['before_name']	= $data['before_name'];
	   	$aParam['ring_time']	= $data['ring_duration'];       
	   	$aParam['v_password'] 	= (($data['voicemail_password']>999) && ($data['voicemail_password']<100000000))? $data['voicemail_password']: 'NULL';   
		
		if($aParam['foapp'] == '') {
			$aParam['foappno']='';
		} else if ($aParam['foapp'] == 'EXTERNAL' ){
			$aParam['foappno']= $data['external_failover_app_no'];
			
		} else {
			$aParam['foappno']= $data['failover_app_no'];
		}
		
		$aParam['billtype']= $data['billing_type'];
		$aParam['pickupoverride']=($data['exten_hdcallgrp']!=''&& $data['exten_hdcallgrp']!=$aParam['pickupgrp'])?'1':'0';
		
		return $aParam;
	}
}