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
	$code_class = lire_parametre('code_class', 0, 'POST');
	$annee_scolaire = lire_parametre('annee_scolaire', '', 'POST');
	//***************************************************************************

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Initialisation sur changement de classe
	if($operation == "reload_code_class") {
		$annee_scolaire = '';
	}

	//***************************************************************************
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_BAREME." b ON c.code_class = b.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	//echo $sql;
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_BAREME." ";
	$sql.="WHERE code_class = " . $code_class . " ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	
	//echo $annee_scolaire;
	
	// Rechercher les baremes
		$sql ="SELECT b.bareme_id, b.libelle, b.annee_scolaire, c.libelle ";
		$sql.="FROM ".FIN_TAB_BAREME." b ";
		$sql.="INNER JOIN ".FIN_TAB_CLASSES." c ON b.code_class = c.code_class ";
		$sql.="WHERE 1 = 1 ";
		if($code_class != '0' && $code_class != '') {
			$sql.="AND b.code_class = " . $code_class . " ";
		}
		if($annee_scolaire != '') {
			$sql.="AND b.annee_scolaire = '" . $annee_scolaire . "' ";
		}
		$sql.="ORDER BY  b.annee_scolaire, c.libelle, b.libelle";
		$baremes=execSql($sql);
		//echo $sql;
	
	
	$nom_fichier_excel = 'editions_bareme_rechercher_' . date('Ymd') . '.xls';
	
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

	if($baremes->numRows() > 0) {
		for($i=0; $i<$baremes->numRows(); $i++) {
			$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
		
			$ligne_courante++;
			$xls->xlsWriteLabel($ligne_courante, 0, $ligne[1]);
			$ligne_courante++;
			$xls->xlsWriteLabel($ligne_courante, 1, LANG_FIN_CLAS_003);
			$xls->xlsWriteLabel($ligne_courante, 2, $ligne[3]);
			$xls->xlsWriteLabel($ligne_courante, 3, LANG_FIN_GENE_011);
			$xls->xlsWriteLabel($ligne_courante, 4, $ligne[2]);
				
			$sql ="SELECT fb.type_frais_id, fb. montant, fb.optionnel , fb.lisse, tf.libelle  ";
			$sql.="FROM ".FIN_TAB_FRAIS_BAREME." fb ";
			$sql.="INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
			$sql.="WHERE fb.bareme_id = " . $ligne[0] . " ";
			$sql.="ORDER BY tf.libelle";
			$frais=execSql($sql);
			
			$ligne_courante=$ligne_courante+2;	
			$xls->xlsWriteLabel($ligne_courante, 1, LANG_FIN_GENE_010);	
			$xls->xlsWriteLabel($ligne_courante, 2, LANG_FIN_GENE_013);	
			$xls->xlsWriteLabel($ligne_courante, 3, LANG_FIN_GENE_012);	
			$xls->xlsWriteLabel($ligne_courante, 4, LANG_FIN_TFRA_014);	
			
			for($j=0; $j<$frais->numRows(); $j++) {
				$res = $frais->fetchInto($ligne_frais, DB_FETCHMODE_DEFAULT, $j);	
				$ligne_courante++;
				$xls->xlsWriteLabel($ligne_courante, 1, $ligne_frais[4]);	
				$xls->xlsWriteLabel($ligne_courante, 2, montant_depuis_bdd($ligne_frais[1], 2));	
				
				if($ligne_frais[2] == '1') {
					$src = "Oui";
				} else {
					$src = "Non";
				}
				$xls->xlsWriteLabel($ligne_courante, 3, $src);
				
				if($ligne_frais[3] == '1') {
					$src = "Oui";
				} else {
					$src = "Non";
				}
				$xls->xlsWriteLabel($ligne_courante, 4, $src);
			}	
			
			$ligne_courante=$ligne_courante+2;
		}
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