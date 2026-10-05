<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acces_profiles_resources_controller.class.php,v 1.1 2022/12/21 08:25:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/acces/acces_profiles_controller.class.php");

class acces_profiles_resources_controller extends acces_profiles_controller {
	
	protected static $list_ui_class_name = 'list_acces_profiles_resources_ui';
	
	protected static $profile_type = 'res';
	
	public static function proceed($id=0) {
		global $action;
		global $prf_id, $prf_lib, $prf_rule, $prf_hrule, $prf_used, $unused_prf_id;
		
		switch ($action) {
			case 'update' :
				if (!isset($unused_prf_id)) {
					$unused_prf_id = array();
				}
				static::$dom->saveResourceProfiles($prf_id, stripslashes_array($prf_lib), stripslashes_array($prf_rule), stripslashes_array($prf_hrule), $prf_used, $unused_prf_id);
				print static::get_display_profiles_list($id,true);
				break;
			case 'delete' :
				static::$dom->deleteResourceProfiles();
				print static::get_display_profiles_list($id);
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	protected static function get_template_profiles_list() {
		global $res_prf_list_form;
		
		return $res_prf_list_form;
	}
	
	public static function get_display_profiles_list($id,$maj=false) {
		global $dom;
		
		$form = parent::get_display_profiles_list($id, $maj);
		$form = str_replace('<!-- properties -->', $dom->getDisplayResourceProperties(), $form);
		return $form;
	}
	
	public static function get_display_calc_profiles_list($id) {
		global $dom;
		
		$form = parent::get_display_calc_profiles_list($id);
		$form = str_replace('<!-- properties -->', $dom->getDisplayResourceProperties(), $form);
		return $form;
	}
}
