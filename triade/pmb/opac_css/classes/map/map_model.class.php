<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_model.class.php,v 1.13.2.1 2024/11/21 09:20:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $class_path;
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_layer_model_record.class.php");
require_once($class_path."/map/map_layer_model_authority.class.php");
require_once($class_path."/map/map_holds_reducer.class.php");
require_once($class_path."/map/map_layer_model_location.class.php");
require_once($class_path."/map/map_layer_model_sur_location.class.php");


/**
 * class map_model
 * 
 */
class map_model {

	/** Aggregations: */

	/** Compositions: */

	/*** Attributes: ***/
	
	/**
	 * Emprise de la carte
	 * @var map_hold_polygon
	 * @access protected
	 */
	protected $map_hold;
	
	/**
	 * Liste des identifiants des objects
	 * @var Array()
	 * @access protected
	 */
	protected $ids;
	
	/**
	 * Tableau de layer_model
	 * @access protected
	 */
	protected $models;

  	/**
   	 * Tableau de booléen sur la visibilité des modèles (clés identiques)
   	 * @access protected
     */
  	protected $visibility;

  	/**
 	 * Nombre maximum d'emprises présentes sur une couche de la carte.
 	 * Si= 0, pas de limitation
	 * @access protected
	 */
	protected $hold_max;
	
	protected $cluster;

	/**
	 *  @param map_hold_polygon map_hold Emprise courante de la carte
	 *  @param Array() ids Liste des identifiants des objets
	 *  @param int hold_max Nombre maximum d'emprise présentes sur une couche de la carte
  
	 * @return void
	 * @access public
     *
     *
     *  Array(
	 *	    Array(
	 *	  		'layer'=> "categ",
	 *	  		'ids=> array(...),
	 *	 	),
	 *		Array(
	 *	  		'layer'=> "record",
	 *	  		'ids=> array(...),
	 *	 	)
	 *	)
	 * 
	*/
	public $mode;

	public function __construct( $map_hold,  $ids,  $hold_max=0,$cluster="true") {
  		$this->map_hold=$map_hold;
  		$this->hold_max = $hold_max;
  		$this->ids = $ids;
  		$this->cluster = $cluster;
  		for($i=0 ; $i<count($this->ids) ; $i++){
   			$layer_model_class_name = $this->get_layer_model_class_name($this->ids[$i]['layer']);
            if ($this->ids[$i]['layer'] == 'authority' || $this->ids[$i]['layer'] == 'authority_concept') {
   				$this->models[$this->ids[$i]['layer']] = new $layer_model_class_name($this->ids[$i]['type'],$this->ids[$i]['ids']);
   			} elseif ($this->ids[$i]['layer'] == 'location' || $this->ids[$i]['layer']=='sur_location' ) {
   				$this->models[$this->ids[$i]['layer']] = new $layer_model_class_name($this->ids[$i]['ids']);   				
   				if (isset($this->ids[$i]['name']) && $this->ids[$i]['name']) {
   					$this->models[$this->ids[$i]['layer']]->set_layer_model_name($this->ids[$i]['name']);
   				}
   			} else {
   				$this->models[$this->ids[$i]['layer']] = new $layer_model_class_name($this->ids[$i]['ids']);
   			}
  		}
  		if($this->map_hold == null){
  			$this->map_hold = $this->get_bounding_box();
  		}
	} // end of member function __construct
  
  
  	/**
     * Calcul l'emprise minimal pour afficher toutes les emprises de tous les modèles
     *
     * @return map_hold
     * @access public
     */
  	public function get_bounding_box() {
  		$collection ="";
  		foreach($this->models as $layer_model){
  			if($collection) $collection.= ",";
  			$layer_bounding_box = $layer_model->get_bounding_box();
  			if($layer_bounding_box){
  				$collection.= $layer_bounding_box->get_wkt();
  			}
  		}
  		if($collection){
  			$query = "select astext(envelope(geomfromtext('geometrycollection(".$collection.")'))) as bounding_box";
			$result = pmb_mysql_query($query) or die(pmb_mysql_error());
			if(pmb_mysql_num_rows($result)){
			  	$bounding_box = new map_hold_polygon("bounding", 0,pmb_mysql_result($result,0,0));
			}
  		}else{
			return false;
		}
		return $bounding_box; 
  	} // end of member function get_bounding_box

  	/**
  	 * Retourne la liste des layers
  	 *
  	 * @return map_layer_model
  	 * @access public
  	 */
  	public function get_layers() {
  		return $this->models;
  	} // end of member function get_layers	

