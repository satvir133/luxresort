<?php
if (!defined('BASE_URL')) {
  $scriptDir = str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
  define('BASE_URL', rtrim($scriptDir, '/'));
}
if (!function_exists('url')) {
  function url(string $path = '/'): string {
    $p = '/' . ltrim($path, '/');
    return (defined('BASE_URL') && BASE_URL !== '') ? (BASE_URL . $p) : $p;
  }
}
