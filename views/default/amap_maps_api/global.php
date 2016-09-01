<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */
 
// access check for closed groups
group_gatekeeper();

$entities = $vars['entities'];
$mapwidth = $vars['mapwidth'];
$mapheight = $vars['mapheight'];
$defaultlocation = $vars['defaultlocation'];
$defaultzoom = $vars['defaultzoom'];
$defaultcoords = $vars['defaultcoords'];
$clustering = $vars['clustering'];
$clustering_zoom = $vars['clustering_zoom'];
$layers = $vars['layers'];
$default_layer = $vars['default_layer'];
$osm_base_layer = $vars['osm_base_layer'];

// load google maps js
//elgg_require_js('amap_ma_googleapi_js');
//elgg_require_js('amap_ma_googleapis_js');
elgg_require_js('amap_ma_markerclusterer_js');
elgg_require_js('amap_ma_oms_js');
    
$add_layers = '';
foreach ($layers as $l) {
	$add_layers .= 'mapTypeIds.push("'.$l.'");';
}
?>

<script type="text/javascript"><!--
    var gm = google.maps;   
	var markers_user = [];
	var markers_group = [];
	var markers_agora = [];     
	var markers_page = [];
	var map;
	var mapTypeIds = [];
	<?php echo $add_layers;?>
	
    $(document).ready(function(){

		infowindow = new google.maps.InfoWindow();
		var myLatlng = new google.maps.LatLng(<?php echo $defaultcoords;?>);
					
		var mapOptions = {
			zoom: <?php echo $defaultzoom;?>,
			center: myLatlng,
			mapTypeControlOptions: {
				mapTypeIds: mapTypeIds
			},
		};
		
		map = new gm.Map(document.getElementById("map"), mapOptions);
		map.setMapTypeId("<?php echo $default_layer;?>");
		
		var markerBounds = new google.maps.LatLngBounds();
		var geocoder = new google.maps.Geocoder();

		map.mapTypes.set("OSM", new google.maps.ImageMapType({
			getTileUrl: function(coord, zoom) {
				// See above example if you need smooth wrapping at 180th meridian
				return "<?php echo $osm_base_layer; ?>" + zoom + "/" + coord.x + "/" + coord.y + ".png";
			},
			tileSize: new google.maps.Size(256, 256),
			name: "OpenStreetMap",
			maxZoom: 18
		}));
				
		//////////////////// Spiderfier feature start ////////////////////
		//var iw = new gm.InfoWindow(); //OBS
		var oms = new OverlappingMarkerSpiderfier(map,{markersWontMove: true, markersWontHide: true, keepSpiderfied: true});
		//////////////////// Spiderfier feature end ////////////////////
		
		$('#chbx_user').click(function() {
			if ($(this).is(':checked'))
				showOverlays(markers_user);
			else
				clearOverlays(markers_user);
		});	 
		$('#chbx_group').click(function() {
			if ($(this).is(':checked'))
				showOverlays(markers_group);
			else
				clearOverlays(markers_group);
		});	
		$('#chbx_agora').click(function() {
			if ($(this).is(':checked'))
				showOverlays(markers_agora);
			else
				clearOverlays(markers_agora);
		});	
		$('#chbx_pages').click(function() {
			if ($(this).is(':checked'))
				showOverlays(markers_page);
			else
				clearOverlays(markers_page);
		});	

		showentities(geocoder, -1, 0, markerBounds, map, 0, 0,oms); 
    });
    
    // search area
    function codeAddress(givenaddr) {
		codeAddressExtend(givenaddr,<?php echo $defaultzoom;?>,'<?php echo elgg_get_site_url();?>','<?php echo $defaultlocation;?>','<?php echo elgg_echo("amap_maps_api:map:2");?>', 0, <?php echo amap_ma_get_unit_of_measurement();?>, '<?php echo amap_ma_get_unit_of_measurement_string_simple();?>', mapTypeIds, map.getMapTypeId(), '<?php echo $osm_base_layer; ?>');
    } 
    
	function clearOverlays(arrMarkers) {
	  if (arrMarkers) {
		for( var i = 0, n = arrMarkers.length; i < n; ++i ) {
		  arrMarkers[i].setMap(null);
		}
	  }
	}    
	
	function showOverlays(arrMarkers) {
	  if (arrMarkers) {
		for( var i = 0, n = arrMarkers.length; i < n; ++i ) {
		  arrMarkers[i].setMap(map);
		}
	  }
	}	
	
    function showentities(geocoder, radius, showradius, markerBounds, map, www1, www2, oms) {
        var ddd;
        var markers = [];

<?php
    foreach ($entities as $u)  {
		if ($u->getLatitude() && $u->getLongitude())  {
			if (elgg_instanceof($u, 'user')) {     
				$namecleared = amap_ma_remove_shits($u->name);
				$entity_description = amap_ma_get_entity_description($u->briefdescription);
				$entity_location = amap_ma_remove_shits($u->location);		
			}  
			else if (elgg_instanceof($u, 'group')) {     
				$namecleared = amap_ma_remove_shits($u->name);
				$entity_description = '';
				$entity_location = amap_ma_remove_shits($u->grouplocation);		
			}   
			else if (elgg_instanceof($u, 'object', 'agora')) {     
				$namecleared = amap_ma_remove_shits($u->title);
				$entity_description = amap_ma_get_entity_description($u->description);
				$entity_location = amap_ma_remove_shits($u->location);
			}   
			else if (elgg_instanceof($u, 'object', 'page') || elgg_instanceof($u, 'object', 'page_top')) {       
				$namecleared = amap_ma_remove_shits($u->title);
				$entity_description = amap_ma_get_entity_description($u->description);
				$entity_location = '';
			}   				
			$icon = amap_ma_get_entity_icon($u);
			$entity_title = amap_ma_get_entity_title($u, $namecleared); 
			$entity_img = amap_ma_get_entity_img($u, $namecleared);
					
?> 
			if (radius>=0) ddd = calcDistance(www1, www2, <?php echo $u->getLatitude();?>, <?php echo $u->getLongitude();?>);
			if (ddd <= radius || radius<0)   {
				
				var myLatlng = new google.maps.LatLng(<?php echo $u->getLatitude();?>,<?php echo $u->getLongitude();?>);
				var marker = new google.maps.Marker({
					map: map,
					position: myLatlng,
					title: '<?php echo $namecleared;?>',
					icon: '<?php echo $icon;?>'
				});                
				google.maps.event.addListener(marker, 'click', function() {
				  infowindow.setContent('<?php echo '<div class="infowindow">'.$entity_img.' '.$entity_title.'<br/>'.$entity_location.'<br/>'.$entity_description.'</div>';?>');
				  infowindow.open(map, this);
				});  
				oms.addMarker(marker);  // Spiderfier feature
				//markers.push(marker);
				 
				<?php if (elgg_instanceof($u, 'user')) {?> markers_user.push(marker); <?php } ?>
				<?php if (elgg_instanceof($u, 'group')) {?> markers_group.push(marker); <?php } ?>
				<?php if (elgg_instanceof($u, 'object', 'agora')) {?> markers_agora.push(marker); <?php } ?>
				<?php if (elgg_instanceof($u, 'object', 'page') || elgg_instanceof($u, 'object', 'page_top')) {?> markers_page.push(marker); <?php } ?>

				if (!showradius.checked)    {
					markerBounds.extend(myLatlng);
					map.fitBounds(markerBounds);                    
				} 
			} 
<?php
		}
        
    }
    
    if ($clustering)    {
?> 
		var array1 = markers_user;
		var array2 = array1.concat(markers_group);
		var array3 = array2.concat(markers_agora);
		var array4 = array3.concat(markers_page);
        var markerCluster = new MarkerClusterer(map, array4, {
          maxZoom: <?php echo $clustering_zoom;?>
        });        
<?php
    }

	// release array to help memory
    unset($entities);
?>        
    } // end of showentities
  
  //--></script>

