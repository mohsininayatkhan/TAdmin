<?php namespace Forge\Drivers\Locale\handler;

use \Forge\Drivers\Locale\LocaleInterface;

class File implements LocaleInterface
{
   public static function lookup($string, $locale)
   {
      $data = array(
         array(
            'locale' => 'en',
            'original' => 'Username',
            'string' => 'Username'
         ),
         array(
            'locale' => 'gb',
            'original' => 'Username',
            'string' => 'Meine uname'
         )
      );
      
      $output = $string;
      foreach ($data as $row)
      {
         if ($row['locale'] == $locale && $row['original'] == $string)
         {
            $output = $row['string'];
            break;
         }
      }
      
      return $output;
   }
}