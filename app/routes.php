<?php
require_once __DIR__.'/Controllers/AuthController.php';
require_once __DIR__.'/Controllers/DashboardController.php';
require_once __DIR__.'/Controllers/PageController.php';
require_once __DIR__.'/Controllers/BookingController.php';


$auth  = require __DIR__.'/Middleware/AuthMiddleware.php';
$roleM = require __DIR__.'/Middleware/RoleMiddleware.php';
$adminOrStaff = $roleM(['admin','staff']);
$adminOnly    = $roleM(['admin']);
$staffM = $roleM(['staff','admin']);
$adminM = $roleM(['admin']);

// Landing / Home
Router::get('/', function($req,$res,$ctx){
  $u = Session::get('user');
  if($u){
    if(in_array('admin',$u['roles']??[])) return $res->redirect('/admin');
    if(in_array('staff',$u['roles']??[])) return $res->redirect('/staff');
    return $res->redirect('/dashboard');
  }
  return (new PageController)->home($req,$res,$ctx);
});
Router::post('/lead', [ (new PageController),'lead' ]);

// Auth
Router::get('/login',    [ (new AuthController),'showLogin' ]);
Router::post('/login',   [ (new AuthController),'login' ]);
Router::get('/register', [ (new AuthController),'showRegister' ]);
Router::post('/register',[ (new AuthController),'register' ]);
Router::get('/logout',   [ (new AuthController),'logout' ], [$auth]);

// Dashboards
Router::get('/dashboard',[ (new DashboardController),'guest' ], [$auth]);
Router::get('/staff',    [ (new DashboardController),'staff' ], [$adminOrStaff]);
Router::get('/admin',    [ (new DashboardController),'admin' ], [$adminOnly]);

Router::get('/booking',   [ (new BookingController),'showForm' ]);
Router::post('/booking/search', [ (new BookingController),'search' ]);
Router::post('/booking/confirm', [ (new BookingController),'book' ]);
Router::post('/booking/cancel', [ (new BookingController),'cancel' ], [$auth]); // NEW

/* Dashboards */
Router::get('/dashboard', [ (new DashboardController),'guest' ], [$auth]);          // Guest
Router::get('/staff',     [ (new DashboardController),'staff' ], [$staffM]);        // Staff
Router::post('/staff/booking/status', [ (new DashboardController),'staffSetStatus' ], [$staffM]);

Router::get('/admin',     [ (new DashboardController),'admin' ], [$adminM]);        // Admin
Router::post('/admin/room/create', [ (new DashboardController),'adminCreateRoom' ], [$adminM]);
Router::post('/admin/room/status', [ (new DashboardController),'adminSetRoomStatus' ], [$adminM]);