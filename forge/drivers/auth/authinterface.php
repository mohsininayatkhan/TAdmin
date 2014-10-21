<?php namespace Forge\Drivers\Auth;

interface AuthInterface
{
   public static function attempt(array $credentials = array());

   public static function check();
}

