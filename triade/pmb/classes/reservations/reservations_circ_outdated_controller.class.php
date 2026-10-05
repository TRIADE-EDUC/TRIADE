<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reservations_circ_outdated_controller.class.php,v 1.1 2021/10/21 13:05:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/reservations/reservations_circ_controller.class.php");

class reservations_circ_outdated_controller extends reservations_circ_controller {
	
	protected static $list_ui_class_name = 'list_reservations_circ_outdated_ui';
	
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		return new static::$list_ui_class_name(array('id_notice' => 0, 'id_bulletin' => 0, 'id_empr' => 0, 'resa_state' => 'depassee'));
	}
	
}