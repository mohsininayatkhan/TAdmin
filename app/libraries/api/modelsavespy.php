<?php namespace Api;

class ModelSaveSpy extends \Illuminate\Database\Eloquent\Model {
    // Last eloquent update set guarded to array(*) - This is problematic!
    public $guarded = array();
    public static $snakeAttributes = false;

    public function getDates() {
        return array();
    }

    public static function boot() {

        // function to trigger the event action system
        static::saving(function($model) {
            \Forge\Event::fire('model.trigger', array($model, 'saving'));
        });

    }

}