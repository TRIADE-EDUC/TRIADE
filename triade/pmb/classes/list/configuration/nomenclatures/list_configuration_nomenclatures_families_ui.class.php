<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_nomenclatures_families_ui.class.php,v 1.2 2023/03/07 15:30:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/nomenclature/nomenclature_family.class.php");

class list_configuration_nomenclatures_families_ui extends list_configuration_nomenclatures_ui {
	
	protected static $object_type = 'family';
	
	protected static $table_name = 'nomenclature_families';
	protected static $field_id = 'id_family';
	protected static $field_order = 'family_order';
	
	protected function get_object_instance($row) {
		return new nomenclature_family($row->id_family);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_nomenclature_family_name',
				'pupitres' => 'admin_nomenclature_family_pupitres'
		);
	}
		
	protected function init_default_columns() {
		$this->add_column_dnd();
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
		$this->add_column_musicstand_edition();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'order', 'name', 'pupitres', 'musicstand_edition'
		);
	}
	
	protected static function get_musicstands_url_base() {
		global $base_path;
		return $base_path."/".static::$module.".php?categ=family&sub=family_musicstand";
	}
	
	protected function add_column_musicstand_edition() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['admin_nomenclature_musicstand_edition'],
				'link' => static::get_musicstands_url_base()."&num_family=!!id!!"
		);
		$this->add_column_simple_action('musicstand_edition', '', $html_properties);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'pupitres':
				$musicstands = $object->get_musicstands();
				if(is_array($musicstands)) {
					foreach ($musicstands as $musicstand) {
						if($content) {
							$content .= "<br />";
						}
						$content .= "<a href='".static::get_musicstands_url_base()."&action=form&id=".$musicstand->get_id()."&num_family=".$musicstand->get_family_num()."'>".$musicstand->get_name()."</a>";
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