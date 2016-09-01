<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

// Based on object location, save his coords. 
function amap_ma_save_object_coords($location, $object, $pluginname, $lat_g = '', $lng_g = '') {
    if ($lat_g && $lng_g) {
        $lat = $lat_g;
        $lng = $lng_g;
    } else if ($location) {
        $prefix = elgg_get_config('dbprefix');
        $coords = amap_ma_geocode_location($location);

        if ($coords) {
            $lat = $coords['lat'];
            $lng = $coords['long'];
        }
    }

    if ($lat && $lng) {
        $prefix = elgg_get_config('dbprefix');
        $object->setLatLong($lat, $lng);
        $query = "INSERT INTO {$prefix}entity_geometry (entity_guid, geometry)
            VALUES ({$object->guid}, GeomFromText('POINT({$lat} {$lng})'))
            ON DUPLICATE KEY UPDATE geometry=GeomFromText('POINT({$lat} {$lng})')";

        insert_data($query);

        return true;
    }

    return false;
}

// retrieve coords for a specified location
function amap_ma_geocode_location($location) {
    $coords = array();
    $google_api_key = trim(elgg_get_plugin_setting('google_api_key', AMAP_MA_PLUGIN_ID));
    $mapquest_api_key = trim(elgg_get_plugin_setting('mapquest_api_key', AMAP_MA_PLUGIN_ID));

    $geocoder = new \Geocoder\ProviderAggregator();
    $adapter = new \Ivory\HttpAdapter\CurlHttpAdapter();
    $chain = new \Geocoder\Provider\Chain([
        new \Geocoder\Provider\GoogleMaps($adapter, $google_api_key),
        new \Geocoder\Provider\MapQuest($adapter, $mapquest_api_key),
    ]);

    $geocoder->registerProvider($chain);

    try {
        $geocode = $geocoder->geocode($location);
    } catch (Exception $e) {
        error_log('amap_maps_api --------->' . $e->getMessage());
        return false;
    }

    if ($geocode->count() > 0) {
        $coords['lat'] = $geocode->first()->getLatitude();
        $coords['long'] = $geocode->first()->getLongitude();
        return $coords;
    }

    return false;
}

// get online users
function amap_ma_get_online_users_map(array $options = array()) { 
    //$count = find_active_users(600, 10, 0, true);
    $options = array_merge(array(
            'seconds' => 600,
    ), $options);    
    $objects = find_active_users($options, 0, 0);

    return $objects;
}

// Retrieve icon from settings
function amap_ma_get_marker_icon($pluginname = null) {
    $markericon = trim(elgg_get_plugin_setting('markericon', $pluginname));
    if (!isset($markericon) || !$markericon) {
        $markericon = 'smiley.png';
    } else {
        $markericon = $markericon . '.png';
    }

    return $markericon;
}

// Retrieve map layers to display from settings
function amap_ma_get_map_layers() {
    $layers_apply = array();
    $layers = elgg_get_config('amap_ma_layers');
    foreach ($layers as $l) {
        $l_option = trim(elgg_get_plugin_setting($l['alias'], AMAP_MA_PLUGIN_ID));

        if ($l_option) {
            array_push($layers_apply, $l['alias']);
        }
    }

    return $layers_apply;
}

// Retrieve default layer of map from settings
function amap_ma_get_map_default_layer() {
    $l_option = trim(elgg_get_plugin_setting('default_layer', AMAP_MA_PLUGIN_ID));
    if (!$l_option)
        $l_option = 'roadmap';

    return $l_option;
}

// Retrieve map width from settings
function amap_ma_get_map_width() {
    $mapwidth = trim(elgg_get_plugin_setting('map_width', AMAP_MA_PLUGIN_ID));
    if (strripos($mapwidth, '%') === false) {
        if (is_numeric($mapwidth))
            $mapwidth = $mapwidth . 'px';
        else
            $mapwidth = '100%';
    }

    return $mapwidth;
}

