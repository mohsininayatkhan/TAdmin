<?php namespace Api;

class Model extends \Illuminate\Database\Eloquent\Model {
    // Last eloquent update set guarded to array(*) - This is problematic!
    public $guarded = array();
    public static $snakeAttributes = false;

    public function getDates() {
        return array();
    }

}