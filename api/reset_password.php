<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../lib/otp.php';

$email = trim($_POST['email'] ?? '');
$otp   = trim($_POST['otp'] ?? '');
$pass1 = $_POST['password']  ?? '';
$pass2 = $_POST['password2'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: '.BASE_URL.'/auth/reset.php?ok=0&msg=Email%20inv%C3%A1lido&email='.urlencode($email));
  exit;
}
if ($otp === '' || !preg_match('/^\d{6}$/', $otp)) {
  header('Location: '.BASE_URL.'/auth/reset.php?ok=0&msg=C%C3%B3digo%20OTP%20inv%C3%A1lido&email='.urlencode($email));
  exit;
}
if ($pass1 === '' || strlen($pass1) < 6 || $pass1 !== $pass2) {
  header('Location: '.BASE_URL.'/auth/reset.php?ok=0&msg=Contrase%C3%B1a%20inv%C3%A1lida%20o%20no%20coincide&email='.urlencode($email));
  exit;
}

$pdo = pdo();

// Verifica OTP
list($ok, $msg) = otp_verify_and_consume($pdo, $email, 'reset', $otp);
if (!$ok) {
  header('Location: '.BASE_URL.'/auth/reset.php?ok=0&msg='.urlencode($msg).'&email='.urlencode($email));
  exit;
}

// Actualiza contraseña
$hash = password_hash($pass1, PASSWORD_DEFAULT);
$st = $pdo->prepare("UPDATE usuarios SET password_hash=? WHERE email=? LIMIT 1");
$st->execute([$hash, $email]);

header('Location: '.BASE_URL.'/auth/login.php?ok=1&msg=Contrase%C3%B1a%20actualizada.%20Inicia%20sesi%C3%B3n.');
