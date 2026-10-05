<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold.class.php,v 1.7 2021/12/24 08:34:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/map/map_hold.class.php");
require_once($class_path."/map/map_coord.class.php");


/**
 * class map_hold
 * 
 */
abstract class map_hold {

  	/** Aggregations: */

	/** Compositions: */

   	/*** Attributes: ***/

  	/**
   	* Tableau des coordonnées de l'emprise
   	* @access protected
   	*/
  	protected $coords = array();

  	/**
   	* Type de l'objet associé
   	* @access protected
   	*/
  	protected $object_type;

  	/**
   	* Identifiant de l'objet associé
   	* @access protected
   	*/
  	protected $num_object;
  	
  	/**
  	 * WKT
  	 * @access protected
  	 */
  	protected $wkt = "";
  	
  	/**
  	 * map_hold bounding box 
  	 * @access protected
  	 */
  	protected $bounding_box;

  	/**
  	 * Tableau des coordonnées à jour (Booléen)
  	 * @access protected
  	 */
  	protected $coords_uptodate = true;
  	
  	/**
  	 * WKT à jour (Booléen)
  	 * @access protected
  	 */
  	protected $wkt_uptodate = true;
  	
  	/**
  	 * Transcription de la bounding box 
  	 * @access protected
  	 */
  	protected $transcription = "";
  	
  	/**
  	 * Aire de la boite normalisée de l'emprise
  	 * @access protected
  	 */
  	protected $normalized_bbox_area;
  	
  	/**
  	 * centre de la boite normalisée de l'emprise
  	 * @access protected
  	 */
  	protected $center;
  	
  	protected $record;
  	
  	protected $color;
  	
  	/**
  	 *
  	 *
  	 * @param string object_type Type de l'objet associé
  	
  	 * @param int num_object Identifiant de l'objet associé
  	
  	 * @return void
  	 * @access public
  	 */
  	public function __construct($object_type,  $num_object, $wkt= "") {
  		$this->object_type = $object_type;
  		$this->num_object = $num_object;
  		if ($wkt) $this->set_wkt($wkt);
  	} // end of member function __construct
  	
  	protected function build_coords(){
  		$coords_string = substr($this->wkt,strpos($this->wkt,"(")+2,-2);
  		$coords = explode(",",$coords_string);
  		$this->coords =array();
  		for($i=0 ; $i< count($coords) ; $i++){
  			$infos =array();
  			$coord = $coords[$i];
  			$infos = explode(" ",$coord);
  			//on ne met pas la dernière coordonnée, c'est la même que la 1ere
  			if (0 == $i || $coords[0] != $coords[$i]) {
  				$this->coords[] = new map_coord($infos[0],$infos[1]);
  			}
  		}
  		$this->coords_uptodate = true;
  	}
  	
  	protected function build_wkt(){
  		$this->wkt = $this->get_hold_type()."(";
  		foreach($this->coords as $coord){
  			$this->wkt.= $coord->get_decimal_lat()." ".$coord->get_decimal_long().",";
  		}
  		$this->wkt_uptodate = true;
  	}
  	
  	protected function build_transcription(){
  		$this->transcription=""; // dépend du type 
  	}
  	
  	public function get_transcription() {  		
  		if(!$this->transcription){
	  		$this->build_coords();
  			$this->build_transcription();
  		}	
  		return $this->transcription;  			
  	}
  	
  	/**
  	 * Retourne une emprise normalisée contenant l'emprise courante
  	 *
  	 * @return map_hold
  	 * @access public
  	 */
  	public function get_bounding_box() {
  		if(!$this->bounding_box){
  			if (!$this->wkt_uptodate) {
  				$this->build_wkt();
  			}
	  		$query =" select astext(envelope(geomfromtext('".$this->wkt."'))) as bounding_box";
			$result = pmb_mysql_query($query);  		
	  		if(pmb_mysql_num_rows($result)){
	  			$this->bounding_box = new map_hold_polygon($this->object_type, $this->num_object,pmb_mysql_result($result,0,0));
	  		}
  		}
  		return $this->bounding_box;
  		
  	} // end of member function get_bounding_box


