<?php
require_once __DIR__.'/../../core/Session.php';
return function(array $roles){
  return function($req,$res,$ctx) use($roles){
    $u = Session::get('user'); $have = $u['roles'] ?? [];
    if(!$u || !array_intersect($have,$roles)){ $res->status(403); return $res->text('Forbidden'); }
    return true;
  };
};
