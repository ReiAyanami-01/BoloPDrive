<?php
declare(strict_types=1);

// 1) Carga BASE_URL SIEMPRE antes de usarlo
require_once __DIR__ . '/../config/config.php';
// 2) Carga helpers de sesión/rol
require_once __DIR__ . '/../config/auth.php';

// 3) Si no hay sesión -> login
if (!logged_in()) {
  header('Location: ' . BASE_URL . '/auth/login.php');
  exit;
}

// 4) Redirige al panel según rol
switch (user_role()) {
  case 'usuario':
    header('Location: ' . BASE_URL . '/user/dashboard.php');
    break;
  case 'conductor':
    header('Location: ' . BASE_URL . '/driver/dashboard.php');
    break;
  case 'admin':
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    break;
  default:
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login.php?e=rol');
    break;
}
exit;
