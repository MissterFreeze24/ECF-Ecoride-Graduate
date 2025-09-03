<?php
require_once __DIR__ . '/../../src/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  json_response(['error'=>'unauthorized'], 401);
}
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_response(['error'=>'email invalide'], 422);
$pass = bin2hex(random_bytes(6));
$hash = password_hash($pass, PASSWORD_BCRYPT);
$stmt = db()->prepare("INSERT INTO users (email, password_hash, role, credits) VALUES (?, ?, 'employee', 0)");
$stmt->execute([$email, $hash]);
json_response(['ok'=>true, 'email'=>$email, 'password'=>$pass]);
