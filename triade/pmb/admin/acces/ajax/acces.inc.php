<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acces.inc.php,v 1.5 2017-09-06 15:33:47 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//droits d'acces actives
if ($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}


//droits d'acces utilisateur/notice
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1 && $dom_id==1) {
	$dom= $ac->setDomain(1);
}

//droits d'acces emprunteur/notice
if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1 && $dom_id==2) {
	$dom= $ac->setDomain(2);
}

//droits d'acces emprunteur/docnums
if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1 && $dom_id==3) {
	$dom= $ac->setDomain(3);
}

//droits d'acces emprunteur/espaces de contribution
if ($gestion_acces_active==1 && $gestion_acces_empr_contribution_area==1 && $dom_id==4) {
	$dom= $ac->setDomain(4);
}

//droits d'acces emprunteur/scenarios de contribution
if ($gestion_acces_active==1 && $gestion_acces_empr_contribution_scenario==1 && $dom_id==5) {
	$dom= $ac->setDomain(5);
}

//droits d'acces modérateurs/contributeurs
if ($gestion_acces_active==1 && $gestion_acces_contribution_moderator_empr==1 && $dom_id==6) {
	$dom= $ac->setDomain(6);
}

if (isset($dom) && is_object($dom)) {
	switch ($fname) {
		case 'getNbResourcesToUpdate':
			$nb=$dom->getNbResourcesToUpdate();
			ajax_http_send_response($nb);
			break;
		case 'updateRessources' :
			if(!$nb_done) $nb_done=0;
			$nb=$dom->applyDomainRights($nb_done,$chk_sav_spe_rights);
			ajax_http_send_response($nb);
			break;
		case 'cleanResources' :
			$dom->cleanResources();
			ajax_http_send_response('done');
		default:
			break;
	}
}
?>