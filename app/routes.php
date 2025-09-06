<?php
require_once __DIR__.'/Controllers/AuthController.php';
require_once __DIR__.'/Controllers/DashboardController.php';

$auth  = require __DIR__.'/Middleware/AuthMiddleware.php';
$roleM = require __DIR__.'/Middleware/RoleMiddleware.php';
$adminOrStaff = $roleM(['admin','staff']);
$adminOnly    = $roleM(['admin']);

Router::get('/', function($req,$res,$ctx){
  $u = Session::get('user');
  if(!$u) return $res->redirect('/login');
  if(in_array('admin',$u['roles'])) return $res->redirect('/admin');
  if(in_array('staff',$u['roles'])) return $res->redirect('/staff');
  return $res->redirect('/dashboard');
});

Router::get('/login',    [ (new AuthController),'showLogin' ]);
Router::post('/login',   [ (new AuthController),'login' ]);
Router::get('/register', [ (new AuthController),'showRegister' ]);
Router::post('/register',[ (new AuthController),'register' ]);
Router::get('/logout',   [ (new AuthController),'logout' ], [$auth]);

Router::get('/dashboard',[ (new DashboardController),'guest' ], [$auth]);
Router::get('/staff',    [ (new DashboardController),'staff' ], [$adminOrStaff]);
Router::get('/admin',    [ (new DashboardController),'admin' ], [$adminOnly]);
