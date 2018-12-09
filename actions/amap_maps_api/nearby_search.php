<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

if (!elgg_is_xhr()) {
    register_error('Sorry, Ajax only!');
    forward(REFERRER);
}

elgg_load_library('elgg:amap_maps_api');
elgg_load_library('elgg:amap_maps_api_geo');

// get variables
$s_location = get_input("s_location");
$s_radius = (int) get_input('s_radius', 0);
$s_keyword = get_input("s_keyword");
$showradius = get_input("showradius");
$initial_load = get_input("initial_load");
$s_change_title = get_input("s_change_title");

if ($s_radius > 0) {
    $search_radius_txt = amap_ma_get_radius_string($s_radius);
}
else {
    $search_radius_txt = amap_ma_get_default_radius_search_string();
}

$s_radius = amap_ma_get_default_radius_search($s_radius);

// retrieve coords of location asked, if any
$coords = amap_ma_geocode_location($s_location);

$options = array(
    'limit' => get_input('limit', 0),
    'offset' => get_input('proximity_offset', 0),
    'count' => false
);
$options['metadata_name_value_pairs'][] = array('name' => 'location', 'value' => '', 'operand' => '!=');

$entities = [];

if (amap_ma_check_if_membersmap_gm_enabled()) {
    $options_u = $options;
    $options_u['type'] = "user";
    if ($s_keyword) {
        $db_prefix = elgg_get_config("dbprefix");
        $query = sanitise_string($s_keyword);

        $options_u["joins"] = array("JOIN {$db_prefix}users_entity ge ON e.guid = ge.guid");
        $where = "(ge.name LIKE '%$query%' OR ge.username LIKE '%$query%')";
        $options_u["wheres"] = array($where);
    }

    if ($coords) {
        $search_location_txt = $s_location;
        $s_lat = $coords['lat'];
        $s_long = $coords['long'];

        if ($s_lat && $s_long) {
            $options_u = add_order_by_proximity_clauses($options_u, $s_lat, $s_long);
            $options_u = add_distance_constraint_clauses($options_u, $s_lat, $s_long, $s_radius);
        }
    }
    $users = elgg_get_entities_from_metadata($options_u);
    $entities = array_merge($entities, $users);
}
/////////////////////////////////////////////////////

if (amap_ma_check_if_groupsmap_gm_enabled()) {
    $options_g = $options;
    $options_g['type'] = "group";
    if ($s_keyword) {
        $db_prefix = elgg_get_config("dbprefix");
        $query = sanitise_string($s_keyword);

        $options_g["joins"] = array("JOIN {$db_prefix}groups_entity ge ON e.guid = ge.guid");
        $where = "(ge.name LIKE '%$query%' OR ge.description LIKE '%$query%')";
        $options_g["wheres"] = array($where);
    }

    if ($coords) {
        $search_location_txt = $s_location;
        $s_lat = $coords['lat'];
        $s_long = $coords['long'];

        if ($s_lat && $s_long) {
            $options_g = add_order_by_proximity_clauses($options_g, $s_lat, $s_long);
            $options_g = add_distance_constraint_clauses($options_g, $s_lat, $s_long, $s_radius);
        }
    }
    $groups = elgg_get_entities_from_metadata($options_g);
    $entities = array_merge($entities, $groups);
}
/////////////////////////////////////////////////////

if (amap_ma_check_if_pagesmap_gm_enabled()) {
    $options_p = $options;
    $options_p['type'] = "object";
    $options_p['subtype'] = array('page', 'page_top');
    if ($s_keyword) {
        $db_prefix = elgg_get_config("dbprefix");
        $query = sanitise_string($s_keyword);

        $options_p["joins"] = array("JOIN {$db_prefix}objects_entity ge ON e.guid = ge.guid");
        $where = "(ge.title LIKE '%$query%' OR ge.description LIKE '%$query%')";
        $options_p["wheres"] = array($where);
    }

    if ($coords) {
        $search_location_txt = $s_location;
        $s_lat = $coords['lat'];
        $s_long = $coords['long'];

        if ($s_lat && $s_long) {
            $options_p = add_order_by_proximity_clauses($options_p, $s_lat, $s_long);
            $options_p = add_distance_constraint_clauses($options_p, $s_lat, $s_long, $s_radius);
        }
    }
    $pages = elgg_get_entities_from_metadata($options_p);
    $entities = array_merge($entities, $pages);
}
/////////////////////////////////////////////////////

