<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_tables_data_controller.class.php,v 1.1 2021/11/25 14:30:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/misc/misc_controller.class.php");

class misc_tables_data_controller extends misc_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = 'list_misc_tables_data_ui';
	
	public static function proceed($id=0) {
		global $table;
		
		$id = intval($id);
		list_misc_tables_data_ui::set_table($table);
		print list_misc_tables_data_ui::get_selector_tables();
		parent::proceed($id);
	}
}