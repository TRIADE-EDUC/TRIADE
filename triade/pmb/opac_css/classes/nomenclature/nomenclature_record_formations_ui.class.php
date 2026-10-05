<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formations_ui.class.php,v 1.5 2023/08/17 09:47:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class nomenclature_record_formations
 * Représente les formations de la nomenclature d'une notice
 */
global $class_path;
require_once($class_path."/nomenclature/nomenclature_record_formations.class.php");
require_once($class_path."/nomenclature/nomenclature_formations.class.php");
require_once($class_path."/nomenclature/nomenclature_datastore.class.php");
require_once($class_path."/nomenclature/nomenclature_family.class.php");

class nomenclature_record_formations_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Nom du type
	 * @access protected
	 */

	public $record_formations;
	public $id;
		
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->record_formations = new nomenclature_record_formations($id);
	} // end of member function __construct
	
	public function get_form(){
		
		$data=  encoding_normalize::json_encode($this->record_formations->get_data());				
		$div .= "
  		<script src='./javascript/instru_drag_n_drop.js'></script>
  		<div id='nomenclature_record_formations_".$this->record_formations->get_id()."' data-dojo-type='apps/nomenclature/nomenclature_record_formations_ui' data-dojo-props='num_record:".$this->record_formations->id.",record_formations:\"".addslashes($data)."\"'></div>";
  		return $div;
	} 
	
	public function save_form(){		
		global $record_formations;
		
		if(!$this->id)return; // pas id de notice
		$formations_list=array();
		if (is_array($record_formations)) {
			foreach($record_formations as $name){
				global ${$name};
				$record_formation=${$name};
				$record_formation["num_record"]=$this->id;
				$formations_list[]=$record_formation;		
			}
		}
		$this->record_formations->save_form($formations_list);
	}
	
	public function delete(){		
		if(!$this->id)return; // pas id de notice
		// supression de la nomenclature de la notice 		
		$this->record_formations->delete();

	}
	
	public function get_isbd(){
		global $msg;	
		
		$all_formations= new nomenclature_formations();
		$isbd="";
		// pour toutes les formations de la nomenclature de la notice
		foreach($this->record_formations->get_data()  as $record_formation){	
			$titre="";
			$label="";
			$contenu="";
			$workshops_tpl="";
			$families_notes_tpl="";
			$exotic_instruments_note_tpl="";
			$has_workshop_undefined = false;
			foreach ($all_formations->get_data() as $formation){				
				if($formation['id']==$record_formation['num_formation']){
					foreach ($formation["types"] as $type){
						if($type['id']==$record_formation['num_type']){
							$label= " / ".$type['name'];
							break;
						}						
					}
					// décompose par atelier				
					foreach ($record_formation["workshops"] as $workshop){
						$workshop_tpl="- ".$workshop["label"];
						if(!$workshop['defined']){
							$workshop_tpl.= " &asymp;";
							$has_workshop_undefined = true;
						}
						if($workshop["label"]) $instruments_tpl=" : ";
						else $instruments_tpl="";
						foreach ($workshop["instruments"] as $indice=>$instrument){
							if($indice)$instruments_tpl.=" / ";
							$instruments_tpl.= " ".$instrument["effective"]." ".$instrument["code"];
							if($instrument["name"])$instruments_tpl.=" ( ". $instrument["name"]." ) ";
						}
						$workshop_tpl.= $instruments_tpl;
						if($workshop_tpl)$workshops_tpl.="<br />".$workshop_tpl;
					}
					
					// Ateliers: on liste tous les instruments sur une ligne
					/*
					foreach ($record_formation["workshops"] as $workshop){
						$instruments_tpl="";
						foreach ($workshop["instruments"] as $instrument){
							if($instruments_tpl)$instruments_tpl.=" / ";
							$instruments_tpl.= " ".$instrument["effective"]." ".$instrument["code"];
							if($instrument["name"])$instruments_tpl.=" ( ". $instrument["name"]." ) ";
						}
						$workshops_tpl.= $instruments_tpl;
					}
					*/
					
					if($workshops_tpl) $workshops_tpl="<br />".$msg['nomenclature_js_workshops_label']." : ".($has_workshop_undefined ? "&asymp;" : count($record_formation["workshops"])).$workshops_tpl;
					
					$instruments_no_standard_tpl="";
					foreach ($record_formation["instruments"] as $instrument){
						if($instruments_no_standard_tpl)$instruments_no_standard_tpl.=" / ";
						$instruments_no_standard_tpl.= " ".$instrument["effective"]." ".$instrument["code"];
						if($instrument["name"])$instruments_no_standard_tpl.=" ( ". $instrument["name"]." ) ";
						if(isset($instrument["other"]) && is_array($instrument["other"])) {
    						foreach ($instrument["other"] as $instrument_other){	
    							if($instruments_no_standard_tpl)$instruments_no_standard_tpl.=" / ";
    							$instruments_no_standard_tpl.= " ".$instrument["effective"]." ".$instrument_other["code"];
    							if($instrument_other["name"])$instruments_no_standard_tpl.=" ( ". $instrument_other["name"]." ) ";
    							
    						}				
						}
					}
					if($instruments_no_standard_tpl)$instruments_no_standard_tpl="<br />".$msg["nomenclature_formation_isbd_instruments_non_standards"].$instruments_no_standard_tpl;
					if(is_array($record_formation['families_notes'])) {
						foreach ($record_formation['families_notes'] as $id_family=>$family_note) {
							if($family_note != '') {
								$nomenclature_family = nomenclature_family::get_instance($id_family);
								$families_notes_tpl .= "<br />".$msg['nomenclature_js_family_note']." ".$nomenclature_family->get_name()." : ".$family_note;
							}
						}
					}
					if($record_formation['exotic_instruments_note']) {
						$exotic_instruments_note_tpl .= "<br />".$msg['nomenclature_js_exotic_instruments_note']." ".$msg['nomenclature_js_exotic_instruments_label']." : ".$record_formation['exotic_instruments_note'];
					}
					break;
				}
			}	
			$titre.="<b>".$msg["nomenclature_formation_isbd_formation"].$formation['name']."$label</b>";
			if($record_formation['label'])	$titre.= " / ".$record_formation['label'];	
			if($record_formation['abbreviation']){
				if(!$formation['nature'])
					$contenu.= $msg["nomenclature_formation_isbd_abbreviation"]. $record_formation['abbreviation'];
				else
					$contenu.= $msg["nomenclature_formation_isbd_abbreviation_voix"]. $record_formation['abbreviation'];
				if($record_formation['notes']) {
					$contenu.= "<br />".$msg['nomenclature_js_voices_note']." : ".$record_formation['notes'];
				}
			} elseif($record_formation['notes']) {
				$contenu.= $msg['nomenclature_js_voices_note']." : ".$record_formation['notes'];
			}
			$contenu.=$workshops_tpl.$instruments_no_standard_tpl.$families_notes_tpl.$exotic_instruments_note_tpl;
			
			$isbd.=gen_plus($record_formation['id'],$titre,$contenu);			
		}
  		return $isbd;
	}
	
	public static function get_index($id) {
		return nomenclature_record_formations::get_index($id);
	}
} // end of nomenclature_record_formations

