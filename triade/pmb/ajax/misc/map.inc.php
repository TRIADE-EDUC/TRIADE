<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map.inc.php,v 1.9 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $search_id, $cluster, $sub, $wkt_map_hold, $loc_ids, $action, $indice;

require_once($class_path."/map/map_search_controler.class.php");
require_once($class_path."/map/map_search_controler_location.class.php");
require_once($class_path."/map/map_hold_polygon.class.php");

if(!isset($search_id)) $search_id = '';
if(!isset($cluster)) $cluster = '';

switch($sub){
	case 'search_location':
		if($wkt_map_hold){
			$wkt_map_hold = new map_hold_polygon("bounding", 0, $wkt_map_hold);
		}
		$search_controler = new map_search_controler_location($wkt_map_hold, $search_id, 250,false, $cluster, $loc_ids);
		session_write_close();
		switch($action){
			case "get_layers" :
				print $search_controler->get_json_informations();
				break;
			case "get_holds" :
				print $search_controler->get_holds_json_informations($indice);
				break;
		}
		break;		
	case 'search':
		if($wkt_map_hold){
			$wkt_map_hold = new map_hold_polygon("bounding", 0, $wkt_map_hold);
		}
		
		$search_controler = new map_search_controler($wkt_map_hold, $search_id, 250,false, $cluster);
		session_write_close();
		switch($action){
			case "get_layers" :
				print $search_controler->get_json_informations();
				break;
			case "get_holds" :
				print $search_controler->get_holds_json_informations($indice);
				break;
		}
		break;
}