 	/**
 	 * Retourne les objets à  afficher sur la carte.
	 * La méthode fait appel à  l'algo de réduction si besoin
   	 *
     * @param int id_layer Identifiant du layer

     * @return map_hold
     * @access public
     */
 	public function get_objects( $id_layer) {
 	    if (empty($id_layer)) {
 	        return;
 	    }
 	    if (!empty($this->models[$id_layer])) {
      		$objects = $this->models[$id_layer]->get_holds();
      		if($this->get_mode() == "edition" || $this->get_mode() == "visualisation" || $this->cluster === "false"){
      			uasort($objects, array('map_holds_reducer', 'cmp_area'));
      			return $objects;
      		}else{
      			$holds_reducer = new map_holds_reducer($this->map_hold,$objects);
      			$objects = $holds_reducer->get_reduction();
      			return $objects;
      		}
 	    }
 	    return [];
	} // end of member function get_objects

    /**
     * 
     *;
     * @param bool visible Visible ou non

     *  @param int num_layer Identifiant du layer

     * @return void
     * @access public
     */
	public function set_visibility( $visible,  $num_layer) {
	} // end of member function set_visibility



	/**
	 * Retourne une structure JS au format JSON,contenant les informations du modèle
	 * courant.
	 * Soit les données (les différentes emprises typées avec la réduction si
	 * nécessaire), soit l'URL à  appeler en AJAX pour les récupérer
	 *
	 * @param bool mode_ajax Défini si on passe la structure complète ou les infos pour récupérer en AJAX
	
	 * @param string url_base URL de base fournie par le controler
	
	 * @return string
	 * @access public
	 */
	public function get_json_informations($mode_ajax, $url_base, $editable=false) {
		$informations =array();
		
		foreach($this->models as $key => $layer_model){
			$infos = $layer_model->get_informations();
			if(!$mode_ajax){
				$infos['holds'] = $this->get_holds_informations($key);
			}else{
				$infos['holds'] = array();
			}
			$infos['data_url'] = $url_base."ajax.php?module=ajax&categ=map&sub=".$this->mode."&action=get_holds";
			$infos['editable'] = $editable;	
			$infos['ajax'] = $mode_ajax;
			$informations[] = $infos;
		}
		return $informations;
		
	} // end of member function get_json_informations
	
	protected function get_layer_model_class_name($layer=""){
		if($layer){
            if($layer == "authority_concept") $layer = "authority";
			if(class_exists("map_layer_model_".$layer)){
				return "map_layer_model_".$layer;
			}else{
				return $this->get_layer_model_class_name();
			}
		}else{
			return false;
		}
	}
	
	public function get_holds_informations($id_layer){
		$informations = array();
		$holds_layer = $this->get_objects($id_layer);
		foreach($holds_layer as $hold){
			$infos = array(
				'wkt' => $hold->get_wkt(),
				'type' => $hold->get_hold_type(),
				'color' => null,
				'objects'=> array(
					 $id_layer => (is_array($hold->get_num_object()) ? $hold->get_num_object() : array($hold->get_num_object()))
				)
			);
            if ($id_layer == "authority" || $id_layer == "authority_concept") {
                $type_authority = $this->models[$id_layer]->get_type();                
                $notices_ids = $this->get_restrict_ids();               
                if ($type_authority == 2) {
                    $requete = "select notcateg_notice as notice_id from notices_categories where num_noeud in (" . implode(",", $infos['objects']['authority']) . ")";
                    if (count($notices_ids)) {
                        $requete.= " and notcateg_notice in (" . implode(",", $notices_ids) . ")";
                    }
                } else {
                    $requete = "select num_object as notice_id from index_concept where type_object=1 and num_concept in (" . implode(",", $infos['objects']['authority_concept']) . ")";
                    if (count($notices_ids)) {
                        $requete.= " and num_object in (" . implode(",", $notices_ids) . ")";
                    }
                }
                $result = pmb_mysql_query($requete);
                $notice_ids = array();
                while ($row = pmb_mysql_fetch_object($result)) {
                    $notice_ids[] = $row->notice_id;
                }
                $infos['objects']['record'] = $notice_ids;
            }
			$informations[] = $infos;
		}
		return $informations;
	}
	
	public function get_restrict_ids() {
	    
	    if(isset($_SESSION['tab_result'])) {
	        return explode(',', $_SESSION['tab_result']);
	    }
	}
	
	public function have_results(){
		$have_results = false;
		foreach($this->models as $model){
			$have_results = $model->have_results();
			if($have_results) {
				break;
			}
		}
  		return $have_results;
  	}
  	
  	public function set_mode($mode){
  		$this->mode = $mode;
  	}
  	
  	public function get_mode(){
  		return $this->mode;
  	}
	
} // end of map_model