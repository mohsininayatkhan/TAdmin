<?php

return array(

   /**
    * Session Type
    *
    * Types: 'cookie', 'database'.
    *
    */

   'type' => 'cookie',

   /**
    * Session Database
    * if the database driver is used then the table must be given a name
    */


   'table' => 'sessions',

   /**
    * session domain
    *
    * optional
    */

    'domain' => \Forge\Server::get('HTTP_HOST'),


   /**
    *
    * Session GC
    *
    *
    * Some session drivers require the manual clean-up of expired sessions.
    * This option specifies the probability of session garbage collection
    * occuring for any given request to the application.
    *
    * For example, the default value states that garbage collection has a
    * 2% chance of occuring for any given request to the application.
    * Feel free to tune this to your requirements.
    */

   'tidy' => array(2, 100),

   /**
    *idle timeout
    *
    */

   'lifetime' => 6000,

   /**
    * Session Expiration On Close
    */

   'expire_on_close' => false,

   /**
    * Session Cookie Name
    *
    * The name that should be given to the session cookie.
    *
    */

   'name' => 'sid',

   /*
    * Session Cookie Path
    * The path for which the session cookie is available.
    *
    */

   'path' => '/'


);
