<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_load_library('elgg:amap_maps_api');
elgg_require_js("amap_ma_latlon_picker");

$entity = elgg_extract("entity", $vars, "");
$hide_label = elgg_extract("hide_label", $vars, true);
$hide_coords_box = elgg_extract("hide_coords_box", $vars, false);

$lat = '';
$lon = '';

if (!$entity)	{
    $entity = get_entity(elgg_get_page_owner_guid());
}

if ($entity)	{
    $lat = $entity->getLatitude();
    $lon = $entity->getLongitude();
}

// get location directly from value if available (e.g. used by profile_manager)
$location = elgg_extract("value", $vars, "");
if (!$location && $entity)	{
    // if empty location but entity is available, then get the entity location
    $location = $entity->location;
}

if (!$hide_label) {
    echo elgg_format_element('label', [], elgg_echo("amap_maps_api:form:setlocation"));
}
?> 

<fieldset class="gllpLatlonPicker">
<?php 
    $search_box .= elgg_view("input/location_autocomplete", array(
        'name' => 'location',
        'value' => $location,
        'class' => 'gllpSearchField',
    ));	
    $search_box .= elgg_view("input/button", array(
        'name' => 'submit',
        'id' => 'search_loc',
        'value' => elgg_echo("amap_maps_api:form:search"),
        'class' => 'gllpSearchButton elgg-button elgg-button-submit',
    ));	
    $search_box .= elgg_format_element('p', ['class' => 'location_input_pre'], elgg_echo('amap_maps_api:location_input:pre'));
    echo elgg_format_element('div', [], $search_box);
        
    //$coords_box = elgg_echo("amap_maps_api:form:latlon"); //OBS
    $coords_box .= elgg_view("input/hidden", array(
        'name' => 'latitude',
        'value' => ($lat?$lat:amap_ma_get_map_default_location_lat()),
        'class' => 'gllpLatitude',
    ));	
    //$coords_box .= ' / '; //OBS
    $coords_box .= elgg_view("input/hidden", array(
        'name' => 'longitude',
        'value' => ($lon?$lon:amap_ma_get_map_default_location_lon()),
        'class' => 'gllpLongitude',
    ));
    $coords_box .= elgg_view("input/hidden", array(
        'name' => 'map_zoom',
        'value' => ($entity->map_zoom?$entity->map_zoom:amap_ma_get_map_zoom()),
        'class' => 'gllpZoom',
    ));
    $coords_box .= elgg_view("input/hidden", array(
        'name' => 'map_center',
        'value' => ($entity->map_center?$entity->map_center:''),
        'class' => 'gllpCenter',
    ));    
        
    echo elgg_format_element('div', ['style' => ($hide_coords_box?'display:none':'')], $coords_box);
    echo elgg_format_element('div', ['class' => 'gllpMap', 'style' => 'width: 100%; height: 250px;'], '');
?>  		
</fieldset>


