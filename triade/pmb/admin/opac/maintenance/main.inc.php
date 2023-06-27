<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2016-08-10 08:54:34 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/maintenance_page.class.php');

$maintenance_page = new maintenance_page();

switch($action) {
	case 'save' :
		$maintenance_page->get_values_from_form();
		$maintenance_page->save();
		print display_notification($msg['admin_opac_maintenance_save_success']);
		print $admin_layout;
		print $maintenance_page->get_form();
		break;
	default :
		$maintenance_page->fetch_data();
		print $admin_layout;
		print $maintenance_page->get_form();
		break;
}


