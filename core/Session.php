<?php
class Session {
  public static function init(string $name): void {
    if (session_status()===PHP_SESSION_ACTIVE) return;
    if ($name && $name!==session_name()) session_name($name);
    session_start();
  }
  public static function put(string $k,$v): void { $_SESSION[$k]=$v; }
  public static function get(string $k,$d=null){ return $_SESSION[$k]??$d; }
  public static function flash(string $k,$v): void { $_SESSION['_flash'][$k]=$v; }
  public static function flash_get(string $k){ $v=$_SESSION['_flash'][$k]??null; unset($_SESSION['_flash'][$k]); return $v; }
  public static function regenerate(): void { if(session_status()===PHP_SESSION_ACTIVE) session_regenerate_id(true); }
  public static function flush(): void { if(session_status()===PHP_SESSION_ACTIVE){ $_SESSION=[]; session_destroy(); } }
}
