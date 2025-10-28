<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/secrets.php';

/** Hash canónico: PEPPER + CODE (¡siempre igual en toda la app!) */
function otp_hash(string $code): string {
  return hash('sha256', OTP_PEPPER . $code);
}

/** OTP de 6 dígitos con ceros a la izquierda, como string */
function otp_generate(): string {
  return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Crea y guarda un OTP genérico.
 * $purpose: 'verify' (verificación de email), 'login' (inicio de sesión), 'reset' (cambio de clave), etc.
 * Devuelve [ $codePlain, $otpId ]
 */
function otp_create_and_store(PDO $pdo, ?int $userId, string $email, string $purpose = 'verify'): array {
  $code      = otp_generate();
  $codeHash  = otp_hash($code);
  $expiresAt = (new DateTimeImmutable('now'))
                 ->modify('+' . OTP_TTL_SECONDS . ' seconds')
                 ->format('Y-m-d H:i:s');

  $st = $pdo->prepare("
    INSERT INTO otp_codes (user_id, email, purpose, code_hash, expires_at, attempts, max_attempts, created_at)
    VALUES (?, ?, ?, ?, ?, 0, ?, NOW())
  ");
  $st->execute([$userId, $email, $purpose, $codeHash, $expiresAt, OTP_MAX_ATTEMPTS]);

  return [$code, (int)$pdo->lastInsertId()];
}

/**
 * Verifica y consume el OTP más reciente válido para ese email + propósito.
 * Retorna [true, 'OK', $row] si coincide; si no, [false, 'mensaje'].
 */
function otp_verify_and_consume(PDO $pdo, string $email, string $purpose, string $codePlain): array {
  $st = $pdo->prepare("
    SELECT *
    FROM otp_codes
    WHERE LOWER(email) = LOWER(?) AND purpose = ?
      AND used_at IS NULL
      AND expires_at > NOW()
    ORDER BY id DESC
    LIMIT 5
  ");
  $st->execute([$email, $purpose]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  if (!$rows) return [false, 'Código inválido o expirado.'];

  // Respeta el mismo algoritmo de hash
  $calc = otp_hash($codePlain);

  foreach ($rows as $row) {
    if (hash_equals($row['code_hash'], $calc)) {
      // marcar usado
      $pdo->prepare("UPDATE otp_codes SET used_at = NOW() WHERE id = ?")->execute([$row['id']]);
      return [true, 'OK', $row];
    } else {
      // sumar intento
      $pdo->prepare("UPDATE otp_codes SET attempts = attempts + 1 WHERE id = ?")->execute([$row['id']]);
      if (isset($row['max_attempts']) && (int)$row['attempts'] + 1 >= (int)$row['max_attempts']) {
        $pdo->prepare("UPDATE otp_codes SET used_at = NOW() WHERE id = ?")->execute([$row['id']]);
      }
    }
  }
  return [false, 'Código incorrecto.'];
}
