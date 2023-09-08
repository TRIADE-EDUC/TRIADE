<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: invoices.inc.php,v 1.5 2019-05-28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $action, $id_bibli, $id, $ids;

require_once($class_path.'/entites.class.php');
require_once($class_path.'/rent/rent_invoices.class.php');
require_once($class_path.'/rent/rent_invoice.class.php');

//Traitement des actions
print "<h1>".htmlentities($msg['acquisition_rent_ges'],ENT_QUOTES, $charset)."&nbsp;:&nbsp;".htmlentities($msg['acquisition_rent_invoices'],ENT_QUOTES, $charset)."</h1>";

switch($action) {
	case 'list' :
		entites::setSessionBibliId($id_bibli);
		$rent_invoices = new rent_invoices();
		print $rent_invoices->get_display_list();
		break;
	case 'edit' :
		$rent_invoice = new rent_invoice($id);
		print $rent_invoice->get_form();
		break;
	case 'update' :
		$rent_invoice = new rent_invoice($id);
		$rent_invoice->set_properties_from_form();
		$rent_invoice->save();
		$rent_invoices = new rent_invoices();
		print $rent_invoices->get_display_list();
		break;
	case 'delete' :
		$rent_invoice = new rent_invoice($id);
		$rent_invoice->delete();
		$rent_invoices = new rent_invoices();
		print $rent_invoices->get_display_list();
		break;
	case 'create_from_accounts' :
		$created = false;
		if($ids) {
			$accounts = explode(',', $ids);
			$created = rent_invoices::create_from_accounts($accounts);
		}
		$rent_invoices = new rent_invoices();
		if(!$created) {
			$rent_invoices->set_messages($msg['acquisition_account_cant_invoice_create'].'<br /><br />');
		}
		print $rent_invoices->get_display_list();
		break;
	case 'validate' :
		if($ids) {
			$invoices = explode(',', $ids);
			rent_invoices::validate($invoices);
		}
		$rent_invoices = new rent_invoices();
		print $rent_invoices->get_display_list();
		break;
	default:
		print entites::show_list_biblio('get_display_list', 'rent_invoices');	
		break;
}
