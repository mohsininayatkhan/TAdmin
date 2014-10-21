<?php namespace Model;

class Base {
    public static function validate($data, $rules) {
        $validator = new \Forge\Validator($data, $rules);
        
        if($validator->fails()) {
            throw new \Validation\Exception("", $validator);
        }
        
        return true;
    }
}