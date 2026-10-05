<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_search_controler.class.php,v 1.19.2.1 2024/06/12 13:52:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $class_path, $include_path;
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_model.class.php");
require_once($class_path."/map/map_objects_controler.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($include_path."/rec_history.inc.php");

/**
 * class map_search_controler
 * Controlleur de notre super dev
 */
class map_search_controler {

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

	public $editable = false;
	public $ajax = false;
	public $objects = [];

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
	public function __construct($map_hold, $mode, $max_hold, $force_ajax=false, $cluster="true") {
		$this->editable = false;
		$this->ajax = $force_ajax;
  		$this->set_mode($mode);

  		$this->objects = array();
  		$this->objects = $this->get_objects();

  		if (count($this->objects)) {
  			$this->model = new map_model($map_hold, $this->objects,$max_hold,$cluster);
  			$this->model->set_mode("search");
  		} else {
  			//la recherche n'est pas encore enregistré...
  			$this->ajax = true;
  		}
  	}

  	/**
  	 * Modifie le mode
  	 *
  	 * @return void
  	 * @access public
  	 */
  	public function set_mode($mode) {
  		$this->mode = $mode;
  	}

  	/**
  	 * Retourne le mode
  	 *
  	 * @return string
  	 * @access public
  	 */
  	public function get_mode() {
  		return $this->mode;
  	}


	/**
	 * Retourne les objets
	 *
	 * @return array[]
	 */
  	public function get_objects() {
		global $opac_search_results_per_page, $nb_per_page_custom;

		$objects = array();
  		$notices_ids = array();
  		if (!empty($_SESSION["tab_result"])) {
  		    $notices_ids = explode(",",$_SESSION["tab_result"]);
  		}

		if (!count($notices_ids) || $notices_ids[0] == '') {
			return $objects;
		}

		// On prends en compte le nb de notices par page à afficher
		$nb_per_page = $nb_per_page_custom ?? $opac_search_results_per_page;
		$notices_ids = array_splice($notices_ids, 0, $nb_per_page);

  		$objects[] = array(
  			'layer' => "record",
  			'ids' => $notices_ids
  		);

  		if (count($notices_ids)) {
  		    $requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (" . implode(",", $notices_ids) . ")";
  		    $result = pmb_mysql_query($requete);
  		    if (pmb_mysql_num_rows($result)) {
  		        $categ_ids = array();
  		        while ($row = pmb_mysql_fetch_object($result)) {
  		            $categ_ids[] = $row->map_emprise_obj_num;
  		        }
  		        $objects[] = array(
  		            'layer' => "authority",
  		            'type' => 2,
  		            'ids' => $categ_ids
  		        );
  		    }
  		    $requete = "select distinct map_emprise_obj_num from map_emprises
                                join index_concept on map_emprises.map_emprise_obj_num=index_concept.num_concept and index_concept.type_object = 1
                                where index_concept.type_object = 1 and index_concept.num_object in (" . implode(",", $notices_ids) . ")";
  		    $result = pmb_mysql_query($requete);
  		    if (pmb_mysql_num_rows($result)) {
  		        $concept_ids = array();
  		        while ($row = pmb_mysql_fetch_object($result)) {
  		            $concept_ids[] = $row->map_emprise_obj_num;
  		        }
  		        $objects[] = array(
  		            'layer' => "authority_concept",
  		            'type' => 10,
  		            'ids' => $concept_ids
  		        );
  		    }
  		}
  		return $objects;

  	}

  	public function have_results(){
  		if(!$this->model){
  			return false;
  		}else{
  			return $this->model->have_results();
  		}
  	}

  	public function get_holds_json_informations($indice){
		$json = array();
  		if($this->model){
  			$json = $this->model->get_holds_informations($this->objects[$indice]['layer']);
  		}
  		return json_encode($json);
  	}

  	public function get_json_informations(){
  		global $opac_url_base;
  		global $opac_map_base_layer_type;
  		global $opac_map_base_layer_params;
  		global $map_emprises_query;

  		$layer_params = json_decode($opac_map_base_layer_params,true);
  		$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
  		if (is_array($layer_params) && count($layer_params)) {
			if ($layer_params['name']) {
				$baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
			}
			if ($layer_params['url']) {
				$baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
			}
			if ($layer_params['options']) {
				$baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
			}
  		}

  		if ($this->ajax){
  			return "mode:\"search_result\", searchId: ".$this->mode.",".$baselayer.",layers_url: \"".$opac_url_base."ajax.php?module=ajax&categ=map&sub=search&action=get_layers\"";
  		} elseif($this->model) {
  			$json = array();
	  		$map_hold = $this->get_bounding_box();
	  		if($map_hold){
		  		$coords = $map_hold->get_coords();
				if (!isset($map_emprises_query[0])) {
					$map_emprises_query[0] = '';
				}

		  		$search_hold = new map_hold_polygon(0,0,$map_emprises_query[0]);
		  		$search_bounding_box = $search_hold->get_bounding_box();
		  		$search_coords = $search_bounding_box->get_coords();

				if ($search_coords) {
		  		    $initialFit = explode(',', map_objects_controler::get_coord_initialFit($search_coords));
		  		} else {
		  		    $initialFit = explode(',', map_objects_controler::get_coord_initialFit($coords));
		  		}

				$json = array(
		  		    'initialFit' => $initialFit,
		  		    'layers' => $this->model->get_json_informations(true, $opac_url_base,false)
			  	);
	  		} else {
	  			$json = array(
	  				'initialFit' => array(0,0,0,0),
	  				'layers' => array(
	  					array(
	  						'type' => "record",
	  						'name' => "record",
	  						'holds' => array(),
	  						'ajax' => false
	  					)
	  				)
	  			);
	  		}
	  		return json_encode($json);
  		}
  	}

  	public function get_bounding_box(){
  		return $this->model->get_bounding_box();
  	}

}