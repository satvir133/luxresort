<?php
function url(string $path='/'): string {
  $p = '/' . ltrim($path, '/');
  return BASE_URL === '' ? $p : (BASE_URL . $p);
}
