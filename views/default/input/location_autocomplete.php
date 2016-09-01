<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_require_js("amap_maps_api/amap_autocomplete");

$entity = elgg_extract("entity", $vars, "");

// get location directly from value if available (e.g. used by profile_manager)
$location = elgg_extract("value", $vars, "");
if (!$location && $entity)	{
    // if empty location but entity is available, then get the entity location
    $location = $entity->location;
}

$name = elgg_extract("name", $vars, "");
$class = elgg_extract("class", $vars, "");

$defaults = array(
    'disabled' => false,
    'type' => 'text',
    'name' => ($name?$name:'location'), 
    'placeholder' => elgg_echo("amap_maps_api:search:location"),	
    'id' => 'autocomplete',
    'class' => "elgg-input-text txt_medium {$class}", 
    'value' => $location,    
);

$vars = array_merge($defaults, $vars);

echo elgg_format_element('input', $vars);

?> 


