<?php
class View {
  public static function render(string $path, array $data = []) {
    extract($data);
    ob_start();
    include $path;
    return ob_get_clean();
  }
}
