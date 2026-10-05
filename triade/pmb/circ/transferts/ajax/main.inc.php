<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.9 2020/11/05 10:22:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/circ/transferts/affichage.inc.php");
require_once($class_path.'/encoding_normalize.class.php');

switch ($action) {

	case "date_retour":
		//permet de changer la date retour d'un transfert
		include("./circ/transferts/ajax/chg_date_retour.inc.php");
		break;

	case "change_loc":
		//annule le transfert
		//et change la localisation d'un exemplaire
		include("./circ/transferts/ajax/retour_change_loc.inc.php");
		break;
		
	case "gen_transfert":
		//annule le changement de localisation
		//et genere un transfert
		include("./circ/transferts/ajax/retour_gen_transfert.inc.php");
		break;

	case "loc_retrait":
		//change la localisation de retrait d'une resa
		include("./circ/transferts/ajax/chg_loc_retrait.inc.php");
		break;
	
	case "change_section":
		//annule le transfert
		//et change la localisation d'un exemplaire
		include("./circ/transferts/ajax/chg_section_retour.inc.php");
		break;
	
	case "list":
		lists_controller::proceed_ajax($object_type, 'transferts');
		break;
	default:
		//par defaut on renvoie une erreur
		ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
		break;		
		
}

?>