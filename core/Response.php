<?php
class Response {
  public function status(int $c){ http_response_code($c); }

  public function redirect(string $to){
    if (isset($to[0]) && $to[0] === '/' && defined('BASE_URL') && BASE_URL !== '') {
      $to = BASE_URL . $to;
    }
    header("Location: $to");
    exit;
  }

  public function view(string $view, array $data = []) {
    // Base views directory
    $base = realpath(__DIR__ . '/../app/Views');
    if ($base === false) {
      http_response_code(500);
      echo 'Views directory not found.';
      return;
    }

    // Normalize the given view path
    $file = $view;

    // If it's not an absolute path, make it relative to /app/Views
    if (!preg_match('#^([a-zA-Z]:\\\\|/)|^\\\\#', $file)) {
      $file = $base . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file), DIRECTORY_SEPARATOR);
    }

    // Append .php if missing
    if (substr($file, -4) !== '.php') {
      $file .= '.php';
    }

    if (!is_file($file)) {
      http_response_code(500);
      echo 'View not found: ' . htmlspecialchars($file);
      return;
    }

    // Render
    extract($data, EXTR_SKIP);
    include $file;
  }

  public function text(string $s){ echo $s; }
  public function body(){}
}
