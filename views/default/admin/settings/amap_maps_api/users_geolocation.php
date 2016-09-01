<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

//analysis form
echo elgg_view_form('amap_maps_api/admin/users_geolocation', array(
	'action' => '#',
	'disable_security' => true,
));

//analysis result
$body = '';
$body .= elgg_view('graphics/ajax_loader', array(
	'id' => 'users_geolocation-loader'
));
$body .= '<div id="users_geolocation-result">';

if ($version) {
	$body .= elgg_view('amap_maps_api/users_geolocation', array(
		'version' => $version,
	));
} else {
	//$body .= elgg_echo('amap_maps_api:settings:batchusers:note'); // OBS
}

$body .= '</div>';

echo elgg_view_module('main', elgg_echo('amap_maps_api:settings:geolocation:results'), $body, array(
	'class' => 'mbl',
));
