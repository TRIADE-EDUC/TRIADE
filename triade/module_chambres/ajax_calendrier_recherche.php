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
	$chambre_id = lire_parametre('chambre_id', 0, 'POST');
	$maj_variables_session = lire_parametre('maj_variables_session', 0, 'POST');
	//***************************************************************************

	$debug_message = '';

	//***************************************************************************
	// Recuperer la liste des chambres
	$html_resultat = '';
	if($erreur == 0) {
	$annee = date('Y');
	// On genere la liste
	$html_resultat = "<a title=\"Voir le calendrier de la chambre\" 
					onclick=\"window.open('planning_calendrier.php?chambre=$chambre_id&annee=$annee','','toolbar=0,menubar=0,location=0,scrollbars=1,width=840,height=800')\">
					<img src='module_chambres/images/calendrier.png' border='0' align='center'/>
					</a>";	
		
	} else {
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
