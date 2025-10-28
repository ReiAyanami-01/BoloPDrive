<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['admin', 3]); // admin

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';

$pdo = pdo();

/* ==== Helpers seguros ==== */
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
function safeCount(PDO $pdo, string $sql, array $params = []): int {
  try {
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return (int)$st->fetchColumn();
  } catch (Throwable $e) {
    return 0;
  }
}

/* ==== Paginación ==== */
$perPage = 20;
$page = (int)($_GET['p'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

/* ==== Filtros simples ==== */
$activo = $_GET['activo'] ?? '';
$where = 'WHERE u.rol_id = 2'; // solo conductores
$params = [];

if ($activo !== '' && ($activo === '0' || $activo === '1')) {
  $where .= ' AND u.activo = ?';
  $params[] = (int)$activo;
}

/* ==== Total y datos ==== */
$total = safeCount($pdo, "SELECT COUNT(*) FROM usuarios u $where", $params);

/* Join opcional a perfiles_conductor si existe */
$join = '';
$selExtra = '';
if (tableExists($pdo, 'perfiles_conductor')) {
  $colsPC = columnsOf($pdo, 'perfiles_conductor');
  $join = 'LEFT JOIN perfiles_conductor pc ON pc.usuario_id = u.id';
  // Agrega columnas si existen; no todas son obligatorias
  $pick = [];
  foreach (['licencia','telefono_emergencia','telefono_familia','vehiculo','placa'] as $c) {
    if (in_array($c, $colsPC, true)) $pick[] = "pc.`$c` AS `$c`";
  }
  if ($pick) $selExtra = ', ' . implode(', ', $pick);
}

$sql = "
  SELECT u.id, u.nombre, u.email, u.activo, u.creado_en, u.email_verificado_at, u.twofa_enabled
  $selExtra
  FROM usuarios u
  $join
  $where
  ORDER BY u.creado_en DESC, u.id DESC
  LIMIT $perPage OFFSET $offset
";
try {
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $rows = $st->fetchAll();
} catch (Throwable $e) {
  $rows = [];
  echo "<div class='card' style='margin:14px'><strong>Error al consultar conductores:</strong> "
     . htmlspecialchars($e->getMessage()) . "</div>";
}

$totalPages = max(1, (int)ceil($total / $perPage));
?>
<main style="margin-left:240px;padding:14px">
  <h2>Conductores</h2>

  <div class="card" style="max-width:1024px;margin-bottom:12px">
    <form method="get" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <label>Activo:</label>
      <select name="activo">
        <option value="">(Todos)</option>
        <option value="1" <?= $activo==='1'?'selected':'' ?>>Sí</option>
        <option value="0" <?= $activo==='0'?'selected':'' ?>>No</option>
      </select>
      <button class="btn" type="submit">Filtrar</button>
      <a class="btn secondary" href="<?= BASE_URL ?>/admin/drivers.php">Limpiar</a>
    </form>
  </div>

  <div class="card" style="max-width:1200px;overflow:auto">
    <?php if (!$rows): ?>
      <p style="margin:0">No hay conductores para mostrar.</p>
    <?php else: ?>
      <table style="width:100%; border-collapse:collapse">
        <thead>
          <tr>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">ID</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Nombre</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Email</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Activo</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Verificado</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">2FA</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Creado</th>
            <?php if (strpos($selExtra, 'licencia') !== false): ?>
              <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Licencia</th>
            <?php endif; ?>
            <?php if (strpos($selExtra, 'vehiculo') !== false): ?>
              <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Vehículo</th>
            <?php endif; ?>
            <?php if (strpos($selExtra, 'placa') !== false): ?>
              <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Placa</th>
            <?php endif; ?>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= (int)$r['id'] ?></td>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)$r['nombre']) ?></td>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)$r['email']) ?></td>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= ((int)($r['activo'] ?? 0)) ? 'Sí' : 'No' ?></td>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['email_verificado_at'] ?? '—')) ?></td>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= ((int)($r['twofa_enabled'] ?? 0)) ? 'Sí' : 'No' ?></td>
              <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['creado_en'] ?? '')) ?></td>
              <?php if (strpos($selExtra, 'licencia') !== false): ?>
                <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['licencia'] ?? '')) ?></td>
              <?php endif; ?>
              <?php if (strpos($selExtra, 'vehiculo') !== false): ?>
                <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['vehiculo'] ?? '')) ?></td>
              <?php endif; ?>
              <?php if (strpos($selExtra, 'placa') !== false): ?>
                <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['placa'] ?? '')) ?></td>
              <?php endif; ?>
              <td style="padding:8px;border-bottom:1px solid var(--border)">
                <a class="link" href="#" onclick="alert('Ver (MVP)');return false;">Ver</a> ·
                <a class="link" href="#" onclick="alert('Suspender (MVP)');return false;">Suspender</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="card" style="max-width:1200px;margin-top:12px;display:flex;gap:6px;flex-wrap:wrap">
      <?php for ($p=1; $p <= $totalPages; $p++): ?>
        <?php
          $qs = $_GET; $qs['p'] = $p;
          $url = BASE_URL . '/admin/drivers.php?' . http_build_query($qs);
        ?>
        <a class="btn <?= $p===$page ? '' : 'secondary' ?>" href="<?= $url ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
