<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

    elgg_require_js("amap_ma_googleapis_js");
    elgg_require_js("amap_ma_markerclusterer_js");
    elgg_require_js("amap_ma_oms_js");
    
    $mapwidth = $vars['mapwidth'];
    $mapheight = $vars['mapheight'];
    $sidebar = $vars['sidebar'];
    
    if ($sidebar)   {
?>
        <div class="map_parent" style="min-height:<?php echo $mapheight; ?>;">
            <div class="map_sidebar">
                <?php echo $sidebar; ?>
            </div>
            <div class="map_map">
                <div id="map" style="width:<?php echo $mapwidth; ?>; height:<?php echo $mapheight; ?>;"></div>
            </div>            
        </div>
<?php
    }
    else {
?>
        <div id="map" style="width:<?php echo $mapwidth; ?>; height:<?php echo $mapheight; ?>;"></div>
<?php        
    }
?>






