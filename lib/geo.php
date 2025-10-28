<?php
declare(strict_types=1);

/**
 * Distancia Haversine en kilómetros entre (lat1,lng1) y (lat2,lng2).
 */
function haversine_km(float $lat1, float $lng1, float $lat2, float $lng2): float {
  $lat1 = deg2rad($lat1); $lng1 = deg2rad($lng1);
  $lat2 = deg2rad($lat2); $lng2 = deg2rad($lng2);
  $dlat = $lat2 - $lat1; $dlng = $lng2 - $lng1;

  $a = sin($dlat/2) ** 2 + cos($lat1) * cos($lat2) * sin($dlng/2) ** 2;
  $c = 2 * asin(min(1, sqrt($a)));
  return 6371 * $c; // Radio promedio de la Tierra en km
}

/**
 * ETA (minutos) dado una distancia en km y velocidad km/h.
 * Si la velocidad es <= 0, usa 20 km/h por defecto (tráfico urbano).
 */
function eta_minutos(float $dist_km, float $vel_kmh = 20): int {
  if ($vel_kmh <= 0) $vel_kmh = 20;
  return max(1, (int) round(($dist_km / $vel_kmh) * 60));
}

//require_once __DIR__ . '/../lib/geo.php';
