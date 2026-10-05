<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_facette_controler.class.php,v 1.4 2019/02/26 13:48:41 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/map/map_objects_controler.class.php");

/**
 * class map_objects_controler
 * Controlleur de notre super dev
 */
class map_facette_controler extends map_objects_controler {
	
	public $location_objects;
	public $surlocation_objects;
	public $ids_surloc;
	
	
	public function __construct($type, $ids, $tab_locations, $ids_surloc, $tab_surlocations) {
		global $opac_map_max_holds;
		
		$this->id_dom = "facette";
		$this->ids=$ids;
		$this->location_objects = $tab_locations;
		
		$this->ids_surloc = $ids_surloc;
		$this->surlocation_objects = $tab_surlocations;
		
		$this->editable = false;
		$this->ajax = false;
		$this->type=$type;	
		
		if (count($ids)) {
			$this->objects[] = array(
					'layer' => "location",
					'ids' => $this->ids
			);
		}
		
		if (count($ids_surloc)) {
			$this->objects[] = array(
					'layer' => "sur_location",
					'ids' => $this->ids_surloc
			);
		}
		
		$this->fetch_datas();
		$this->model = new map_model(null, $this->objects,$opac_map_max_holds);
		$this->model->set_mode("search_location");
  		
  	} 
  	
	public function get_json_informations(){
  		global $opac_url_base;
  		  		
  		$map_hold = $this->get_bounding_box();
  		if($map_hold){
  			$coords = $map_hold->get_coords();
  			if(!count($coords)) {
  				return "";
  			}
  			return "mode:\"facette\", type:\"" . $this->type . "\", initialFit: [ ".self::get_coord_initialFit($coords)."], layers : ".json_encode($this->model->get_json_informations(false, $opac_url_base,$this->editable)).", data : " . encoding_normalize::json_encode(array_merge($this->location_objects,$this->surlocation_objects));
  		}else{
  			return "";
  		}
  	}
  	
  	public function get_map_controler_name(){
  		return "apps/map/map_location_facette_controler";
  	}

  	static public function get_map_facette_location($ids_loc, $tab_locations, $ids_surloc, $tab_surlocations) {
  		global $msg;
  		
  		if(!count($ids_loc) && !count($ids_surloc)){
  			return '';
  		}
                
  		if(count($ids_loc)==1 && !count($ids_surloc)){
  			return '';
  		}
  		
  		$map = new map_facette_controler(TYPE_LOCATION, $ids_loc, $tab_locations, $ids_surloc, $tab_surlocations);
  		return  "<span class='map_location_facette_title'>".$msg['record_expl_map_location']. "</span><div id='map_location_facette' class='map_location_facette'>". $map->get_map()."</div>";   		
  	}
  	
  	public function get_map_size() {
  		global $opac_map_size_location_facette;        
		$size=explode("*",$opac_map_size_location_facette);
		return $this->format_size($size);
  	} 	
} 
