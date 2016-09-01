<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_load_library('elgg:amap_maps_api');
$map_settings = elgg_get_plugin_from_id('amap_maps_api')->getAllSettings();

$settings = [
    'd_location_lat' => amap_ma_get_map_default_location_lat(),
    'd_location_lon' => amap_ma_get_map_default_location_lon(),
    'default_zoom' => amap_ma_get_map_zoom(),
    'cluster' => amap_ma_get_map_clustering(),
    'cluster_zoom' => AMAP_MA_CUSTOM_CLUSTER_ZOOM,
    'unitmeas' => elgg_extract('unitmeas', $map_settings),
    'layers' => json_encode(amap_ma_get_map_layers()),
    'osm_base_layer' => amap_ma_get_osm_base_layer(),
    'default_layer' => amap_ma_get_map_default_layer(),
];

?>

define(<?php echo json_encode($settings); ?>);
