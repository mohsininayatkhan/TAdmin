<?php namespace Forge;

/**
 * Quick helper functions for working with files
 * @author Ashley Wilson
 */
class File
{
   /**
    * Delete a single file
    * @access public
    * @param string $file File name to delete
    * @return bool
    * @throws \Exception
    */
   public static function delete($file = '')
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
         
      if (is_readable($file) === false) {
         throw new \Exception("File not found [$file");
      }
         
      if (unlink($file) === false) {
         throw new Exception("Failed to delete file [$file]");
      }

      return true;
   }
   
   /**
    * Read a file contents and encode
    * @access public
    * @param string $file File name to read
    * @return string
    * @throws \Exception
    */
   public static function encode($file = '')
   {
      if (self::exists($file) !== true) {
         throw new \Exception("File doesn't exist");
      }
      
      $handle = fopen($file, 'r');
      $content = fread($handle, filesize($file));
      fclose($handle);
      
      return base64_encode($content);
   }
   
   public static function decode($file = '', $content = '')
   {
      if (empty($content)) throw new \Exception("No content to save");
      
      $content = base64_decode($content);
      
      if ($content === false) throw new \Exception("Unable to decode content");
      
      self::put($file, $content, 'w');
   }
   
   /**
    * Wrapper to file_exists
    * @access public
    * @param string $file File to check
    * @return bool
    * @throws \Exception
    */
   public static function exists($file = '')
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
      
      return file_exists($file);
   }

   /**
    * Copy a file to a new location
    * @access public
    * @param string $file File to copy
    * @param string $target Destination (including new filename)
    * @param bool $force If the file exists, should we replace?
    * @return bool
    * @throws \Exception
    */
   public static function copy($file = '', $target = '', $force = false)
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
         
      if (empty($target)) {
         throw new \Exception("No target set");
      }
         
      if (file_exists($file) === false) {
         throw new \Exception("File not found [$file]");
      }
         
      if ($force !== true && file_exists($target)) {
         throw new Exception("This file already exists [$target]");
      }
         
      if (file_exists(dirname($target)) === false) {
         throw new Exception("Target directory does not exist [$target]");
      }

      return copy($file, $target);
   }

   /**
    * Move a file to a new location
    * @access public
    * @param string $file File to move
    * @param string $target Destination (including new filename)
    * @param bool $force If the file exists, should we replace?
    * @return bool
    * @throws \Exception
    */
   public static function move($file = '', $target = '', $force = false)
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
         
      if (empty($target)) {
         throw new \Exception("No target set");
      }

      if (file_exists($file) === false) {
         throw new \Exception("File not found [$file]");
      }
         
      if ($force !== true && file_exists($target)) {
         throw new \Exception("This file already exists [$target]");
      }

      if (file_exists(dirname($target)) === false)
      {
         throw new \Exception("Target directory does not exist [$target]");
      }

      return rename($file, $target);
   }

   /**
    * Return file extension
    * @access public
    * @param string $file File name to query
    * @return mixed
    * @throws \Exception
    */
   public static function extension($file = '')
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
         
      if (is_readable($file) === false) {
         throw new \Exception("File not found [$file]");
      }

      return pathinfo($file, PATHINFO_EXTENSION);
   }

   /**
    * Get file contents (wrapper)
    * @access public
    * @param string $file File name to pull contents from
    * @return mixed
    * @throws \Exception
    */
   public static function get($file = '')
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
         
      if (is_readable($file) === false) {
         throw new \Exception("File not found [$file]");
      }

      if (($content = file_get_contents($file)) === false) {
         throw new \Exception("Failed to load file contents [$file]");
      }
      
      return $content;
   }
   
   /**
    * Write contents to a file
    * @access public
    * @param string $file File name to write
    * @param string $content File contents
    * @param string $mode Write mode
    * @return bool
    * @throws \Exception
    */
   public static function put($file = '', $content = '', $mode = 'a')
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
      
      $handle = fopen($file, $mode);
      
      if (is_resource($handle) === false) {
         throw new \Exception("Cannot open file [$file]");
      }
      
      if (fwrite($handle, $content) === false) {
         throw new \Exception("Failed to write file [$file]");
      }
      
      fclose($handle);
      
      return true;
   }

   /**
    * Open a file handle (wrapper)
    * @access public
    * @param string $file File name to open
    * @param string $type File edit mode
    * @return mixed
    * @throws \Exception
    */
   public static function handle($file = '', $type = 'r')
   {
      if (empty($file)) {
         throw new \Exception("No file name set");
      }
         
      if (is_readable($file) === false) {
         throw new \Exception("File is not valid [$file]");
      }

      if (($handle = fopen($file, $type)) === false) {
         throw new \Exception("Failed to open file [$file]");
      }
      
      return $handle;
   }

}
