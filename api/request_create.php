<?php
declare(strict_types=1);
require_once __DIR__.'/../config/auth.php';
require_role(['usuario','admin']);
header('Content-Type: application/json; charset=utf-8');

$lat = isset($_POST['lat']) ? (float)$_POST['lat'] : 0.0;
$lng = isset($_POST['lng']) ? (float)$_POST['lng'] : 0.0;
if (!$lat || !$lng) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'coords']); exit; }

$stmt = pdo()->prepare("INSERT INTO solicitudes (usuario_id, origen_lat, origen_lng) VALUES (?,?,?)");
$stmt->execute([user_id(), $lat, $lng]);
$id = (int) pdo()->lastInsertId();

log_interaccion(user_id(), 'solicitud_creada', ['solicitud_id'=>$id]);
echo json_encode(['ok'=>true, 'solicitud_id'=>$id]);
