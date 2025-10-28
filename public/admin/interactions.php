<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['admin', 3]);

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';

$pdo = pdo();

/* ===== Helpers para introspección segura ===== */
function tableExists(PDO $pdo, string $table): bool {
  $st = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
  $st->execute([$table]);
  return (int)$st->fetchColumn() > 0;
}
function columnsOf(PDO $pdo, string $table): array {
  $st = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
  $st->execute([$table]);
  return array_map('strval', $st->fetchAll(PDO::FETCH_COLUMN));
}
function has(array $cols, string $name): bool { return in_array($name, $cols, true); }

/** SELECT flexible por tabla con mapeo de columnas opcionales */
function fetchLastRows(PDO $pdo, string $table, int $limit = 20): array {
  if (!tableExists($pdo, $table)) return ['rows'=>[], 'cols'=>[], 'exists'=>false];

  $cols = columnsOf($pdo, $table);
  if (!$cols) return ['rows'=>[], 'cols'=>[], 'exists'=>true];

  // Campos comunes “si existen”
  $sel = [];
  foreach (['id','solicitud_id','usuario_id','conductor_id','precio','estado','evento','tipo','detalle','creado_en','created_at','actualizado_en','updated_at','origen_lat','origen_lng','destino_lat','destino_lng'] as $c) {
    if (has($cols, $c)) $sel[] = "t.`$c` AS `$c`";
  }
  if (!$sel) $sel[] = "t.*";

  // JOIN opcional a usuarios (nombres) si hay FK
  $joinUser = $joinDriver = '';
  if (has($cols,'usuario_id')) {
    $joinUser = "LEFT JOIN usuarios uu ON uu.id = t.usuario_id";
    $sel[] = "uu.nombre AS usuario_nombre";
    $sel[] = "uu.email  AS usuario_email";
  }
  if (has($cols,'conductor_id')) {
    $joinDriver = "LEFT JOIN usuarios ud ON ud.id = t.conductor_id";
    $sel[] = "ud.nombre AS conductor_nombre";
    $sel[] = "ud.email  AS conductor_email";
  }

  // Orden sugerido: creado_en/created_at/id
  $order = has($cols,'creado_en') ? 't.creado_en DESC'
         : (has($cols,'created_at') ? 't.created_at DESC' : 't.id DESC');

  $sql = "SELECT ".implode(", ", $sel)." FROM `$table` t $joinUser $joinDriver ORDER BY $order LIMIT $limit";
  try {
    $rows = $pdo->query($sql)->fetchAll();
    return ['rows'=>$rows, 'cols'=>$cols, 'exists'=>true];
  } catch (Throwable $e) {
    // Si algo falla, devolvemos vacío y un mensaje en pantalla más abajo
    return ['rows'=>[], 'cols'=>$cols, 'exists'=>true, 'error'=>$e->getMessage()];
  }
}

/* ===== Carga de datasets ===== */
$sol  = fetchLastRows($pdo, 'solicitudes', 20);
$off  = fetchLastRows($pdo, 'ofertas', 20);
$ints = fetchLastRows($pdo, 'interacciones', 20);

/* ===== Render ===== */
function renderCard(string $title, array $data): void {
  $rows = $data['rows'] ?? [];
  $exists = $data['exists'] ?? false;
  $error = $data['error'] ?? '';

  echo '<div class="card" style="margin-bottom:12px;max-width:1200px;overflow:auto">';
  echo "<h3 style='margin-top:0'>$title</h3>";

  if (!$exists) {
    echo "<p>La tabla no existe en la base de datos.</p>";
    echo '</div>';
    return;
  }
  if ($error) {
    echo "<p style='color:#b00020'><strong>Error:</strong> ".htmlspecialchars($error)."</p>";
  }
  if (!$rows) {
    echo "<p>No hay registros para mostrar.</p>";
    echo '</div>';
    return;
  }

  // Determinar columnas a mostrar (unión de claves del primer registro)
  $headers = array_keys($rows[0]);

  echo '<table style="width:100%;border-collapse:collapse">';
  echo '<thead><tr>';
  foreach ($headers as $h) {
    echo '<th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">'.htmlspecialchars($h).'</th>';
  }
  echo '</tr></thead><tbody>';

  foreach ($rows as $r) {
    echo '<tr>';
    foreach ($headers as $h) {
      $v = $r[$h] ?? '';
      echo '<td style="padding:8px;border-bottom:1px solid var(--border)">'.htmlspecialchars((string)$v).'</td>';
    }
    echo '</tr>';
  }
  echo '</tbody></table>';
  echo '</div>';
}
?>
<main style="margin-left:240px;padding:14px">
  <h2>Interacciones</h2>

  <?php
    renderCard('Solicitudes recientes', $sol);
    renderCard('Ofertas recientes', $off);
    renderCard('Interacciones recientes', $ints);
  ?>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
