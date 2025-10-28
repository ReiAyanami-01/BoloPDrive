<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php'; // 👈 BASE_URL + pdo()
require_once __DIR__ . '/../../config/auth.php';   // 👈 sesión + helpers

require_login();
require_role(['conductor']);

$pdo = pdo();

$uid = user_id();

// Carga (o crea) perfil de conductor
$st = $pdo->prepare("SELECT * FROM perfiles_conductor WHERE usuario_id = ? LIMIT 1");
$st->execute([$uid]);
$perfil = $st->fetch(PDO::FETCH_ASSOC);

if (!$perfil) {
  $pdo->prepare("INSERT INTO perfiles_conductor (usuario_id, disponible, rating_promedio)
                 VALUES (?, 0, 5.00)")->execute([$uid]);
  $st->execute([$uid]);
  $perfil = $st->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<main style="margin-left:240px;padding:14px">
  <h2>Mi perfil (Conductor)</h2>

  <?php if (isset($_GET['ok']) && (int)$_GET['ok'] === 1): ?>
    <div class="alert ok" style="margin:10px 0;padding:10px 12px;border-radius:10px;background:#ecfdf5;border:1px solid #b7f0d6;color:#0f5132;">
      Perfil actualizado.
    </div>
  <?php endif; ?>

  <div class="card" style="max-width:780px">
    <form method="post" action="<?= BASE_URL ?>/../api/driver_profile_save.php">
      <input type="hidden" name="usuario_id" value="<?= (int)$uid ?>">

      <label>Nombre</label>
      <input type="text" name="nombre" value="<?= htmlspecialchars($perfil['nombre'] ?? '') ?>">

      <label>Número de licencia</label>
      <input type="text" name="licencia" value="<?= htmlspecialchars($perfil['licencia'] ?? '') ?>">

      <label>Teléfono de emergencia</label>
      <input type="text" name="tel_emergencia" value="<?= htmlspecialchars($perfil['tel_emergencia'] ?? '') ?>">

      <label>Teléfono de familiar</label>
      <input type="text" name="tel_familia" value="<?= htmlspecialchars($perfil['tel_familia'] ?? '') ?>">

      <label>Disponible para recibir solicitudes</label>
      <select name="disponible">
        <option value="1" <?= (int)($perfil['disponible'] ?? 0) === 1 ? 'selected' : '' ?>>Sí</option>
        <option value="0" <?= (int)($perfil['disponible'] ?? 0) === 0 ? 'selected' : '' ?>>No</option>
      </select>

      <div style="margin-top:12px">
        <button type="submit" class="btn">Guardar</button>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
