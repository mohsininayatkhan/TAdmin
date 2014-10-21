<?php namespace Api;

use \Forge\Drivers\Auth\Handler\Database as ForgeAuth;
use \Forge\Drivers\Auth\AuthInterface;

class Auth implements AuthInterface {
    private static $access;

    private static $user;

    public static function user() {
        return self::$user;
    }

    public static function buildAccess() {
        if (is_null(self::$access)) {
            $user_id = \Forge\Auth::user()->user_id;

            self::$access = \Forge\Cache::remember("user-access-$user_id", function() use ($user_id){
                $obj = \Ad\System\User::with(
                    'group',
                    'group.role',
                    'group.role.api',
                    'group.role.api.object'
                )->where('user_id', '=', $user_id)->first();

                $access = array();

                foreach ($obj->group as $group) {
                    foreach ($group->role as $role) {
                        foreach ($role->api as $method) {
                            $access[$method->object->object][$method->method_id] = $method->method;
                        }
                    }
                }

                return $access;
            }, 10);
        }

        return self::$access;
    }

    public static function can($class, $method = null) {
        self::buildAccess();

        if (array_key_exists($class, self::$access) === false || in_array($method, self::$access[$class]) === false) return false;

        return true;
    }

    public static function cannot($class, $method = null) {
        return ! self::can($class, $method);
    }

    public static function attempt(array $data = array()) {
        if (is_null(array_get($data, 'username')) || is_null(array_get($data, 'password'))) {
            return false;
        }

        $user = \Ad\Auth\User::where('username', '=', $data['username'])->where('password', '=', self::doubleSalt($data['password'], $data['username']))->first();

        if (is_null($user)) return false;

        self::$user = $user;

        \Forge\Session::set(array('forge-user-id' => self::$user->user_id));

        return true;
    }

    public static function check() {
        $user_id = \Forge\Session::get('forge-user-id');

        if (is_null($user_id)) return false;

        $user = \Ad\Auth\User::find($user_id);

        if (is_null($user)) return false;

        self::$user = $user;
        return true;
    }

    public static function logout() {
        \Forge\Session::destroy();
    }
    
    public static function checkUserHasRole($role)
    {
        $user_id = \Forge\Auth::user()->user_id;

        $roles = \Ad\System\User
            ::join(
                'erp_system.user_group',
                'erp_system.user.user_id',
                '=',
                'erp_system.user_group.user_id'
            )->join(
                'erp_system.group',
                'erp_system.user_group.group_id',
                '=',
                'erp_system.group.group_id'
            )->join(
                'erp_system.group_role',
                'erp_system.group.group_id',
                '=',
                'erp_system.group_role.group_id'
            )->join(
                'erp_system.role',
                'erp_system.group_role.role_id',
                '=',
                'erp_system.role.role_id'
            )
            ->where('erp_system.user.user_id', '=', $user_id)
            ->where('erp_system.role.role', '=', $role)
            ->first();
//return $roles->toArray();
        return (is_null($roles) === false);
    }
    
    /**
     * Takes an array of roles and either 'AND' or 'OR'
     */
    public static function checkUserHasRoles($roles, $venn = 'OR')
    {
        $return;
        foreach ($roles as $role) {
            $return = $this->checkUserHasRole($role);
            if (
                ($venn == 'OR' && $return == true)
                || ($venn == 'AND' && $return == false)
            ) {
                break;
            }
        }
        return $return;
    }
	
	/**
	 * Encrpt password
	 */
	 private static function doubleSalt($toHash, $username){ 
		$password = str_split($toHash,(strlen($toHash)/2)+1); 
		$hash = hash('md5', $username.$password[0].'centerSalt'.$password[1]); 
		return $hash; 
	} 
}