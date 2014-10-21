<?php

class DbTestModel extends \Model\Base {
    
    public function getFirstRecord() {
        
        $record = \AD\Test\ForgeTestAD::first();
        
        return array('status' => 'COMPLETE', 'data' => $record->toArray());
        
    }
    
}