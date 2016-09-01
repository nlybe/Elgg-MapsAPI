<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_load_library('elgg:amap_maps_api');

$entity = $vars["entity"];
$map_width = $vars['map_width'];
$map_height = $vars['map_height'];
$map_zoom = $vars['map_zoom'];
$marker = $vars['marker'];
$show_map = $vars["show_map"];

if($map_zoom == '' || !is_numeric($map_zoom)){
    $map_zoom = amap_ma_get_map_zoom();
}  

if (elgg_instanceof($entity) && $show_map) {

    if ($entity->getLatitude() && $entity->getLongitude()) {
        elgg_load_library('elgg:amap_maps_api');  
        elgg_require_js("amap_maps_api/location_map");    
    
        if (($entity instanceof \ElggUser) || ($entity instanceof \ElggGroup))
            $title_cleared = amap_ma_remove_shits($entity->name);
        else
            $title_cleared = amap_ma_remove_shits($entity->title);
            
        echo elgg_format_element('span', ['id' => 'entity_title', 'style' => 'display:none;'], $title_cleared);
        echo elgg_format_element('span', ['id' => 'entity_lat', 'style' => 'display:none;'], $entity->getLatitude());
        echo elgg_format_element('span', ['id' => 'entity_lon', 'style' => 'display:none;'], $entity->getLongitude());
        echo elgg_format_element('span', ['id' => 'map_zoom', 'style' => 'display:none;'], $map_zoom);
        if (!empty($marker)) {
            echo elgg_format_element('span', ['id' => 'entity_marker', 'style' => 'display:none;'], $marker);
        }
?>

        <div id="map" style="width:<?php echo $map_width; ?>; height:<?php echo $map_height; ?>;"></div>

<?php 
    }
    else {
        echo $vars["value"];
    }
}
else {
    echo $vars["value"];
}
?>



