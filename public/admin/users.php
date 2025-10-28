<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['admin', 3]); // acepta nombre o id

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';

$pdo = pdo();

/** Map de roles (ajusta si usas otros ids) */
$ROLE_MAP = [1=>'usuario', 2=>'conductor', 3=>'admin'];

/** Filtro de rol (por id o por nombre) */
$roleParam = trim($_GET['role'] ?? '');
$roleIdFilter = null;
if ($roleParam !== '') {
  if (ctype_digit($roleParam)) {
    $roleIdFilter = (int)$roleParam;
  } else {
    $inverse = array_flip($ROLE_MAP);
    $roleParam = strtolower($roleParam);
    if (isset($inverse[$roleParam])) $roleIdFilter = (int)$inverse[$roleParam];
  }
}

/** Paginación */
$perPage = 20;
$page = (int)($_GET['p'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

/** WHERE opcional */
$where = '';
$params = [];
if ($roleIdFilter !== null) {
  $where = 'WHERE u.rol_id = ?';
  $params[] = $roleIdFilter;
}

try {
  // Total
  $sqlCount = "SELECT COUNT(*) FROM usuarios u $where";
  $stC = $pdo->prepare($sqlCount);
  $stC->execute($params);
  $total = (int)$stC->fetchColumn();

  // Datos (usa solo columnas que tienes: NO 'actualizado_en')
  $sql = "
    SELECT u.id, u.nombre, u.email, u.rol_id, u.activo,
           u.creado_en, u.email_verificado_at, u.twofa_enabled
    FROM usuarios u
    $where
    ORDER BY u.creado_en DESC, u.id DESC
    LIMIT $perPage OFFSET $offset
  ";
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $rows = $st->fetchAll();
} catch (Throwable $e) {
  $rows = [];
  $total = 0;
  echo "<div class='card' style='margin:14px'><strong>Error al consultar usuarios:</strong> "
     . htmlspecialchars($e->getMessage()) . "</div>";
}

$totalPages = max(1, (int)ceil($total / $perPage));
?>
<main style="margin-left:240px;padding:14px">
  <h2>Usuarios</h2>

  <div class="card" style="max-width:1024px;margin-bottom:12px">
    <form method="get" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <label>Filtrar por rol:</label>
      <select name="role">
        <option value="">(Todos)</option>
        <option value="1" <?= $roleIdFilter===1?'selected':'' ?>>Usuario</option>
        <option value="2" <?= $roleIdFilter===2?'selected':'' ?>>Conductor</option>
        <option value="3" <?= $roleIdFilter===3?'selected':'' ?>>Administrador</option>
      </select>
      <button class="btn" type="submit">Aplicar</button>
      <a class="btn secondary" href="<?= BASE_URL ?>/admin/users.php">Limpiar</a>
    </form>
  </div>

  <div class="card" style="max-width:1024px;overflow:auto">
    <?php if (!$rows): ?>
      <p style="margin:0">No hay usuarios para mostrar.</p>
    <?php else: ?>
      <table style="width:100%; border-collapse:collapse">
        <thead>
          <tr>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">ID</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Nombre</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Email</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Rol</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Activo</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Verificado</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">2FA</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Creado</th>
            <th style="text-align:left;padding:8px;border-bottom:1px solid var(--border)">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= (int)$r['id'] ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)$r['nombre']) ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)$r['email']) ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)">
              <?php $rid = (int)$r['rol_id']; echo htmlspecialchars($ROLE_MAP[$rid] ?? ("rol:$rid")); ?>
            </td>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= ((int)($r['activo'] ?? 0)) ? 'Sí' : 'No' ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['email_verificado_at'] ?? '—')) ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= ((int)($r['twofa_enabled'] ?? 0)) ? 'Sí' : 'No' ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)"><?= htmlspecialchars((string)($r['creado_en'] ?? '')) ?></td>
            <td style="padding:8px;border-bottom:1px solid var(--border)">
              <a class="link" href="#" onclick="alert('Ver (MVP)');return false;">Ver</a> ·
              <a class="link" href="#" onclick="alert('Reset pass (MVP)');return false;">Reset</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="card" style="max-width:1024px;margin-top:12px;display:flex;gap:6px;flex-wrap:wrap">
      <?php for ($p=1; $p <= $totalPages; $p++): ?>
        <?php $qs = $_GET; $qs['p'] = $p;
              $url = BASE_URL . '/admin/users.php?' . http_build_query($qs); ?>
        <a class="btn <?= $p===$page ? '' : 'secondary' ?>" href="<?= $url ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
