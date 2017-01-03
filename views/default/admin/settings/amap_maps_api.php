<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api
 */

$tab = get_input('tab', 'general_options');

echo elgg_view('navigation/tabs', array(
    'tabs' => array(
        array(
            'text' => elgg_echo('amap_maps_api:settings:tabs:general_options'),
            'href' => '/admin/settings/amap_maps_api?tab=general_options',
            'selected' => ($tab == 'general_options'),
        ),

        array(
            'text' => elgg_echo('amap_maps_api:settings:tabs:global_options'),
            'href' => '/admin/settings/amap_maps_api?tab=global_options',
            'selected' => ($tab == 'global_options'),
        ),
        array(
            'text' => elgg_echo('amap_maps_api:settings:tabs:personalized_options'),
            'href' => '/admin/settings/amap_maps_api?tab=personalized_options',
            'selected' => ($tab == 'personalized_options'),
        ),
        array(
            'text' => elgg_echo('amap_maps_api:settings:tabs:users_geolocation'),
            'href' => '/admin/settings/amap_maps_api?tab=users_geolocation',
            'selected' => ($tab == 'users_geolocation'),
        ),
    )
));

switch ($tab) {
    case 'global_options':
        echo elgg_view('admin/settings/amap_maps_api/global_options');
        break;
    case 'personalized_options':
        echo elgg_view('admin/settings/amap_maps_api/personalized_options');
        break;
    case 'users_geolocation':
        echo elgg_view('admin/settings/amap_maps_api/users_geolocation');
        break;

    default:
    case 'general_options':
        echo elgg_view('admin/settings/amap_maps_api/general_options');
        break;
}
