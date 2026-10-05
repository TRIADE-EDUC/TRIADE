<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_locations_controler.class.php,v 1.4 2021/12/24 08:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
global $class_path;
require_once($class_path . "/map/map_objects_controler.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_locations_controler extends map_objects_controler {

    public $location_objects;

    public function __construct($type, $ids, $objects, $id_dom = '') {
        $this->location_objects = $objects;
        parent::__construct($type, $ids);
        $this->id_dom = $id_dom;
        $this->model->set_mode("search_location");
    }

    public function get_json_informations() {
        global $pmb_url_base;
        global $pmb_map_max_holds;

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
                $this->model = new map_model(null, $items, $pmb_map_max_holds);
                $this->model->set_mode("search_location");
                $json = $this->model->get_json_informations(false, $pmb_url_base, $this->editable);
                $json[0]['type_objet'] = $type;
                $json_list = array_merge($json_list, $json);
            }

            return "mode:\"visualization\", type:\"" . TYPE_LOCATION . "\", initialFit: [ " . self::get_coord_initialFit($coords) . "], layers : " .
                json_encode($json_list) . ", data : " . json_encode($this->location_objects);
        } else {
            return "";
        }
    }

    public function get_map_controler_name() {
        return "apps/map/map_location_controler";
    }

    static public function get_map_location($id_notice, $id_bulletin = 0, $id_dom = TYPE_LOCATION) {
        global $msg, $explr_invisible, $pmb_droits_explr_localises;

        if ($id_bulletin) {
            $where_expl_notice_expl_bulletin = " expl_bulletin='" . $id_bulletin . "' ";
        } else {
            $where_expl_notice_expl_bulletin = " expl_notice='" . $id_notice . "' ";
        }
        if ($pmb_droits_explr_localises && $explr_invisible) {
            $where_expl_localises = " and expl_location not in (" . $explr_invisible . ") ";
        } else {
            $where_expl_localises = "";
        }
        $requete = "SELECT expl_id, expl_location FROM exemplaires WHERE " . $where_expl_notice_expl_bulletin . $where_expl_localises;
        $display = '';
        $ids = array();
        $objects = array();
        $result = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($result)) {
            while ($expl = pmb_mysql_fetch_object($result)) {
                $objects["expl"][$expl->expl_location][] = $expl->expl_id;
                $ids[] = $expl->expl_location;
            }
        }

        if ($id_bulletin) {
            $where_expl_notice_expl_bulletin = " explnum_bulletin='" . $id_bulletin . "' ";
        } else {
            $where_expl_notice_expl_bulletin = " explnum_notice='" . $id_notice . "' ";
        }
        $requete = "SELECT explnum_id, num_location FROM explnum join explnum_location on explnum_id=num_explnum WHERE " . $where_expl_notice_expl_bulletin;

        $result = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($result)) {
            while ($expl = pmb_mysql_fetch_object($result)) {
                $objects["explnum"][$expl->num_location][] = $expl->explnum_id;
                $ids[] = $expl->num_location;
            }
        }
        if (count($ids)) {
            $map = new map_locations_controler(TYPE_LOCATION, $ids, $objects, $id_dom);
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