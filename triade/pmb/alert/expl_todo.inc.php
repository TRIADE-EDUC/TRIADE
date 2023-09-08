<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_todo.inc.php,v 1.4 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte, $msg;

$temp_aff = expl_retour_todo();

if ($temp_aff) $aff_alerte .= "<ul>".$msg["alert_circ_retour"].$temp_aff."</ul>";
  
function expl_retour_todo () {
	global $msg;
	global $deflt_docs_location;
	
	if(!$deflt_docs_location)	return"";
	$sql = "SELECT expl_id FROM exemplaires where expl_retloc='$deflt_docs_location' limit 1";
	$req = pmb_mysql_query($sql) or die ($msg["err_sql"]."<br />".$sql."<br />".pmb_mysql_error());
	$nb = pmb_mysql_num_rows($req) ;
	if (!$nb) return "" ;
	else return "<li><a href='./circ.php?categ=ret_todo' target='_parent'>".$msg["alert_circ_retour_todo"]."</a></li>" ;
}

