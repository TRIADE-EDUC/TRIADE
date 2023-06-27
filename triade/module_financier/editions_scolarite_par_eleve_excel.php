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
	$operation = lire_parametre('operation', 'rechercher', 'REQUEST');
	$annee_scolaire = lire_parametre('annee_scolaire', annee_scolaire_courante(), 'POST');
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
	
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	if($annees_scolaires->numRows() > 0) {
		if($annee_scolaire == '') {
			$res = $annees_scolaires->fetchInto($ligne_annee, DB_FETCHMODE_DEFAULT, 0);
			$annee_scolaire = $ligne_annee[0];
		}
	}
	/*
	if($annees_scolaires->numRows() > 0) {
		if($annee_scolaire == '') {
			$annee_scolaire = annee_scolaire_courante();
			$annee_trouvee = false;
			for($i=0; $i<$annees_scolaires->numRows(); $i++) {
				$res = $annees_scolaires->fetchInto($ligne_annee, DB_FETCHMODE_DEFAULT, $i);
				if($ligne_annee[0] == $annee_scolaire) {
					$annee_trouvee = true;
				}
			}
			if(!$annee_trouvee) {
				$annee_scolaire = '';
			}
		}
	}
	*/
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="WHERE i.annee_scolaire = '" . $annee_scolaire . "' ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	// Initialisation sur changement d'annee scolaire
	if($operation == "reload_annee_scolaire") {
		$code_class = 0;
	}

	if($operation != '')
	{
	
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
				
				
		// Rechercher la liste des inscriptions
		$sql ="SELECT c.code_class, c.libelle, e.elev_id, e.nom, e.prenom, i.inscription_id ";
		$sql.="FROM (".FIN_TAB_CLASSES." c ";
		$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class) ";
		$sql.="INNER JOIN ".FIN_TAB_ELEVES." e ON e.elev_id = i.elev_id ";
		$sql.="WHERE 1 = 1 ";
		if($annee_scolaire != '') {
			$sql.="AND i.annee_scolaire = '" . $annee_scolaire . "' ";
		}
		if($code_class != '0') {
			$sql.="AND i.code_class = " . $code_class . " ";
		}
		//$sql.="GROUP BY c.code_class, c.libelle ";
		if($ordre_tri == 'libelle_classe') {
			$sql.="ORDER BY c.libelle, e.nom, e.prenom";
		} else {
			$sql.="ORDER BY e.nom, e.prenom, c.libelle";
		}
		$inscriptions=execSql($sql);
	//echo $sql;
		$tab_classes = array();
		$code_class_courant = 0;
		$total_classes_general = 0;
		$total_eleves_general = 0;
		$total_scolarite_general = 0.0;
		$total_reste_a_payer_general = 0.0;
		$total_groupe = 0.0;
		for($i=0; $i<$inscriptions->numRows(); $i++) {
			// Acces s l'enregistrement courant
			$res = $inscriptions->fetchInto($ligne_inscription, DB_FETCHMODE_DEFAULT, $i);
			// Ajouter une nouvelle classe si le ID est different du precedent
			if($ligne_inscription[0] != $code_class_courant) {
				$total_classes_general++;
				$tab_classes[count($tab_classes)] = array(
											'code_class' => $ligne_inscription[0],
											'libelle_classe' => $ligne_inscription[1],
											'eleves' => array(),
											'total_scolarite' => 0.0,
											'total_reste_a_payer' => 0.0,
											'total_encaisse' => 0.0
										);
				$code_class_courant = $ligne_inscription[0];
			}
			$total_eleves_general++;
			$frais_pour_cet_eleve = inscription_total_frais($ligne_inscription[5]);
			$reste_a_payer_pour_cet_eleve = reglement_reste_a_payer('inscription', $ligne_inscription[5]);
			$encaisse_pour_cet_eleve = $frais_pour_cet_eleve - $reste_a_payer_pour_cet_eleve;
			$tab_classes[count($tab_classes) - 1]['eleves'][count($tab_classes[count($tab_classes) - 1]['eleves'])] = array(
								'elev_id' => $ligne_inscription[2],
								'nom' => $ligne_inscription[3],
								'prenom' => $ligne_inscription[4],
								'total_scolarite' => $frais_pour_cet_eleve,
								'total_reste_a_payer' => $reste_a_payer_pour_cet_eleve,
								'total_encaisse' => $encaisse_pour_cet_eleve
							);
			
			
			$sql1 ="SELECT groupe_id, echeancier_id, montant ";
			$sql1.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
			$sql1.="WHERE inscription_id = $ligne_inscription[5] ";
			$sql1.="ORDER BY echeancier_id ";
			$groupes=execSql($sql1);
			// echo $sql1;
			
			$pourcentage = 0.0;
			if($reste_a_payer_pour_cet_eleve != 0){
				$pourcentage = $reste_a_payer_pour_cet_eleve /$frais_pour_cet_eleve;
			}
			for($v=0; $v <$groupes->numRows();$v++)
			{
				$resg = $groupes->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
				
				for($l=0; $l< count($tab_groupe_type);$l++)
				{
					if($tab_groupe_type[$l]['groupe_id'] == $ligne_groupe[0]) {
						
						$tab_groupe_type[$l]['total'] += $ligne_groupe[2];
						$total_groupe += $ligne_groupe[2];
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
			
			$tab_classes[count($tab_classes) - 1]['total_scolarite'] += $frais_pour_cet_eleve;
			$total_scolarite_general += $frais_pour_cet_eleve;

			$tab_classes[count($tab_classes) - 1]['total_reste_a_payer'] += $reste_a_payer_pour_cet_eleve;
			$total_reste_a_payer_general += $reste_a_payer_pour_cet_eleve;
			
			$tab_classes[count($tab_classes) - 1]['total_encaisse'] += $encaisse_pour_cet_eleve;
			$total_encaisse_general += $encaisse_pour_cet_eleve;
		
		}
		for($j=0;$j<count($tab_groupe_type);$j++)
		{
			$groupe_type_total += $tab_groupe_type[$j]['total'];
			$groupe_type_reste += $tab_groupe_type[$j]['reste_a_payer'];
			$groupe_type_encaisse += $tab_groupe_type[$j]['encaisse'];
		}
	}	

	$nom_fichier_excel = 'editions_scolarite_eleve_' . date('Ymd') . '.xls';
	
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
	for($i=0; $i<count($tab_classes); $i++) {
		$ligne_courante=$ligne_courante+2;
		$xls->xlsWriteLabel($ligne_courante, 0,LANG_FIN_CLAS_003);
		$xls->xlsWriteLabel($ligne_courante, 1,$tab_classes[$i]['libelle_classe']);
		
		$ligne_courante=$ligne_courante+2;
		
		$xls->xlsWriteLabel($ligne_courante, 1,LANG_FIN_ELEV_002);
		$xls->xlsWriteLabel($ligne_courante, 2,LANG_FIN_GENE_036);
		$xls->xlsWriteLabel($ligne_courante, 3,LANG_FIN_EENC_008);
		$xls->xlsWriteLabel($ligne_courante, 4,LANG_FIN_GENE_034);
		
		for($j=0; $j<count($tab_classes[$i]['eleves']); $j++) {
		$ligne_courante++;
		$nom = strtoupper($tab_classes[$i]['eleves'][$j]['nom']);	
		$prenom =  ucfirst($tab_classes[$i]['eleves'][$j]['prenom']);
		$nomcomplet = $nom . " " .$prenom;
		$xls->xlsWriteLabel($ligne_courante, 1,$nomcomplet);
		$xls->xlsWriteLabel($ligne_courante, 2,montant_depuis_bdd($tab_classes[$i]['eleves'][$j]['total_scolarite']));
		$xls->xlsWriteLabel($ligne_courante, 3,montant_depuis_bdd($tab_classes[$i]['eleves'][$j]['total_encaisse']));
		$xls->xlsWriteLabel($ligne_courante, 4,montant_depuis_bdd($tab_classes[$i]['eleves'][$j]['total_reste_a_payer']));
		}
		
		$ligne_courante=$ligne_courante+4;
		
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_003));
		$xls->xlsWriteLabel($ligne_courante, 1, count($tab_classes[$i]['eleves']));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_004));
		$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($tab_classes[$i]['total_scolarite']));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EENC_008));
		$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($tab_classes[$i]['total_encaisse']));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_GENE_034));
		$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($tab_classes[$i]['total_reste_a_payer']));
	}
		$ligne_courante=$ligne_courante+4;
		
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_008));
		$xls->xlsWriteLabel($ligne_courante, 1, ucfirst($total_classes_general));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_005));
		$xls->xlsWriteLabel($ligne_courante, 1, ucfirst($total_eleves_general));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_006));
		$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($total_scolarite_general));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EENC_008));
		$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($total_encaisse_general));
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_ESCO_007));
		$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($total_reste_a_payer_general));
		
		$ligne_courante=$ligne_courante+4;
		
		$xls->xlsWriteLabel($ligne_courante, 1,ucfirst(LANG_FIN_EENC_010));
		$xls->xlsWriteLabel($ligne_courante, 2,ucfirst(LANG_FIN_EENC_007));
		$xls->xlsWriteLabel($ligne_courante, 3,ucfirst(LANG_FIN_EENC_008));
		
		for($k=0; $k<count($tab_groupe_type); $k++) {
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0,ucfirst($tab_groupe_type[$k]['libelle']));
		$xls->xlsWriteLabel($ligne_courante, 1,montant_depuis_bdd($tab_groupe_type[$k]['total']));
		$xls->xlsWriteLabel($ligne_courante, 2,montant_depuis_bdd($tab_groupe_type[$k]['reste_a_payer']));
		$xls->xlsWriteLabel($ligne_courante, 3,montant_depuis_bdd($tab_groupe_type[$k]['encaisse']));
		}
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 1,montant_depuis_bdd($groupe_type_total));
		$xls->xlsWriteLabel($ligne_courante, 2,montant_depuis_bdd($groupe_type_reste));
		$xls->xlsWriteLabel($ligne_courante, 3,montant_depuis_bdd($groupe_type_encaisse));
		
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