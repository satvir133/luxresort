<?php
declare(strict_types=1);

// Core
require_once __DIR__.'/../core/Router.php';
require_once __DIR__.'/../core/Request.php';
require_once __DIR__.'/../core/Response.php';
require_once __DIR__.'/../core/Session.php';
require_once __DIR__.'/../core/View.php';
require_once __DIR__.'/../core/Helpers.php'; // <-- BASE_URL + url() are defined here (guarded)

// DO NOT define BASE_URL again here.

// Load env
$env = is_file(__DIR__.'/../config/env.php')
  ? require __DIR__.'/../config/env.php'
  : require __DIR__.'/../config/env.example.php';

if (!is_array($env)) { http_response_code(500); exit('Invalid env config.'); }

// Session
Session::init($env['SESSION_NAME'] ?? 'luxsess');

// DB (PDO)
try {
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
} catch (PDOException $e) {
  http_response_code(500);
  exit('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}

// Routes
require __DIR__.'/../app/routes.php';

// Dispatch
$req = Request::capture();
$res = new Response();
echo Router::dispatch($req, $res, compact('pdo','env'));