// Retrieve map height from settings
function amap_ma_get_map_height() {
    $mapheight = trim(elgg_get_plugin_setting('map_height', AMAP_MA_PLUGIN_ID));
    if (strripos($mapheight, '%') === false) {
        if (is_numeric($mapheight))
            $mapheight = $mapheight . 'px';
        else
            $mapheight = '500px';
    }

    return $mapheight;
}

// Retrieve map default location from settings
function amap_ma_get_map_default_location() {
    $defaultlocation = trim(elgg_get_plugin_setting('map_default_location', AMAP_MA_PLUGIN_ID));

    return $defaultlocation;
}

// Retrieve map default location coords from settings
function amap_ma_get_map_default_location_coords() {
    $map_default_lat = trim(elgg_get_plugin_setting('map_default_lat', AMAP_MA_PLUGIN_ID));
    $map_default_lng = trim(elgg_get_plugin_setting('map_default_lng', AMAP_MA_PLUGIN_ID));

    if (empty($map_default_lat) || empty($map_default_lat))
        return AMAP_MA_CUSTOM_DEFAULT_COORDS; // set coords of Europe in case default location is not set
    else
        return $map_default_lat . ',' . $map_default_lng;
}

// Retrieve map default location lat
function amap_ma_get_map_default_location_lat() {
    $map_default_lat = trim(elgg_get_plugin_setting('map_default_lat', AMAP_MA_PLUGIN_ID));

    if (!empty($map_default_lat))
        return $map_default_lat;

    return 0;
}

// Retrieve map default location lon
function amap_ma_get_map_default_location_lon() {
    $map_default_lng = trim(elgg_get_plugin_setting('map_default_lng', AMAP_MA_PLUGIN_ID));

    if (!empty($map_default_lng))
        return $map_default_lng;

    return 0;
}

// Retrieve map zoom from settings
function amap_ma_get_map_zoom() {
    $mapzoom = trim(elgg_get_plugin_setting('map_default_zoom', AMAP_MA_PLUGIN_ID));
    if (!is_numeric($mapzoom))
        $mapzoom = AMAP_MA_CUSTOM_DEFAULT_ZOOM;
    if ($mapzoom < 1)
        $mapzoom = AMAP_MA_CUSTOM_DEFAULT_ZOOM;
    if ($mapzoom > 20)
        $mapzoom = AMAP_MA_CUSTOM_DEFAULT_ZOOM;

    return $mapzoom;
}

// Retrieve cluster feature from settings
function amap_ma_get_map_clustering() {
    $cluster = trim(elgg_get_plugin_setting('cluster', AMAP_MA_PLUGIN_ID));
    if ($cluster === AMAP_MA_GENERAL_YES) {
        return true;
    }

    return false;
}

// Retrieve cluster feature from settings for global map
function amap_ma_get_map_gm_clustering() {
    $cluster = trim(elgg_get_plugin_setting('gm_cluster', AMAP_MA_PLUGIN_ID));
    if ($cluster === AMAP_MA_GENERAL_YES) {
        return true;
    }

    return false;
}

// Retrieve 'search by name' feature from settings
function amap_ma_get_search_by_name($pluginname = null) {
    $searchbyname = trim(elgg_get_plugin_setting('searchbyname', $pluginname));
    if ($searchbyname === AMAP_MA_GENERAL_YES) {
        return true;
    }

    return false;
}

// Retrieve unit of measurement for distance searching
function amap_ma_get_unit_of_measurement() {
    $unitmeas = trim(elgg_get_plugin_setting('unitmeas', AMAP_MA_PLUGIN_ID));
    if ($unitmeas === 'meters') {
        return 1;
    } else if ($unitmeas === 'km') {
        return 1000;
    } else if ($unitmeas === 'miles') {
        return 1609.344;
    }

    return 1; // default value is for meters
}

