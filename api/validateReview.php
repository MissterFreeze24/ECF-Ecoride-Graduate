<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';
$employee = requireRole('employee');
$body = json_decode(file_get_contents('php://input'), true);
$reviewId = isset($body['reviewId']) ? (int)$body['reviewId'] : 0;
$action = isset($body['action']) ? $body['action'] : '';
$adminComment = isset($body['comment']) ? trim($body['comment']) : null;
if (!$reviewId || !in_array($action, ['validate','reject'])) { echo json_encode(['success'=>false,'message'=>'Données invalides']); exit; }
try {
    if ($action === 'validate') {
        $stmt = $pdo->prepare("UPDATE reviews SET validated = 1 WHERE id = ?");
        $stmt->execute([$reviewId]);
        $msg = 'Avis validé';
    } else {
        $stmt = $pdo->prepare("UPDATE reviews SET validated = -1 WHERE id = ?");
        $stmt->execute([$reviewId]);
        $msg = 'Avis refusé';
    }
    $stmt = $pdo->prepare("INSERT INTO admin_actions (admin_id, action_type, details, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$employee['user_id'], 'review_'.$action, json_encode(['reviewId'=>$reviewId, 'comment'=>$adminComment])]);
    echo json_encode(['success'=>true,'message'=>$msg]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>