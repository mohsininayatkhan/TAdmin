<?php namespace Exception;

/**
 * Class for better logging
 */
class PermissionDenied extends \Exception {
    public function __construct($user_id, $class, $method) {     
      // Log the error!
      \Forge\Event::fire('error.fire', array(
         'locator' => $this->getFile(),
         'pointer' => $this->getLine(),
         'message' => "User #{$user_id} tried to access {$class}\\{$method}",
         'type'    => get_class()
      ));
      
      parent::__construct('You do not have permission to access this module');
   }
}
