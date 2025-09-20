<?php
header('Content-Type: application/json');
require_once 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
$chauffeur_id = (int)($data['chauffeur_id'] ?? 0);
$depart = $data['depart_city'] ?? '';
$arrivee = $data['arrivee_city'] ?? '';
$date = $data['date_depart'] ?? '';
$time_depart = $data['time_depart'] ?? '';
$time_arrivee = $data['time_arrivee'] ?? '';
$places = max(1,(int)($data['places'] ?? 1));
$prix = (float)($data['prix'] ?? 0);
$vehicle_id = isset($data['vehicle_id']) ? (int)$data['vehicle_id'] : null;
$ecologique = isset($data['ecologique']) && $data['ecologique'] ? 1 : 0;
if (!$chauffeur_id || !$depart || !$arrivee || !$date) { echo json_encode(['success'=>false,'message'=>'Données incomplètes']); exit; }
$stmt = $pdo->prepare("INSERT INTO rides (chauffeur_id, vehicle_id, depart_city, arrivee_city, date_depart, time_depart, time_arrivee, places_total, places_restantes, prix, ecologique, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->execute([$chauffeur_id, $vehicle_id, $depart, $arrivee, $date, $time_depart, $time_arrivee, $places, $places, $prix, $ecologique]);
echo json_encode(['success'=>true,'message'=>'Trajet créé']);
?>