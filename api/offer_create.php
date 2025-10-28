<?php
declare(strict_types=1);
require_once __DIR__.'/../config/auth.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$solicitud_id   = isset($_POST['solicitud_id']) ? (int)$_POST['solicitud_id'] : 0;
$precio         = isset($_POST['precio']) ? round((float)$_POST['precio'], 2) : 0.0;
$conductor_id__ = isset($_POST['conductor_id']) ? (int)$_POST['conductor_id'] : 0; // opcional si usuario oferta a un driver específico

if ($solicitud_id<=0 || $precio<=0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'bad']); exit; }

$pdo = pdo();
if (user_role()==='conductor') {
  // Conductor oferta a una solicitud abierta
  $stmt = $pdo->prepare("INSERT INTO ofertas (solicitud_id, conductor_id, precio) VALUES (?,?,?)");
  $stmt->execute([$solicitud_id, user_id(), $precio]);
} else {
  // Usuario propone a un conductor concreto (opcional)
  if ($conductor_id__<=0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'conductor_id']); exit; }
  $stmt = $pdo->prepare("INSERT INTO ofertas (solicitud_id, conductor_id, precio) VALUES (?,?,?)");
  $stmt->execute([$solicitud_id, $conductor_id__, $precio]);
}

log_interaccion(user_id(), 'oferta', ['solicitud_id'=>$solicitud_id,'precio'=>$precio]);
echo json_encode(['ok'=>true]);
