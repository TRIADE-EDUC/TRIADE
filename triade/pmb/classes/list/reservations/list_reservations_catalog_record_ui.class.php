<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_catalog_record_ui.class.php,v 1.3 2022/10/13 07:34:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_reservations_catalog_record_ui extends list_reservations_catalog_ui {
	
	protected function get_js_sort_script_sort() {
		return "<script type='text/javascript' src='./javascript/sorttable.js'></script>";
	}
	
	protected function _cell_is_sortable($name) {
		return false;
	}
	
	protected function get_uid_objects_list() {
		return $this->objects_type."_".$this->filters['id_notice']."_list";
	}
	
	protected function get_class_objects_list() {
		return parent::get_class_objects_list()." sortable";
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=';
	}
}