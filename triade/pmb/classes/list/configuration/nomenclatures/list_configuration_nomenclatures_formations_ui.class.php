<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_formations_ui.class.php,v 1.3 2023/03/07 15:30:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_formation.class.php");

class list_configuration_nomenclatures_formations_ui extends list_configuration_nomenclatures_ui {
	
	protected static $object_type = 'formation';
	
	protected static $table_name = 'nomenclature_formations';
	protected static $field_id = 'id_formation';
	protected static $field_order = 'formation_order';
	
	protected function get_object_instance($row) {
		return new nomenclature_formation($row->id_formation);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_nomenclature_formation_name',
				'types' => 'admin_nomenclature_formation_types',
				'nature' => 'admin_nomenclature_formation_nature',
		);
	}
		
	protected function init_default_columns() {
		$this->add_column_dnd();
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
		$this->add_column_types_edition();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'order', 'name', 'types', 'nature', 'types_edition'
		);
	}
	
	protected static function get_types_url_base() {
		global $base_path;
		return $base_path."/".static::$module.".php?categ=formation&sub=formation_type";
	}
	
	protected function add_column_types_edition() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['admin_nomenclature_type_edition'],
				'link' => static::get_types_url_base()."&num_formation=!!id!!"
		);
		$this->add_column_simple_action('types_edition', '', $html_properties);
	}
	
	protected function _get_object_property_nature($object) {
		global $msg;
		if($object->get_nature()) { // voix
			return $msg['admin_nomenclature_formation_form_nature_voice'];
		} else {// instruments
			return $msg['admin_nomenclature_formation_form_nature_instrument'];
		}
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'types':
				$types = $object->get_types();
				if(is_array($types)) {
					foreach ($types as $type) {
						if($content) {
							$content .= "<br />";
						}
						$content .= "<a href='".static::get_types_url_base()."&action=form&id=".$type->get_id()."&num_formation=".$type->get_formation_num()."'>".$type->get_name()."</a>";
					}
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
}