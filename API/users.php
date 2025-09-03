<?php
require_once __DIR__ . '/../../src/db.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','employee'])) {
  json_response(['error'=>'unauthorized'], 401);
}
$stmt = db()->query("SELECT id, email, role, suspended, credits FROM users ORDER BY id DESC");
json_response($stmt->fetchAll());
