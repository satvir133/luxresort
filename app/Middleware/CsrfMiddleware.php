<?php
return function($req,$res,$ctx){
  if($req->method()==='POST'){
    $token=$req->input('_csrf'); $valid=hash_equals(Session::get('_csrf')??'', $token??'');
    if(!$valid){ $res->status(419); return $res->text('CSRF token mismatch'); }
  }
  return true;
};
