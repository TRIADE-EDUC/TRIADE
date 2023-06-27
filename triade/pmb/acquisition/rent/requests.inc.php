<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: requests.inc.php,v 1.2 2019-05-28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $action, $id_bibli, $id;

require_once($class_path.'/entites.class.php');
require_once($class_path.'/rent/rent_requests.class.php');
require_once($class_path.'/rent/rent_request.class.php');

//Traitement des actions
print "<h1>".htmlentities($msg['acquisition_rent_ges'],ENT_QUOTES, $charset)."&nbsp;:&nbsp;".htmlentities($msg['acquisition_rent_requests'],ENT_QUOTES, $charset)."</h1>";

switch($action) {
	case 'list' :
		entites::setSessionBibliId($id_bibli);
		$rent_requests = new rent_requests();
		print $rent_requests->get_display_list();
		break;
	case 'edit' :
		$rent_request = new rent_request($id);
		print $rent_request->get_form();
		break;
	case 'update' :
		$rent_request = new rent_request($id);
		$rent_request->set_properties_from_form();
		$rent_request->save();
		$rent_requests = new rent_requests();
		print $rent_requests->get_display_list();
		break;
	case 'delete' :
		$rent_request = new rent_request($id);
		$deleted = $rent_request->delete();
		$rent_requests = new rent_requests();
		$rent_requests->set_messages($deleted['msg_to_display']);
		print $rent_requests->get_display_list();
		break;
	default:
		print entites::show_list_biblio('get_display_list', 'rent_requests');	
		break;
}
