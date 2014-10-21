<?php
    
class ip4
{
   // --------------------
   public static function cidr_to_subnet($cidr, $ip_count=1)
   {
      // Return array
      $store = array(); 
      
      $bitmask     = self::number_to_bitmask($ip_count);
      $network_arr = self::cidr_to_network($cidr);
      $addr_incr   = ip2long($network_arr['network_addr']);
      
      while($addr_incr <= ip2long($network_arr['broadcast_addr']))
      {
         $subnet_cidr = long2ip($addr_incr) .'/'. $bitmask;
         $store[] = self::cidr_to_network($subnet_cidr);
    
         $addr_incr += $ip_count;
      }
      
      return $store;
   }

   // --------------------
   public static function cidr_to_range($cidr)
   {
      // Return array
      $store = array();
      
      $network_arr = self::cidr_to_network($cidr);
      
      $first_addr_long = ip2long($network_arr['network_addr']);
      $last_addr_long  = ip2long($network_arr['broadcast_addr']);
      
      // Incrementing ip
      $ip_long = $first_addr_long;
      
      while($ip_long <= $last_addr_long)
      {
         $store[] = long2ip($ip_long);
         $ip_long++;
      }
      
      return $store;
   }
   
   // --------------------
   public static function cidr_to_network($cidr)
   {      
      // Return array
      $store = array();
      
      list($target_ip, $bitmask) = explode('/', $cidr);

      // Calculate the first + last ip
      $range_count    = pow(2, (32 - $bitmask)) - 1;
      
      $network_addr   = long2ip(ip2long($target_ip) & -1 << (32 - $bitmask));
      $broadcast_addr = long2ip(ip2long($target_ip) + $range_count);
      
      if ($bitmask == 32)
      {
         $first = long2ip(ip2long($network_addr));
         $last = long2ip(ip2long($broadcast_addr));
      } else {
         $first = long2ip(ip2long($network_addr) + 1);
         $last = long2ip(ip2long($broadcast_addr) - 1);
      }

      $store = array
      (
         'cidr'               => $cidr,
         'network_cidr'       => $network_addr .'/'. $bitmask,
         'bitmask'            => $bitmask,
         'netmask'            => self::bitmask_to_netmask($bitmask),
         'network_addr'       => $network_addr,
         'broadcast_addr'     => $broadcast_addr,
         'first_useable_addr' => $first,
         'last_useable_addr'  => $last,
         'range_count'        => $range_count + 1
      );
      
      return $store;
   }
   
   // --------------------
   public static function bitmask_to_netmask($mask)
   {
      if ($mask > 32 || $mask < 0) return FALSE;
      
      $mask_bin = -1 << (32 - $mask);
      return long2ip($mask_bin);
   }
   
   // --------------------
   public static function netmask_to_bitmask($subnet)
   {
      $mask_long = ip2long($subnet);
      return self::count_set_bits($mask_long);
   }
   
   // --------------------
   public static function validate_bit_number($int)
   {
      if (($int & ($int - 1)) !== 0) return FALSE;
      else return TRUE;
   }

   // --------------------
   public static function validate_cidr($cidr)
   {
      list($target_ip, $bitmask) = explode('/', $cidr);
      
      $subnet_mask = self::bitmask_to_netmask($bitmask);
      
      $subnet_long = ip2long($subnet_mask);
      $target_long = ip2long($target_ip);
      
      if ($target_ip == long2ip($subnet_long & $target_long))
         return TRUE;
      
      return FALSE; 
   }

   // --------------------
   public static function count_set_bits($int)
   {
      $int = $int - (($int >> 1) & 0x55555555);
      $int = ($int & 0x33333333) + (($int >> 2) & 0x33333333);
      return (($int + ($int >> 4) & 0xF0F0F0F) * 0x1010101) >> 24;
   }
   
   // --------------------
   public static function number_to_bitmask($int)
   {
      return 32 - (LOG($int) / LOG(2));
   }

   // --------------------
   // --------------------
   // --------------------
   // --------------------
   
} // END Class
