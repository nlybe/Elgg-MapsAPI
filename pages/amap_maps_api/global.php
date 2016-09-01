<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_load_library('elgg:amap_maps_api');  
// elgg_load_library('elgg:amap_maps_api_geocoder');  //OBS

if (amap_ma_not_permit_public_access())	{
	gatekeeper();
}

// Retrieve layers to show on map
$layers = amap_ma_get_map_layers();
// Retrieve default layer
$default_layer = amap_ma_get_map_default_layer();
// Retrieve map width 
$mapwidth = amap_ma_get_map_width();
// Retrieve map height
$mapheight = amap_ma_get_map_height();
// Retrieve map default location
$defaultlocation = amap_ma_get_map_default_location();
// Retrieve map zoom
$mapzoom = amap_ma_get_map_zoom();
// Retrieve cluster feature
$clustering = amap_ma_get_map_gm_clustering();
$clustering_zoom = AMAP_MA_CUSTOM_CLUSTER_ZOOM;

// get coords of default location
$defaultcoords = amap_ma_get_map_default_location_coords();

$title = elgg_echo('amap_maps_api:all');

$indextable = '';
$users = array();
if (amap_ma_check_if_membersmap_gm_enabled()) {
	$options1 = array('type' => 'user', 'full_view' => false);
	$options1['limit'] =0;
	$options1['metadata_name_value_pairs'] = array(array('name' => 'location', 'value' => '', 'operand' => '!='));
	$users = elgg_get_entities_from_metadata($options1);
	$indextable .= elgg_view('input/checkbox', array(
		'name' => 'chbx_user', 
		'id' => 'chbx_user', 
		'checked' => true,
	)).'<label for="chbx_user">'.elgg_echo('amap_maps_api:members').'</label>';	
	
	if ($users) {
		foreach ($users as $entity) {
			$entity = amap_ma_set_entity_additional_info($entity, 'name', 'description', $entity->location);
		}
	}
}

$groups = array();
if (amap_ma_check_if_groupsmap_gm_enabled()) {
	elgg_load_library('elgg:groupsmap');  
	$options2 = array('type' => 'group', 'full_view' => false);
	$options2['limit'] = 0;
	$options2['metadata_name_value_pairs'] = array(array('name' => 'location', 'value' => '', 'operand' => '!='));
	$groups = elgg_get_entities_from_metadata($options2);
	$indextable .= elgg_view('input/checkbox', array(
		'name' => 'chbx_group', 
		'id' => 'chbx_group', 
		'checked' => true,
	)).'<label for="chbx_group">'.elgg_echo('amap_maps_api:groups').'</label>';	
	
	if ($groups) {
		foreach ($groups as $entity) {
			$entity = amap_ma_set_entity_additional_info($entity, 'name', 'briefdescription', groupsmap_get_group_location_str($entity));
		}
	}
}

$ads = array();
if (amap_ma_check_if_agora_gm_enabled()) {
	$options3 = array(
		'type' => 'object',
		'subtype' => 'agora',
		'limit' => 0,
		'full_view' => false,
		'view_toggle_type' => false 
	);
	$options3['metadata_name_value_pairs'] = array(array('name' => 'location', 'value' => '', 'operand' => '!='));
	$ads = elgg_get_entities_from_metadata($options3);
	$indextable .= elgg_view('input/checkbox', array(
		'name' => 'chbx_agora', 
		'id' => 'chbx_agora', 
		'checked' => true,
	)).'<label for="chbx_agora">'.elgg_echo('amap_maps_api:agora').'</label>';	
	
	if ($ads) {
		foreach ($ads as $entity) {
			$entity = amap_ma_set_entity_additional_info($entity, 'title', 'description');
		}
	}
}

$pages = array();
if (amap_ma_check_if_pagesmap_gm_enabled()) {
	$array4 = array();
	$options4 = array(
		'type' => 'object',
		'subtype' => array('page','page_top'),
		'full_view' => false,	
		'limit' => 0,
	);
	$pages = elgg_get_entities($options4);
	
	if ($pages) {
		foreach ($pages as $entity) {
			$entity = amap_ma_set_entity_additional_info($entity, 'title', 'description', $entity->location);
		}
	}	
	
	$indextable .= elgg_view('input/checkbox', array(
		'name' => 'chbx_pages', 
		'id' => 'chbx_pages', 
		'checked' => true,
	)).'<label for="chbx_pages">'.elgg_echo('amap_maps_api:pages').'</label>';	
}

if (!empty($indextable)) {
	$indextable = '<div class="map_indextable">'.$indextable.'</div>';
}
 
$subtotal1 = array_merge($users, $groups);
$subtotal2 = array_merge($subtotal1, $pages);
$total = array_merge($subtotal2, $ads);

$content = elgg_view('amap_maps_api/map_box', array(
    'mapwidth'=>$mapwidth,
    'mapheight'=>$mapheight,
));
$content .= elgg_view('amap_maps_api/map', array(
	'entities'=>$total,
	'defaultlocation'=>$defaultlocation,
	'defaultzoom'=>$mapzoom,
	'defaultcoords'=>$defaultcoords,
	'clustering'=>$clustering,
	'clustering_zoom'=>$clustering_zoom,
	'layers'=>$layers,
	'default_layer'=>$default_layer,
	'osm_base_layer' => amap_ma_get_osm_base_layer(),
));

// locad index table only if clustering is disable as it does'not work 
if (!$clustering)    {
	$content = $indextable.$content;
}
$side_vars = array();

$params = array(
	'content' => $content,
	'sidebar' => '',
	'title' => $title,
	'filter_override' => '',
);

$body = elgg_view_layout('one_column', $params);


echo elgg_view_page($title, $body);

// release variables
unset($users);
unset($groups);
unset($ads);
unset($pages);
unset($subtotal1);
unset($subtotal2);
unset($total);



