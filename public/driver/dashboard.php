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
  <h2>Panel del conductor</h2>

  <div id="mapDriverHome" style="height:60vh;border:1px solid #ddd;border-radius:10px"></div>

  <div class="card" style="margin-top:12px;max-width:720px">
    <p>Aquí verás tu ubicación actual. (MVP sin solicitudes)</p>
  </div>

  <!-- Helper -->
  <script src="<?= BASE_URL ?>/assets/js/gmaps.js"></script>
  <!-- Google Maps con callback -->
  <script>
    window.onGoogleMapsReadyDriverHome = function(){
      BP.initMap(document.getElementById('mapDriverHome'), {
        onReady: function(ctx){
          console.log('Mapa (driver dashboard) listo', ctx);
        }
      });
    };
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode(GOOGLE_MAPS_API_KEY) ?>&callback=onGoogleMapsReadyDriverHome" async defer></script>
</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