  	/**
  	 *
  	 *
  	 * @param Array() coords Tableau de coordonnée map_coord représentant l'emprise
  	
  	 * @return void
  	 * @access public
  	 */
  	public function set_coords( $coords) {
  		$this->coords = $coords;
  		$this->coords_uptodate = true;
  		$this->wkt_uptodate = false;
  	} // end of member function set_coords

  	/**
  	 * Permet d'ajouter une coordonnée dans l'emprise  dans la propriété coords
  	 *
  	 * @param map_coord coord Coordonnée à  ajouter dans l'emprise
  	
  	 * @param map_coord after
  	
  	 * @return void
  	 * @access public
  	 */
  	public function add_coord( $coord,  $after=null) {
  		if (!$this->coords_uptodate) {
  			$this->build_coords();
  		}
  		$coords = array(); 
  		if($after){
	  		foreach ($this->coords as $object) {
	  			$coords[] = $object;
	  			if (($object->get_decimal_lat() == $after->get_decimal_lat()) && ($object->get_decimal_long() == $after->get_decimal_long())) {
	  				$coords[] = $coord;
	  			}
	  		}
  		}else{
  			$this->coords[] = $coords;
  		}
  		$this->coords = $coords;
  		$this->wkt_uptodate = false;
  	} // end of member function add_coord

  	/**
  	 * Permet de supprimer une coordonnée dans la propriété coords
  	 *
  	 * @param map_coord coord Coordonnée à  supprimer de l'emprise
  	
  	 * @return void
  	 * @access public
  	 */
  	public function delete_coord( $coord) {
  		if (!$this->coords_uptodate) {
  			$this->build_coords();
  		}
  		$coords = array();
  		foreach ($this->coords as $object) {
  			if (($object->get_decimal_lat() != $coord->get_decimal_lat()) && ($object->get_decimal_long() != $coord->get_decimal_long())) {
  				$coords[] = $object;
  			}
  		}
  		$this->coords = $coords;
  		$this->wkt_uptodate = false;
  		
  	} // end of member function delete_coord

  	/**
  	 * Retourne le tableau des coordonnées
  	 *
  	 * @return Array()
  	 * @access public
  	 */
  	public function get_coords() {
  		if (!$this->coords_uptodate) {
  			$this->build_coords();
  		} 
  		return $this->coords;
  	} // end of member function get_coords
  	
  	/**
  	 * Sauvergarde de l'emprise
  	 *
  	 * @return bool
  	 * @access public
  	 */
  	public function save() {
  	} // end of member function save

  	/**
  	 * Méthode abstraite. A voir si on peut jouer avec les fonctions PHP de
  	 * manipulations de classes pour ne pas avoir à  dériver cette méthode...
  	 *
  	 * @return string
  	 * @abstract
  	 * @access public
  	 */
  	abstract public function get_hold_type();
  	
  	/**
  	 * Retourne un export de l'emprise au format WKT
  	 *
  	 * @return string
  	 * @access public
  	*/
  	public function export() {
  	} // end of member function export

  	public function get_num_object(){
  		return $this->num_object;
  	}
  	
  	public function set_num_object($num_object){
  		$this->num_object = $num_object;
  	}
  	
  	public function set_record($ids) {
  	    $this->record = $ids;
  	}
  	
  	public function get_record() {
  	    return $this->record;
  	}
  	
  	public function set_wkt($wkt){
  		$this->wkt = $wkt;
  		$this->coords_uptodate = false;
  		$this->wkt_uptodate = true;
  	}
  	
  	public function get_wkt(){
  		if (!$this->wkt_uptodate) {
  			$this->build_wkt();
  		}
  		return $this->wkt;
  	}
  	
  	public function get_normalized_bbox_area(){
  		return $this->normalized_bbox_area;
  	}
  	
  	public function set_normalized_bbox_area($bbox){
  		$this->normalized_bbox_area = $bbox;
  	}
  	
  	public function get_center(){
  		return $this->center;
  	}
  	 
  	public function set_center($center){
  		$this->center = $center;
  	}
  	
  	public function set_color($color){
  	    $this->color = $color;
  	}
  	
} // end of map_hold