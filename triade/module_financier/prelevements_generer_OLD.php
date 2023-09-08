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
//print_r($_POST);
// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");

// Verification autorisations acces au module
if(autorisation_module()) {

	//******************************Initialisation fichier prelevement******************************
	$sql = "SELECT * FROM ".FIN_TAB_CONFIG_ECOLE." ";
	$res = execSql($sql);
	$ligne = &$res->fetchRow();
	
	// Nom du fichier de prelevement
	$g_nom_fichier_prelevement = $ligne[0];
	
	// Donnes constantes utilisees dans le fichier de prelevement
	$g_tab_fichier_prelevement_donnees_vatel = array();
	$tab_tmp = array(
		'CODE' => '03',
		'CODOPE' => '08',
		'B' => '',
		'NUMEMET' => $ligne[1],
		'REF' => '',
		'DATE' => '',
		'ICB' => $ligne[2],
		'DOM' => $ligne[3],
		'B2' => '  E',
		'CG' =>  $ligne[4],
		'COMPT' => $ligne[5], 
		'MT1' => '',
		'LIBELLE' => $ligne[6], 
		'CB' => $ligne[7], 
		'B1' => ''
	);
	$g_tab_fichier_prelevement_donnees_vatel['premiere_ligne'] = $tab_tmp;

	$tab_tmp = array(
		'CODE' => '06',
		'CODOPE' => '08',
		'B' => '',
		'NUMEMET' => $ligne[1],
		'REF' => $ligne[8],
		'DATE' => '',
		'ICB' => '',
		'DOM' => '',
		'B2' => '',
		'CG' => '',
		'COMPT' => '',
		'MT1' => '',
		'LIBELLE' => $ligne[6], 
		'CB' => '',
		'B1' => ''
	);
	$g_tab_fichier_prelevement_donnees_vatel['autre_ligne'] = $tab_tmp;

	$tab_tmp = array(
		'CODE' => '08',
		'CODOPE' => '08',
		'B' => '',
		'NUMEMET' => $ligne[1],
		'REF' => '',
		'DATE' => '',
		'ICB' => '',
		'DOM' => '',
		'B2' => '',
		'CG' => '',
		'COMPT' => '',
		'MT1' => '',
		'LIBELLE' => '',
		'CB' => '',
		'B1' => ''
	);
	$g_tab_fichier_prelevement_donnees_vatel['derniere_ligne'] = $tab_tmp;
	//*************************************************************************************

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$date_limite = lire_parametre('date_limite', '', 'POST');
	$date_reglement = lire_parametre('date_reglement', '', 'POST');
	$liste_elev_id = lire_parametre('liste_elev_id', '', 'POST');
	$liste_numero_rib = lire_parametre('liste_numero_rib', '', 'POST');
	//***************************************************************************

	if($date_limite != '' && $date_reglement != '') {
		
		$total_global = 0.0;
		
	
		//echo "$liste_elev_id <br>";
		//echo "$liste_numero_rib <br>";

		// Recuperer la liste des eleves 
		// Ensuite on va :
		//   - recuperer la liste des echeances de cet eleve
		//   - pour chaque echeance
		//       - recuperer le RIB selectionne
		//       - si le RIB n'existe pas dans le tableau $prelevements => l'ajouter
		//       - si le RIB existe deja dans le tableau $prelevements => ajouter au total
		$tab_prelevements = array();
		//$tab_elev_id = array();
		$tab_echeances = array();
		if($liste_elev_id != '') {
			$tmp_elev_id = preg_split('/,/', $liste_elev_id);
			for($i=0; $i<count($tmp_elev_id); $i++) {
				// Recuperer la liste des echeances pour cet eleve
				$sql  = "SELECT ec.echeancier_id, ec.montant, ec.date_echeance, i.date_depart ";
				$sql .= "FROM ".FIN_TAB_ECHEANCIER." ec ";
				$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON ec.inscription_id = i.inscription_id ";
				$sql .= "WHERE ec.date_echeance <= '" . date_vers_bdd($date_limite) . "' ";
				$sql .= "AND ec.type_reglement_id = " . $g_tab_type_reglement_id['prelevement'] . " ";
				$sql .= "AND i.elev_id = " . $tmp_elev_id[$i] . " ";
				$sql .= "ORDER BY ec.date_echeance ASC";
				$echeances = execSql($sql);
				$total_a_payer = 0.0;
				if($echeances->numRows() > 0) {
					//echo $sql . "<br><br>";
					// Recuperer les infos de chaque echeance a traiter
					
					$nb ="SELECT max(numero) ";
					$nb.="FROM ".FIN_TAB_REGLEMENT." ";
					$res = execSql($nb);
					$ligne_nb = $res->fetchRow();
					$num = $ligne_nb[0] + 1;
					$datedujour = date("Y-m-d H:i:s");
					for($k=0; $k<$echeances->numRows(); $k++) {
						// Recuperer les infos de l'echeance
						$ligne_echeance = $echeances->fetchRow();

						// 20100805 - AP : on doit verifier que l'echeance n'est pas posterieure a la date de depart 
						//                 (date de sortie)
						$posterieure_date_sortie = false;
						$date_echeance_tmp = trim($ligne_echeance[2]);
						$date_echeance_tmp = str_replace("-", "", $date_echeance_tmp);
						$date_depart_tmp = trim($ligne_echeance[3]);
						$date_depart_tmp = str_replace("-", "", $date_depart_tmp);
						if($date_depart_tmp != '' && $date_echeance_tmp > $date_depart_tmp) {
							$posterieure_date_sortie = true;
						}
						
						if(!$posterieure_date_sortie) {
						
							// Recupere la position du RIB selectionne (liste deroulante)
							$id_select = "numero_rib_" . $tmp_elev_id[$i] . "_" . $ligne_echeance[0];
							$numero_rib = lire_parametre($id_select, '', 'POST');
							
							// On continue seulement si on a reussi a lire la position du RIB
							if($numero_rib != '' && $numero_rib != '0') {
								//echo $id_select . "<br>";
								// On verifie si il y a bien un RIB a cette position pour cet eleve
								$sql  = "SELECT rib_id, code_banque, code_guichet, numero_compte, cle_rib, titulaire, banque ";
								$sql .= "FROM ".FIN_TAB_RIB." ";
								$sql .= "WHERE elev_id = " . $tmp_elev_id[$i] . " ";
								$sql .= "AND numero_rib = " . $numero_rib . " ";
								//echo $sql;
								$rib=execSql($sql);
								if($rib->numRows()) {
									// Recuperer la ligne d'infos du RIB
									$ligne_rib = $rib->fetchRow();
									
									// Calculer combien il reste a payer pour l'échéance
									$reste_a_payer = reglement_reste_a_payer('echeance', $ligne_echeance[0]);
									
									// On verifie si il reste a payer quelque chose pour cette echeance
									// Ainsi, si on lance deux fois la generation, ca ne genere pas deux fois les prelevements
									if($reste_a_payer > 0) {
										// Guarder les infos de l'echeance (et les preparer pour ajout des reglements
										// Une fois le fichier genere
										
										$tab_echeances[count($tab_echeances)] = array(
																					'echeancier_id' => $ligne_echeance[0],
																					'libelle' => LANG_FIN_GPRE_010 . ' ' . date_depuis_bdd($ligne_echeance[2]),
																					'date_reglement' => date_vers_bdd($date_reglement),
																					'montant' => $reste_a_payer,
																					'type_reglement_id' => $g_tab_type_reglement_id['prelevement'],
																					'realise' => 1,
																					'commentaire' => '',
																					'date_enregistrement' => $datedujour,
																					'numero' => $num,
																					'numero_bordereau' => '',
																					'numero_cheque' => '',
																					'rib_id' =>  $ligne_rib[0],
																					'numero_rib' =>  $numero_rib,
																					'code_banque' =>  $ligne_rib[1],
																					'code_guichet' =>  $ligne_rib[2],
																					'numero_compte' =>  $ligne_rib[3],
																					'cle_rib' =>  $ligne_rib[4],
																					'titulaire' =>  $ligne_rib[5],
																					'banque' =>  $ligne_rib[6],
																					'total_a_payer' =>  $reste_a_payer	
																				);							
										
										// Verifier si le RIB est deja dans $tab_prelevements
										$rib_existe_deja = false;
										$pos_rib_prelevements = count($tab_prelevements);
										for($l=0; $l < count($tab_prelevements); $l++) {
											if($tab_prelevements[$l]['rib_id'] == $ligne_rib[0]) {
												$rib_existe_deja = true;
												$pos_rib_prelevements = $l;
												break;
											}
										}
								
										//$rib_existe_deja = false;
										//$pos_rib_prelevements = count($tab_prelevements);
										if(!$rib_existe_deja) {
											// => on ajoute une nouvelle ligne de prelevement
											$tab_prelevements[$pos_rib_prelevements] = array(
																				'rib_id' =>  $ligne_rib[0],
																				'elev_id' =>  $tmp_elev_id[$i],
																				'numero_rib' =>  $numero_rib,
																				'code_banque' =>  $ligne_rib[1],
																				'code_guichet' =>  $ligne_rib[2],
																				'numero_compte' =>  $ligne_rib[3],
																				'cle_rib' =>  $ligne_rib[4],
																				'titulaire' =>  $ligne_rib[5],
																				'banque' =>  $ligne_rib[6],
																				'total_a_payer' =>  $reste_a_payer
																				);
										} else {
											// => on ajoute le total a payer a la ligne de prelevement deja existante
											$tab_prelevements[$pos_rib_prelevements]['total_a_payer'] += $reste_a_payer;
										}
										
										$total_global += $reste_a_payer;
									}
								}
							}
							
						}
					}
				}								
			}
		}


		//echo "<pre>";
		//print_r($tab_prelevements);
		//echo "</pre>";
		//exit;

	
		// Pour que le resultat ne soit pas mis en cache
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		
		// Type de document : texte
		header('Content-type: text/plain');

		//Forcer le telechargement (avec le nom du fichier)
		$nom_fichier = $g_nom_fichier_prelevement . '_' . substr($date_reglement, 6, 4) . substr($date_reglement, 3, 2) . substr($date_reglement, 0, 2) . '.txt';

		header('Content-Disposition: attachment; filename="' . $nom_fichier . '"');
	 

		//print_r($tab_elev_id);

		// Renvoyer les donnees pour chacun des prelevements
		$separateur_ligne = chr(13) . chr(10);
		$fin_de_fichier = chr(26);
		if(count($tab_prelevements) > 0) {
			
			//$date_prelevement = date('d') . date('m') . substr(date('Y'), 0, 1);
			$date_prelevement = date_vers_bdd($date_reglement);
			$date_prelevement = substr($date_prelevement, 8, 2) . substr($date_prelevement, 5, 2) . substr($date_prelevement, 3, 1);

			// Envoyer la premiere ligne
			echo prelevement_formatter_champ('CODE', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CODE']);
			echo prelevement_formatter_champ('CODOPE', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CODOPE']);
			echo prelevement_formatter_champ('B', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['B']);
			echo prelevement_formatter_champ('NUMEMET', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['NUMEMET']);
			echo prelevement_formatter_champ('REF', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['REF']);
			echo prelevement_formatter_champ('DATE', $date_prelevement);
			echo prelevement_formatter_champ('ICB', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['ICB']);
			echo prelevement_formatter_champ('DOM', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['DOM']);
			echo prelevement_formatter_champ('B2', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['B2']);
			echo prelevement_formatter_champ('CG', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CG']);
			echo prelevement_formatter_champ('COMPT', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['COMPT']);
			echo prelevement_formatter_champ('MT1', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['MT1'], ' ');
			echo prelevement_formatter_champ('LIBELLE', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['LIBELLE']);
			echo prelevement_formatter_champ('CB', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CB']);
			echo prelevement_formatter_champ('B1', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['B1']);
			echo $separateur_ligne;

			for($i=0; $i<count($tab_prelevements); $i++) {
				// Envoyer la ligne de l'eleve
				echo prelevement_formatter_champ('CODE', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['CODE']);
				echo prelevement_formatter_champ('CODOPE', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['CODOPE']);
				echo prelevement_formatter_champ('B', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['B']);
				echo prelevement_formatter_champ('NUMEMET', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['NUMEMET']);
				echo prelevement_formatter_champ('REF', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['REF']);
				echo prelevement_formatter_champ('DATE', $date_prelevement);
				echo prelevement_formatter_champ('ICB', strtoupper($tab_prelevements[$i]["titulaire"]));
				echo prelevement_formatter_champ('DOM', strtoupper($tab_prelevements[$i]["banque"]));
				echo prelevement_formatter_champ('B2', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['B2']);
				echo prelevement_formatter_champ('CG', strtoupper($tab_prelevements[$i]["code_guichet"]));
				echo prelevement_formatter_champ('COMPT', strtoupper($tab_prelevements[$i]["numero_compte"]));
				echo prelevement_formatter_champ('MT1', montant_vers_fichier_prelevement($tab_prelevements[$i]["total_a_payer"]));
				echo prelevement_formatter_champ('LIBELLE', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['LIBELLE']);
				echo prelevement_formatter_champ('CB', strtoupper($tab_prelevements[$i]["code_banque"]));
				echo prelevement_formatter_champ('B1', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['B1']);
				echo $separateur_ligne;
			}

			// Envoyer la derniere ligne
			echo prelevement_formatter_champ('CODE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CODE']);
			echo prelevement_formatter_champ('CODOPE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CODOPE']);
			echo prelevement_formatter_champ('B', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['B']);
			echo prelevement_formatter_champ('NUMEMET', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['NUMEMET']);
			echo prelevement_formatter_champ('REF', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['REF']);
			echo prelevement_formatter_champ('DATE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['DATE']);
			echo prelevement_formatter_champ('ICB', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['ICB']);
			echo prelevement_formatter_champ('DOM', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['DOM']);
			echo prelevement_formatter_champ('B2', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['B2']);
			echo prelevement_formatter_champ('CG_DERNIERE_LIGNE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CG']);
			echo prelevement_formatter_champ('COMPT_DERNIERE_LIGNE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['COMPT']);
			echo prelevement_formatter_champ('MT1', montant_vers_fichier_prelevement($total_global));
			echo prelevement_formatter_champ('LIBELLE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['LIBELLE']);
			echo prelevement_formatter_champ('CB_DERNIERE_LIGNE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CB']);
			echo prelevement_formatter_champ('B1', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['B1']);
			echo $separateur_ligne;
			//echo chr(26); // Pour ajouter le caractere 'SUB' (0x1A)
			echo $separateur_ligne;

		}
		
		// Ajouter les enregistrements de reglement
		if(count($tab_echeances) > 0) {
			for($i=0; $i<count($tab_echeances); $i++) {	
			//	$tab_temp = prelevement_valeur($tab_echeances[$i]['echeancier_id'], $tab_prelevements);
			
				$titulaire = addslashes($tab_echeances[$i]['titulaire']);
				$banque = addslashes($tab_echeances[$i]['banque']);
				$sql= "INSERT INTO ".FIN_TAB_REGLEMENT." (echeancier_id, libelle, date_reglement, montant, type_reglement_id, realise, commentaire, date_enregistrement, numero, numero_bordereau, numero_cheque, rib_id_utilise, code_banque_utilise, code_guichet_utilise, numero_compte_utilise, cle_rib_utilise, titulaire_utilise, banque_utilise, reste_a_payer ) ";
				$sql.="VALUES(";
				$sql.="".$tab_echeances[$i]['echeancier_id'].", ";
				$sql.="'".esc($tab_echeances[$i]['libelle'])."', ";
				$sql.="'".$tab_echeances[$i]['date_reglement']."', ";
				$sql.="".$tab_echeances[$i]['montant'].", ";
				$sql.="".$tab_echeances[$i]['type_reglement_id'].", ";
				$sql.="".$tab_echeances[$i]['realise'].", ";
				$sql.="'".esc($tab_echeances[$i]['commentaire'])."', ";
				$sql.="'".$tab_echeances[$i]['date_enregistrement']."', ";
				$sql.="'".$tab_echeances[$i]['numero']."', ";
				$sql.="'".$tab_echeances[$i]['numero_bordereau']."', ";
				$sql.="'".$tab_echeances[$i]['numero_cheque']."', ";
				$sql.="'".$tab_echeances[$i]['rib_id']."', ";
				$sql.="'".$tab_echeances[$i]['code_banque']."', ";
				$sql.="'".$tab_echeances[$i]['code_guichet']."', ";
				$sql.="'".$tab_echeances[$i]['numero_compte']."', ";
				$sql.="'".$tab_echeances[$i]['cle_rib']."', ";
				$sql.="'".$titulaire."', ";
				$sql.="'".$banque."', ";
				$sql.="'".$tab_echeances[$i]['total_a_payer']."' ";
				$sql.="); ";
				$res=execSql($sql);
			}
		}	
	} else {
		exit();
	}


	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	//***************************************************************************
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

// Verification droits acces groupe
validerequete("2");

// Fermeture connexion bddd
Pgclose();
?>
