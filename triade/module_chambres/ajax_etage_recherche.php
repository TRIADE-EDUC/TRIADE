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
	$etage_id_defaut = lire_parametre('etage_id_defaut', 0, 'POST');
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
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
		}
		
		// Execution requete
		$sql ="SELECT c.etage_id ";
		$sql.="FROM ".CHA_TAB_CHAMBRE." c INNER JOIN ".CHA_TAB_ETAGE." e ON c.etage_id = e.etage_id ";
		if($batiment_id != 0) {
			$sql.="WHERE batiment_id = ". $batiment_id . " ";
		} else {
			$sql.="WHERE 1 = 0 ";
		}
		$sql.="GROUP BY c.etage_id ";
		$sql.="ORDER BY e.ordre ASC";
		$res=execSql($sql);
		//echo $sql;
		$total_enregistrements = $res->numRows();
		
		// Si on est en mode '' => verifier si l'etage a selectionner est dans la liste
		$etage_id_a_selectionner = 0;
		$enregistrements = array();
		for($i=0; $i<$res->numRows(); $i++) {
			$ligne = &$res->fetchRow();
			$enregistrements[count($enregistrements)] = $ligne;
			if($batiment_id != 0) {
				if($mode_page == 'initialisation_recherche') {
					//echo $ligne[0] . ' - ' . $etage_id_defaut . ' ##### ';
					if($ligne[0] == $etage_id_defaut) {
						$etage_id_a_selectionner = $etage_id_defaut;
					}
				}
			}
		}
		
		// On genere la liste
		$html_resultat .= '<select name="etage_id" id="etage_id" onChange="recuperer_liste_chambres()">';
		
		// Ajouter l'option 'Toutes'
		$selected = '';
		if($etage_id_a_selectionner == 0) {
			$selected = 'selected';
			// On guarde les criteres selectionnes dans la session
			if($maj_variables_session == '1') {
				$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = 0;
			}
		}
		$html_resultat .= '<option value="0" ' . $selected . ' class="">' . ucfirst(LANG_CHA_GENE_025) . '</option>';
		
		for($i=0; $i<count($enregistrements); $i++) {
			$ligne = $enregistrements[$i];
			$selected = '';
			if($ligne[0] == $etage_id_a_selectionner) {
				$selected = 'selected';
				// On guarde les criteres selectionnes dans la session
				if($maj_variables_session == '1') {
					$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = $ligne[0];
				}
			}
			
			eval('$texte = LANG_CHA_ETAG_ID_' . $ligne[0] .'_LIBELLE;');
			eval('$exposant = LANG_CHA_ETAG_ID_' . $ligne[0] .'_EXPOSANT;');
			if(trim($exposant) != '') {
				$texte .= ' ' . $exposant . '';
			}

			$html_resultat .= '<option value="' . $ligne[0] . '" ' . $selected . '>' . $texte . '</option>';
		}
		$html_resultat .= '</select>';

	} else {
		// Pas d'enregistrement => envoyer un message d'erreur avec un champ cache a 0
		$html_resultat = '<select name="etage_id" id="etage_id" onChange="recuperer_liste_chambres()">';
		$html_resultat .= '<option value="0" selected class="">' . ucfirst(LANG_CHA_GENE_025) . '</option>';
		$html_resultat .= '</select>';
		// On guarde les criteres selectionnes dans la session
		if($maj_variables_session == '1') {
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = 0;
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
