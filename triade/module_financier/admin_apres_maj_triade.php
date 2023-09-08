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
	$operation = lire_parametre('operation', '', 'POST');
	//***************************************************************************



	//****************************** FONCTIONS **********************************

	// Rechercher un texte donne dans un fichier
	function chercher_position_insertion($fichier_a_traiter, $texte_a_rechercher, $operation) {
		//$tab_resultat = array();
		//$tab_resultat['ligne'] = 0;
		$ligne = 0;
		
		$texte_concatene = '';
		
		$texte_a_rechercher_nettoye = str_replace("\n", "", $texte_a_rechercher);
		$texte_a_rechercher_nettoye = str_replace("\r", "", $texte_a_rechercher_nettoye);
		$texte_a_rechercher_nettoye = str_replace(" ", "", $texte_a_rechercher_nettoye);
		$texte_a_rechercher_nettoye = str_replace("\t", "", $texte_a_rechercher_nettoye);
		$texte_a_rechercher_nettoye = strtolower($texte_a_rechercher_nettoye);
		
		if(file_exists($fichier_a_traiter)) {
			$lignes = file($fichier_a_traiter);
			
			switch($operation) {	
				case 'inserer_texte_apres':
					for($i=0; $i<count($lignes); $i++) {
						$texte_concatene .= $lignes[$i];
						$texte_concatene_nettoye = str_replace("\n", "", $texte_concatene);
						$texte_concatene_nettoye = str_replace("\r", "", $texte_concatene_nettoye);
						$texte_concatene_nettoye = str_replace(" ", "", $texte_concatene_nettoye);
						$texte_concatene_nettoye = str_replace("\t", "", $texte_concatene_nettoye);
						$texte_concatene_nettoye = strtolower($texte_concatene_nettoye);
						//echo ($i + 1) . ' - ' . $lignes[$i] . '<br>';
						if(strpos($texte_concatene_nettoye, $texte_a_rechercher_nettoye) !== false) {
							$ligne = $i + 1;
							break;
						}
					}
					break;
				case 'inserer_texte_fin':
					$ligne = count($lignes);
					break;
				case 'remplacer_texte':
					for($i=0; $i<count($lignes); $i++) {
						$texte_concatene .= $lignes[$i];
						$texte_concatene_nettoye = str_replace("\n", "", $texte_concatene);
						$texte_concatene_nettoye = str_replace("\r", "", $texte_concatene_nettoye);
						$texte_concatene_nettoye = str_replace(" ", "", $texte_concatene_nettoye);
						$texte_concatene_nettoye = str_replace("\t", "", $texte_concatene_nettoye);
						$texte_concatene_nettoye = strtolower($texte_concatene_nettoye);
						//echo ($i + 1) . ' - ' . $lignes[$i] . '<br>';
						if(strpos($texte_concatene_nettoye, $texte_a_rechercher_nettoye) !== false) {
							$ligne = $i + 1;
							break;
						}
					}
					break;
			}
		}
		return($ligne);
	}
	
	// Verifier si un fichier a deja ete traite ou non
	function fichier_deja_traite($fichier_a_traiter) {
		$trouve = false;
		$texte_deja_traite = "APRES_MAJ_TRIADE_AUTO";
		if(file_exists($fichier_a_traiter)) {
			$texte = file_get_contents($fichier_a_traiter);
			if(strpos($texte	, $texte_deja_traite) !== false) {
				$trouve = true;
			}
		}
		//echo $fichier_a_traiter . $trouve;
		return($trouve);
	}
	
	// Fait une copie de sauvegarde du fichier 'fichier_a_traiter' puis y insere le contenu de $fichier_donnees apres la ligne 'ligne'
	function inserer_donnees($fichier_a_traiter, $fichier_donnees, $ligne) {
		$code_erreur = 'OK';
		// Verifier que le fichier 'fichier_a_traiter' existe bien
		if(file_exists($fichier_a_traiter)) {
			// Verifier que le fichier 'fichier_donnees' existe bien
			if(file_exists($fichier_donnees)) {
				// Lire 'fichier_a_traiter'
				$lignes_fichier_a_traiter = file($fichier_a_traiter);
				// Verifier que $ligne ne depasse pas le nombre de lignes du fichier 'fichier_a_traiter'
				if($ligne <= count($lignes_fichier_a_traiter)) {
					$date_heure = date("YmdHis");
					// Faire une copie de sauvegarde de 'fichier_a_traiter' en fichier_a_traiter_apres_maj_triade_auto_AAAAMMJJHHMMSS
					$elements_chemin = pathinfo($fichier_a_traiter);
					$fichier_a_traiter_copie = $elements_chemin['dirname'] . DIRECTORY_SEPARATOR . str_replace('.'.$elements_chemin['extension'], "", $elements_chemin['basename']) . '_apres_maj_triade_' . $date_heure . '.' . $elements_chemin['extension'];
					//echo $fichier_a_traiter_copie . '<br>';
					if(@copy($fichier_a_traiter, $fichier_a_traiter_copie)) {
						// Lire 'fichier_donnees'
						$texte_fichier_donnees = file_get_contents($fichier_donnees);
						
						// Remplacer les balises
						$texte_fichier_donnees = str_replace("[APRES_MAJ_TRIADE_AUTO_DATE_TRAITEMENT]", $date_heure, $texte_fichier_donnees);
						$texte_fichier_donnees = str_replace("[APRES_MAJ_TRIADE_AUTO_ENTITE]", "IGONE", $texte_fichier_donnees);
						
						// Guarder le debut de 'fichier_a_traiter'
						$texte_final = '';
						for($i=0; $i<$ligne; $i++) {
							$texte_final .= $lignes_fichier_a_traiter[$i] . "";
						}
						
						// Ajouter les donnees
						$texte_final .= "\n\r" . $texte_fichier_donnees . "\n\r";

						// Guarder la fin de 'fichier_a_traiter'
						for($i=$ligne; $i<count($lignes_fichier_a_traiter); $i++) {
							$texte_final .= $lignes_fichier_a_traiter[$i] . "";
						}
						//echo $texte_final;
						if(!@file_put_contents($fichier_a_traiter, $texte_final)) {
							$code_erreur = 'FICHIER_A_TRAITER_ECRITURE_IMPOSSIBLE';
						}
						
					} else {
						$code_erreur = 'FICHIER_A_TRAITER_COPIE_IMPOSSIBLE';
					}
				} else {
					$code_erreur = 'LIGNE_EXISTE_PAS';
				}
			} else {
				$code_erreur = 'FICHIER_DONNEES_EXISTE_PAS';
			}
		} else {
			$code_erreur = 'FICHIER_A_TRAITER_EXISTE_PAS';
		}
		return($code_erreur);
	}
	

	// Fait une copie de sauvegarde du fichier 'fichier_a_traiter' puis y remplacer le texte par le contenu de $fichier_donnees
	function remplacer_texte($fichier_a_traiter, $fichier_donnees, $texte_a_remplacer) {
		$code_erreur = 'OK';
		// Verifier que le fichier 'fichier_a_traiter' existe bien
		if(file_exists($fichier_a_traiter)) {
			// Verifier que le fichier 'fichier_donnees' existe bien
			if(file_exists($fichier_donnees)) {
				// Lire 'fichier_a_traiter'
				$texte_fichier_a_traiter = file_get_contents($fichier_a_traiter);
				$date_heure = date("YmdHis");
				// Faire une copie de sauvegarde de 'fichier_a_traiter' en fichier_a_traiter_apres_maj_triade_auto_AAAAMMJJHHMMSS
				$elements_chemin = pathinfo($fichier_a_traiter);
				$fichier_a_traiter_copie = $elements_chemin['dirname'] . DIRECTORY_SEPARATOR . str_replace('.'.$elements_chemin['extension'], "", $elements_chemin['basename']) . '_apres_maj_triade_' . $date_heure . '.' . $elements_chemin['extension'];
				//echo $fichier_a_traiter_copie . '<br>';
				if(@copy($fichier_a_traiter, $fichier_a_traiter_copie)) {
					// Lire 'fichier_donnees'
					$texte_fichier_donnees = file_get_contents($fichier_donnees);
					
					// Remplacer les balises
					$texte_fichier_donnees = str_replace("[APRES_MAJ_TRIADE_AUTO_DATE_TRAITEMENT]", $date_heure, $texte_fichier_donnees);
					$texte_fichier_donnees = str_replace("[APRES_MAJ_TRIADE_AUTO_ENTITE]", "IGONE", $texte_fichier_donnees);
					
					
					$texte_final = str_replace($texte_a_remplacer, $texte_fichier_donnees, $texte_fichier_a_traiter);
					
					if(!@file_put_contents($fichier_a_traiter, $texte_final)) {
						$code_erreur = 'FICHIER_A_TRAITER_ECRITURE_IMPOSSIBLE';
					}
					
				} else {
					$code_erreur = 'FICHIER_A_TRAITER_COPIE_IMPOSSIBLE';
				}
			} else {
				$code_erreur = 'FICHIER_DONNEES_EXISTE_PAS';
			}
		} else {
			$code_erreur = 'FICHIER_A_TRAITER_EXISTE_PAS';
		}
		return($code_erreur);
	}
	//***************************************************************************


	//****************** LISTE DES FICHIERS A MODIFIER **************************

	
	$tab_fichiers = array();
	
	// fichier_a_traiter : fichier (avec chemin) a modifier
	// fichier_a_traiter_description : description du 'fichier_a_traiter'
	// operation : 'inserer_texte_apres'|'inserer_texte_fin'
	// texte_a_rechercher : le texte qui sera recherche dans le 'fichier_a_traiter'
	// fichier_donnees : fichier (avec chemin) qui contient le texte qui sera insere ou qui remplacera
	// ligne_inserer_apres : ligne du 'fichier_a_traiter' apres laquelle les donnees seront inserees (milieu ou fin : depend de 'operation')
	// fichier_a_traiter_existe : (true|false) indique si le 'fichier_a_traiter' a ete trouve
	// fichier_donnees_existe : (true|false) indique si le 'fichier_donnees' a ete trouve
	// texte_a_rechercher_existe : (true|false) indique si le 'texte_a_rechercher_existe' a ete trouve dans 'fichier_a_traiter'
	// erreurs : (integer) indique le nombre d'erreur trouvees
	
	
	// Script affichage menu admin 'menuadmin2.js'
	$tab_fichiers[0] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'librairie_js' . DIRECTORY_SEPARATOR . 'menuadmin2.js',
								'fichier_a_traiter_description' => 'Script javascript qui affiche le menu d\'administration (à droite)',
								'operation' => 'inserer_texte_apres',
								'texte_a_rechercher' => 'href=\'./gestion_stage.php\'>"+langmenuadmin517+"</a><br>");document.write("</p></td>");',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'librairie_js' . DIRECTORY_SEPARATOR . 'menuadmin2.js',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);

	// Traduction des menus en arabe 'langue-menu-arabe.js'
	$tab_fichiers[1] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-arabe.js',
								'fichier_a_traiter_description' => 'Traduction des menus en arabe',
								'operation' => 'inserer_texte_fin',
								'texte_a_rechercher' => '',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-arabe.js',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);

	// Traduction des menus en breton 'langue-menu-bret.js'
	$tab_fichiers[2] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-bret.js',
								'fichier_a_traiter_description' => 'Traduction des menus en breton',
								'operation' => 'inserer_texte_fin',
								'texte_a_rechercher' => '',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-bret.js',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);

	// Traduction des menus en anglais 'langue-menu-en.js'
	$tab_fichiers[3] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-en.js',
								'fichier_a_traiter_description' => 'Traduction des menus en anglais',
								'operation' => 'inserer_texte_fin',
								'texte_a_rechercher' => '',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-en.js',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);

	// Traduction des menus en espagnol 'langue-menu-es.js'
	$tab_fichiers[4] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-es.js',
								'fichier_a_traiter_description' => 'Traduction des menus en espagnol',
								'operation' => 'inserer_texte_fin',
								'texte_a_rechercher' => '',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-es.js',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);

	// Traduction des menus en francais 'langue-menu-fr.js'
	$tab_fichiers[5] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-fr.js',
								'fichier_a_traiter_description' => 'Traduction des menus en francais',
								'operation' => 'inserer_texte_fin',
								'texte_a_rechercher' => '',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'librairie_js' . DIRECTORY_SEPARATOR . 'langue-menu-fr.js',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);


	// Proposer de saisir le RIB apres la creation de l'eleve 'creat_eleve.php'
	$tab_fichiers[6] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'creat_eleve.php',
								'fichier_a_traiter_description' => 'Proposer de saisir le RIB apres la création de l\'élève',
								'operation' => 'inserer_texte_apres',
								'texte_a_rechercher' => 'history_cmd($_SESSION["nom"],"CREATION","élève ".$_POST["saisie_nom"]);alertJs(LANGELE28);',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'creat_eleve.php',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);

	// Boutons dans la fiche de l'eleve 'edit_eleve.php'
	$tab_fichiers[7] = array(
								'fichier_a_traiter' => site_repertoire_racine(FIN_REP_MODULE) . 'edit_eleve.php',
								'fichier_a_traiter_description' => 'Boutons dans la fiche de l\'élève',
								'operation' => 'remplacer_texte',
								'texte_a_rechercher' => '<div align=right><input type=button value="<?php print LANGBT52?>" onclick="open(\'modif_eleve.php?eid=<?php print $data[0][0]?>\',\'_parent\',\'\')"  class="bouton2"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR><BR></td></tr>',
								'fichier_donnees' => site_repertoire_racine(FIN_REP_MODULE) . DIRECTORY_SEPARATOR .  FIN_REP_MODULE . DIRECTORY_SEPARATOR . 'admin_apres_maj_triade' . DIRECTORY_SEPARATOR . 'edit_eleve.php',
								'ligne_ou_inserer' => 0,
								'fichier_a_traiter_existe' => false,
								'fichier_donnees_existe' => false,
								'texte_a_rechercher_existe' => false,
								'fichier_a_traiter_deja_modifie' => false,
								'erreurs' => 0,
								'erreurs_traitements' => 0,
								'erreurs_traitements_messages' => ''
							);
	
	
	$total_erreurs = 0;
	$total_non_traites = 0;
	$total_deja_traites = 0;
	$total_traites_succes = 0;
	$total_traites_erreur = 0;
	for($i=0; $i<count($tab_fichiers); $i++) {
		
		// Verifier si le 'fichier_a_traiter' existe sur le serveur
		if(file_exists($tab_fichiers[$i]['fichier_a_traiter'])) {
			$tab_fichiers[$i]['fichier_a_traiter_existe'] = true;
			// Verifier si le fichier a deja ete traite precedement
			$tab_fichiers[$i]['fichier_a_traiter_deja_modifie'] = fichier_deja_traite($tab_fichiers[$i]['fichier_a_traiter']);
			if($tab_fichiers[$i]['fichier_a_traiter_deja_modifie']) {
				$total_deja_traites++;
			} else {
				$total_non_traites++;
			}
		} else {
			// Le fichier n'existe pas  => erreur
			$tab_fichiers[$i]['fichier_a_traiter_existe'] = false;
			$tab_fichiers[$i]['erreurs']++;
		}
		
		// On ne genere pas d'erreur si le fichier a deja ete modifie
		if(!$tab_fichiers[$i]['fichier_a_traiter_deja_modifie']) {
			// Verifier si le 'fichier_donnees' existe sur le serveur
			if(file_exists($tab_fichiers[$i]['fichier_donnees'])) {
				$tab_fichiers[$i]['fichier_donnees_existe'] = true;
			} else {
				// Le fichier n'existe pas  => erreur
				$tab_fichiers[$i]['fichier_donnees_existe'] = false;
				$tab_fichiers[$i]['erreurs']++;
			}
		} else {
			// Verifier si le 'fichier_donnees' existe sur le serveur
			if(file_exists($tab_fichiers[$i]['fichier_donnees'])) {
				$tab_fichiers[$i]['fichier_donnees_existe'] = true;
			}
		}
		
		// Si le 'fichier_a_traiter' existe sur le serveur => verifier l'exitence du texte a remplacer
		if($tab_fichiers[$i]['fichier_a_traiter_existe']) {
			// Rechercher le texte dans 'fichier_a_traiter'
			$ligne = chercher_position_insertion($tab_fichiers[$i]['fichier_a_traiter'], $tab_fichiers[$i]['texte_a_rechercher'], $tab_fichiers[$i]['operation']);
			if($ligne > 0) {
				$tab_fichiers[$i]['texte_a_rechercher_existe'] = true;
				$tab_fichiers[$i]['ligne_inserer_apres'] = $ligne;
				
			} else {
				$tab_fichiers[$i]['texte_a_rechercher_existe'] = false;
				$tab_fichiers[$i]['ligne_inserer_apres'] = 0;
				// On ne genere pas d'erreur si le fichier a deja ete modifie
				if(!$tab_fichiers[$i]['fichier_a_traiter_deja_modifie']) {
					$tab_fichiers[$i]['erreurs']++;
				}
			}
		}
		if($tab_fichiers[$i]['erreurs'] > 0) {
			$total_erreurs += $tab_fichiers[$i]['erreurs'];
		}
	}
	
	//***************** TRAITER L'OPERATION DEMANDEE ****************************
	if($operation == 'traiter_les_fichiers') {
		//print_r($tab_fichiers);
		// Verifier pour chaque fichier si il doit etre traiter
		for($i=0; $i<count($tab_fichiers); $i++) {
			// On traite si il n'y a pas d'erreur et qu'il n'a pas deja ete traite
			if($tab_fichiers[$i]['erreurs'] == 0 && !$tab_fichiers[$i]['fichier_a_traiter_deja_modifie']) {
				//echo $i . ' - ' . $tab_fichiers[$i]['fichier_a_traiter'] . '<br>';

				switch($tab_fichiers[$i]['operation']) {
					case 'inserer_texte_apres':
					case 'inserer_texte_fin':
						$code_erreur = inserer_donnees($tab_fichiers[$i]['fichier_a_traiter'], $tab_fichiers[$i]['fichier_donnees'], $tab_fichiers[$i]['ligne_inserer_apres']);
						break;
					case 'remplacer_texte':
						$code_erreur = remplacer_texte($tab_fichiers[$i]['fichier_a_traiter'], $tab_fichiers[$i]['fichier_donnees'], $tab_fichiers[$i]['texte_a_rechercher']);
						break;
				}

				// Verifier si il y a eu une erreur				
				if($code_erreur != 'OK') {
					// Traiter l'erreur
					switch($code_erreur) {
						case 'FICHIER_A_TRAITER_EXISTE_PAS':
							$tab_fichiers[$i]['erreurs_traitements']++;
							$total_traites_erreur++;
							$separateur = '';
							if($tab_fichiers[$i]['erreurs_traitements_messages'] != '') {
								$separateur = '<br>';
							}
							$tab_fichiers[$i]['erreurs_traitements_messages'] .= $separateur . "Fichier n°" . ($i + 1) . " : '" . $tab_fichiers[$i]['fichier_a_traiter'] . "' introuvable.";
							break;
						case 'FICHIER_DONNEES_EXISTE_PAS':
							$tab_fichiers[$i]['erreurs_traitements']++;
							$total_traites_erreur++;
							$separateur = '';
							if($tab_fichiers[$i]['erreurs_traitements_messages'] != '') {
								$separateur = '<br>';
							}
							$tab_fichiers[$i]['erreurs_traitements_messages'] .= $separateur . "Fichier n°" . ($i + 1) . " : '" . $tab_fichiers[$i]['fichier_a_traiter'] . "' introuvable.";
							break;
						case 'LIGNE_EXISTE_PAS':
							$tab_fichiers[$i]['erreurs_traitements']++;
							$total_traites_erreur++;
							$separateur = '';
							if($tab_fichiers[$i]['erreurs_traitements_messages'] != '') {
								$separateur = '<br>';
							}
							$tab_fichiers[$i]['erreurs_traitements_messages'] .= $separateur . "Fichier n°" . ($i + 1) . " : ligne n°" . $tab_fichiers[$i]['ligne_inserer_apres'] . " introuvable.";
							break;
						case 'FICHIER_A_TRAITER_COPIE_IMPOSSIBLE':
							$tab_fichiers[$i]['erreurs_traitements']++;
							$total_traites_erreur++;
							$separateur = '';
							if($tab_fichiers[$i]['erreurs_traitements_messages'] != '') {
								$separateur = '<br>';
							}
							$tab_fichiers[$i]['erreurs_traitements_messages'] .= $separateur . "Fichier n°" . ($i + 1) . " : impossible de copier '" . $tab_fichiers[$i]['fichier_a_traiter'] . "'.";
							break;
						case 'FICHIER_A_TRAITER_ECRITURE_IMPOSSIBLE':
							$tab_fichiers[$i]['erreurs_traitements']++;
							$total_traites_erreur++;
							$separateur = '';
							if($tab_fichiers[$i]['erreurs_traitements_messages'] != '') {
								$separateur = '<br>';
							}
							$tab_fichiers[$i]['erreurs_traitements_messages'] .= $separateur . "Fichier n°" . ($i + 1) . " : impossible d'écrire dans '" . $tab_fichiers[$i]['fichier_a_traiter'] . "'.";
							break;
							
							
					}

				} else {
					$total_traites_succes++;
				}
			}
		}
		
		
		// Reverifier apres les traitements
		$total_deja_traites = 0;
		$total_non_traites = 0;
		for($i=0; $i<count($tab_fichiers); $i++) {
			// Verifier si le fichier a deja ete traite precedement
			$tab_fichiers[$i]['fichier_a_traiter_deja_modifie'] = fichier_deja_traite($tab_fichiers[$i]['fichier_a_traiter']);
			if($tab_fichiers[$i]['fichier_a_traiter_deja_modifie']) {
				$total_deja_traites++;
			} else {
				$total_non_traites++;
			}
		}
		
		
	}
	//***************************************************************************

	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	if($total_traites_succes > 0) {
		msg_util_ajout($total_traites_succes . " fichier(s) traité(s) sans erreur.", 'message');
	}
	if($total_traites_erreur > 0) {
		msg_util_ajout($total_traites_erreur . " fichier(s) traité(s) avec erreur.", 'erreur');
	}
	for($i=0; $i<count($tab_fichiers); $i++) {
		if($tab_fichiers[$i]['erreurs_traitements'] > 0) {
			msg_util_ajout($tab_fichiers[$i]['erreurs_traitements_messages'], 'erreur');
		}
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:10px;margin-right:10px;">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" >Mise à jour des scripts de Triade après upgrade de Triade</font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						<input type="hidden" name="operation" id="operation" value="">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="left">Nombre total d'erreurs : <?php echo $total_erreurs; ?></td>
							</tr>
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">
						
									<table cellspacing="1" cellpadding="0" border="0" bgcolor="">
										<?php
											for($i=0; $i<count($tab_fichiers); $i++) {
										?>
													<tr>
														<td align="left" nowrap="nowrap" colspan="2">Fichier n°<?php echo $i + 1;?> :&nbsp;</td>
													</tr>
													<tr>
														<td align="left" nowrap="nowrap">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
														<td align="left" nowrap="nowrap">
															<table cellspacing="1" cellpadding="0" border="0" bgcolor="" align="left">
																<tr>
																	<td align="right" nowrap="nowrap">Description :&nbsp;</td>
																	<td align="left" nowrap="nowrap"><?php echo $tab_fichiers[$i]['fichier_a_traiter_description']; ?></td>
																</tr>
																<tr>
																	<td align="right" nowrap="nowrap">Fichier à traiter :&nbsp;</td>
																	<td align="left" nowrap="nowrap"><?php echo $tab_fichiers[$i]['fichier_a_traiter']; ?></td>
																</tr>
																<tr>
																	<td align="right" nowrap="nowrap">Fichier déjà traité ? :&nbsp;</td>
																	<td align="left" nowrap="nowrap">
																		<?php
																		if($tab_fichiers[$i]['fichier_a_traiter_deja_modifie']) {
																			echo '<font color="#33FF00"><b>Oui</b></font>';
																		} else {
																			echo 'Non';
																		}
																		?>
																	</td>
																</tr>
																<tr>
																	<td align="right" nowrap="nowrap">Fichier à traiter existe ? :&nbsp;</td>
																	<td align="left" nowrap="nowrap">
																		<?php
																		if($tab_fichiers[$i]['fichier_a_traiter_existe']) {
																			echo 'Oui';
																		} else {
																			echo '<font color="#FF0000">Non</font>';
																		}
																		?>
																	</td>
																</tr>
																<tr>
																	<td align="right" nowrap="nowrap">Fichier des données à utiliser :&nbsp;</td>
																	<td align="left" nowrap="nowrap"><?php echo $tab_fichiers[$i]['fichier_donnees']; ?></td>
																</tr>
																<tr>
																	<td align="right" nowrap="nowrap">Fichier des données à utiliser existe ? :&nbsp;</td>
																	<td align="left" nowrap="nowrap">
																		<?php
																		if($tab_fichiers[$i]['fichier_donnees_existe']) {
																			echo 'Oui';
																		} else {
																			echo '<font color="#FF0000">Non</font>';
																		}
																		?>
																	</td>
																</tr>
																<tr>
																	<td align="right" nowrap="nowrap">Insérer les données après la ligne n° :&nbsp;</td>
																	<td align="left" nowrap="nowrap">
																		<?php
																		if($tab_fichiers[$i]['texte_a_rechercher_existe']) {
																			echo $tab_fichiers[$i]['ligne_inserer_apres'];
																		} else {
																			echo '<font color="#FF0000">' . $tab_fichiers[$i]['ligne_inserer_apres'] . '</font>';
																		}
																		?>
																	</td>
																</tr>
																
																
																
															</table>
														</td>
													</tr>
													<tr>
														<td align="left" nowrap="nowrap" colspan="2">&nbsp;</td>
													</tr>
										<?php
											}
										?>
									</table>
								</td>
							</tr>
		
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
											<td align="center">
												<script language="javascript">buttonMagic3("Enregistrer les modifications","onclick_enregistrer()");</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
								
								
						</table>
						
					</form>
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						function onclick_enregistrer() {
							if(<?php echo $total_non_traites; ?> > 0) {
								if(<?php echo $total_erreurs; ?> == 0) {
									if(confirm("Si vous continuez, le(s) <?php echo $total_non_traites; ?> fichier(s) pas encore traité(s) seront modifié(s).\nVoulez-vous continuer ?")) {
										msg_util_attente_montrer(true);
										document.getElementById('formulaire').operation.value = 'traiter_les_fichiers';
										document.getElementById('formulaire').submit();
									}
								} else {
									alert("Il y a <?php echo $total_erreurs; ?> erreurs.\nVeuillez corriger ces erreurs avant de pouvoir continuer.");
								}
							} else {
									alert("Les <?php echo $total_deja_traites; ?> fichiers on déjà été traités.");
							}
						}
					</script>
				
					
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