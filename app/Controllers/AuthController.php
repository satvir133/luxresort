<?php
require_once __DIR__.'/../../core/Session.php';

class AuthController {
  public function showLogin($req,$res,$ctx){ return $res->view('auth/login.php'); }

  public function login($req,$res,$ctx){
    $email = trim($req->input('email')); $pass = $req->input('password');
    // simple demo auth: lookup user by email
    $st=$ctx['pdo']->prepare("SELECT u.*, GROUP_CONCAT(r.name) roles
                              FROM users u
                              LEFT JOIN user_roles ur ON ur.user_id=u.id
                              LEFT JOIN roles r ON r.id=ur.role_id
                              WHERE email=? GROUP BY u.id");
    $st->execute([$email]); $u=$st->fetch();
    if(!$u || !password_verify($pass, $u['password_hash'] ?? '')) {
      Session::flash('error','Invalid credentials'); return $res->redirect('/login');
    }
    $roles = $u['roles'] ? explode(',', $u['roles']) : ['guest'];
    Session::regenerate();
    Session::put('user', ['id'=>$u['id'],'email'=>$u['email'],'name'=>trim(($u['first_name']??'').' '.($u['last_name']??'')),'roles'=>$roles]);
    if(in_array('admin',$roles)) return $res->redirect('/admin');
    if(in_array('staff',$roles)) return $res->redirect('/staff');
    return $res->redirect('/dashboard');
  }

  public function showRegister($req,$res,$ctx){ return $res->view('auth/register.php'); }

  public function register($req,$res,$ctx){
    $data=$req->only(['first_name','last_name','email','phone','password']);
    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['password'])<8){
      Session::flash('error','Valid email + 8+ char password required.'); return $res->redirect('/register');
    }
    $st=$ctx['pdo']->prepare("INSERT INTO users(email,phone,password_hash,first_name,last_name) VALUES(?,?,?,?,?)");
    $st->execute([$data['email'],$data['phone']??null,password_hash($data['password'], PASSWORD_DEFAULT),$data['first_name']??null,$data['last_name']??null]);
    $uid=(int)$ctx['pdo']->lastInsertId();
    $ctx['pdo']->prepare("INSERT INTO user_roles(user_id,role_id) SELECT ?, id FROM roles WHERE name='guest'")->execute([$uid]);
    Session::flash('success','Registered. Please login.'); return $res->redirect('/login');
  }

  public function logout($req,$res,$ctx){ Session::flush(); return $res->redirect('/login'); }
}
