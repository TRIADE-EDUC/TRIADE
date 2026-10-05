<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_edition_controler.class.php,v 1.34.8.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once($class_path . "/map/map_model.class.php");
require_once($class_path . "/map/map_objects_controler.class.php");

/**
 * class map_edition_controler
 *
 */
class map_edition_controler {
    /** Aggregations: */
    /** Compositions: */
    /*     * * Attributes: ** */

    /**
     *
     * @access protected
     */
    protected $model;

    protected $editable;
    protected $type;
    protected $id;

    /**
     *
     *
     * @param string object_type Type d'objet lié à  l'emprise

     * @param int object_id Identifiant de l'objet lié à  l'emprise

     * @return void
     * @access public
     */
    public function __construct($object_type, $object_id) {
        $this->editable = true;

        $this->type = $object_type;
        $this->id = $object_id;
        $objects = array();
        switch ($this->type) {
            case TYPE_RECORD :
                $objects[] = array(
                    'layer' => "record",
                    'ids' => array($this->id)
                );
                break;
            case TYPE_LOCATION :
                $objects[] = array(
                    'layer' => "location",
                    'ids' => array($this->id)
                );
                break;
            case TYPE_SUR_LOCATION :
                $objects[] = array(
                    'layer' => "sur_location",
                    'ids' => array($this->id)
                );
                break;
            case AUT_TABLE_CATEG :
                $objects[] = array(
                    'type' => $this->type,
                    'layer' => "authority",
                    'ids' => array($this->id)
                );
                break;
            case AUT_TABLE_CONCEPT :
                $objects[] = array(
                'type' => $this->type,
                'layer' => "authority_concept",
                'ids' => array($this->id)
                );
                break;
        }
        $this->model = new map_model('', $objects, 0);
        $this->model->set_mode("edition");
    }

// end of member function __construct

    public function get_json_informations() {
        global $pmb_url_base;

        $map_hold = $this->get_bounding_box();
        if (!$map_hold) {
            return "";
        }
        $coords = $map_hold->get_coords();
        if (!count($coords)) {
            return "";
        }
        return "initialFit: [ " . map_objects_controler::get_coord_initialFit($coords) . "], layers : " . json_encode($this->model->get_json_informations(false, $pmb_url_base, $this->editable));
    }

    public function get_bounding_box() {
        return $this->model->get_bounding_box(1);
    }

    public function get_map() {
        global $pmb_map_base_layer_type;
        global $pmb_map_base_layer_params;
        global $pmb_map_size_notice_edition;
        global $pmb_map_size_location_edition;

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
        $size = explode("*", $pmb_map_size_notice_edition);
        switch ($this->type) {
            case TYPE_RECORD :
                break;
            case TYPE_LOCATION :
                $size = explode("*", $pmb_map_size_location_edition);
                break;
            case TYPE_SUR_LOCATION :
                $size = explode("*", $pmb_map_size_location_edition);
                break;
            case AUT_TABLE_CATEG :
                break;
            case AUT_TABLE_CONCEPT :
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

        $map = "
		<div class='row'>
			<div class='colonne60'>
				<div id='map_objet_" . $this->type . "_" . $this->id . "' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='mode:\"edition\", hiddenField:\"map_wkt\", " . $baselayer . "," . $this->get_json_informations() . "'></div>
			</div>
			<div class='colonne40'>
				<div id='map_manual_edition'>
				</div>
			</div>
			<div class='row'></div>
		</div>";
        return $map;
    }

    public function get_form() {
        global $msg;
        $form_map = "";

        switch ($this->type) {
            case TYPE_RECORD :
                $form_map = "
					<div class='row'>
						" . $this->get_map() . "
					</div>
				";
                break;
            case TYPE_LOCATION :
                $form_map = "
					<div class='row'>
						" . $this->get_map() . "
					</div>
				";
                break;
            case TYPE_SUR_LOCATION :
                $form_map = "
					<div class='row'>
						" . $this->get_map() . "
					</div>
				";
                break;
            case AUT_TABLE_CATEG :
                $form_map = "
	  				<div class='row'>
	  					<label class='etiquette'>" . $msg["categ_map_title"] . "</label>
	  				</div>
	  				<div class='row'>
	  					" . $this->get_map() . "
	  				</div>
	  			";
                break;
            case AUT_TABLE_CONCEPT :
                $form_map = "
	  				<div class='row'>
	  					<label class='etiquette'>" . $msg["categ_map_title"] . "</label>
	  				</div>
	  				<div class='row'>
	  					" . $this->get_map() . "
	  				</div>
	  			";
                break;
        }
        return $form_map;
    }

    public function save_form() {
        global $map_wkt;

        $this->delete();
        // save des emprises:
        if (!empty($map_wkt) && count($map_wkt)) {
            for ($i = 0; $i < count($map_wkt); $i++) {
                $query = "insert into map_emprises set
				map_emprise_data= GeomFromText('" . $map_wkt[$i] . "'),
				map_emprise_type=" . $this->type . ",
				map_emprise_obj_num=" . $this->id . ",
				map_emprise_order = " . $i;
                pmb_mysql_query($query);
                $id_emprise = pmb_mysql_insert_id();
                $query_area = "insert into map_hold_areas set
  				id_obj=" . $id_emprise . ",
  				type_obj=" . $this->type . ",
  				area=Area(GeomFromText('" . $map_wkt[$i] . "')),
  				bbox_area=Area(envelope(GeomFromText('" . $map_wkt[$i] . "'))),
  				center=AsText(Centroid(envelope(GeomFromText('" . $map_wkt[$i] . "'))))";
                pmb_mysql_query($query_area);
            }
        }
    }

    public function delete() {
        $req = "select map_emprise_id from map_emprises where map_emprise_type=" . $this->type . " and map_emprise_obj_num=" . $this->id;
        $result = pmb_mysql_query($req);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $req = "DELETE FROM map_emprises where map_emprise_type=" . $this->type . " and map_emprise_obj_num=" . $this->id;
            pmb_mysql_query($req);
            //Partie map_hold_areas
            $req_areas = "DELETE FROM map_hold_areas where type_obj=" . $this->type . " and id_obj=" . $row->map_emprise_id;
            pmb_mysql_query($req_areas);
        }
    }

    public function replace($by) {
        // TO DO
        $this->delete();
    }
}

// end of map_edition_controler