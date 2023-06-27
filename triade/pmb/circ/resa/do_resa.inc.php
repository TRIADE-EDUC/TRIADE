<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: do_resa.inc.php,v 1.18 2017-11-21 12:01:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de création d'une réservation
// toute la mécanique doit être ici
// on dispose des variables $id_empr et $id_notice || $id_bulletin

require_once("$class_path/emprunteur.class.php");
require_once("$class_path/resa.class.php");
require_once("$class_path/serial_display.class.php");

if(!isset($delete)) $delete = '';
if(!isset($force_resa)) $force_resa = '';

if($id_empr && ($id_notice || $id_bulletin)) {
	// on teste si c'est une suppression
	if(!$delete) {
		// on tente d'effectuer la réservation
		if($id_notice) {
			$resa = new reservation($id_empr, $id_notice, 0);
		} else {
			$resa = new reservation($id_empr, 0, $id_bulletin);
		}
		
		if($force_resa && $pmb_resa_records_no_expl){
			$sucessfull_booking = $resa->add(0, 1);
		}else{
			$sucessfull_booking = $resa->add();
		}
		
		
		if(!$sucessfull_booking && $pmb_resa_records_no_expl){
			$erreur_affichage.="<input type='button' class='bouton' value='".$msg["resa_force"]."' onClick=\"document.location='circ.php?categ=resa&id_empr=$id_empr&id_notice=$id_notice&id_bulletin=$id_bulletin&quota_resa=1'\">";
			$erreur_affichage="<div class='row'><div class='colonne10'><img src='".get_url_icon('info.png')."' /></div>
						<div class='colonne-suite'>";
			$erreur_affichage.="<span class='erreur'>".$resa->message."</span>";
			$erreur_affichage.="</div>";
			$erreur_affichage.="<input type='button' class='bouton' value='".$msg["resa_force"]."' onClick=\"document.location='circ.php?categ=resa&id_empr=$id_empr&id_notice=$id_notice&id_bulletin=$id_bulletin&force_resa=1'\">";
			$erreur_affichage.= "</div>\n";
			$empr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1, $resa->id);
			print pmb_bidi($empr->fiche);
		}else{
			$erreur_affichage="<div class='row'><div class='colonne10'><img src='".get_url_icon('info.png')."' /></div>
						<div class='colonne-suite'>";
			$erreur_affichage.="<span class='erreur'>".$resa->message."</span>";
			$erreur_affichage.="</div>";
			if ($resa->force) {
				$erreur_affichage.="<input type='button' class='bouton' value='".$msg["resa_force"]."' onClick=\"document.location='circ.php?categ=resa&id_empr=$id_empr&id_notice=$id_notice&id_bulletin=$id_bulletin&quota_resa=1'\">";
			}
			$erreur_affichage.= "</div>\n";
			$empr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1, $resa->id);
			print pmb_bidi($empr->fiche);
		}

	} else {
		// c'est une suppression
		if($id_notice) {
			$resa = new reservation($id_empr, $id_notice);
		} else {
			$resa = new reservation($id_empr, 0, $id_bulletin);
		}
		$resa->delete();
		$erreur_affichage="<table border='0' cellpadding='1' height='40' border='1'><tr><td style='width:33px'><span><img src='".get_url_icon('info.png')."' /></span></td>
					<td style='width:100%'>";
		$erreur_affichage.="<span class='erreur'>".$resa->message."</span>";
		$erreur_affichage.="</td></tr></table>";
		$empr = new emprunteur($id_empr, $erreur_affichage, FALSE, 1);
		print pmb_bidi($empr->fiche);
	}
} else {
	if($groupID) print "<script type='text/javascript'>document.location=\"./circ.php&categ=groups&groupID=$groupID\";</script>";
	else print "<script type='text/javascript'>document.location=\"./circ.php\";</script>";
}
