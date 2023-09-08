<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_circ_state.inc.php,v 1.2 2016-05-10 15:32:19 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc_state.class.php");

$serialcirc_state= new serialcirc_state();
$serialcirc_state->get_filters_from_form();
switch ($dest) {
	case 'TABLEAU':
		$serialcirc_state->export_list_tableau();
		break;
	case 'TABLEAUHTML':
		print $serialcirc_state->export_list_tableauhtml();
		break;
	default :
		print $serialcirc_state->get_list();
		break;
}