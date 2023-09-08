<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: manager.inc.php,v 1.10 2017-08-11 06:12:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($act)) $act = '';

require_once($class_path."/scheduler/scheduler_tasks.class.php");
require_once($class_path."/scheduler/scheduler_tasks_type.class.php");
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/scheduler/scheduler_task_calendar.class.php");

switch ($act)  {
	case "modif":
		$type_task_id += 0;
		if ($type_task_id) {
			$scheduler_tasks_type=new scheduler_tasks_type($type_task_id);
			print $scheduler_tasks_type->get_form();
		}
		break;
	case "update":
		$scheduler_tasks_type=new scheduler_tasks_type($type_task_id);
		$scheduler_tasks_type->set_properties_from_form();
		$scheduler_tasks_type->save_global_properties();
		$scheduler_tasks = new scheduler_tasks();
		print $scheduler_tasks->get_display_list();
		break;
	case "task":
		$name = scheduler_tasks::get_catalog_element($type_task_id, 'NAME');
		$path = scheduler_tasks::get_catalog_element($type_task_id, 'PATH');
		require_once ($base_path.'/admin/planificateur/'.$path.'/'.$name.'_planning.class.php');
		$class_name = $name."_planning";
		$scheduler_planning = new $class_name($planificateur_id);
		$scheduler_planning->set_id_type($type_task_id);
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
	case "task_del":
		$name = scheduler_tasks::get_catalog_element($type_task_id, 'NAME');
		$path = scheduler_tasks::get_catalog_element($type_task_id, 'PATH');
		require_once ($base_path.'/admin/planificateur/'.$path.'/'.$name.'_planning.class.php');
		$class_name = $name."_planning";
		$scheduler_planning = new $class_name($planificateur_id);
		$scheduler_planning->set_id_type($type_task_id);
		print $scheduler_planning->delete();
		break;
	case "task_duplicate":
		$name = scheduler_tasks::get_catalog_element($type_task_id, 'NAME');
		$path = scheduler_tasks::get_catalog_element($type_task_id, 'PATH');
		require_once ($base_path.'/admin/planificateur/'.$path.'/'.$name.'_planning.class.php');
		$class_name = $name."_planning";
		$scheduler_planning = new $class_name($planificateur_id);
		$scheduler_planning->set_id_type($type_task_id);
		print $scheduler_planning->get_form();
		break;
	default:
		$scheduler_tasks = new scheduler_tasks();
		print $scheduler_tasks->get_display_list();
		break;
}