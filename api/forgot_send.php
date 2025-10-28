<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../lib/brevo.php';
require_once __DIR__.'/../lib/otp.php';

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: '.BASE_URL.'/auth/forgot.php?ok=0&msg=Email%20inv%C3%A1lido');
  exit;
}

$pdo = pdo();

// Buscar usuario (no revelamos si existe o no)
$st = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email=? LIMIT 1");
$st->execute([$email]);
$user   = $st->fetch(PDO::FETCH_ASSOC);
$userId = $user ? (int)$user['id'] : null;
$nombre = $user['nombre'] ?? 'Usuario';

// Rate limit simple: 3 envíos en 10min
$rl = $pdo->prepare("SELECT COUNT(*) FROM otp_codes WHERE email=? AND purpose='reset' AND created_at > (NOW() - INTERVAL 10 MINUTE)");
$rl->execute([$email]);
if ((int)$rl->fetchColumn() >= 3) {
  header('Location: '.BASE_URL.'/auth/forgot.php?ok=0&msg=Demasiadas%20solicitudes.%20Int%C3%A9ntalo%20luego.');
  exit;
}

// Crear OTP
list($otpCode, $otpId) = otp_create_and_store($pdo, $userId, $email, 'reset');

// Email
$subject = 'Tu código de seguridad (BOLO P-Drive)';
$html = '
  <div style="font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;line-height:1.5;">
    <h2 style="margin:0 0 10px;">Hola '.htmlspecialchars($nombre).',</h2>
    <p>Tu código de verificación es:</p>
    <div style="font-size:28px;font-weight:800;letter-spacing:4px;padding:12px 16px;border:1px solid #e5e7eb;border-radius:12px;display:inline-block;background:#f9fafb;">
      '.htmlspecialchars($otpCode).'
    </div>
    <p style="margin-top:14px;">Caduca en <strong>10 minutos</strong>. Si no lo solicitaste, ignora este correo.</p>
  </div>
';

list($ok, $info) = send_brevo_mail($email, $nombre, $subject, $html);
if (!$ok) {
  // En local puedes loguear $info para ver el error exacto de Brevo
  header('Location: '.BASE_URL.'/auth/forgot.php?ok=0&msg=No%20se%20pudo%20enviar%20el%20c%C3%B3digo.');
  exit;
}

// Ir a reset con el email rellenado
header('Location: '.BASE_URL.'/auth/reset.php?email='.urlencode($email).'&sent=1');
