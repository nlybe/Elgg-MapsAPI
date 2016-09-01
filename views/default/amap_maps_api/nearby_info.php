<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */
  
    $search_keyword = $vars['search_keyword'];
    $search_location = $vars['search_location'];
    $search_radius_txt = $vars['search_radius_txt'];

?>

<div class="map_search_info">
    <div id='map_keyword_txt'>
        <?php 
            echo elgg_echo('amap_maps_api:search:personalized:keyword');
            echo '<span id="map_keyword">'.($search_keyword?$search_keyword:'').'</span>'; 
        ?>
    </div>

    <div id='map_location_txt'>
        <?php 
            echo elgg_echo('amap_maps_api:search:personalized:location');
            echo '<span id="map_location">'.($search_location?$search_location:'').'</span>'; 
        ?>
    </div>

    <div id='map_radius_txt'>
        <?php 
            echo elgg_echo('amap_maps_api:search:personalized:radius');
            echo '<span id="map_radius">'.($search_radius_txt?$search_radius_txt:'').'</span>'; 
        ?>
    </div>
</div>


