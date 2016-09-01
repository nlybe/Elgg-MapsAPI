<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */
 ?>

#map    {
    border: 4px solid #ebebeb;
    margin-top: 0.6em;
    overflow:hidden;
    background-color: #fff!important;
}

div.infowindow	{
    width:200px; 
    padding: 3px; 
    border:0px solid #eaeaea;
    height:auto;
    overflow-x:hidden; 	
}

.infowindow img	{
    float: left;
    margin: 0 5px 5px 0;
}

#map_side_entities {
    height: 590px;
    float: left;
    overflow: auto;
    overflow-x:hidden;
    width: 100%;
}

.map_entity_block {
    width:100%;
    display: block;
    clear: both;
    margin: 1px 0 0 0;
    padding: 5px 3px;
    cursor: pointer;
    cursor: hand;
}

.map_entity_block:hover {
    background-color: #E8E8E8;
}

.map_entity_block img {
    margin: 0 5px 5px 0;
    float: left;
}

.map_entity_block a {
    cursor: pointer; 
}

.box_even {
    background-color: #F0F0F0;
}

.box_odd {
    background-color: #F8F8F8;
}

div.map_indextable	{
    width:100%; 
    clear: both;
    text-align: right;
    margin: 0 0 5px 0;
}

div.map_indextable input	{
	margin: 0 2px 0 12px;
}

/* OBS
div.disabled
{
  pointer-events: none;

  /* for "disabled" effect */
  opacity: 0.5;
} */

.mapicon {
    float:left; 
    margin: 6px 4px 0 0;
    border-radius: 
    3px 3px 3px 3px;
}

.nearby_search_form {
    width: 100%;
    max-width: 100%!important;
    display: block;
    clear:both;
    text-align: left;
}

.nearby_search_form input, .nsf_element {
    display: inline-block;
    max-width: 200px;
}

.nearby_search_form .txt_small	{
    max-width: 100px; 
}

.nearby_search_form .nsf_small	{
    max-width: 150px; 
    margin-right: 10px;
}

.nearby_search_form .nsf_medium	{
    max-width: 220px; 
    margin-right: 20px;
}

.nearby_search_form .txt_medium	{
    max-width: 200px; 
}

.nearby_search_form .txt_big	{
    max-width: 400px; 
}

.nearby_search_form .nsf_big	{
    max-width: 220px; 
}

.nearby_search_form .float-alt {
    float:left!important;
}

#map_keyword_txt, #map_location_txt, #map_radius_txt {
    display: inline-block;
    margin: 0 20px 5px 0;
    float: right;
}

.map_parent {
    width: 100%;
    display: block;
    clear: both;
    padding: 0px;
}

.map_parent .map_sidebar {
    width: 24.3%;
    float:left;
}

.map_parent .map_map {
    width: 75%;
    float:left;
    padding-left: 5px;
}

.map_parent .map_map #map {
    margin-top: 0px;
}

input.gllpLatitude, input.gllpLongitude	{
	width: 180px;
	display: inline;
}

input.gllpSearchField	{
	width: 500px;
	display: inline;
	margin-right: 10px;
}

input.gllpSearchButton	{
	width: 100px;
	margin-right: 20px;
}

