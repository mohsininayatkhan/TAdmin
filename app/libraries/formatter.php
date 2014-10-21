<?php

/**
 * Holder for generic/useful formatters
 * 
 * @author Ashley Wilson
 */
class Formatter {
    /**
     * Quick standard date formatter
     * @access public
     * @param string $format Date format
     * @param string $time Time string
     * @return string
     */
    public static function date($format = 'Y-m-d H:i:s', $time = null) {
        return date($format, (is_null($time) ? time() : strtotime($time)));
    }
    
    /**
     * Calculate and create (optional) customers folder.
     * This function exists to create nested folder structure, to avoid the 32,000 sub-folder limit
     * @access public
     * @param string $customerNo Customer number
     * @param bool $create Optional - Will create the folder(s) if true
     * @return string
     * @throws \Exception
     */
    public static function customerFolder($customerNo, $create = true) {
        $base = strtolower(md5($customerNo));
        $sub = str_split(substr($base, 0, 3));
        $sub[] = $customerNo;
        
        if ($create === true) {
            $folder = \Forge\Core::path('root') .'/storage/documents/';
            
            for ($i = 0; $i < count($sub); $i++) {
                $folder .= $sub[$i] .'/';
                
                if (is_dir($folder) === false) {
                    if (mkdir($folder) !== true) {
                        throw new \Exception("Failed to create path [$folder]");
                    }
                }
            }
        }
        
        $parts = implode('/', $sub);
        $path = 'storage/documents/'. $parts .'/';
        
        return $path;
    }
    
    /**
     * Calculate and create (optional) product folder
     * This function exists to create nested folder structure, to avoid the 32,000 sub-folder limit (wont occur for products, but its nice to be consistent)
     * @access public
     * @param string $product_id
     * @param bool $create Optional - Will create the folders if true
     * @return string
     */
    public static function productFolder($product_id, $create = true) {
        
        //lookup product record
        $product = \Ad\Stock\Product::where('product_id', '=', $product_id)
                                    ->first();
        if(is_null($product)) {
            throw new \Exception("product does not exist {$product_id}");
        }
        if($create == true){
            $folder = \Forge\Core::path('root') .'/storage/images/products/';
            //this little if statement will probably only ever occur once.
            if (is_dir($folder) === false) {
                if (mkdir($folder) !== true) {
                    throw new \Exception("Failed to create path [$folder]");
                }
            }
            for($i = 'a'; $i < 'd'; $i++) {
                
                $folder .= $product['product_group_'.$i].'/';
                if (is_dir($folder) === false) {
                    if (mkdir($folder) !== true) {
                        throw new \Exception("Failed to create path [$folder]");
                    }
                }
            }
            $folder .= $product['product'].'/';
            if (is_dir($folder) === false) {
                if (mkdir($folder) !== true) {
                    throw new \Exception("Failed to create path [$folder]");
                }
            }
        }
        $path = 'storage/images/products/'.$product['product_group_a'].'/'.$product['product_group_b'].'/'.$product['product_group_c'].'/'.$product['product'].'/';
        return $path;
    }
    
    /**
     * Calculate and create (optional) quotes folders for uploading of documents.
     * This function exists to create nested folder structure, to avoid the 32,000 sub-folder limit
     * @access public
     * @param string $reference Quote reference number
     * @param bool $create Optional - Will create the folder(s) if true
     * @return string
     * @throws \Exception
     */
    public static function quoteFolder($reference, $create = true) {
        $base = strtolower(md5($reference));
        $sub = str_split(substr($base, 0, 3));
        $sub[] = $reference;
        
        if ($create === true) {
            $folder = \Forge\Core::path('root') .'/storage/quote/';
            
            for ($i = 0; $i < count($sub); $i++) {
                $folder .= $sub[$i] .'/';
                
                if (is_dir($folder) === false) {
                    if (mkdir($folder) !== true) {
                        throw new \Exception("Failed to create path [$folder]");
                    }
                }
            }
        }
        
        $parts = implode('/', $sub);
        $path = 'storage/quote/'. $parts .'/';
        
        return $path;
    }
    
