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
	$operation = lire_parametre('operation', 'rechercher', 'POST');
	$code_class = lire_parametre('code_class', 0, 'POST');
	$annee_scolaire = lire_parametre('annee_scolaire', '', 'POST');
	$groupes_id = lire_parametre('groupes_id', array(), 'POST');
	$groupes_id = explode(',',$groupes_id);
	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Initialisation sur changement de classe
	if($operation == "reload_code_class") {
		$annee_scolaire = '';
	}

	//***************************************************************************
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	//echo $sql;
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	// 20100708 - AP : Afficher la liste dea annees meme si aucune classe n'est selectionnee
	if($code_class != "0") {
		$sql.="WHERE code_class = " . $code_class . " ";
	}
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;

	// Rechercher les types de frais
	$sql ="SELECT groupe_id, libelle ";
	$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
	$sql.="ORDER BY groupe_id";
	$groupes_frais=execSql($sql);
	
	//echo $annee_scolaire;
	$total_inscriptions_trouvees = 0;
	if($operation == 'rechercher' || $operation == 'reload_code_class' || $operation == 'reload_annee_scolaire') {
	
		// Rechercher les inscriptions
		$sql ="SELECT i.inscription_id, i.annee_scolaire, c.code_class, c.libelle, e.elev_id, e.nom, e.prenom ";
		$sql.="FROM ((".FIN_TAB_INSCRIPTIONS." i ";
		$sql.="INNER JOIN ".FIN_TAB_ELEVES." e ON i.elev_id = e.elev_id) ";
		$sql.="INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class) ";
		//$sql.="INNER JOIN ".FIN_TAB_FRAIS_INSCRIPTION." fi ON i.inscription_id = fi.inscription_id ";
		$sql.="WHERE 1 = 1 ";
		if($code_class != '0' && $code_class != '') {
			$sql.="AND i.code_class = " . $code_class . " ";
		}
		if($annee_scolaire != '') {
			$sql.="AND i.annee_scolaire = '" . $annee_scolaire . "' ";
		}
		//if(count($type_frais_id) > 0) {
		//	$sql.="AND fi.type_frais_id IN (" . implode(',', $type_frais_id) . ") ";
		//}
		$sql.="GROUP BY i.annee_scolaire, c.code_class, c.libelle, e.elev_id, e.nom, e.prenom ";
		$sql.="ORDER BY i.annee_scolaire, c.libelle, e.nom, e.prenom ";
		$inscriptions=execSql($sql);
		//echo $sql;
		
		$tab_inscriptions = array();
	
		for($i=0; $i<$inscriptions->numRows(); $i++) {
			$res = $inscriptions->fetchInto($ligne_inscription, DB_FETCHMODE_DEFAULT, $i);
			
			$tous_les_groupes_trouves = true;
			
			if(count($groupes_id) > 0) {
				// Rechercher les types de frais de l'inscription
				$sql ="SELECT tf.groupe_id ";
				$sql.="FROM ".FIN_TAB_FRAIS_INSCRIPTION." fi INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ";
				$sql.="ON fi.type_frais_id = tf.type_frais_id ";
				$sql.="WHERE fi.inscription_id = " . $ligne_inscription[0] . " ";
				$sql.="AND ((fi.optionnel = 0) OR (fi.optionnel = 1 AND fi.selectionne = 1)) ";
				$groupes_de_inscription=execSql($sql);
				
				// Stocker la liste des frais de l'inscription courante
				$tab_groupes_de_cette_inscription = array();
				
				for($j=0; $j<$groupes_de_inscription->numRows(); $j++) {
				
					$res = $groupes_de_inscription->fetchInto($ligne_un_groupes, DB_FETCHMODE_DEFAULT, $j);
					$tab_groupes_de_cette_inscription[count($tab_groupes_de_cette_inscription)] = $ligne_un_groupes[0];
				}
				
				// Verifier que tous les frais selectionnes sont dans les frais de l'inscription
				$total_groupes_selectionnes_trouves = 0;
				for($k=0; $k<count($groupes_id); $k++) {
					$ce_groupes_trouve = false;
					for($l=0; $l<count($tab_groupes_de_cette_inscription); $l++) {
						if($groupes_id[$k] == $tab_groupes_de_cette_inscription[$l]) {
							$ce_groupes_trouve = true;
							break;
						}
					}
					if($ce_groupes_trouve) {
						$total_groupes_selectionnes_trouves++;
					}
				}
	
				if($total_groupes_selectionnes_trouves != count($groupes_id)) {
					$tous_les_groupes_trouves = false;
				}
	
	
			}
				
			// guarder l'inscription si tous ses frais sont valides
			if($tous_les_groupes_trouves) {
				$tab_inscriptions[count($tab_inscriptions)] = array (
												'inscription_id' => $ligne_inscription[0],
												'annee_scolaire' => $ligne_inscription[1],
												'code_class' => $ligne_inscription[2],
												'libelle' => $ligne_inscription[3],
												'elev_id' => $ligne_inscription[4],
												'nom' => $ligne_inscription[5],
												'prenom' => $ligne_inscription[6]
											);
			}
				
		//echo $sql;
		}
		$total_inscriptions_trouvees = count($tab_inscriptions);
  }

	$nom_fichier_excel = 'editions_inscriptions_rechercher_groupes_' . date('Ymd') . '.xls';
	
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
	$xls->xlsWriteLabel($ligne_courante, 0, $total_inscriptions_trouvees);
	$xls->xlsWriteLabel($ligne_courante, 1, LANG_FIN_EINS_003);
	$ligne_courante=$ligne_courante+2;
	
	$xls->xlsWriteLabel($ligne_courante, 1, LANG_FIN_GENE_011);
	$xls->xlsWriteLabel($ligne_courante, 2, LANG_FIN_CLAS_003);
	$xls->xlsWriteLabel($ligne_courante, 3, LANG_FIN_ELEV_005);
	$xls->xlsWriteLabel($ligne_courante, 4, LANG_FIN_ELEV_004);
	
	
	for($i=0; $i<count($tab_inscriptions); $i++) {
			$ligne_courante++;
			$xls->xlsWriteLabel($ligne_courante, 1, $tab_inscriptions[$i]['annee_scolaire']);
			$xls->xlsWriteLabel($ligne_courante, 2, ucfirst($tab_inscriptions[$i]['libelle']));
			$xls->xlsWriteLabel($ligne_courante, 3, ucfirst($tab_inscriptions[$i]['nom']));
			$xls->xlsWriteLabel($ligne_courante, 4, ucfirst($tab_inscriptions[$i]['prenom']));
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