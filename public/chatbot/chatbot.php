<?php require_once __DIR__.'/../../config/config.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Chatbot (Simulación) — BOLO P-Drive</title>
  <style>
    :root{
      --bg: #f6f8ff;
      --line: #d7def0;
      --txt: #1e293b;
      --muted: #6b7280;
      --primary: #6aa3ff;
      --primary-2: #7eb0ff;
      --danger-bg: #fff3f3;
      --danger-bd: #ffd0d0;
      --danger-tx: #8a2b2b;
      --radius: 16px;
      --shadow: 0 10px 28px rgba(20,30,60,.12);
      --fs-h1: 22px;
      --fs-body: 15px;
      --height-touch: 48px;
      --pad-page: 16px;
    }

    * {box-sizing: border-box;}
    html, body {height: 100%; margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--txt);}
    body {line-height: 1.35; padding: env(safe-area-inset-top, 0) env(safe-area-inset-right, 0) env(safe-area-inset-bottom, 0) env(safe-area-inset-left, 0);}
    header {position: sticky; top: 0; padding: 12px var(--pad-page); border-bottom: 1px solid var(--line); background: linear-gradient(90deg, #121723, #161c2a); color: #eaf0ff;}
    .wrap {display: grid; place-items: center; min-height: calc(100vh - 52px); padding: 12px var(--pad-page) 24px;}
    .card {width: 100%; max-width: 440px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); padding: 24px; position: relative;}
    .card::before {content: ""; position: absolute; inset: 0 0 auto 0; height: 4px; background: linear-gradient(90deg, #a9b8ff, var(--primary), #a9b8ff); opacity: .9;}
    h1 {font-size: var(--fs-h1); margin: 6px 0 8px;}
    p.sub {margin: 0 0 14px; color: var(--muted);}
    label {display: block; margin: 10px 0 6px; font-weight: 600;}
    input {width: 100%; height: var(--height-touch); padding: 10px 12px; border: 1px solid var(--line); border-radius: 12px; font-size: 16px; background: #fff; outline: none;}
    input:focus {border-color: var(--primary); box-shadow: 0 0 0 4px rgba(106, 163, 255, .2);}
    .btn {appearance: none; border: 0; padding: 12px 14px; border-radius: 12px; cursor: pointer; font-weight: 700; color: #fff; background: var(--primary); width: 100%; box-shadow: 0 10px 22px rgba(106, 163, 255, .25); transition: filter .15s, transform .08s, box-shadow .2s;}
    .btn:hover {filter: brightness(1.03); transform: translateY(-1px); box-shadow: 0 16px 30px rgba(106, 163, 255, .30);}
    .btn-ghost {background: #fff; border: 1px solid var(--line); color: #223354;}
    .btn-ghost:hover {background: #f7faff;}
    .alert {background: var(--danger-bg); border: 1px solid var(--danger-bd); color: var(--danger-tx); padding: 10px 12px; border-radius: 12px; margin-bottom: 10px; font-size: 14px;}
    .hint {margin-top: 10px; color: var(--muted); font-size: 13px;}
    .actions {display: flex; gap: 10px; justify-content: space-between; margin-top: 12px;}
    @media (max-width: 520px) { .actions {flex-direction: column;} }
  </style>
</head>
<body>
<header><strong>BOLO P-Drive</strong></header>

<div class="wrap">
  <div class="card">
    <h1>Chatbot (Simulación)</h1>
    <p class="sub">Hola, ¿en qué puedo ayudarte hoy? Puedes preguntar sobre lugares, tarifas y precios, o información sobre leyes contra y a favor del alcoholismo.</p>

    <div style="margin-top: 10px;">
      <div style="background: #f1f5fe; padding: 12px; border-radius: 12px; margin-bottom: 10px;">
        <strong>¡Hola! ¿Me puedes decir las tarifas de transporte?</strong>
      </div>
      <div style="background: #e9efff; padding: 12px; border-radius: 12px; margin-bottom: 10px;">
        Lo siento, este chatbot aún está en desarrollo. Estará disponible después del MVP. ¡Gracias por tu paciencia!
      </div>
    </div>

    <form action="javascript:void(0);">
      <label for="userMessage">Escribe tu mensaje:</label>
      <input id="userMessage" type="text" name="message" placeholder="Escribe aquí..." required>
      <button class="btn" type="submit">Enviar</button>
    </form>

    <div class="actions">
      <a href="<?= BASE_URL ?>/user/dashboard.php" class="btn btn-ghost">Regresar al Menú</a>
    </div>
  </div>
</div>
</body>
</html>
