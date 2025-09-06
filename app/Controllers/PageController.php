<?php
require_once __DIR__.'/../../core/Session.php';

class PageController {
  public function home($req,$res,$ctx){
    // Generate CSRF once and store in session
    $csrf = Session::get('_csrf');
    if (!$csrf) {
      $csrf = bin2hex(random_bytes(16));
      Session::put('_csrf', $csrf);
    }
    return $res->view('home/index.php', compact('csrf'));
  }

  public function lead($req,$res,$ctx){
    $name  = trim($req->input('name') ?? '');
    $email = trim($req->input('email') ?? '');
    $token = $req->input('_csrf') ?? '';

    if (!hash_equals(Session::get('_csrf') ?? '', $token)) {
      Session::flash('error','Security check failed. Please try again.');
      return $res->redirect('/');
    }
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      Session::flash('error','Please provide a valid name and email.');
      return $res->redirect('/');
    }

    // Ensure leads table exists (idempotent)
    $ctx['pdo']->exec("CREATE TABLE IF NOT EXISTS leads(
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(120) NOT NULL,
      email VARCHAR(190) NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Save lead
    $st = $ctx['pdo']->prepare("INSERT INTO leads(name,email,created_at) VALUES(?,?,NOW())");
    try {
      $st->execute([$name,$email]);
      Session::flash('success','Thanks! We’ll get back to you soon.');
    } catch (Throwable $e) {
      Session::flash('error','Could not save your request. Please try later.');
    }
    return $res->redirect('/');
  }
}
