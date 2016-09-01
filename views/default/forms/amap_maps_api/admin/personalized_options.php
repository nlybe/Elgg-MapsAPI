<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

$plugin = elgg_get_plugin_from_id('amap_maps_api');

$potential_yes_no = array(
    AMAP_MA_GENERAL_NO => elgg_echo('amap_maps_api:settings:no'),
    AMAP_MA_GENERAL_YES => elgg_echo('amap_maps_api:settings:yes'),
); 

$potential_yes_no_reversed = array(
    elgg_echo('amap_maps_api:settings:yes') => AMAP_MA_GENERAL_YES,
    elgg_echo('amap_maps_api:settings:no') => AMAP_MA_GENERAL_NO,
); 

// enable global search map
$personalizedmap = $plugin->personalizedmap;
if(empty($personalizedmap)){
	$personalizedmap = AMAP_MA_GENERAL_NO;
}    
$maponmenufield = '<div class="amap_settings_box">';
$maponmenufield .= elgg_view('input/dropdown', array('name' => 'params[personalizedmap]', 'value' => $personalizedmap, 'options_values' => $potential_yes_no));
$maponmenufield .= "<span class='elgg-subtext'>" . elgg_echo('amap_maps_api:settings:personalizedmap:note') . "</span>";
$maponmenufield .= '</div>';
echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:personalizedmap'), $maponmenufield);    

// get all registered entities
$types = get_registered_entity_types();

$output = '';
foreach ($types as $key => $t) {

	if ($key == 'user' || $key == 'group') {
		$param_name_entity = 'amap_maps_api_'.$key;
		$param_name = 'params['.$param_name_entity.']';
	
		$tmp = '<div class="amap_settings_box">';
		$tmp .= "<div class='txt_label'>" . elgg_echo($key) . ": </div>";
		$tmp .= "<div class='txt_label'>" .elgg_view('input/radio', array('name' => $param_name, 'value' => $plugin->$param_name_entity, 'options' => $potential_yes_no_reversed, 'align' => 'horizontal'))."</div>";
		$tmp .= '</div>';
		$output .= $tmp;
	}
	else {
		if ($key == 'object') {
			$sub_arr = $t;
			
			foreach ($sub_arr as $sub) {
				$param_name_entity = 'amap_maps_api_'.$sub;
				$param_name = 'params['.$param_name_entity.']';
						
				$tmp = '<div class="amap_settings_box">';
				$tmp .= "<span class='txt_label'>" . elgg_echo($sub) . ": </span>";
				$tmp .= "<div class='txt_label'>" .elgg_view('input/radio', array('name' => $param_name, 'value' => $plugin->$param_name_entity, 'options' => $potential_yes_no_reversed, 'align' => 'horizontal'))."</div>";
				$tmp .= '</div>';
				$output .= $tmp;				
			}
		}
	}
}

$output = '<h4>'.elgg_echo('amap_maps_api:settings:geolocation:description').'</h4>'.$output;
echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:geolocation:title'), $output);


// search options
$search_options = '';
$unitmeas = trim(elgg_get_plugin_setting('unitmeas', AMAP_MA_PLUGIN_ID));
$search_options .= '<div class="amap_settings_box">';
$search_options .= "<div class='txt_label'>" . elgg_echo('amap_maps_api:settings:default_radius') . ": </div>";
$search_options .= elgg_view('input/text', array('name' => 'params[default_radius]', 'value' => $plugin->default_radius, 'class' => 'txt_small'))." (".$unitmeas.") ";
$search_options .= "<span class='elgg-subtext'>".elgg_echo('amap_maps_api:settings:default_radius:note', array($unitmeas))."</span>";
$search_options .= '</div>';

echo elgg_view_module("inline", elgg_echo('amap_maps_api:settings:search_options'), $search_options);

echo elgg_view('input/submit', array('value' => elgg_echo("save")));

