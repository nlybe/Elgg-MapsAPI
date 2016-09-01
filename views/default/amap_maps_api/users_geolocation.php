<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

elgg_admin_gatekeeper();

elgg_load_library('elgg:amap_maps_api');  
// elgg_load_library('elgg:amap_maps_api_geocoder');  // OBS

$options = array('type' => 'user', 'full_view' => false, 'limit' => 0);
$users = elgg_get_entities($options);

foreach ($users as $u)  {

	if (!isset($u->location) || !$u->location) {
		echo $u->username.': not location set<br />';
	}
	else    {
		// function below is required when users saved location before enable members map plugin
		if (!$u->getLatitude() || !$u->getLongitude())  {
			sleep(1);
			$vars['value'] = $u->location;
			if (is_array($vars['value'])) {
				$vars['value'] = implode(', ', $vars['value']);
				$location = elgg_view('output/tag', $vars);
			}	
			else {
				$location = $u->location;
			}
			$location = strip_tags($location);
			
			$ccc = amap_ma_save_object_coords($location, $u, AMAP_MA_PLUGIN_ID);
			if ($ccc) echo $u->username.': geolocation DONE<br />';
			else {
				echo $u->username.': geolocation failed, '.$location.'<br />';
			}
			
			// keeps it flowing to the browser
			flush();
			// 50000 microseconds keeps things flowing in safari, IE, firefox, etc
			usleep(50000);				
		}
		else  {
			echo $u->username.': is OK<br />';
		}
	}	

}

echo "Geolocation finished for all users";

