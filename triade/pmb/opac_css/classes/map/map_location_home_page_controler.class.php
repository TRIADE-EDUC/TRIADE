<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_location_home_page_controler.class.php,v 1.2 2019/02/26 13:48:41 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_facette_controler.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_location_home_page_controler extends map_facette_controler {
    
    static public function get_map_location_home_page($ids_loc, $tab_locations, $ids_surloc, $tab_surlocations) {
        global $msg;

        if(!count($ids_loc) && !count($ids_surloc)){
            return '';
        }
        $map = new map_facette_controler(TYPE_LOCATION, $ids_loc, $tab_locations, $ids_surloc, $tab_surlocations);
        return  $map->get_map();
    }

    public function get_map_size() {
        global $opac_map_size_location_home_page;

        $size=explode("*",$opac_map_size_location_home_page);

        if(count($size)!=2) {
            $map_size="width:100%; height:240px;";
        } else {
            if (is_numeric($size[0])) {
                $size[0] = $size[0] . "px";
            }
            if (is_numeric($size[1])) {
                $size[1] = $size[1] . "px";
            }
            $map_size= "width:".$size[0]."; height:".$size[1].";";
        }
        return $map_size;
    }
        
}