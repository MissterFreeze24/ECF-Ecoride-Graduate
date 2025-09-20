<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';
$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true);
$rideId = isset($body['rideId']) ? (int)$body['rideId'] : 0;
$action = isset($body['action']) ? $body['action'] : '';
if (!$rideId || !in_array($action, ['start','finish'])) { echo json_encode(['success'=>false,'message'=>'Données invalides']); exit; }
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT chauffeur_id, status FROM rides WHERE id = ? FOR UPDATE");
    $stmt->execute([$rideId]);
    $ride = $stmt->fetch();
    if (!$ride) throw new Exception('Covoiturage introuvable');
    if ($user['role'] !== 'admin' && $user['user_id'] !== (int)$ride['chauffeur_id']) throw new Exception('Tu n’es pas autorisé sur ce trajet');
    if ($action === 'start') {
        if ($ride['status'] !== 'scheduled') throw new Exception('Impossible de démarrer: statut incorrect');
        $stmt = $pdo->prepare("UPDATE rides SET status='started' WHERE id = ?");
        $stmt->execute([$rideId]);
        $message = 'Trajet démarré';
    } else {
        if ($ride['status'] !== 'started') throw new Exception('Impossible de clôturer: statut incorrect');
        $stmt = $pdo->prepare("UPDATE rides SET status='finished' WHERE id = ?");
        $stmt->execute([$rideId]);
        $message = 'Trajet terminé - demande de validation envoyée aux participants';
    }
    $pdo->commit();
    echo json_encode(['success'=>true,'message'=>$message]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>