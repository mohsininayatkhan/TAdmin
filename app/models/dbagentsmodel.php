<?php

class DbAgentsModel extends \Model\Base {
    
    public function getAll() {
        
        $record = \AD\Agents\ForgeAgentsAD::all();
        
        return $record->toArray();
        
    }
    
}