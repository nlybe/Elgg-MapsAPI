define(function (require) {
    var elgg = require('elgg');
    var $ = require('jquery');
    require('amap_ma_googleapis_js')
    require(['elgg/widgets'], function (widgets) {
        widgets.init();
    });
    
    
    var map_settings = require("amap_maps_api/settings");
    
    function foo(){
        var entity_title = $('#entity_title').text();
        var entity_lat = $('#entity_lat').text();
        var entity_lon = $('#entity_lon').text();
        var zoom = parseInt($('#map_zoom').text());
        var map_center = $('#map_center').text();
        var marker = $('#entity_marker').text();
        var gm = google.maps;   

        // ensure that zoom is integer
        if (isNaN(zoom)) {
            zoom = 12;
        }

        infowindow = new google.maps.InfoWindow();
        var myLatlng = new google.maps.LatLng(entity_lat,entity_lon);
        var cLatlng = myLatlng;
        if (map_center) {
            var center_arr = map_center.split(",");
            cLatlng = new google.maps.LatLng(center_arr[0],center_arr[1]);
        }
        
        var mapOptions = {
            zoom: zoom,
            center: cLatlng,
            mapTypeId: gm.MapTypeId.ROADMAP
        }
        
        var location_map = new gm.Map(document.getElementById("location_map"), mapOptions);
        location_map.setMapTypeId(map_settings['default_layer']);
        
        location_map.mapTypes.set("OSM", new google.maps.ImageMapType({
            getTileUrl: function(coord, zoom) {
                // See above example if you need smooth wrapping at 180th meridian
                return map_settings['osm_base_layer'] + zoom + "/" + coord.x + "/" + coord.y + ".png";
            },
            tileSize: new google.maps.Size(256, 256),
            name: "OpenStreetMap",
            maxZoom: 18
        })); 
        
        var marker = new google.maps.Marker({
            map: location_map,
            position: myLatlng,
            title: entity_title,
            icon: marker,
        });                
        google.maps.event.addListener(marker, "click", function() {
          infowindow.setContent(entity_title);
          infowindow.open(location_map, this);
        });  
        
        location_map.setCenter(cLatlng);
        
        return true;	    
    }
    
    $( document ).ready(foo());

});