// Retrieve unit of measurement string for distance searching input box
function amap_ma_get_unit_of_measurement_string() {
    $unitmeas = trim(elgg_get_plugin_setting('unitmeas', AMAP_MA_PLUGIN_ID));
    if ($unitmeas === 'meters') {
        return elgg_echo("amap_maps_api:search:radius:meters");
    } else if ($unitmeas === 'km') {
        return elgg_echo("amap_maps_api:search:radius:km");
    } else if ($unitmeas === 'miles') {
        return elgg_echo("amap_maps_api:search:radius:miles");
    }

    return elgg_echo("amap_maps_api:settings:unitmeas:meters"); // default value is for meters
}

// Retrieve unit of measurement string for map tooltip
function amap_ma_get_unit_of_measurement_string_simple() {
    $unitmeas = trim(elgg_get_plugin_setting('unitmeas', AMAP_MA_PLUGIN_ID));
    if ($unitmeas === 'meters') {
        return elgg_echo("amap_maps_api:search:meters");
    } else if ($unitmeas === 'km') {
        return elgg_echo("amap_maps_api:search:km");
    } else if ($unitmeas === 'miles') {
        return elgg_echo("amap_maps_api:search:miles");
    }

    return elgg_echo("amap_maps_api:search:meters"); // default value is for meters
}

// Retrieve the default radius for personalized map searches
function amap_ma_get_default_radius_search($search_radius = null, $reverse = false) {
    if (is_numeric($search_radius) && $search_radius > 0) {
        $radius = $search_radius;
    } else {
        $unitmeas = intval(elgg_get_plugin_setting('default_radius', AMAP_MA_PLUGIN_ID));
        if (is_numeric($unitmeas) && $unitmeas > 0)
            $radius = $unitmeas;
        else
            $radius = AMAP_MA_DEFAULT_RADIUS; // default value
    }

    if ($reverse)
        return $radius / amap_ma_get_unit_of_measurement();
    else
        return $radius * amap_ma_get_unit_of_measurement();
}

// Retrieve the default radius for personalized map searches and return it in string
function amap_ma_get_default_radius_search_string() {
    $unitmeas = intval(elgg_get_plugin_setting('default_radius', AMAP_MA_PLUGIN_ID));
    if (is_numeric($unitmeas) && $unitmeas > 0)
        $radius = $unitmeas;
    else
        $radius = AMAP_MA_DEFAULT_RADIUS; // default value

    return amap_ma_get_radius_string($radius);
}

// Get the given radius in string including current unit
function amap_ma_get_radius_string($radius) {
    if (!$radius)
        return false;

    return $radius . ' ' . amap_ma_get_unit_of_measurement_string_simple();
}

// check if add tab on members page from settings
function amap_ma_check_if_add_sidebar_list($pluginname = null) {
    $sidebar_list = trim(elgg_get_plugin_setting('sidebar_list', $pluginname));
    if ($sidebar_list === AMAP_MA_GENERAL_YES) {
        return true;
    }

    return false;
}

// check if add tab on members page from settings
function amap_ma_check_if_add_tab_on_entity_page($pluginname = null) {
    $customtab = trim(elgg_get_plugin_setting('customtab', $pluginname));
    if ($customtab === AMAP_MA_GENERAL_YES) {
        return true;
    }

    return false;
}

// check if add "Plugin Map" item on site menu
function amap_ma_check_if_map_menu_item($pluginname = null) {
    $maponmenu = trim(elgg_get_plugin_setting('maponmenu', $pluginname));
    if ($maponmenu === AMAP_MA_GENERAL_NO) {
        return false;
    }

    return true;
}

// check if add "Newest Members" tab as intro page of the map section
function amap_ma_check_if_newest_tab($pluginname = null) {
    $newestusers = trim(elgg_get_plugin_setting('newestusers', $pluginname));
    if ($newestusers === AMAP_MA_GENERAL_NO) {
        return false;
    }

    return true;
}

// hack for disable public access to maps for certains sites // OBS
function amap_ma_not_permit_public_access() {
    $temp = array();
    $temp = explode(".", elgg_get_site_entity()->getDomain());
    if (in_array("socialbusinessworld", $temp)) {
        return true;
    }

    return false;
}

