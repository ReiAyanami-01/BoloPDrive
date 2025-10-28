(function (w) {
  w.BP = w.BP || {};

  /**
   * Inicializa un mapa de Google en 'el'.
   * - Centra en geolocalización si está disponible; si no, usa San Salvador.
   * - Crea un marcador "Tu ubicación".
   * - Llama a opts.onReady({ map, me }) cuando el mapa está listo.
   */
  w.BP.initMap = function (el, opts) {
    opts = opts || {};
    var fallbackCenter = { lat: 13.69294, lng: -89.21819 }; // San Salvador

    function makeMap(center) {
      var map = new google.maps.Map(el, {
        center: center,
        zoom: 14,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
      });

      var me = new google.maps.Marker({
        position: center,
        map: map,
        title: 'Tu ubicación',
      });

      if (opts.onReady) opts.onReady({ map: map, me: me });
    }

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (pos) {
          makeMap({ lat: pos.coords.latitude, lng: pos.coords.longitude });
        },
        function () {
          makeMap(fallbackCenter);
        },
        { enableHighAccuracy: true, timeout: 5000 }
      );
    } else {
      makeMap(fallbackCenter);
    }
  };
})(window);
