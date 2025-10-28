<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../config/auth.php';

header('Content-Type: application/json; charset=utf-8');
require_login();
require_role(['usuario']); // solo usuarios consultan conductores

$lat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$lng = isset($_GET['lng']) ? (float)$_GET['lng'] : null;
$km  = isset($_GET['km'])  ? max(0.2, (float)$_GET['km']) : 1.0; // default 1km

if ($lat === null || $lng === null) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'msg'=>'lat/lng requeridos']); exit;
}

$pdo = pdo();

/* Conductores = rol_id = 2. Filtramos en radio usando fórmula Haversine en SQL.
   6371 = radio tierra en km. */
$sql = "
  SELECT u.id, u.nombre, uc.disponible,
         ub.lat, ub.lng,
         (6371 * 2 * ASIN(SQRT(
            POWER(SIN(RADIANS(? - ub.lat)/2),2) +
            COS(RADIANS(?)) * COS(RADIANS(ub.lat)) *
            POWER(SIN(RADIANS(? - ub.lng)/2),2)
         ))) AS dist_km
  FROM usuarios u
  JOIN ubicaciones ub ON ub.usuario_id = u.id
  LEFT JOIN perfiles_conductor uc ON uc.usuario_id = u.id
  WHERE u.rol_id = 2
  HAVING dist_km <= ?
  ORDER BY dist_km ASC
  LIMIT 50
";
$st = $pdo->prepare($sql);
$st->execute([$lat, $lat, $lng, $km]);

echo json_encode(['ok'=>true,'drivers'=>$st->fetchAll()]);
