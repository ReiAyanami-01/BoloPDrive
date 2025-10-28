<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth.php';

require_login();
require_role(['conductor']);

// Guardado MVP en sesión (sin DB)
$_SESSION['driver_prefs'] = [
  'accept_requests'    => isset($_POST['accept_requests']) ? 1 : 0,
  'email_notifications'=> isset($_POST['email_notifications']) ? 1 : 0,
];

header('Location: ' . BASE_URL . '/driver/settings.php?ok=1');
