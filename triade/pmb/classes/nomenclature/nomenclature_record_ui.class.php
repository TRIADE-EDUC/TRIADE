<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_ui.class.php,v 1.13 2023/05/04 09:22:14 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class nomenclature_record_ui
 * Représente la nomenclature d'une notice
 */
global $class_path;
require_once($class_path."/nomenclature/nomenclature_record_formations_ui.class.php");
require_once($class_path."/nomenclature/nomenclature_record_child_ui.class.php");
require_once($class_path."/nomenclature/nomenclature_datastore.class.php");
require_once($class_path."/notice_relations_collection.class.php");

class nomenclature_record_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Nom du type
	 * @access protected
	 */

	public $nomenclature_record;
	public $id;
	public $id_parent;
		
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		global $pmb_nomenclature_record_children_link;
		
		$this->id = intval($id);	
		$this->id_parent=0;
		
		$notice_relations = notice_relations_collection::get_object_instance($this->id);
		$parents = $notice_relations->get_parents();
		if(isset($parents[$pmb_nomenclature_record_children_link][0])) {
			$this->id_parent=$parents[$pmb_nomenclature_record_children_link][0]->get_linked_notice();
			$this->nomenclature_record = new nomenclature_record_child_ui($this->id);
		} else {
			$this->nomenclature_record = new nomenclature_record_formations_ui($this->id);
		}
	} // end of member function __construct
	
	public function create_record_child($id_parent){
		$this->nomenclature_record = new nomenclature_record_child_ui();
		return $this->nomenclature_record->create_record_child($id_parent);
	}
		
	public function get_form($duplicate = false){

		$args = "num_record:".($duplicate ? 0 : $this->id);
		if($this->id_parent){
			$args.=",child_detail :\"".addslashes(encoding_normalize::json_encode($this->nomenclature_record->record_child->get_data()))."\"";
		}else{
			$args.=",record_formations :\"".addslashes(encoding_normalize::json_encode($this->nomenclature_record->record_formations->get_data($duplicate)))."\"";
		}
		
		$div = nomenclature_datastore::get_form();
		$div.= "<script type='text/javascript' src='./javascript/instru_drag_n_drop.js'></script>";
		return $div."<div data-dojo-type='apps/nomenclature/nomenclature_record_ui' data-dojo-props='".$args."'></div>";
	} 
	
	public function save_form(){	
		
		if(!$this->id)return; // pas id de notice
		$this->nomenclature_record->save_form();				
	}
	
	public function delete(){		
		if(!$this->id)return; // pas id de notice	
		$this->nomenclature_record->delete();

	}
	
	public function get_isbd(){
		return $this->nomenclature_record->get_isbd();
	}
	
	public static function get_index($id) {
		global $pmb_nomenclature_record_children_link;
		
		$notice_relations = notice_relations_collection::get_object_instance($id);
		$parents = $notice_relations->get_parents();
		if(isset($parents[$pmb_nomenclature_record_children_link][0])) {
			return nomenclature_record_child_ui::get_index($id);
		}else{
			return nomenclature_record_formations_ui::get_index($id);
		}	
	}
} // end of nomenclature_record_formations

