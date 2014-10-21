<?php namespace Forge;

class Encryption {
    private static $seed = 'fOrGe';
    
    public static function seed($key) {
        self::$seed = $key;
    }
    
    public static function encrypt($value){
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::$seed, $value, MCRYPT_MODE_ECB, $iv);
        return base64_encode($crypttext);
    }
    
    public static function decrypt($value){
        $crypttext = base64_decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::$seed, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}
