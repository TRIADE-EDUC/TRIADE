<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: family.inc.php,v 1.2 2015-01-15 11:03:48 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/nomenclature_family_admin.class.php");

switch($action) {
	case 'form':
		$nomenclature_family=new nomenclature_family_admin($id);
		print $nomenclature_family->get_form();
		break;
	case 'save':
		$nomenclature_family=new nomenclature_family_admin($id);
		print $nomenclature_family->save();
		$nomenclature_families=new nomenclature_family_admins();
		print $nomenclature_families->get_list();
		break;
	case 'delete':
		$nomenclature_family=new nomenclature_family_admin($id);
		print $nomenclature_family->delete();
		$nomenclature_families=new nomenclature_family_admins();
		print $nomenclature_families->get_list();
		break;
	case "up":		
		$nomenclature_families=new nomenclature_family_admins();
		$nomenclature_families->order_up($id);
		print $nomenclature_families->get_list();
		break;
	case "down":
		$nomenclature_families=new nomenclature_family_admins();
		$nomenclature_families->order_down($id);
		print $nomenclature_families->get_list();
		break;
	case 'musicstand_list': 
		$musicstand_family=new nomenclature_family_musicstand_admins($id);
		print $musicstand_family->get_list();
		break;
	case 'musicstand_form': 
		$nomenclature_family=new nomenclature_family_admin($id);
		print $nomenclature_family->get_musicstand_form($id_musicstand);
		break;
	case 'musicstand_save':
		$nomenclature_family=new nomenclature_family_admin($id);
		print $nomenclature_family->musicstand_save($id_musicstand);
		$musicstand_family=new nomenclature_family_musicstand_admins($id);
		print $musicstand_family->get_list();
		break;
	case 'musicstand_delete':
		$nomenclature_family=new nomenclature_family_admin($id);
		print $nomenclature_family->musicstand_delete($id_musicstand);
		$nomenclature_families=new nomenclature_family_admins();
		print $nomenclature_families->get_list();
		break;
	case "musicstand_up":
		$musicstand_family=new nomenclature_family_musicstand_admins($id);
		$musicstand_family->order_up($id_musicstand);
		print $musicstand_family->get_list();
		break;
	case "musicstand_down":		
		$musicstand_family=new nomenclature_family_musicstand_admins($id);
		$musicstand_family->order_down($id_musicstand);
		print $musicstand_family->get_list();		
		break;		
	default:
		$nomenclature_families=new nomenclature_family_admins();
		print $nomenclature_families->get_list();
		break;
}
