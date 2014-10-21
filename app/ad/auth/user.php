<?php namespace Ad\Auth;

class User extends \Model {
    public $table = 'users';
    // public $connection = 'auth';
    public $primaryKey = 'user_id';
    public $timestamps = false;
}