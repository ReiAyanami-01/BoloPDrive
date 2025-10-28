<?php require_once __DIR__.'/../../config/config.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro — BOLO P-Drive</title>
  <style>
    :root{
      --rei-bg:#f6f8ff; --rei-line:#d7def0; --rei-primary:#6aa3ff; --rei-primary-2:#7dafff; --radius:16px;
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--rei-bg)}
    header{padding:10px 16px;border-bottom:1px solid var(--rei-line);background:linear-gradient(90deg,#121723,#161c2a);color:#eaefff}
    .wrap{min-height:calc(100% - 52px);display:grid;place-items:center;padding:24px 16px}
    .card{width:100%;max-width:480px;background:#fff;border:1px solid var(--rei-line);border-radius:var(--radius);box-shadow:0 12px 40px rgba(20,30,60,.12);padding:24px}
    h1{margin:6px 0 12px;font-size:22px}
    label{display:block;margin:10px 0 6px;font-weight:600}
    input,select{width:100%;padding:12px;border:1px solid var(--rei-line);border-radius:12px}
    button{margin-top:16px;width:100%;padding:12px;border:0;border-radius:12px;background:var(--rei-primary);color:#fff;font-weight:700;cursor:pointer}
    button:hover{filter:brightness(1.03)}
    .hint{margin-top:10px;font-size:12px;color:#4b5563}
  </style>
</head>
<body>
<header><strong>BOLO P-Drive</strong></header>
<div class="wrap">
  <div class="card">
    <h1>Crear cuenta</h1>
    <form method="post" action="<?= BASE_URL ?>/../api/register.php" autocomplete="on">
      <label>Nombre completo</label>
      <input type="text" name="nombre" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Contraseña</label>
      <input type="password" name="password" minlength="6" required>

      <label>Rol</label>
      <select name="rol">
        <option value="usuario" selected>Usuario</option>
        <option value="conductor">Conductor</option>
        <!-- Admin se crea manualmente por seguridad -->
      </select>

      <button type="submit">Registrarme</button>
      <div class="hint">Al registrarte aceptas los términos del servicio.</div>
    </form>
  </div>
</div>
</body>
</html>
