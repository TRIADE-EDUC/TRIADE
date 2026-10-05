<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_locations_controler.class.php,v 1.5 2021/12/24 08:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $class_path;
require_once($class_path."/map/map_objects_controler.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_locations_controler extends map_objects_controler
{

    public $location_objects;

    public function __construct($type, $ids, $objects, $id_dom = '') {
        $this->location_objects = $objects;
        parent::__construct($type, $ids);
        $this->id_dom = $id_dom;
        $this->model->set_mode("search_location");
    }

    public function get_json_informations() {
        global $opac_url_base;
        global $opac_map_max_holds;

        $map_hold = $this->get_bounding_box();
        if ($map_hold) {
            $coords = $map_hold->get_coords();
            if (!count($coords)) {
                return "";
            }
            $json_list = array();
            foreach ($this->location_objects as $type => $tab) {
                $items = array();
                $items[0]["layer"] = "location";
                $items[0]["name"] = $type;
                foreach ($tab as $id_location => $value) {
                    $items[0]["ids"][] = $id_location;
                }
                $this->model = new map_model(null, $items, $opac_map_max_holds);
                $this->model->set_mode("search_location");
                $json = $this->model->get_json_informations(false, $opac_url_base, $this->editable);
                
                $json[0]['type_objet'] = $type;
                $json_list = array_merge($json_list, $json);
            }
            return "mode:\"visualization\", type:\"" . TYPE_LOCATION . "\", initialFit: [ " . self::get_coord_initialFit($coords) . "], layers : " .
                encoding_normalize::json_encode($json_list) . ", data : " . encoding_normalize::json_encode($this->location_objects);
        } else {
            return "";
        }
    }

    public function get_map_controler_name() {
        return "apps/map/map_location_controler";
    }

    static public function get_map_location($memo_expl, $id_dom = TYPE_LOCATION, $map_only = 0) {
        global $msg;
        
        $display = '';
        $ids = array();
        $objects = array();
        if (!count($memo_expl))
            return '';
        foreach ($memo_expl as $type => $list) {
            foreach ($list as $expl_list) {
                foreach ($expl_list['expl_location'] as $id_loc) {
                    $id_notice = $expl_list['id_notice'];
                    $id_bulletin = $expl_list['id_bulletin'];
                    $objects[$type][$id_loc][] = $expl_list['expl_id'];
                    $ids[] = $id_loc;
                }
            }
        }
        if (count($ids)) {
            $map = new map_locations_controler(TYPE_LOCATION, $ids, $objects, $id_dom);
            if ($map_only) {
                return $map->get_map();
            }
            $map_display = $map->get_map($id_notice . '_' . $id_bulletin, 'map_location_' . $id_notice . '_' . $id_bulletin.'Img');
            if($map_display) {
	            $display = gen_plus(
	                'map_location_' . $id_notice . '_' . $id_bulletin, 
	                $msg['record_expl_map_location'], 
	                $map_display,
	                '',
	                '',
	                '',
	                'map_location-parent',
	                'map_location-child'
	            );
            }
        }
        return $display;
    }


}

// end of map_objects_controler