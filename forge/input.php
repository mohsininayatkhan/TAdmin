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
   
   public static function file($key = '')
   {
      if (empty($key)) return array();
      
      $upload = array_get($_FILES, $key, array());
      
      $name = (array) array_get($upload, 'name', array());
      
      if (empty($name[0])) return array();

      $type = (array) $upload['type'];
      $tmp_name = (array) $upload['tmp_name'];
      $error = (array) $upload['error'];
      $size = (array) $upload['size'];
      $result = array();

      for ($i = 0; $i < count($upload['name']); $i++)
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
