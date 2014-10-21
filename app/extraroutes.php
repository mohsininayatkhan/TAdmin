<?php
//Extra routes should be included here.
Router::register(array('login'), function(){
   return \Login::index();
});