<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: crontab_pmb.php,v 1.3 2011-11-04 13:44:01 dgoron Exp $
 
//PARAMETRAGE CLIENT
/* Identifiant de la source du connecteur sortant */
$source_id=3;
// adresse WS	
$adresse_ws="http://SERVER/PATH_PMB/ws/";

verif_exec($adresse_ws."connector_out.php?source_id=".$source_id."&wsdl");

function verif_exec($url) {
	global $source_id;

	$ws=new SoapClient($url);

	//ces 3 fonctions doivent être autorisée dans le groupe anonyme 
	//Tâches dont le timeout serait dépassé...
	$ws->pmbesTasks_timeoutTasks();
	//Tâches interrompues involontairement..
	$ws->pmbesTasks_checkTasks();
	//Tâches à exécuter	
	$ws->pmbesTasks_runTasks($source_id);

}
