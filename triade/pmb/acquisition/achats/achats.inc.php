<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: achats.inc.php,v 1.9 2019/05/28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;

switch($sub) {
	case 'devi':
		include('./acquisition/achats/devis/devis.inc.php');
		break;
	case 'cmde':
		include('./acquisition/achats/commandes/commandes.inc.php');
		break;
	case 'livr':
		include('./acquisition/achats/livraisons/livraisons.inc.php');
		break;
	case 'recept':
		include('./acquisition/achats/receptions/receptions.inc.php');
		break;
	case 'fact':
		include('./acquisition/achats/factures/factures.inc.php');
		break;
	case 'fourn':
		include('./acquisition/achats/fournisseurs/fournisseurs.inc.php');
		break;
	case 'bud':
		include('./acquisition/achats/budgets/budgets.inc.php');
		break;
	default:
		include('./acquisition/achats/commandes/commandes.inc.php');
		break;
}
?>
