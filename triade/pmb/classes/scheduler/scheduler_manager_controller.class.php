<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_manager_controller.class.php,v 1.1 2023/03/28 13:02:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/scheduler/scheduler_tasks.class.php");
require_once($class_path."/scheduler/scheduler_tasks_type.class.php");

class scheduler_manager_controller extends lists_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = '';

	public static function proceed($id=0) {
		global $action, $subaction;
		global $type_id;
		
		$id = intval($id);
		$type_id = intval($type_id);
		switch ($action) {
			case "type_edit":
				$scheduler_tasks_type=new scheduler_tasks_type($id);
				print $scheduler_tasks_type->get_form();
				break;
			case "type_update":
				$scheduler_tasks_type=new scheduler_tasks_type($id);
				$scheduler_tasks_type->set_properties_from_form();
				$scheduler_tasks_type->save_global_properties();
				$scheduler_tasks = new scheduler_tasks();
				print $scheduler_tasks->get_display_list();
				break;
			case "edit":
				$name = scheduler_tasks::get_catalog_element($type_id, 'NAME');
				$class_name = $name."_planning";
				$scheduler_planning = new $class_name($id);
				$scheduler_planning->set_id_type($type_id);
				switch ($subaction) {
					case "change":
						print $scheduler_planning->get_form();
						break;
					case "save":
						$scheduler_planning->save_property_form();
						$scheduler_tasks = new scheduler_tasks();
						print $scheduler_tasks->get_display_list();
						break;
					default :
						print $scheduler_planning->get_form();
				}
				break;
			case "delete":
				$name = scheduler_tasks::get_catalog_element($type_id, 'NAME');
				$class_name = $name."_planning";
				$scheduler_planning = new $class_name($id);
				$scheduler_planning->set_id_type($type_id);
				print $scheduler_planning->delete();
				break;
			case "duplicate":
				$name = scheduler_tasks::get_catalog_element($type_id, 'NAME');
				$class_name = $name."_planning";
				$scheduler_planning = new $class_name($id);
				$scheduler_planning->set_id_type($type_id);
				print $scheduler_planning->get_form();
				break;
			default:
				$scheduler_tasks = new scheduler_tasks();
				print $scheduler_tasks->get_display_list();
				break;
		}
	}
}