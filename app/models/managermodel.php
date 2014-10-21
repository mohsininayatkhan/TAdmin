<?php 
class ManagerModel extends \Model\Base {
    
    public function listCallforward($account_id) {
        $res = \AD\Pbx\ManagerAd::listCallforward(Config::get('app._TENANT_'), $account_id);
		return $res;
    }
	
	public function listSpeeddial($customer, $account_id) {
        $res = \AD\Pbx\ManagerAd::listSpeeddial($customer, $account_id);
		return $res;
    }
	
	public function listDailplans() {
        $res = \AD\Pbx\ManagerAd::listDailplans();
		return $res;
    }
	
	public function listCallpickup() {
        $res = \AD\Pbx\ManagerAd::listCallpickup(Config::get('app._TENANT_'));
		return $res;
    }
	
	public function phoneModelStore() {
        $res = \AD\Pbx\ManagerAd::phoneModelStore(Config::get('app._TENANT_'));
		return $res;
    }
	
	public function listCallplan() {
		$res = \AD\Pbx\ManagerAd::listCallplan(Config::get('app._TENANT_'));
		return $res;
	}
	
	public function listAnnouncement() {
		$res = \AD\Pbx\ManagerAd::listAnnouncement(Config::get('app._TENANT_'));
		return $res;
	}
	
	public function listIVR() {
		$res = \AD\Pbx\ManagerAd::listIVR(Config::get('app._TENANT_'));
		return $res;
	}
	
	public function listRinggroup() {
		$res = \AD\Pbx\ManagerAd::listRinggroup(Config::get('app._TENANT_'));
		return $res;
	}
	
	public function listVoicemail() {
		$res = \AD\Pbx\ManagerAd::listVoicemail(Config::get('app._TENANT_'));
		return $res;
	}
	
	public function listMusicOnHold() {
		$res = \AD\Pbx\ManagerAd::listMusicOnHold(Config::get('app._TENANT_'));
		return $res;
	}
}