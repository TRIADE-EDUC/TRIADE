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

$style_tableau = 'border: #000000 solid 1px; background-color:#fce4ba;';
$style_lien = 'color: #5f3a0c; font-weight: bold;';

// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");

$erreur = 0;
$total_enregistrements = 0;
$html = '';

// Envoyer les entetes HTTP pour generer du texte
header('Content-type: text/plain; charset=ISO-8859-1');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$type_recherche = lire_parametre('type_recherche', '', 'POST');
	$critere_1 = lire_parametre('critere_1', '', 'POST');
	$critere_2 = lire_parametre('critere_2', '', 'POST');
	$critere_3 = lire_parametre('critere_3', '', 'POST');
	$critere_4 = lire_parametre('critere_4', '', 'POST');
	$critere_5 = lire_parametre('critere_5', '', 'POST');
	$fonction_onclick = lire_parametre('fonction_onclick', '', 'POST');
	//***************************************************************************
	
	//***************************************************************************
	// Recuperer la liste des eleves
	
	if($erreur == 0) {
	
		switch($type_recherche) {
			case 'eleve':
				// Execution requete
				$sql ="SELECT elev_id, prenom, nom ";
				$sql.="FROM ".FIN_TAB_ELEVES." ";
				if($critere_1 != '') {
					$sql.="WHERE LCASE(LEFT(nom, " . strlen($critere_1) . ")) ='". strtoupper($critere_1) . "' ";
				}
				$sql.="ORDER BY nom ASC";
				//echo $sql;
				$res=execSql($sql);
				
				$total_enregistrements = $res->numRows();
				
				$separateur = '';
				
				for($i=0; $i<$res->numRows(); $i++) {
					$ligne = &$res->fetchRow();
					$onclick = '';
					
					$prenom = addslashes($ligne[1]);
					$nom = addslashes($ligne[2]);
					
					
					if($fonction_onclick != '') {
						$onclick = $fonction_onclick . '(\'' . $ligne[0] . '\',\'' . $prenom . '\',\''  . $nom . '\')';
					}
					$html .= $separateur . '<a href="javascript:;" onclick="' . $onclick . '" style="' . $style_lien . '">' . ($ligne[2]) . '</a>';
					$separateur = '<br>';
				}
				
				// Formatage du tableau
				if($html != '') {
				
					$html_tableau = '<table border="0" style="' . $style_tableau . '" >';
					$html_tableau .= '<tr>';
					$html_tableau .= '<td align="left">';
					$html_tableau .= $html;
					$html_tableau .= '</td>';
					$html_tableau .= '</tr>';
					$html_tableau .= '</table>';
					$html = $html_tableau;
				}
				
				break;
				
				
			default:
				$erreur = 1; // Type de recherche inconnu
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
echo $html;
?>