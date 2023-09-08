<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bulletinage.inc.php,v 1.3 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $aff_alerte;

$temp_aff = alerte_bulletinage() ;
if ($temp_aff) $aff_alerte.= "<ul>".$msg["pointage_menu_pointage"].$temp_aff."</ul>" ;

function alerte_bulletinage() {
	global $msg;
	
	$alert = "";
	
	// comptage des abonnements à renouveler
	$query = "SELECT count(*) as total FROM abts_abts WHERE date_fin BETWEEN CURDATE() AND  DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
	$result = pmb_mysql_query($query);
	if ($result && pmb_mysql_result($result, 0, 0)) {
		$alert .= "<li><a href='./catalog.php?categ=serials&sub=pointage&id=0' target='_parent'>".$msg["abonnements_to_renew"]."</a></li>";
	}
	// comptage des abonnements dépassés
	$query = "SELECT count(*) as total FROM abts_abts WHERE date_fin < CURDATE()";
	$result = pmb_mysql_query($query);
	if ($result && pmb_mysql_result($result, 0, 0)) {
		$alert .= "<li><a href='./catalog.php?categ=serials&sub=pointage&id=0' target='_parent'>".$msg["abonnements_outdated"]."</a></li>";
	}
	return $alert;
}