// Check if membersmap is enabled for global map 
function amap_ma_check_if_membersmap_gm_enabled() {
    $gm_membersmap = trim(elgg_get_plugin_setting('gm_membersmap', AMAP_MA_PLUGIN_ID));

    if ($gm_membersmap == AMAP_MA_GENERAL_YES && elgg_is_active_plugin('membersmap')) {
        return true;
    }

    return false;
}

// Check if groupsmap is enabled for global map 
function amap_ma_check_if_groupsmap_gm_enabled() {
    $gm_groupsmap = trim(elgg_get_plugin_setting('gm_groupsmap', AMAP_MA_PLUGIN_ID));

    if ($gm_groupsmap == AMAP_MA_GENERAL_YES && elgg_is_active_plugin('groupsmap')) {
        return true;
    }

    return false;
}

// Check if agora is enabled for global map 
function amap_ma_check_if_agora_gm_enabled() {
    $gm_agora = trim(elgg_get_plugin_setting('gm_agora', AMAP_MA_PLUGIN_ID));

    if ($gm_agora == AMAP_MA_GENERAL_YES && elgg_is_active_plugin('agora')) {
        return true;
    }

    return false;
}

// Check if pagesmap is enabled for global map 
function amap_ma_check_if_pagesmap_gm_enabled() {
    $gm_pagesmap = trim(elgg_get_plugin_setting('gm_pagesmap', AMAP_MA_PLUGIN_ID));

    if ($gm_pagesmap == AMAP_MA_GENERAL_YES && elgg_is_active_plugin('pagesmap') && elgg_is_active_plugin('pages')) {
        return true;
    }

    return false;
}

// remove single and double quotes from strings
function amap_ma_remove_shits($toclear) {
    $cleared = str_replace("'", "&#39;", $toclear);
    $cleared = str_replace('"', "&quot;", $cleared);

    return $cleared;
}

// get icon description depending on entity
function amap_ma_get_entity_description($desc) {
    $entity_description = preg_replace('/[^(\x20-\x7F)]*/', '', $desc);
    $entity_description = amap_ma_remove_shits($entity_description);

    // code below replace the &quot; with " from href of a tag. 
    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    // Check if there is a url in the text
    if (preg_match($reg_exUrl, $entity_description, $url)) {
        $temp1 = preg_replace($reg_exUrl, "mmmfffmmm", $entity_description);
        $temp2 = str_replace('&quot;', "\"", $url[0]);
        $entity_description = str_replace('&quot;mmmfffmmm', "\"{$temp2}", $temp1);
    }

    return $entity_description;
}

// get icon title depending on entity - OBS
function amap_ma_get_entity_title($u, $namecleared) {
    if (!$namecleared)
        return false;

    if (elgg_instanceof($u, 'user')) {
        $entity_title = '<a href="' . elgg_get_site_url() . 'profile/' . $u->username . '">' . $namecleared . '</a>';
    } else if (elgg_instanceof($u, 'group')) {
        //$entity_title = '<a href="'.elgg_get_site_url().'groups/profile/'.$u->guid.'/'.$u->name.'">'.$namecleared.'</a>';
        $entity_title = $namecleared;
    } else if (elgg_instanceof($u, 'object', 'agora')) {
        $entity_title = elgg_view('output/url', array(
            'href' => "agora/view/{$u->guid}/" . elgg_get_friendly_title($namecleared),
            'text' => $namecleared,
        ));
    } else if (elgg_instanceof($u, 'object', 'page') || elgg_instanceof($u, 'object', 'page_top')) {
        $entity_title = elgg_view('output/url', array(
            'href' => "pages/view/{$u->guid}/" . elgg_get_friendly_title($namecleared),
            'text' => $namecleared,
        ));
    }

    return $entity_title;
}

