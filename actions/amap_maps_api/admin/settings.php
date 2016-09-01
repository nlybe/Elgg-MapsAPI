<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_load_library('elgg:amap_maps_api');
$plugin = elgg_get_plugin_from_id(AMAP_MA_PLUGIN_ID);

$params = get_input('params');
foreach ($params as $k => $v) {
    // geolocate default location
    if ($k == 'map_default_location' && !empty($v)) {
    $coords = amap_ma_geocode_location($v); 
        if ($coords) { 
            $plugin->setSetting('map_default_lat', $coords['lat']);
            $plugin->setSetting('map_default_lng', $coords['long']);
        } 		
    }

    if (!$plugin->setSetting($k, $v)) {
        register_error(elgg_echo('plugins:settings:save:fail', array(AMAP_MA_PLUGIN_ID)));
        forward(REFERER);
    }
}


system_message(elgg_echo('amap_maps_api:settings:save:ok'));
forward(REFERER);
