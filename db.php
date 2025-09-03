<?php
declare(strict_types=1);
$DB_HOST = getenv('MYSQL_HOST') ?: 'db';
$DB_NAME = getenv('MYSQL_DATABASE') ?: 'ecoride';
$DB_USER = getenv('MYSQL_USER') ?: 'ecoride';
$DB_PASS = getenv('MYSQL_PASSWORD') ?: 'ecoride';
function db(): PDO {
  static $pdo = null;
  global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
  if ($pdo === null) {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}
function json_response($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}
session_start();
?>