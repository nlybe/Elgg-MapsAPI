<?php

/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */
//http://stackoverflow.com/questions/18661189/getting-data-from-json-using-mapquest-and-php
// require_once __DIR__ . '/Geocoder/src/Geocoder/Geocoder.php';

require_once __DIR__ . '/autoloader.php';
require_once(dirname(__FILE__) . "/lib/hooks.php");

elgg_register_event_handler('init', 'system', 'amap_maps_api_init');

define('AMAP_MA_PLUGIN_ID', 'amap_maps_api'); // plugin ID
define('AMAP_MA_CUSTOM_DEFAULT_COORDS', '49.037868,14.941406'); // set coords of Europe in case default location is not set
define('AMAP_MA_CUSTOM_DEFAULT_LOCATION', 'Europe'); // set default location in case default location is not set
define('AMAP_MA_CUSTOM_DEFAULT_ZOOM', 12); // set default zoom in case is not set
define('AMAP_MA_CUSTOM_CLUSTER_ZOOM', 7); // set cluster zoom that define when markers grouping ends
define('AMAP_MA_DEFAULT_RADIUS', 500); // set the default search radius for personalized services in km
define('AMAP_MA_DEFAULT_OSM_LAYER', 'http://tile.openstreetmap.org/'); // set the default OSM base layer
define('AMAP_MA_GENERAL_YES', 'yes'); // general purpose string for yes
define('AMAP_MA_GENERAL_NO', 'no'); // general purpose string for no
define('AMAP_MA_NEWEST_NO_DEFAULT', 100); // initial number of entities to show
define('AMAP_MA_RADIUS_DEFAULT', 100); // set the default search radius on initial map in km, if selected in settings

function amap_maps_api_init() {

    // register a library of helper functions
    elgg_register_library('elgg:amap_maps_api', elgg_get_plugins_path() . 'amap_maps_api/lib/amap_maps_api.php');
    //elgg_register_library('elgg:amap_maps_api_geocoder', elgg_get_plugins_path() . 'amap_maps_api/lib/Geocoder.php'); // OBS
    elgg_register_library('elgg:amap_maps_api_geo', elgg_get_plugins_path() . 'amap_maps_api/lib/geo_functions.php');

    // Extend CSS
    elgg_extend_view('css/elgg', 'amap_maps_api/css');
    elgg_extend_view('css/admin', 'amap_maps_api/css_admin');

    // register extra js files
    $mapkey = trim(elgg_get_plugin_setting('google_api_key', AMAP_MA_PLUGIN_ID));
    elgg_define_js('amap_ma_googleapis_js', array(
        'src' => "//maps.googleapis.com/maps/api/js?key={$mapkey}&v=3&libraries=places", 
        'exports' => 'amap_ma_googleapis_js',
    ));

    elgg_define_js('amap_ma_googleapi_js', array(
        'src' => "//maps.google.com/maps/api/js?key={$mapkey}&v=3&libraries=geometry",
        'exports' => 'amap_ma_googleapi_js',
    ));

    elgg_define_js('amap_ma_markerclusterer', array(
        'deps' => array('jquery', 'amap_ma_markerclusterer_js'),
        'exports' => 'amap_ma_markerclusterer',
    ));

    elgg_define_js('amap_ma_placeholder_js', array(
        'deps' => array('jquery'),
        'exports' => 'amap_ma_placeholder_js',
    ));

    elgg_define_js('amap_ma_oms_js', array(
        'deps' => array('amap_ma_googleapis_js'),
        'exports' => 'amap_ma_oms_js',
    ));

    elgg_define_js('amap_ma_latlon_picker', array(
        'deps' => array('jquery', 'amap_ma_googleapis_js'),
        'exports' => 'amap_ma_latlon_picker',
    ));
    
    // register plugin settings view
    elgg_register_simplecache_view('amap_maps_api/settings.js');    

    // Global map: add if enabled in settings and any of map plugins are enabled
    if (check_if_global_map_enabled()) {
        $item = new ElggMenuItem(AMAP_MA_PLUGIN_ID, elgg_echo('amap_maps_api:menu'), 'globalmap/all');
        elgg_register_menu_item('site', $item);
    }

    // Add admin menu item
    elgg_register_admin_menu_item('configure', AMAP_MA_PLUGIN_ID, 'settings');

    // Register a page handler, so we can have nice URLs
    elgg_register_page_handler('globalmap', 'amap_maps_api_page_handler');

    // set map layers
    elgg_set_config('amap_ma_layers', array(
        'roadmap' => array('alias' => 'roadmap', 'label' => elgg_echo('amap_maps_api:settings:roadmap')),
        'terrain' => array('alias' => 'terrain', 'label' => elgg_echo('amap_maps_api:settings:terrain')),
        'satellite' => array('alias' => 'satellite', 'label' => elgg_echo('amap_maps_api:settings:satellite')),
        'hybrid' => array('alias' => 'hybrid', 'label' => elgg_echo('amap_maps_api:settings:hybrid')),
        'OSM' => array('alias' => 'OSM', 'label' => elgg_echo('amap_maps_api:settings:OSM')),
    ));

    // we need geolocation of users for providing personalized searches
    // Register a handler for create members
    elgg_register_event_handler('create', 'user', 'amap_ma_geolocate');
    // Register a handler for update members
    elgg_register_event_handler('profileupdate', 'user', 'amap_ma_geolocate');

    // register ajax view for map
    elgg_register_ajax_view('amap_maps_api/map');
    // register ajax view for users geolocation
    elgg_register_ajax_view('amap_maps_api/users_geolocation');

    if (elgg_is_active_plugin("profile_manager")) {
        // default profile options
        $profile_options = array(
            "show_on_register" => false,
            "mandatory" => false,
            "user_editable" => true,
            "output_as_tags" => false,
            "admin_only" => false,
            "simple_search" => true,
            "advanced_search" => true
        );

        // Add profile fields
        profile_manager_add_custom_field_type("custom_profile_field_types", 'location_map', elgg_echo("amap_maps_api:input:map:title"), $profile_options);
        profile_manager_add_custom_field_type("custom_profile_field_types", 'location_autocomplete', elgg_echo("amap_maps_api:input:autocomplete:title"), $profile_options);
    }

    // Register actions admin
    $action_path = elgg_get_plugins_path() . 'amap_maps_api/actions/amap_maps_api';
    elgg_register_action('amap_maps_api/admin/general_options', "$action_path/admin/settings.php", 'admin');
    elgg_register_action('amap_maps_api/admin/global_options', "$action_path/admin/settings.php", 'admin');
    elgg_register_action('amap_maps_api/admin/personalized_options', "$action_path/admin/settings.php", 'admin');
    elgg_register_action('amap_maps_api/nearby_search', "$action_path/nearby_search.php");
}

