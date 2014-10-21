<?php namespace Api;

class TicketModel extends Model{

    public function __construct(array $attributes = array()) {
        $this->switchConnection();
        parent::__construct($attributes);

    }

    private function switchConnection() {
        $name = \Forge\Config::get('ticketbox.connection');
        if (empty($name) === false) {
            $this->setConnection($name);
        }
    }
}
