<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../config/auth.php';

header('Content-Type: application/json; charset=utf-8');
require_login();

$uid = user_id();
$lat = isset($_POST['lat']) ? (float)$_POST['lat'] : null;
$lng = isset($_POST['lng']) ? (float)$_POST['lng'] : null;

if ($lat === null || $lng === null) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'msg'=>'lat/lng requeridos']);
  exit;
}

$pdo = pdo();
$st = $pdo->prepare("
  INSERT INTO ubicaciones (usuario_id, lat, lng, updated_at)
  VALUES (?, ?, ?, NOW())
  ON DUPLICATE KEY UPDATE lat=VALUES(lat), lng=VALUES(lng), updated_at=NOW()
");
$st->execute([$uid, $lat, $lng]);

echo json_encode(['ok'=>true]);
