<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: formation.inc.php,v 1.2 2015-01-23 14:53:26 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/nomenclature_formation_admin.class.php");

switch($action) {
	case 'form':
		$nomenclature_formation=new nomenclature_formation_admin($id);
		print $nomenclature_formation->get_form();
		break;
	case 'save':
		$nomenclature_formation=new nomenclature_formation_admin($id);
		print $nomenclature_formation->save();
		$nomenclature_formation=new nomenclature_formation_admins();
		print $nomenclature_formation->get_list();
		break;
	case 'delete':
		$nomenclature_formation=new nomenclature_formation_admin($id);
		print $nomenclature_formation->delete();
		$nomenclature_formation=new nomenclature_formation_admins();
		print $nomenclature_formation->get_list();
		break;
	case "up":		
		$nomenclature_formation=new nomenclature_formation_admins();
		$nomenclature_formation->order_up($id);
		print $nomenclature_formation->get_list();
		break;
	case "down":
		$nomenclature_formation=new nomenclature_formation_admins();
		$nomenclature_formation->order_down($id);
		print $nomenclature_formation->get_list();
		break;
	case 'type_list': 
		$type_formation=new nomenclature_formation_type_admins($id);
		print $type_formation->get_list();
		break;
	case 'type_form': 
		$nomenclature_formation=new nomenclature_formation_admin($id);
		print $nomenclature_formation->get_type_form($id_type);
		break;
	case 'type_save':
		$nomenclature_formation=new nomenclature_formation_admin($id);
		print $nomenclature_formation->type_save($id_type);
		$type_formation=new nomenclature_formation_type_admins($id);
		print $type_formation->get_list();
		break;
	case 'type_delete':
		$nomenclature_formation=new nomenclature_formation_admin($id);
		print $nomenclature_formation->type_delete($id_type);
		$type_formation=new nomenclature_formation_type_admins($id);
		print $type_formation->get_list();
		break;
	case "type_up":
		$type_formation=new nomenclature_formation_type_admins($id);
		$type_formation->order_up($id_type);
		print $type_formation->get_list();
		break;
	case "type_down":		
		$type_formation=new nomenclature_formation_type_admins($id);
		$type_formation->order_down($id_type);
		print $type_formation->get_list();		
		break;		
	default:
		$nomenclature_formation=new nomenclature_formation_admins();
		print $nomenclature_formation->get_list();
		break;
}
