<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recouvr.inc.php,v 1.5 2021/04/21 20:49:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Gestion des recouvrements
global $act;
switch ($act) {
	case "recouvr_reader":
		require_once("recouvr_reader.inc.php");
		break;
	case "recouvr_liste":
	default:
		require_once("recouvr_liste.inc.php");
		break;
}
?>