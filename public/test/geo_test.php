<?php
declare(strict_types=1);

// Si en config ya incluyes geo.php de forma global, con esto basta:
require_once __DIR__ . '/../../config/config.php';

// Si NO lo incluyes globalmente en config.php, descomenta la siguiente línea:
// require_once __DIR__ . '/../../lib/geo.php';

header('Content-Type: application/json; charset=utf-8');

// Lee parámetros de la URL (con valores por defecto para probar rápido)
$lat1 = isset($_GET['lat1']) ? (float)$_GET['lat1'] : 13.692940;   // San Salvador aprox
$lng1 = isset($_GET['lng1']) ? (float)$_GET['lng1'] : -89.218191;
$lat2 = isset($_GET['lat2']) ? (float)$_GET['lat2'] : 13.700000;   // punto cercano
$lng2 = isset($_GET['lng2']) ? (float)$_GET['lng2'] : -89.220000;

// Calcula distancia y ETA
$dist = haversine_km($lat1, $lng1, $lat2, $lng2);

// Usa la constante si existe; si no, 20 km/h
$velocidad = defined('VEL_PROMEDIO_CIUDAD_KMH') ? VEL_PROMEDIO_CIUDAD_KMH : 20;
$eta = eta_minutos($dist, $velocidad);

// Respuesta JSON
echo json_encode([
  'ok'        => true,
  'punto_origen'      => ['lat'=>$lat1, 'lng'=>$lng1],
  'punto_destino'     => ['lat'=>$lat2, 'lng'=>$lng2],
  'distancia_km'      => round($dist, 3),
  'velocidad_km_h'    => (int)$velocidad,
  'eta_minutos'       => $eta,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
