<?php
header('Content-Type: application/json');
require_once 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
if (!$email || !$password) { echo json_encode(['success'=>false,'message'=>'Champs manquants']); exit; }
$stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password_hash'])) { echo json_encode(['success'=>false,'message'=>'Identifiants invalides']); exit; }
$token = bin2hex(random_bytes(32));
$stmt = $pdo->prepare("INSERT INTO api_tokens (user_id, token, created_at) VALUES (?, ?, NOW())");
$stmt->execute([$user['id'], $token]);
echo json_encode(['success'=>true,'token'=>$token, 'userId'=>$user['id']]);
?>