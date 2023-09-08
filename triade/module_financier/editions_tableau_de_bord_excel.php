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
	$operation = lire_parametre('operation', '', 'REQUEST');
	$annee_scolaire = lire_parametre('annee_scolaire', 'vide', 'REQUEST');
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	//***************************************************************************

	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	
		// Verifier si l'annee scolaire est dans la liste
		if($annee_scolaire != 'vide') {
			$annee_trouvee = false;
			for($i=0; $i<$annees_scolaires->numRows(); $i++) {
				$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
				if($ligne[0] == $annee_scolaire) {
					$annee_trouvee = true;
					$temp = true;
					break;
				}
			}
			if(!$annee_trouvee) {
				$annee_scolaire = 'vide';
			}
		}
		
	if($annee_scolaire != 'vide')
	{	
		$donnees = array();
		
		$donnees['nb_inscriptions'] = 0;
		$donnees['montant_encaisse'] = 0.0;
		$donnees['montant_a_encaisser'] = 0.0;
		$donnees['types_frais'] = array();
		$donnees['groupes_frais'] = array();	
		
		
		// Rechercher la liste des types de frais
		$sql ="SELECT type_frais_id, libelle  ";
		$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ";
		$sql.="ORDER BY libelle";
		$frais=execSql($sql);
		for($i=0; $i<$frais->numRows(); $i++) {
			$res = $frais->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
			$donnees['types_frais'][count($donnees['types_frais'])] = array(
																			'type_frais_id' => $ligne[0],
																			'libelle' => $ligne[1],
																			'nombre' => 0,
																			'montant' => 0.0,
																			'reste_a_payer' => 0.0,
																			'encaisse' => 0.0
																			);
		}
		
		
		// Rechercher la liste des groupes de frais
		$sql ="SELECT groupe_id, libelle ";
		$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
		$sql.="ORDER BY groupe_id";
		$groupe_type=execSql($sql);
				
		for($i=0; $i<$groupe_type->numRows(); $i++) {
			// Acces s l'enregistrement courant
			$res1 = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
			
			$donnees['groupes_frais'][count($donnees['groupes_frais'])] = array(
																			'groupe_id' => $ligne_groupe_type[0],
																			'libelle' => $ligne_groupe_type[1],
																			'nombre' => 0,
																			'montant' => 0.0,
																			'reste_a_payer' => 0.0,
																			'encaisse' => 0.0
																			);
		}

		
		if($annees_scolaires->numRows() > 0) {
			// Rechercher les inscriptions
			$sql ="SELECT inscription_id ";
			$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
			$sql.="WHERE annee_scolaire = '" . $annee_scolaire . "' ";
			$sql.="ORDER BY annee_scolaire ";		

			$inscriptions=execSql($sql);
			
			$donnees['nb_inscriptions'] = $inscriptions->numRows();

			for($i=0; $i<$inscriptions->numRows(); $i++) {
				$res = $inscriptions->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
				$total_frais = inscription_total_frais($ligne[0], -1);
				$total_reglements_realises = 0.0;
				$frais_pour_cet_eleve = 0.0;
				$reste_a_payer_pour_cet_eleve = 0.0;

				// Rechercher les reglements pour cette inscription
				$sql ="SELECT SUM(r.montant) as total_reglement ";
				$sql.="FROM ".FIN_TAB_ECHEANCIER." e ";
				$sql.="INNER JOIN ".FIN_TAB_REGLEMENT." r ON e.echeancier_id = r.echeancier_id ";
				$sql.="WHERE e.inscription_id = " . $ligne[0] . " ";
				$sql.="AND r.realise = 1 ";
				$total_reglement=execSql($sql);
				if($total_reglement->numRows()) {
					$res = $total_reglement->fetchInto($ligne_total_reglement, DB_FETCHMODE_DEFAULT, 0);
					$total_reglements_realises = $ligne_total_reglement[0];
				}

				$donnees['montant_encaisse'] += $total_reglements_realises;
				
				$donnees['montant_a_encaisser'] += $total_frais - $total_reglements_realises;
				
				
				// Rechercher la liste des frais 
				$sql ="SELECT type_frais_id, montant  ";
				$sql.="FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
				$sql.="WHERE inscription_id = " . $ligne[0] . " ";
				$sql.="AND ((optionnel = 0) OR (optionnel = 1 AND selectionne = 1)) ";
				$frais=execSql($sql);
				for($k=0; $k<$frais->numRows(); $k++) {
					$res = $frais->fetchInto($ligne_frais, DB_FETCHMODE_DEFAULT, $k);
					
					// Rechercher la position du frais dans la liste<br>
					// Si on le trouve, on met a jour ses donnees


					for($l=0; $l<count($donnees['types_frais']); $l++) {

						if($donnees['types_frais'][$l]['type_frais_id'] == $ligne_frais[0]) {

							$donnees['types_frais'][$l]['nombre']++;
							$donnees['types_frais'][$l]['montant'] += $ligne_frais[1];
							break;
						}
					}
				}
				
				$frais_pour_cet_eleve = inscription_total_frais($ligne[0]);
				$reste_a_payer_pour_cet_eleve = reglement_reste_a_payer('inscription', $ligne[0]);
				
				
				// Rechercher la liste des groupes de frais 
				
				$sql ="SELECT ti.type_frais_id, fi.montant, ti.groupe_id  ";
				$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ti INNER JOIN ".FIN_TAB_FRAIS_INSCRIPTION." fi ";
				$sql.="ON ti.type_frais_id = fi.type_frais_id ";
				$sql.="WHERE fi.inscription_id = " . $ligne[0] . " ";
				$sql.="AND ((fi.optionnel = 0) OR (fi.optionnel = 1 AND fi.selectionne = 1)) ";

				$groupe1=execSql($sql);
				$pourcentage = 0.0;
				if($reste_a_payer_pour_cet_eleve != 0){
					$pourcentage = $reste_a_payer_pour_cet_eleve /$frais_pour_cet_eleve;
				}
				for($v=0; $v<$groupe1->numRows();$v++)
				{
					$resg = $groupe1->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
					
					for($l=0; $l<count($donnees['groupes_frais']);$l++)
					{
						if($donnees['groupes_frais'][$l]['groupe_id'] == $ligne_groupe[2]) {
							
							$donnees['groupes_frais'][$l]['nombre'] ++;
							$donnees['groupes_frais'][$l]['montant'] += $ligne_groupe[1];
							
							if($pourcentage != 0)
							{
								$temp = $ligne_groupe[1] * $pourcentage;
								$donnees['groupes_frais'][$l]['reste_a_payer'] += $temp;
								$donnees['groupes_frais'][$l]['encaisse'] += ($ligne_groupe[1] - $temp);
							}
							else
							{
								$donnees['groupes_frais'][$l]['encaisse'] += $ligne_groupe[1];		
							}
						}
					}
				}
				
				
				
				
			}
		}
	}

	$nom_fichier_excel = 'editions_tableau_de_bord_' . date('Ymd') . '.xls';
	
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
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EBOR_005));
	$xls->xlsWriteLabel($ligne_courante, 1, $donnees['nb_inscriptions']);
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EBOR_006));
	$xls->xlsWriteLabel($ligne_courante, 1,montant_depuis_bdd($donnees['montant_encaisse']));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_EBOR_007));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($donnees['montant_a_encaisser']));
	$ligne_courante++;
	$montant_total = $donnees['montant_encaisse'] + $donnees['montant_a_encaisser'];
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_GENE_013));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($montant_total));
	$ligne_courante=$ligne_courante+2;
	
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_FBAR_003));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 1, ucfirst(LANG_FIN_FBAR_004));
	$xls->xlsWriteLabel($ligne_courante, 2, ucfirst(LANG_FIN_EBOR_008));
	$xls->xlsWriteLabel($ligne_courante, 3, ucfirst(LANG_FIN_GENE_013));
	
	$total_des_frais = 0.0;
	
	for($i=0; $i<count($donnees['types_frais']); $i++) {
		$total_des_frais += $donnees['types_frais'][$i]['montant'];
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 1, $donnees['types_frais'][$i]['libelle']);
		$xls->xlsWriteLabel($ligne_courante, 2, $donnees['types_frais'][$i]['nombre']);
		$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($donnees['types_frais'][$i]['montant']));
	}
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 2, ucfirst(LANG_FIN_GENE_024));
	$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($total_des_frais));
	
	
	$ligne_courante=$ligne_courante+2;
	$groupe_encaisse = 0.0;
	$groupe_reste_a_payer = 0.0;
	$xls->xlsWriteLabel($ligne_courante, 0, ucfirst(LANG_FIN_FBAR_013));
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 1, ucfirst(LANG_FIN_GROUPE_014));
	$xls->xlsWriteLabel($ligne_courante, 2, ucfirst(LANG_FIN_EBOR_008));
	$xls->xlsWriteLabel($ligne_courante, 3, ucfirst(LANG_FIN_GENE_013));
	$xls->xlsWriteLabel($ligne_courante, 4, ucfirst(LANG_FIN_EENC_008));
	$xls->xlsWriteLabel($ligne_courante, 5, ucfirst(LANG_FIN_GENE_034));
	
	$total_des_groupes= 0.0;
	$groupe_encaisse = 0.0;
	$groupe_reste_a_payer = 0.0;
	for($i=0; $i<count($donnees['groupes_frais']); $i++) {
		$total_des_groupes += $donnees['groupes_frais'][$i]['montant'];
		$groupe_encaisse += $donnees['groupes_frais'][$i]['encaisse'];
		$groupe_reste_payer += $donnees['groupes_frais'][$i]['reste_a_payer'];
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 1, $donnees['groupes_frais'][$i]['libelle']);
		$xls->xlsWriteLabel($ligne_courante, 2, $donnees['groupes_frais'][$i]['nombre']);
		$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($donnees['groupes_frais'][$i]['montant']));
		$xls->xlsWriteLabel($ligne_courante, 4, montant_depuis_bdd($donnees['groupes_frais'][$i]['encaisse']));
		$xls->xlsWriteLabel($ligne_courante, 5, montant_depuis_bdd($donnees['groupes_frais'][$i]['reste_a_payer']));
	}
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 2, ucfirst(LANG_FIN_GENE_024));
	$xls->xlsWriteLabel($ligne_courante, 3, montant_depuis_bdd($total_des_groupes));
	$xls->xlsWriteLabel($ligne_courante, 4, montant_depuis_bdd($groupe_encaisse));
	$xls->xlsWriteLabel($ligne_courante, 5, montant_depuis_bdd($groupe_reste_payer));
	
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