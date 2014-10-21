<?php namespace API;

use \Exception\ModelRuleError;

class ModelRulesBase {
    private $message = null;
    protected $rule = array();

    public function __construct(array $rule = array()) {
        if (empty($rule)) {
            throw new ModelRuleError("No rule configured for class construction [" . __CLASS__ . "]");
        }

        $this->rule = $rule;
    }

    public function message($msg = null){
        if (empty($msg)) {
            return $this->message;
        }
        $this-> message = $msg;
    }

    protected function writeAudit($model, $data, $action, $response, $status) {
        if (empty($this->rule)) {
            throw new ModelRuleError(__METHOD__ . " the rule id not not set on model rule [{$model}]");
        }

        \Ad\Audit\TriggerSpyLog
            ::create(array(
                'rule' => $this->rule['name'],
                'model' => $model,
                'primary_key' => $data->primaryKey,
                'primary_key_value' => (isset($data[$data->primaryKey]) ? 0: $data[$data->primaryKey]),
                'data' => serialize($data),
                'action' => $action,
                'response' => serialize($response),
                'status' => $status
            ));

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
            file_put_contents('/tmp/ticketexception' . date('ymd') . '.log', print_r($e->getMessage(), true), FILE_APPEND);
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
            // if anything goes wrong an exception will be thrown
            $this->message = $response['data'];

        } catch (\Exception $e) {
            $this->message = $e->getMessage() . " @ " . $e->getLine() . " in " . $e->getFile();
            return 'FAILED';
        }
        return $response['status'];
    }

}