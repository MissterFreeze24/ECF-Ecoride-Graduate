<?php
require_once __DIR__ . '/db.php';
function getUserFromToken($pdo, $token) {
    if (!$token) return null;
    $stmt = $pdo->prepare("SELECT t.user_id, u.role FROM api_tokens t JOIN users u ON t.user_id = u.id WHERE t.token = ? LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    return $row ? ['user_id' => (int)$row['user_id'], 'role' => $row['role']] : null;
}
function requireAuth() {
    global $pdo;
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (strpos($token, 'Bearer ') === 0) $token = substr($token, 7);
    $user = getUserFromToken($pdo, $token);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success'=>false, 'message'=>'Token manquant ou invalide']);
        exit;
    }
    return $user;
}
function requireRole($neededRole) {
    $user = requireAuth();
    $rolesHierarchy = ['user' => 1, 'employee' => 2, 'admin' => 3];
    if ($rolesHierarchy[$user['role']] < $rolesHierarchy[$neededRole]) {
        http_response_code(403);
        echo json_encode(['success'=>false, 'message'=>'Accès refusé (rôle insuffisant)']);
        exit;
    }
    return $user;
}
?>