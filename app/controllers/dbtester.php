<?php

class Dbtester extends \Forge\Controller {

    public static function testDb() {
        
        $testDbModel = new \DbtestModel();
        $record = $testDbModel->getFirstRecord();
        
        $data = array(
            'record' => $record['data']
        );
        
        return Blade::make('db.test', $data);
        
    }

}