    /**
     * Filesize notation (KB, GB, etc.)
     * @access public
     * @param int $bytes Filesize in bytes
     * @return string
     */
    public static function filesize($bytes) {
        if ($bytes >= pow(1024, 3)) {
            $bytes = number_format($bytes / pow(1024, 3), 2) .' GB';
        } elseif ($bytes >= pow(1024, 2)) {
            $bytes = number_format($bytes / pow(1024, 2), 2) .' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) .' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes .' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes .' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
    
    /**
     * Hours, minutes, seconds, etc.
     * @access public
     * @param int $seconds Seconds
     * @return string
     */
    public static function secondsToMinutes($value) {
        $minutes = floor($value / 60);
        $seconds = $value % 60;
        return $minutes .' mins'. ($seconds > 0 ? ' '. $seconds .' secs' : '');
    }
    
    /**
     * Working day calculator
     * @access public
     * @param int $days How many working days ahead/behind
     * @param string $startdate Optional - Set a date to start from
     * @param string $format PHP Date format
     * @param array $holidays List of 'holiday' days (should be same format as format param)
     * @param array $working_day List of days to count as non-working days
     * @return string
     */
    public static function workingDay($days = 7, $startdate = null, $format = "Y-m-d", array $holidays = null, array $working_day = array(6,7)) {
        if (is_numeric($days) === false) throw new Exception('Invalid working days ('. $days .')');
        if (empty($format)) throw new Exception('No output format set');
        if (is_null($startdate)) $startdate = date("Y-m-d");
        if (is_null($holidays)) $holidays = \Forge\Config::get('app.holidays');
        
        $days = (int) $days; // Cast to integer
        
        $enddate = new DateTime($startdate);
        $type = $days < 0 ? "DEC" : "INC";
        
        while ($days != 0) {
            // Add/subtract a day
            if ($type == 'INC') $enddate->add(new DateInterval('P1D'));
            else $enddate->sub(new DateInterval('P1D'));
            
            // Not Weekend: Move day 1 along
            if (in_array($enddate->format('N'), $working_day) === false) $days = ($type == 'INC' ? $days - 1 : $days + 1);
            
            // Holiday: Move 1 day back
            if (in_array($enddate->format('N'), $working_day) === false && in_array($enddate->format($format), $holidays)) $days = ($type == 'INC' ? $days + 1 : $days - 1);
        }
        
        return $enddate->format($format);
    }

    /**
     * Generic Avatar system used by GitLab (among others)
     * @access public
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public static function gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' '. $key .'="'. $val .'"';
            }
            $url .= ' />';
        }
        
        return $url;
    }
    
    /**
     * Generate a random password based on a string of characters.
     * 50/50 chance of password being upper/lower case
     * @access public
     * @param int $length Password length
     * @param string $chars Character list (defaults to Hexadecimal)
     * @return Password string
     */
    public static function password($length = 8, $chars = null, $bool = true) {
        $list = is_null($chars) ? "abcdef0123456789" : $chars;
        $list_length = strlen($list);
        
        $password = "";
        
        $bool = $bool === true ? mt_rand(0, 1) : 0;
        
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $list_length - 1);
            
            $password .= ($bool) ? strtoupper($list[$rand]) : $list[$rand];
        }
        
        return $password;
    }
    
    /**
     * Format a phone number into something pretty
     * @access public
     * @param int $number Phone number
     * @return string Pretty phone number
     */
    public static function phoneNumber($number) {
        $first = substr($number, 0, 1);
        
        if ($first != '0') return false;
        
        $output = str_replace(' ', '', $number);
        
        if (strlen($number) > 9) {
            $second = substr($number, 1, 1);
            
            if ($second == 7 || $second == 1) {
                // Mobile OR Home phone
                $output = substr($number, 0, 5) ." ". substr($number, 5, strlen($number) - 5);
            } elseif ($second == 8) {
                // Business
                $output = substr($number, 0, 4) ." ". substr($number, 4, 3) ." ". substr($number, strlen($number) - 4, strlen($number));
            }
        }
        
        return $output;
    }
}
