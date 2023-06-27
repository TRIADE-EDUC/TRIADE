<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_main.inc.php,v 1.12 2016-05-06 12:44:27 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch gestion du bulletinage périodiques

switch($action) {
	case 'view':
		include('./catalog/serials/bulletinage/bul_view.inc.php');
		break;
	case 'bul_form':
		include('./catalog/serials/bulletinage/bul_form.inc.php');
		break;
	case 'bul_duplicate':
		include('./catalog/serials/bulletinage/bul_duplicate.inc.php');
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
	default:
		echo "case default -> à traiter (retour vers info périodique ou accueil périodiques)";
		break;
}
?>