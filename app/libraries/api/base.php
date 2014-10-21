<?php namespace Api;

/**
 * Base class for API classes. Handles '404' style errors and validation
 */
abstract class Base {
    /**
     * Validator instance
     * @access public
     * @var Forge\Validator
     */
    public $validation;

    /**
     * Nothing fancy, just check we are logged in: This is rather important
     */
    public function __construct() {
        if (\Forge\Auth::check() !== true) {
            throw new \Exception("You must be logged in to access this functionality");
        }
    }

    /**
     * Calls to class that do not exist will throw a '404' exception for consistency
     * @access public
     * @param string $method Method name
     * @param array $args Arguments
     * @throws \Exception
     */
    public function __call($method, $args) {
        throw new \Exception("The requested method does not exist: $method");
    }

    /**
     * Quick access validator. If validation fails, an exception will be thrown
     * @access protected
     * @param array $input Data to validate
     * @param array $rules List of rules to apply to data
     * @param array $messages List of optional messages
     * @return bool
     * @throws \Exception\Validation
     */
    protected function validate(array $input = array(), array $rules = array(), array $messages = array()) {
        $this->validation = new \Forge\Validator($input, $rules, $messages);

        if ($this->validation->fails()) throw new \Exception\Validation('Failed validation', $this->validation);

        return true;
    }

    /**
     * Raw date formatter
     * @access protected
     * @return object
     */
    protected function date($php = false) {
        if ($php) return \Formatter::date();
        else return \Forge\Database::raw('NOW()');
    }

        /**
     * required fields 'tag' , 'customer_id', 'request_description', 'request_subject', 'topic'
     */

    protected function createTicket($options) {

        $ticket = new \Ticket\Ticket;
        $topic = null;
        // get the topic id
        try {
            $topic = $ticket->getTopicId(array_only($options, array('tag', 'topic')));
        } catch (\Exception $e) {
            // just contiune no matching topic was found
            // so we will be setting it to zero
        }

        // swap topic for the topic id
        $options['topicid'] = 0;
        if (empty($topic) === false && $topic['status'] == 'COMPLETE') {
            $options['topicid'] = $topic['data']['topic_id'];
        }

        // make sure we have only the values we need
        // the osticket api thrown an error if none required variables are passed to it
        $options = array_only($options, array('tag' , 'customer_id', 'request_description', 'request_subject', 'topicid'));

        try {
            $response = $ticket->createSupportRequest($options);
            // just return the data
            // if anything goes wrong an exception will have been thrown
            $this->message = $response['data'];

        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            return false;
        }

        return $response['status'] == 'COMPLETE' ? true : false;
    }

}
