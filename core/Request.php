<?php
class Request {
  public static function capture(){ return new self; }
  public function method(): string { $m=$_SERVER['REQUEST_METHOD'] ?? 'GET'; return $m==='HEAD'?'GET':$m; }
  public function path(): string {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($base && strpos($uri, $base) === 0) $uri = substr($uri, strlen($base));
    $uri = '/' . ltrim($uri, '/'); if ($uri !== '/') $uri = rtrim($uri, '/'); return $uri;
  }
  public function input($k=null){ return $k?($_POST[$k]??$_GET[$k]??null):array_merge($_GET,$_POST); }
  public function only(array $keys){ $all=$this->input(); return array_intersect_key($all,array_flip($keys)); }
}
