<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();
require_role(['conductor']);

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<main style="margin-left:240px;padding:14px">
  <h2>Solicitudes cercanas</h2>

  <div id="mapDriver" style="height:60vh;border:1px solid #ddd;border-radius:10px"></div>

  <div class="card" style="margin-top:12px;max-width:720px">
    <p>Cuando implementes backend, aquí podrás ver solicitudes a 1 km.</p>
  </div>

  <!-- Helper -->
  <script src="<?= BASE_URL ?>/assets/js/gmaps.js"></script>
  <!-- API de Google con callback específico para esta vista -->
  <script>
    window.onGoogleMapsReadyDriver = function(){
      BP.initMap(document.getElementById('mapDriver'), {
        onReady: function(ctx){
          // Aquí luego podrás pintar pines de usuarios solicitando.
          console.log('Mapa (conductor) listo', ctx);
        }
      });
    };
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode(GOOGLE_MAPS_API_KEY) ?>&callback=onGoogleMapsReadyDriver" async defer></script>
    
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
