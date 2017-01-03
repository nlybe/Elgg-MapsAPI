define(function (require) {
    var elgg = require('elgg');
	var $ = require('jquery');
    require('amap_ma_googleapis_js')

    $( document ).ready(function() {
        var entity_title = $('#entity_title').text();
        var entity_lat = $('#entity_lat').text();
        var entity_lon = $('#entity_lon').text();
        var zoom = parseInt($('#map_zoom').text());
        var marker = $('#entity_marker').text();
        var gm = google.maps;

        // ensure that zoom is integer
        if (isNaN(zoom))
            zoom = 12;

        infowindow = new google.maps.InfoWindow();
        var myLatlng = new google.maps.LatLng(entity_lat,entity_lon);
        var mapOptions = {
            zoom: zoom,
            center: myLatlng,
            mapTypeId: gm.MapTypeId.ROADMAP
        }

        var map = new gm.Map(document.getElementById("map"), mapOptions);
        var marker = new google.maps.Marker({
            map: map,
            position: myLatlng,
            title: entity_title,
            icon: marker,
        });
        google.maps.event.addListener(marker, "click", function() {
          infowindow.setContent(entity_title);
          infowindow.open(map, this);
        });
        map.setCenter(marker.getPosition());

        return true;
    });

});