// get icon depending on entity - OBS ???
function amap_ma_get_entity_icon($u) {
    if (elgg_instanceof($u, 'user')) {
        $user_icon = amap_ma_get_marker_icon('membersmap');
        if ($user_icon == 'user_icon_tiny.png') {
            $entity_icon = $u->getIconURL('tiny');
        } 
        else if ($user_icon == 'user_icon_small.png') {
            $entity_icon = $u->getIconURL('small');
        } 
        else {
            $entity_icon = elgg_get_site_url() . "mod/membersmap/graphics/{$user_icon}";
        }
    } else if (elgg_instanceof($u, 'group')) {
        $entity_icon = elgg_get_simplecache_url('groupsmap/icon/' . amap_ma_get_marker_icon('groupsmap'));
    } else if (elgg_instanceof($u, 'object', 'agora')) {
        $adicon = amap_ma_get_marker_icon('agora');
        if ($adicon == 'ad_image.png') {
            elgg_load_library('elgg:agora');
            $entity_icon = agora_getImageUrl($u, 'tiny');
        } else
            $entity_icon = elgg_get_site_url() . 'mod/agora/graphics/' . amap_ma_get_marker_icon('agora');
    }
    else if (elgg_instanceof($u, 'object', 'lcourt')) {
        $entity_icon = elgg_get_simplecache_url('leaguemanager/icon/icons/stadium.png');
    } 
    else if (elgg_instanceof($u, 'object', 'page') || elgg_instanceof($u, 'object', 'page_top')) {
        //$entity_icon = elgg_get_site_url() . 'mod/pagesmap/graphics/' . amap_ma_get_marker_icon('pagesmap');
        $entity_icon = elgg_get_simplecache_url('pagesmap/icon/' . amap_ma_get_marker_icon('pagesmap'));
    }
        
    return $entity_icon;
}

// get icon image depending on entity
function amap_ma_get_entity_img($u, $namecleared) {
    if (!$namecleared)
        return false;

    if (elgg_instanceof($u, 'user') || elgg_instanceof($u, 'group')) {
        $entity_img = elgg_view('output/img', array(
            'src' => $u->getIconURL('tiny'),
            'alt' => $namecleared,
            'class' => "mapicon",
        ));
    } else if (elgg_instanceof($u, 'object', 'agora')) {
        $entity_img = elgg_view('output/url', array(
            'href' => "agora/view/{$u->guid}/" . elgg_get_friendly_title($namecleared),
            'text' => elgg_view('agora/thumbnail', array('classfdguid' => $u->guid, 'size' => 'tiny', 'tu' => $u->time_updated)),
            'class' => "mapicon",
        ));
    } else if (elgg_instanceof($u, 'object', 'page') || elgg_instanceof($u, 'object', 'page_top')) {
        $entity_img = elgg_view('output/img', array(
            'src' => $u->getIconURL('tiny'),
            'alt' => $namecleared,
            'class' => "mapicon",
        ));
    }
    return $entity_img;
}

// Retrieve OSM Base Layer
function amap_ma_get_osm_base_layer() {
    $osm_base_layer = trim(elgg_get_plugin_setting('osm_base', AMAP_MA_PLUGIN_ID));
    if (!$osm_base_layer) {
        $osm_base_layer = AMAP_MA_DEFAULT_OSM_LAYER;
    }

    return $osm_base_layer;
}

// get initial number of entities to show
function amap_ma_get_initial_limit($pluginname) {
    if (!$pluginname)
        return AMAP_MA_NEWEST_NO_DEFAULT;

    $limit = trim(elgg_get_plugin_setting('newest_no', $pluginname));
    if (is_numeric($limit) && $limit > 0) {
        return $limit;
    }

    return AMAP_MA_NEWEST_NO_DEFAULT;
}

