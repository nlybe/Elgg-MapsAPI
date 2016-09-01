<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

// batch convert geolocation	

elgg_require_js("amap_maps_api/users_geolocation");

$batchlink = "<div>" . elgg_echo('amap_maps_api:settings:batchusers:note') ."</div>";	

echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:batchusers'),"<div class='elgg-text'>".$batchlink."</div>");

echo elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('amap_maps_api:settings:batchusers:start'),
	'style' => 'margin-bottom:10px;',
	'id' => 'users_geolocation_btn',
));


