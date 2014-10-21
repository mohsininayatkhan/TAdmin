<?php

/**
 * Quick helper functions for working with folders
 * @author Ashley Wilson
 */
class Folder
{
   /**
    * Recursively remove a directory
    * @access public
    * @static
    * @param string $path Folder path
    * @param bool $preserve Keep the folder (delete onto files/subfolders)
    * @return bool
    * @throws Exception
    */
   public static function remove($path = '', $preserve = false)
   {
      if (empty($path))
         return false;

      if (is_dir($path) === false)
         throw new Exception('Cannot find the directory specified (' . $path . ')');

      // Open handle using directory class. Will throw an UnexpectedValueException if the path cannot be found
      $dir = new DirectoryIterator($path);

      // Loop over each item in the directory
      foreach ($dir as $fileinfo)
      {
         if ($fileinfo->isFile() || $fileinfo->isLink())
         {
            // If item is a file or symblic link, delete
            File::delete($fileinfo->getPathName());
         } elseif ($fileinfo->isDot() == false && $fileinfo->isDir())
         {
            // Recursive call to delete sub-directory
            self::remove($fileinfo->getPathName());
         }
      }

      // Remove the now empty directory
      if ($preserve === FALSE && rmdir($path) !== true)
         throw new Exception('Failed to delete directory (' . $path . ')');

      return true;
   }

   /**
    * Return an array containing a directory listing
    * @access public
    * @static
    * @param string $path Folder path
    * @return array
    * @throws Exception
    */
   public static function fetch($path = '')
   {
      if (empty($path))
         return false;

      if (is_dir($path) === false)
         throw new Exception('Cannot find the directory specified (' . $path . ')');

      $data = array(
         'file' => array(),
         'folder' => array()
      );

      // Open handle using directory class
      $dir = new DirectoryIterator(realpath($path));

      foreach ($dir as $fileinfo)
      {
         if ($fileinfo->isReadable() === false) continue;
         
         if ($fileinfo->isFile() || $fileinfo->isLink())
         {
            $data['file'][] = $fileinfo->getBaseName();
         } elseif ($fileinfo->isDot() == false && $fileinfo->isDir())
         {
            if (substr($fileinfo->getBaseName(), 0, 1) == '.') continue; // SVN or hidden dir
            
            // Recursive call to delete sub-directory
            $data['folder'][$fileinfo->getBaseName()] = self::fetch($fileinfo->getPathName());
         }
      }

      sort($data['file']);
      ksort($data['folder']);

      return $data;
   }

   /**
    * Parse reflection data from classes in a directory
    * *Note: All parsed classes are pulled into the top level array
    * @access public
    * @static
    * @param string $path Folder path
    * @param string $prefix Namespace for the class
    * @return array
    * @throws Exception
    */
   public static function reflect($path, $prefix = '')
   {
      if (empty($path))
         return false;

      if (is_dir($path) === false)
         throw new Exception('Cannot find the directory specified (' . $path . ')');

      // Open handle using directory class
      $realpath = realpath($path);
      static $i = 0;

      $dir = new DirectoryIterator($realpath);

      $data = array();

      foreach ($dir as $fileinfo)
      {

         if ($fileinfo->isReadable() === false) continue;

         if ($fileinfo->isFile() || $fileinfo->isLink())
         {
            // Work out the class name according to PSR-0 standard
            $classname = trim($prefix .(str_replace('/', '\\', str_replace($realpath, '', $fileinfo->getPathName()))), '\\');
            $classname = str_replace('.php', '', $classname);

            try {
               $class = new ReflectionClass($classname);
            } catch (Exception $e)
            {
               // Class does not exist? Skip!
               continue;
            }

            $details = array('id' => ++$i, 'text' => $class->getShortName(), 'full' => $classname, 'state' => 'closed', 'children' => array());

            $methods = array_filter($class->getMethods(ReflectionMethod::IS_PUBLIC), function($value){
               return (strstr($value, '__')) ? false : true;
            });

            foreach ($methods as $method)
            {
               if (Auth::cannot($classname, $method->name)) continue;

               // Stubby
               $output = array(
                  'id' => ++$i,
                  'text' => $method->name,
                  'attributes' => array(
                     'class' => $classname,
                     'notes' => array(),
                     'param' => array(),
                     'extras' => array(),
                     'return' => ""
                  )
               );

               // Explode each new line that starts with *
               $doc = explode('*', $method->getDocComment());

               // Filter out rows that only have whitespace
               $doc = array_filter($doc, function($val){
                  return ($val == '/' || preg_match('/^[\s]*$/', $val)) ? 0 : 1;
               });

               foreach ($doc as $key => $line)
               {
                  if (preg_match('/@access/', $line) || preg_match('/@function/', $line)) continue;

                  if (preg_match('/@param/', $line))
                  {
                     $param = trim(str_replace('@param', '' , $line));

                     $exp = explode('[', $param);

                     if (count($exp) > 1)
                     {
                        $output['attributes']['extras'][trim($exp[0])] = str_replace(']', '', $exp[1]);
                     }

                     $output['attributes']['param'][] = trim($exp[0]);
                     continue;
                  }

                  if (preg_match('/@return/', $line))
                  {
                     $output['attributes']['return'] = trim(str_replace('@return', '' , $line));
                     continue;
                  }

                  $output['attributes']['notes'][] = array('note' => trim(str_replace('@notes', '', $line)));
               }

               $details['children'][] = $output;
            }

            if (count($details['children']))
            {
               usort($details['children'], function($a, $b){
                  if ($a['text'] == $b['text']) return 0;

                  return ($a['text'] < $b['text']) ? -1 : 1;
               });

               if (array_key_exists($prefix, $data) === false) {
                   $data[$prefix] = array(
                      'id' => ++$i,
                      'text' => ucwords($prefix),
                      'children' => array()
                   );
               }

               $data[$prefix]['children'][] = $details;
            }
         } elseif ($fileinfo->isDot() == false && $fileinfo->isDir())
         {
            // Recursive call parse sub-directory
            $data = array_merge($data, self::reflect($fileinfo->getPathName(), $fileinfo->getBaseName()));
         }
      }

      foreach ($data as &$second) {
          usort($second['children'], function($a, $b){
              if ($a['text'] == $b['text']) return 0;

              return ($a['text'] < $b['text']) ? -1 : 1;
          });
      }

      // Sort the array by the class name
      usort($data, function($a, $b){
         if ($a['text'] == $b['text']) return 0;

         return ($a['text'] < $b['text']) ? -1 : 1;
      });

      return $data;
   }

}
