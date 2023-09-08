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
$message_erreur = '';

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$eleve_id = lire_parametre('eleve_id', 0, 'POST');
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
	$chambre_id = lire_parametre('chambre_id', 0, 'POST');
	$date_debut = lire_parametre('date_debut', '', 'POST');
	$date_fin = lire_parametre('date_fin', '', 'POST');
	$reservation_id_courante = lire_parametre('reservation_id_courante', 0, 'POST');
	//***************************************************************************

	$res_eleve_id = 0;
	$res_batiment_id = 0;
	$res_chambre_id = 0;
	$res_date_debut = 0;
	$res_date_fin = 0;
	$res_reservations_existantes = 0;
	$res_eleve_existant = '';
	$res_date_debut_existant = '';
	$res_date_fin_existant = '';
	
	// Eleve
	if($eleve_id == 0) {
		$res_eleve_id = 1; // On doit avoir un eleve
		$erreur = 1;
	}

	// Batiment
	if($batiment_id == 0) {
		$res_batiment_id = 1; // On doit avoir une batiment
		$erreur = 1;
	}

	// Chambre
	if($chambre_id == 0) {
		$res_chambre_id = 1; // On doit avoir une chambre
		$erreur = 1;
	}

	// Date debut
	if($date_debut == '') {
		$res_date_debut = 1; // On doit avoir une date de debut
		$erreur = 1;
	}

	// Date fin
	if($date_fin == '') {
		$res_date_fin = 1; // On doit avoir une date de fin
		$erreur = 1;
	}
	if($res_date_fin == 0) {
		if(strtotime(date_vers_bdd($date_fin)) < strtotime(date_vers_bdd($date_debut))) {
			$res_date_fin = 2; // La date de fin doit etre superieure ou egale a la date de debut
			$erreur = 1;
		}
	}
	
	$nombre_de_lits = 1;
	//********************************************************
	// Rechercher le nombre de lits de la chambre
	$sql ="SELECT tc.nombre_lits ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." c INNER JOIN ".CHA_TAB_TYPE_CHAMBRE." tc ON c.type_chambre_id = tc.type_chambre_id ";
	$sql.="WHERE c.chambre_id = " . $chambre_id . " ";
	$res_nombre_de_lits=execSql($sql);
	if($res_nombre_de_lits->numRows() > 0) {
		$ligne = &$res_nombre_de_lits->fetchRow();
		$nombre_de_lits = $ligne[0];
	}
	$debug_message = $nombre_de_lits;
	//***************************************************************************
	// Verifier si il y a deja une reservation pour cette chambre a ces dates
	if($erreur == 0) {
		$sql ="SELECT reservation_id, elev_id, date_debut, date_fin ";
		$sql.="FROM ".CHA_TAB_RESERVATION." ";
		$sql.="WHERE chambre_id = " . $chambre_id . " ";
		$sql.="AND ( ";
		$sql.="('" . date_vers_bdd($date_debut) . "' >= date_debut AND '" . date_vers_bdd($date_debut) . "' <= date_fin) ";
		$sql.=" OR ('" . date_vers_bdd($date_fin) . "' >= date_debut AND '" . date_vers_bdd($date_fin) . "' <= date_fin) ";
		$sql.=" OR ('" . date_vers_bdd($date_debut) . "' >= date_debut AND '" . date_vers_bdd($date_fin) . "' <= date_fin) ";
		$sql.=" OR ('" . date_vers_bdd($date_debut) . "' <= date_debut AND '" . date_vers_bdd($date_fin) . "' >= date_fin) ";
		$sql.=") ";
		$sql.="AND reservation_id <> " . $reservation_id_courante;
		$res=execSql($sql);
	
		//$debug_message = $sql;
	
		if($res->numRows() >= $nombre_de_lits) {
			$res_reservations_existantes = 1;
			$erreur = 1;
			
			$message_erreur = '';
			$separateur = '';
			//$message_erreur .= $res->numRows();
			for($i=0; $i<$res->numRows(); $i++) {

				$ligne = &$res->fetchRow();
//$debug_message .= ' - ' . $i;
				$message_erreur .= $separateur . '              - n°' . ($i + 1) . ' - ' . LANG_CHA_RESA_105;
				$separateur = "\n";

				$message_erreur = str_replace('#2', date_depuis_bdd($ligne[2]), $message_erreur);
				$message_erreur = str_replace('#3', date_depuis_bdd($ligne[3]), $message_erreur);
				
				// Rechercher l'eleve qui a deja une reservation
				$sql ="SELECT prenom, nom ";
				$sql.="FROM ".CHA_TAB_ELEVES." ";
				$sql.="WHERE elev_id = " . $ligne[1] . " ";
				$res_eleve=execSql($sql);
				if($res_eleve->numRows() > 0) {
					$ligne_eleve = &$res_eleve->fetchRow();	
					$res_eleve_existant = $ligne_eleve[0] . ' ' . $ligne_eleve[1];
					$message_erreur = str_replace('#1', $ligne_eleve[0] . ' ' . $ligne_eleve[1], $message_erreur);
				}				
				
			}
			
			/*
			$ligne = &$res->fetchRow();
			
			$res_date_debut_existant = date_depuis_bdd($ligne[2]);
			$res_date_fin_existant = date_depuis_bdd($ligne[3]);
			
			// Rechercher l'eleve qui a deja une reservation
			$sql ="SELECT prenom, nom ";
			$sql.="FROM ".CHA_TAB_ELEVES." ";
			$sql.="WHERE elev_id = " . $ligne[1] . " ";
			$res=execSql($sql);
			if($res->numRows() > 0) {
				$ligne = &$res->fetchRow();	
				$res_eleve_existant = $ligne[0] . ' ' . $ligne[1];
			}	
			*/	
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

echo $erreur; // 0
echo '¬';
echo $res_eleve_id; // 1
echo '¬';
echo $res_batiment_id; // 2
echo '¬';
echo $res_chambre_id; // 3
echo '¬';
echo $res_date_debut; // 4
echo '¬';
echo $res_date_fin; // 5
echo '¬';
echo $res_reservations_existantes; // 6
echo '¬';
echo $res_eleve_existant; // 7
echo '¬';
echo $res_date_debut_existant; // 8
echo '¬';
echo $res_date_fin_existant; // 9
echo '¬';
echo $debug_message; // 10
echo '¬';
echo $message_erreur; // 11
?>
