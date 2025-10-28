<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth.php';

require_login();
require_role(['usuario']);

// Guardado MVP en sesión (sin BD)
$notify  = isset($_POST['notify_email']) ? 1 : 0;
$nearby  = isset($_POST['visibility_nearby']) ? 1 : 0;

$_SESSION['prefs'] = [
  'notify_email'      => $notify,
  'visibility_nearby' => $nearby,
];

// Redirige de vuelta a settings con mensaje ok
header('Location: ' . BASE_URL . '/user/settings.php?ok=1');
