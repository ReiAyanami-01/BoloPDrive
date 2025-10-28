<?php require_once __DIR__.'/../../config/config.php'; ?>
<?php require_once __DIR__.'/../../config/auth.php'; ?>
<?php require_login(); ?>
<?php include __DIR__.'/../../partials/header.php'; ?>
<?php include __DIR__.'/../../partials/sidebar.php'; ?>

<main style="margin-left:240px;padding:14px">
  <h2>Bienvenido, usuario</h2>

  <div id="map" style="height:60vh;border:1px solid #ddd;border-radius:10px"></div>

  <script>
    // si usas Leaflet (ya tienes leaflet.js y leaflet.css):
    document.addEventListener('DOMContentLoaded', function(){
      var map = L.map('map').setView([13.69294, -89.21819], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap'
      }).addTo(map);
      // ...tu lógica para conductores/usuarios cercanos
    });
  </script>
</main>

<?php include __DIR__.'/../../partials/footer.php'; ?>
