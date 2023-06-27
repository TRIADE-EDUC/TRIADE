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
	$date_debut = lire_parametre('date_debut', '01/01/' . date('Y'), 'POST');
	$date_fin = lire_parametre('date_fin', '31/12/' . date('Y'), 'POST');
	$code_class = lire_parametre('code_class', 0, 'POST');
	$ordre_tri = lire_parametre('ordre_tri', 'libelle_classe', 'POST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	/*
	if($operation != "reload_annee_scolaire") {
		if($annee_scolaire == 'aucune') {
			$annee_scolaire = annee_scolaire_courante();
		}
	}
	*/
	//***************************************************************************
	
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);

	if($operation != '')
	{
			$tab_types_reglement = array();
			// Rechercher la liste des types de reglement
			$sql ="SELECT type_reglement_id, libelle ";
			$sql.="FROM ".FIN_TAB_TYPE_REGLEMENT." ";
			$sql.="ORDER BY libelle";
			$types_reglement=execSql($sql);
			for($i=0; $i<$types_reglement->numRows(); $i++) {
				// Acces s l'enregistrement courant
				$res = $types_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $i);
				$tab_types_reglement[count($tab_types_reglement)] = array(
																		'type_reglement_id' => $ligne_type_reglement[0],
																		'libelle' => $ligne_type_reglement[1],
																		'a_payer' => 0.0,
																		'reste_a_payer' => 0.0,
																		'encaisse' => 0.0
															);
			}

			
			$tab_groupe_type = array();
			// Rechercher la liste des types de reglement
			$sql ="SELECT groupe_id, libelle ";
			$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
			$sql.="ORDER BY groupe_id";
			$groupe_type=execSql($sql);
			
			for($i=0; $i<$groupe_type->numRows(); $i++) {
				// Acces s l'enregistrement courant
				$res = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
				$tab_groupe_type[count($tab_groupe_type)] = array(
																		'groupe_id' => $ligne_groupe_type[0],
																		'libelle' => $ligne_groupe_type[1],
																		'total' => 0.0,
																		'reste_a_payer' => 0.0,
																		'encaisse' => 0.0
															);
			}


			// Rechercher la liste des eleves
			$sql ="SELECT el.elev_id, el.nom, el.prenom, i.inscription_id, i.annee_scolaire, cl.code_class, cl.libelle as libelle_classe, ec.echeancier_id, ec.date_echeance, ec.montant, tr.libelle as libelle_type_reglement, ec.type_reglement_id, i.date_depart ";
			$sql.="FROM (((".FIN_TAB_INSCRIPTIONS." i INNER JOIN ".FIN_TAB_ELEVES." el ON el.elev_id = i.elev_id) ";
			$sql.="INNER JOIN ".FIN_TAB_ECHEANCIER." ec ON ec.inscription_id = i.inscription_id) ";
			$sql.="INNER JOIN ".FIN_TAB_CLASSES." cl ON cl.code_class = i.code_class) ";
			$sql.="INNER JOIN ".FIN_TAB_TYPE_REGLEMENT." tr ON tr.type_reglement_id = ec.type_reglement_id ";
			$sql.="WHERE 1 = 1 ";
			if($code_class != '0') {
				$sql.="AND i.code_class = " . $code_class . " ";
			}
			if($date_debut != '') {
				$sql.="AND ec.date_echeance >= '" . date_vers_bdd($date_debut) . " 00:00:00' ";
			}
			if($date_fin != '') {
				$sql.="AND ec.date_echeance <= '" . date_vers_bdd($date_fin) . " 23:59:59' ";
			}
			$sql.="ORDER BY el.nom ASC, el.prenom ASC, i.annee_scolaire ASC, cl.libelle ASC, ec.date_echeance ASC";
			$echeances=execSql($sql);
			//echo $sql;
			
			$tab_eleves = array();
			
			$total_eleves_general = 0;
			$total_a_payer_general = 0.0;
			$total_reste_a_payer_general = 0.0;
			$total_encaisse_general = 0.0;
			$total_impaye_general = 0.0;
			$elev_id_courant = 0;
			$pourcentage = 0;
			
			for($i=0; $i<$echeances->numRows(); $i++) {
				// Acces s l'enregistrement courant
				$res = $echeances->fetchInto($ligne_echeance, DB_FETCHMODE_DEFAULT, $i);
				if(($ligne_echeance[12] == '') OR ($ligne_echeance[8] < $ligne_echeance[12]))
				{
					// Ajouter un nouvel eleve si le ID est different du precedent
					if($ligne_echeance[0] != $elev_id_courant) {
						$total_eleves_general++;
						$tab_eleves[count($tab_eleves)] = array(
													'elev_id' => $ligne_echeance[0],
													'nom' => $ligne_echeance[1],
													'prenom' => $ligne_echeance[2],
													'echeances' => array(),
													'total_a_payer' => 0.0,
													'reste_a_payer' => 0.0,
													'encaisse' => 0.0,
													'impaye' => 0.0
												);
						$elev_id_courant = $ligne_echeance[0];
					}
					
					// Total a payer pour cet eleve et cette echeance
					$total_a_payer_pour_cet_eleve_echeance = $ligne_echeance[9];
					// Reste a payer pour cet eleve et cette echeance
					$reste_a_payer_pour_cet_eleve_echeance = reglement_reste_a_payer('echeance', $ligne_echeance[7]);
					// Encaisse pour cet eleve et cette echeance
					$encaisse_pour_cet_eleve_echeance = $total_a_payer_pour_cet_eleve_echeance - $reste_a_payer_pour_cet_eleve_echeance;
					// Impaye si il reste quelque chose a payer et que l'echeance est passee
					$impaye_pour_cet_eleve_echeance = 0.0;
					if($reste_a_payer_pour_cet_eleve_echeance > 0.0) {
						if(strtotime($ligne_echeance[8]) < strtotime(date('Y-m-d'))) {
							$impaye_pour_cet_eleve_echeance = $reste_a_payer_pour_cet_eleve_echeance;
						}
					}
					
					$tab_eleves[count($tab_eleves) - 1]['echeances'][count($tab_eleves[count($tab_eleves) - 1]['echeances'])] = array(
										'annee_scolaire' => $ligne_echeance[4],
										'libelle_classe' => $ligne_echeance[6],
										'date_echeance' => $ligne_echeance[8],
										'libelle_type_reglement' => $ligne_echeance[10],
										'total_a_payer_echeance' => $total_a_payer_pour_cet_eleve_echeance,
										'reste_a_payer_echeance' => $reste_a_payer_pour_cet_eleve_echeance,
										'encaisse_echeance' => $encaisse_pour_cet_eleve_echeance,
										'impaye_echeance' => $impaye_pour_cet_eleve_echeance
									);
					
					$tab_eleves[count($tab_eleves) - 1]['total_a_payer'] += $total_a_payer_pour_cet_eleve_echeance;
					$tab_eleves[count($tab_eleves) - 1]['reste_a_payer'] += $reste_a_payer_pour_cet_eleve_echeance;
					$tab_eleves[count($tab_eleves) - 1]['encaisse'] += $encaisse_pour_cet_eleve_echeance;
					$tab_eleves[count($tab_eleves) - 1]['impaye'] += $impaye_pour_cet_eleve_echeance;
					
					$total_a_payer_general += $total_a_payer_pour_cet_eleve_echeance;
					$total_reste_a_payer_general += $reste_a_payer_pour_cet_eleve_echeance;
					$total_encaisse_general += $encaisse_pour_cet_eleve_echeance;
					$total_impaye_general += $impaye_pour_cet_eleve_echeance;
					
					// Rechercher le type de reglement pour cette echeance
					$type_reglement_trouve = false;
					for($k=0; $k<count($tab_types_reglement); $k++) {
						if($tab_types_reglement[$k]['type_reglement_id'] == $ligne_echeance[11]) {
							$type_reglement_trouve = true;
							break;
						}
					}
					if($type_reglement_trouve) {
						$tab_types_reglement[$k]['a_payer'] += $total_a_payer_pour_cet_eleve_echeance;
						$tab_types_reglement[$k]['reste_a_payer'] += $reste_a_payer_pour_cet_eleve_echeance;
						$tab_types_reglement[$k]['encaisse'] += $encaisse_pour_cet_eleve_echeance;
					}
					
					$sql1 ="SELECT groupe_id, echeancier_id, montant ";
					$sql1.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
					$sql1.="WHERE echeancier_id = $ligne_echeance[7] ";
					$sql1.="ORDER BY groupe_id ";
					$groupes=execSql($sql1);
					// echo $sql1;
					$pourcentage = 0;
					if($reste_a_payer_pour_cet_eleve_echeance != 0){
						$pourcentage = $reste_a_payer_pour_cet_eleve_echeance / $total_a_payer_pour_cet_eleve_echeance;
					}
					for($v=0; $v <$groupes->numRows();$v++)
					{
						$resg = $groupes->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
					
						for($l=0; $l< count($tab_groupe_type);$l++)
						{
							if($tab_groupe_type[$l]['groupe_id'] == $ligne_groupe[0]) {
							
								$tab_groupe_type[$l]['total'] += $ligne_groupe[2];
								
								if($pourcentage != 0)
								{
									$temp = $ligne_groupe[2] * $pourcentage;
									$tab_groupe_type[$l]['reste_a_payer'] += $temp;
									$tab_groupe_type[$l]['encaisse'] += ($ligne_groupe[2] - $temp);
									
									
								}
								else
								{
										$tab_groupe_type[$l]['encaisse'] += $ligne_groupe[2];
									
								}
							}
						}
					}
				}
			}
			for($j=0;$j<count($tab_types_reglement);$j++)
			{
				$reglement_type_total += $tab_types_reglement[$j]['a_payer'];
				$reglement_type_reste += $tab_types_reglement[$j]['reste_a_payer'];
				$reglement_type_encaisse += $tab_types_reglement[$j]['encaisse'];
			}
			
			
			for($j=0;$j<count($tab_groupe_type);$j++)
			{
				$groupe_type_total += $tab_groupe_type[$j]['total'];
				$groupe_type_reste += $tab_groupe_type[$j]['reste_a_payer'];
				$groupe_type_encaisse += $tab_groupe_type[$j]['encaisse'];
			}
	}

	$nom_fichier_excel = 'editions_encaisses_impayes_' . date('Ymd') . '.xls';
	
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
		$xls->xlsWriteLabel($ligne_courante, 0,Nom);
		$xls->xlsWriteLabel($ligne_courante, 1,Prénom);
		$xls->xlsWriteLabel($ligne_courante, 2, ucfirst(LANG_FIN_GENE_011));
		$xls->xlsWriteLabel($ligne_courante, 3, ucfirst(LANG_FIN_CLAS_003));
		$xls->xlsWriteLabel($ligne_courante, 4, ucfirst(LANG_FIN_EENC_005));
		$xls->xlsWriteLabel($ligne_courante, 5, ucfirst(LANG_FIN_TREG_015));
		$xls->xlsWriteLabel($ligne_courante, 6, ucfirst(LANG_FIN_EENC_006));
		$xls->xlsWriteLabel($ligne_courante, 7, ucfirst(LANG_FIN_EENC_007));
		$xls->xlsWriteLabel($ligne_courante, 8, ucfirst(LANG_FIN_EENC_008));
		$xls->xlsWriteLabel($ligne_courante, 9, ucfirst(LANG_FIN_EENC_009));
		$xls->xlsWriteLabel($ligne_courante, 10, ucfirst(LANG_FIN_EENC_006));
		$xls->xlsWriteLabel($ligne_courante, 11, ucfirst(LANG_FIN_EENC_007));
		$xls->xlsWriteLabel($ligne_courante, 12, ucfirst(LANG_FIN_EENC_008));
		$xls->xlsWriteLabel($ligne_courante, 13, ucfirst(LANG_FIN_EENC_009));
		
	for($i=0; $i<count($tab_eleves); $i++) {
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0,strtoupper($tab_eleves[$i]['nom']));
		$xls->xlsWriteLabel($ligne_courante, 1,ucfirst($tab_eleves[$i]['prenom']));

		for($j=0; $j<count($tab_eleves[$i]['echeances']); $j++) {
			
			// Annee Scolaire
			$xls->xlsWriteLabel($ligne_courante, 2, ucfirst($tab_eleves[$i]['echeances'][$j]['annee_scolaire']));
			
			// Libelle classe
			$xls->xlsWriteLabel($ligne_courante, 3, ucfirst($tab_eleves[$i]['echeances'][$j]['libelle_classe']));
			
			// Date echeance
			$xls->xlsWriteLabel($ligne_courante, 4, ucfirst($tab_eleves[$i]['echeances'][$j]['date_echeance']));
			
			// Libelle type reglement
			$xls->xlsWriteLabel($ligne_courante, 5, ucfirst($tab_eleves[$i]['echeances'][$j]['libelle_type_reglement']));
			
			// Total a payer echeance
			$xls->xlsWriteLabel($ligne_courante, 6,  montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['total_a_payer_echeance']));
			
			// Reste a payer echeance
			$xls->xlsWriteLabel($ligne_courante, 7,  montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['reste_a_payer_echeance']));
			
			// Encaisse echeance
			$xls->xlsWriteLabel($ligne_courante, 8,  montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['encaisse_echeance']));
			
			// Impaye echeance
			$xls->xlsWriteLabel($ligne_courante, 9, montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['impaye_echeance']));
			$ligne_courante++;
		}
		$ligne_courante--;
		// Total a payer echeance
		$xls->xlsWriteLabel($ligne_courante, 10,  montant_depuis_bdd($tab_eleves[$i]['total_a_payer']));
		
		// Reste a payer echeance
		$xls->xlsWriteLabel($ligne_courante, 11,  montant_depuis_bdd($tab_eleves[$i]['reste_a_payer']));
		
		// Encaisse echeance
		$xls->xlsWriteLabel($ligne_courante, 12,  montant_depuis_bdd($tab_eleves[$i]['encaisse']));
		
		// Impaye echeance
		$xls->xlsWriteLabel($ligne_courante, 13, montant_depuis_bdd($tab_eleves[$i]['impaye']));

	}
	
	$ligne_courante=$ligne_courante+4;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_005));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($total_eleves_general));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EENC_006));
	$xls->xlsWriteLabel($ligne_courante, 1,  montant_depuis_bdd($total_a_payer_general));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EENC_007));
	$xls->xlsWriteLabel($ligne_courante, 1,  montant_depuis_bdd($total_reste_a_payer_general));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EENC_008));
	$xls->xlsWriteLabel($ligne_courante, 1,  montant_depuis_bdd($total_encaisse_general));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EENC_009));
	$xls->xlsWriteLabel($ligne_courante, 1,  montant_depuis_bdd($total_impaye_general));
	
	$ligne_courante=$ligne_courante+4;
	
	$xls->xlsWriteLabel($ligne_courante, 1,ucfirst(LANG_FIN_EENC_006));
	$xls->xlsWriteLabel($ligne_courante, 2,ucfirst(LANG_FIN_EENC_007));
	$xls->xlsWriteLabel($ligne_courante, 3,ucfirst(LANG_FIN_EENC_008));
	
	for($k=0; $k<count($tab_types_reglement); $k++) {
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0,ucfirst($tab_types_reglement[$k]['libelle']));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($tab_types_reglement[$k]['a_payer']));
	$xls->xlsWriteLabel($ligne_courante, 2, montant_depuis_bdd($tab_types_reglement[$k]['reste_a_payer']));
	$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($tab_types_reglement[$k]['encaisse']));
	}
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 1,montant_depuis_bdd($reglement_type_total));
	$xls->xlsWriteLabel($ligne_courante, 2,montant_depuis_bdd($reglement_type_reste));
	$xls->xlsWriteLabel($ligne_courante, 3,montant_depuis_bdd($reglement_type_encaisse));
	
	$ligne_courante=$ligne_courante+4;
	
	$xls->xlsWriteLabel($ligne_courante, 1,ucfirst(LANG_FIN_EENC_010));
	$xls->xlsWriteLabel($ligne_courante, 2,ucfirst(LANG_FIN_EENC_007));
	$xls->xlsWriteLabel($ligne_courante, 3,ucfirst(LANG_FIN_EENC_008));
	
	for($k=0; $k<count($tab_groupe_type); $k++) {
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0,ucfirst($tab_groupe_type[$k]['libelle']));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($tab_groupe_type[$k]['total']));
	$xls->xlsWriteLabel($ligne_courante, 2, montant_depuis_bdd($tab_groupe_type[$k]['reste_a_payer']));
	$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($tab_groupe_type[$k]['encaisse']));
	}
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($groupe_type_total));
	$xls->xlsWriteLabel($ligne_courante, 2, montant_depuis_bdd($groupe_type_reste));
	$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($groupe_type_encaisse));
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