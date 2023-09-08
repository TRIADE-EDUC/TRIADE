<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_layer_model_record.class.php,v 1.7 2017-02-09 15:08:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/map/map_layer_model.class.php");

/**
 * class map_layer_model_record
 * Classe représentant le modèle de données pour des notices
 */
class map_layer_model_record extends map_layer_model {

  /** Aggregations: */

  /** Compositions: */

   /*** Attributes: ***/


  /**
   * Va chercher et instancier les emprises correspondantes.
   * Peut appeler la classe map_model_authority pour les emprises des autorités
   * utilisées pour indexer la notice
   *
   * @return void
   * @access public
   */
  public function fetch_datas() {
  	global $dbh;  
  	global $opac_map_holds_record_color;
  	
  	$this->holds=array();	
  	
  	$emprises = array();
  	$coordonnees =array();
  	$infos =array();
  	if(count($this->ids)>0){
  		$req="select map_emprises.map_emprise_id, map_emprises.map_emprise_obj_num, AsText(map_emprises.map_emprise_data) as map, map_hold_areas.bbox_area as bbox_area, map_hold_areas.center as center from map_emprises join map_hold_areas on map_emprises.map_emprise_id = map_hold_areas.id_obj where map_emprises.map_emprise_type=".TYPE_RECORD." and map_emprises.map_emprise_obj_num in (".implode(",", $this->ids).")";
  		$res=pmb_mysql_query($req, $dbh);
  		if (pmb_mysql_num_rows($res)) {
  			while($r=pmb_mysql_fetch_object($res)){
  				$geometric = strtolower(substr($r->map,0,strpos($r->map,"(")));
  				$hold_class = "map_hold_".$geometric;
  				if(class_exists($hold_class)){
  					$emprise =  new $hold_class("record",$r->map_emprise_obj_num,$r->map);
  					$emprise->set_normalized_bbox_area($r->bbox_area);
  					$emprise->set_center($r->center);
  					$this->holds[$r->map_emprise_id] = $emprise;
  				}
  			}
  		}	
  	}
  	$this->color = $opac_map_holds_record_color;
  } // end of member function fetch_datas
  
  protected function get_layer_model_type(){
  	return "record";
  }

  protected function get_layer_model_name() {
  	return "record";
  }

} // end of map_layer_model_records