<?php

/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */
$plugin = elgg_get_plugin_from_id('amap_maps_api');

$potential_yesno = array(
    AMAP_MA_GENERAL_NO => elgg_echo('amap_maps_api:settings:no'),
    AMAP_MA_GENERAL_YES => elgg_echo('amap_maps_api:settings:yes'),
);

// Google API
$google_api_key = elgg_view_input('text', array(
    'name' => 'params[google_api_key]',
    'value' => $plugin->google_api_key,
    'label' => elgg_echo('amap_maps_api:settings:google_api_key'),
    'help' => elgg_echo('amap_maps_api:settings:google_api_key:help'),
        ));
$apikeys = elgg_format_element('div', ['class' => 'amap_settings_box'], $google_api_key);

// set if update timezone or not (timezone field is required on user profile or user settings)
$update_timezone = $plugin->update_timezone;
if (empty($update_timezone)) {
    $update_timezone = AMAP_MA_GENERAL_NO;
}
$update_timezone = elgg_view_input('dropdown', array(
    'name' => 'params[update_timezone]',
    'value' => $update_timezone,
    'options_values' => $potential_yesno,
    'label' => elgg_echo('amap_maps_api:settings:update_timezone'),
    'help' => elgg_echo('amap_maps_api:settings:update_timezone:help'),
        ));
$apikeys .= elgg_format_element('div', ['class' => 'amap_settings_box'], $update_timezone);

$google_server_key = elgg_view_input('text', array(
    'name' => 'params[google_server_key]',
    'value' => $plugin->google_server_key,
    'label' => elgg_echo('amap_maps_api:settings:google_server_key'),
    'help' => elgg_echo('amap_maps_api:settings:google_server_key:help'),
        ));
$apikeys .= elgg_format_element('div', ['class' => 'amap_settings_box'], $google_server_key);

$mapquest_api_key = elgg_view_input('text', array(
    'name' => 'params[mapquest_api_key]',
    'value' => $plugin->mapquest_api_key,
    'label' => elgg_echo('amap_maps_api:settings:mapquest_api_key'),
    'help' => elgg_echo('amap_maps_api:settings:mapquest_api_key:help'),
        ));
$apikeys .= elgg_format_element('div', ['class' => 'amap_settings_box'], $mapquest_api_key);

echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:api_keys:title'), $apikeys);


// initialize map options string
$map_options = '';

// set default location
$defaultlocation = $plugin->map_default_location;
if (empty($defaultlocation)) {
    $defaultlocation = AMAP_MA_CUSTOM_DEFAULT_LOCATION;
}
$map_options .= '<div class="amap_settings_box">';
$map_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:defaultlocation') . ": </div>";
$map_options .= elgg_view('input/text', array('name' => 'params[map_default_location]', 'value' => $defaultlocation, 'class' => 'txt_medium'));
$map_options .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:defaultlocation:note') . "</span>";
$map_options .= '</div>';


// Default map zoom
$defaultzoomc = (int) $plugin->map_default_zoom;
if ($defaultzoomc == "") {
    $defaultzoomc = AMAP_MA_CUSTOM_DEFAULT_ZOOM;
}
$map_options .= '<div class="amap_settings_box">';
$map_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:defaultzoom') . ": </div>";
$map_options .= elgg_view('input/dropdown', array('name' => 'params[map_default_zoom]', 'value' => $defaultzoomc, 'options' => range(0, 19)));
$map_options .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:defaultzoom:note') . "</span>";
$map_options .= '</div>';


// Map width
$map_options .= '<div class="amap_settings_box">';
$map_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:map_width') . ": </div>";
$map_options .= elgg_view('input/text', array('name' => 'params[map_width]', 'value' => $plugin->map_width, 'class' => 'txt_small'));
$map_options .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:map_width:note') . "</span>";
$map_options .= '</div>';


// Map height
$map_options .= '<div class="amap_settings_box">';
$map_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:map_height') . ": </div>";
$map_options .= elgg_view('input/text', array('name' => 'params[map_height]', 'value' => $plugin->map_height, 'class' => 'txt_small'));
$map_options .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:map_height:note') . "</span>";
$map_options .= '</div>';


// set if use map cluster or no
$cluster = $plugin->cluster;
if (empty($cluster)) {
    $cluster = AMAP_MA_GENERAL_YES;
}
$map_options .= '<div class="amap_settings_box">';
$map_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:cluster') . ": </div>";
$map_options .= elgg_view('input/dropdown', array('name' => 'params[cluster]', 'value' => $cluster, 'options_values' => $potential_yesno));
$map_options .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:cluster:note') . "</span>";
$map_options .= '</div>';

// set unit of measurement for distance searching
$unitmeas = $plugin->unitmeas;
if (empty($unitmeas)) {
    $unitmeas = 'meters';
}
$potential_unitmeas = array(
    "meters" => elgg_echo('amap_maps_api:settings:unitmeas:meters'),
    "km" => elgg_echo('amap_maps_api:settings:unitmeas:km'),
    "miles" => elgg_echo('amap_maps_api:settings:unitmeas:miles'),
);
$map_options .= '<div class="amap_settings_box">';
$map_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:unitmeas') . ": </div>";
$map_options .= elgg_view('input/dropdown', array('name' => 'params[unitmeas]', 'value' => $unitmeas, 'options_values' => $potential_unitmeas));
$map_options .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:unitmeas:note') . "</span>";
$map_options .= '</div>';

echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:map_options'), $map_options);

// map layers options
$layers = elgg_get_config('amap_ma_layers');
$defaults = array();
$selected_layers = '<h4>' . elgg_echo('amap_maps_api:settings:layers_select') . '</h4>';
foreach ($layers as $l) {
    $name = 'params[' . $l['alias'] . ']';
    $name_x = $l['alias'];
    $selected_layers .= elgg_view('input/checkbox', array(
        'name' => $name,
        'id' => $l['alias'],
        'checked' => ($plugin->$name_x ? true : false),
    )) . '<label for="' . $l['alias'] . '" class="doseaera" >' . $l['label'] . '</label>';

    if ($plugin->$name_x) {
        $defaults[$l['label']] = $l['alias'];
    }
}

// select default layer
$default_layer = $plugin->default_layer;
if (!$default_layer)
    $default_layer = 'roadmap';

$selected_layers .= '<br/><br/>';
$selected_layers .= '<h4>' . elgg_echo('amap_maps_api:settings:default_layer') . '</h4>';
$selected_layers .= elgg_view('input/radio', array('name' => 'params[default_layer]', 'value' => $default_layer, 'options' => $defaults, 'align' => 'horizontal'));

// set OSM base
$selected_layers .= '<div class="amap_settings_box">';
$selected_layers .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:OSM:osm_base') . ": </div>";
$selected_layers .= elgg_view('input/text', array('name' => 'params[osm_base]', 'value' => ($plugin->osm_base ? $plugin->osm_base : AMAP_MA_DEFAULT_OSM_LAYER), 'class' => 'txt_big'));
$selected_layers .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:OSM:osm_base:note') . "</span>";
$selected_layers .= '</div>';
echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:layers'), $selected_layers);

echo elgg_view('input/submit', array('value' => elgg_echo("save")));




