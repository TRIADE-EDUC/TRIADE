<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_main.inc.php,v 1.14 2022/02/23 14:47:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $msg, $serial_header;
global $bul_id, $serial_id, $explnum_id, $f_explnum_id, $f_bulletin;

// page de switch gestion du bulletinage périodiques

switch($action) {
	case 'view':
		include('./catalog/serials/bulletinage/bul_view.inc.php');
		break;
	case 'bul_form':
		require_once($class_path."/entities/entities_bulletinage_controller.class.php");
		$entities_bulletinage_controller = new entities_bulletinage_controller($bul_id);
		$entities_bulletinage_controller->set_serial_id($serial_id);
		$entities_bulletinage_controller->set_action('form');
		$entities_bulletinage_controller->proceed();
		break;
	case 'bul_duplicate':
		require_once($class_path."/entities/entities_bulletinage_controller.class.php");
		$entities_bulletinage_controller = new entities_bulletinage_controller($bul_id);
		$entities_bulletinage_controller->set_serial_id($serial_id);
		$entities_bulletinage_controller->set_action('duplicate');
		$entities_bulletinage_controller->proceed();
		break;
	case 'bul_del_notice':
		include('./catalog/serials/bulletinage/bul_del_notice.inc.php');
		break;
	case 'dupl_expl':
	case 'expl_form':
		include('./catalog/serials/bulletinage/expl/bul_expl_form.inc.php');
		break;
	case 'expl_update':
		include('./catalog/serials/bulletinage/expl/bul_expl_update.inc.php');
		break;
	case 'expl_delete':
		include('./catalog/serials/bulletinage/expl/bul_expl_delete.inc.php');
		break;
	case 'update':
		include('./catalog/serials/bulletinage/bul_update.inc.php');
		break;
	case 'delete': 
		include('./catalog/serials/bulletinage/bul_delete.inc.php');
		break;
	case 'explnum_form':
		include('./catalog/serials/bulletinage/explnum/bul_explnum_form.inc.php');
		break;
	case 'explnum_update':
		include('./catalog/serials/bulletinage/explnum/bul_explnum_update.inc.php');
		break;
	case 'explnum_delete':
		include('./catalog/serials/bulletinage/explnum/bul_explnum_delete.inc.php');
		break;
	case 'copy_isdone':
		include('./catalog/serials/bulletinage/copy_isdone.inc.php');
		break;
	case 'bul_move':
	    include('./catalog/serials/bulletinage/bul_move.inc.php');
	    break;
	default:
		echo "case default -> à traiter (retour vers info périodique ou accueil périodiques)";
		break;
}
?>