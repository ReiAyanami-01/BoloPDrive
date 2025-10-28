<?php $rol = user_role(); ?>
<aside style="width:220px;float:left;border-right:1px solid #ddd;padding:12px;min-height:calc(100vh - 51px)">
  
  <div style="margin-bottom:8px"><strong>Menú (<?= htmlspecialchars($rol) ?>)</strong></div>
  <hr>
  <nav>
  <?php if ($rol==='usuario'): ?>
    <div><a href="<?= BASE_URL ?>/user/dashboard.php"><span class="ico">🏠</span><span class="txt">Inicio</span></a></div>
    <div><a href="<?= BASE_URL ?>/user/search.php"><span class="ico">🚗</span><span class="txt">Buscar conductor</span></a></div>
    <div><a href="<?= BASE_URL ?>/user/profile.php"><span class="ico">👤</span><span class="txt">Mi perfil</span></a></div>
    <div><a href="<?= BASE_URL ?>/user/settings.php"><span class="ico">⚙️</span><span class="txt">Configuraciones</span></a></div>
    <div><a href="<?= BASE_URL ?>/chatbot/chatbot.php"><span class="ico">🤖</span><span class="txt">Chatbot</span></a></div> <!-- Chatbot button -->
  <?php elseif ($rol==='conductor'): ?>
    <div><a href="<?= BASE_URL ?>/driver/dashboard.php"><span class="ico">🏠</span><span class="txt">Inicio</span></a></div>
    <div><a href="<?= BASE_URL ?>/driver/requests.php"><span class="ico">📍</span><span class="txt">Solicitudes</span></a></div>
    <div><a href="<?= BASE_URL ?>/driver/profile.php"><span class="ico">👤</span><span class="txt">Mi perfil</span></a></div>
    <div><a href="<?= BASE_URL ?>/driver/settings.php"><span class="ico">⚙️</span><span class="txt">Configuraciones</span></a></div>
    <div><a href="<?= BASE_URL ?>/chatbot/chatbot.php"><span class="ico">🤖</span><span class="txt">Chatbot</span></a></div> <!-- Chatbot button -->
  <?php else: ?>
    <div><a href="<?= BASE_URL ?>/admin/dashboard.php"><span class="ico">📊</span><span class="txt">Dashboard</span></a></div>
    <div><a href="<?= BASE_URL ?>/admin/users.php"><span class="ico">👥</span><span class="txt">Usuarios</span></a></div>
    <div><a href="<?= BASE_URL ?>/admin/interactions.php"><span class="ico">🧾</span><span class="txt">Interacciones</span></a></div>
    <div><a href="<?= BASE_URL ?>/admin/drivers.php"><span class="ico">🚘</span><span class="txt">Conductores</span></a></div>
    <div><a href="<?= BASE_URL ?>/chatbot/chatbot.php"><span class="ico">🤖</span><span class="txt">Chatbot</span></a></div> <!-- Chatbot button -->
  <?php endif; ?>
  </nav>
  <hr>
  <div><a href="<?= BASE_URL ?>/auth/logout.php"><span class="ico">↩️</span><span class="txt">Logout</span></a></div>
</aside>
