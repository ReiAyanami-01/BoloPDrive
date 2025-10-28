<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../lib/otp.php';

$email = trim($_POST['email'] ?? '');
$otp   = trim($_POST['otp'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: '.BASE_URL.'/auth/otp_login.php?ok=0&msg=Email%20inv%C3%A1lido&email='.urlencode($email));
  exit;
}
if ($otp === '' || !preg_match('/^\d{6}$/', $otp)) {
  header('Location: '.BASE_URL.'/auth/otp_login.php?ok=0&msg=C%C3%B3digo%20inv%C3%A1lido&email='.urlencode($email));
  exit;
}

$pdo = pdo();

// Verificar OTP
list($ok, $msg) = otp_verify_and_consume($pdo, $email, 'login', $otp);
if (!$ok) {
  header('Location: '.BASE_URL.'/auth/otp_login.php?ok=0&msg='.urlencode($msg).'&email='.urlencode($email));
  exit;
}

// Tomar el prelogin de la sesión y promoverlo a login real
session_regenerate_id(true);
$pre = $_SESSION['prelogin'] ?? null;
if (!$pre || $pre['email'] !== $email) {
  header('Location: '.BASE_URL.'/auth/login.php?e=2');
  exit;
}

$_SESSION['user'] = [
  'id'    => (int)$pre['user_id'],
  'email' => $pre['email'],
  'rol'   => (int)$pre['rol_id'],
];
unset($_SESSION['prelogin']);

header('Location: '.BASE_URL.'/index.php');
