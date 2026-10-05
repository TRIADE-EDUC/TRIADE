<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loans_groups_edition_controller.class.php,v 1.2 2021/04/13 08:04:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/loans/loans_groups_controller.class.php");

class loans_groups_edition_controller extends loans_groups_controller {
	
	protected static $list_ui_class_name = 'list_loans_groups_edition_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $sub;
		
		$list_ui_instance = null;
		switch($sub) {
		    case 'ppargroupe' :
		        $list_ui_instance = new static::$list_ui_class_name(array('associated_group' => '1', 'pret_retour_end' => '', 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'groups'));
		        break;
		    case 'rpargroupe' :
		        $list_ui_instance = new static::$list_ui_class_name(array('associated_group' => '1', 'pret_retour_end' => date('Y-m-d'), 'pret_date_end' => '', 'pret_retour_start' => ''), array(), array('by' => 'groups'));
		        break;
		}
		return $list_ui_instance;
	}
	
	public static function proceed($id=0) {
		global $dest;
		
		parent::proceed($id);
		switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				break;
			default:
				//impression/emails (on est dans le cas retards/retards par date)
//              if ($action == "print") {
//                 $list_ui_instance->print_relances();
// 				}
				break;
		}
	}
}