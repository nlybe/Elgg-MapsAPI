<?php
/**
 * Elgg MembersMap Plugin
 * @package membersmap 
 */


elgg_load_library('elgg:amap_maps_api');  
// elgg_load_library('elgg:amap_maps_api_geocoder');  // OBS

elgg_require_js("amap_ma_googleapis_js");

$zoom = $vars['zoom'];
$location = $vars['location'];
$marker = $vars['marker'];

if(!isset($zoom)){
	$zoom = amap_ma_get_map_zoom();
} 
if(!isset($location)){
	$location = AMAP_MA_CUSTOM_DEFAULT_COORDS;
}  

?>

<div id="map_canvas" style="overflow:hidden; width:100%; height:250px"></div>
<script type='text/javascript'>
    // Delayed load is required, or elgg page continually reloads
    $(document).ready(function () { initialize();  });
    var geocoder = new google.maps.Geocoder();
    function getAddress(address, callback) {
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                callback(results[0].geometry.location);
            } 
        });
    }	
    function initialize() {
        getAddress("<?php echo $location;?>", function(defaultLocation) {
            var map = new google.maps.Map(document.getElementById("map_canvas"),{ 
                center: defaultLocation,
                zoom: <?php echo $zoom;?>, 
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });                       
            var myLatlng = new google.maps.LatLng(<?php echo $location;?>);
            var marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                icon: '<?php echo $marker;?>'
            });                        
        });
    };
</script>

