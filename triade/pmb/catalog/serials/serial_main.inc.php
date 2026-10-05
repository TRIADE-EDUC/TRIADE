<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_main.inc.php,v 1.30 2022/02/23 14:47:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $database_window_title, $sub, $serial_header;
global $id,$serial_id, $bulletin_id, $bul_id, $explnum_id, $f_explnum_id, $f_notice;

// inclusion du template de gestion des périodiques
include("$include_path/templates/serials.tpl.php");

// classes particulières à ce module
require_once("$class_path/serials.class.php");
require_once($class_path."/collstate.class.php");
require_once("./catalog/serials/serial_func.inc.php");
require_once("./catalog/serials/bulletinage/bul_func.inc.php");

echo window_title($database_window_title.$msg[771].$msg[1003].$msg[1001]);

switch($sub) {
	case 'serial_form':
		require_once($class_path."/entities/entities_serials_controller.class.php");
		
		$entities_serials_controller = new entities_serials_controller($id);
		$entities_serials_controller->set_action('form');
		$entities_serials_controller->proceed();
		break;
	case 'update':
		include('./catalog/serials/serial_update.inc.php');
		break;
	case 'delete':
		include('./catalog/serials/serial_delete.inc.php');
		break;
	case 'search':
		include('./catalog/serials/serial_search.inc.php');
		break;
	case 'view':
		include('./catalog/serials/serial_view.inc.php');
		break;
	case 'bulletinage':
		include('./catalog/serials/bulletinage/bul_main.inc.php');
		break;
	case 'analysis':
		include('./catalog/serials/analysis/analysis_main.inc.php');
		break;
	case 'serial_replace':
		require_once($class_path."/entities/entities_serials_controller.class.php");
		
		$entities_serials_controller = new entities_serials_controller($serial_id);
		$entities_serials_controller->set_action('replace');
		$entities_serials_controller->proceed();
		break;
	case 'serial_duplicate':
		// routine de copie
		$serial = new serial($serial_id);
		$serial->serial_id=0 ;
		$serial->id=0;
		$serial->code="" ;
		$serial->duplicate_from_id = $serial_id ; 
		print pmb_bidi($serial->do_form()) ;
		break;
	case 'bulletin_replace':
		include('./catalog/serials/bulletinage/bul_replace.inc.php');
		break;
	case 'modele':
		include('./catalog/serials/modele/modele_main.inc.php');//TODO
		break;
	case 'abon':
		include('./catalog/serials/abonnement/abonnement_main.inc.php');//TODO
		break;
	case 'pointage':
		include('./catalog/serials/pointage/pointage_main.inc.php');//TODO
		break;
	case 'explnum_form':
		include('./catalog/serials/explnum/serial_explnum_form.inc.php');
		break;
	case 'explnum_update':
		include('./catalog/serials/explnum/serial_explnum_update.inc.php');
		break;
	case 'explnum_delete':
		include('./catalog/serials/explnum/serial_explnum_delete.inc.php');
		break;
	case 'collstate_form':
		$collstate = new collstate($id,$serial_id);
		echo $collstate->do_form();
		break;
	case 'collstate_update':
		$collstate = new collstate($id,$serial_id, $bulletin_id);
		$collstate->update_from_form();
		$view="collstate";
		$location=$location_id;
		include('./catalog/serials/serial_view.inc.php');
		break;
	case 'collstate_delete':
		$collstate = new collstate($id);
		$collstate->delete();
		$view="collstate";
		include('./catalog/serials/serial_view.inc.php');
		break;
	case 'abts_retard':
		include('./catalog/serials/abts_retard/abts_retard.inc.php');
		break;
	case 'circ_ask':
		include('./catalog/serials/serialcirc_ask/serialcirc_ask.inc.php');
		break;
	case 'collstate_bulletins_list':
		$collstate = new collstate($id, $serial_id, $bulletin_id);
		print $collstate->get_bulletins_list();
		break;
	default:
		echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["recherche"], $serial_header);
		echo $serial_access_form;
		break;
}

echo $serial_footer;
?>