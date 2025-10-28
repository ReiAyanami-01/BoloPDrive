<?php
declare(strict_types=1);
require_once __DIR__ . '/secrets.php';
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'BDboloPdrive');
define('DB_USER', 'root');
define('DB_PASS', '');

// 👇 MUY IMPORTANTE: en XAMPP, tu app está en /BoloPDrive/public
define('BASE_URL', '/BoloPDrive/public');

function pdo(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

// (opcional) si quieres que geo.php esté disponible en toda la app:
require_once __DIR__ . '/../lib/geo.php';
ini_set('display_errors', '1');
error_reporting(E_ALL);
