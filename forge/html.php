<?php namespace Forge;

class HtmlHelperNotFound extends \Exception {};

/**
 * HTML builder
 *
 * @author Ashley Wilson
 */
class Html
{
   /**
    * Form input fields
    */
   private static $input = array('text', 'date', 'password', 'hidden', 'file', 'radio', 'checkbox');

   /**
    * HTML tags
    */
   private static $build = array('span', 'div', 'h1', 'h2', 'h3', 'h4', 'p', 'b', 'i', 'em', 'strong');

   /**
    * Form buttons
    */
   private static $button = array('button', 'submit', 'reset');

   /**
    * Lists
    */
   private static $lists = array('ol', 'ul');

   /**
    * To keep things DRY, common elements above are built here
    * @return string
    * @throws HtmlHelperNotFound
    */
   public static function __callStatic($method, $params)
   {
      if (in_array($method, self::$input))
      {
         return call_user_func_array(array('\Forge\Html', 'input'), array_merge(array($method), $params));
      }

      if (in_array($method, self::$build))
      {
         return call_user_func_array(array('\Forge\Html', 'build'), array_merge(array($method), $params));
      }

      if (in_array($method, self::$button))
      {
         return call_user_func_array(array('\Forge\Html', 'btn'), array_merge(array($method), $params));
      }

      if (in_array($method, self::$lists))
      {
         return call_user_func_array(array('\Forge\Html', 'lists'), array_merge(array($method), $params));
      }

      throw new HtmlHelperNotFound("HTML helper does not exist [$method]");
   }

   /**
    * Form input label
    * @param string $content Label text
    * @param string $for Link to ID on an input
    * @param array $extra HTML properties
    * @return string
    */
   public static function label($content, $for = null, array $extra = array())
   {
      return self::build('label', $content, array_merge($extra, array('for' => $for)));
   }

   /**
    * Form textarea
    * @param string $name
    * @param string $content
    * @param array $extra HTML properties
    * @return string
    */
   public static function textarea($name, $content, array $extra = array())
   {
      return self::build('textarea', $content, array_merge($extra, array('name' => $name)));
   }

   /**
    * Build a select box with an array of options. $values may be nested arrays for option grouping
    * @param string $name HTML name
    * @param string $value Default value
    * @param array $values Array used to build options
    * @param array $extra HTML properties
    * @return string
    */
   public static function select($name, $value, array $values = array(), array $extra = array())
   {
      $html = "<select name=\"". (string) $name ."\" value=\"". (string) $value ."\" ". self::params($extra) .">";

      foreach ($values as $k => $v)
      {
         if (is_array($v))
         {
            $html .= "<optgroup label=\"". (string) $k ."\">";
            foreach ($v as $i => $j)
            {
               $html .= "<option value=\"". (string) $i ."\" ". ($i == $value ? "selected " :"") .">". (string) $j ."</option>";
            }
            $html .= "</optgroup>";
         } else {
            $html .= "<option value=\"". (string) $k ."\" ". ($k == $value ? "selected " :"") .">". (string) $v ."</option>";
         }
      }

      $html .= "</select>";

      return $html;
   }

   /**
    * Simple wrapper for multiple select box
    * @param string $name HTML name
    * @param string $value Default value
    * @param array $values Array used to build options
    * @param array $extra HTML properties
    * @return string
    */
   public static function multiple($name, $value, array $values = array(), array $extra = array())
   {
      return self::select($name, $value, $values, array_merge($extra, array('multiple' => 'multiple')));
   }

   /**
    * Image tag
    * @param string $src Image URL
    * @param array $extra HTML Properties
    * @return string
    */
   public static function img($src = '', array $extra = array())
   {
      return "<img src=\"". (string) $src ."\" ". self::params($extra) ."/>";
   }

   /**
    * Open a form
    * @param string $url Form action
    * @param string $type Form type [default is POST]
    * @param array $extra HTML properties
    * @return string
    */
   public static function form_open($url, $type = 'POST', array $extra = array())
   {
      return "<form action=\"". (string) $url ."\" method=\"". (string) $type ."\" ". self::params($extra) .">";
   }

   /**
    * Open a form for file upload
    * @param string $url Form action
    * @param string $type Form type [default is POST]
    * @param array $extra HTML properties
    * @return string
    */
   public static function form_open_file($url, $type = 'POST', array $extra = array())
   {
      return self::form_open($url, $type, array_merge($extra, array('enctype' => 'multipart/form-data')));
   }

   /**
    * Close a form
    * @return string
    */
   public static function form_close()
   {
      return "</form>";
   }

   /**
    * Special case list
    * @param array $values Data to build the list
    * @param array $extra HTML Properties
    * @return string
    */
   public static function dl($values = array(), array $extra = array())
   {
      $html = "<dl ". self::params($extra) .">";
      foreach ($values as $k => $v)
      {
         $html .= "<dt>$k</dt><dd>$v</dd>";
      }
      $html .= "</dl>";

      return $html;
   }

   /**
    * Standard a link
    * @param string $url Href attribute
    * @param string $content Body of tag
    * @param array $extra HTML properties
    * @return string
    */
   public static function a($url, $content, array $extra = array())
   {
      return self::build('a', $content, array_merge($extra, array('href' => $url)));
   }

   /**
    * Build quick lists. Supports nested list building
    * @access private
    * @param string $type List type
    * @param array $values Content for the list
    * @param array $extra HTML properties
    * @return string
    */
   private static function lists($type, $values = array(), array $extra = array())
   {
      $html = "<{$type} ". self::params($extra) .">";

      foreach ($values as $line)
      {
         if (is_array($line))
         {
            $html .= self::lists($type, $line);
         } else {
            $html .= "<li>". (string) $line ."</li>";
         }
      }

      $html .= "</{$type}>";

      return $html;
   }

   /**
    * Builds Form buttons
    * @access private
    * @param string $type Button type
    * @param string $label Button name
    * @param array $extra HTML properties
    * @return string
    */
   private static function btn($type, $label = '', array $extra = array())
   {
      return self::input($type, '', $label, $extra);
   }

   /**
    * Builds HTML elements
    * @access private
    * @param string $type Element type
    * @param string $content Element content
    * @param array $extra HTML properties
    * @return string
    */
   private static function build($type, $content, array $extra = array())
   {
      return "<{$type} ". self::params($extra) .">". (string) $content ."</{$type}>";
   }

   /**
    * Builds Form inputs
    * @access private
    * @param string $type Input type
    * @param string $name Element name
    * @param string $value Element value
    * @param array $extra HTML properties
    * @return string
    */
   private static function input($type, $name = '', $value = '', array $extra = array())
   {
      return "<input type=\"{$type}\" name=\"". (string) $name ."\" value=\"". (string) $value ."\" ". self::params($extra) ."/>";
   }

   /**
    * Builds HTML properties into a string
    * @access private
    * @param array $data
    * @return string
    */
   private static function params($data)
   {
      $html = '';
      foreach ($data as $k => $v)
      {
         if (is_null($v)) continue;

         $k = is_numeric($k) ? $v : $k;
         $html .= " ". (string) $k ."=\"". (string) $v ."\"";
      }

      return $html;
   }
}
