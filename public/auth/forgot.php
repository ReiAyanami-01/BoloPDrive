<?php require_once __DIR__.'/../../config/config.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Olvidé mi contraseña — BOLO P-Drive</title>
  <style>
    /* ===== Paleta Rei Ayanami (fría, ligera) ===== */
    :root{
      --rei-bg: #f6f8ff;           /* casi blanco frío */
      --rei-ice:#e9eef8;           /* gris hielo */
      --rei-deep:#1e2a44;          /* azul noche sutil */
      --rei-primary:#6aa3ff;       /* azul Rei */
      --rei-primary-2:#7dafff;     /* hover */
      --rei-accent:#a9b8ff;        /* lila tenue */
      --rei-line:#d7def0;          /* bordes */
      --radius:16px;
      --shadow:0 12px 40px rgba(20,30,60,.12);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:#1f2937;
      background:
        radial-gradient(1200px 600px at 80% -10%, #dfe7ff 0%, transparent 45%),
        radial-gradient(900px 500px at -10% 90%, #edf2ff 0%, transparent 50%),
        var(--rei-bg);
    }
    header{
      padding:10px 16px; border-bottom:1px solid var(--rei-line);
      background: linear-gradient(90deg, #121723, #161c2a); color:#eaefff; letter-spacing:.4px;
    }
    .wrap{min-height:calc(100% - 52px); display:grid; place-items:center; padding:24px 16px}
    .card{
      width:100%; max-width:480px;
      background: linear-gradient(180deg, #ffffff, #fbfdff);
      border:1px solid var(--rei-line); border-radius:var(--radius);
      box-shadow: var(--shadow); padding:24px; position:relative; overflow:hidden;
      animation: floatIn .35s ease both;
    }
    .card::before{
      content:""; position:absolute; inset:0 0 auto 0; height:4px;
      background: linear-gradient(90deg, var(--rei-accent), var(--rei-primary), var(--rei-accent)); opacity:.8;
    }
    h1{margin:6px 0 6px; font-size:22px; color:var(--rei-deep); letter-spacing:.3px}
    p.sub{margin:0 0 14px; color:#44516f; font-size:14px}

    label{display:block; margin:10px 0 6px; font-weight:600; color:#2a3550}
    input{
      width:100%; padding:12px; border:1px solid var(--rei-line); border-radius:12px; background:#fff;
      font-size:15px; outline:none; transition: box-shadow .2s ease, border-color .2s ease;
    }
    input:focus{ border-color:var(--rei-primary); box-shadow:0 0 0 4px rgba(106,163,255,.20) }

    .actions{display:grid; gap:10px; margin-top:16px}
    button{
      border:0; outline:0; cursor:pointer; padding:12px 14px; border-radius:12px; width:100%;
      background: var(--rei-primary); color:#fff; font-weight:700;
      box-shadow:0 8px 18px rgba(106,163,255,.25); transition: transform .08s ease, filter .15s ease, box-shadow .2s ease;
    }
    button:hover{ transform: translateY(-1px); filter:brightness(1.03); box-shadow:0 14px 28px rgba(106,163,255,.30) }
    .links{display:flex; gap:12px; justify-content:space-between; flex-wrap:wrap}
    .link{
      color: var(--rei-deep); text-decoration:none; font-weight:600; padding:8px 10px; border-radius:10px;
      transition: background .2s ease, color .2s ease;
    }
    .link:hover{ background:#eef3ff; color:#13203a }

    .note{
      margin-top:12px; font-size:12px; color:#6b7a9a;
      background: linear-gradient(180deg, #f9fbff, #f2f6ff);
      border: 1px solid var(--rei-line); border-radius: 12px; padding: 10px 12px;
    }
    .alert{
      margin: 8px 0 12px; padding:10px 12px; border-radius:10px; font-size:14px;
      display:none;
    }
    .alert.ok{
      display:block; background:#ecfdf5; border:1px solid #b7f0d6; color:#0f5132;
    }
    .alert.err{
      display:block; background:#fff3f3; border:1px solid #ffd0d0; color:#8a2b2b;
    }
    @keyframes floatIn { from {opacity:0; transform: translateY(8px)} to {opacity:1; transform: translateY(0)} }
  </style>
</head>
<body>
<header><strong>BOLO P-Drive</strong></header>

<div class="wrap">
  <div class="card">
    <h1>Restablecer contraseña</h1>
    <p class="sub">Ingresa tu email y te enviaremos un enlace temporal.</p>

    <!-- Mensajes (opcionales vía query ?ok=1&msg=...) -->
    <?php
      $ok = isset($_GET['ok']) ? (int)$_GET['ok'] : 0;
      $msg = isset($_GET['msg']) ? trim($_GET['msg']) : '';
      if ($msg !== '') {
        $cls = $ok ? 'ok' : 'err';
        echo '<div class="alert '.$cls.'">'.htmlspecialchars($msg, ENT_QUOTES).'</div>';
      }
    ?>

    <form method="post" action="<?= BASE_URL ?>/../api/forgot_send.php" autocomplete="on">
      <label>Email</label>
      <input type="email" name="email" placeholder="tu@email.com" required>

      <div class="actions">
        <button type="submit">Enviar enlace</button>
        <div class="links">
          <a class="link" href="<?= BASE_URL ?>/auth/login.php">Volver a iniciar sesión</a>
          <a class="link" href="<?= BASE_URL ?>/auth/register.php" target="_blank" rel="noopener">Crear cuenta</a>
        </div>
      </div>

      <div class="note">
        Si no recibes el correo en unos minutos, revisa tu carpeta de <strong>spam</strong> o <strong>correo no deseado</strong>.
      </div>
    </form>
  </div>
</div>
</body>
</html>
