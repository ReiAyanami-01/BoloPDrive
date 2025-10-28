<?php declare(strict_types=1);
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';
require_login();
require_role(['usuario']);

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<main style="margin-left:240px;padding:14px">
  <h2>Buscar conductor</h2>

  <div id="mapUser" style="height:60vh;border:1px solid #ddd;border-radius:10px"></div>

  <div class="card" style="margin-top:12px;max-width:720px">
    <button id="btnSolicitar" type="button" class="btn">Solicitar conductor</button>
  </div>

  <script>
    function initMapUser(){
      var center = { lat: 13.69294, lng: -89.21819 };
      var map = new google.maps.Map(document.getElementById('mapUser'), {
        center: center,
        zoom: 14,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true
      });
      new google.maps.Marker({ position: center, map: map, title: 'Centro de prueba' });
      console.log('Mapa (prueba mínima) OK');
    }

    setTimeout(function(){
      if (typeof google === 'undefined' || !google.maps) {
        alert('Google Maps no se cargó. Revisa API key / billing / referrers / Brave Shields.');
        console.error('Google no cargó. Revisa consola para ver el error de la API.');
      }
    }, 4000);
  </script>

  <!-- Usa la constante de secrets.php -->
  <script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars(GOOGLE_MAPS_API_KEY, ENT_QUOTES) ?>&callback=initMapUser" async defer></script>
<script>
(function(){
  // 1) Actualiza tu ubicación cada 10s
  function pushLocation(lat, lng){
    const fd = new FormData();
    fd.append('lat', lat);
    fd.append('lng', lng);
    fetch('<?= BASE_URL ?>/../api/location_update.php', { method: 'POST', body: fd });
  }

  // 2) Guarda marker propio y marcadores de conductores por id
  let myPos = { lat: 13.69294, lng: -89.21819 };
  let driverMarkers = {}; // id -> google.maps.Marker
  let mapRef = null;

  // Si usas Google:
  window.initMapUser = function(){
    const center = myPos;
    const map = new google.maps.Map(document.getElementById('mapUser'), { center, zoom: 14 });
    mapRef = map;
    const me = new google.maps.Marker({ position: center, map, title:'Tú' });

    // Geolocalización
    if (navigator.geolocation){
      navigator.geolocation.watchPosition(function(p){
        myPos = { lat: p.coords.latitude, lng: p.coords.longitude };
        me.setPosition(myPos);
        pushLocation(myPos.lat, myPos.lng);
      }, null, { enableHighAccuracy:true });
    }

    // Poll cada 7s: conductores cercanos
    async function poll(){
      try{
        const url = '<?= BASE_URL ?>/../api/nearby_drivers.php?lat=' + myPos.lat + '&lng=' + myPos.lng + '&km=1';
        const r = await fetch(url);
        const j = await r.json();
        if (!j.ok) return;
        // pintar/actualizar
        const seen = {};
        j.drivers.forEach(d => {
          seen[d.id] = true;
          if (!driverMarkers[d.id]){
            driverMarkers[d.id] = new google.maps.Marker({
              position: {lat: parseFloat(d.lat), lng: parseFloat(d.lng)},
              map: mapRef,
              title: (d.nombre || 'Conductor') + ' (' + d.dist_km.toFixed(2) + ' km)'
            });
          } else {
            driverMarkers[d.id].setPosition({lat: parseFloat(d.lat), lng: parseFloat(d.lng)});
          }
        });
        // quitar los que ya no están
        Object.keys(driverMarkers).forEach(id => {
          if (!seen[id]) { driverMarkers[id].setMap(null); delete driverMarkers[id]; }
        });
      }catch(e){}
    }
    setInterval(poll, 7000);
  };
})();
</script>

</main>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
