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
	$chambre_id = lire_parametre('chambre_id', 0, 'POST');
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
	$numero = lire_parametre('numero', '', 'POST');
	//***************************************************************************

	if($batiment_id == 0) {
		$erreur = 1;
	}

	//***************************************************************************
	// Verifier si ce numero de chambre existe deja pour ce batiment
	$resultat_numero = 0;
	if($erreur == 0) {
		$sql ="SELECT chambre_id ";
		$sql.="FROM ".CHA_TAB_CHAMBRE." ";
		$sql.="WHERE chambre_id <> " . $chambre_id . " AND batiment_id = " . $batiment_id . " AND LOWER(numero) = '" . strtolower($numero) . "' ";
		$res=execSql($sql);
	
		if($res->numRows() > 0) {
			$resultat_numero = 1;
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
echo $resultat_numero;
?>
