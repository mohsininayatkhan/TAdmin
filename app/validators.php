<?php

// Register AD folder with the autoloader
Forge\Autoload::dir(\Forge\Core::path('app') .'ad', 'ad');

// Custom validators
Forge\Validator::register('name', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9\s \'-]+$/", $value);
});

Forge\Validator::register('tag', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9_]+$/", $value);
});

Forge\Validator::register('extension', function($value){
    return (bool) preg_match('/([0-9]{4})/', $value);
});

Forge\Validator::register('alphanum-minus', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9-]+$/", $value);
});

Forge\Validator::register('alphanum-uscore', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9_]+$/", $value);
});

Forge\Validator::register('intnl_phonenum', function($value){
    return (bool) preg_match("/^[+][0-9]+$/", $value);
});
Forge\Validator::register('json', function($value){
    return (bool) json_decode($value);
});

Forge\Validator::register('html', function($value){
    return true;
    try {
        $tidy = new tidy;
        $tidy->parseString($value);

        // 3 seems to be the standard here..
        if (tidy_warning_count($tidy) > 3) return false;
        else return true;
    } catch (\Exception $e) {
        return false;
    }
});

Forge\Validator::register('slug', function($value) {
    return (bool) preg_match("/^([a-z0-9-]+)?$/", $value);
});

\Forge\Validator::register('extendedname', function($val){
    return (bool) preg_match("/^[a-zA-Z0-9_ :&.-]{2,45}$/", $val);
});

\Forge\Validator::register('datetime', function($val){
    return (bool) preg_match("/\\A(?:^((\\d{2}(([02468][048])|([13579][26]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])))))|(\\d{2}(([02468][1235679])|([13579][01345789]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|(1[0-9])|(2[0-8]))))))(\\s(((0?[0-9])|(1[0-9])|(2[0-3]))\\:([0-5][0-9])((\\s)|(\\:([0-5][0-9])))?))?$)\\z/", $val);
});

Forge\Validator::register('address', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9\s. &\'-]+$/", $value);
});

Forge\Validator::register('time', function($value){
    return (bool) preg_match("/^[0-9]{2}:[0-9]{2}+$/", $value);
});

Forge\Validator::register('postcode', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9\s]+$/", $value);
});

Forge\Validator::register('file', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9_.\/-\s]+$/", $value);
});

Forge\Validator::register('ssid', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9]{4,20}+$/", $value);
});

Forge\Validator::register('wpaPassword', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9]{6,20}+$/", $value);
});

Forge\Validator::register('wepPassword', function($value){
    return (bool) preg_match("/^[a-fA-F0-9]{26}+$/", $value);
});

Forge\Validator::register('description', function($value){
    return (bool) preg_match("~^[a-zA-Z0-9\s\+\*\%\^\'\"\\\/\!.,_\&£\$\(\)\#\[\]\{\}\?<>=:;`\@-]+$~", $value);
});

Forge\Validator::register('macaddress', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9]+$/", $value);
});

Forge\Validator::register('maccode', function($value){
    return (bool) preg_match("~^[A-Z]{3,4}[0-9]{7,9}\/[0-9A-Z]{5}$~", $value);
});

Forge\Validator::register('product', function($value){
   return (bool) preg_match("/^[0-9\-]+$/", $value);
});

Forge\Validator::register('pdf', function($value){
    return (bool) preg_match("/^[a-zA-Z0-9\s\'\"\\/\!.,-_\&<>]+$/", $value);
});

Forge\Validator::register('phonenum', function($value){
    return (bool) preg_match('/^[0-9\s]{10,14}+$/', $value);
});

Forge\Validator::register('telephone', function($value){
    return (bool) preg_match('/^0[1-2]{1}[0-9]{8,15}$/', $value);
});

Forge\Validator::register('mobile', function($value){
    return (bool) preg_match('/^07[0-9]{9,15}$/', $value);
});

Forge\Validator::register('number', function($value){
    return (bool) preg_match('/([0-9]+)/', $value);
});

Forge\Validator::register('alphanumspacehypen', function($val){
    if (preg_match("/^[a-zA-Z0-9\s-]+$/", $val)) {
        return true;
    }
    return false;
});