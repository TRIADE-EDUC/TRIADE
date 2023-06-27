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
	$operation = lire_parametre('operation', '', 'POST');
	$type_reglement_id = lire_parametre('type_reglement_id', 0, 'POST');
	$date_limite = lire_parametre('date_limite', '', 'POST');
	//***************************************************************************

	// Initialiser la date limite si elle est vide
	if($date_limite == '') {
		$date_limite = date('d/m/Y');
	}

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	//***************************************************************************
	
	// Rechercher la liste des types de reglement
	$sql ="SELECT tr.type_reglement_id, tr.libelle ";
	$sql.="FROM ".FIN_TAB_TYPE_REGLEMENT." tr ";
	$sql.="INNER JOIN ".FIN_TAB_ECHEANCIER." e ON tr.type_reglement_id = e.type_reglement_id ";
	$sql.="GROUP BY tr.type_reglement_id, tr.libelle ";
	$sql.="ORDER BY tr.libelle ASC ";
	$types_reglement=execSql($sql);

	if($types_reglement->numRows() > 0) {
		
		/*
		// Preselectionner le premier type de reglement si il n'y en a pas déjà un
		if($type_reglement_id == 0) {
			$res = $type_reglement_id->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			$type_reglement_id = ligne[0];
		}
		*/
	
		// Rechercher la liste des echeances expirees
		$sql ="SELECT e.elev_id, e.nom, e.prenom, c.code_class, c.libelle, i.inscription_id, i.date_inscription, i.annee_scolaire, ec.echeancier_id, ec.date_echeance, ec.montant, tr.libelle, i.date_depart ";
		$sql.="FROM ((".FIN_TAB_INSCRIPTIONS." i ";
		$sql.="INNER JOIN ".FIN_TAB_ELEVES." e ON i.elev_id = e.elev_id) ";
		$sql.="INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class) ";
		$sql.="INNER JOIN ".FIN_TAB_ECHEANCIER." ec ON i.inscription_id = ec.inscription_id ";
		$sql.="INNER JOIN ".FIN_TAB_TYPE_REGLEMENT." tr ON ec.type_reglement_id = tr.type_reglement_id ";
		$sql.="WHERE ec.date_echeance < '" . date_vers_bdd($date_limite) . "' ";
		//$sql.="WHERE ec.date_echeance < CURDATE() ";
		$sql.="AND ec.montant > 0 ";
		$sql.="AND ec.impaye = 0 ";
		$sql.="AND ec.type <> 2 "; // => Ne pas inclure les remises exceptionnelles 
		if($type_reglement_id != 0) {
			$sql.="AND ec.type_reglement_id = $type_reglement_id ";
		}
		$sql.="ORDER BY LEFT(i.annee_scolaire, 4) DESC, ec.date_echeance DESC, tr.type_reglement_id";
		//echo $sql;
		$impayes=execSql($sql);
		
		
		// Verifier si l'echeance a ete payee ou non et stocker pour affichage
		$tab_impayes = array();
		$reste_a_payer = 0;
		if($impayes->numRows() > 0) {
			for($i=0; $i<$impayes->numRows(); $i++) {
				$ligne = $impayes->fetchRow();
				$reste_a_payer = reglement_reste_a_payer('echeance', $ligne[8]);
				
				// Verifier si l'eleve est partit ou non
				$eleve_partit = false;
				$date_depart = $ligne[12];
				if(!is_null($date_depart) && !empty($date_depart)) {
					if(trim($date_depart) != '') {
						$date_depart = strtotime($date_depart);
						$date_echeance = strtotime($ligne[9]);
						if($date_echeance >= $date_depart) {
							$eleve_partit = true;
						}
					}
				}
				
				if($reste_a_payer > 0 && !$eleve_partit) {
					$tab_impayes[count($tab_impayes)] = array(
														"eleves_elev_id" => $ligne[0],
														"eleves_nom" => $ligne[1],
														"eleves_prenom" => $ligne[2],
														"classes_code_class" => $ligne[3],
														"classes_libelle" => $ligne[4],
														"inscription_inscription_id" => $ligne[5],
														"inscription_date_inscription" => $ligne[6],
														"inscription_annee_scolaire" => $ligne[7],
														"echeancier_echeancier_id" => $ligne[8],
														"echeancier_date_echeance" => $ligne[9],
														"echeancier_montant" => $ligne[10],
														"reste_a_payer" => $reste_a_payer,
														"type_reglement_libelle" => $ligne[11]
														);
			
				}
			}
		}
	}
	
	$nom_fichier_excel = 'impayes_liste_' . date('Ymd') . '.xls';
	
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
	$xls->xlsWriteLabel($ligne_courante, 1, LANG_FIN_CLAS_003);
	$xls->xlsWriteLabel($ligne_courante, 2, LANG_FIN_GENE_011);
	$xls->xlsWriteLabel($ligne_courante, 3, LANG_FIN_ELEV_004);
	$xls->xlsWriteLabel($ligne_courante, 4, LANG_FIN_ELEV_005);
	$xls->xlsWriteLabel($ligne_courante, 5, LANG_FIN_ECHE_004);
	$xls->xlsWriteLabel($ligne_courante, 6, LANG_FIN_GENE_013);
	$xls->xlsWriteLabel($ligne_courante, 7, LANG_FIN_GENE_034);
	$xls->xlsWriteLabel($ligne_courante, 8, LANG_FIN_TREG_015);
	
	$total_reste_a_payer = 0.0;
	if(count($tab_impayes) > 0) {
		for($i=0; $i<count($tab_impayes); $i++) {
			$total_reste_a_payer += $tab_impayes[$i]['reste_a_payer'];
			$ligne_courante++;
			$xls->xlsWriteLabel($ligne_courante, 1, ucfirst($tab_impayes[$i]['classes_libelle']));
			$xls->xlsWriteLabel($ligne_courante, 2, ucfirst($tab_impayes[$i]['inscription_annee_scolaire']));
			$xls->xlsWriteLabel($ligne_courante, 3, ucfirst($tab_impayes[$i]['eleves_prenom']));
			$xls->xlsWriteLabel($ligne_courante, 4, strtoupper($tab_impayes[$i]['eleves_nom']));
			$xls->xlsWriteLabel($ligne_courante, 5, date_depuis_bdd($tab_impayes[$i]['echeancier_date_echeance']));
			$xls->xlsWriteLabel($ligne_courante, 6, montant_depuis_bdd($tab_impayes[$i]['echeancier_montant']));
			$xls->xlsWriteLabel($ligne_courante, 7, montant_depuis_bdd($tab_impayes[$i]['reste_a_payer']));
			$xls->xlsWriteLabel($ligne_courante, 8, $tab_impayes[$i]['type_reglement_libelle']);
		}	
			$ligne_courante=$ligne_courante+2;
			
			$xls->xlsWriteLabel($ligne_courante, 6, ucfirst(LANG_FIN_GENE_024));
			$xls->xlsWriteLabel($ligne_courante, 7, montant_depuis_bdd($total_reste_a_payer));
			
			
	}
	$xls->xlsEOF();
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

// Fermeture connexion bddd
Pgclose();
?>