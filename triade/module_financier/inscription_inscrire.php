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

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation_rech = lire_parametre('operation_rech', '', 'POST');
	$code_class_rech = lire_parametre('code_class_rech', 0, 'POST');
	$nom_eleve_rech = lire_parametre('nom_eleve_rech', '', 'POST');
	$annee_scolaire_rech = lire_parametre('annee_scolaire_rech', '', 'POST');
	$operation = lire_parametre('operation', '', 'POST');
	$elev_id_insc = lire_parametre('elev_id_insc', 0, 'POST');
	$code_class_insc = lire_parametre('code_class_insc', $code_class_rech, 'POST');
	$annee_scolaire_insc = lire_parametre('annee_scolaire_insc', '', 'POST');
	
	$bareme_id = lire_parametre('bareme_id', 0, 'POST');
	$type_echeancier_id = lire_parametre('type_echeancier_id', 0, 'POST');
	$type_reglement_id = lire_parametre('type_reglement_id', 0, 'POST');
	$date_debut = lire_parametre('date_debut', '', 'POST');
	$echeances_total = lire_parametre('echeances_total', 0, 'POST');
	
	//***************************************************************************

	//echo $code_class_insc;
	//echo " - ";
	//echo $elev_id_insc;


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	$echeancier = array();
	$inscription_terminee = false;
	$montrer_bouton_inscrire = false;
	
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['type_creation'] = 'nouvelle';
	
	// Si la classe a changee, reinitialiser l'annee scolaire selectionnee
	if($operation == 'changement_code_class_insc') {
		$annee_scolaire_insc = '';
	}
	
	if($operation == 'calculer_echeances') {
		if($type_echeancier_id > 0) {
			// Rechercher le type d'echeancier selectione
			$sql  = "SELECT type_echeancier_id, libelle, ordre, echeances, intervale_mois ";
			$sql .= "FROM ".FIN_TAB_TYPE_ECHEANCIER." ";
			$sql .= "WHERE type_echeancier_id = $type_echeancier_id ";
			$un_echeancier = execSql($sql);
			if($un_echeancier->numRows() > 0) {
				// Recuperer le nombre d'echeances et l'intervale entre les echeances
				$res = $un_echeancier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
				$echeances = $ligne[3];
				$intervale_mois = $ligne[4];
				//echo $type_echeancier_id;
				//echo "<br>";
				//echo $echeances;
				// Generer chaque echeance
				
				// Rechercher les groupes de frais
				$sql ="SELECT groupe_id, libelle ";
				$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
				$res1 = execSql($sql);
				

				// Rechercher les frais du bareme selectionne
				$sql  ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle, tf.groupe_id "; // MODIFICATION MOHAMED/////////////////
				$sql .= "FROM ".FIN_TAB_FRAIS_BAREME." fb ";
				$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";	
				$sql .= "WHERE fb.bareme_id = $bareme_id ";
				$sql .= "ORDER BY tf.libelle ASC";
				//echo $sql;
				$frais_bareme=execSql($sql);
				$tab_liste_frais = array();
			
				$montant_echeance = 0.0;
				$montant_derniere_echeance = 0.0;
				
				if($frais_bareme->numRows() > 0) {
					
					for($i=0;$i<$frais_bareme->numRows();$i++) {
					
						// Recuperer les infos de la ligne
						$res = $frais_bareme->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
						
						// ********* verifier si le frais doit etre inclus dans les calculs ***********
						// Verifier si le frais est optionnel ou non
						if($ligne[4] == 0) {
							$inclure_le_frais = true;
						} else {
							// Pour savoir si le frais optionnel a ete selectionne
							$selecttionne = lire_parametre('type_frais_id_' . $ligne[2], 0, 'POST');
							if($selecttionne == 1) {
								$inclure_le_frais = true;
							} else {
								$inclure_le_frais = false;
							}
						}
						// ****************************************************************************
						
						if($inclure_le_frais) {
						
							// 20100518 - AP : dans le cas d'un echeancier au comptant, aucun frais n'est considere comme lisse
							if($type_echeancier_id != 1) {
								// Pour savoir si le frais est lisse ou non
								$lisse = lire_parametre('type_frais_id_' . $ligne[2] . '_lisse', 0, 'POST');
							} else {
								$lisse = 0;
							}
							
							// Stockage du frais
							$tab_liste_frais[count($tab_liste_frais)] = array(
																'type_frais_id' => $ligne[2],
																'montant' => $ligne[3],
																'lisse' => $lisse,
																'groupe_id' => $ligne[6],
																);
							
							
							//Calcul du montant total a lisser par groupe
							for($j=0; $j<$res1->numRows(); $j++)
							{
								$res2 = $res1->fetchInto($ligne2, DB_FETCHMODE_DEFAULT, $j);

								if($ligne2[0] == $ligne[6])
								{
									if($lisse == 1)
									{
										${'montant_total_lisser_'.$ligne2[0]} += $ligne[3];		
									}
									
								}
							}
						}
					}

					if($echeances > 0) {
						for($j=0; $j<$res1->numRows(); $j++){
								$res3 = $res1->fetchInto($ligne2, DB_FETCHMODE_DEFAULT, $j);
								// Calcul le montant echeance de chaque groupe
								${'montant_echeance_'.$ligne2[0]} = number_format((${'montant_total_lisser_'.$ligne2[0]}) / $echeances, 2);	
								${'montant_echeance_'.$ligne2[0]} = str_replace(',', '', ${'montant_echeance_'.$ligne2[0]});		
								// Calcul le montant total des groupes
								$montant_echeance += ${'montant_echeance_'.$ligne2[0]};
						}
						
						// Calcul de la derniere echeance (peut etre inferieure ou superieur aux autres echeances a cause des arrondis)
						for($j=0; $j<$res1->numRows(); $j++){
							$res4 = $res1->fetchInto($ligne3, DB_FETCHMODE_DEFAULT, $j);
							if(((${'montant_echeance_'.$ligne3[0]}) * $echeances) == (${'montant_total_lisser_'.$ligne3[0]}) || $echeances <= 1) {
								// => toutes les echeances sont egales
								${'montant_derniere_echeance_'.$ligne3[0]}  = ${'montant_echeance_'.$ligne3[0]};			
							}
							else{
								// Calcul de la derniere echeance
								${'montant_derniere_echeance_'.$ligne3[0]} = (${'montant_total_lisser_'.$ligne3[0]}) - (($echeances - 1) * (${'montant_echeance_'.$ligne3[0]}));
							}	
						$montant_derniere_echeance += ${'montant_derniere_echeance_'.$ligne3[0]};	
						}							
					}
					
					// Remplir le tableau des echeances (qui sera affiche dans le HTML) => POUR LES NON-LISSES
					for($i=0;$i<count($tab_liste_frais);$i++) 
					{	
						if($tab_liste_frais[$i]['lisse'] == 0) {
							// Stockage dans le tableau
							$echeancier[count($echeancier)] = array(
																"date" => date('d/m/Y'),
																"montant" => $tab_liste_frais[$i]['montant'],
																"groupe_id" => $tab_liste_frais[$i]['groupe_id'],
																"lisse" => 0
																	);
						}
					}
					
					// Remplir le tableau des echeances (qui sera affiche dans le HTML) => POUR LES LISSES
					if($echeances > 0) {
						$date_echeance = $date_debut;
						for($i=1;$i<=$echeances;$i++) {
							// Verifier si on est sur la derniere echeance ou non
							if($i < $echeances) {
								$montant = $montant_echeance;
							} else {
								$montant = $montant_derniere_echeance;
							}
							
							// Stockage dans le tableau
							$echeancier[count($echeancier)] = array(
																"date" => $date_echeance,
																"montant" => $montant,
																"lisse" => 1
																	);
							// Calcul de la prochaine date d'echeance (date precedente + "$intervale_mois" mois)			
							$timestamp = strtotime("+$intervale_mois month", mktime(0, 0, 0, substr($date_echeance, 3, 2), substr($date_echeance, 0, 2), substr($date_echeance, 6, 4)));
							$date_echeance = date('d/m/Y', $timestamp);
						}
					}	
					
					// Remplir le tableau des groupes (qui sera affiche dans le HTML) => POUR LES NON-LISSES
					for($i=0;$i<count($echeancier);$i++) 
					{
						if($echeancier[$i]['lisse'] == 0) 
						{
							// Stockage dans le tableau
							for($j=0; $j<$res1->numRows(); $j++)
							{
							$res5 = $res1->fetchInto($ligne4, DB_FETCHMODE_DEFAULT, $j);

								if($ligne4[0] ==  $echeancier[$i]['groupe_id'])
								{
										${'groupe_echeancier_'.($i+1)}[count(${'groupe_echeancier_'.($i+1)})] = array(
																	"libelle" => $ligne4[1],
																	"montant" => $echeancier[$i]['montant'],
																	"groupe_id" => $ligne4[0],
																	"lisse" => 0
																		);
										
								}
								else
								{
										${'groupe_echeancier_'.($i+1)}[count(${'groupe_echeancier_'.($i+1)})] = array(
																	"libelle" => $ligne4[1],
																	"montant" => 0,
																	"groupe_id" => $ligne4[0],
																	"lisse" => 0
																		);
								}
							}
							// Sauvegarde de la derniere valeur de i
							$temp++;
						}
						
					}
		
					// Remplir le tableau des groupes (qui sera affiche dans le HTML) => POUR LES LISSES
					if($echeances > 0)
					{
						for($i=($temp-1);$i<count($echeancier);$i++) 
						{
							// Verifier si on est sur la derniere echeance ou non
							if($i < (count($echeancier)-1))
							{
								if($echeancier[$i]['lisse'] == 1) 
									{
										for($j=0; $j<$res1->numRows(); $j++)
										{
										$res5 = $res1->fetchInto($ligne5, DB_FETCHMODE_DEFAULT, $j);
										${'groupe_echeancier_'.($i+1)}[count(${'groupe_echeancier_'.($i+1)})] = array(
																													"libelle" => $ligne5[1],
																													"montant" => ${'montant_echeance_'.$ligne5[0]},
																													"groupe_id" => $ligne5[0],
																													"lisse" => 1
																													);		
										
										}	
									}
							}
							else
							{
								if($echeancier[$i]['lisse'] == 1) 
								{
											for($y=0; $y<$res1->numRows(); $y++)
											{
											$res5 = $res1->fetchInto($ligne6, DB_FETCHMODE_DEFAULT, $y);
											${'groupe_echeancier_'.($i+1)}[count(${'groupe_echeancier_'.($i+1)})] = array(
																														"libelle" => $ligne6[1],
																														"montant" => ${'montant_derniere_echeance_'.$ligne6[0]},
																														"groupe_id" => $ligne6[0],
																														"lisse" => 1
																														);		
											
											}	
								}
							
							}
						}
						
					}
					
				}
				//print_r($groupe_echeancier_1);
			}
		}
	}
	
	
	if($operation == 'inscrire') {
		// Verifier si il y a deja une inscription
		$sql  = "SELECT inscription_id ";
		$sql .= "FROM ".FIN_TAB_INSCRIPTIONS." ";
		$sql .= "WHERE elev_id = $elev_id_insc ";
		$sql .= "AND code_class = $code_class_insc ";
		$sql .= "AND annee_scolaire = '$annee_scolaire_insc' ";
		$inscription_existe = execSql($sql);


	
		$sqlgroupe ="SELECT groupe_id, libelle ";
				$sqlgroupe.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
				$resgroupe = execSql($sqlgroupe);
		if($inscription_existe->numRows() == 0) {
			// Ajouter l'inscription
			$sql  = "INSERT INTO ".FIN_TAB_INSCRIPTIONS." (elev_id, code_class, annee_scolaire, date_inscription, type_echeancier_id, commentaire, id_bareme_initial) ";
			$sql .= "VALUES (";
			$sql .= "".$elev_id_insc.", ";
			$sql .= "".$code_class_insc.", ";
			$sql .= "'".$annee_scolaire_insc."', ";
			$sql .= "'".date("Y-m-d H:i:s")."', ";
			$sql .= "".$type_echeancier_id.", ";
			$sql .= "'', ";
			$sql .= "".$bareme_id." ";
			$sql .= ") ";
			//echo $sql;
			$res_lock=execSql("LOCK TABLES ".FIN_TAB_INSCRIPTIONS." WRITE");
			$inscription = execSql($sql);
			// Recuperer le id
			$inscription_id = dernier_id($cnx->connection);
			$res_lock=execSql("UNLOCK TABLES ");

			//exit();

			// Verifier si l'eleve a au moins un rib pour definir le numero de rib a enregistrer
			$sql  = "SELECT rib_id ";
			$sql .= "FROM ".FIN_TAB_RIB." ";
			$sql .= "WHERE elev_id = $elev_id_insc ";
			//echo $sql . "<br>";
			$rib_eleve = execSql($sql);
			if($rib_eleve->numRows() > 0) {
				$numero_rib = 1;
			} else {
				$numero_rib = 0;
			}
			
			
			// Ajouter chaque echeance
			for($ech=1; $ech<=$echeances_total; $ech++) {
				// Recuperer la date de l'echeance
				$date_echeance = lire_parametre('echeance_'.$ech.'_date', '', 'POST');
				// Recuperer le montant de l'echeance
				$montant_echeance = lire_parametre('echeance_'.$ech.'_montant', 0, 'POST');
				// Recuperer la valeur de l'option 
				$lisse = lire_parametre('echeance_'.$ech.'_lisse', 0, 'POST');
	
				// Ajouter l'echeance
				$sql  = "INSERT INTO ".FIN_TAB_ECHEANCIER." (inscription_id, date_echeance, montant, impaye, type_reglement_id, libelle, type, numero_rib, lisse) ";
				$sql .= "VALUES (";
				$sql .= "".$inscription_id.", ";
				$sql .= "'".date_vers_bdd($date_echeance)."', ";
				$sql .= "".montant_vers_bdd($montant_echeance).", ";
				$sql .= "0, ";
				$sql .= "".$type_reglement_id.", ";
				$sql .= "'', ";
				$sql .= "0, ";
				$sql .= "".$numero_rib.",";
				$sql .= "".$lisse."";
				$sql .= ") ";
				$nouvelle_echeance = execSql($sql);
				$num_echeance = mysqli_insert_id($cnx->connection);

				for($grp=1; $grp <= $resgroupe->numRows(); $grp++)
				{
					$id_groupe =lire_parametre('groupe_'.$ech.'_'.$grp.'_id', 0, 'POST');
					$montant_groupe = lire_parametre('montant_groupe_echeancier_'.$ech.'_'.$grp, 0, 'POST');

					$sql9  = "INSERT INTO ".FIN_TAB_ECHEANCIER_GROUPE." (inscription_id, echeancier_id, groupe_id, montant) ";
							$sql9 .= "VALUES (";
							$sql9 .= "".$inscription_id.", ";
							$sql9 .= "".$num_echeance.", ";
							$sql9 .= "".$id_groupe.", ";
							$sql9 .= "".montant_vers_bdd($montant_groupe)."";
							$sql9 .= ") ";

					$nouveau_groupe = execSql($sql9);
				}
			}
			
			//exit;
			
			// Rechercher les frais du bareme selectionne
			$sql  ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle ";
			$sql .= "FROM ".FIN_TAB_FRAIS_BAREME." fb ";
			$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
			$sql .= "WHERE fb.bareme_id = $bareme_id ";
			$sql .= "ORDER BY tf.libelle ASC";
			$frais_bareme=execSql($sql);
			
			if($frais_bareme->numRows() > 0) {
				for($i=0;$i<$frais_bareme->numRows();$i++) {
					// Recuperer la Neme ligne
					$res = $frais_bareme->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
					
					// Verifier si le frais est optionnel ou non
					if($ligne[4] == 0) {
						// Pas optionnel => pas selectionne
						$optionnel = 0;
						$selectionne = 0;
					} else {
						// Optionnel => verifier si l'option a ete selectionnee
						$option = lire_parametre('type_frais_id_' . $ligne[2], 0, 'POST');
						if($option == '1') {
							$optionnel = 1;
							$selectionne = 1;
						} else{
							$optionnel = 1;
							$selectionne = 0;
						}
					}

					$lisse = lire_parametre('type_frais_id_' . $ligne[2] . '_lisse', 0, 'POST');
					
					// Ajouter le frais pour l'inscription
					$sql  = "INSERT INTO ".FIN_TAB_FRAIS_INSCRIPTION." (inscription_id, type_frais_id, montant, optionnel, selectionne, lisse) ";
					$sql .= "VALUES (";
					$sql .= "".$inscription_id.", ";
					$sql .= "".$ligne[2].", ";
					$sql .= "".$ligne[3].", ";
					$sql .= "".$optionnel.", ";
					$sql .= "".$selectionne.", ";
					$sql .= "".$lisse." ";
					$sql .= ") ";
					//echo $sql . '<br>';
					$frais_inscription = execSql($sql);

				}	
			}	
			
			//exit;
			$inscription_terminee = true;
			//msg_util_ajout(LANG_FIN_INSC_016);
			
			
		} else {
			// L'inscription existe deja => erreur
			$inscription_terminee = true;
			msg_util_ajout(LANG_FIN_INSC_017, "erreur");
		}

	}

	//***************************************************************************


	// Rechercher la liste des classes ou l'eleve peut etre inscrit
	$sql  = "SELECT code_class, libelle ";
	$sql .= "FROM ".FIN_TAB_CLASSES." ";
	$sql .= "ORDER BY libelle ";
	//echo $sql;
	$classes = execSql($sql);


	// Selectionner la premier classe (si il n'y en a pas deja une)
	if($classes->numRows() > 0 && $code_class_insc <= 0) {
		$ligne = null;
		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$code_class_insc = $ligne[0];
	}

	// Recuperer la liste complete des annees scolaires
	$tab_annees_scolaires_complet = liste_annees_scolaire_toutes();
	// print_r($tab_annees_scolaires_complet);
	$tab_annees_scolaires = array();
	$tab_inscriptions = array();
	for($i=0;$i<count($tab_annees_scolaires_complet);$i++) {
		// Verifier si une inscription existe
		$sql  = "SELECT inscription_id ";
		$sql .= "FROM ".FIN_TAB_INSCRIPTIONS." ";
		$sql .= "WHERE code_class = " . $code_class_insc . " ";
		$sql .= "AND annee_scolaire = '" . $tab_annees_scolaires_complet[$i] . "' ";
		$sql .= "AND elev_id = " . $elev_id_insc . " ";

		//echo $sql;
		$inscription = execSql($sql);
		if($inscription->numRows() > 0) {
			// => il y a une inscription
			$tab_inscriptions[count($tab_inscriptions)] = $tab_annees_scolaires_complet[$i];
		} else {
			// => pas d'inscription : on peut utiliser l'annee
			$tab_annees_scolaires[count($tab_annees_scolaires)] = $tab_annees_scolaires_complet[$i];
		}
	}
	
	
	// Selectionner la premiere annee scolaire (si il n'y en a pas deja une)
	if(count($tab_annees_scolaires) > 0 && $annee_scolaire_insc == '') {
		$annee_scolaire_insc = $tab_annees_scolaires[0];
	}
	

	// Rechercher la liste des baremes disponibles (pour la classe donnee)
	$sql  = "SELECT b.bareme_id, b.libelle ";
	$sql .= "FROM ".FIN_TAB_BAREME." b ";
	$sql .= "INNER JOIN ".FIN_TAB_FRAIS_BAREME." fb ON b.bareme_id = fb.bareme_id ";
	$sql .= "WHERE b.code_class = $code_class_insc ";
	$sql .= "AND b.annee_scolaire = '" . $annee_scolaire_insc . "' ";
	$sql .= "GROUP BY b.bareme_id, b.libelle ";
	$sql .= "ORDER BY b.libelle ";
	//echo $sql;
	$baremes = execSql($sql);
	
	// Selectionner le premier bareme (si il n'y en a pas deja un)
	if($baremes->numRows() > 0 && $bareme_id <= 0) {
		$ligne = null;
		$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$bareme_id = $ligne[0];
	}
	
	// Rechercher les infos de l'eleve
	$sql  = "SELECT nom, prenom ";
	$sql .= "FROM ".FIN_TAB_ELEVES." ";
	$sql .= "WHERE elev_id = $elev_id_insc ";
	$eleve = execSql($sql);

	if($bareme_id > 0) {
		// Rechercher les frais pour le bareme selectionne
		$sql  ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle, fb.lisse, tf.caution ";
		$sql .= "FROM ".FIN_TAB_FRAIS_BAREME." fb ";
		$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
		$sql .= "WHERE fb.bareme_id = $bareme_id ";
		$sql .= "ORDER BY tf.libelle ASC";
		//echo $sql;
		$frais_bareme=execSql($sql);
		
		// Rechercher les types d'echeancier
		$sql  = "SELECT type_echeancier_id, libelle ";
		$sql .= "FROM ".FIN_TAB_TYPE_ECHEANCIER." ";
		$sql .= "ORDER BY ordre ";
		$types_echeancier = execSql($sql);

		// Selectionner le premier type d'echeancier (si il n'y en a pas deja un)
		if($types_echeancier->numRows() > 0 && $type_echeancier_id <= 0) {
			$ligne = null;
			$res = $types_echeancier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			$type_echeancier_id = $ligne[0];
		}
		
		// Rechercher les types de reglement
		$sql  = "SELECT type_reglement_id, libelle ";
		$sql .= "FROM ".FIN_TAB_TYPE_REGLEMENT." ";
		$sql .= "ORDER BY libelle ";
		$types_reglement = execSql($sql);

		// Selectionner le premier type de reglement (si il n'y en a pas deja un)
		if($types_reglement->numRows() > 0 && $type_reglement_id <= 0) {
			$ligne = null;
			$res = $types_reglement->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			$type_reglement_id = $ligne[0];
		}
		
		
		
	}
	
	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	
	if(count($tab_annees_scolaires) == 0) {
		// Afficher un message indiquant que l'eleve est deja inscrit pour toutes les annees
		$liste_annees = '';
		$separateur = '';
		for($k=0; $k<count($tab_inscriptions); $k++) {
			$liste_annees .= $separateur . $tab_inscriptions[$k];
			$separateur = ', ';
		}
		msg_util_ajout(LANG_FIN_INSC_029 . ' (' . $liste_annees . ')', 'avertissement');
	}
	//***************************************************************************
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>
<html>
	<head>
		<meta http-equiv="CacheControl" content = "no-cache">
		<meta http-equiv="pragma" content = "no-cache">
		<meta http-equiv="expires" content = -1>
		<meta name="Copyright" content="Triade©, 2001">
		<base href="<?php echo site_url_racine(FIN_REP_MODULE); ?>">
		<link title="style" type="text/CSS" rel="stylesheet" href="./librairie_css/css.css">
		<script language="javascript" src="./librairie_js/clickdroit2.js"></script>
		<script language="javascript" src="./librairie_js/function.js"></script>
		<script language="javascript" src="./librairie_js/lib_css.js"></script>
		<script language="javascript" src="./librairie_js/verif_creat.js"></script>
		<link title="style" type="text/CSS" rel="stylesheet" href="./<?php echo $g_chemin_relatif_module; ?>librairie_css/css.css">
		<?php
		// Inclure les scripts Javascript
		inclure_scripts_js_toutes_pages();
		?>
		<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
	</head>
	
	<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

		<?php //********** GENERATION DU DEBUT DE LA PAGE ET DES MENUS PRINCIPAUX ********** ?>
		
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		// Verification droits acces groupe
		validerequete("2");
		?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
		<?php include("./librairie_php/lib_defilement.php"); ?>
		</td>
		<td width="472" valign="middle" rowspan="3" align="center">
			<div align='center'>
				<?php top_h(); ?>
				<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>


		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		
		<!-- TITRE ET CADRE CENTRAL -->
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_FIN_INSC_006; ?>
					<?php
					if($eleve->numRows() > 0) {
						$ligne = $eleve->fetchRow();
						
						echo ' : <font id="color2">' . strtoupper($ligne[0]) . ' ' . ucfirst($ligne[1]) . '</font>';
					}
					?>
					</font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
                
					<?php
                    // Rediriger directement vers l'edition de l'inscription, une fois l'inscription reussie
                    if($inscription_terminee) {
                    ?>
                    <form name="for_aller_inscription_editer" id="for_aller_inscription_editer" method="post" action="<?php echo $g_chemin_relatif_module; ?>inscription_editer.php">
                        <input type="hidden" name="inscription_id" id="inscription_id" value="<?php echo $inscription_id; ?>">
                    </form>
                    <script language="javascript">
                        document.for_aller_inscription_editer.submit();
                    </script>
                    <?php
                    }
                    ?>

					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<input type="hidden" name="operation" id="operation" value="">
						
						<input type="hidden" name="elev_id_insc" id="elev_id_insc" value="<?php echo $elev_id_insc; ?>">


						<input type="hidden" name="operation_rech" id="operation_rech" value="<?php echo $operation_rech; ?>">
						<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class_rech; ?>">
						<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve_rech; ?>">
						<input type="hidden" name="annee_scolaire_rech" id="annee_scolaire_rech" value="<?php echo $annee_scolaire_rech; ?>">
						<script language="javascript">
							var tab_type_frais_optionnels = new Array();
							var tab_frais = new Array();
						</script>
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>                            
                            
							<?php
							if(!$inscription_terminee) {
							?>
							<tr>
								<td valign=top align="center">

									<?php
									//*******************  CLASSES ET ANNEES SCOLAIRES DISPONIBLES *********************
									
									?>
									<fieldset id="fieldset_classes" style="z-index:6">
										<legend><?php echo LANG_FIN_INSC_028; ?></legend>
										
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="code_class_insc" id="code_class_insc" onChange="onchange_code_class_insc()">
														<?php
														for($i=0; $i<$classes->numRows(); $i++) {
															$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
															$selected = '';
															if($code_class_insc == $ligne[0]) {
																$selected = 'selected="selected"';
															}
														?>
														<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
														<?php
														}
														?>
													</select>
												</td>
											</tr>
											<?php
											if(count($tab_annees_scolaires) > 0) {
											?>
											<tr>
												<td align="right"><?php echo LANG_FIN_GENE_011; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="annee_scolaire_insc" id="annee_scolaire_insc" onChange="onchange_annee_scolaire_insc()">
														<?php
														for($i=0; $i<count($tab_annees_scolaires); $i++) {
															$selected = '';
															if($annee_scolaire_insc == $tab_annees_scolaires[$i]) {
																$selected = 'selected="selected"';
															}
														?>
														<option value="<?php echo $tab_annees_scolaires[$i]; ?>" <?php echo $selected; ?>><?php echo $tab_annees_scolaires[$i]; ?></option>
														<?php
														}
														?>
													</select>
												</td>
											</tr>
											<?php
											}
											?>
										</table>
									</fieldset>
						
									<?php
									// Verifier si il y a des annees scolaires dispponibles
									if(count($tab_annees_scolaires) > 0) {
									
									?>
									
									<?php
									//*******************  BAREMES DISPONIBLES *********************
									
									?>
									<fieldset style="z-index:5">
										<legend><?php echo LANG_FIN_BARE_004; ?></legend>
										<?php
										// Verifier si on a au moins un bareme
										if($baremes->numRows() > 0) {
										?>
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_BARE_004; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="bareme_id" id="bareme_id" onChange="onchange_bareme_id()">
														<?php
														for($i=0; $i<$baremes->numRows(); $i++) {
															$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
															$selected = '';
															if($bareme_id == $ligne[0]) {
																$selected = 'selected="selected"';
															}
														?>
														<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
														<?php
														}
														?>
													</select>
												</td>
											</tr>
										</table>
										<?php
										} else {
										?>
											<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_FIN_BARE_005; ?></span></div>
										<?php
										}
										?>
									</fieldset>
									
									
									<?php
									//*******************  LISTE DES FRAIS (OPTIONNELS OU NON) *********************
									
										if($bareme_id > 0 && $baremes->numRows() > 0) {
											$montrer_bouton_inscrire = true;
									?>
									<br>
									<fieldset style="z-index:4">
										<legend><?php echo LANG_FIN_FBAR_003; ?></legend>

											<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
												<tr bgcolor="#ffffff">
													<td align="left"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
													<td align="right"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
													<td align="center"><b><?php echo LANG_FIN_GENE_012; ?></b></td>
													<td align="center"><b><?php echo LANG_FIN_GENE_028; ?></b></td>
													<td align="center"><b><?php echo LANG_FIN_TFRA_014; ?></b></td>
													<td align="center"><b><?php echo LANG_FIN_TFRA_016; ?></b></td>
												</tr>
											<?php
												if($frais_bareme->numRows() > 0) {
													$montant_total = 0;
													$montant_sans_optionnel = 0;
													$type_frais_id_total = 0;
													for($i=0; $i<$frais_bareme->numRows(); $i++) {
														$ligne = $frais_bareme->fetchRow();
											?>
														<script language="javascript">
															tab_frais[tab_frais.length] = {
																						"id" : "<?php echo $ligne[2]; ?>"
																														};
														</script>
												<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
													<td><?php echo $ligne[5]; ?></td>
													<?php
														// Remplacer le separateur de decimal bdd, par le francais
														$valeur = montant_depuis_bdd($ligne[3]);
													?>
													<td nowrap="nowrap" align="right"><?php echo $valeur; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
													<?php
														// Pour afficher "oui" ou "non"
														if($ligne[4] == 1) {
															$valeur = LANG_FIN_GENE_017; // Oui
														} else {
															$valeur = LANG_FIN_GENE_018; // Non
															$montant_sans_optionnel += $ligne[3];
														}
													?>
													<td nowrap="nowrap" align="center"><?php echo $valeur; ?></td>
													<?php
														// Si est optionel
														if($ligne[4] == 1) {
															// Essayer de recuperer la valeur de l'option dans les parametres
															$option = lire_parametre('type_frais_id_' . $ligne[2], 0, 'POST');
															
															// Verifier si on doit selectionner l'option
															if($option == '1') {
																$valeur = 'checked';
																$montant_total += $ligne[3];
															} else {
																$valeur = '';
															}
													?>
													<td align="center" nowrap="nowrap">
														<input type="checkbox" name="type_frais_id_<?php echo $ligne[2]; ?>" id="type_frais_id_<?php echo $ligne[2]; ?>" value="1" onClick="onclick_frais('<?php echo $ligne[2]; ?>');" <?php echo $valeur; ?> >
														<script language="javascript">
															tab_type_frais_optionnels[tab_type_frais_optionnels.length] = {
																						"id" : "type_frais_id_<?php echo $ligne[2]; ?>",
																						montant : "<?php echo $ligne[3]; ?>"
																														};
														</script>
													</td>
													<?php
														} else {
															// Pas optionnel => inclus : Oui
															$montant_total += $ligne[3];
													?>
													<td align="center" nowrap="nowrap"><?php echo LANG_FIN_GENE_017; ?></td>
													<?php
														}
													?>
													<?php
														// Indiquer si c'est lisse ou non
														$valeur = '';
														if($operation == 'calculer_echeances') {
															// => On recalcule donc on affiche ce qui avait ete selectionne
														
															// Essayer de recuperer la valeur de l'option dans les parametres
															$lisse = lire_parametre('type_frais_id_' . $ligne[2] . '_lisse', 0, 'POST');
															if($lisse == 1) {
																$valeur = 'checked';
															}
														} else {
															// => On ne recalule pas donc on affiche la valeur par defaut venant du bareme
														
															if($ligne[6] == 1) {
																$valeur = 'checked';
															}
														}
													?>
													<td align="center" nowrap="nowrap"><input type="checkbox" name="type_frais_id_<?php echo $ligne[2]; ?>_lisse" id="type_frais_id_<?php echo $ligne[2]; ?>_lisse" value="1" <?php echo $valeur; ?> ></td>
													<?php
														// Indiquer si c'est un caution ou non
														$valeur = LANG_FIN_GENE_018;
														if($ligne[7] == 1) {
															$valeur = LANG_FIN_GENE_017;
														}
													?>
													<td nowrap="nowrap" align="center"><?php echo $valeur; ?></td>
												</tr>										
											<?php
												}
											?>
												<tr class='tabnormal2'>
													<td align="center" nowrap="nowrap" colspan="2">&nbsp;</td>
													<td align="center" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
													<td align="center" nowrap="nowrap"><b><span id="total_final"><?php echo montant_depuis_bdd($montant_total); ?></span>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
													<td align="center" nowrap="nowrap" colspan="2">&nbsp;</td>
												</tr>
												<input type="hidden" name="montant_sans_optionnel" id="montant_sans_optionnel" value="<?php echo $montant_sans_optionnel; ?>" >
												<input type="hidden" name="montant_total" id="montant_total" value="<?php echo $montant_total; ?>" >
											<?php
											} else {
											?>
											<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
												<td align="left" colspan="4"><?php echo LANG_FIN_FBAR_005; ?></td>
											</tr>
											<?php
											}
											?>
											</table>
									</fieldset>
									<?php
										}
									?>


									<?php
									//*******************  TYPES D'ECHEANCIER ET DATES D'ECHEANCE *********************
									
										if($bareme_id > 0 && $baremes->numRows() > 0) {
									?>
									<br>
									<fieldset id="fieldset_type_echeancier" style="z-index:3">
										<legend><?php echo LANG_FIN_ECHE_002; ?></legend>
										<?php
										// Verifier si on a au moins un type d'echeancier
											if($types_echeancier->numRows() > 0) {
										?>
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_TECHE_002; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="type_echeancier_id" id="type_echeancier_id" onChange="onchange_type_echeancier_id()">
														<?php
														for($i=0; $i<$types_echeancier->numRows(); $i++) {
															$res = $types_echeancier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
															$selected = '';
															if($type_echeancier_id == $ligne[0]) {
																$selected = 'selected="selected"';
															}
														?>
														<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
														<?php
														}
														?>
													</select>
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_FIN_TECHE_006; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<table border="0" cellpadding="0" cellspacing="0" align="center">
														<tr>
															<td>
																<input type="text" name="date_debut" id="date_debut" size="10" maxlength="10" readonly="" value="<?php echo $date_debut; ?>" onClick="onclick_date_debut();">
															</td>
															<td>&nbsp;</td>
															<td>
																<?php
																include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");
																calendarDim("div_date_debut","document.formulaire.date_debut",$_SESSION["langue"], "0", "0", 'fieldset_type_echeancier', 'null', date("m/d/Y"));
																?>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="center" colspan="2">&nbsp;
													
												</td>
											</tr>
											<tr>
												<td align="center" colspan="2">
													<input type="button" class="button" value="<?php echo LANG_FIN_GENE_029; ?>" onClick="onclick_calculer();" >
												</td>
											</tr>
											<tr>
												<td align="center" colspan="2">&nbsp;
													
												</td>
											</tr>
											<tr>
												<td align="center" colspan="2">
													<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
														<tr bgcolor="#ffffff">
															<td align="left"><b>#</b></td>
															<td align="left"><b><?php echo LANG_FIN_GENE_030; ?></b></td>
															<td align="left"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
															<td align="right"><b><?php echo LANG_FIN_GROUPE_016;?></b></td>
														</tr>
														<?php
														if(count($echeancier) > 0) {
															for($i=0;$i<count($echeancier);$i++) 
															{
														?>
																<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
																	<td align="right"><?php echo $i+1; ?></td>
																	<td align="left">
																		<table cellspacing="0" cellpadding="0" border="0">
																			<input type="hidden" name="echeance_<?php echo $i+1; ?>_lisse" id="echeance_<?php echo $i+1; ?>_lisse" value="<?php echo $echeancier[$i]["lisse"]; ?>" >
																			<tr>
																				<td align="left">
																					<input type="text" name="echeance_<?php echo $i+1; ?>_date" id="echeance_<?php echo $i+1; ?>_date" size="10" maxlength="10" value="<?php echo $echeancier[$i]["date"]; ?>" >
																				</td>
																				<td>&nbsp;</td>
																				<td align="left">
																					<?php
																						calendarDim1("div_echeance_".($i+1)."_date","document.formulaire.echeance_".($i+1)."_date",$_SESSION["langue"], "0", "0", 'fieldset_type_echeancier', 'null', date("m/d/Y"));
																					?>
																				</td>
																			</tr>
																		</table>

																	</td>
																	<td align="right" nowrap="nowrap">
																	<?php
																	$valeur = $echeancier[$i]["montant"];
																	$valeur = str_replace('.', ',', $valeur);
																	?>
																	<input type="text" name="echeance_<?php echo $i+1; ?>_montant" id="echeance_<?php echo $i+1; ?>_montant" size="12" maxlength="12" value="<?php echo $valeur; ?>" style="text-align:right;" onBlur="formatage_montant(this);" readonly="readonly">&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
																
																	<?php
																	$info_bulle = '<a href="javascript:;" onclick="onclick_detail_frais(\'' . ($i+1) . '\')" alt="' . LANG_FIN_GENE_044 . '" title="' . LANG_FIN_GENE_044 . '" id="groupe_' . ($i+1) . '_detail_frais"><span id="groupe_' . ($i+1) . '_frais_detail">[+]</span></a>';

																	
																	?>
																		<td align="left">
																			<?php echo $info_bulle; ?>
																		</td>
																
																</tr>
																
																<tr id="liste_<?php echo $i+1; ?>_detail_frais" style="display:none"  class='tabnormal2'>
																<td colspan="4" align="left">
																<table border="0" cellpadding="0" cellspacing="0" align="left">
																	<?php
																	for($j=0; $j< count(${'groupe_echeancier_'.($i+1)}); $j++)
																	{
																		$nb_montant =count(${'groupe_echeancier_'.($i+1)});
																			// Affichage du montant par groupe ?>
							
																		<tr>		
																			<td>
																					<td align="right">
																					&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ${'groupe_echeancier_'.($i+1)}[$j]["libelle"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																					</td>
																			</td>	
																		
																			<td>
																					<td align="right">
																					<?php
																					$valeur1  = number_format(${'groupe_echeancier_'.($i+1)}[$j]["montant"],2, '.', '');  
																					$valeur1  = str_replace('.', ',', $valeur1 );
																					?>
																					<input type="text" name="montant_groupe_echeancier_<?php echo $i+1;?>_<?php echo $j+1; ?>" id="montant_groupe_echeancier_<?php echo $i+1;?>_<?php echo $j+1; ?>" size="12" maxlength="12" value="<?php echo $valeur1; ?>" style="text-align:right;" onChange="Change_montant(<?php echo $i+1; ?>,<?php echo $nb_montant; ?>)" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019;"</br>" ?></td>

																			</td>
																		</tr>
																		<input type="hidden" name="groupe_<?php echo ($i+1);?>_<?php echo $j+1;?>_id" id="groupe_<?php echo ($i+1);?>_<?php echo $j+1;?>_id" value="<?php echo ${'groupe_echeancier_'.($i+1)}[$j]["groupe_id"]; ?>">
																		<?php
																	}
																		?>
																		
																		
																</table>
																
																</td>
																</tr>
														<?php
															}
														} 
														else {
														?>
														<tr class='tabnormal2'>
															<td align="left" colspan="4"><?php echo LANG_FIN_INSC_008; ?></td>
														</tr>
														<?php
														}
														?>
													</table>
													<input type="hidden" name="echeances_total" id="echeances_total" value="<?php echo count($echeancier); ?>">
													
												</td>
											</tr>
											
										</table>
										<?php
											} else {
										?>
											<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_FIN_TECHE_005; ?></span></div>
										<?php
											}
										?>									
										
									<?php
										}
									?>
									</fieldset>


									<?php
									//*******************  TYPES DE REGLEMENT *********************
									
										if($bareme_id > 0 && $baremes->numRows() > 0) {
									?>
									<br>
									<fieldset id="fieldset_type_reglement" style="z-index:2">
										<legend><?php echo LANG_FIN_TREG_017; ?></legend>
										<?php
											// Verifier si on a au moins un type de reglement
											if($types_reglement->numRows() > 0) {
										?>
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_TREG_015; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="type_reglement_id" id="type_reglement_id" onChange="onchange_type_reglement_id()">
														<?php
														for($i=0; $i<$types_reglement->numRows(); $i++) {
															$res = $types_reglement->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
															$selected = '';
															if($type_reglement_id == $ligne[0]) {
																$selected = 'selected="selected"';
															}
														?>
														<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
														<?php
														}
														?>
													</select>
												</td>
											</tr>
										</table>
										<?php
											} else {
										?>
											<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_FIN_TREG_011; ?></span></div>
										<?php
											}
										?>									
										</fieldset>
									<?php
										}
									}
									?>									
								</td>
							</tr>
							<?php
								}
							?>
		
							<?php //********** MESSAGES UTILISATEUR ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<a name="MESSAGE"></a>
									<?php 
									msg_util_afficher();
									msg_util_attente_init(); 
									?>
									</td>
							</tr>
				
				
							<?php //********** BOUTONS ********** ?>
							
							<tr>
								<td align="center">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<?php
											if(!$inscription_terminee && $montrer_bouton_inscrire) {
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_INSC_004?>","onclick_inscrire()");</script>
											</td>
											<?php
											}
											?>
											<?php
											if(!$inscription_terminee) {
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
											</td>
											<?php
											}
											?>
										</tr>
									</table>
								</td>
							</tr>
								
								
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
					</form>
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						var fenetre = null;
						var type_frais_id_modifies = 0;
						var type_echeancier_id_modifies = 0;
						function onclick_annuler() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_annuler').submit();
						}
						
						// Chamgement de bareme (tout est initialise)
						function onchange_bareme_id() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = "changement_bareme_id";
							document.formulaire.but_actualiser.click();
						}
						
						// Changement de type d'echeancier
						function onchange_type_echeancier_id() {
							// on garde le changement
							type_echeancier_id_modifies++;
							
							// Griser ou non les checkbox de lissage
							griser_degriser_checkbox_lissage();
						}		
						
						function griser_degriser_checkbox_lissage() {
							var i;
							// On verifie le type d'echeancier choisi
							if(document.getElementById('type_echeancier_id').options[document.getElementById('type_echeancier_id').selectedIndex].value == 1) {
								// Griser les checkbox de lissage
								for (i=0; i<tab_frais.length; i++) {
									document.getElementById('type_frais_id_' + tab_frais[i]['id'] + '_lisse').disabled = true;
								}
							} else {
								// Degriser les checkbox de lissage
								for (i=0; i<tab_frais.length; i++) {
									document.getElementById('type_frais_id_' + tab_frais[i]['id'] + '_lisse').disabled = false;
								}
							}
						}		
						
						// Click sur un frais optionel (on recalcule le montant total de l'inscription)
						function onclick_frais(type_frais_id) {
							// On recupere le montant sans aucune option
							var montant_sans_optionnel = document.formulaire.montant_sans_optionnel.value;
							
							// On verifie chaque option et on ajoute son montant si elle est selectionnee
							var montant_total_final = montant_sans_optionnel * 1;
							for (i=0; i<tab_type_frais_optionnels.length; i++) {
								//alert(tab_type_frais_optionnels[i]['id']);
								eval('obj= document.formulaire.' + tab_type_frais_optionnels[i]['id'] + ';');
								if(obj.checked) {
									montant_total_final += tab_type_frais_optionnels[i]['montant'] * 1;
								}
							}
							
							document.getElementById('montant_total').value = montant_total_final;
							
							// Convertion du montant au format francais
							montant_total_final = montant_depuis_bdd(montant_total_final, 2, ',', ' ');
							
							// Affichage du montant
							document.getElementById('total_final').innerHTML = montant_total_final;
							
							// On garde le changement d'option
							type_frais_id_modifies++;
							
						}
						
						// Click sur le textbox 'date_debut' qui est en readonly (affichage calendrier)
						function onclick_date_debut() {
							document.getElementById('anchor18div_date_debut').onclick();
						}
						
						// Click sur le bouton permettant de calculer les echeances
						function onclick_calculer() {
							var continuer;
							// On verifie si une date de debut a ete donnee
							if(document.formulaire.date_debut.value != '') {
								// => on a une date de debut
								
								// On verifie si il y a deja un echeancier affiche
								continuer = true;
								if(document.formulaire.echeances_total.value > 0) {
									// => Il y a un echeancier : demander la confirmation a l'utilisateur pour l'effacer
									if(!confirm("<?php echo LANG_FIN_INSC_011; ?>")) {
										continuer = false;
									}
								}
								// Tou est ok : on recharge le formulaire en donnant l'operation 'calculer_echeances'
								if(continuer) {
									document.formulaire.operation.value = 'calculer_echeances';
									document.formulaire.but_actualiser.click();
								}
							} else {
								// => pas de date de debut : message erreur et affichage calendrier
								alert("<?php echo LANG_FIN_INSC_007; ?>");
								document.getElementById('anchor18div_date_debut').onclick();
							}
						}
						
						// Lancer l'inscription de l'eleve
						function onclick_inscrire() {
							var echeances, date_echeance, montant_echeance, lisse_echeance;
							var message_erreur = '';
							var separateur = '';
							var date_courante = null;
							var date_precedente = null;
							var valide = true;
							var aujourdhui = new Date();
							var i;
							var messsage;
							var  montant_total_echeances, montant_total_inscription;

							montant_total_echeances = 0.0;

							// On verifie que l'echeancier n'est pas vide
							if(document.formulaire.echeances_total.value > 0) {
								// => echeancier pas vide
								
								// Verifier si un des frais ou le type d'echeancier ont ete modifies
								if(type_frais_id_modifies == 0 && type_echeancier_id_modifies == 0) {
									// => tout est ok : verifier les donnees de l'echeancier (dates et montants)
									// Recuperer le nombre d'echeances
									echeances = document.formulaire.echeances_total.value;
									//alert('echeance total=' + echeances);
									
									// Verifier chaque echeance
									for (i=1; i<=echeances; i++) {
										// Recuperation de la date et du montant
										date_echeance = document.getElementById('echeance_' + i + '_date').value;
										montant_echeance = document.getElementById('echeance_' + i + '_montant').value;
										lisse_echeance = document.getElementById('echeance_' + i + '_lisse').value;
										montant_total_echeances = montant_total_echeances + (montant_echeance.replace(',', '.') * 1);
										//alert('echeance n°' + (montant_echeance.replace(',', '.') * 1));
										
										// On verifie que la date est valide
										if(!est_date(date_echeance, false)) {
											messsage = "<?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_INSC_012); ?>";
											messsage = messsage.replace('#i#', i);
											message_erreur += separateur + "     - " + messsage;
											separateur = "\n";
											valide = false;
											
											
										} else {
											// => date valide : verifier qu'elle est superieure a la precedente
											//                  (seulement a partir de la deuxieme
											
											// On ne verifie que pour les echeances lissees
											if(lisse_echeance == 1) {
												//alert('dp=' + date_precedente);
												if(date_precedente != null) {
													
													date_courante = Date.parse(date_fr_vers_us(date_echeance));
													//alert(date_courante + ' - ' +  date_precedente);
													if(date_courante > date_precedente) {
														date_precedente = date_courante;
													} else {
														messsage = "<?php echo LANG_FIN_INSC_014; ?>";
														messsage = messsage.replace('#i#', i);
														message_erreur += separateur + "     - " + messsage;
														separateur = "\n";
														valide = false;
													}
													
												} else {
													//alert(Date.parse((date_echeance)));
													date_precedente = Date.parse(date_fr_vers_us(date_echeance));
												}
											}
										}
										
										// On verifie que le montant est valide
										if(!est_nombre(montant_echeance, 'decimal', ',')) {
											
											messsage = "<?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_INSC_013); ?>";
											messsage = messsage.replace('#i#', i);
											message_erreur += separateur + "     - " + messsage;
											separateur = "\n";
											valide = false;
										}
										
									}
									
									// Arrondir a deux decimales
									montant_total_echeances = Math.round(montant_total_echeances*100) / 100;
									
									// Verifier si la somme des montants des echeances est egale au montant total de l'inscription
									montant_total_inscription = (document.getElementById('montant_total').value * 1);
									if(montant_total_echeances != montant_total_inscription) {									
										messsage = "<?php echo LANG_FIN_INSC_015; ?>";
										messsage = messsage.replace('#s1#', montant_depuis_bdd(montant_total_echeances, 2, ',', ' '));
										messsage = messsage.replace('#s2#', montant_depuis_bdd(montant_total_inscription, 2, ',', ' '));
										message_erreur += separateur + "     - " + messsage;
										
										separateur = "\n";
										valide = false;
									}
									
									
									if(valide) {
										//alert('Fonctionalité en cours de développement.....');
										msg_util_attente_montrer(true);
										document.formulaire.operation.value = 'inscrire';
										document.formulaire.but_actualiser.click();

									} else {
										alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
									}

									
								} else {
									// => un des elements a ete modifie : message erreur pour calculer de nouveau l'echeancier
									alert("<?php echo LANG_FIN_INSC_009; ?>");
								}
							} else {
								// => echeancier vide : messge erreur et affichage calendrier si la date de debut est vide
								alert("<?php echo LANG_FIN_INSC_010; ?>");
								if(document.formulaire.date_debut.value == '') {
									document.getElementById('anchor18div_date_debut').onclick();
								}
							}
						}
						
						// Pour afficher ou cacher les detail des frais 
						function onclick_detail_frais(echeancier_id) {
							var infos = document.getElementById('liste_' + echeancier_id + '_detail_frais');
							var lien = document.getElementById('groupe_' + echeancier_id + '_detail_frais');
							var texte_lien = document.getElementById('groupe_' + echeancier_id + '_frais_detail');
							
							if(infos.style.display == '') {
								infos.style.display = 'none';
								texte_lien.innerHTML = '[+]';
								lien.alt = "<?php echo LANG_FIN_GENE_044; ?>";
								lien.title = "<?php echo LANG_FIN_GENE_044; ?>";
							} else {
								infos.style.display = '';
								texte_lien.innerHTML = '[-]';
								lien.alt = "<?php echo LANG_FIN_GENE_045; ?>";
								lien.title = "<?php echo LANG_FIN_GENE_045; ?>";
							}
						}
						
						function Change_montant(i,nb)
						{
							montant_total1 = 0.0;
							montant_detail = 0.0;
							for(j=0;j< nb;j++)
							{
								montant_detail = document.getElementById('montant_groupe_echeancier_'+i+'_'+ (j+1)).value;	
								montant_total1 = montant_total1 + (montant_detail.replace(',', '.') * 1);
							}
							montant_total1 = montant_total1.toFixed(2);
							montant_total1 = montant_total1.replace('.', ',');
							document.getElementById('echeance_'+i+'_montant').value = montant_total1;
						}
						
				
													
						function onchange_code_class_insc() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = 'changement_code_class_insc';
							document.formulaire.but_actualiser.click();
						}

						function onchange_annee_scolaire_insc() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = 'changement_annee_scolaire_insc';
							document.formulaire.but_actualiser.click();
						}
						
					</script>
					<!--
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>inscription_rechercher.php" method="post">
						<input type="hidden" name="operation" id="operation" value="<?php echo $operation_rech; ?>">
						<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class_rech; ?>">
						<input type="hidden" name="nom_eleve" id="nom_eleve" value="<?php echo $nom_eleve_rech; ?>">
						<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire_rech; ?>">
					</form>
					-->
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>inscription_dupliquer_echeancier.php" method="post">
						<input type="hidden" name="elev_id_insc" id="elev_id_insc" value="<?php echo $elev_id_insc; ?>">
						<input type="hidden" name="code_class" id="code_class_rech" value="<?php echo $code_class_rech; ?>">
						<input type="hidden" name="nom_eleve" id="nom_eleve_rech" value="<?php echo $nom_eleve_rech; ?>">
					</form>
					
					
				</td>
			</tr>
		</table>


		<?php //********** GENERATION DES MENUS ADMINISTRATEUR ********** ?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></script>
		

		<?php //********** INITIALISATION DES BULLES D'AIDE ********** ?>
		<script language="javascript">InitBulle("#000000","#FCE4BA","red",1);</script>


		<?php //********** TRAITEMENT A EFFECTUER APRES LE CHARGEMENT DE LA PAGE ********** ?>
		<script language="javascript" type="text/javascript">
		
			// Traitement a effectuer apres le chargement de la page
			function initialisation_page() {
				// Preparer la liste des liens a remplacer
				var liens_a_remplacer = new Array();
				liens_a_remplacer[0] = 	{
											"lien_avec" : '<?php echo site_url_racine(FIN_REP_MODULE); ?>#',
											"remplacer_par" : 'javascript:;'
										};
				// Traitements a effectuer sur toutes les pages
				initialisation_page_global(liens_a_remplacer);
				
				griser_degriser_checkbox_lissage();
			}
			
			// Executer initialisation_page() au chargement de la page
			if (window.addEventListener) {
				window.addEventListener("load",initialisation_page,false);
			} else if (window.attachEvent) { 
				window.attachEvent("onload",initialisation_page);
			}	
					
		</script>

		
		<?php
		}
		?>
		
	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>
