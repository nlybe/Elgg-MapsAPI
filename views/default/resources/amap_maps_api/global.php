<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_load_library('elgg:amap_maps_api');
elgg_load_library('elgg:amap_maps_api_geo');

if (amap_ma_not_permit_public_access()) {
    gatekeeper();
}

// Retrieve map width 
$mapwidth = amap_ma_get_map_width();
// Retrieve map height
$mapheight = amap_ma_get_map_height();

$clustering = amap_ma_get_map_gm_clustering();
$clustering_zoom = AMAP_MA_CUSTOM_CLUSTER_ZOOM;

// set default parameters
$title = elgg_echo('amap_maps_api:all');


$initial_load = '';

// load the search form only in global view
$body_vars = [];
$body_vars['s_action'] = 'amap_maps_api/nearby_search';

$user = elgg_get_logged_in_user_entity();
if ($user->location) {
    $body_vars['my_location'] = $user->location;
    if (isset($initial_load) && $initial_load == 'location') {
        $body_vars['initial_location'] = $user->location;
    }
}
$form_vars = array('enctype' => 'multipart/form-data');
$content = elgg_view_form('amap_maps_api/nearby', $form_vars, $body_vars);
$content .= elgg_view('amap_maps_api/map_box', array(
    'mapwidth' => $mapwidth,
    'mapheight' => $mapheight,
    'clustering' => $clustering,
    'clustering_zoom' => $clustering_zoom,
));

$indextable = '';
if (amap_ma_check_if_membersmap_gm_enabled()) {
    $legend .= elgg_view('output/img', [
        'src' => elgg_get_simplecache_url('membersmap/icon/' . amap_ma_get_marker_icon('membersmap')),
        'alt' => elgg_echo("item:user"),
    ]);
    if (!$clustering)    {
        $legend .= '<input type="checkbox" name="chbx_user" id="chbx_user" checked="checked">';
    }
    $legend .= elgg_echo("item:user");
//    $indextable .= elgg_format_element('div', ['style'=>'margin: 0 15px 0 0;'], 
//        elgg_view_input('checkbox', ['name' => 'chbx_user', 'id' => 'chbx_user', 'checked' => 'checked']).
//        elgg_format_element('label', ['for' => 'chbx_user'], elgg_echo("item:user"))            
//    );
}
if (amap_ma_check_if_groupsmap_gm_enabled()) {
    $legend .= elgg_view('output/img', [
        'src' => elgg_get_simplecache_url('groupsmap/icon/' . amap_ma_get_marker_icon('groupsmap')),
        'alt' => elgg_echo("item:group"),
    ]);
    if (!$clustering)    {
        $legend .= '<input type="checkbox" name="chbx_group" id="chbx_group" checked="checked">';
    }
    $legend .= elgg_echo("item:group");
//    $indextable .= elgg_format_element('div', ['style'=>'margin: 0 15px 0 0;'], 
//        elgg_view_input('checkbox', ['name' => 'chbx_group', 'id' => 'chbx_group', 'checked' => 'checked']).
//        elgg_format_element('label', ['for' => 'chbx_group'], elgg_echo("item:group"))            
//    );    
}
if (amap_ma_check_if_pagesmap_gm_enabled()) {
    $legend .= elgg_view('output/img', [
        'src' => elgg_get_simplecache_url('pagesmap/icon/' . amap_ma_get_marker_icon('pagesmap')),
        'alt' =>  elgg_echo("item:object:page"),
    ]);
    if (!$clustering)    {
        $legend .= '<input type="checkbox" name="chbx_page" id="chbx_page" checked="checked">';
    }
    $legend .= elgg_echo("item:object:page");
//    $indextable .= elgg_format_element('div', ['style'=>'margin: 0 15px 0 0;'], 
//        elgg_view_input('checkbox', ['name' => 'chbx_page', 'id' => 'chbx_page', 'checked' => 'checked']).
//        elgg_format_element('label', ['for' => 'chbx_page'], elgg_echo("item:object:page"))            
//    );    
}

if (amap_ma_check_if_photosmap_gm_enabled()) {
    $legend .= elgg_view('output/img', [
        'src' => elgg_get_simplecache_url('photosmap/icon/photography.png'),
        'alt' =>  elgg_echo("item:object:image"),
    ]);
    if (!$clustering)    {
        $legend .= '<input type="checkbox" name="chbx_image" id="chbx_image" checked="checked">';
    }
    $legend .= elgg_echo("item:object:image");
}
$content .= elgg_format_element('div', ['id' => 'globalmap_legend'], $legend);

$params = array(
    'content' => $content,
    'sidebar' => '',
    'title' => $title,
    'filter' => '',
);

$body = elgg_view_layout('one_column', $params);

echo elgg_view_page($title, $body);

