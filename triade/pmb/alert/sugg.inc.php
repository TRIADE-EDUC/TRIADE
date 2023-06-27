<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sugg.inc.php,v 1.7 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte, $msg;

$temp_aff = alerte_sugg() ;
if ($temp_aff) $aff_alerte.= "<ul>".$msg["alerte_suggestion"].$temp_aff."</ul>" ;

function alerte_sugg() {
	global $msg;
	global $opac_show_suggest;
	global $acquisition_sugg_localises, $deflt_docs_location;
	
	$alert = "";
	
	if ($opac_show_suggest) {		
		// comptage des tags à valider
		$sql = " SELECT 1 FROM suggestions where statut=1 ".($acquisition_sugg_localises?" AND sugg_location=".$deflt_docs_location:"")." limit 1";
		$res = pmb_mysql_query($sql);
		if ($res && pmb_mysql_num_rows($res)) {
			$alert = "<li><a href='./acquisition.php?categ=sug&action=list&statut=1' target='_parent'>".$msg["alerte_suggestion_traiter"]."</a></li>";
		}
	}
	
	return $alert;
}

