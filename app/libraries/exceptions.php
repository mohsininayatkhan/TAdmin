<?php

/**
 * DB error
 */
class DataStorageException extends Exception { //fe
    function __construct($message = null, $code = 0) {
        $this->message = trim(preg_replace('/^.*_doQuery:\s*(.*?)\[Native message: ERROR:\s*(.*)\]$/ism', '\\1 [\\2]', $message));
        $this->code = $code;
    }
}

class ADException extends Exception {
    function __construct($message = null, $debug = null, \Exception $previous = null) {

        \Forge\Log::write(\Forge\Log::CRITICAL, trim(preg_replace('/^.*_doQuery:\s*(.*?)\[Native message: ERROR:\s*(.*)\]$/ism', '\\1 [\\2]', $debug)));

        parent::__construct($message, 0, $previous);
    }
}

class ValidationException extends Exception {
    function __construct($message = null, \Exception $previous = null) {
        if ( is_array($message) ) {
            foreach($message as $key => $msg) {
               //$output[] = sprintf("<strong>%s:</strong> %s<br/>", $key, implode(',', $msg));
			   $output[] = sprintf("%s: %s\n", $key, implode(',', $msg));
            }
            $message = implode($output);
        }
        parent::__construct($message, 0, $previous);
    }
}

class AccessException extends Exception {
    function __construct($message = null, \Exception $previous = null) {
        if (is_null($message)) {
            $message = "Access is denied";
        } else {
            $message = $message;
        }
        parent::__construct($message, 0, $previous);
    }
}

?>