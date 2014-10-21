<?php namespace Forge;

/**
 * Output headers with output
 * @author Ashley Wilson
 */
class Response
{
   /**
    * Wrap the response with correct headers and output (via echo)
    * @param string $str The content
    * @param int $code HTTP code
    * @return bool
    */
   public static function make($str = null, $code = 200)
   {
      if (is_null($str)) return;
      
      if (headers_sent() === false)
      {
          switch ($code)
          {
             case 500:
                header("HTTP/1.0 500 Internal Server Error");
                break;
             case 404:
                header("HTTP/1.0 404 Not Found");
                break;
             case 200:
                break;
             default:
                throw new \Exception("Unsupported header code [$code]");
          }
      }
      
      echo $str;
      return true;
   }
   
   /**
    * Wrapper to create an error response from the content in views/error/*
    * @param int $code HTTP code
    * @param mixed $data Data to pass to view
    */
   public static function error($code = 500, $data = null)
   {
      self::make(View::load("error.$code", $data), $code);
   }
   
   /**
    * redirect the page to the new location set in the url variable
    * @param string $url defualt index
    */
   public static function redirect($url = 'index')
   {
       header("location: {$url}");
   }
   
   /**
    * Custom output for use with JSON routes
    * @param array $data
    */
   public static function json($data = array()) {
       header('Cache-Control: no-cache, must-revalidate');
       header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
       header("content-type: application/json");
       echo json_encode((array) $data);
   }
   
   /**
    * Stream a file to the browser
    * @access public
    * @param string $file File path
    * @param string $name File name [optional]
    */
   public static function stream($file, $name = null) {
       header('Content-Description: File Transfer');
       header('Content-Type: application/octet-stream');
       header('Content-Disposition: attachment; filename='.(is_null($name) ? basename($file) : $name));
       header('Content-Transfer-Encoding: binary');
       header('Expires: 0');
       header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
       header('Pragma: public');
       header('Content-Length: ' . filesize($file));
       ob_clean();
       flush();
       readfile($file);
   }
}