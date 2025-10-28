<?php
declare(strict_types=1);
require_once __DIR__.'/../config/auth.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$oferta_id = isset($_POST['oferta_id']) ? (int)$_POST['oferta_id'] : 0;
if ($oferta_id<=0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'bad']); exit; }

$pdo = pdo();
$of = $pdo->prepare(
  "SELECT o.*, s.usuario_id
   FROM ofertas o
   JOIN solicitudes s ON s.id=o.solicitud_id
   WHERE o.id=? LIMIT 1"
);
$of->execute([$oferta_id]);
$oferta = $of->fetch();
if (!$oferta) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'no_oferta']); exit; }

// Permisos: usuario dueño de la solicitud, conductor de la oferta, o admin
$permitido = (user_role()==='admin')
          || (user_role()==='usuario'   && user_id()===(int)$oferta['usuario_id'])
          || (user_role()==='conductor' && user_id()===(int)$oferta['conductor_id']);
if (!$permitido) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit; }

$pdo->beginTransaction();
$pdo->prepare("UPDATE ofertas SET estado='aceptada' WHERE id=?")->execute([$oferta_id]);
$pdo->prepare("UPDATE ofertas SET estado='expirada' WHERE solicitud_id=? AND id<>? AND estado='propuesta'")
    ->execute([$oferta['solicitud_id'], $oferta_id]);
$pdo->prepare("UPDATE solicitudes SET estado='tomada' WHERE id=?")->execute([$oferta['solicitud_id']]);
$pdo->prepare("INSERT INTO viajes (solicitud_id, usuario_id, conductor_id, precio_acordado) VALUES (?,?,?,?)")
    ->execute([$oferta['solicitud_id'], $oferta['usuario_id'], $oferta['conductor_id'], $oferta['precio']]);
$viaje_id = (int)$pdo->lastInsertId();
$pdo->commit();

log_interaccion(user_id(), 'oferta_aceptada', ['oferta_id'=>$oferta_id,'viaje_id'=>$viaje_id]);
echo json_encode(['ok'=>true,'viaje_id'=>$viaje_id]);
