<?php
header('Content-Type: application/json');
require_once 'db.php';
$body = json_decode(file_get_contents('php://input'), true);
$rideId = isset($body['rideId']) ? (int)$body['rideId'] : 0;
$userId = isset($body['userId']) ? (int)$body['userId'] : 0;
$seats = isset($body['seats']) ? max(1,(int)$body['seats']) : 1;
if (!$rideId || !$userId) { echo json_encode(['success'=>false,'message'=>'Données manquantes']); exit; }
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT places_restantes, prix FROM rides WHERE id = ? FOR UPDATE");
    $stmt->execute([$rideId]);
    $ride = $stmt->fetch();
    if (!$ride) throw new Exception('Covoiturage introuvable');
    if ($ride['places_restantes'] < $seats) throw new Exception('Pas assez de places');
    $stmt = $pdo->prepare("SELECT credits FROM users WHERE id = ? FOR UPDATE");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) throw new Exception('Utilisateur introuvable');
    $totalPrice = $ride['prix'] * $seats;
    if ($user['credits'] < $totalPrice) throw new Exception('Crédits insuffisants');
    $stmt = $pdo->prepare("UPDATE users SET credits = credits - ? WHERE id = ?");
    $stmt->execute([$totalPrice, $userId]);
    $stmt = $pdo->prepare("UPDATE rides SET places_restantes = places_restantes - ? WHERE id = ?");
    $stmt->execute([$seats, $rideId]);
    $stmt = $pdo->prepare("INSERT INTO participations (ride_id, user_id, seats, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$rideId, $userId, $seats]);
    $pdo->commit();
    echo json_encode(['success'=>true,'message'=>'Participation confirmée']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>