/**
 * Dispatches global api pages.
 *
 * @param array $page
 * @return bool
 */
function amap_maps_api_page_handler($page) {
    $base = elgg_get_plugins_path() . 'amap_maps_api/pages/amap_maps_api';

    if (!isset($page[0])) {
        $page[0] = 'all';
    }

    $vars = array();
    $vars['page'] = $page[0];

    require_once "$base/global.php";

    return true;
}

/**
 * Check if global map is enable and if any of available plugins are enabled
 *
 * @return bool
 */
function check_if_global_map_enabled() {
    $maponmenu = trim(elgg_get_plugin_setting('maponmenu', AMAP_MA_PLUGIN_ID));

    if ($maponmenu == AMAP_MA_GENERAL_YES && (elgg_is_active_plugin('membersmap') || elgg_is_active_plugin('groupsmap') || elgg_is_active_plugin('agora')))
        return true;

    return false;
}

/**
 * Check if user personalized services are enabled
 *
 * @return bool
 */
function check_if_personalized_map_enabled() {
    $option = trim(elgg_get_plugin_setting('personalizedmap', AMAP_MA_PLUGIN_ID));

    if ($option == AMAP_MA_GENERAL_YES)
        return true;

    return false;
}

/**
 * Geolocate User based on location field
 */
function amap_ma_geolocate($event, $object_type, $object) {
    if ($object instanceof ElggUser) {
        elgg_load_library('elgg:amap_maps_api');

        $lat = get_input("latitude");
        $lng = get_input("longitude");
        $location = $object->location;
        if ($location || (isset($lat) && isset($lng))) {
            $ccc = amap_ma_save_object_coords($location, $object, AMAP_MA_PLUGIN_ID, (isset($lat) ? $lat : ''), (isset($lng) ? $lng : ''));
        } else {
            $object->setLatLong('', '');
        }

        // update timezone, if enabled
        if (amap_ma_get_timezone_update() && $object->getLatitude() && $object->getLongitude()) {
            $mapkey = trim(elgg_get_plugin_setting('google_server_key', AMAP_MA_PLUGIN_ID));
            $url = "//maps.googleapis.com/maps/api/timezone/json?location={$object->getLatitude()},{$object->getLongitude()}&timestamp=" . time() . "&key={$mapkey}";

            $json_data = file_get_contents($url);
            $result = json_decode($json_data);

            if ($result->status === "OK") {
                $object->timezone = $result->timeZoneId;
                $object->save();
            } else {
                error_log('amap_maps_api --------->' . $result->status);
                error_log('amap_maps_api --------->' . $result->errorMessage);
            }
        }

        return true;
    }

    return false;
}
