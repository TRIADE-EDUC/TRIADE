<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_hold_polygon.class.php,v 1.5 2022/04/20 07:52:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/map/map_hold.class.php");

/**
 * class map_hold_polygon
 * 
 */

//TODO, la présence d'un deuxième polygone n'est un autre mais un trou dans le premier
class map_hold_polygon extends map_hold {

	/** Aggregations: */
	
	/** Compositions: */
	
	/*** Attributes: ***/
	
	/**
	 * Multipolygon
	 * @access protected
	 */
	protected $multiple = false;
	
	/**
	 *
	 *
	 * @return string
	 * @access public
	 */
	public function get_hold_type() {
		 
		return "POLYGON";
		 
	} // end of member function get_hold_type
	
	
  	protected function build_coords(){
  		$coords_string = substr($this->wkt,strpos($this->wkt,"(")+1,-1);
  		//s'agit-il d'un multi-polygon ?
  		if (strpos($coords_string,"),(") !== false) $this->multiple = true;
  		if ($this->multiple) {
  			$coords_multiple_string = substr($coords_string,strpos($coords_string,"(")+1,-1);
  			$polygons = explode("),(",$coords_multiple_string);
  			foreach ($polygons as $polygon) {
  				$coords = explode(",",$polygon);
  				$coords_polygon =array();
  				for($i=0 ; $i< count($coords) ; $i++){
  					$infos =array();
  					$coord = $coords[$i];
  					$infos = explode(" ",$coord);
  					//on ne met pas la dernière coordonnée, c'est la même que la 1ere
  					if (0 == $i || $coords[0] != $coords[$i]) {
  						$coords_polygon[] = new map_coord($infos[0],$infos[1]);
  					}
  				}
  				$this->coords[] = $coords_polygon;
  			}
  		} else {
  			$coords_string = str_replace(array("(",")"),"",$coords_string);
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
  		}
  		$this->coords_uptodate = true;
  	}
  	
  	protected function build_transcription(){
  		if ($this->multiple) {
  			//A traiter plus tard si besoin
  		} else {
  			$this->transcription="(";
  			if(!empty($this->coords[0]) && $this->coords[0]->get_decimal_long() >= 0) $this->transcription.="E ";
  			else $this->transcription.="W ";
  			if(!empty($this->coords[0])) {
  				$this->transcription.=map_coord::convert_decimal_to_sexagesimal($this->coords[0]->get_decimal_long());
  			}
  			$this->transcription.=" - ";
  			if(!empty($this->coords[1]) && $this->coords[1]->get_decimal_long() >= 0) $this->transcription.="E ";
  			else $this->transcription.="W ";
  			if(!empty($this->coords[1])) {
  				$this->transcription.=map_coord::convert_decimal_to_sexagesimal($this->coords[1]->get_decimal_long());
  			}
  			$this->transcription.=" / ";
  			if(!empty($this->coords[2]) && $this->coords[2]->get_decimal_lat() >= 0) $this->transcription.="N ";
  			else $this->transcription.="S ";
  			if(!empty($this->coords[2])) {
  				$this->transcription.=map_coord::convert_decimal_to_sexagesimal($this->coords[2]->get_decimal_lat());
  			}
  			$this->transcription.=" - ";
  			if(!empty($this->coords[0]) && $this->coords[0]->get_decimal_lat() >= 0) $this->transcription.="N ";
  			else $this->transcription.="S ";
  			if(!empty($this->coords[0])) {
  				$this->transcription.=map_coord::convert_decimal_to_sexagesimal($this->coords[0]->get_decimal_lat());
  			}
  			$this->transcription.=")";
  		}
  	}
 	
	protected function build_wkt(){
		$this->wkt = $this->get_hold_type()."(";
		if ($this->multiple) {
			$tmp_wkt = "";
			foreach ($this->coords as $polygon) {
				if ($tmp_wkt == "") $tmp_wkt = "(";
				else $tmp_wkt .= ",("; 
				foreach($polygon as $coord){
					$tmp_wkt.= $coord->get_decimal_lat()." ".$coord->get_decimal_long().",";
				}
				$tmp_wkt.= $polygon[0]->get_decimal_lat()." ".$polygon[0]->get_decimal_long();
				$tmp_wkt .= ")";
			}
			$this->wkt .= $tmp_wkt;
		} else {
			$this->wkt .= "(";
			foreach($this->coords as $coord){
				$this->wkt.= $coord->get_decimal_lat()." ".$coord->get_decimal_long().",";
			}
			$this->wkt.= $this->coords[0]->get_decimal_lat()." ".$this->coords[0]->get_decimal_long().")";
		}
		$this->wkt .= ")";
		$this->wkt_uptodate = true;
	}
	
} // end of map_hold_polygon