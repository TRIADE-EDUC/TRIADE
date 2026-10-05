<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.6 2020/12/09 09:45:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $sub, $action, $sub_action, $table;
global $id, $object_type;

require_once($base_path."/admin/planificateur/caddie/scheduler_caddie_planning.class.php");
require_once($class_path."/parameters.class.php");

switch($sub){
	case 'caddie':
		switch ($action) {
			case 'get_list':
			    print encoding_normalize::utf8_normalize(scheduler_caddie_planning::get_display_caddie_list($object_type));
				break;
			case 'get_actions':
			    print encoding_normalize::utf8_normalize(scheduler_caddie_planning::get_actions_selector($object_type));
				break;
			case 'get_action_form':
				$scheduler_caddie_planning = new scheduler_caddie_planning($id);
				print encoding_normalize::utf8_normalize($scheduler_caddie_planning->get_action_form($object_type, $sub_action));
				break;
			case 'get_proc_options':
			    $table = (!empty($table) ? $table : 'caddie_procs');
			    $hp = new parameters ($id, $table);
				print $hp->get_content_form();
				break;
		}
		break;
	case 'reporting':
		switch ($action) {
			case 'list':
				lists_controller::proceed_ajax($object_type);
				break;
		}
		break;
	default:
		break;
}
	