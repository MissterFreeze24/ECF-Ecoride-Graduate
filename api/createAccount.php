<?php
header('Content-Type: application/json');
require_once 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
$pseudo = trim($data['pseudo'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
if (!$pseudo || !$email || !$password) { echo json_encode(['success'=>false,'message'=>'Tous les champs sont requis']); exit; }
if (strlen($password) < 8) { echo json_encode(['success'=>false,'message'=>'Mot de passe trop court (<8)']); exit; }
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) { echo json_encode(['success'=>false,'message'=>'Email déjà utilisé']); exit; }
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (pseudo, email, password_hash, credits, created_at) VALUES (?, ?, ?, 20, NOW())");
$stmt->execute([$pseudo, $email, $hash]);
echo json_encode(['success'=>true,'message'=>'Compte créé']);
?>