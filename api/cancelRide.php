<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';
$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true);
$rideId = isset($body['rideId']) ? (int)$body['rideId'] : 0;
if (!$rideId) { echo json_encode(['success'=>false,'message'=>'rideId manquant']); exit; }
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT chauffeur_id, status FROM rides WHERE id = ? FOR UPDATE");
    $stmt->execute([$rideId]);
    $ride = $stmt->fetch();
    if (!$ride) throw new Exception('Covoiturage introuvable');
    if ($user['role'] !== 'admin' && $user['user_id'] !== (int)$ride['chauffeur_id']) {
        throw new Exception('Tu n’es pas autorisé à annuler ce trajet');
    }
    if ($ride['status'] === 'cancelled') throw new Exception('Trajet déjà annulé');
    $stmt = $pdo->prepare("UPDATE rides SET status='cancelled' WHERE id = ?");
    $stmt->execute([$rideId]);
    $parts = $pdo->prepare("SELECT p.user_id, p.seats, r.prix FROM participations p JOIN rides r ON p.ride_id = r.id WHERE p.ride_id = ?");
    $parts->execute([$rideId]);
    $rows = $parts->fetchAll();
    foreach ($rows as $p) {
        $refund = $p['seats'] * $p['prix'];
        $stmt = $pdo->prepare("UPDATE users SET credits = credits + ? WHERE id = ?");
        $stmt->execute([$refund, $p['user_id']]);
    }
    $stmt = $pdo->prepare("UPDATE participations SET cancelled = 1 WHERE ride_id = ?");
    $stmt->execute([$rideId]);
    $pdo->commit();
    echo json_encode(['success'=>true,'message'=>'Trajet annulé et participants remboursés']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>