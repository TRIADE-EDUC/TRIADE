<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_objects_controler.class.php,v 1.20 2019-05-28 10:22:54 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
require_once($class_path . "/map/map_hold.class.php");
require_once($class_path . "/map/map_model.class.php");
require_once($class_path . "/search.class.php");
require_once($class_path . "/searcher.class.php");
require_once($class_path . "/analyse_query.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_objects_controler {
    /** Aggregations: */
    /** Compositions: */
    /*     * * Attributes: ** */

    /**
     *
     * @access protected
     */
    protected $model;

    /**
     *
     * @access protected
     */
    protected $mode;
    public $id_dom = '';

    /**
     * Constructeur.
     *
     * Il joue à  aller chercher les infos utiles pour le modèle (listes d'ids des
     * objets liés,...)
     *
     * @param map_hold map_hold Emprise courante de la carte

     * @param int mode Mode de récupération des éléments

     * @return void
     * @access public
     */
    public function __construct($type, $ids) {
        global $pmb_map_max_holds;
        $this->editable = false;
        $this->ajax = false;

        $this->ids = $ids;
        $this->type = $type;
        $this->objects = array();
        $this->id_dom = $type;

        switch ($this->type) {
            case TYPE_RECORD :
                $items = array(
                    'layer' => "record",
                    'ids' => $this->ids
                );
                break;
            case AUT_TABLE_AUTHORS :
                $items = array(
                    'layer' => "authority",
                    'ids' => $this->ids
                );
                break;
            case TYPE_LOCATION :
                $items = array(
                    'layer' => "location",
                    'ids' => $this->ids
                );
                break;
            case TYPE_SUR_LOCATION :
                $items = array(
                    'layer' => "sur_location",
                    'ids' => $this->ids
                );
                break;
            case AUT_TABLE_CATEG :
                $items = array(
                    'layer' => "authority",
                    'type' => AUT_TABLE_CATEG,
                    'ids' => $this->ids
                );
                break;
            case AUT_TABLE_CONCEPT :
                $items = array(
                    'layer' => "authority_concept",
                    'type' => AUT_TABLE_CONCEPT,
                    'ids' => $this->ids
                );
                break;
        }

        $this->objects[] = $items;
        $this->fetch_datas();
        $this->model = new map_model(null, $this->objects, $pmb_map_max_holds);
        $this->model->set_mode("visualisation");
    }

// end of member function __construct

    public function get_data() {
        return $this->map;
    }

    public function fetch_datas() {
        global $dbh, $msg;

        switch ($this->type) {
            case TYPE_RECORD :
                break;
            case AUT_TABLE_AUTHORS :
                break;
        }
    }

    public function get_json_informations() {
        global $pmb_url_base;
        global $dbh;

        $map_hold = $this->get_bounding_box();
        if ($map_hold) {
            $coords = $map_hold->get_coords();
            if (!count($coords)) {
                return "";
            }
            return "mode:\"visualization\", type:\"" . $this->type . "\", initialFit: [ " . self::get_coord_initialFit($coords) . "], layers : " . json_encode($this->model->get_json_informations(false, $pmb_url_base, $this->editable));
        } else {
            return "";
        }
    }

    public function get_bounding_box() {
        return $this->model->get_bounding_box();
    }

    public function get_map($suffix='', $id_img_plus = "") {
        global $pmb_map_base_layer_type;
        global $pmb_map_base_layer_params;
        global $pmb_map_size_notice_view;
        global $pmb_map_size_location_view;
        
        $map = '';
        $json_informations = $this->get_json_informations();
        if ($json_informations) {
            $id = $this->ids[0];
            $map_hold = null;
            $layer_params = json_decode($pmb_map_base_layer_params, true);
            $baselayer = "baseLayerType: dojox.geo.openlayers.BaseLayerType." . $pmb_map_base_layer_type;
            if (is_array($layer_params) && count($layer_params)) {
                if ($layer_params['name'])
                    $baselayer.=",baseLayerName:\"" . $layer_params['name'] . "\"";
                if ($layer_params['url'])
                    $baselayer.=",baseLayerUrl:\"" . $layer_params['url'] . "\"";
                if ($layer_params['options'])
                    $baselayer.=",baseLayerOptions:" . json_encode($layer_params['options']);
            }

            switch ($this->type) {
                case TYPE_SUR_LOCATION :
                // no break
                case TYPE_LOCATION :
                    $size = explode("*", $pmb_map_size_location_view);
                    break;
                case TYPE_RECORD :
                // no break
                case AUT_TABLE_AUTHORS :
                    // no break
                case AUT_TABLE_CONCEPT :
                    // no break
                default:
                    $size = explode("*", $pmb_map_size_notice_view);
                    break;
            }
            if(count($size)!=2) {
                $map_size="width:100%; height:400px;";
            } else {
                if (is_numeric($size[0])) {
                    $size[0] = $size[0] . "px";
                }
                if (is_numeric($size[1])) {
                    $size[1] = $size[1] . "px";
                }
                $map_size= "width:".$size[0]."; height:".$size[1].";";
            }
            $map = "<div id='map_objet_" . $this->id_dom . "_" . $id . $suffix . "' data-dojo-type='" . $this->get_map_controler_name() . "' style='$map_size' data-dojo-props='mode:\"visualization\"," . $baselayer . ", " . $json_informations . ", id_img_plus:\"". $id_img_plus ."\"'></div>";
        }
        return $map;
    }

    public function get_map_controler_name() {
        return "apps/map/map_controler";
    }

    public static function get_coord_initialFit($tab_coords) {

        $lats_longs = $lats = $longs = array();
        for ($i = 0; $i < count($tab_coords); $i++) {
            $lats_longs[] = $tab_coords[$i]->get_decimal_lat() . '/' . $tab_coords[$i]->get_decimal_long();
        }
        $lats_longs = array_unique($lats_longs);

        //Cas de figure avec une seule coordonnée enregistrée
        if (!isset($lats_longs[1])) {
            $lats_longs[1] = $lats_longs[0];
        }

        //initialisation des variables avec les valeurs extremes
        $lat_min = 90;
        $lat_max = -90;
        $long_min = 180;
        $long_max = -180;
        //On explode
        
        foreach ($lats_longs as $lat_long) {
            $tmp_coord = explode('/', $lat_long);
            $lat = $tmp_coord[0];
            $long = $tmp_coord[1];

            if ($lat < $lat_min) {
                $lat_min = $lat;
            }
            if ($lat > $lat_max) {
                $lat_max = $lat;
            }
            if ($long < $long_min) {
                $long_min = $long;
            }
            if ($long > $long_max) {
                $long_max = $long;
            }
        }
        return $long_min . " , " . $lat_min . " , " . $long_max . " , " . $lat_max;
    }
}

// end of map_objects_controler