<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

$entities = $vars['entities'];
$mapheight = $vars['mapheight'];
$list_view = $vars['list_view'];

$sidebar = '';
if ($entities) {
    $box_color_flag = true;
    foreach ($entities as $entity) {
        $sidebar .= elgg_view($list_view, array('entity' => $entity, 'box_color' => ($box_color_flag ? 'box_even' : 'box_odd')));
        $box_color_flag = !$box_color_flag;
    }
}
?>

<div id='map_side_entities' style="height:<?php echo $mapheight; ?>;">
    <?php echo $sidebar; ?>
</div>

<?php
// release variables
unset($entities);
?>
