<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations8.inc.php,v 1.5 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la taille d'un paquet de notices
$lot = SERIE_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);
print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_clean_blob"], ENT_QUOTES, $charset)."</h2>";

$affected = 0;

for($i=1;$i<=2;$i++){
	if($i==1){
		$table='logopac';
	}else{
		$table='statopac';
	}
	$query = "SELECT column_type FROM information_schema.columns WHERE table_schema = '".DATA_BASE."' AND table_name = '".$table."' AND column_name = 'empr_expl'";
	$res = pmb_mysql_query($query);
	$row = pmb_mysql_fetch_object($res);
	
	if ($row->column_type == 'blob') {
		$query = pmb_mysql_query("ALTER TABLE ".$table." CHANGE empr_expl empr_expl MEDIUMBLOB NOT NULL");
		$affected += pmb_mysql_affected_rows();
	}
}

$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_suppr_relations"], ENT_QUOTES, $charset)." : ";
$v_state .= $affected." ".htmlentities($msg["nettoyage_res_suppr_blob"], ENT_QUOTES, $charset);

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '9');
