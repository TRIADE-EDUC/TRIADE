<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: profil.inc.php,v 1.2 2017-02-20 19:04:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($id_profil)) $id_profil = 0;

require_once($class_path."/harvest_profil_import.class.php");

switch($action) {
	case 'form':
		$harvest=new harvest_profil_import($id_profil);
		print $harvest->get_form();
	break;
	case 'save':
		$harvest=new harvest_profil_import($id_profil);
		$data['name']=$name;
		$data['num_harvest']=$num_harvest;
		print $harvest->save($data);
		$harvests=new harvest_profil_imports();
		print $harvests->get_list();
	break;	
	case 'delete':
		$harvest=new harvest_profil_import($id_profil);
		print $harvest->delete();
		$harvests=new harvest_profil_imports();
		print $harvests->get_list();
	break;		

	default:
		$harvests=new harvest_profil_imports();
		print $harvests->get_list();
	break;
}
