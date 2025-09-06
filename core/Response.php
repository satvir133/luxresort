<?php
class Response {
  public function status(int $c){ http_response_code($c); }
  public function redirect(string $to){
    // Prepend BASE_URL for app-relative paths like "/login"
    if (isset($to[0]) && $to[0] === '/' && defined('BASE_URL') && BASE_URL !== '') {
      $to = BASE_URL . $to;
    }
    header("Location: $to");
    exit;
  }
  public function view(string $view, array $data=[]){ extract($data); include __DIR__."/../app/Views/$view"; }
  public function text(string $s){ echo $s; }
  public function body(){}
}
