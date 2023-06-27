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

$erreur = 0;

// Envoyer les entetes HTTP pour generer du texte
header('Content-type: text/plain; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$mode_page = lire_parametre('mode_page', 'normal', 'POST');
	$chambre_id_defaut = lire_parametre('chambre_id_defaut', 0, 'POST');
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
	$etage_id = lire_parametre('etage_id', 0, 'POST');
	$maj_variables_session = lire_parametre('maj_variables_session', 0, 'POST');
	//***************************************************************************

	$debug_message = '';

	//***************************************************************************
	// Recuperer la liste des chambres
	$html_resultat = '';
	$total_enregistrements = 0;
	if($erreur == 0) {
		
		// On guarde les criteres selectionnes dans la session
		if($maj_variables_session == '1') {
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'] = $batiment_id;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = $etage_id;
		}
		
		// Execution requete
		$sql ="SELECT chambre_id, numero, libelle, type_chambre_id ";
		$sql.="FROM ".CHA_TAB_CHAMBRE." ";
		$sql.="WHERE 1 = 1 ";
		if($batiment_id != 0 || $etage_id != 0) {
			if($batiment_id != 0) {
				$sql.="AND batiment_id = ". $batiment_id . " ";
			}
			if($etage_id != 0) {
				$sql.="AND etage_id = ". $etage_id . " ";
			}
		} else {
			$sql.="AND 1 = 0 ";
		}
		$sql.="ORDER BY numero ASC, libelle ASC";
		$res=execSql($sql);
		//echo $sql;
		$total_enregistrements = $res->numRows();
		
		// Si on est en mode '' => verifier si la chambre a selectionner est dans la liste
		$chambre_id_a_selectionner = 0;
		$enregistrements = array();
		for($i=0; $i<$res->numRows(); $i++) {
			$ligne = &$res->fetchRow();
			$enregistrements[count($enregistrements)] = $ligne;
			if($mode_page == 'initialisation_recherche') {
				if($ligne[0] == $chambre_id_defaut) {
					$chambre_id_a_selectionner = $chambre_id_defaut;
				}
			}
		}
		
		// On genere la liste
		$html_resultat .= '<select name="chambre_id" id="chambre_id" onChange="recuperer_calendrier()">';
		
		// Ajouter l'option 'Toutes'
		$selected = '';
		if($chambre_id_a_selectionner == 0) {
			$selected = 'selected';
			// On guarde les criteres selectionnes dans la session
			if($maj_variables_session == '1') {
				$_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'] = 0;
			}
		}
		$html_resultat .= '<option value="0" ' . $selected . ' class="">' . ucfirst(LANG_CHA_GENE_060) . '</option>';
		
		for($i=0; $i<count($enregistrements); $i++) {
			$ligne = $enregistrements[$i];
			$selected = '';
			if($ligne[0] == $chambre_id_a_selectionner) {
				$selected = 'selected';
				// On guarde les criteres selectionnes dans la session
				if($maj_variables_session == '1') {
					$_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'] = $ligne[0];
				}
			}
			$separateur = '';
			$texte = trim($ligne[1]);
			if(trim($texte) != '') {
				$texte = 'n°' . $texte;
				$separateur = ' - ';
			}
			
			eval('$texte .= $separateur . LANG_CHA_TCHA_ID_' . $ligne[3] .';');
			//$texte .= $separateur . trim($ligne[2]);
			$separateur = ' - ';

			if(trim($ligne[2]) != '') {
				$texte .= $separateur . trim($ligne[2]);
				$separateur = ' - ';
			}

			$html_resultat .= '<option value="' . $ligne[0] . '" ' . $selected . '>' . $texte . '</option>';
			
			
		}
		$html_resultat .= '</select>';
		
	} else {
		// Pas d'enregistrement => envoyer un message d'erreur avec un champ cache a 0
		$html_resultat = '<select name="chambre_id" id="chambre_id">';
		$html_resultat .= '<option value="0" selected class="">' . ucfirst(LANG_CHA_GENE_060) . '</option>';
		$html_resultat .= '</select>';
		// On guarde les criteres selectionnes dans la session
		if($maj_variables_session == '1') {
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'] = 0;
		}
	}
	//***************************************************************************
	
} else {
	// Erreur authentification (code=99)
	$erreur = 99;

	// Fermeture connexion bddd
	Pgclose();

	exit();
}

echo $erreur;
echo '¬';
echo $total_enregistrements;
echo '¬';
echo $html_resultat;
echo '¬';
echo $debug_message;
?>
