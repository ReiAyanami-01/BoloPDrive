<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['admin', 3]); // acepta nombre o id

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';

$pdo = pdo();

// Helper: si la consulta falla (tabla/columna no existe), devuelve 0
function safeCount(PDO $pdo, string $sql): int {
  try { return (int)$pdo->query($sql)->fetchColumn(); }
  catch (Throwable $e) { return 0; }
}

$totUsuarios = safeCount($pdo, "SELECT COUNT(*) FROM usuarios WHERE rol_id=1");
$totConduct  = safeCount($pdo, "SELECT COUNT(*) FROM usuarios WHERE rol_id=2");
$pendSol     = safeCount($pdo, "SELECT COUNT(*) FROM solicitudes WHERE estado='pendiente'");
$viajesHoy   = safeCount($pdo, "SELECT COUNT(*) FROM viajes WHERE DATE(creado_en)=CURDATE()");

/* 🔹 NUEVOS CONTADORES (MVP, robustos) */
$peticionesConductores = safeCount($pdo, "SELECT COUNT(*) FROM ofertas");
$aceptacionesChoferes  = safeCount($pdo, "
  SELECT COUNT(*) FROM ofertas
  WHERE LOWER(estado) IN ('aceptada','aceptado')
");
?>
<style>
  :root{
    --pad:16px; --radius:14px; --line:#e5e7eb;
    --shadow:0 10px 26px rgba(20,30,60,.10);
    --muted:#6b7280;
  }

  /* ===== Layout base de escritorio ===== */
  main.dashboard {
    margin-left: 240px;           /* sidebar fijo de tu tema */
    padding: 12px;
  }
  .cards{ display:grid; gap:12px; }
  .cards.metrics{ grid-template-columns: repeat(4, minmax(0,1fr)); max-width:1100px; margin-bottom:12px }
  .cards.extra   { grid-template-columns: repeat(2, minmax(0,1fr)); max-width:1100px }
  .card{
    background:#fff; border:1px solid var(--line); border-radius:var(--radius);
    padding:12px; box-shadow: var(--shadow);
  }

  /* Botón menú móvil (aparece solo en pantallas pequeñas) */
  .mobile-menu-btn{
    display:none; position:sticky; top:8px; z-index:45;
    border:0; background:#111827; color:#fff; border-radius:12px;
    height:44px; padding:0 12px; margin-bottom:8px;
  }

  /* ===== MODO MÓVIL ===== */
  @media (max-width: 540px){
    /* el main ya no deja margen (sidebar será off-canvas) */
    main.dashboard{ margin-left:0; padding: 10px var(--pad) 18px; }

    /* sidebar off-canvas (usa #sidebar si existe; si no, .sidebar) */
    #sidebar, .sidebar{
      position: fixed; inset:56px 0 0 auto;     /* bajo el header sticky */
      width: 82vw; max-width:320px; z-index: 40;
      background:#fff; border-left:1px solid var(--line);
      transform: translateX(-105%); transition: transform .25s ease;
      box-shadow: 0 20px 40px rgba(0,0,0,.18);
    }
    #sidebar.sidebar--open, .sidebar.sidebar--open{ transform: translateX(0); }

    /* Backdrop */
    .bd-admin{
      position:fixed; inset:0; background:rgba(0,0,0,.35);
      opacity:0; pointer-events:none; transition:opacity .2s; z-index:35;
    }
    .bd-admin.show{ opacity:1; pointer-events:auto; }

    .mobile-menu-btn{ display:inline-flex; align-items:center; gap:8px; }

    /* Tarjetas en una columna (dos si hay algo más de espacio) */
    .cards.metrics{ grid-template-columns: 1fr; }
    .cards.extra{ grid-template-columns: 1fr; }
    @media (min-width: 420px){
      .cards.metrics{ grid-template-columns: 1fr 1fr; }
      .cards.extra{ grid-template-columns: 1fr 1fr; }
    }
  }
</style>

<main class="dashboard">
  <!-- Botón hamburguesa (solo móvil) -->

  <h2 style="margin:6px 0 12px">Dashboard Admin</h2>

  <!-- Métricas base -->
  <div class="cards metrics">
    <div class="card">Usuarios: <strong><?= $totUsuarios ?></strong></div>
    <div class="card">Conductores: <strong><?= $totConduct ?></strong></div>
    <div class="card">Solicitudes pendientes: <strong><?= $pendSol ?></strong></div>
    <div class="card">Viajes hoy: <strong><?= $viajesHoy ?></strong></div>
  </div>

  <!-- Nuevos contadores -->
  <div class="cards extra">
    <div class="card">Peticiones de conductores: <strong><?= $peticionesConductores ?></strong></div>
    <div class="card">Aceptaciones de choferes: <strong><?= $aceptacionesChoferes ?></strong></div>
  </div>

  <div class="card" style="margin-top:14px;max-width:1100px">
    <p style="margin:0">
      Usa el menú lateral para gestionar <strong>Usuarios</strong>, <strong>Conductores</strong> e <strong>Interacciones</strong>.
      <span style="color:var(--muted)">En móvil, abre el menú con “☰”.</span>
    </p>
  </div>
</main>

<!-- Backdrop móvil -->
<div class="bd-admin" id="bdAdmin"></div>

<script>
  (function(){
    const btn = document.getElementById('menuToggle');
    const bd  = document.getElementById('bdAdmin');

    // intenta encontrar el sidebar por id o por clase
    const sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');

    if (!btn || !sidebar || !bd) return;

    function openMenu(){
      sidebar.classList.add('sidebar--open');
      bd.classList.add('show');
      btn.setAttribute('aria-expanded','true');
    }
    function closeMenu(){
      sidebar.classList.remove('sidebar--open');
      bd.classList.remove('show');
      btn.setAttribute('aria-expanded','false');
    }

    btn.addEventListener('click', () => {
      const open = sidebar.classList.contains('sidebar--open');
      open ? closeMenu() : openMenu();
    });
    bd.addEventListener('click', closeMenu);
    window.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });

    // Cierra al navegar dentro del menú (enlaces del sidebar)
    sidebar.addEventListener('click', e => {
      if (e.target.tagName === 'A') closeMenu();
    });
  })();
</script>

<?php include __DIR__ . '/../../partials/footer.php'; ?>
