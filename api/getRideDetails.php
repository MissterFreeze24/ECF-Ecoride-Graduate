<?php
header('Content-Type: application/json');
require_once 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { echo json_encode(['success'=>false,'message'=>'id manquant']); exit; }
$sql = "SELECT r.*, u.pseudo, u.photo, u.note_moyenne, v.marque, v.modele, v.energie FROM rides r JOIN users u ON r.chauffeur_id = u.id LEFT JOIN vehicles v ON v.id = r.vehicle_id WHERE r.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$ride = $stmt->fetch();
if (!$ride) { echo json_encode(['success'=>false,'message'=>'Covoiturage introuvable']); exit; }
$reviews = $pdo->prepare("SELECT rv.*, u.pseudo FROM reviews rv JOIN users u ON rv.user_id = u.id WHERE rv.ride_id = ?");
$reviews->execute([$id]);
echo json_encode(['success'=>true,'data'=>['ride'=>$ride,'reviews'=>$reviews->fetchAll()]]);
?>