// get initial radius when searching by location
function amap_ma_get_initial_radius($pluginname) {
    if (!$pluginname)
        return AMAP_MA_RADIUS_DEFAULT;

    $radius = trim(elgg_get_plugin_setting('mylocation_radius', $pluginname));
    if (is_numeric($radius) && $radius > 0) {
        //return amap_ma_get_default_radius_search($radius);
        return $radius;
    }

    return AMAP_MA_RADIUS_DEFAULT;
}

/**
 * Assign to entity additional information for showing on map
 * 
 * @param type $entity
 * @param type $etitle
 * @param type $edescription
 * @param type $elocation
 * @param type $eotherinfo
 * @param type $m_icon_light: if need to give entity map icon opacity, set this to true
 * @param type $eurl: if need to use other URL than standard entity URL, set this to true
 * 
 * @return Elgg Entity as given
 */
function amap_ma_set_entity_additional_info($entity, $etitle, $edescription, $elocation = null, $eotherinfo = null, $m_icon_light = false, $eurl = false) {
    $edescription = elgg_get_excerpt($edescription);
    $namecleared = amap_ma_remove_shits($entity->$etitle);
    $description = amap_ma_remove_shits(elgg_get_excerpt($entity->description, 100));
    $map_icon = amap_ma_get_entity_icon($entity);

    if ($elocation) {
        $location = $elocation;
    } else {
        $location = $entity->location;
    }

    if (elgg_instanceof($entity, 'object', 'agora')) {
        elgg_load_library('elgg:agora');
        $entity_icon = elgg_view('output/url', array(
            'href' => $entity->getURL(),
            'text' => elgg_view('output/img', array('src' => agora_getImageUrl($entity, 'tiny'), 'class' => "elgg-photo")),
        ));
    }
    if (elgg_instanceof($entity, 'object', 'lcourt')) {
        elgg_load_library('elgg:leaguemanager');
        $entity_photo = elgg_view('output/img', array(
            'src' => lm_getEntityIconUrl($entity->getGUID(), 'tiny'),
            'alt' => $entity->title,
            'class' => 'elgg-photo',
        ));
        $entity_icon = elgg_view('output/url', array(
            'href' => ($eurl?$eurl:$entity->getURL()),
            'text' => $entity_photo,
        ));
    } elseif ($entity instanceof ElggUser || $entity instanceof ElggGroup) {
        $icon = elgg_view('output/img', array(
            'src' => $entity->getIconURL('tiny'),
            'class' => "elgg-photo",
        ));
        $entity_icon = elgg_view('output/url', array(
            'href' => $entity->getURL(),
            'text' => $icon,
        ));
    } else {
        $entity_icon = elgg_view_entity_icon($entity, 'tiny', array(
            'href' => $entity->getURL(),
            'width' => '',
            'height' => '',
            'style' => 'float: left;',
        ));
    }
    $entity->setVolatileData('m_title', $namecleared);
    $entity->setVolatileData('m_description', $description);
    $entity->setVolatileData('m_location', $location);
    $entity->setVolatileData('m_icon', $entity_icon);
    $entity->setVolatileData('m_map_icon', $map_icon);
    if ($eotherinfo) {
        $entity->setVolatileData('m_other_info', $eotherinfo);
    }
    $entity->setVolatileData('m_icon_light', $m_icon_light);
    
    /* hide at the moment as the distance displayed is not well calculated
      if ($user->getLatitude() && $user->getLongitude()) {
      $distance = get_distance($entity->getLatitude(), $entity->getLongitude(), $user->getLatitude(), $user->getLongitude()); // distance in metres
      $distance = round($distance / 1000, 2); // distance in km
      $distance_str = elgg_echo('amap_maps_api:search:proximity', array($user->location, $distance));
      $entity->setVolatileData('distance_from_user', $distance_str);
      } */

    return $entity;
}

// Retrieve timezone_update option from settings
function amap_ma_get_timezone_update() {
    $update_timezone = trim(elgg_get_plugin_setting('update_timezone', AMAP_MA_PLUGIN_ID));
    if ($update_timezone === AMAP_MA_GENERAL_YES) {
        return true;
    }

    return false;
}
