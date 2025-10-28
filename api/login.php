<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../lib/brevo.php';
require_once __DIR__ . '/../lib/otp.php';

/* === helpers compatibles con PHP 7 === */
function starts_with($haystack, $needle): bool {
  return $needle === '' ? true : (strncmp($haystack, $needle, strlen($needle)) === 0);
}

function find_user_by_email(PDO $pdo, string $email): ?array {
  $st = $pdo->prepare("
    SELECT id, nombre, email, password_hash, rol_id
    FROM usuarios
    WHERE LOWER(email) = LOWER(?)
    LIMIT 1
  ");
  $st->execute([$email]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}

function check_password_robusto(string $plain, ?string $stored): bool {
  $stored = (string)$stored;

  // bcrypt / argon2
  if ($stored !== '' && (starts_with($stored, '$2y$') || starts_with($stored, '$argon2'))) {
    return password_verify($plain, $stored);
  }

  // SHA-256 (64 hex)
  if ($stored !== '' && ctype_xdigit($stored) && strlen($stored) === 64) {
    return hash('sha256', $plain) === strtolower($stored);
  }

  // (solo pruebas) texto plano
  return $stored !== '' && $stored === $plain;
}

/* === entrada === */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/auth/login.php?e=1'); exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = trim($_POST['password'] ?? '');
if ($email === '' || $pass === '') {
  header('Location: ' . BASE_URL . '/auth/login.php?e=1'); exit;
}

try {
  $pdo  = pdo();
  $user = find_user_by_email($pdo, $email);

  if (!$user || !check_password_robusto($pass, $user['password_hash'] ?? '')) {
    header('Location: ' . BASE_URL . '/auth/login.php?e=2'); exit; // credenciales inválidas
  }

  $uid   = (int)$user['id'];
  $rolId = (int)$user['rol_id'];

  // === ADMIN: bypass OTP ===
  if ($rolId === 3) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['user'] = [
      'id'      => $uid,
      'email'   => $user['email'],
      'rol'     => $rolId,
      'role_id' => $rolId,
      'role'    => 'admin',
      'nombre'  => $user['nombre'] ?? ''
    ];
    session_regenerate_id(true);
    header('Location: ' . BASE_URL . '/index.php'); exit;
  }

  // === Usuario / Conductor → crear OTP y enviar por email ===
  $code = otp_generate();       // "001234" etc (string 6 dígitos)
  $hash = otp_hash($code);      // usa el MISMO algoritmo que la verificación

  // Ajustado a tu tabla (según capturas)
  $pdo->prepare("
    INSERT INTO otp_codes (user_id, email, purpose, code_hash, expires_at, created_at)
    VALUES (?, ?, 'login', ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())
  ")->execute([$uid, $user['email'], $hash]);

  // Envío por email con Brevo
  $subject = 'Tu código de acceso (BOLO P-Drive)';
  $html = '
    <div style="font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;line-height:1.5;">
      <h2 style="margin:0 0 8px;">Hola '.htmlspecialchars($user['nombre'] ?? 'Usuario').',</h2>
      <p>Tu código de verificación es:</p>
      <div style="font-size:28px;font-weight:800;letter-spacing:4px;padding:12px 16px;border:1px solid #e5e7eb;border-radius:12px;display:inline-block;background:#f9fafb;">
        '.htmlspecialchars($code).'
      </div>
      <p style="margin-top:10px;">Caduca en <strong>10 minutos</strong>.</p>
    </div>
  ';
  // No bloqueamos si falla el envío; mostramos igual la pantalla OTP
  @list($okSend, $infoSend) = send_brevo_mail($user['email'], $user['nombre'] ?? 'Usuario', $subject, $html);

  header('Location: ' . BASE_URL . '/auth/otp_login.php?email=' . urlencode($user['email'])); exit;

} catch (Throwable $e) {
  // error_log($e->getMessage());
  header('Location: ' . BASE_URL . '/auth/login.php?e=500'); exit;
}
