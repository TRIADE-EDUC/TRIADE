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
	$date_limite = lire_parametre('date_limite', '', 'POST');
	$ordre_tri = lire_parametre('ordre_tri', 'nom_eleve', 'POST');
	//***************************************************************************
	
	$tab_groupe_type = array();
	
	$sql ="SELECT groupe_id, libelle ";
	$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
	$sql.="ORDER BY groupe_id";
	$groupe_type=execSql($sql);
			
	for($i=0; $i<$groupe_type->numRows(); $i++) {
		
		$res = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
		$tab_groupe_type[count($tab_groupe_type)] = array(
													'groupe_id' => $ligne_groupe_type[0],
													'libelle' => $ligne_groupe_type[1],
													'total' => 0.0,
													'reste_a_payer' => 0.0,
													'encaisse' => 0.0
													);
	}
	
	// Initialiser la date limite si elle est vide
	if($date_limite == '') {
		$date_limite = date('d/m/Y');
	}
	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	$tab_echeances = array();
	if($operation == 'rechercher') {

		// Rechercher la liste des echeances
		$sql  = "SELECT el.elev_id, el.nom, el.prenom, ec.echeancier_id, ec.date_echeance, ec.montant, ec.numero_rib, cl.code_class, cl.libelle, i.annee_scolaire, i.date_depart ";
		$sql .= "FROM ".FIN_TAB_ECHEANCIER." ec ";
		$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON ec.inscription_id = i.inscription_id ";
		$sql .= "INNER JOIN ".FIN_TAB_ELEVES." el ON i.elev_id = el.elev_id ";
		$sql .= "INNER JOIN ".FIN_TAB_CLASSES." cl ON i.code_class = cl.code_class ";
		$sql .= "WHERE ec.date_echeance <= '" . date_vers_bdd($date_limite) . "' ";
		$sql .= "AND ec.type_reglement_id = " . $g_tab_type_reglement_id['prelevement'] . " ";
		$sql .= "AND ec.type <> 2 ";  // => Ne pas inclure les remises exceptionnelles 
		if($ordre_tri == 'nom_eleve') {
			$sql .= "ORDER BY el.nom ASC, el.prenom ASC, ec.date_echeance ASC ";
		} else {
			$sql .= "ORDER BY ec.date_echeance ASC, el.nom ASC, el.prenom ASC ";
		}
		// 20100708 - AP : Maintenant on ordone par date d'echeance, puis par nom de l'eleve
		//$sql .= "ORDER BY el.nom, el.prenom, ec.date_echeance ASC";
		
		//echo $sql;
		$echeances = execSql($sql);
		if($echeances->numRows() > 0) {
		
			// Recuperer les infos de chaque echeance a traiter
			for($i=0; $i<$echeances->numRows(); $i++) {
			
				$res = $echeances->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);

				// 20100805 - AP : on doit verifier que l'echeance n'est pas posterieure a la date de depart 
				//                 (date de sortie)
				$posterieure_date_sortie = false;
				$date_echeance_tmp = trim($ligne[4]);
				$date_echeance_tmp = str_replace("-", "", $date_echeance_tmp);
				$date_depart_tmp = trim($ligne[10]);
				$date_depart_tmp = str_replace("-", "", $date_depart_tmp);
				if($date_depart_tmp != '' && $date_echeance_tmp > $date_depart_tmp) {
					$posterieure_date_sortie = true;
				}
				
				if(!$posterieure_date_sortie) {
				
					// 20100708 - AP : Maintenant les echeances sont triees par date : il faut donc rechercher la 
					// position ou inserer chaque nouvelle echeance trouvee
				
					// Verifier si on a deja une echeance pour le meme eleve
					$derniere_position = -1;
					for($j=0; $j<count($tab_echeances); $j++) {
						// Il y a deja l'eleve dans le tableau
						if(trim($tab_echeances[$j]["eleves_elev_id"]) == trim($ligne[0])) {
							$derniere_position = $j;
							// Chercher la derniere position pour cet eleve si on est pas deja a la fin du tableau
							if($derniere_position != (count($tab_echeances) - 1)) {
								for($k=$derniere_position; $k<count($tab_echeances); $k++) {
									if(trim($tab_echeances[$k]["eleves_elev_id"]) != trim($ligne[0])) {
										break;
									}
									$derniere_position = $k;
								}
							}
							break;
						}
						
					}
				
					// Si aucune position trouvee (eleve pas dans le tableau) ou bien si 
					// c'est le dernier element du tableau , on ajoute a la fin
					if($derniere_position == -1 || $derniere_position == (count($tab_echeances) - 1)) {
						$nouvelle_position = count($tab_echeances);
					} else {
						// => Dans ce cas, on va ajouter au milieu du tableau
						
						// Decaler les elements de la fin du tableau (une position vers l'exterieur)
						for($j=(count($tab_echeances) - 1); $j>$derniere_position; $j--) {
							$tab_echeances[$j + 1] = $tab_echeances[$j];
						}
						// La nouvelle position est celle de l'emplacement maintenant libre.
						$nouvelle_position = $derniere_position + 1;
					}
					
					
					// Recupere la position du RIB selectionne (liste deroulante)
					$id_select = "numero_rib_" . $ligne[0] . "_" . $ligne[3];
					$numero_rib = lire_parametre($id_select, '', 'POST');
	
					// Recuperer la ligne du resultat
						
					$reste_a_payer = reglement_reste_a_payer('echeance', $ligne[3]);	
					$tab_echeances[$nouvelle_position] = array(
														"eleves_elev_id" => $ligne[0],
														"eleves_nom" => $ligne[1],
														"eleves_prenom" => $ligne[2],
														"echeancier_echeancier_id" => $ligne[3],
														"echeancier_date_echeance" => $ligne[4],
														"echeancier_montant" => $ligne[5],
														"echeancier_numero_rib" => $numero_rib,
														"classes_code_class" => $ligne[7],
														"classes_libelle" => $ligne[8],
														"inscription_annee_scolaire" => $ligne[9],
														"reste_a_payer" => $reste_a_payer
														);
				}
			}

			// Cumuler les echeances par eleve
			if(count($tab_echeances) > 0) {
				$tab_echeances_tmp = $tab_echeances;
				$tab_echeances = array();
				$elev_id_courant = 0;
				for($i=0; $i<count($tab_echeances_tmp); $i++) {
					// Verifier que l'echeance n'a pas encore ete completement payee
					if($tab_echeances_tmp[$i]["reste_a_payer"] > 0) {
						if($elev_id_courant != $tab_echeances_tmp[$i]["eleves_elev_id"]) {
							// => Ajouter un nouvel enregistrement avec une nouvelle echeance
							
							// Verifier si l'eleve a un RIB
							$sql  = "SELECT rib_id ";
							$sql .= "FROM ".FIN_TAB_RIB." ";
							$sql .= "WHERE elev_id = " . $tab_echeances_tmp[$i]["eleves_elev_id"];
							//echo $sql;
							$rib=execSql($sql);
							if($rib->numRows()) {
								$rib_existe = true;
							} else {
								$rib_existe = false;
							}
							
							$echeancier = array();
							$echeancier[0] = array(
									"echeancier_echeancier_id" => $tab_echeances_tmp[$i]["echeancier_echeancier_id"],
									"echeancier_date_echeance" => $tab_echeances_tmp[$i]["echeancier_date_echeance"],
									"echeancier_montant" => $tab_echeances_tmp[$i]["echeancier_montant"],
									"echeancier_numero_rib" => $tab_echeances_tmp[$i]["echeancier_numero_rib"],
									"reste_a_payer" => $tab_echeances_tmp[$i]["reste_a_payer"]
									);
							$tab_echeances[count($tab_echeances)] = array(
									"eleves_elev_id" => $tab_echeances_tmp[$i]["eleves_elev_id"],
									"eleves_nom" => $tab_echeances_tmp[$i]["eleves_nom"],
									"eleves_prenom" => $tab_echeances_tmp[$i]["eleves_prenom"],
									"echeancier" => $echeancier,
									"classes_code_class" => $tab_echeances_tmp[$i]["classes_code_class"],
									"classes_libelle" => $tab_echeances_tmp[$i]["classes_libelle"],
									"inscription_annee_scolaire" => $tab_echeances_tmp[$i]["inscription_annee_scolaire"],
									"total" => $tab_echeances_tmp[$i]["reste_a_payer"],
									"rib_existe" => $rib_existe
									);
							$elev_id_courant = $tab_echeances_tmp[$i]["eleves_elev_id"];
						} else {
							// => Ajouter une nouvelle echeance dans l'enregistrement precedent
							
							// Recuperer les donnees de l'enregistrement precedent
							$echeancier = $tab_echeances[count($tab_echeances)-1]["echeancier"];
							$total = $tab_echeances[count($tab_echeances)-1]["total"];
							
							// Ajouter
							$echeancier[count($echeancier)] = array(
									"echeancier_echeancier_id" => $tab_echeances_tmp[$i]["echeancier_echeancier_id"],
									"echeancier_date_echeance" => $tab_echeances_tmp[$i]["echeancier_date_echeance"],
									"echeancier_montant" => $tab_echeances_tmp[$i]["echeancier_montant"],
									"echeancier_numero_rib" => $tab_echeances_tmp[$i]["echeancier_numero_rib"],
									"reste_a_payer" => $tab_echeances_tmp[$i]["reste_a_payer"]
									);
							$total += $tab_echeances_tmp[$i]["reste_a_payer"];
							
							// Remettre les infos dans l'enregistrement precedent
							$tab_echeances[count($tab_echeances)-1]["echeancier"] = $echeancier;
							$tab_echeances[count($tab_echeances)-1]["total"] = $total;
							
						}
						
						$temp_ech =$tab_echeances_tmp[$i]["echeancier_echeancier_id"];

						$sql1 ="SELECT groupe_id, echeancier_id, montant ";
						$sql1.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
						$sql1.="WHERE echeancier_id = $temp_ech ";
						$sql1.="ORDER BY groupe_id ";
						$groupes=execSql($sql1);
						// echo $sql1;
						$pourcentage = 0;
						if($tab_echeances_tmp[$i]["reste_a_payer"] != 0){
							$pourcentage = $tab_echeances_tmp[$i]["reste_a_payer"] / $tab_echeances_tmp[$i]["echeancier_montant"];
						}
						for($v=0; $v <$groupes->numRows();$v++)
						{
							$resg = $groupes->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
						
							for($l=0; $l< count($tab_groupe_type);$l++)
							{
								if($tab_groupe_type[$l]['groupe_id'] == $ligne_groupe[0]) {
									if($pourcentage != 0)
									{
										$temp = $ligne_groupe[2] * $pourcentage;
										$tab_groupe_type[$l]['reste_a_payer'] += $temp;
									}
								}
							}
						}
					}
				}
				for($l=0; $l< count($tab_groupe_type);$l++)
				{
					$total_groupe_type += $tab_groupe_type[$l]['reste_a_payer'];
				}
			}
		}
	}
	//***************************************************************************
	//print_r($tab_echeances);
	//exit;
	
	$nom_fichier_excel = 'prelevements_' . date('Ymd') . '.xls';
	
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
	
	$colonne_courante = -1;

	$colonne_courante++;
	//$s = preg_replace('@<b[^>]*>(.*?)</b>@i','*$1*','<b>#</b>'); 
	$xls->xlsWriteLabel(0, $colonne_courante, '#');
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_CLAS_003));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_GENE_011));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_ELEV_004));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_ELEV_005));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_GENE_030));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_GENE_034));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_RIB_017));
	$colonne_courante++;
	$xls->xlsWriteLabel(0, $colonne_courante, ucfirst(LANG_FIN_GENE_024));
	

	$ligne_courante = 0;
	for($i=0; $i<count($tab_echeances); $i++) {
		
		 $ligne_courante++;
	
		 $lignes_pour_eleve = count($tab_echeances[$i]['echeancier']);
		 
		 if($tab_echeances[$i]['rib_existe']) {
			$rib_existe = 1;
		 } else {
			$rib_existe = 0;
		 }
		 
		// Recuperer la liste des RIB pour l'eleve courant
		$tab_rib = liste_rib($tab_echeances[$i]['eleves_elev_id']);
		
		$colonne_courante = -1;
		
		// ID
		$colonne_courante++;
		$xls->xlsWriteNumber($ligne_courante, $colonne_courante, $i+1);
		
		// Classe
		$colonne_courante++;
		$xls->xlsWriteLabel($ligne_courante, $colonne_courante, ucfirst($tab_echeances[$i]['classes_libelle']));
	
		// Annee_scolaire
		$colonne_courante++;
		$xls->xlsWriteLabel($ligne_courante, $colonne_courante, ucfirst($tab_echeances[$i]['inscription_annee_scolaire']));
	
		// Prenom
		$colonne_courante++;
		$xls->xlsWriteLabel($ligne_courante, $colonne_courante, ucfirst($tab_echeances[$i]['eleves_prenom']));

		// Nom
		$colonne_courante++;
		$xls->xlsWriteLabel($ligne_courante, $colonne_courante, strtoupper($tab_echeances[$i]['eleves_nom']));

		// Date echeance
		$colonne_courante++;
		$xls->xlsWriteLabel($ligne_courante, $colonne_courante, date_depuis_bdd($tab_echeances[$i]['echeancier'][0]['echeancier_date_echeance']));

		// Reste a payer
		$colonne_courante++;
		$xls->xlsWriteNumber($ligne_courante, $colonne_courante, $tab_echeances[$i]['echeancier'][0]['reste_a_payer']);

		// RIB
		$valeur = $tab_echeances[$i]['echeancier'][0]['echeancier_numero_rib'];
		$rib_id_par_defaut = $valeur;
		if($valeur == 0) {
			$valeur = LANG_FIN_GENE_049;
		} else {
			$valeur = $tab_rib[$valeur - 1];
		}
		$colonne_courante++;
		$xls->xlsWriteLabel($ligne_courante, $colonne_courante, $valeur);
		
		// Total
		$colonne_courante++;
		$xls->xlsWriteNumber($ligne_courante, $colonne_courante, $tab_echeances[$i]['total']);

		// Affichage des lignes supplementaires pour l'eleve courant			
		for($j=1; $j<count($tab_echeances[$i]['echeancier']); $j++) {
			
			$ligne_courante++;
			
			$colonne_courante = -1;

			// ID
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante, '');
			
			// Classe
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante,  '');
		
			// Annee_scolaire
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante,  '');
		
			// Prenom
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante,  '');

			// Nom
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante,  '');

			// Date d'echeance
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante,  date_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['echeancier_date_echeance']));

			// Reste a payer
			$colonne_courante++;
			$xls->xlsWriteNumber($ligne_courante, $colonne_courante, $tab_echeances[$i]['echeancier'][$j]['reste_a_payer']);
			// RIB
			$valeur = $tab_echeances[$i]['echeancier'][$j]['echeancier_numero_rib'];
			if($valeur == 0) {
				$valeur = LANG_FIN_GENE_049;
			} else {
				$valeur = $tab_rib[$valeur - 1];
			}
			$valeur = str_replace(' ', '', $valeur);
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante,  $valeur);

			// Total
			$colonne_courante++;
			$xls->xlsWriteLabel($ligne_courante, $colonne_courante, '');

		}
	}
	
	$ligne_courante=$ligne_courante+4;
	
	$xls->xlsWriteLabel($ligne_courante, 1,ucfirst(LANG_FIN_GPRE_014));
	
	for($k=0; $k<count($tab_groupe_type); $k++) {
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0,ucfirst($tab_groupe_type[$k]['libelle']));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($tab_groupe_type[$k]['reste_a_payer']));
	}
	$ligne_courante++;
	$xls->xlsWriteLabel($ligne_courante, 0,ucfirst(LANG_FIN_GENE_024));
	$xls->xlsWriteLabel($ligne_courante, 1, montant_depuis_bdd($total_groupe_type));

	
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