<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_voices_ui.class.php,v 1.3 2023/05/05 13:38:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_voice.class.php");

class list_configuration_nomenclatures_voices_ui extends list_configuration_nomenclatures_ui {
	
	protected static $object_type = 'voice';
	
	protected static $table_name = 'nomenclature_voices';
	protected static $field_id = 'id_voice';
	protected static $field_order = 'voice_order';
	
	protected function get_object_instance($row) {
		return nomenclature_voice::get_instance($row->id_voice);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'code' => 'admin_nomenclature_voice_code',
				'name' => 'admin_nomenclature_voice_name',
		);
	}
		
	protected function init_default_columns() {
		$this->add_column_dnd();
		parent::init_default_columns();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'code','name'
		);
	}
}