<?php
require_once __DIR__ . '/../../src/db.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','employee'])) {
  json_response(['error'=>'unauthorized'], 401);
}
$pdo = db();
// total credits: 2 per confirmed booking
$total = (int)($pdo->query("SELECT COUNT(*)*2 AS c FROM bookings WHERE status='confirmed'")->fetch()['c'] ?? 0);
// by day trips and credits for last 14 days
$stmt = $pdo->query("
  SELECT DATE(start_datetime) AS d, COUNT(*) AS trips
  FROM trips
  WHERE start_datetime >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
  GROUP BY d ORDER BY d
");
$rows = $stmt->fetchAll();
$labels = []; $tripCounts = []; $creditCounts = [];
foreach ($rows as $r) {
  $labels[] = $r['d'];
  $tripCounts[] = (int)$r['trips'];
  $stmt2 = $pdo->prepare("SELECT COUNT(*)*2 AS c FROM bookings WHERE status='confirmed' AND DATE(created_at)=?");
  $stmt2->execute([$r['d']]);
  $creditCounts[] = (int)$stmt2->fetch()['c'];
}
json_response([ 'total_credits' => $total, 'by_day' => ['labels'=>$labels, 'trips'=>$tripCounts, 'credits'=>$creditCounts] ]);