if (amap_ma_check_if_photosmap_gm_enabled()) {
    $options_p = $options;
    $options_p['type'] = "object";
    $options_p['subtype'] = 'image';
    if ($s_keyword) {
        $db_prefix = elgg_get_config("dbprefix");
        $query = sanitise_string($s_keyword);

        $options_p["joins"] = array("JOIN {$db_prefix}objects_entity ge ON e.guid = ge.guid");
        $where = "(ge.title LIKE '%$query%' OR ge.description LIKE '%$query%')";
        $options_p["wheres"] = array($where);
    }

    if ($coords) {
        $search_location_txt = $s_location;
        $s_lat = $coords['lat'];
        $s_long = $coords['long'];

        if ($s_lat && $s_long) {
            $options_p = add_order_by_proximity_clauses($options_p, $s_lat, $s_long);
            $options_p = add_distance_constraint_clauses($options_p, $s_lat, $s_long, $s_radius);
        }
    }
    $images = elgg_get_entities_from_metadata($options_p);
    $entities = array_merge($entities, $images);
}
/////////////////////////////////////////////////////

$map_objects = [];
if ($entities) {
    foreach ($entities as $entity) {
        $entity = amap_ma_set_entity_additional_info($entity, 'name', 'description', $entity->location);
    }
        
    foreach ($entities as $e) {
        if ($e->getLatitude() && $e->getLongitude())  {
            $object_x = array();
            $object_x['guid'] = $e->getGUID();
            $object_x['title'] = amap_ma_remove_shits($e->getVolatileData('m_title'));;
            $object_x['description'] = amap_ma_get_entity_description($e->getVolatileData('m_description'));
            $object_x['location'] = elgg_echo('amap_maps_api:location', array(amap_ma_remove_shits($e->getVolatileData('m_location'))));	
            $object_x['lat'] = $e->getLatitude();
            $object_x['lng'] = $e->getLongitude();
            $object_x['icon'] = $e->getVolatileData('m_icon');
            $object_x['other_info'] = $e->getVolatileData('m_other_info');
            $object_x['map_icon'] = $e->getVolatileData('m_map_icon');
            $object_x['info_window'] = $object_x['icon'].' '.$object_x['title'];
            $object_x['info_window'] .= ($object_x['location']?'<br/>'.$object_x['location']:'');
            $object_x['info_window'] .= ($object_x['other_info']?'<br/>'.$object_x['other_info']:'');
            $object_x['info_window'] .= ($object_x['description']?'<br/>'.$object_x['description']:''); 
            if ($e instanceof \ElggUser) {
                $object_x['type'] = 'user';
            }
            else if ($e instanceof \ElggGroup) {
                $object_x['type'] = 'group';
            }
            else {
                $object_x['type'] = $e->getSubtype();
            } 
            array_push($map_objects, $object_x);        
        }
    }
    
    $sidebar = '';

} else {
    $content = elgg_echo('amap_maps_api:search:personalized:empty');
}

$result = array(
    'error' => false,
    'location' => $search_location_txt,
    'radius' => $search_radius_txt,
    's_radius' => amap_ma_get_default_radius_search($s_radius, true),
    's_radius_no' => $s_radius,
    'content' => $content,
    'map_objects' => json_encode($map_objects),
    's_location_lat' => ($s_lat? $s_lat: ''),
    's_location_lng' => ($s_long? $s_long: ''),
    's_location_txt' => $search_location_txt,
    'sidebar' => $sidebar,
    's_change_title' => (isset($s_change_title) && $s_change_title==0?false:true),
);

// release variables
unset($users);
unset($groups);
unset($pages);
unset($images);
unset($entities);
unset($map_objects);

echo json_encode($result);
exit;
