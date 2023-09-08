<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: accounts.inc.php,v 1.4 2019-05-28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $action, $id_bibli, $id;

require_once($class_path.'/entites.class.php');
require_once($class_path.'/rent/rent_accounts.class.php');
require_once($class_path.'/rent/rent_account.class.php');

//Traitement des actions
print "<h1>".htmlentities($msg['acquisition_rent_ges'],ENT_QUOTES, $charset)."&nbsp;:&nbsp;".htmlentities($msg['acquisition_rent_accounts'],ENT_QUOTES, $charset)."</h1>";

switch($action) {
	case 'list' :
		entites::setSessionBibliId($id_bibli);
		$rent_accounts = new rent_accounts();
		print $rent_accounts->get_display_list();
		break;
	case 'edit' :
		$rent_account = new rent_account($id);
		print $rent_account->get_form();
		break;
	case 'update' :
		$rent_account = new rent_account($id);
		$rent_account->set_properties_from_form();
		$rent_account->save();
		$rent_accounts = new rent_accounts();
		print $rent_accounts->get_display_list();
		break;
	case 'delete' :
		$rent_account = new rent_account($id);
		$deleted = $rent_account->delete();
		$rent_accounts = new rent_accounts();
		$rent_accounts->set_messages($deleted['msg_to_display']);
		print $rent_accounts->get_display_list();
		break;
	default:
		print entites::show_list_biblio('get_display_list', 'rent_accounts');	
		break;
}
