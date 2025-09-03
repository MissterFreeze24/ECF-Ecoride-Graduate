<?php
require_once __DIR__ . '/../../src/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  json_response(['error'=>'unauthorized'], 401);
}
$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);
$act = $input['act'] ?? 'suspend';
$stmt = db()->prepare("UPDATE users SET suspended=? WHERE id=?");
$stmt->execute([$act==='suspend'?1:0, $id]);
json_response(['ok'=>true]);
