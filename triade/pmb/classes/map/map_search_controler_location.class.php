<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_search_controler_location.class.php,v 1.3 2023/04/21 06:50:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path . "/map/map_search_controler.class.php");

class map_search_controler_location extends map_search_controler {

    public function __construct($map_hold, $mode, $max_hold, $force_ajax = false, $cluster = "true", $data_loc = array()) {
        $this->editable = false;
        $this->ajax = $force_ajax;
        $this->set_mode($mode);

        if (count($data_loc['location'])) {
            $this->objects[] = array(
                'layer' => "location",
                'ids' => $data_loc['location']
            );
        }
        if (isset($data_loc['sur_location']) && count($data_loc['sur_location'])) {
            $this->objects[] = array(
                'layer' => "sur_location",
                'ids' => $data_loc['sur_location']
            );
        }

        if (count($this->objects)) {
            $this->model = new map_model($map_hold, $this->objects, $max_hold, $cluster);
            $this->model->set_mode("search_location");
        } else {
            //la recherche n'est pas encore enregistré...
            $this->ajax = true;
        }
    }
}
