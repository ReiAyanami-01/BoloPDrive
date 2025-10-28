<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['conductor']);

// Preferencias en sesión (MVP). Persistencia simple.
if (!isset($_SESSION['driver_prefs'])) {
  $_SESSION['driver_prefs'] = [
    'accept_requests'   => 1, // disponible para recibir solicitudes
    'email_notifications'=> 1, // correos (OTP/estado)
  ];
}
$prefs = $_SESSION['driver_prefs'];

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<main style="margin-left:240px;padding:14px">
  <h2>Configuraciones (Conductor)</h2>

  <?php if (isset($_GET['ok']) && (int)$_GET['ok'] === 1): ?>
    <div class="alert ok" style="margin:10px 0;padding:10px 12px;border-radius:10px;background:#ecfdf5;border:1px solid #b7f0d6;color:#0f5132;">
      Preferencias guardadas.
    </div>
  <?php endif; ?>

  <div class="card" style="max-width:720px">
    <form method="post" action="<?= BASE_URL ?>/../api/driver_settings_save.php">
      <fieldset style="border:0; padding:0; margin:0">
        <legend style="font-weight:700; margin-bottom:10px">Preferencias básicas (MVP)</legend>

        <label style="display:flex; align-items:center; gap:10px; margin:10px 0;">
          <input type="checkbox" name="accept_requests" <?= $prefs['accept_requests'] ? 'checked' : '' ?>>
          <span>Disponible para recibir solicitudes</span>
        </label>

        <label style="display:flex; align-items:center; gap:10px; margin:10px 0;">
          <input type="checkbox" name="email_notifications" <?= $prefs['email_notifications'] ? 'checked' : '' ?>>
          <span>Permitir notificaciones por correo (OTP, estado de viaje)</span>
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
