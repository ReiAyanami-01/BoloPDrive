<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth.php';

require_login();
require_role(['conductor']);

$uid           = user_id();
$nombre        = trim($_POST['nombre'] ?? '');
$licencia      = trim($_POST['licencia'] ?? '');
$telEmergencia = trim($_POST['tel_emergencia'] ?? '');
$telFamilia    = trim($_POST['tel_familia'] ?? '');
$disponible    = isset($_POST['disponible']) && $_POST['disponible'] == '1' ? 1 : 0;

$pdo = pdo();

// Asegura que exista el registro
$pdo->prepare("INSERT IGNORE INTO perfiles_conductor (usuario_id, disponible, rating_promedio)
               VALUES (?, 0, 5.00)")->execute([$uid]);

$upd = $pdo->prepare("
  UPDATE perfiles_conductor
     SET nombre = ?, licencia = ?, tel_emergencia = ?, tel_familia = ?, disponible = ?
   WHERE usuario_id = ?
   LIMIT 1
");
$upd->execute([$nombre, $licencia, $telEmergencia, $telFamilia, $disponible, $uid]);

header('Location: ' . BASE_URL . '/driver/profile.php?ok=1');
