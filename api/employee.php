<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';
$employee = requireRole('employee');
$type = $_GET['type'] ?? 'problems';
try {
    if ($type === 'pending_reviews') {
        $stmt = $pdo->prepare("SELECT r.*, u.pseudo AS author_pseudo, rv.note, rv.comment, rv.created_at FROM reviews rv JOIN users u ON rv.user_id = u.id JOIN rides r ON rv.ride_id = r.id WHERE rv.validated = 0 ORDER BY rv.created_at DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]); exit;
    } elseif ($type === 'problems') {
        $stmt = $pdo->prepare("SELECT r.id, r.depart_city, r.arrivee_city, r.date_depart, r.status, u.pseudo as chauffeur FROM rides r JOIN users u ON r.chauffeur_id = u.id WHERE r.status = 'finished' ORDER BY r.date_depart DESC LIMIT 50");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]); exit;
    } else {
        echo json_encode(['success'=>false,'message'=>'Type non supporté']); exit;
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>