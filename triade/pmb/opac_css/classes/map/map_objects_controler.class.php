<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_objects_controler.class.php,v 1.15.2.1 2024/09/06 08:59:47 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_model.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/analyse_query.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_objects_controler
{
    public const MIN_LATITUDE = 90;
    public const MAX_LATITUDE = -90;

    public const MIN_LONGITUDE = 180;
    public const MAX_LONGITUDE = -180;

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
    public $editable;
    public $ajax;
    public $ids;
    public $type;
    public $objects;
    public $map;

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
    public function __construct($type, $ids)
    {
        global $opac_map_max_holds;
        $this->editable = false;
        $this->ajax = false;

        $this->ids = $ids;
        $this->type = $type;
        $this->objects = [];
        $this->id_dom = $type;

        switch($this->type) {
            case TYPE_RECORD:
                $items = [
                    'layer' => "record",
                    'ids' =>  $this->ids,
                ];
                break;
            case AUT_TABLE_AUTHORS:
                $items = [
                    'layer' => "authority",
                    'ids' => $this->ids,
                ];
                break;
            case TYPE_LOCATION:
                $items = [
                    'layer' => "location",
                    'ids' => $this->ids,
                ];
                break;
            case TYPE_SUR_LOCATION:
                $items = [
                    'layer' => "sur_location",
                    'ids' => $this->ids,
                ];
                break;
            case AUT_TABLE_CATEG:
                $items = [
                    'layer' => "authority",
                    'type' => AUT_TABLE_CATEG,
                    'ids' => $this->ids,
                ];
                break;
            case AUT_TABLE_CONCEPT:
                $items = [
                    'layer' => "authority_concept",
                    'type' => AUT_TABLE_CONCEPT,
                    'ids' => $this->ids,
                ];
                break;
        }
        $this->objects[] = $items;
        $this->fetch_datas();
        $this->model = new map_model(null, $this->objects, $opac_map_max_holds);
        $this->model->set_mode("visualisation");
    } // end of member function __construct


    public function get_data()
    {
        return $this->map;
    }

    public function fetch_datas()
    {
        switch($this->type) {
            case TYPE_RECORD:
                break;
            case AUT_TABLE_AUTHORS:
                break;
        }
    }

    public function get_json_informations()
    {
        global $opac_url_base;

        $map_hold = $this->get_bounding_box();
        if($map_hold) {
            $coords = $map_hold->get_coords();
            if(!count($coords)) {
                return "";
            }
            return "mode:\"visualization\", type:\"" . $this->type . "\", initialFit: [ ".self::get_coord_initialFit($coords)."], layers : ".json_encode($this->model->get_json_informations(false, $opac_url_base, $this->editable));
        } else {
            return "";
        }
    }

    public function get_bounding_box()
    {
        return $this->model->get_bounding_box();
    }

    public function get_map($suffix = '', $id_img_plus = "")
    {
        global $opac_map_base_layer_type;
        global $opac_map_base_layer_params;

        $json_informations = $this->get_json_informations();
        $map = "";
        if($json_informations) {
            $id = (!empty($this->ids[0]) ? $this->ids[0] : 0);
            $layer_params = json_decode($opac_map_base_layer_params, true);
            $baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
            if(isset($layer_params) && is_array($layer_params) && count($layer_params)) {
                if($layer_params['name']) {
                    $baselayer .= ",baseLayerName:\"".$layer_params['name']."\"";
                }
                if($layer_params['url']) {
                    $baselayer .= ",baseLayerUrl:\"".$layer_params['url']."\"";
                }
                if($layer_params['options']) {
                    $baselayer .= ",baseLayerOptions:".json_encode($layer_params['options']);
                }
            }
            $map = "
              <div id='map_objet_".$this->id_dom."_" . $id . $suffix ."' data-dojo-type='".$this->get_map_controler_name()."' style='".$this->get_map_size()."' data-dojo-props='mode:\"visualization\",".$baselayer.", ".$json_informations.", id_img_plus:\"". $id_img_plus ."\"'></div>";
        }
        return $map;
    }

    public function get_map_controler_name()
    {
        return "apps/map/map_controler";
    }

    public function get_map_size()
    {
        global $opac_map_size_notice_view;
        global $opac_map_size_location_view;

        switch($this->type) {
            case TYPE_SUR_LOCATION :
            case TYPE_LOCATION :
                $size = explode("*", $opac_map_size_location_view);
                break;
            case TYPE_RECORD :
            case AUT_TABLE_AUTHORS :
            case AUT_TABLE_CONCEPT :
            default:
                $size = explode("*", $opac_map_size_notice_view);
                break;
        }
        return $this->format_size($size);
    }

    public static function get_coord_initialFit($tab_coords)
    {
        $lats_longs = array_map(function ($coord) {
            return $coord->get_decimal_lat().'/'.$coord->get_decimal_long();
        }, $tab_coords);
        $lats_longs = array_unique($lats_longs);

        // Cas de figure avec une seule coordonnée enregistrée
        if (!isset($lats_longs[1])) {
            $lats_longs[1] = $lats_longs[0];
        }

        // initialisation des variables avec les valeurs extremes
        $lat_min = static::MIN_LATITUDE;
        $lat_max = static::MAX_LATITUDE;
        $long_min = static::MIN_LONGITUDE;
        $long_max = static::MAX_LONGITUDE;

        // On explode
        foreach($lats_longs as $lat_long) {
            [$lat, $long] = explode('/', $lat_long, 2);

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
        return $long_min." , ".$lat_min." , ".$long_max." , ".$lat_max;
    }

    protected function format_size(array $size)
    {
        global $charset;

        if(count($size) != 2) {
            $map_size = "width:100%; height:200px;";
        } else {
            $size = array_map("trim", $size);
            if (is_numeric($size[0])) {
                $size[0] = $size[0] . "px";
            }
            if (is_numeric($size[1])) {
                $size[1] = $size[1] . "px";
            }
            $map_size = "width:".$size[0]."; height:".$size[1].";";
        }

        if ($charset != "utf8") {
            $map_size = encoding_normalize::utf8_normalize($map_size);
        }
        return $map_size;
    }
}
