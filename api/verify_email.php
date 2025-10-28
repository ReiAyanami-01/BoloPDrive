<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../lib/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/auth/verify.php?e=missing'); exit;
}

$email = trim($_POST['email'] ?? '');
$code  = trim($_POST['code']  ?? '');
if ($email === '' || !preg_match('/^\d{6}$/', $code)) {
  header('Location: ' . BASE_URL . '/auth/verify.php?e=missing&email=' . urlencode($email)); exit;
}

try {
  $pdo = pdo();

  // Verificar OTP 'verify'
  [$ok, $msg] = otp_verify_and_consume($pdo, $email, 'verify', $code);
  if (!$ok) {
    $q = http_build_query(['e' => 'invalid', 'email' => $email]);
    header('Location: ' . BASE_URL . '/auth/verify.php?' . $q); exit;
  }

  // Marcar email verificado
  $pdo->prepare("UPDATE usuarios SET email_verificado_at = NOW() WHERE LOWER(email)=LOWER(?)")->execute([$email]);

  // Listo → al login
  header('Location: ' . BASE_URL . '/auth/login.php?ok=1'); exit;

} catch (Throwable $e) {
  // error_log($e->getMessage());
  header('Location: ' . BASE_URL . '/auth/verify.php?e=500'); exit;
}
