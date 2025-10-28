<?php require_once __DIR__ . '/../config/auth.php'; ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>BOLO P-Drive</title>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/leaflet.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
<script src="<?= BASE_URL ?>/assets/js/leaflet.js"></script>

<style>

  
    body.menu-hidden aside { 
    display: none !important;            /* lo saca del layout */
  }
  body.menu-hidden main { 
    margin-left: 0 !important;           /* el contenido ocupa todo el ancho */
  }
  /* Estilos mínimos del botón hamburguesa */
  .hamb-btn{
    appearance:none; border:0; background:transparent; color:#eaeaea;
    font-size:22px; line-height:1; padding:8px 10px; border-radius:8px; cursor:pointer;
  }
  .hamb-btn:hover{ background: rgba(255,255,255,.08); }
  .brand{ display:flex; align-items:center; gap:10px; }
  @media (min-width: 900px){
    .hamb-btn{ display:none; } /* en desktop, ocúltalo si quieres */
  }

  /* Overrides para desplazar el main cuando colapsa (porque tus páginas usan margin-left inline) */
  body.menu-collapsed aside{ width: 72px !important; }
  body.menu-collapsed aside a{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  body.menu-collapsed main{ margin-left: 90px !important; } /* sobreescribe los inline con !important */
</style>
</head>

<?php $__role = logged_in() ? user_role() : 'guest'; ?>
<body data-role="<?= htmlspecialchars($__role) ?>">
<header>
  <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;">
    <div class="brand">
<button class="hamb-btn" id="hambToggle" type="button" aria-label="Abrir/cerrar menú">☰</button>
      <strong>BOLO P-Drive</strong>
    </div>
    <div style="font-size:12px;opacity:.85">
      <span class="badge">Tema: <?= htmlspecialchars(strtoupper($__role)) ?></span>
    </div>
  </div>
</header>

<script>
  (function(){
    var btn = document.getElementById('hambToggle');
    if (!btn) return;

    // Toggle simple (tu comportamiento actual)
    btn.addEventListener('click', function(){
      document.body.classList.toggle('menu-collapsed');
    });

    // Cierra el menú al navegar por un enlace del sidebar (no cambia tu UX en desktop)
    var aside = document.querySelector('aside');
    if (aside) {
      aside.addEventListener('click', function(e){
        var a = e.target.closest('a');
        if (!a) return;
        document.body.classList.remove('menu-collapsed');
      });
    }
  })();
</script>

