<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alert.inc.php,v 1.13 2019-05-29 12:12:29 btafforeau Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $current_alert, $pmb_transferts_actif, $pmb_pnb_param_login, $charset, $aff_alerte;

// définition du minimum nécéssaire                         
$base_auth = "CIRCULATION_AUTH|CATALOGAGE_AUTH|AUTORITES_AUTH|ADMINISTRATION_AUTH|EDIT_AUTH";  
$base_title = "\$msg[5]";
require_once ("$base_path/includes/init.inc.php");  

$aff_alerte = '';
require_once("$base_path/alert/message.inc.php");
if ($current_alert=="circ") {
	require_once("$base_path/alert/resa.inc.php");
	require_once("$base_path/alert/expl_todo.inc.php");			
	require_once("$base_path/alert/empr.inc.php");
	//pour les alertes de transferts
	if ($pmb_transferts_actif && (SESSrights & TRANSFERTS_AUTH))
		require_once ("$base_path/alert/transferts.inc.php");
}
if ($current_alert=="catalog") {
	require_once("$base_path/alert/tag.inc.php");
	require_once("$base_path/alert/sugg.inc.php");
	require_once("$base_path/alert/serialcirc.inc.php");
	require_once("$base_path/alert/bulletinage.inc.php");
	if($pmb_pnb_param_login) {
	   require_once($base_path . "/alert/pnb.inc.php");
	}
}

if ($current_alert=="acquisition") {
	require_once("$base_path/alert/sugg.inc.php");
}
if ($current_alert=="demandes") {
	require_once("$base_path/alert/demandes.inc.php");
}

//on reprend le format de la réponse. VIVE LE JSON !
if (trim($aff_alerte)) {
	if($charset!="utf-8"){
		$aff_alerte = utf8_encode($aff_alerte);
	}
	$response = array(
		'state' => 1,
		'module' => $current_alert,
		'separator' => "<hr class='alert_separator'>",
		'html' => $aff_alerte
	);
} else {
	$response = array(
			'state' => 1,
			'module' => $current_alert,
			'separator' => "",
			'html' => ""
	);
}
ajax_http_send_response($response);


// // le '1' permet de savoir que la session est toujours active, pour éviter les transactions ajax ultérieures
// if($aff_alerte)ajax_http_send_response("1<hr class='alert_separator'> $aff_alerte");
// else ajax_http_send_response("1");
?>