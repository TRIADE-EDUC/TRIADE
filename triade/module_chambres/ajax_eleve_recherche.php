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

$debug_message = '';

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	//$eleve_id = lire_parametre('eleve_id', 0, 'POST');
	//$prenom = lire_parametre('prenom', '', 'POST');
	//$nom = lire_parametre('nom', '', 'POST');
	$mode_page = lire_parametre('mode_page', 'normal');
	$eleve_id_defaut = lire_parametre('eleve_id_defaut', 0);
	$prenom = lire_parametre('prenom', '', 'POST');
	$nom = lire_parametre('nom', '', 'POST');
	$maj_variables_session = lire_parametre('maj_variables_session', 0, 'POST');
	//***************************************************************************

	//$debug_message .= 'eleve_id_defaut=' . $eleve_id_defaut . ' - ';
	//$debug_message .= 'mode_page=' . $mode_page . '';


	//***************************************************************************
	// Recuperer la liste des eleves
	$html_resultat = '';
	$total_enregistrements = 0;
	
	$sql2 = '';
	
	//$sql2 = print_r($_POST, true);
	
	if($erreur == 0 && ($prenom != '' || $nom != '' || $mode_page == 'initialisation_recherche')) {
		
		// Execution requete
		$sql ="SELECT elev_id, prenom, nom ";
		$sql.="FROM ".CHA_TAB_ELEVES." ";
		$sql.="WHERE 1=1 ";
		if($mode_page == 'normal') {
			if($prenom != '') {
				$sql.="AND LOWER(prenom) LIKE '". strtolower($prenom). "%' ";
			}
			if($nom != '') {
				$sql.="AND LOWER(nom) LIKE '". strtolower($nom). "%' ";
			}
			// On guarde le prenom et le nom selectionnes dans la session
			if($maj_variables_session == '1') {
				$_SESSION[CHA_REP_MODULE]['reservation_liste']['prenom'] = $prenom;
				$_SESSION[CHA_REP_MODULE]['reservation_liste']['nom'] = $nom;
			}
		} else {
			$sql.="AND elev_id = ". $eleve_id_defaut . " ";
		}
		$sql.="ORDER BY nom ASC, prenom ASC";
		$res=execSql($sql);
		//echo $sql;
		//$sql2 = $sql;
		$total_enregistrements = $res->numRows();
		
		if($total_enregistrements > 0) {
			if($total_enregistrements > 1) {
				// Plus de un enregistrement => envoyer une liste deroulante
				$html_resultat .= '<select name="eleve_id" id="eleve_id">';
				for($i=0; $i<$res->numRows(); $i++) {
					$ligne = &$res->fetchRow();
					$selected = '';
					if($i == 0) {
						$selected = 'selected';
						// On guarde l'eleve trouve dans la session
						if($maj_variables_session == '1') {
							$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = $ligne[0];
						}
					}

					$html_resultat .= '<option value="' . $ligne[0] . '" ' . $selected . '>' . $ligne[1] . ' ' . $ligne[2] . '</option>';
				}
				$html_resultat .= '</select>';
				$html_resultat .= '<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="select">';
			} else {
				// Un seul enregistrement => envoyer le prenom et le nom avec un champ cache initialise
				$ligne = &$res->fetchRow();
				$html_resultat .= $ligne[1] . ' ' . $ligne[2];
				$html_resultat .= '<input type="hidden" name="eleve_id" id="eleve_id" value="' . $ligne[0] . '">';
				$html_resultat .= '<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="hidden">';
				// On guarde l'eleve trouve dans la session
				if($maj_variables_session == '1') {
					$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = $ligne[0];
				}
			}
		} else {
			// Pas d'enregistrement => envoyer un message d'erreur avec un champ cache a 0
			$html_resultat .= LANG_CHA_RESA_010;
			$html_resultat .= '<input type="hidden" name="eleve_id" id="eleve_id" value="0">';
			$html_resultat .= '<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="hidden">';
			// On guarde l'eleve trouve dans la session
			if($maj_variables_session == '1') {
				$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = 0;
			}
		}

	} else {
		// Pas d'enregistrement => envoyer un message d'erreur avec un champ cache a 0
		$html_resultat .= LANG_CHA_RESA_010;
		$html_resultat .= '<input type="hidden" name="eleve_id" id="eleve_id" value="0">';
		$html_resultat .= '<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="hidden">';
		// On guarde l'eleve trouve dans la session
		if($maj_variables_session == '1') {
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = 0;
		}
	}
	
	// Ajouter l'icone pour effacer (si on a trouve au moins un eleve)
	// if($total_enregistrements > 0) {
		// $html_resultat_tmp = '';
		// $html_resultat_tmp .= '<table cellspacing="0" cellpadding="0" border="0">';
		// $html_resultat_tmp .= '	<tr>';
		// $html_resultat_tmp .= '		<td nowrap="nowrap">';
		// $html_resultat_tmp .= 			$html_resultat;
		// $html_resultat_tmp .= '		</td>';
		// $html_resultat_tmp .= '		<td>';
		// $html_resultat_tmp .= 			'&nbsp;';
		// $html_resultat_tmp .= '		</td>';
		// $html_resultat_tmp .= '		<td>';
		// $html_resultat_tmp .= 			'<a href="javascript:;" onClick="effacer_eleve()" title="' . LANG_CHA_GENE_059 . '"><img src="image/commun/b_drop.png" border="0" alt="' . LANG_CHA_GENE_059 . '">';
		// $html_resultat_tmp .= '		</td>';
		// $html_resultat_tmp .= '	</tr>';
		// $html_resultat_tmp .= '</table>';
		// $html_resultat = $html_resultat_tmp;
	// }
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
echo $html_resultat.$sql2;
echo '¬';
echo $debug_message;
?>
