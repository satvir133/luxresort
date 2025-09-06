<?php
require_once __DIR__.'/../../core/Session.php';
return function($req,$res,$ctx){
  if(!Session::get('user')){ $res->redirect('/login'); return false; }
  return true;
};
