<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['usuario']);

// Preferencias en sesión (MVP). En producción puedes llevar esto a DB.
if (!isset($_SESSION['prefs'])) {
  $_SESSION['prefs'] = [
    'notify_email'     => 1,
    'visibility_nearby'=> 1,
  ];
}
$prefs = $_SESSION['prefs'];

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<main style="margin-left:240px;padding:14px">
  <h2>Configuraciones</h2>

  <?php if (isset($_GET['ok']) && (int)$_GET['ok'] === 1): ?>
    <div class="alert ok" style="margin:10px 0;padding:10px 12px;border-radius:10px;background:#ecfdf5;border:1px solid #b7f0d6;color:#0f5132;">
      Preferencias guardadas.
    </div>
  <?php endif; ?>

  <div class="card" style="max-width:720px">
    <form method="post" action="<?= BASE_URL ?>/../api/user_settings_save.php">
      <fieldset style="border:0; padding:0; margin:0">
        <legend style="font-weight:700; margin-bottom:10px">Preferencias básicas (MVP)</legend>

        <label style="display:flex; align-items:center; gap:10px; margin:10px 0;">
          <input type="checkbox" name="notify_email" <?= $prefs['notify_email'] ? 'checked' : '' ?>>
          <span>Permitir notificaciones por correo (OTP, estado de viaje)</span>
        </label>

        <label style="display:flex; align-items:center; gap:10px; margin:10px 0;">
          <input type="checkbox" name="visibility_nearby" <?= $prefs['visibility_nearby'] ? 'checked' : '' ?>>
          <span>Visibilidad para conductores cercanos (1 km)</span>
        </label>

        <label style="display:flex; align-items:center; gap:10px; margin:10px 0; opacity:.75;">
          <input type="checkbox" disabled>
          <span>Eliminar cuenta (placeholder)</span>
        </label>
      </fieldset>

      <div style="margin-top:14px">
        <button type="submit" class="btn">Guardar cambios</button>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
