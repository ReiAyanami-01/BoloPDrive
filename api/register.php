<?php
declare(strict_types=1);
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../lib/brevo.php';
require_once __DIR__.'/../lib/otp.php';

header('Content-Type: text/html; charset=utf-8');

$nombre = trim($_POST['nombre'] ?? '');
$email  = trim($_POST['email'] ?? '');
$pass   = $_POST['password'] ?? '';
$rolTxt = $_POST['rol'] ?? 'usuario';

if ($nombre==='' || $email==='' || $pass==='') {
  http_response_code(400);
  echo 'Faltan datos. <a href="'.BASE_URL.'/auth/register.php">Volver</a>';
  exit;
}

$rolMap = ['usuario'=>1,'conductor'=>2,'admin'=>3];
$rol_id = $rolMap[$rolTxt] ?? 1;
if ($rol_id === 3) { $rol_id = 1; $rolTxt = 'usuario'; }

try {
  $pdo = pdo();

  // ¿email ya existe?
  $st = $pdo->prepare("SELECT id FROM usuarios WHERE email=? LIMIT 1");
  $st->execute([$email]);
  if ($st->fetch()) {
    header('Location: '.BASE_URL.'/auth/login.php?ok=0&msg=El%20email%20ya%20est%C3%A1%20registrado'); exit;
  }

  // Crear usuario (email aún no verificado)
  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $ins  = $pdo->prepare("
    INSERT INTO usuarios (email,password_hash,nombre,rol_id,email_verificado_at,twofa_enabled)
    VALUES (?,?,?,?,NULL,1)
  ");
  $ins->execute([$email, $hash, $nombre, $rol_id]);
  $uid = (int)$pdo->lastInsertId();

  // Crear OTP de verificación (purpose = 'verify')
  list($otpCode,) = otp_create_and_store($pdo, $uid, $email, 'verify');

  // Enviar correo
  $subject = 'Verifica tu email (BOLO P-Drive)';
  $html = '
    <div style="font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;line-height:1.5;">
      <h2 style="margin:0 0 10px;">Hola '.htmlspecialchars($nombre).',</h2>
      <p>Para activar tu cuenta, usa este código de verificación:</p>
      <div style="font-size:28px;font-weight:800;letter-spacing:4px;padding:12px 16px;border:1px solid #e5e7eb;border-radius:12px;display:inline-block;background:#f9fafb;">
        '.htmlspecialchars($otpCode).'
      </div>
      <p style="margin-top:14px;">Caduca en <strong>10 minutos</strong>.</p>
    </div>
  ';
  list($ok,) = send_brevo_mail($email, $nombre, $subject, $html);

  header('Location: '.BASE_URL.'/auth/verify.php?email='.urlencode($email).'&sent=1'); exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo 'Error al registrar: '.htmlspecialchars($e->getMessage()); exit;
}
