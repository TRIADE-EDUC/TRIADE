<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13 2021/02/08 10:30:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'entite':
		include('./admin/acquisition/entite.inc.php');
		break;
	case 'compta':
		include('./admin/acquisition/comptabilite.inc.php');
		break;
	case 'type':
		include('./admin/acquisition/types_produits.inc.php');
		break;
	case 'tva':
		include('./admin/acquisition/tva_achats.inc.php');
		break;
	case 'frais':
		include('./admin/acquisition/frais.inc.php');
		break;
	case 'mode':
		include('./admin/acquisition/modes_paiements.inc.php');
		break;	
	case 'budget':
		include('./admin/acquisition/budgets.inc.php');
		break;
	case 'categ':
		include('./admin/acquisition/suggestions_categ.inc.php');
		break;
	case 'src':
		include('./admin/acquisition/suggestions_src.inc.php');
		break;
	case 'lgstat':
		include('./admin/acquisition/lgstat.inc.php');
		break;
	case 'pricing_systems':
		include('./admin/acquisition/pricing_systems.inc.php');
		break;
	case 'account_types':
		include('./admin/acquisition/account_types.inc.php');
		break;
	case 'thresholds':
		include('./admin/acquisition/thresholds.inc.php');
		break;
	default:
		include("$include_path/messages/help/$lang/admin_acquisitions.txt");
		break;
}
?>
