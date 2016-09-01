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

// enable global search map
$maponmenu = $plugin->maponmenu;
if(empty($maponmenu)){
	$maponmenu = AMAP_MA_GENERAL_NO;
}    
$maponmenufield = elgg_view('input/dropdown', array('name' => 'params[maponmenu]', 'value' => $maponmenu, 'options_values' => $potential_yesno));
$maponmenufield .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:maponmenu:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:maponmenu'), $maponmenufield);    

// entities selection for global map
if(elgg_is_active_plugin("membersmap") || elgg_is_active_plugin("groupsmap") || elgg_is_active_plugin("agora") || elgg_is_active_plugin("pagesmap")){
	$gm_entities = '';
	if(elgg_is_active_plugin("membersmap")) {
		$gm_membersmap = $plugin->gm_membersmap;
		if(empty($gm_membersmap)){
				$gm_membersmap = AMAP_MA_GENERAL_YES;
		}    

		$gm_entities .= '<div class="amap_settings_box">';
		$gm_entities .= "<span class='txt_label'>" . elgg_echo('amap_maps_api:settings:membersmap') . ": </span>";
		$gm_entities .= elgg_view('input/dropdown', array('name' => 'params[gm_membersmap]', 'value' => $gm_membersmap, 'options_values' => $potential_yesno));
		$gm_entities .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:membersmap:note') . "</span>";
		$gm_entities .= '</div>';
	}
	
	if(elgg_is_active_plugin("groupsmap")) {
		$gm_groupsmap = $plugin->gm_groupsmap;
		if(empty($gm_groupsmap)){
				$gm_groupsmap = AMAP_MA_GENERAL_YES;
		}    

		$gm_entities .= '<div class="amap_settings_box">';
		$gm_entities .= "<span class='txt_label'>" . elgg_echo('amap_maps_api:settings:groupsmap') . ": </span>";
		$gm_entities .= elgg_view('input/dropdown', array('name' => 'params[gm_groupsmap]', 'value' => $gm_groupsmap, 'options_values' => $potential_yesno));
		$gm_entities .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:groupsmap:note') . "</span>";
		$gm_entities .= '</div>';
	}
	
	if(elgg_is_active_plugin("agora")) {
		$gm_agora = $plugin->gm_agora;
		if(empty($gm_agora)){
				$gm_agora = AMAP_MA_GENERAL_YES;
		}    

		$gm_entities .= '<div class="amap_settings_box">';
		$gm_entities .= "<span class='txt_label'>" . elgg_echo('amap_maps_api:settings:agora') . ": </span>";
		$gm_entities .= elgg_view('input/dropdown', array('name' => 'params[gm_agora]', 'value' => $gm_agora, 'options_values' => $potential_yesno));
		$gm_entities .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:agora:note') . "</span>";
		$gm_entities .= '</div>';
	}
	
	if(elgg_is_active_plugin("pagesmap") && elgg_is_active_plugin("pages")) {
		$gm_pagesmap = $plugin->gm_pagesmap;
		if(empty($gm_pagesmap)){
				$gm_pagesmap = AMAP_MA_GENERAL_YES;
		}    

		$gm_entities .= '<div class="amap_settings_box">';
		$gm_entities .= "<span class='txt_label'>" . elgg_echo('amap_maps_api:settings:pagesmap') . ": </span>";
		$gm_entities .= elgg_view('input/dropdown', array('name' => 'params[gm_pagesmap]', 'value' => $gm_pagesmap, 'options_values' => $potential_yesno));
		$gm_entities .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:pagesmap:note') . "</span>";
		$gm_entities .= '</div>';
	}	
	
	echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:entities'), $gm_entities);  
}
else
	echo '<div class="display:block; width:100%; margin: 5px 0;"><h4>' . elgg_echo('amap_maps_api:settings:entities:notenabled') . '</h4></div>';

// set if use map cluster or no
$gm_cluster = $plugin->gm_cluster;
if(empty($gm_cluster)){
        $gm_cluster = AMAP_MA_GENERAL_NO;
}    

$clusterfield = elgg_view('input/dropdown', array('name' => 'params[gm_cluster]', 'value' => $gm_cluster, 'options_values' => $potential_yesno));
$clusterfield .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:gm_cluster:note') . "</span>";
echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:gm_cluster'), $clusterfield);

echo elgg_view('input/submit', array('value' => elgg_echo("save")));

