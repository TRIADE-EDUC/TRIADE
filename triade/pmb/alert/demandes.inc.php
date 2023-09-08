<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.inc.php,v 1.3 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $aff_alerte;

$temp_aff = alerte_demandes() ;
if ($temp_aff) $aff_alerte.= "<ul>".$msg["alerte_demandes"].$temp_aff."</ul>" ;

function alerte_demandes () {
	global $msg;
				
	// comptage des demandes à valider
	$sql = " SELECT 1 FROM demandes where etat_demande=1 limit 1";
	$req = pmb_mysql_query($sql) or die ($msg["err_sql"]."<br />".$sql."<br />".pmb_mysql_error());
	$nb_limite = pmb_mysql_num_rows($req) ;
	if (!$nb_limite) return "" ;
	else return "<li><a href='./demandes.php?categ=list&idetat=1' target='_parent'>$msg[alerte_demandes_traiter]</a></li>" ;
}

