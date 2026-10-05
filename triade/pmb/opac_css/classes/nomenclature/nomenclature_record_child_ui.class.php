<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_child_ui.class.php,v 1.2 2022/09/12 09:19:11 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_record_child.class.php");

/**
 * class nomenclature_record_formations
 * Représente les formations de la nomenclature d'une notice
 */


class nomenclature_record_child_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	public $record_child;
	
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->record_child = new nomenclature_record_child($this->id);
	} // end of member function __construct
		
	public function get_form(){		
		$data= encoding_normalize::json_encode($this->record_child->get_data());		
		$div .= "
  		<div id='nomenclature_record_child_".$this->id."' data-dojo-type='apps/nomenclature/nomenclature_child_record_ui' data-dojo-props='num_record:".$this->id.",record_child:\"".addslashes($data)."\"'></div>";
  		return $div;
	} 
	
	public function save_form(){
		if(!$this->id)return; // pas id de notice		
		$this->record_child->save_form();
	}
	
	public function delete(){		
		$this->record_child->delete();
	}
	
	public function get_isbd(){
		global $msg;	
		$isbd="";	
		$data=$this->record_child->get_data();
		
		$type_display="";
		if($data["type_name"])$type_display.=" / ".$data["type_name"];
		if($data["formation_label"])$type_display.=" / ".$data["formation_label"];
		if(!$data["nature"]){
			// Instrument
			$isbd.="
				<b>".$msg["nomenclature_isbd_child_formation"]."</b> : ".$data["formation_name"].$type_display."<br/>
				<b>".$msg["nomenclature_isbd_child_musicstand"]."</b> : ".$data["musicstand_name"]."<br/>
				<b>".$msg["nomenclature_isbd_child_instrument"]."</b> : ".$data["instrument_name"]."<br/>";
			if($data["other"]) {
				$other_instruments = explode('/', $data["other"]);
				$other_instruments_name = array();
				foreach ($other_instruments as $other_instrument) {
					$instrument_name = nomenclature_instrument::get_instrument_name_from_code($other_instrument);
					if($instrument_name) {
						$other_instruments_name[] = $instrument_name;
					}
				}
				if(count($other_instruments_name)) {
					$isbd.="
						<b>".$msg["nomenclature_isbd_child_other_instruments"]."</b> : ".implode(' / ', $other_instruments_name)."<br />";
				}
			}
			$isbd.="
				<b>".$msg["nomenclature_isbd_child_effective"]."</b> : " .$data["effective"]."<br/>
				<b>".$msg["nomenclature_isbd_child_order"]."</b> : " .$data["order"]."<br/>			
			";
		}else{
			//Voix
			$isbd.="
				<b>".$msg["nomenclature_isbd_child_formation"]."</b> : ".$data["formation_name"].$type_display."<br/>
				<b>".$msg["nomenclature_isbd_child_voice"]."</b> : ".$data["voice_name"]."<br/>
				<b>".$msg["nomenclature_isbd_child_effective"]."</b> : " .$data["effective"]."<br/>
				<b>".$msg["nomenclature_isbd_child_order"]."</b> : " .$data["order"]."<br/>			
			";
		}
  		return $isbd;
	}
	
	public static function get_index($id) {
		$record_child = new nomenclature_record_child($id);
		return $record_child->get_index();
	}
	
	public function get_possible_values($num_parent){
		return encoding_normalize::json_encode($this->record_child->get_possible_values($num_parent));
	}
} // end of nomenclature_record_child_ui

