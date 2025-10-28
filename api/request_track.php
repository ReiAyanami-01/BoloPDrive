<?php
declare(strict_types=1);

require_once __DIR__.'/../config/auth.php';  // incluye config y sesión
require_once __DIR__.'/../lib/geo.php';      // haversine_km y eta_minutos
header('Content-Type: application/json; charset=utf-8');

require_login();

$viajeId = (int)($_GET['viaje_id'] ?? 0);
if ($viajeId <= 0) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'viaje_id requerido']); exit;
}

// ------ helpers mínimos (si ya los tienes, usa los tuyos) ------
function getViaje(int $id): ?array {
  $pdo = pdo();
  $sql = "SELECT v.*, 
                 u.nombre  AS usuario_nombre,
                 c.nombre  AS conductor_nombre
          FROM viajes v
          JOIN usuarios u ON u.id = v.usuario_id
          JOIN usuarios c ON c.id = v.conductor_id
          WHERE v.id = ? LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([$id]);
  $row = $st->fetch();
  return $row ?: null;
}

function getUbicacion(int $usuarioId): ?array {
  $pdo = pdo();
  $st = $pdo->prepare("SELECT lat,lng,actualizado_en FROM ubicaciones WHERE usuario_id=? LIMIT 1");
  $st->execute([$usuarioId]);
  $row = $st->fetch();
  return $row ?: null;
}
// ---------------------------------------------------------------

$viaje = getViaje($viajeId);
if (!$viaje) {
  http_response_code(404);
  echo json_encode(['ok'=>false,'error'=>'Viaje no encontrado']); exit;
}

// Control de acceso: solo el usuario, el conductor de ese viaje o admin
$esAdmin     = (user_role() === 'admin');
$esUsuario   = (user_role() === 'usuario'  && user_id() === (int)$viaje['usuario_id']);
$esConductor = (user_role() === 'conductor'&& user_id() === (int)$viaje['conductor_id']);
if (!$esAdmin && !$esUsuario && !$esConductor) {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'Sin permiso']); exit;
}

$ubicConductor = getUbicacion((int)$viaje['conductor_id']);
$ubicUsuario   = getUbicacion((int)$viaje['usuario_id']);

if (!$ubicConductor || !$ubicUsuario) {
  // Aún no han enviado ubicación
  echo json_encode([
    'ok'=>true,
    'viaje_id'=>$viajeId,
    'estado'=>$viaje['estado'],
    'conductor'=>$ubicConductor, // puede venir null
    'usuario'=>$ubicUsuario,     // puede venir null
    'dist_km'=>null,
    'eta_min'=>null,
    'msg'=>'Ubicación no disponible aún'
  ]);
  exit;
}

$dist = haversine_km(
  (float)$ubicConductor['lat'],
  (float)$ubicConductor['lng'],
  (float)$ubicUsuario['lat'],
  (float)$ubicUsuario['lng']
);

// Usa la constante global definida en config.php
$eta  = eta_minutos($dist, (defined('VEL_PROMEDIO_CIUDAD_KMH') ? VEL_PROMEDIO_CIUDAD_KMH : 20));

echo json_encode([
  'ok'=>true,
  'viaje_id'=>$viajeId,
  'estado'=>$viaje['estado'],
  'conductor'=>$ubicConductor,
  'usuario'=>$ubicUsuario,
  'dist_km'=>round($dist, 2),
  'eta_min'=>$eta
]);
require_once __DIR__ . '/../lib/geo.php';
