<?php
header('Content-Type: application/json');
require_once 'db.php';
$headers = getallheaders();
$token = $headers['Authorization'] ?? $headers['authorization'] ?? '';
if (strpos($token, 'Bearer ') === 0) $token = substr($token, 7);
if (!$token) { echo json_encode(['success'=>false,'message'=>'Token manquant']); exit; }
$stmt = $pdo->prepare("SELECT user_id FROM api_tokens WHERE token = ?");
$stmt->execute([$token]);
$tk = $stmt->fetch();
if (!$tk) { echo json_encode(['success'=>false,'message'=>'Token invalide']); exit; }
$userId = $tk['user_id'];
$userStmt = $pdo->prepare("SELECT id,pseudo,email,credits FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();
$parts = $pdo->prepare("SELECT p.*, r.depart_city, r.arrivee_city, r.date_depart FROM participations p JOIN rides r ON p.ride_id = r.id WHERE p.user_id = ?");
$parts->execute([$userId]);
echo json_encode(['success'=>true,'data'=>['user'=>$user, 'participations'=>$parts->fetchAll()]]);
?>