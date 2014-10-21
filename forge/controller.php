<?php namespace Forge;

class Controller
{

   public function __call($method, $args)
   {
      Core::error('404', "The page you are heading for {$method} does not exist");
   }

   public static function __callstatic($method, $args)
   {
      Core::error('404', "The page you are heading for {$method} does not exist");
   }
   
    public static function validate($data, $rules) {
        $validator = new \Forge\Validator($data, $rules);
        
        if($validator->fails()) {
            throw new \Validation\Exception("", $validator);
        }
        
        return true;
    }
}