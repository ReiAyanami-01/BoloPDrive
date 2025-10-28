<?php require_once __DIR__.'/../../config/config.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#111827">
  <title>Login — BOLO P-Drive</title>
<style>
  :root{
    /* escala adaptable a 360–480px de ancho */
    --fs-body: clamp(14px, 3.8vw, 16px);
    --fs-h1: clamp(18px, 5.5vw, 22px);
    --radius: 14px;
    --line: #d7def0;
    --txt: #0f172a;
    --muted:#64748b;
    --primary:#6aa3ff;
    --primary-2:#7eb0ff;
    --height-touch: 52px; /* tap target cómodo */
    --pad: 16px;
    --shadow: 0 10px 26px rgba(20,30,60,.12);
  }

  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    color:var(--txt);
    font-size:var(--fs-body);
    background:
      radial-gradient(1000px 420px at 80% -10%, #e6edff 0%, transparent 45%),
      radial-gradient(800px 420px at -10% 95%, #eff4ff 0%, transparent 55%),
      #f6f8ff;
    /* usa la altura visual del móvil (cubre barra de URL) */
    min-height: 100dvh;
    padding:
      env(safe-area-inset-top,0)
      env(safe-area-inset-right,0)
      env(safe-area-inset-bottom,0)
      env(safe-area-inset-left,0);
  }

  header{
    position: sticky; top:0; z-index:10;
    padding: 12px var(--pad);
    border-bottom:1px solid var(--line);
    background: linear-gradient(90deg,#121723,#161c2a);
    color:#eaf0ff;
  }

  .wrap{
    min-height: calc(100dvh - 56px);
    display:grid;
    place-items:start center;
    padding: 10px var(--pad) 22px;
  }

  .card{
    width:100%;
    max-width: 440px;       /* en 412px ocupa casi todo */
    background:#fff;
    border:1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 18px;
    margin-top: 8px;
    position: relative;
    overflow: hidden;
  }
  .card::before{
    content:"";
    position:absolute; inset:0 0 auto 0; height: 3px;
    background: linear-gradient(90deg, #a9b8ff, var(--primary), #a9b8ff);
    opacity:.9;
  }

  h1{ margin: 6px 0 8px; font-size: var(--fs-h1); }
  .sub{ margin:0 0 12px; color:var(--muted); }

  label{ display:block; margin: 10px 0 6px; font-weight:600; }

  /* inputs a 16px para evitar zoom en Android/iOS y altura táctil */
  input{
    width:100%;
    height: var(--height-touch);
    padding: 12px;
    border:1px solid var(--line);
    border-radius: 12px;
    background:#fff;
    outline:none;
    font-size:16px;
    transition: border-color .2s, box-shadow .2s;
  }
  input:focus{ border-color:var(--primary); box-shadow:0 0 0 4px rgba(106,163,255,.2); }

  .row{
    display:flex; gap: 10px; justify-content:space-between; align-items:center;
    margin-top: 10px; flex-wrap: wrap;
  }
  .link{
    text-decoration:none; color:#1f2a48; padding:8px 10px; border-radius: 10px;
  }
  .link:active{ background:#eef3ff; }

  .btn{
    appearance:none; border:0; width:100%;
    height: var(--height-touch);
    border-radius:12px;
    font-weight:700; letter-spacing:.2px;
    color:#fff; background: var(--primary);
    box-shadow: 0 10px 22px rgba(106,163,255,.25);
    transition: transform .06s, filter .15s, box-shadow .2s;
    margin-top: 14px;
  }
  .btn:active{ transform: scale(.99); }

  .btn-ghost{
    width:100%; height: var(--height-touch);
    background:#fff; color:#223354; border:1px solid var(--line);
    border-radius:12px; font-weight:700;
  }

  .actions{ display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:12px; }
  @media (max-width: 430px){ .actions{ grid-template-columns:1fr; } }

  .alert{
    background:#fff0f0; border:1px solid #ffd0d0; color:#8a2b2b;
    border-radius: 12px; padding: 10px 12px; margin-bottom: 10px;
    font-size: 14px;
  }

  /* Pantallas con poca altura (teclado abierto) */
  @media (max-height: 700px){
    .wrap{ padding-top: 6px; }
    .card{ padding: 14px; }
    .btn{ margin-top: 10px; }
  }
</style></head>
<body>
<header><strong>BOLO P-Drive</strong></header>

<div class="wrap">
  <div class="card">
    <h1>Bienvenido</h1>
    <p class="sub">Ingresa a tu cuenta</p>

    <?php if (isset($_GET['e'])): ?>
      <div class="alert">
        <?= $_GET['e']=='1' ? 'Completa email y contraseña.' : 'Credenciales inválidas.' ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>/../api/login.php" autocomplete="on">
      <label>Email</label>
      <input type="email" name="email" placeholder="tu@email.com" required>

      <label>Contraseña</label>
      <input type="password" name="password" placeholder="••••••••" required>

      <div class="row">
        <a class="link" href="<?= BASE_URL ?>/auth/forgot.php">Olvidé mi contraseña</a>
        <a class="link" href="<?= BASE_URL ?>/auth/register.php">Crear cuenta</a>
      </div>

      <button class="btn" type="submit">Ingresar</button>
    </form>
  </div>
</div>
</body>
</html>
