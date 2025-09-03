<?php
require_once __DIR__ . '/../../src/db.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','employee'])) {
  json_response(['error'=>'unauthorized'], 401);
}
$stmt = db()->query("
  SELECT t.id, u.email AS driver_email, t.depart, t.arrivee, t.start_datetime, t.places
  FROM trips t JOIN users u ON u.id=t.driver_id
  ORDER BY t.start_datetime DESC
");
json_response($stmt->fetchAll());
