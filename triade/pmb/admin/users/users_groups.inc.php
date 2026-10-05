<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users_groups.inc.php,v 1.12 2021/02/19 12:44:46 dgoron Exp $

// gestion des groupes d'utilisateurs

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $msg, $id;

require_once($class_path.'/users_group.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");
require_once($class_path.'/event/events/event_users_group.class.php');

$id = intval($id);
switch($action) {
	case 'update':
		$users_group = new users_group($id);
		$users_group->set_properties_from_form();
		
		global $form_libelle;
		// verification validite des donnees fournies
		$q = $users_group->get_query_if_exists();
		$r = pmb_mysql_query($q);
		$nb = pmb_mysql_result($r, 0, 0);
		if ($nb > 0) {
			error_form_message($form_libelle.$msg['admin_usr_grp_lib_used']);
		} else {
			$users_group->save();
		}
		//Evenement publié
		$evt_handler = events_handler::get_instance();
		$event = new event_users_group("users_group", "save_form");
		$event->set_group_id($id);
		$evt_handler->send($event);
		
		print list_configuration_users_groups_ui::get_instance()->get_display_list();
		break;
	default:
		configuration_controller::set_model_class_name('users_group');
		configuration_controller::set_list_ui_class_name('list_configuration_users_groups_ui');
		configuration_controller::proceed($id);
		break;
	}
?>