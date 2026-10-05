<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: diffusion_auto.php,v 1.7 2023/12/08 08:48:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$base_path = "../.";
$base_auth = "DSI_AUTH";  
$base_noheader = 1;
$base_nobody = 1;  
$base_nodojo = 1;  
$clean_pret_tmp=1;

require_once($base_path."/includes/init.inc.php");

global $class_path;
require_once($class_path."/bannette.class.php");

diff_all_bannettes_full_auto();

function diff_all_bannettes_full_auto() {
	$requete = "SELECT id_bannette, proprio_bannette FROM bannettes WHERE (DATE_ADD(date_last_envoi, INTERVAL periodicite DAY) <= sysdate()) and bannette_auto=1 ";
	$res = pmb_mysql_query($requete);
	print "<table role='presentation'>";		
	while(($bann=pmb_mysql_fetch_object($res))) {
		$bannette = new bannette($bann->id_bannette);
		if(!$bannette->limite_type)$bannette->vider();
		$bannette->remplir();
		$bannette->purger();
		print"<tr>";
		print"<td>".$bannette->nom_bannette."</td>";
		print"<td>".$bannette->aff_date_last_envoi."</td>";
		print"<td>".$bannette->diffuser()."</td>";
		print"</tr>";		
		
	}	
	print"</table>";
	
}
