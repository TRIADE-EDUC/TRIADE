<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: records_bulletins_collstate_edition_controller.class.php,v 1.1 2022/02/15 08:32:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/records/records_bulletins_controller.class.php");

class records_bulletins_collstate_edition_controller extends records_bulletins_controller {
	
	protected static $list_ui_class_name = 'list_records_bulletins_collstate_edition_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		return new static::$list_ui_class_name(array('niveau_biblio' => 's', 'niveau_hierar' => '1'), array(), array());
	}
	
}