<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_instruments_ui.class.php,v 1.4 2023/05/05 13:38:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_instrument.class.php");

class list_configuration_nomenclatures_instruments_ui extends list_configuration_nomenclatures_ui {
	
	protected static $object_type = 'instrument';
	
	protected static $table_name = 'nomenclature_instruments';
	protected static $field_id = 'id_instrument';
	protected static $field_order = 'instrument_order';
	
	protected function get_object_instance($row) {
		return new nomenclature_instrument($row->id_instrument);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('code');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'code' => 'admin_nomenclature_instrument_code',
				'name' => 'admin_nomenclature_instrument_name',
				'musicstand' => 'admin_nomenclature_instrument_musicstand',
				'musicstand_family' => 'admin_nomenclature_instrument_musicstand_family',
				'standard' => 'admin_nomenclature_instrument_standard',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('standard', 'align', 'center');
		$this->set_setting_column('standard', 'datatype', 'boolean');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'code', 'name', 'musicstand', 'musicstand_family', 'standard'
		);
	}
	
	protected static function get_families_url_base() {
		global $base_path;
		return $base_path."/".static::$module.".php?categ=family&sub=family";
	}
	
	protected static function get_musicstands_url_base() {
		global $base_path;
		return $base_path."/".static::$module.".php?categ=family&sub=family_musicstand";
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'musicstand':
				$nomenclature_musicstand = nomenclature_musicstand::get_instance($object->get_musicstand_num());
				$content .= "<a href='".static::get_musicstands_url_base()."&action=form&id=".$nomenclature_musicstand->get_id()."'>".$nomenclature_musicstand->get_name()."</a>";
				break;
			case 'musicstand_family':
				$nomenclature_musicstand = nomenclature_musicstand::get_instance($object->get_musicstand_num());
				$family = $nomenclature_musicstand->get_family();
				if(is_object($family)) {
					$content .= "<a href='".static::get_families_url_base()."&action=form&id=".$family->get_id()."'>".$family->get_name()."</a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
}