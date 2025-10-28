<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../lib/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/auth/otp_login.php?e=missing'); exit;
}

$email = trim($_POST['email'] ?? '');
$code  = trim($_POST['code']  ?? '');
if ($email === '' || $code === '' || !preg_match('/^\d{6}$/', $code)) {
  header('Location: ' . BASE_URL . '/auth/otp_login.php?e=missing&email=' . urlencode($email)); exit;
}

try {
  $pdo = pdo();

  // Buscar varios OTP recientes (no usados y no vencidos) ignorando mayúsculas
  $st = $pdo->prepare("
    SELECT id, user_id, email, code_hash, expires_at
    FROM otp_codes
    WHERE LOWER(email) = LOWER(?) AND purpose = 'login'
      AND (used_at IS NULL) AND (expires_at > NOW())
    ORDER BY id DESC
    LIMIT 5
  ");
  $st->execute([$email]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  if (!$rows) {
    header('Location: ' . BASE_URL . '/auth/otp_login.php?e=invalid&email=' . urlencode($email)); exit;
  }

  // Calcular el hash exactamente igual que al guardar (PEPPER + CODE)
  $calc = otp_hash($code);

  $match = null;
  foreach ($rows as $r) {
    if (hash_equals($r['code_hash'], $calc)) { $match = $r; break; }
  }

  if (!$match) {
    header('Location: ' . BASE_URL . '/auth/otp_login.php?e=invalid&email=' . urlencode($email)); exit;
  }

  // Marcar usado
  $pdo->prepare("UPDATE otp_codes SET used_at = NOW() WHERE id = ?")->execute([$match['id']]);

  // Cargar usuario (por id del OTP; resp. por email normalizado si hiciera falta)
  $stU = $pdo->prepare("SELECT id, email, nombre, rol_id FROM usuarios WHERE id = ? LIMIT 1");
  $stU->execute([(int)$match['user_id']]);
  $user = $stU->fetch(PDO::FETCH_ASSOC);
  if (!$user) {
    // fallback por email (caso raro)
    $stU = $pdo->prepare("SELECT id, email, nombre, rol_id FROM usuarios WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $stU->execute([$email]);
    $user = $stU->fetch(PDO::FETCH_ASSOC);
  }
  if (!$user) { header('Location: ' . BASE_URL . '/auth/login.php?e=500'); exit; }

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION['user'] = [
    'id'      => (int)$user['id'],
    'email'   => $user['email'],
    'rol'     => (int)$user['rol_id'],
    'role_id' => (int)$user['rol_id'],
    'role'    => ((int)$user['rol_id'] === 3 ? 'admin' : ((int)$user['rol_id'] === 2 ? 'conductor' : 'usuario')),
    'nombre'  => $user['nombre'] ?? ''
  ];
  session_regenerate_id(true);

  header('Location: ' . BASE_URL . '/index.php'); exit;

} catch (Throwable $e) {
  // error_log($e->getMessage());
  header('Location: ' . BASE_URL . '/auth/login.php?e=500'); exit;
}
