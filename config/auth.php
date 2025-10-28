<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/** Mapa canónico de roles */
function roles_map(): array {
  return [
    1 => 'usuario',
    2 => 'conductor',
    3 => 'admin',
  ];
}

function logged_in(): bool {
  return isset($_SESSION['user']['id']);
}

function user_id(): ?int {
  return logged_in() ? (int)$_SESSION['user']['id'] : null;
}

function user_role_id(): ?int {
  if (!logged_in()) return null;
  // Acepta tanto 'rol' numérico como 'role_id' por compatibilidad
  if (isset($_SESSION['user']['rol']))     return (int)$_SESSION['user']['rol'];
  if (isset($_SESSION['user']['role_id'])) return (int)$_SESSION['user']['role_id'];
  return null;
}

function user_role(): ?string {
  $rid = user_role_id();
  if ($rid === null) return null;
  $map = roles_map();
  return $map[$rid] ?? null;
}

/**
 * Requiere login; si no, envía al login
 */
function require_login(): void {
  if (!logged_in()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
  }
}

/**
 * Requiere que el rol del usuario esté dentro de $allowed.
 * $allowed puede contener ids (int) o nombres (string).
 * Ej: require_role(['admin']) o require_role([3, 'admin'])
 */
function require_role(array $allowed): void {
  require_login();
  $rid  = user_role_id();
  $rstr = user_role();

  // normaliza los permitidos a ambos formatos (id y nombre)
  $allow_ids = [];
  $allow_str = [];
  $map = roles_map();
  foreach ($allowed as $r) {
    if (is_int($r) || ctype_digit((string)$r)) {
      $allow_ids[] = (int)$r;
      if (isset($map[(int)$r])) $allow_str[] = $map[(int)$r];
    } else {
      $allow_str[] = strtolower((string)$r);
      // agrega id equivalente si existe
      $id = array_search(strtolower((string)$r), $map, true);
      if ($id !== false) $allow_ids[] = (int)$id;
    }
  }

  $ok = in_array($rid, $allow_ids, true) || in_array((string)$rstr, $allow_str, true);
  if (!$ok) {
    // 403 básico
    http_response_code(403);
    echo "<h3 style='font-family:system-ui'>Acceso denegado (rol requerido)</h3>";
    exit;
  }
}
