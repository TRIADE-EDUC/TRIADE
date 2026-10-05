<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_model_generic.class.php,v 1.3 2023/07/27 06:57:38 gneveu Exp $
class map_model_generic extends map_model
{

    public function __construct($layers = [], $ids = [], $hold_max = 0, $cluster = "true")
    {
        global $wkt_map_hold;
        
        $this->map_hold = new map_hold_polygon('bounding', 0, $wkt_map_hold);
        $this->hold_max = $hold_max;
        $this->ids = $ids;
        $this->cluster = $cluster;
        foreach ($layers as $name => $instance) {
            $this->layers[$name] = new map_layer_model_generic($instance);
        }
    }

    public function add_layer($layer)
    {
        $this->models[] = $layer;
    }

    public function get_json_informations($mode_ajax, $url_base, $editable = true)
    {
        $informations = array();

        foreach ($this->models as $key => $layer_model) {
            $infos = $layer_model->get_informations();
            $infos['holds'] = array();
            if (! $mode_ajax) {
                $infos['holds'] = $this->get_holds_informations($key);
            }
            $infos['data_url'] = $url_base;
            $infos['editable'] = false;
            $infos['ajax'] = $mode_ajax;
            $infos['type_record'] = $layer_model->get_type_record();
            $informations[] = $infos;
        }
        return $informations;
    }

    public function get_objects($id_layer)
    {
        $objects = $this->models[$id_layer]->get_holds();
        if ($this->get_mode() == "edition" || $this->get_mode() == "visualisation" || $this->cluster === "false") {
            uasort($objects, array('map_holds_reducer', 'cmp_area'));
            return $objects;
        } else {
            $holds_reducer = new map_holds_reducer($this->map_hold, $objects);
            $objects = $holds_reducer->get_reduction();
            return $objects;
        }
    }

    public function get_holds_informations($id_layer)
    {
        $informations = array();
        $holds_layer = $this->get_objects($id_layer);
        foreach ($holds_layer as $hold) {
            $infos = array(
                'wkt' => $hold->get_wkt(),
                'type' => $hold->get_hold_type(),
                'color' => null,
                'objects' => array(
                    'authority_concept' => (is_array($hold->get_num_object()) ? $hold->get_num_object() : array($hold->get_num_object()))
                )
            );
            $query = cms_module_map_datasource_multiple::get_query_record($id_layer, $infos);
            $result = pmb_mysql_query($query);
            $records = array();
            while ($row = pmb_mysql_fetch_object($result)) {
                $records[] = $row->num_object;
            }
            $infos['objects']['record'] = $records;
            $informations[] = $infos;
        }
        return $informations;
    }
}