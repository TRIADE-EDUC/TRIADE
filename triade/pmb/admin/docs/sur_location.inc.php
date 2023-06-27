<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sur_location.inc.php,v 1.1 2011-04-20 06:27:21 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des sur-localisations
require_once("$class_path/sur_location.class.php");
$sur_loc= new sur_location($id);

switch($action) {
	case 'update':
		$sur_loc->update();
		print $sur_loc->do_list();
		break;
	case 'add':
		print $sur_loc->do_form();
		break;	
	case 'modif':
		print $sur_loc->do_form();	
		break;
	case 'del':
		$sur_loc->delete();	
		print $sur_loc->do_list();
		break;
	default:
		print $sur_loc->do_list();
	break;
}
