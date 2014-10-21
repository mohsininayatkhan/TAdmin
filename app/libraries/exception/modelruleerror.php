<?php namespace Exception;

use \Forge\Config;
use \Forge\Core;
use \Forge\Event;

/**
 * Class for better logging
 */
class ModelRuleError extends \Exception {
    public function __construct($message) {
      // Log the error!
      $currentlog = Config::get('error.log.file');

      // swap the error log file
      Config::set('error.log.file', 'modelrules/' . date('Ymd').'.log');

      Event::fire('model.trigger.error', array(
         'locator' => $this->getFile(),
         'pointer' => $this->getLine(),
         'message' => $message,
         'type'    => get_class()
      ));

      Config::set('error.log.file', $currentlog);

      parent::__construct($message);
   }
}
