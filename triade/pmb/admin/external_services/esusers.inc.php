<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: esusers.inc.php,v 1.5 2021/02/19 12:44:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
global $action, $id;

//Initialisation des classes
require_once($class_path."/external_services.class.php");
require_once($class_path."/external_services_esusers.class.php");
require_once($include_path."/templates/external_services.tpl.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_esusers_ui.class.php");

function list_users() {
	print list_configuration_external_services_esusers_ui::get_instance()->get_display_list();
}

function update_esuser_from_form() {
	global $msg,$id;
	global $esuser_username, $esuser_esgroup;
	if ($esuser_esgroup) {
		//Vérifions que le groupe existe
		if (!es_esgroup::id_exists($esuser_esgroup)) {
			print $msg['es_user_error_unknowngroup'];
			$the_user = new es_esuser();
			$the_user->set_properties_from_form();
			print $the_user->get_form();
			return false;
		}
	}
	if (!$id) {
		//Ajout d'un nouvel utilisateur
		if (!$esuser_username) {
			print $msg['es_user_error_emptyfield'];
			$the_user = new es_esuser();
			$the_user->set_properties_from_form();
			print $the_user->get_form();
			return false;
		}
		if (es_esuser::username_exists($esuser_username)) {
			print $msg['es_user_error_usernamealreadyexists'];
			$the_user = new es_esuser();
			$the_user->set_properties_from_form();
			print $the_user->get_form();
			return false;
		}
		$new_esuser = es_esuser::add_new();
		$new_esuser->set_properties_from_form();
		$new_esuser->commit_to_db(); 
	}
	else {
		$the_user = new es_esuser($id);
			if ($the_user->error) {
				return false;
		}
		$the_user->set_properties_from_form();
		$the_user->commit_to_db(); 
	}
	return true;
}

switch ($action) {
	case "add":
		$the_user = new es_esuser();
		print $the_user->get_form();
		break;
	case "edit":
		$the_user = new es_esuser($id);
		print $the_user->get_form();
		break;
	case "update":
		if (update_esuser_from_form())
			list_users();
		break;
	case "del":
		if ($id) {
			$the_user = new es_esuser($id);
			$the_user->delete();
		}
		list_users();
		break;
	default:
		list_users();
		break;
}

?>