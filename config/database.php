<?php
// config/database.php
$envPath = __DIR__ . '/env.php';
$envExamplePath = __DIR__ . '/env.example.php';

$env = is_file($envPath) ? require $envPath : require $envExamplePath;
if (!is_array($env)) {
  throw new RuntimeException('Invalid config: env.php must return an associative array.');
}

$dsn = sprintf(
  'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
  $env['DB_HOST'] ?? '127.0.0.1',
  $env['DB_PORT'] ?? '3306',
  $env['DB_NAME'] ?? 'luxresort'
);

$pdo = new PDO($dsn, $env['DB_USER'] ?? 'root', $env['DB_PASS'] ?? '', [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

return $pdo;
