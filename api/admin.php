<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth.php';
$admin = requireRole('admin');
$action = $_GET['action'] ?? 'stats';
try {
    if ($action === 'stats') {
        $stmt = $pdo->prepare("SELECT DATE(created_at) as day, COUNT(*) as rides_count FROM rides WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY day ASC");
        $stmt->execute();
        $ridesByDay = $stmt->fetchAll();
        $stmt = $pdo->prepare("SELECT DATE(p.created_at) as day, SUM(r.prix * p.seats) as total_revenue FROM participations p JOIN rides r ON p.ride_id = r.id WHERE p.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(p.created_at) ORDER BY day ASC");
        $stmt->execute();
        $revenueByDay = $stmt->fetchAll();
        $stmt = $pdo->prepare("SELECT SUM(2 * 1) as credits_total FROM participations");
        $stmt->execute();
        $totalPlatformCredits = $stmt->fetchColumn();
        echo json_encode(['success'=>true, 'data'=>['ridesByDay' => $ridesByDay, 'revenueByDay' => $revenueByDay, 'totalPlatformCredits' => $totalPlatformCredits]]);
        exit;
    }
    if ($action === 'suspendUser') {
        $body = json_decode(file_get_contents('php://input'), true);
        $userId = isset($body['userId']) ? (int)$body['userId'] : 0;
        if (!$userId) throw new Exception('userId manquant');
        $stmt = $pdo->prepare("UPDATE users SET role = 'suspended' WHERE id = ?");
        $stmt->execute([$userId]);
        echo json_encode(['success'=>true,'message'=>'Compte suspendu']); exit;
    }
    if ($action === 'restoreUser') {
        $body = json_decode(file_get_contents('php://input'), true);
        $userId = isset($body['userId']) ? (int)$body['userId'] : 0;
        if (!$userId) throw new Exception('userId manquant');
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
        $stmt->execute([$userId]);
        echo json_encode(['success'=>true,'message'=>'Compte restauré']); exit;
    }
    echo json_encode(['success'=>false,'message'=>'Action inconnue']);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>