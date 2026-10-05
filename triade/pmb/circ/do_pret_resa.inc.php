<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: do_pret_resa.inc.php,v 1.3 2021/12/13 08:30:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// script de prêt d'une résa validée
global $class_path;
require_once($class_path."/ajax_pret.class.php");

function do_pret_resa($id_resa, $force_pret=0) {
	global $msg;
	
	$return_array = array();

	$query = "select expl_cb, expl_id, resa_idempr, resa_idnotice, resa_idbulletin from exemplaires, resa where id_resa='$id_resa' and resa_cb=expl_cb limit 1";
	$result = pmb_mysql_query($query);
	if(pmb_mysql_num_rows($result)) {
		$r = pmb_mysql_fetch_object($result);
		$id_empr = $r->resa_idempr;
		$expl_id = $r->expl_id;
		$expl_cb = $r->expl_cb;		
		if($r->resa_idnotice) {
			$display = new mono_display($r->resa_idnotice);
		}elseif($r->resa_idbulletin) {
			$display = new bulletinage_display($r->resa_idbulletin);
		}
		$libelle = $display->header;
		
		$pret = new do_pret();
		if($force_pret) $force_pret+= 1000; // si action de forcage, on force tous les pièges
		$return_val = $pret->mode1_check_pieges('', $id_empr, $expl_cb, $expl_id, $force_pret);
		if(!$return_val['status']) {
			// pas de piège, le prêt est effectué
			$pret->confirm_pret($id_empr, $expl_id, 0, 'gestion_standard');
		} else {
			// supression du pret temporaire, si créé
			$pret->del_pret($expl_id);
		}		
		$return_array = array(
			'id_resa' => $id_resa,
			'id_empr' => $id_empr,
			'cb_expl' => $expl_cb,	
			'id_expl' => $expl_id,			
			'status' => $return_val['status'],
			'error_message' => $return_val['error_message'],
			'forcage' => $return_val['forcage'],
			'libelle' => $libelle,						
			'info' => $pret
		);
	}else { // erreur: Réservation non validée, ou inexistante
		$id_empr = '';
		$libelle = '';
		$query = "select resa_idempr, resa_idnotice, resa_idbulletin from resa where id_resa='$id_resa' limit 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$r = pmb_mysql_fetch_object($result);
			$id_empr = $r->resa_idempr;
			if($r->resa_idnotice) {
				$display = new mono_display($r->resa_idnotice);	
			}elseif($r->resa_idbulletin) { 
				$display = new bulletinage_display($r->resa_idbulletin);
			}			
			$libelle = $display->header;
		}
		$return_array = array(
			'id_resa' => $id_resa,
			'id_empr' => $id_empr,
			'status' => 2,
			'libelle' => $libelle,		
			'error_message' => $msg['empr_do_pret_refuse_resa_not_valid'],
		);
	}
	return $return_array; 
}

function do_pret_resa_retour_affichage($temp){
	global $alert_sound_list, $msg, $form_cb;	

	$erreur_affichage = "";
	$array_id_piege = array();
	$erreur = "";
	foreach ($temp as $temp_detail) {
		$id_empr = $temp_detail['id_empr'];
		if($temp_detail['status']) {
			$erreur.= "<div class='row'>";
			$erreur.= $temp_detail['libelle']." : <span class='erreur'>".$temp_detail['error_message']."</span>";
			$erreur.= "</div>";
			$array_id_piege[] = $temp_detail['id_resa'];
		}
	}
	if ($erreur) {
		$erreur_affichage.= "<div class='row'>";
		$erreur_affichage.= "	<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
		$erreur_affichage.= "	<div class='colonne-suite'><span class='erreur'>".$msg['empr_do_pret_refuse']."</span></div>";
		$erreur_affichage.= "		<input type='button' class='bouton' value='".$msg['76']."' onClick=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($form_cb)."'\">";
		$erreur_affichage.= "		&nbsp;<input type='button' class='bouton' value='".$msg['empr_do_pret_refuse_forcage']."'";
		$erreur_affichage.= "		onClick=\"document.location='./circ.php?categ=pret&sub=do_pret_resa&id_empr=".$id_empr;
		$erreur_affichage.= "&ids_resa[]=".implode('&ids_resa[]=',$array_id_piege)."&force_pret=1'\" />";
		$erreur_affichage.= "</div><br />".$erreur;
		
		$alert_sound_list[]="critique";
	}
	return $erreur_affichage;
}


