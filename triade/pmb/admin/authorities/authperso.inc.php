<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.5 2021/03/05 08:38:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $base_path, $auth_action, $id_authperso;

require_once($class_path."/authperso_admin.class.php");

switch($auth_action) {
	case 'form':
		$authperso=new authperso_admin($id_authperso);
		print $authperso->get_form();
		break;
	case 'save':
		$authperso=new authperso_admin($id_authperso);
		$authperso->set_properties_from_form();
		print $authperso->save();
		print list_configuration_authorities_authperso_ui::get_instance()->get_display_list();
		break;	
	case 'delete':
		$deleted = authperso_admin::delete($id_authperso);
		if($deleted) {
			print list_configuration_authorities_authperso_ui::get_instance()->get_display_list();
		} else {
			pmb_error::get_instance('authperso_admin')->display(1, $base_path."/admin.php?categ=authorities&sub=authperso");
		}
		break;			
	case 'edition': // gestion des champs persos (liste ,cration, edition, suppression...
		$authperso=new authperso_admin($id_authperso);		
		$authperso->fields_edition();		
		break;
	case 'update_global_index':
		print authperso::update_all_global_index($id_authperso);
		print list_configuration_authorities_authperso_ui::get_instance()->get_display_list();
		break;	
	default:
		print list_configuration_authorities_authperso_ui::get_instance()->get_display_list();
		break;
}
