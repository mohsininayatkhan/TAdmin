<?php

return array(
   'log' => array(
      'level' => 10,
      'format'    => "[{dD}-{dM}-{dY} {dH}:{dI}:{dS}{dZ}] #{ID} {SRC}->",
      'sender'    => 'System',
      'file'  => date('Ymd').'.log',
   ),
   
   'vbose' => true,
   'logging' => true,
   
   // Exception detailed errors
   'detail' => array(
      'payment' => true,
      'sql' => true,
      'validation' => true,
   )
);
