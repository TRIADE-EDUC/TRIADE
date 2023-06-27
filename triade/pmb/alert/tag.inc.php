<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tag.inc.php,v 1.6 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte, $msg;

$temp_aff = alerte_tag() . alerte_avis() ;

if ($temp_aff) $aff_alerte .= "<ul>".$msg["alerte_avis_tag"].$temp_aff."</ul>" ;
  
function alerte_tag () {
global $dbh ;
global $msg;
global $opac_allow_add_tag ;

if (!$opac_allow_add_tag) return "";			
// comptage des tags à valider
$sql = " SELECT 1 FROM tags limit 1";
$req = pmb_mysql_query($sql) or die ($msg["err_sql"]."<br />".$sql."<br />".pmb_mysql_error());
$nb_limite = pmb_mysql_num_rows($req) ;
if (!$nb_limite) return "" ;
	else return "<li><a href='./catalog.php?categ=tags' target='_parent'>$msg[alerte_tag_a_valider]</a></li>" ;
}

function alerte_avis () {
global $dbh ;
global $msg; 
				
global $opac_avis_allow ;

if (!$opac_avis_allow) return "";			
// comptage des avis à valider
$sql = " SELECT 1 FROM avis where valide=0 limit 1";
$req = pmb_mysql_query($sql) or die ($msg["err_sql"]."<br />".$sql."<br />".pmb_mysql_error());
$nb_depasse = pmb_mysql_num_rows($req) ;
if (!$nb_depasse) return "" ;
	else return "<li><a href='./catalog.php?categ=avis' target='_parent'>$msg[alerte_avis_a_valider]</a></li>" ;
}

