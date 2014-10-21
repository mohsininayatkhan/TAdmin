<?php

/**
 * Wrapper for parsing and building XML
 * 
 * <code>
 *    // Array to XML
 *    $_simple = new SimpleXML('<root/>');
 *    $_simple->toXML($array);
 * 
 *    // XML to array
 *    $_simple = new SimpleXML($array);
 *    $_simple->toArray();
 * </code>
 * @author Ashley Wilson
 * @version 1.1
 */
class SimpleXML extends SimpleXMLElement
{
   /**
    * Standard XML to array function
    * We use json_encode/decode to convert the SimpleXML Object into an array,
    * however we still have some work to do:
    * - Moving the attributes into the main tree
    * - Add the correct key for elements that are numeric and contain the 'value' attribute
    * - Change the case of keys (optional)
    * 
    * @access public
    * @param string $override Used on numeric arrays: will ignore this key and place elements a level higher
    * @param boolean $change_case If true will lowercase all keys in the end array
    * @param string $arr XML converted to an array
    * @return array
    */
   public function toArray($override = 'line', $change_case = true, $arr = null)
   {
      $root = false;
      
      if (is_null($arr))
      {
         $root = true;
         $arr = json_decode(json_encode($this), true);
         
         
///////////////////////////If an issue suddenly develops with this function its most likely due to the new single line fix////////////
         $this->singleLineFix($arr, $override);
      }
      
      $override = strtolower($override);
      
      $out = array();
      
      foreach ($arr as $key => $value)
      {
         // Attributes: Add to the main tree structure
         if (strstr($key, '@'))
         {
            foreach ($value as $name => $attribute)
            {
               if (strtolower($name) != 'value')
               {
                  if ($change_case) $name = strtolower($name);
                  $out['@'. $name] = $attribute;
               }
            }
            continue;
         }

         // Check if a key is numeric. If so, look for a value attribute to be used in its place
         if (is_numeric($key))
         {
            $key = isset($value['@attributes']['value']) ? $value['@attributes']['value'] : $key;
         }
         
         if ($change_case) $key = strtolower($key);
         
         // Add catch for blank array with a space - Null the value
         if (is_array($value) && count($value) == 1) {
             $vals = array_values($value);
             $first = array_shift($vals);
             if (is_array($first) === false) { 
                 $first = trim($first);
                 if (empty($first)) $value = null;
             }
         }
         
         // If we encounter an array, recursive call this method
         if (is_array($value))
         {
            if (count($value))
            {
               if (strtolower($key) == strtolower($override))
               {
                  if (isset($value['@attributes']['value']))
                  {
                     $out[$value['@attributes']['value']] = $this->toArray($override, $change_case, $value);
                  } else {
                     $out = $this->toArray($override, $change_case, $value);
                  }
               } else {
                  $out[$key] = $this->toArray($override, $change_case, $value);
               }
            } else {
               $out[$key] = null;
            }
         } else {
            // Single value
            if ($override == $key) $out = $value;
            else $out[$key] = $value;
         }
      }
      
      if ($root) return array($change_case ? strtolower($this->getName()) : $this->getName() => $out);
      else return $out;
   }

    /**
     * Trying to fix single line problem
     */
    private function singleLineFix(&$arr, $override){
        if(array_key_exists($override, $arr)) {
            if(is_array($arr)) {
                if($this->isAssoc($arr[$override])) {
                    $arr = array($arr);
                }
            }
            return true;
        } else {
            foreach($arr as &$value) {
                if(is_array($value)) {
                    if($this->singleLineFix($value, $override)) {
                        break;
                    }
                }
            }
        }
        return false;
    }
    
    //isAssoc function i got off stack overflow, seems to work quite smoothly.
    private function isAssoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

   /**
    * Search XML using case insensitive xpath query (somewhat cheated)
    * Due to the limitation of extending the class, we cannot create a copy of the search as a property
    * This means for every search, a new instance must be created?!?
    * @todo Change class to 'use' and not extend SimpleXMLElement
    * @param string $xpath XPath search string
    * @param string $override Use on numeric arrays, add this as an extra key to contain the array data
    * @return array|NULL
    */
   public function search($xpath = '', $override = 'line')
   {
      if (empty($xpath)) return;
      
      $search = new SimpleXML($this->toXML($this->toArray($override), $override, false));
      
      $override = strtolower($override);
      
      $result = $search->xpath($xpath);
      
      if (empty($result)) return;
      
      $data = array();
      
      foreach ($result as $line)
      {
         $arr = $line->toArray();
         $val = array_values($arr);
         $val = $val[0];
         $data[] = isset($val[0]) ? $val[0] : $val;
      }
      
      return count($data) == 1 ? $data[0] : $data;
   }
   
   /**
    * Used to build basic XML from an array
    * Returns the resulting XML string
    * 
    * @access public
    * @param array $data Our data (should be an array, however if passed an object we will attempt to convert to array)
    * @param string $override Use on numeric arrays, add this as an extra key to contain the array data
    * @param boolean $change_case Will upper case all keys in the XML doc
    * @param string $xml Used internally as part of the recursive loop
    * @return string
    */
   public function toXML($data, $override = 'line', $change_case = true, &$xml = null)
   {
      $out = array();
      
      if (is_object($data)) $data = json_decode(json_encode($data), true);
      if (is_array($data) === false) throw new Exception('Passed item is not an array');
      
      //throw new \Exception(print_r($data, true));
      
      $override = strtolower($override);
      
      foreach ($data as $key => $value)
      {
         $new = false;
         
         $key = $change_case ? strtoupper($key) : $key;
         $override = $change_case ? strtoupper($override) : $override;
         
         if (is_null($xml))
         {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$key />");
            $new = true;
         }
      
         $val = null;
         
         if (strstr($key, '@'))
         {
            $xml->addAttribute(str_replace('@', '', $key), $value);
         } else {
            if (is_numeric($key))
            {
               $val = $key;
               $key = $override;
            }
            
            if (is_array($value))
            {
               if ($new === true) $node = $xml;
               else $node = $xml->addChild($key);
               
               if ($key == $override && is_null($val) === false) $node->addAttribute('value', $val);
               
               $this->toXML($value, $override, $change_case, $node);
            } else {
               @$xml->addChild($key, $value);
            }
         }
      }
      
      return $xml->asXML();
   }
}
