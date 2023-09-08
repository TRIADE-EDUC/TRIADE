<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4 2018-11-08 13:01:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/admin/planificateur/caddie/scheduler_caddie_planning.class.php");
require_once($class_path."/parameters.class.php");

switch($sub){
	case 'caddie':
		switch ($action) {
			case 'get_list':
				print scheduler_caddie_planning::get_display_caddie_list($object_type);
				break;
			case 'get_actions':
				print scheduler_caddie_planning::get_actions_selector($object_type);
				break;
			case 'get_action_form':
				$scheduler_caddie_planning = new scheduler_caddie_planning($id);
				print $scheduler_caddie_planning->get_action_form($object_type, $sub_action);
				break;
			case 'get_proc_options':
				$hp = new parameters ($id);
				print $hp->get_content_form();
				break;
		}
		break;
	case 'reporting':
		switch ($action) {
			case 'list':
				require_once($class_path.'/list/lists_controller.class.php');
				lists_controller::proceed_ajax($object_type);
				break;
		}
		break;
	default:
		break;
}
	