<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_material_admin.class.php,v 1.3 2021/03/10 12:58:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;

require_once($include_path."/templates/nomenclature_material_admin.tpl.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path.'/skos/skos_concept.class.php');
require_once($class_path."/parameters/parameter.class.php");

class nomenclature_material_admin {
	
	protected $music_concept_before;
	protected $music_concept_after;
	protected $music_concept_blank;
	protected $music_children_relation;
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $pmb_nomenclature_music_concept_before, $pmb_nomenclature_music_concept_after, $pmb_nomenclature_music_concept_blank;
		global $pmb_nomenclature_record_children_link;
		
		$this->music_concept_before = $pmb_nomenclature_music_concept_before;
		$this->music_concept_after = $pmb_nomenclature_music_concept_after;
		$this->music_concept_blank = $pmb_nomenclature_music_concept_blank;
		$this->music_children_relation = $pmb_nomenclature_record_children_link;
	}
 
	public function get_form() {
		global $nomenclature_material_form_tpl, $charset;

		$concept_before_label = '';
		if ($this->music_concept_before) {
			$concept = new skos_concept(0, $this->music_concept_before);
			$concept_before_label = $concept->get_display_label();
		}
		$concept_after_label = '';
		if ($this->music_concept_after) {
			$concept = new skos_concept(0, $this->music_concept_after);
			$concept_after_label = $concept->get_display_label();
		}
		$concept_blank_label = '';
		if ($this->music_concept_blank) {
			$concept = new skos_concept(0, $this->music_concept_blank);
			$concept_blank_label = $concept->get_display_label();
		}
		$nomenclature_material_form_tpl = str_replace('!!music_concept_before_value!!', $this->music_concept_before, $nomenclature_material_form_tpl);
		$nomenclature_material_form_tpl = str_replace('!!music_concept_before_label!!', htmlentities($concept_before_label, ENT_QUOTES, $charset), $nomenclature_material_form_tpl);
		$nomenclature_material_form_tpl = str_replace('!!music_concept_after_value!!', $this->music_concept_after, $nomenclature_material_form_tpl);
		$nomenclature_material_form_tpl = str_replace('!!music_concept_after_label!!', htmlentities($concept_after_label, ENT_QUOTES, $charset), $nomenclature_material_form_tpl);
		$nomenclature_material_form_tpl = str_replace('!!music_concept_blank_value!!', $this->music_concept_blank, $nomenclature_material_form_tpl);
		$nomenclature_material_form_tpl = str_replace('!!music_concept_blank_label!!', htmlentities($concept_blank_label, ENT_QUOTES, $charset), $nomenclature_material_form_tpl);

		$liste_type_relation_down=new marc_select("relationtypedown", 'music_children_relation', $this->music_children_relation);
		$nomenclature_material_form_tpl = str_replace('!!music_children_relation_select!!', $liste_type_relation_down->display, $nomenclature_material_form_tpl);
		
		return $nomenclature_material_form_tpl;
	}
	
	public function get_values_from_form() {
		global $music_concept_before_value, $music_concept_after_value, $music_concept_blank_value, $music_children_relation;
		
		$this->music_concept_before = $music_concept_before_value;
		$this->music_concept_after = $music_concept_after_value;
		$this->music_concept_blank = $music_concept_blank_value;
		$this->music_children_relation = $music_children_relation;
	}
	
	public function save() {
		global $msg;

		parameter::update('pmb', 'nomenclature_music_concept_before', $this->music_concept_before);
		parameter::update('pmb', 'nomenclature_music_concept_after', $this->music_concept_after);
		parameter::update('pmb', 'nomenclature_music_concept_blank', $this->music_concept_blank);
		parameter::update('pmb', 'nomenclature_record_children_link', $this->music_children_relation);
		print display_notification($msg['account_types_success_saved']);
	}
}