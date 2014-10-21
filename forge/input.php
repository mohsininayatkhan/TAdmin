<?php namespace Forge;

/**
 * Input handler: GET, POST, FILES
 * 
 * @author Ashley Wilson
 */
class Input
{
   public static function get($key = null, $default = null)
   {
      if (empty($key)) return $_GET;
      
      return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
   }
   
   public static function post($key = null, $default = null)
   {
      if (empty($key)) return $_POST;
      
      return array_key_exists($key, $_POST) ? $_POST[$key] : $default;
   }
   
   public static function file()
   {
      $name = array_key_exists('name', $_FILES) ? (array) $_FILES['name'] : array();
      
      if (empty($name[0])) return array();
      
      $type = (array) $_FILES['type'];
      $tmp_name = (array) $_FILES['tmp_name'];
      $error = (array) $_FILES['error'];
      $size = (array) $_FILES['size'];
      $result = array();
      
      for ($i = 0; $i < count($_FILES['name']); $i++)
      {
         $result[] = array(
            'name' => $name[$i],
            'type' => $type[$i],
            'tmp_name' => $tmp_name[$i],
            'error' => $error[$i],
            'size' => $size[$i]
         );
      }
      
      return $result;
   }
   
   public static function all()
   {
      $file = count($_FILES) ? array('files' => $_FILES) : array();
      return array_merge($_POST, $_GET, $file);
   }
}
