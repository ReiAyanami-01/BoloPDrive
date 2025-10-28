<?php require_once __DIR__.'/../../config/config.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Restablecer contraseña — BOLO P-Drive</title>
  <style>
    :root{ --rei-bg:#f6f8ff; --rei-line:#d7def0; --rei-primary:#6aa3ff; --rei-primary-2:#7dafff; --radius:16px; }
    *{box-sizing:border-box} html,body{height:100%}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--rei-bg)}
    header{padding:10px 16px;border-bottom:1px solid var(--rei-line);background:linear-gradient(90deg,#121723,#161c2a);color:#eaefff}
    .wrap{min-height:calc(100% - 52px);display:grid;place-items:center;padding:24px 16px}
    .card{width:100%;max-width:480px;background:#fff;border:1px solid var(--rei-line);border-radius:var(--radius);box-shadow:0 12px 40px rgba(20,30,60,.12);padding:24px}
    h1{margin:6px 0 12px;font-size:22px}
    label{display:block;margin:10px 0 6px;font-weight:600}
    input{width:100%;padding:12px;border:1px solid var(--rei-line);border-radius:12px}
    button{margin-top:16px;width:100%;padding:12px;border:0;border-radius:12px;background:var(--rei-primary);color:#fff;font-weight:700;cursor:pointer}
    button:hover{filter:brightness(1.03)}
    .links{margin-top:10px;display:flex;gap:10px;justify-content:space-between}
    .alert{margin:8px 0 12px;padding:10px 12px;border-radius:10px;font-size:14px;display:none}
    .alert.ok{display:block;background:#ecfdf5;border:1px solid #b7f0d6;color:#0f5132}
    .alert.err{display:block;background:#fff3f3;border:1px solid #ffd0d0;color:#8a2b2b}
  </style>
</head>
<body>
<header><strong>BOLO P-Drive</strong></header>
<?php
  $email = $_GET['email'] ?? '';
  $sent  = isset($_GET['sent']) ? (int)$_GET['sent'] : 0;
  $ok    = isset($_GET['ok']) ? (int)$_GET['ok'] : null;
  $msg   = $_GET['msg'] ?? '';
?>
<div class="wrap">
  <div class="card">
    <h1>Restablecer contraseña</h1>
    <?php if ($sent): ?>
      <div class="alert ok">Te enviamos un código a <strong><?= htmlspecialchars($email) ?></strong>.</div>
    <?php endif; ?>
    <?php if ($msg !== ''): ?>
      <div class="alert <?= $ok ? 'ok' : 'err' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/../api/reset_password.php">
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

      <label>Código OTP (6 dígitos)</label>
      <input type="text" name="otp" pattern="\d{6}" maxlength="6" placeholder="••**••" required>

      <label>Nueva contraseña</label>
      <input type="password" name="password" minlength="6" required>

      <label>Confirmar nueva contraseña</label>
      <input type="password" name="password2" minlength="6" required>

      <button type="submit">Cambiar contraseña</button>

      <div class="links">
        <a href="<?= BASE_URL ?>/auth/login.php">Volver al login</a>
        <a href="<?= BASE_URL ?>/auth/forgot.php">Solicitar otro código</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
