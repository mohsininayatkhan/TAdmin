<?php

return array(
    // Auth Driver: cookie/database
    // 'driver' => 'api\auth',
    'driver' => 'database',
    
    // Table name - Used in database sessions
    'table' => 'blf_livestatus',
    
    // Column name - Used in database sessions
    'id_column' => 'blf_id',
    
    // Username column
    'username' => 'guiuser',
    
    // Password column
    'password' => 'guisecret'
);
