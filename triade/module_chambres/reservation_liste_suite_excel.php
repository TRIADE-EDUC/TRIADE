<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");
include("librairie_php/lib_xls.inc.php");
// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	
	$eleve_id = $_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'];
	$batiment_id = $_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'];
	$etage_id = $_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'];
	$chambre_id = $_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'];
	$date_debut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['date_debut'];
	$date_fin = $_SESSION[CHA_REP_MODULE]['reservation_liste']['date_fin'];
	
	
	// Execution requete
	$sql ="SELECT e.prenom, e.nom, b.libelle as libelle_batiment, b.code_postal, b.ville, c.chambre_id, c.numero, c.libelle as libelle_chambre, r.reservation_id, r.date_debut, r.date_fin, r.commentaire, r.date_reservation, c.type_chambre_id, c.etage_id ";
	$sql.="FROM ((".CHA_TAB_RESERVATION." r LEFT JOIN ".CHA_TAB_ELEVES." e ON r.elev_id = e.elev_id) ";
	$sql.="INNER JOIN ".CHA_TAB_CHAMBRE." c ON r.chambre_id = c.chambre_id) ";
	$sql.="INNER JOIN ".CHA_TAB_BATIMENT." b ON c.batiment_id = b.batiment_id ";
	$sql.="WHERE 1=1 ";
	if($eleve_id != 0) {
		$sql.="AND e.elev_id = " . $eleve_id . " ";
	}
	if($batiment_id != 0) {
		$sql.="AND c.batiment_id = " . $batiment_id . " ";
	}
	if($etage_id != 0) {
		$sql.="AND c.etage_id = " . $etage_id . " ";
	}
	if($chambre_id != 0) {
		$sql.="AND c.chambre_id = " . $chambre_id . " ";
	}
	if($date_debut != '') {
		$sql.="AND r.date_fin >= '" . date_vers_bdd($date_debut) . "' ";
	}
	if($date_fin != '') {
		$sql.="AND r.date_debut <= '" . date_vers_bdd($date_fin) . "' ";
	}
	$sql.="ORDER BY b.libelle, b.code_postal, b.ville, c.numero ASC, c.libelle ASC";
	$res=execSql($sql);
	

	$nom_fichier_excel = 'reservation_eleve_' . $eleve_id . '_' . date('Ymd') . '.xls';
	
	// Envoyer les entetes HTTP
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");;
	header("Content-Disposition: attachment;filename=" . $nom_fichier_excel);
	header("Content-Transfer-Encoding: binary ");	
	
	$xls = new xls();
	
	$xls->xlsBOF();
	
	$ligne_courante = -1;

	$ligne_courante++;
	$total_enregistrements = $res->numRows();
	
	$xls->xlsWriteLabel($ligne_courante, 0, LANG_CHA_GENE_058 . ' : ' . $total_enregistrements);

	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, '#');
	$xls->xlsWriteLabel($ligne_courante, 1, LANG_CHA_RESA_009);
	$xls->xlsWriteLabel($ligne_courante, 2, LANG_CHA_RESA_005);
	$xls->xlsWriteLabel($ligne_courante, 3, LANG_CHA_ETAG_001);
	$xls->xlsWriteLabel($ligne_courante, 4, LANG_CHA_RESA_006);
	$xls->xlsWriteLabel($ligne_courante, 5, LANG_CHA_TCHA_001);
	$xls->xlsWriteLabel($ligne_courante, 6, LANG_CHA_RESA_007);
	$xls->xlsWriteLabel($ligne_courante, 7, LANG_CHA_RESA_008);
	$xls->xlsWriteLabel($ligne_courante, 8, LANG_CHA_RESA_018);
	
	if($res->numRows() > 0) {
		for($i=0; $i<$res->numRows(); $i++) {
		$ligne_courante++;
		$ligne = &$res->fetchRow();
		
		$xls->xlsWriteLabel($ligne_courante, 0, $ligne[8]);
		$separateur = '';
		$texte = trim($ligne[0]);
		if(trim($texte) != '') {
			$separateur = ' - ';
		}
				
		if(trim($ligne[4]) != '') {
			$texte .= $separateur . trim($ligne[4]);															
			$separateur = ' - ';
		}

		if(trim($ligne[5]) != '') {
			$texte .= $separateur . trim($ligne[5]);
			$separateur = ' - ';
		}
		$texte = trim($ligne[0]) . ' ' . trim($ligne[1]);
		$xls->xlsWriteLabel($ligne_courante, 1, $texte);
		$texte = trim($ligne[2]) . ' - ' . trim($ligne[3]) . ' ' . trim($ligne[4]);
		$xls->xlsWriteLabel($ligne_courante, 2, $texte);
		eval('$texte = LANG_CHA_ETAG_ID_' . $ligne[14] .'_LIBELLE;');
		eval('$exposant = LANG_CHA_ETAG_ID_' . $ligne[14] .'_EXPOSANT;');
		$xls->xlsWriteLabel($ligne_courante, 3, $texte . $exposant);
		$separateur = '';
		$texte = trim($ligne[6]);
		if(trim($texte) != '') {
			$texte = 'n°' . $texte;
			$separateur = ' - ';
		}
		if(trim($ligne[7]) != '') {
			$texte .= $separateur . trim($ligne[7]);															
			$separateur = ' - ';
		}
		$xls->xlsWriteLabel($ligne_courante, 4, $texte);
		eval('$texte = LANG_CHA_TCHA_ID_' . $ligne[13] .';');
		$xls->xlsWriteLabel($ligne_courante, 5, $texte);
		$texte = date_depuis_bdd(trim($ligne[9]));
		$xls->xlsWriteLabel($ligne_courante, 6, $texte);
		$texte = date_depuis_bdd(trim($ligne[10]));
		$xls->xlsWriteLabel($ligne_courante, 7, $texte);
		$texte = date_depuis_bdd(trim($ligne[12]), false);
		$xls->xlsWriteLabel($ligne_courante, 8, $texte);
		}
	}
	else{
	}
	
	$xls->xlsEOF();

	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . CHA_SCRIPT_PAS_AUTORISATION) ;
	exit();
}
				