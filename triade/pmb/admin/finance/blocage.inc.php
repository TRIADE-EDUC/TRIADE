<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: blocage.inc.php,v 1.5 2021/02/26 13:19:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $action, $msg;

require_once("$include_path/templates/finance.tpl.php");

switch ($action) {
	case 'update':
	case 'save':
		//Mise à jour !!
		list_configuration_finance_blocage_ui::get_instance()->save();
		print "
				<script type='text/javascript'>
					window.location.href='".list_configuration_finance_blocage_ui::get_instance()->get_controller_url_base()."';
				</script>";
		break;
	case 'modif':
		print list_configuration_finance_blocage_ui::get_instance()->get_display_list();
		break;
	default:
		//Gestion simple
		print list_configuration_finance_blocage_ui::get_instance()->get_display_list();
		print "<div class='row'></div>
		<div class='row'><input type='button' class='bouton' value='".$msg["finance_amende_modifier"]."' onClick=\"document.location='./admin.php?categ=finance&sub=blocage&action=modif';\"></div>";
		break;
}

?>