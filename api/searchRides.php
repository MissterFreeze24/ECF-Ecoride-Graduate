<?php
header('Content-Type: application/json');
require_once 'db.php';
$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date'] ?? '';
$prixMax = isset($_GET['prixMax']) ? (float)$_GET['prixMax'] : null;
$ecologique = isset($_GET['ecologique']) && $_GET['ecologique'] === '1';
$noteMin = isset($_GET['noteMin']) ? (float)$_GET['noteMin'] : null;
$durationMax = isset($_GET['durationMax']) ? (int)$_GET['durationMax'] : null;
$sql = "SELECT r.id, r.depart_city, r.arrivee_city, r.date_depart, r.time_depart, r.time_arrivee, r.places_restantes, r.prix, r.ecologique, r.duree_min, u.pseudo, u.photo, u.note_moyenne FROM rides r JOIN users u ON r.chauffeur_id = u.id WHERE r.places_restantes > 0";
$params = [];
if ($depart !== '') { $sql .= " AND r.depart_city LIKE ?"; $params[] = "%".$depart."%"; }
if ($arrivee !== '') { $sql .= " AND r.arrivee_city LIKE ?"; $params[] = "%".$arrivee."%"; }
if ($date !== '') { $sql .= " AND r.date_depart = ?"; $params[] = $date; }
if ($prixMax !== null) { $sql .= " AND r.prix <= ?"; $params[] = $prixMax; }
if ($ecologique) { $sql .= " AND r.ecologique = 1"; }
if ($noteMin !== null) { $sql .= " AND u.note_moyenne >= ?"; $params[] = $noteMin; }
if ($durationMax !== null) { $sql .= " AND r.duree_min <= ?"; $params[] = $durationMax; }
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
echo json_encode(['success'=>true, 'data'=>$rows]);
?>