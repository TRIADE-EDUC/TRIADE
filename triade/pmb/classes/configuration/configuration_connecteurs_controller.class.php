<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_connecteurs_controller.class.php,v 1.2 2021/02/25 08:46:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class configuration_connecteurs_controller extends configuration_controller {
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			case "editanonymous":
				
				break;
			case "updateanonymous":
				
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	public static function get_label_already_used() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'categout_sets':
				return $msg['admin_connecteurs_setcateg_namealreadyexists'];
			case 'out_sets':
				return $msg['admin_connecteurs_set_namealreadyexists'];
			default:
				return parent::get_label_already_used();
		}
	}
}