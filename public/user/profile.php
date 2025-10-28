<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php'; // BASE_URL + pdo()
require_once __DIR__ . '/../../config/auth.php';   // sesión + helpers

require_login();
require_role(['usuario']); // esta vista es del rol usuario

$pdo = pdo();

// Intenta cargar perfil del usuario; si no existe, créalo vacío
$uid = user_id();
$st  = $pdo->prepare("SELECT * FROM perfiles_usuario WHERE usuario_id = ? LIMIT 1");
$st->execute([$uid]);
$perfil = $st->fetch(PDO::FETCH_ASSOC);

if (!$perfil) {
  $pdo->prepare("INSERT INTO perfiles_usuario (usuario_id) VALUES (?)")->execute([$uid]);
  $st->execute([$uid]);
  $perfil = $st->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<main style="margin-left:240px;padding:14px">
  <h2>Mi perfil</h2>

  <div class="card" style="max-width:720px">
    <form method="post" action="<?= BASE_URL ?>/../api/user_profile_save.php">
      <input type="hidden" name="usuario_id" value="<?= (int)$uid ?>">

      <label>Nombre</label>
      <input type="text" name="nombre" value="<?= htmlspecialchars($perfil['nombre'] ?? '') ?>">

      <label>Teléfono de familiares</label>
      <input type="text" name="tel_familia" value="<?= htmlspecialchars($perfil['tel_familia'] ?? '') ?>">

      <label>Número de emergencia</label>
      <input type="text" name="tel_emergencia" value="<?= htmlspecialchars($perfil['tel_emergencia'] ?? '') ?>">

      <label>Tipo de sangre</label>
      <input type="text" name="tipo_sangre" value="<?= htmlspecialchars($perfil['tipo_sangre'] ?? '') ?>">

      <label>Alergias</label>
      <input type="text" name="alergias" value="<?= htmlspecialchars($perfil['alergias'] ?? '') ?>">

      <div style="margin-top:12px">
        <button type="submit">Guardar</button>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
