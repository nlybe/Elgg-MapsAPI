<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

// load amap_maps_api maps api libraries
// elgg_load_library('elgg:amap_maps_api');  // OBS as it's loaded on pages

$sidebar = '';
$sidebar .= '<div class="elgg-module  elgg-module-aside">';
$sidebar .= '<div class="elgg-head"><h3>'.elgg_echo("amap_maps_api:search").'</h3></div>';
$sidebar .= '<div class="elgg-body">';
$sidebar .= elgg_view('input/text', array(
	'name' => 'address', 
	'placeholder' => elgg_echo("amap_maps_api:search:location"),	
	'id' => 'address', 
	'class' => 'elgg-input-text elgg-autofocus', 
));
$sidebar .= elgg_view('input/text', array(
	'name' => 'radius', 
	'placeholder' => amap_ma_get_unit_of_measurement_string(AMAP_MA_PLUGIN_ID),	
	'id' => 'radius', 
	'class' => 'elgg-input-text', 
));
$sidebar .= '<label class="mtm float-alt">'.elgg_view('input/checkbox', array('name' => 'showradius', 'value' => 'show', 'id' => 'showradius')).elgg_echo("amap_maps_api:search:showradius").'</label><br />';
$sidebar .=  elgg_view('input/submit', array(
	'value' => elgg_echo('amap_maps_api:search:submit'),
	'class' => 'elgg-button elgg-button-submit', 
	'onclick' => 'codeAddress()', 
));
$sidebar .= '</div>';
$sidebar .= '</div>';

if ($user = elgg_get_logged_in_user_entity())   {
	if (!empty($user->location))    {
		$sidebar .= '<div class="elgg-module  elgg-module-aside">';
		$sidebar .= '<div class="elgg-head"><h3>'.elgg_echo("amap_maps_api:searchnearby").'</h3></div>';
		$sidebar .= '<div class="elgg-body">';
		$sidebar .= '<small>'.elgg_echo("amap_maps_api:mylocationsis").'<i>'.$user->location.'</i></small>';
		$sidebar .= elgg_view('input/text', array(
			'name' => 'radiusmyloc', 
			'placeholder' => amap_ma_get_unit_of_measurement_string(AMAP_MA_PLUGIN_ID),	
			'id' => 'radiusmyloc', 
			'class' => 'elgg-input-text', 
		));		
		$sidebar .= '<label class="mtm float-alt">'.elgg_view('input/checkbox', array('name' => 'showradiusloc', 'value' => 'show', 'id' => 'showradiusloc')).elgg_echo("amap_maps_api:search:showradius").'</label>';
		$sidebar .=  elgg_view('input/submit', array(
			'value' => elgg_echo('amap_maps_api:search:submit'),
			'class' => 'elgg-button elgg-button-submit', 
			'onclick' => 'codeAddress(\''.$user->location.'\')', 
		));		
		$sidebar .= '</div>';
		$sidebar .= '</div>';
	}
}

$sidebar .= '
	<script>
		$(function() {
			$( "#address" ).autocomplete({
				source: function( request, response ) {
				$.ajax({
				url: "http://gd.geobytes.com/AutoCompleteCity",
				dataType: "jsonp",
				data: {
					q: request.term
				},
					success: function( data ) {
						response( data );
					}
				});
			},
			minLength: 3,
			});
		});
	</script>	
';	

echo $sidebar;
	
