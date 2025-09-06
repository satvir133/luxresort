<?php
function url(string $path='/'): string {
  $p = '/' . ltrim($path, '/');
  return (defined('BASE_URL') && BASE_URL !== '') ? (BASE_URL . $p) : $p;
}
