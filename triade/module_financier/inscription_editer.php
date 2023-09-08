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

$afficher_tableaux = true;

// Verification autorisations acces au module
if(autorisation_module()) {


	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation_rech = lire_parametre('operation_rech', '', 'POST');
	$code_class_rech = lire_parametre('code_class_rech', '', 'POST');
	$nom_eleve_rech = lire_parametre('nom_eleve_rech', '', 'POST');
	$annee_scolaire_rech = lire_parametre('annee_scolaire_rech', '', 'POST');

	$operation = lire_parametre('operation', '', 'POST');
	$id_operation = lire_parametre('id_operation', 0, 'POST');
	$inscription_id = lire_parametre('inscription_id', 0, 'POST');
	$echeances_total = lire_parametre('echeances_total', 0, 'POST');

	$nouveau_frais = lire_parametre('nouveau_frais', 0, 'POST');
	$nouveau_frais_type_frais_id = lire_parametre('nouveau_frais_type_frais_id', 0, 'POST');
	$nouveau_frais_montant = lire_parametre('nouveau_frais_montant', 0, 'POST');
	$nouveau_frais_optionnel = lire_parametre('nouveau_frais_optionnel', 0, 'POST');
	$nouveau_frais_selectionne = lire_parametre('nouveau_frais_selectionne', 0, 'POST');
	$nouveau_frais_lisse = lire_parametre('nouveau_frais_lisse', 0, 'POST');	
	
	$nouvelle_echeance = lire_parametre('nouvelle_echeance', 0, 'POST');
	$nouvelle_echeance_date_echeance = lire_parametre('nouvelle_echeance_date_echeance', '', 'POST');
	$nouvelle_echeance_libelle = lire_parametre('nouvelle_echeance_libelle', '', 'POST');
	$nouvelle_echeance_montant = lire_parametre('nouvelle_echeance_montant', 0, 'POST');
	$nouvelle_echeance_type_reglement_id = lire_parametre('nouvelle_echeance_type_reglement_id', 0, 'POST');
	$nouvelle_echeance_type_echeance = lire_parametre('nouvelle_echeance_type_echeance', 0, 'POST');

	$appelant = lire_parametre('appelant', '', 'POST');
	$date_depart = trim(lire_parametre('date_depart', '', 'POST'));
	$commentaire = trim(lire_parametre('commentaire', '', 'POST'));
	$elev_id = trim(lire_parametre('elev_id', 0, 'POST'));
	
	$montrer_frais = trim(lire_parametre('montrer_frais', 0, 'POST'));

	
	//***************************************************************************

	// Rechercher la liste des reglements pour l'inscription courante
	$sql  = "SELECT r.reglement_id ";
	$sql .= "FROM ".FIN_TAB_REGLEMENT." r ";
	$sql .= "INNER JOIN ".FIN_TAB_ECHEANCIER." e ON r.echeancier_id = e.echeancier_id ";
	$sql .= "WHERE e.inscription_id = $inscription_id ";
	$sql .= "GROUP BY r.reglement_id";
	//echo $sql;
	$liste_reglements=execSql($sql);


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Rechercher les groupes de frais
	$sql ="SELECT groupe_id, libelle ";
	$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
	$res1_nb = execSql($sql);
	
	if($operation == 'frais_supprimer') {
		$sql  = "DELETE FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
		$sql .= "WHERE frais_inscription_id  = $id_operation ";
		//echo $sql;
		$res=execSql($sql);
		$montrer_frais = 1;
	}
	

	if($operation == 'echeance_supprimer') {
		// 20010709 - AP : On autorise les suppressions multiples
		if(trim($id_operation) != '' && trim($id_operation) != '0') {			
			$sql  = "DELETE FROM ".FIN_TAB_ECHEANCIER." ";
			$sql .= "WHERE echeancier_id IN ($id_operation) ";
			$res=execSql($sql);
			
			$sql = "SELECT * ";
			$sql .= "FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
			$sql .= "WHERE echeancier_id IN ($id_operation) ";
			$nb_groupe=execSql($sql);
			
			for($j=0;$j<$nb_groupe->numRows();$j++)
			{
				$sql3  = "DELETE FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
				$sql3 .= "WHERE echeancier_id IN ($id_operation) ";
				$res3=execSql($sql3);
			}
		}
	}
	
	if($operation == 'echeance_diviser') {
		// 20010709 - AP : On autorise les divisions multiples
		if(trim($id_operation) != '' && trim($id_operation) != '0') {			
			
			// Decouper la liste des echeancier_id
			$tab_echeances_a_diviser = preg_split('/,/', $id_operation);
			
			// Traiter chaque echeancier_id
			for($l=0; $l<count($tab_echeances_a_diviser); $l++) {
			
				$id_operation = $tab_echeances_a_diviser[$l];
			
				// Rechercher l'echeance a diviser
				$sql  = "SELECT echeancier_id, inscription_id, date_echeance, montant, impaye, type_reglement_id, libelle, type, numero_rib, lisse ";
				$sql .= "FROM ".FIN_TAB_ECHEANCIER." e ";
				$sql .= "WHERE echeancier_id = $id_operation ";
				//echo $sql;
				$echeance_a_diviser=execSql($sql);
				
				
				$sql="SELECT echeancier_id, montant, groupe_id ";
				$sql.= "FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
				$sql.= "WHERE echeancier_id = $id_operation ";
				$groupe_a_diviser = execSql($sql);
				
				if($echeance_a_diviser->numRows() > 0) {
					// Recuperer les donnees de l'echeance
					$ligne_echeance_a_diviser = $echeance_a_diviser->fetchRow();
					
					// Diviser le montant en deux parties
					// $montant2 = number_format($ligne_echeance_a_diviser[3] / 2, 2, '.', '');
					// Permet d'ajuster le montant 1 pour que le total corresponde au montant de l'echeance initiale
					// (a cause des arrondis)
					// $montant1 = $ligne_echeance_a_diviser[3] - $montant2;
					// Guarder le plus grand montant pour l'echeance existante
					// if($montant2 > $montant1) {
						// $montant_tmp = $montant1;
						// $montant1 = $montant2;
						// $montant2 = $montant_tmp;
					// }
					
					$montant_total1=0.0;
					$montant_total2=0.0;
					
					for($i=0;$i<$groupe_a_diviser->numRows();$i++)
					{
						$res1 = $groupe_a_diviser->fetchInto($ligne_groupe_a_diviser, DB_FETCHMODE_DEFAULT, $i);
						
						$groupe_tmp = $ligne_groupe_a_diviser[2];
						
						$montant_g2 = number_format($ligne_groupe_a_diviser[1] / 2, 2, '.', '');
						
						$montant_g1 = $ligne_groupe_a_diviser[1] - $montant_g2;
						
						$montant_total1 += $montant_g1;
						$montant_total2 += $montant_g2;
					}
					
					// Mettre a jour le montant de l'echeance existante
					$sql  = "UPDATE ".FIN_TAB_ECHEANCIER." ";
					$sql .= "SET montant = " . $montant_total1 . " ";
					$sql .= "WHERE echeancier_id = $id_operation ";
					//echo $sql;
					$res=execSql($sql);
					
					// Ajouter la nouvelle echeance
					$sql  = "INSERT INTO ".FIN_TAB_ECHEANCIER." (inscription_id, date_echeance, montant, impaye, type_reglement_id,  libelle, type, numero_rib, lisse) ";
					$sql .= "VALUES(";
					$sql .= "".$ligne_echeance_a_diviser[1].", ";
					$sql .= "'".$ligne_echeance_a_diviser[2]."', ";
					$sql .= "'".$montant_total2."', ";
					$sql .= "0, ";
					$sql .= "".$ligne_echeance_a_diviser[5].", ";
					$sql .= "'".esc(ucfirst(LANG_FIN_GENE_054) . ' ' . LANG_FIN_ECHE_004 . ' ' . date_depuis_bdd($ligne_echeance_a_diviser[2]))."', ";
					$sql .= "".$ligne_echeance_a_diviser[7].", ";
					$sql .= "".$ligne_echeance_a_diviser[8].", ";
					$sql .= "".$ligne_echeance_a_diviser[9]." ";
					$sql .= "); ";
					$res=execSql($sql);
					$num_echeance = mysqli_insert_id($cnx->connection);
					//echo $sql;
					
					for($i=0;$i<$groupe_a_diviser->numRows();$i++)
					{
						$res1 = $groupe_a_diviser->fetchInto($ligne_groupe_a_diviser, DB_FETCHMODE_DEFAULT, $i);
						
						$groupe_tmp = $ligne_groupe_a_diviser[2];
						
						$montant_g2 = number_format($ligne_groupe_a_diviser[1] / 2, 2, '.', '');
						
						$montant_g1 = $ligne_groupe_a_diviser[1] - $montant_g2;
						
						// if($montant_g2 > $montant_g1) {
							// $montant_tmp = $montant_g1;
							// $montant_g1 = $montant_g2;
							// $montant_g2 = $montant_tmp;
						// }
						
						$sql="UPDATE ".FIN_TAB_ECHEANCIER_GROUPE." ";
						$sql.="SET montant = ". $montant_g1 ." ";
						$sql.= "WHERE echeancier_id = $id_operation AND groupe_id = $groupe_tmp ";
						$res=execSql($sql);
						// echo $sql;
						
						$sql  = "INSERT INTO ".FIN_TAB_ECHEANCIER_GROUPE." (echeancier_id, inscription_id, groupe_id, montant) ";
						$sql .= "VALUES(";
						$sql .= "".$num_echeance.", ";
						$sql .= "".$ligne_echeance_a_diviser[1].", ";
						$sql .= "'".$ligne_groupe_a_diviser[2]."', ";
						$sql .= "'".$montant_g2."'";
						$sql .= "); ";
						$res=execSql($sql);
						 // echo $sql;
					}
				}
			}
		}
	}	
	
	if($operation == 'echeance_fusionner') {
		// Recuperer chaque echeancier_id (separes par une ,)
		$tab_echeancier_id = preg_split('/,/', $id_operation);
		//print_r($tab_echeancier_id);
		
		if(count($tab_echeancier_id) == 2) {
		
			// Rechercher la premiere echeance
			$sql  = "SELECT echeancier_id, inscription_id, date_echeance, montant, impaye, type_reglement_id, libelle, type, numero_rib, lisse ";
			$sql .= "FROM ".FIN_TAB_ECHEANCIER." ";
			$sql .= "WHERE echeancier_id = " . $tab_echeancier_id[0];
			//echo $sql;
			$echeance_1=execSql($sql);
			
			// Recherche les groupes de la premier echeance
			
			$sql = "SELECT echeancier_id, inscription_id, groupe_id, montant ";
			$sql .= "FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
			$sql .= "WHERE echeancier_id = " . $tab_echeancier_id[0]." ";
			$sql .= "ORDER BY groupe_id ";
			$groupe_1=execSql($sql);
			
		
			if($echeance_1->numRows() > 0) {
				// Recuperer les donnees de la premiere echeance
				$ligne_echeance_1 = $echeance_1->fetchRow();

				// Rechercher la deuxieme echeance
				$sql  = "SELECT echeancier_id, inscription_id, date_echeance, montant, impaye, type_reglement_id, libelle, type, numero_rib, lisse ";
				$sql .= "FROM ".FIN_TAB_ECHEANCIER." ";
				$sql .= "WHERE echeancier_id = " . $tab_echeancier_id[1];
				//echo $sql;
				$echeance_2=execSql($sql);
				
				$sql = "SELECT echeancier_id, inscription_id, groupe_id, montant ";
				$sql .= "FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
				$sql .= "WHERE echeancier_id = " . $tab_echeancier_id[1]." ";
				$sql .= "ORDER BY groupe_id ";
				$groupe_2=execSql($sql);
			
			
				if($echeance_2->numRows() > 0) {
				
					// Recuperer les donnees de la deuxieme echeance
					$ligne_echeance_2 = $echeance_2->fetchRow();
					
					// Mettre a jour le montant et le libelle de la premiere echeance
					$sql  = "UPDATE ".FIN_TAB_ECHEANCIER." ";
					$sql .= "SET montant = " . ($ligne_echeance_1[3] + $ligne_echeance_2[3]) . " ";
					$sql .= ", libelle = '" . esc(substr($ligne_echeance_1[6] . ' (' . $ligne_echeance_2[6] . ')', 0, 64)) . "' ";
					$sql .= "WHERE echeancier_id = " . $tab_echeancier_id[0];
					//echo $sql;
					$res=execSql($sql);
					
					for($j=0;$j<$groupe_1->numRows();$j++)
					{
						$res5 = $groupe_1->fetchInto($ligne5, DB_FETCHMODE_DEFAULT, $j);
						$res6 = $groupe_2->fetchInto($ligne6, DB_FETCHMODE_DEFAULT, $j);
						
						$groupe_tmp = $ligne5[2];
						
						$sql3  = "UPDATE ".FIN_TAB_ECHEANCIER_GROUPE." ";
						$sql3 .= "SET montant = " . ($ligne5[3] + $ligne6[3]) . " ";
						$sql3 .= "WHERE echeancier_id = " . $tab_echeancier_id[0]. " AND groupe_id = $groupe_tmp ";
						$res3=execSql($sql3);
					}
					
					// Supprimer la deuxieme echeance
					$sql  = "DELETE FROM ".FIN_TAB_ECHEANCIER." ";
					$sql .= "WHERE echeancier_id = " . $tab_echeancier_id[1];
					$res=execSql($sql);

					for($j=0;$j<$groupe_2->numRows();$j++)
						{
							$sql3  = "DELETE FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
							$sql3 .= "WHERE  echeancier_id = " . $tab_echeancier_id[1];
							$res3=execSql($sql3);
						}
				}
			
			}
			
		}
	}

	
	if($operation == 'enregistrer' || $operation == 'recalculer_echeances') {
	
		// Recuperer le nombre de frais presents dans le formulaire
		$type_frais_total = lire_parametre('type_frais_total', 0, 'POST');
		
		// Parcourir la liste des frais
		for($i=0; $i<$type_frais_total; $i++) {
			// Recuperer les valeurs des champs du frais
			$frais_inscription_id = lire_parametre('type_frais_' . ($i + 1) . '_frais_inscription_id', 0, 'POST');
			$type_frais_id = lire_parametre('type_frais_' . ($i + 1) . '_type_frais_id', 0, 'POST');
			$montant = lire_parametre('type_frais_' . ($i + 1) . '_montant', 0, 'POST');
			$optionnel = lire_parametre('type_frais_' . ($i + 1) . '_optionnel', 0, 'POST');
			$selectionne = lire_parametre('type_frais_' . ($i + 1) . '_selectionne', 0, 'POST');
			$lisse = lire_parametre('type_frais_' . ($i + 1) . '_lisse', 0, 'POST');
			$caution_remboursee = lire_parametre('type_frais_' . ($i + 1) . '_caution_remboursee', 0, 'POST');

			$sql  = "UPDATE ".FIN_TAB_FRAIS_INSCRIPTION." ";
			$sql .= "SET  ";
			$sql .= " type_frais_id = " . $type_frais_id . ", ";
			$sql .= " montant = '" . str_replace(' ', '', montant_vers_bdd($montant)) . "', ";
			$sql .= " optionnel = " . $optionnel . ", ";
			$sql .= " selectionne = " . $selectionne . ", ";
			$sql .= " lisse = " . $lisse . ", ";
			$sql .= " caution_remboursee = " . $caution_remboursee . " ";
			$sql .= "WHERE frais_inscription_id  = $frais_inscription_id ";
			//echo $sql . '<br>';
			$res=execSql($sql);
		}
		
		$montrer_frais = true;
		
		/*
	
		// Enregistrer la valeur des options des frais
		$sql  = "SELECT fi.type_frais_id, fi.optionnel, tf.caution ";
		$sql .= "FROM ".FIN_TAB_FRAIS_INSCRIPTION." fi ";
		$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fi.type_frais_id = tf.type_frais_id ";
		$sql .= "WHERE fi.inscription_id = $inscription_id ";
		//$sql .= "AND optionnel = 1 ";
		//echo $sql; frais_inscription_id_
		$frais_inscription=execSql($sql);
		for($i=0; $i<$frais_inscription->numRows(); $i++) {
			// Recuperer la ligne du resultat
			$ligne = $frais_inscription->fetchRow();
			
			if($ligne[1] == 1) {
				// Recuperer la valeur de l'option 'inclus'
				$selectionne = lire_parametre('type_frais_id_'.$ligne[0], 0, 'POST');
			} else {
				$selectionne = 0;
			}

			// Recuperer la valeur de l'option 'lisse'
			$lisse = lire_parametre('type_frais_id_'.$ligne[0] . '_lisse', 0, 'POST');

			$sql  = "UPDATE ".FIN_TAB_FRAIS_INSCRIPTION." ";
			$sql .= "SET selectionne = " . $selectionne . " ";
			$sql .= ", lisse = " . $lisse . " ";
			// Verifier si on doit aussi enregistrer la valeur du champ 'caution_remboursee'
			if($ligne[2] == 1) {
				// Recuperer la valeur de l'option 'caution_remboursee'
				$caution_remboursee = lire_parametre('type_frais_id_'.$ligne[0] . '_caution_remboursee', 0, 'POST');
				$sql .= ", caution_remboursee = " . $caution_remboursee . " ";
			}
			$sql .= "WHERE inscription_id = $inscription_id ";
			$sql .= "AND type_frais_id = " . $ligne[0];
			//echo $sql . '<br>';
			$res=execSql($sql);
		}
		
		*/
	
	}
	
	if($operation == 'enregistrer') {
	
		// Enregistrer les infos de l'inscription
		$sql  = "UPDATE ".FIN_TAB_INSCRIPTIONS." ";
		if(trim($date_depart) != '') {
			$sql .= "SET date_depart = '" . date_vers_bdd($date_depart) . "', ";
		} else {
			$sql .= "SET date_depart = NULL, ";
		}
		$sql .= "commentaire = '" . esc($commentaire) . "' ";
		$sql .= "WHERE inscription_id = $inscription_id ";
		//echo $sql;
		$res=execSql($sql);
	
		
		// Enregistrer les donnees des echeances
		for($i=1;$i<=$echeances_total;$i++) {
			// Recuperer les infos de l'echeance
			$echeance_id = lire_parametre('echeance_'.$i.'_id', 0, 'POST');
			$echeance_disabled = lire_parametre('echeance_'.$i.'_disabled', '', 'POST');
			$echeance_date = lire_parametre('echeance_'.$i.'_date', '', 'POST');
			$echeance_montant = lire_parametre('echeance_'.$i.'_montant', 0, 'POST');
			$echeance_type_reglement_id = lire_parametre('echeance_'.$i.'_type_reglement_id', 0, 'POST');
			$echeance_libelle = lire_parametre('echeance_'.$i.'_libelle', '', 'POST');
			$echeance_numero_rib = lire_parametre('echeance_'.$i.'_numero_rib', '', 'POST');
			//echo ' [' . $echeance_id . '] - [' .$echeance_disabled . ']<br>';

			// Mettre a jour l'echeance
			if($echeance_id != '0' && $echeance_disabled == '') {
				$sql  = "UPDATE ".FIN_TAB_ECHEANCIER." ";
				$sql .= "SET date_echeance = '" . date_vers_bdd(esc($echeance_date)) . "' ";
				$sql .= ", montant = " . str_replace(' ', '', montant_vers_bdd($echeance_montant, 2)) . " ";
				$sql .= ", type_reglement_id = " . $echeance_type_reglement_id . " ";
				$sql .= ", libelle = '" . esc($echeance_libelle) . "' ";
				$sql .= ", numero_rib = " . $echeance_numero_rib . " ";
				$sql .= "WHERE echeancier_id = $echeance_id ";
				$res=execSql($sql);
				//echo $sql . '<br>';
				///////////////////////////////////////////////////////////////////////////////////////////
				for($grp=1; $grp <= $res1_nb->numRows(); $grp++)
				{
					$id_groupe =lire_parametre('groupe_'.$i.'_'.$grp.'_id', 0, 'POST');
					$montant_groupe = lire_parametre('montant_groupe_echeancier_'.$i.'_'.$grp, 0, 'POST');

					$sql9  = "UPDATE ".FIN_TAB_ECHEANCIER_GROUPE." ";
					$sql9 .= "SET montant = " . str_replace(' ', '', montant_vers_bdd($montant_groupe, 2)) . " ";
					$sql9 .= "WHERE echeancier_id = $echeance_id AND groupe_id = $id_groupe ";
					
					$nouveau_groupe = execSql($sql9);
				}
				
			}
		}
		

		// Ajouter d'un frais si necessaire
		if($nouveau_frais == 1) {
				$sql  = "INSERT INTO ".FIN_TAB_FRAIS_INSCRIPTION." (inscription_id, type_frais_id, montant, optionnel, selectionne,  lisse, caution_remboursee) ";
				$sql .= "VALUES(";
				$sql .= "".$inscription_id.", ";
				$sql .= "".$nouveau_frais_type_frais_id.", ";
				$sql .= "'".montant_vers_bdd(esc($nouveau_frais_montant))."', ";
				$sql .= "".$nouveau_frais_optionnel.", ";
				$sql .= "".$nouveau_frais_selectionne.", ";
				$sql .= "".$nouveau_frais_lisse.", ";
				$sql .= "0";
				$sql .= "); ";
				$res=execSql($sql);
				//echo $sql;
				$montrer_frais = true;
		}
		
		// Ajouter d'une echeance si necessaire
		if($nouvelle_echeance == 1) {
			$type = -1;
			$lisse = -1;
			switch($nouvelle_echeance_type_echeance) {
				case 'normale_lissee':
					$type = 0;
					$lisse = 1;
					break;
				case 'normale_non_lissee':
					$type = 0;
					$lisse = 0;
					break;
				case 'additionnelle':
					$type = 1;
					$lisse = 0;
					break;
				case 'remise_exceptionelle':
					$type = 2;
					$lisse = 0;
					break;
			}
			
			if($type >=0 && $lisse >= 0) {
			
			
				// Verifier si l'eleve a au moins un rib pour definir le numero de rib a enregistrer
				$sql  = "SELECT r.rib_id ";
				$sql .= "FROM (".FIN_TAB_INSCRIPTIONS." i ";
				$sql .= "INNER JOIN ".FIN_TAB_ELEVES." e ON i.elev_id = e.elev_id) ";
				$sql .= "INNER JOIN ".FIN_TAB_RIB." r on e.elev_id = r.elev_id ";
				$sql .= "WHERE i.inscription_id = $inscription_id ";
				//echo $sql . "<br>";
				$rib_eleve = execSql($sql);
				if($rib_eleve->numRows() > 0) {
					$numero_rib = 1;
				} else {
					$numero_rib = 0;
				}
			
			
				$sql  = "INSERT INTO ".FIN_TAB_ECHEANCIER." (inscription_id, date_echeance, montant, impaye, type_reglement_id,  libelle, type, numero_rib, lisse) ";
				$sql .= "VALUES(";
				$sql .= "".$inscription_id.", ";
				$sql .= "'".date_vers_bdd($nouvelle_echeance_date_echeance)."', ";
				$sql .= "'".montant_vers_bdd(esc($nouvelle_echeance_montant))."', ";
				$sql .= "0, ";
				$sql .= "".$nouvelle_echeance_type_reglement_id.", ";
				$sql .= "'".esc($nouvelle_echeance_libelle)."', ";
				$sql .= "".$type.", ";
				$sql .= "".$numero_rib.", ";
				$sql .= "".$lisse." ";
				$sql .= "); ";
				$res=execSql($sql);
				//echo $sql;
				
				$num_echeance = mysqli_insert_id($cnx->connection);
				
				for($grp=0; $grp <$res1_nb->numRows(); $grp++)
				{
					$id_groupe =lire_parametre('groupe_'.$grp.'_id', 0, 'POST');
					$montant_groupe = lire_parametre('montant_groupe_echeancier_'.$grp, 0, 'POST');

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
		}
		
		msg_util_ajout(LANG_FIN_GENE_001);			
	}
	
	// Recalculer le montant des echeance (normales) qui n'ont pas encore au moins un reglement (realise)
	if($operation == 'recalculer_echeances') {
	
		// Calculer le montant des frais
		$montant_frais = 0.0;
		$sql  = "SELECT type_frais_id, montant, optionnel, selectionne, lisse ";
		$sql .= "FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
		$sql .= "WHERE inscription_id = $inscription_id ";
		//echo $sql;
		$frais_inscription=execSql($sql);
		for($i=0; $i<$frais_inscription->numRows(); $i++) {
			// Recuperer la ligne du resultat
			$ligne = $frais_inscription->fetchRow();
			// Ne prendre en compte que les frais lisses
			if($ligne[4] == 1) {
				// Ne prendre en compte que les frais non-optionnels et les optionnels selectionnes
				if($ligne[2] == 0 || ($ligne[2] == 1 && $ligne[3] == 1)) {
					$montant_frais += $ligne[1];
				}
			}
		}	
		
		// Calculer les montants des echeances bloquees (on ne changera pas leur montant)
		$montant_bloque = 0.0;
		$nombre_echeances_restantes = 0;
		$liste_echeances_a_modifier = array();
		$sql  = "SELECT echeancier_id, montant ";
		$sql .= "FROM ".FIN_TAB_ECHEANCIER." e ";
		$sql .= "WHERE inscription_id = $inscription_id ";
		$sql .= "AND impaye = 0 ";
		$sql .= "AND type = 0 ";
		$sql .= "AND lisse = 1 ";
		$sql .= "ORDER BY e.date_echeance ASC";
		//echo $sql;
		$echeances_bloquees=execSql($sql);
		for($i=0; $i<$echeances_bloquees->numRows(); $i++) {
			// Recuperer la ligne du resultat
			$res = $echeances_bloquees->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
			// Calculer le montant qui reste a payer pour cette echeance
			$reste_a_payer = reglement_reste_a_payer("echeance", $ligne[0]);
			// Verifier si quelque chose a ete regle => montant bloque
			if($reste_a_payer != $ligne[1]) {
				$montant_bloque += $ligne[1];
			} else {
				// Rien n'a ete paye => le montant sera modifie
				$nombre_echeances_restantes++;
				$liste_echeances_a_modifier[count($liste_echeances_a_modifier)] = $ligne[0];
			}
		}
		
		$montant_total = $montant_frais - $montant_bloque;
		// Calcul d'une echeance (arrondi a 2 chiffres apres la virgule)
		$montant_echeance = number_format($montant_total / $nombre_echeances_restantes, 2, '.', '');

		// Calcul de la derniere echeance (peut etre inferieure aux autres echeances a cause des arrondis)
		if(($montant_echeance * $nombre_echeances_restantes) == $montant_total || $nombre_echeances_restantes <= 1) {
			// => toutes les echeances sont egales
			$montant_derniere_echeance = $montant_echeance;
		} else {
			// => la derniere echeance est differente
			// Ajuster l'echeance pour que la dernier echeance soit inferieure
			if((($nombre_echeances_restantes - 1) * $montant_echeance) > $montant_echeance) {
				$montant_echeance += 0.01;
			}
			// Calcul de la derniere echeance
			$montant_derniere_echeance = $montant_total - (($nombre_echeances_restantes - 1) * $montant_echeance);
		}

		// Mettre a jour les montants des echeances qui sont dans le tableau $liste_echeances_a_modifier
		for($i=0; $i<count($liste_echeances_a_modifier); $i++) {
			// Verifier si on est sur la derniere echeance ou non
			if($i < (count($liste_echeances_a_modifier) - 1)) {
				$montant = $montant_echeance;
			} else {
				$montant = $montant_derniere_echeance;
			}
			// Mettre a jour le montant
			$sql  = "UPDATE ".FIN_TAB_ECHEANCIER." ";
			$sql .= "SET montant = " . $montant . " ";
			$sql .= "WHERE echeancier_id = " . $liste_echeances_a_modifier[$i];
			$res=execSql($sql);
			
			//echo "modif de :" . $liste_echeances_a_modifier[$i] .'<br>';
		}
		
		msg_util_ajout(LANG_FIN_GENE_001);			
		
		//echo $montant_frais .'<br>';
		//echo $montant_bloque .'<br>';
		//echo $montant_total .'<br>';
		//echo $nombre_echeances_restantes .'<br>';
		//echo $montant_echeance .'<br>';
		//echo $montant_derniere_echeance .'<br>';
		//print_r($liste_echeances_a_modifier);
	}
	
	
	// Supprimer l'inscription qui n'a pas de reglements
	if($operation == 'supprimer_inscription_sans_reglements') {
		
		// Supprimer les frais d'inscription pour cette inscription
		$sql  = "DELETE FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
		$sql .= "WHERE inscription_id = " . $inscription_id;
		$res=execSql($sql); 

		// Supprimer l'echeancier pour cette inscription
		$sql  = "DELETE FROM ".FIN_TAB_ECHEANCIER." ";
		$sql .= "WHERE inscription_id = " . $inscription_id;
		$res=execSql($sql);
		
		// Supprimer les groupes de l'echeancier
		
		$sql = "SELECT * ";
		$sql .= "FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
		$sql .= "WHERE  inscription_id = " . $inscription_id;
		$nb_groupe=execSql($sql);

		for($j=0;$j<$nb_groupe->numRows();$j++)
		{
			$sql3  = "DELETE FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
			$sql3 .= "WHERE  inscription_id = " . $inscription_id;
			$res3=execSql($sql3);
		}

		// Supprimer l'inscription
		$sql  = "DELETE FROM ".FIN_TAB_INSCRIPTIONS." ";
		$sql .= "WHERE inscription_id = " . $inscription_id;
		$res=execSql($sql);
		
		$afficher_tableaux = false;
		
		msg_util_ajout(LANG_FIN_INSC_032);			


	}
	//***************************************************************************
		
	// Rechercher les infos de l'eleve
	$sql  = "SELECT nom, prenom ";
	$sql .= "FROM ".FIN_TAB_ELEVES." e ";
	$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON e.elev_id = i.elev_id ";
	$sql .= "WHERE i.inscription_id = $inscription_id ";
	$eleve = execSql($sql);

	// Rechercher les infos de l'inscription
	$sql  = "SELECT i.inscription_id, i.elev_id, i.code_class, i.annee_scolaire, i.date_inscription, i.type_echeancier_id, c.libelle, te.libelle, i.date_depart, i.commentaire, i.id_bareme_initial ";
	$sql .= "FROM (".FIN_TAB_INSCRIPTIONS." i ";
	$sql .= "INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class) ";
	$sql .= "INNER JOIN ".FIN_TAB_TYPE_ECHEANCIER." te ON i.type_echeancier_id = te.type_echeancier_id ";
	$sql .= "WHERE i.inscription_id = $inscription_id ";
	//echo $sql;
	$inscription = execSql($sql);

	// Rechercher les frais pour l'inscription selectionne
	$sql  = "SELECT fi.frais_inscription_id, fi.inscription_id, fi.type_frais_id, fi.montant, fi.optionnel, fi.selectionne, tf.libelle, fi.lisse, tf.caution, fi.caution_remboursee ";
	$sql .= "FROM ".FIN_TAB_FRAIS_INSCRIPTION." fi ";
	$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fi.type_frais_id = tf.type_frais_id ";
	$sql .= "WHERE fi.inscription_id = $inscription_id ";
	$sql .= "ORDER BY tf.libelle ASC";
	//echo $sql;
	$frais_inscription=execSql($sql);

	// Rechercher les echeances pour l'inscription selectionne
	$sql  = "SELECT e.echeancier_id, e.inscription_id, e.date_echeance, e.montant, e.impaye, tr.libelle, e.type_reglement_id, e.libelle, e.type, i.elev_id, e.numero_rib, e.lisse ";
	$sql .= "FROM ".FIN_TAB_ECHEANCIER." e ";
	$sql .= "INNER JOIN ".FIN_TAB_TYPE_REGLEMENT." tr ON e.type_reglement_id = tr.type_reglement_id ";
	$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON e.inscription_id = i.inscription_id ";
	$sql .= "WHERE e.inscription_id = $inscription_id ";
	$sql .= "ORDER BY e.date_echeance ASC, e.echeancier_id ASC";
	//echo $sql;
	$echeances=execSql($sql);
		
	//Rechercher les groupes de frais pour l'inscription sectionne
	
	$sqlgroupe ="SELECT eg.inscription_id, eg.echeancier_id, eg.groupe_id, eg.montant, gf.libelle ";
	$sqlgroupe.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." eg ";
	$sqlgroupe.="INNER JOIN ".FIN_TAB_GROUPE_FRAIS." gf ON eg.groupe_id = gf.groupe_id ";
	$sqlgroupe.="WHERE eg.inscription_id =  $inscription_id ";
	$sqlgroupe.="ORDER BY eg.echeancier_id ASC, eg.groupe_id ";
	$resgroupe = execSql($sqlgroupe);

	// Rechercher combien d'echeances (normales er lissees) n'ont pas encore au moins un reglement (realise)
	$echeances_sans_reglement = 0;
	
	for($i=0; $i<$echeances->numRows(); $i++) {
		$res = $echeances->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
		// Seulement les echeances normales et lissees
		if($ligne[8] == 0 && $ligne[11] == 1) {
			// Rechercher les reglements
			$sql  = "SELECT reglement_id, realise ";
			$sql .= "FROM ".FIN_TAB_REGLEMENT." ";
			$sql .= "WHERE echeancier_id = " . $ligne[0];
			//echo $sql;
			$res_reglements=execSql($sql);
			$un_reglement_realise = false;
			for($j=0; $j<$res_reglements->numRows(); $j++) {
				$res = $res_reglements->fetchInto($ligne_reglements, DB_FETCHMODE_DEFAULT, $j);
				if($ligne_reglements[1] == 1) {
					$un_reglement_realise = true;
				}
			}
			if(!$un_reglement_realise) {
				$echeances_sans_reglement++;
			}
		}
	}

	// Rechercher infos supplementaire sur les echeances
	$tab_infos_echeances = array();
	for($i=0; $i<$echeances->numRows(); $i++) {
		$res = $echeances->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
		
		$reglements = 0;
		$reglements_realises = 0;
		// Rechercher les reglements
		$sql  = "SELECT reglement_id, realise ";
		$sql .= "FROM ".FIN_TAB_REGLEMENT." ";
		$sql .= "WHERE echeancier_id = " . $ligne[0];
		//echo $sql;
		$res_reglements=execSql($sql);
		$un_reglement_realise = false;
		for($j=0; $j<$res_reglements->numRows(); $j++) {
			$res = $res_reglements->fetchInto($ligne_reglements, DB_FETCHMODE_DEFAULT, $j);
			$reglements++;
			if($ligne_reglements[1] == 1) {
				$reglements_realises++;
			}
		}
		$tab_infos_echeances[$ligne[0]] = array(
						'reglements' => $reglements,
						'reglements_realises' => $reglements_realises
						);

	}
	//print_r($tab_infos_echeances);
	
	// Rechercher les type de reglements
	$sql  = "SELECT type_reglement_id, libelle ";
	$sql .= "FROM ".FIN_TAB_TYPE_REGLEMENT." e ";
	$sql .= "ORDER BY libelle ASC";
	//echo $sql;
	$types_reglement=execSql($sql);
	
	
	// Rechercher les type de frais utilises
	$liste_type_frais_id_utilises = '';
	$separateur = '';
	$sql  = "SELECT type_frais_id ";
	$sql .= "FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
	$sql .= "WHERE inscription_id = $inscription_id ";
	//echo $sql;
	$types_frais_utilises=execSql($sql);
	for($j=0; $j<$types_frais_utilises->numRows(); $j++) {
		$ligne_type_frais_utilise = $types_frais_utilises->fetchRow();
		$liste_type_frais_id_utilises .= $separateur . $ligne_type_frais_utilise[0];
		$separateur = ',';
	}

	// Rechercher les type de frais disponibles
	$sql  = "SELECT type_frais_id, libelle, lisse, caution ";
	$sql .= "FROM ".FIN_TAB_TYPE_FRAIS." ";
	if($liste_type_frais_id_utilises != '') {
		$sql .= "WHERE type_frais_id NOT IN ($liste_type_frais_id_utilises) ";
	}
	$sql .= "ORDER BY libelle ASC";
	//echo $sql;
	$types_frais_disponibles=execSql($sql);
	
	// Rechercher les type de frais (tous)
	$sql  = "SELECT type_frais_id, libelle, lisse, caution ";
	$sql .= "FROM ".FIN_TAB_TYPE_FRAIS." ";
	$sql .= "ORDER BY libelle ASC";
	//echo $sql;
	$types_frais=execSql($sql);
	
	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_FIN_INSC_018; ?>
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
					if($afficher_tableaux) {
					?>
                
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<input type="hidden" name="operation" id="operation" value="">
						<input type="hidden" name="id_operation" id="id_operation" value="0">
						<input type="hidden" name="appelant" id="appelant" value="<?php echo $appelant; ?>">
						
						<input type="hidden" name="inscription_id" id="inscription_id" value="<?php echo $inscription_id; ?>">
						<input type="hidden" name="operation_rech" id="operation_rech" value="<?php echo $operation_rech; ?>">
						<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class_rech; ?>">
						<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve_rech; ?>">
						<input type="hidden" name="annee_scolaire_rech" id="annee_scolaire_rech" value="<?php echo $annee_scolaire_rech; ?>">
						<input type="hidden" name="nouveau_frais" id="nouveau_frais" value="0">
						<input type="hidden" name="nouvelle_echeance" id="nouvelle_echeance" value="0">


						<script language="javascript">
							var tab_type_frais = new Array();
							var tab_types_frais_disponibles = new Array();
						</script>
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>

							<tr>
								<td valign=top align="center">
									<?php
									// Pour la gestion des calendriers
									include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");

									//*******************  INFORMATIONS DE L'INSCRIPTION *********************
									
									?>
									<fieldset id="fieldset_inscription" style="z-index:5;margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_FIN_INSC_020; ?></legend>
										<?php
										if($inscription->numRows() > 0) {
											$ligne = $inscription->fetchRow();
											$elev_id = $ligne[1];
											$date_insctiption_timestamp = mktime(0, 0, 0, substr($ligne[4], 5, 2), substr($ligne[4], 8, 2), substr($ligne[4], 0, 4));
										?>
										<table cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td align="right" nowrap><b><?php echo LANG_FIN_GENE_030; ?>&nbsp;:&nbsp;</b></td>
												<td align="left"><?php echo date_depuis_bdd($ligne[4]); ?></td>
											</tr>
											<tr>
												<td align="right" nowrap><b><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;</b></td>
												<td align="left"><?php echo ucfirst($ligne[6]); ?></td>
											</tr>
											<tr>
												<td align="right" nowrap><b><?php echo LANG_FIN_GENE_011; ?>&nbsp;:&nbsp;</b></td>
												<td align="left"><?php echo $ligne[3]; ?></td>
											</tr>
											<tr>
												<td align="right" nowrap><b><?php echo LANG_FIN_BARE_012; ?>&nbsp;:&nbsp;</b></td>
                                                <?php
												$libelle_bareme_initial = '-';
												if(is_numeric($ligne[10]) && $ligne[10] > 0) {
												
													$sql_bareme  = "SELECT libelle ";
													$sql_bareme .= "FROM ".FIN_TAB_BAREME." ";
													$sql_bareme .= "WHERE bareme_id = " . $ligne[10];
													$bareme = execSql($sql_bareme);
													if($bareme->numRows() > 0) {
														$ligne_bareme = &$bareme->fetchRow();
														$libelle_bareme_initial = $ligne_bareme[0];
													}
													
												}
												?>
												<td align="left"><?php echo $libelle_bareme_initial; ?></td>
											</tr>
											<tr>
												<td align="right" nowrap><b><?php echo LANG_FIN_TECHE_002; ?>&nbsp;:&nbsp;</b></td>
												<td align="left"><?php echo $ligne[7]; ?></td>
											</tr>
											<tr>
												<td align="right" nowrap><b><?php echo LANG_FIN_INSC_024; ?>&nbsp;:&nbsp;</b></td>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td align="left">
																<?php
																$valeur = $ligne[8];
																if(trim($valeur) != '') {
																	$valeur = date_depuis_bdd(trim($valeur));
																}
																if(trim($ligne[8]) != '') {
																	$date_depart = mktime(0, 0, 0, substr($ligne[8], 5, 2), substr($ligne[8], 8, 2), substr($ligne[8], 0, 4));
																} else {
																	$date_depart = '';
																}
																?>
																<input type="text" name="date_depart" id="date_depart" size="10" maxlength="10" value="<?php echo $valeur; ?>">
															</td>
															<td>&nbsp;</td>
															<td align="left">
																<?php
																calendarDim("div_date_depart","document.formulaire.date_depart",$_SESSION["langue"], "0", "0", 'fieldset_inscription', 'null', 'null');	
																?>
															</td>
															<td>&nbsp;</td>
															<td valign="middle">
															<a href="javascript:;"  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg',' <?php echo LANG_FIN_INSC_025; ?>', 'fieldset_inscription');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"></a>

															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="right" valign="top" nowrap><b><?php echo LANG_FIN_GENE_051; ?>&nbsp;:&nbsp;</b></td>
												<?php
												$valeur = $ligne[9];
												?>
												<td align="left">
													<textarea name="commentaire" id="commentaire" cols="100" rows="15"><?php echo $valeur; ?></textarea>
												</td>
											</tr>
										</table>
										<?php
										}
										?>
									</fieldset>
									
									<?php
									//*******************  LISTE DES FRAIS (OPTIONNELS OU NON) ****************
									?>
									
									<br>
									<fieldset style="z-index:4;margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_FIN_FBAR_003; ?></legend>
										
										<table cellspacing="1" cellpadding="0" border="0" width="100%" align="center">
                                            <?php
											$display = 'none';
											$texte = '[+] ' . LANG_FIN_INSC_026;
											if($montrer_frais == 1) {
												$display = '';
												$texte = '[-] ' . LANG_FIN_INSC_027;
											}
											?>
											<tr>
												<td align="left">
													 <a href="javascript:;" onClick="onclick_voir_cacher_liste_frais();"><span id="voir_cacher_liste_frais_texte"><?php echo $texte;?></span></a>
												</td>
											</tr>
											<tr id="tr_liste_frais" style="display:<?php echo $display; ?>">
												<td align="left" valign="top">
													<br>

													<table cellspacing="1" cellpadding="0" border="0" width="100%">
                                                    	<tr>
                                                        	<td align="left">
                                                                <table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
                                                                    <tr bgcolor="#ffffff">
                                                                        <td align="left" valign="middle"><b>&nbsp;</b></td>
                                                                        <td align="left" valign="middle"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
                                                                        <td align="right" valign="middle"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
                                                                        <td align="center" valign="middle"><b><?php echo LANG_FIN_GENE_012; ?></b></td>
                                                                        <td align="center" valign="middle"><b><?php echo LANG_FIN_GENE_028; ?></b></td>
                                                                        <td align="center" valign="middle"><b><?php echo LANG_FIN_TFRA_014; ?></b></td>
                                                                        <td align="center" valign="middle" width="5%"><b><?php echo LANG_FIN_TFRA_019; ?></b></td>
                                                                    </tr>
                                                                                                                                            																<input type="hidden" name="type_frais_total" id="type_frais_total" value="<?php echo $frais_inscription->numRows(); ?>">

                                                                    <?php
                                                                    $montant_frais_total = 0.0;
                                                                    $montant_frais_sans_optionnel = 0.0;
                                                                    $montant_lisses = 0.0;
                                                                    $montant_non_lisses = 0.0;
                                                                    if($frais_inscription->numRows() > 0) {
                                                                        $montant_frais_total = 0;
																		
                                                                        for($i=0; $i<$frais_inscription->numRows(); $i++) {
                                                                            $ligne = $frais_inscription->fetchRow();
                                                                    ?>
                                                                    <script language="javascript">
                                                                        tab_type_frais[tab_type_frais.length] = {
                                                                                                    "type_frais_id" : "<?php echo $ligne[2]; ?>",
                                                                                                    "montant" : "<?php echo str_replace(' ', '', $ligne[3]); ?>",
                                                                                                    "optionnel" : "<?php echo $ligne[4]; ?>",
                                                                                                    "selectionne" : "<?php echo $ligne[5]; ?>",
                                                                                                    "lisse" : "<?php echo $ligne[7]; ?>"
                                                                                                                                    };
                                                                    </script>
                                                                    
                                                                    <tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
																		<?php
																		// Stockage du id du type de frais utilise
																		
                                                                        $nom_champ = 'frais_inscription_id';
                                                                        $valeur = $ligne[0];
                                                                        ?>
                                                                        <input type="hidden" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" value="<?php echo $valeur; ?>">
                                                                        
                                                                        <td>
                                                                        <?php
                                                                        $checked = '';
                                                                        if($i == 0) {
                                                                            $checked = 'checked';
                                                                        }
                                                                        ?>
                                                                        <input type="radio" name="frais_inscription_id" id="frais_inscription_id" value="<?php echo $ligne[0]; ?>" <?php echo $checked; ?>></td>
                                                                        
                                                                        
                                                                        <td>
																			<?php
                                                                            // libelle du type de frais utilise
                                                                            
                                                                            $nom_champ = 'type_frais_id';
                                                                            $valeur = $ligne[2];
                                                                            ?>
                                                                           <select name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" >
                                                                            <?php
                                                                            for($l=0; $l<$types_frais->numRows(); $l++) {
                                                                                $res = $types_frais->fetchInto($ligne_type_frais, DB_FETCHMODE_DEFAULT, $l);
                                                                                $selected = '';
                                                                                if($ligne_type_frais[0] == $valeur) {
                                                                                    $selected = 'selected';
                                                                                }
                                                                            ?>
                                                                                <option value="<?php echo $ligne_type_frais[0]; ?>" <?php echo $selected; ?> ><?php echo $ligne_type_frais[1]; ?></option>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                            </select>
                                                                            
																			<?php //echo $ligne[6]; ?>
                                                                        
                                                                        </td>
                                                                        
                                                                        
                                                                        
                                                                        <td nowrap="nowrap" align="right">
																			<?php
                                                                            // montant du type de frais utilise
                                                                            
                                                                            $nom_champ = 'montant';
                                                                            // Remplacer le separateur de decimal bdd, par le francais
                                                                            $valeur =  str_replace(' ', '', montant_depuis_bdd($ligne[3]));
                                                                            ?>
                                                                                                                                            																		<input type="text" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" size="8" maxlength="12" value="<?php echo $valeur; ?>" style="text-align:right;" onBlur="onclick_frais_option(<?php echo $i+1; ?>, <?php echo $ligne[0]; ?>, 'montant');" >&nbsp;<?php echo LANG_FIN_GENE_019; ?>
                                                                        </td>
                                                                        
                                                                        <td nowrap="nowrap" align="center">
																			<?php
                                                                            // option 'optionnel' du type de frais utilise
                                                                            
                                                                            $nom_champ = 'optionnel';

																			if($ligne[4] == 1) {
																				$valeur = 'checked'; // Oui
																			} else {
																				$valeur = ''; // Non
																			}
                                                                            
                                                                            ?>
                                                                            <input type="checkbox" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" value="1" <?php echo $valeur; ?> onClick="onclick_frais_option(<?php echo $i+1; ?>, <?php echo $ligne[0]; ?>, 'optionnel');">
                                                                      	</td>
                    
 
                                                                         
                                                                        <td nowrap="nowrap" align="center">
																			<?php
                                                                            // option 'selectionne' du type de frais utilise
                                                                            
                                                                            $nom_champ = 'selectionne';

																			if($ligne[5] == 1) {
																				$valeur = 'checked'; // Oui
																			} else {
																				$valeur = ''; // Non
																			}


                                                                            // Calculs pour les totaux
                                                                            // Si est optionel
                                                                            if($ligne[4] == 1) {
                                                                                // Verifier si on doit selectionner l'option ou non
                                                                                if($ligne[5] == 1) {
                                                                                    $montant_frais_total += $ligne[3];
                                                                                }
                                                                             } else {
                                                                                // Pas optionnel => inclus : Oui
                                                                                $montant_frais_total += $ligne[3];
                                                                                $montant_frais_sans_optionnel += $ligne[3];
                                                                           	}
                                                                            ?>
                                                                            <input type="checkbox" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" value="1" <?php echo $valeur; ?> onClick="onclick_frais_option(<?php echo $i+1; ?>, <?php echo $ligne[0]; ?>, 'selectionne');">
                                                                      	</td>

                                                                         
                                                                        <td nowrap="nowrap" align="center">
																			<?php
                                                                            // option 'lisse' du type de frais utilise
                                                                            
                                                                            $nom_champ = 'lisse';

																			if($ligne[7] == 1) {
																				$valeur = 'checked'; // Oui
																			} else {
																				$valeur = ''; // Non
																			}


                                                                            // Calculs pour les totaux
																			if($ligne[7] == 1) {
																				$montant_lisses += $ligne[3];
																			} else {
																				$montant_non_lisses += $ligne[3];
																			}
                                                                            ?>
                                                                            <input type="checkbox" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" value="1" <?php echo $valeur; ?>  onClick="onclick_frais_option(<?php echo $i+1; ?>, <?php echo $ligne[0]; ?>, 'lisse');">
                                                                      	</td>
                    
                                                                        
                                                                        <?php
                                                                            // option 'lisse' du type de frais utilise
                                                                            
                                                                            $nom_champ = 'caution_remboursee';
																			
																			// Indiquer si c'est un caution ou non
																			// Si le type de frais est une caution, afficher une checkbox
																			if($ligne[8] == 0) {
                                                                        ?>
                                                                        <td nowrap="nowrap" align="center">-</td>
                                                                        <input type="hidden" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" value="0" >
                                                                        <?php
                                                                        	} else {
                                                                           	 	$valeur = '';
                                                                           		if($ligne[9] == 1) {
                                                                                	$valeur = 'checked';
                                                                            	}
                                                                        ?>
                                                                        <td nowrap="nowrap" align="center"><input type="checkbox" name="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="type_frais_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" value="1" <?php echo $valeur; ?> ></td>
                                                                        <?php
                                                                        	}
                                                                        ?>


                    
                    
                    													<!-- ******************************************** -->
                   														<!-- PLUS UTILISE -->
                    													<!--
                                                                        
                                                                        <?php
																		if(false) {
                                                                            // Afficher oui ou une checkbox
                                                                            
                                                                            // Si est optionel
                                                                            if($ligne[4] == 1) {
                                                                                // Verifier si on doit selectionner l'option ou non
                                                                                if($ligne[5] == 1) {
                                                                                    $valeur = 'checked';
                                                                                    $montant_frais_total += $ligne[3];
                                                                                } else {
                                                                                    $valeur = '';
                                                                                }
                                                                        ?>
                                                                        <td align="center" nowrap="nowrap">
                                                                            <input type="checkbox" name="type_frais_id_<?php echo $ligne[2]; ?>" id="type_frais_id_<?php echo $ligne[2]; ?>" value="1" onClick="onclick_frais_option();" <?php echo $valeur; ?> >
                                                                        </td>
                                                                        <?php
                                                                            } else {
                                                                                // Pas optionnel => inclus : Oui
                                                                                $montant_frais_total += $ligne[3];
                                                                                $montant_frais_sans_optionnel += $ligne[3];
                                                                        ?>
                                                                        <td align="center" nowrap="nowrap"><?php echo LANG_FIN_GENE_017; ?></td>
                                                                        <?php
                                                                            }
																		}
                                                                        ?>
            
            
            
                                                                        <?php
																		if(false) {
																		?>
            
                                                                        <?php
                                                                            // Ajouter au total des lisses/non-lisses si :
                                                                            //    - pas optionnel
                                                                            //    - optionnel  et inclus dans le calcul
                                                                            if($ligne[4] == 0 || ($ligne[4] == 1 && $ligne[5] == 1)) {
                                                                                // verifier si le frais est lisse ou non
                                                                                if($ligne[7] == 1) {
                                                                                    $montant_lisses += $ligne[3];
                                                                                } else {
                                                                                    $montant_non_lisses += $ligne[3];
                                                                                }
                                                                            }
                                                                                                                                    
                                                                            // Indiquer si c'est lisse ou non
                                                                            if($ligne[7] == 1) {
                                                                                $valeur = 'checked';
                                                                                $fond_cellule = "";	
                                                                                // Ajouter au total des lisses si pas optionnel ou inclus dans le calcul
                                                                                //if($ligne[4] == 0 || $ligne[5] == 1) {
                                                                                //	$montant_lisses += $ligne[3];
                                                                                //}
                                                                            } else {
                                                                                $valeur = '';
                                                                                $fond_cellule = "./" . $g_chemin_relatif_module . "images/fond_ligne_vert.jpg";	
                                                                                // Ajouter au total des lisses si pas optionnel ou inclus dans le calcul
                                                                                //if($ligne[5] == 1) {
                                                                                //	$montant_non_lisses += $ligne[3];
                                                                                //}
                                                                            }
            
                                                                        ?>
                                                                        <td align="center" nowrap="nowrap" style="background-image:url('<?php echo $fond_cellule; ?>'); background-repeat:repeat;">
                                                                            <input type="checkbox" name="type_frais_id_<?php echo $ligne[2]; ?>_lisse" id="type_frais_id_<?php echo $ligne[2]; ?>_lisse" value="1" <?php echo $valeur; ?>  onClick="onclick_frais_option();">
                                                                        </td>
                                                                        
                                                                        <?php
																		}
																		?>
                                                                        
                                                                        <?php
																		if(false) {
                                                                        // Indiquer si c'est un caution ou non
                                                                        // Si le type de frais est une caution, afficher une checkbox
                                                                        if($ligne[8] == 0) {
                                                                        ?>
                                                                        <td nowrap="nowrap" align="center">-</td>
                                                                        <?php
                                                                        } else {
                                                                            $valeur = '';
                                                                            if($ligne[9] == 1) {
                                                                                $valeur = 'checked';
                                                                            }
                                                                        ?>
                                                                        <td nowrap="nowrap" align="center"><input type="checkbox" name="type_frais_id_<?php echo $ligne[2]; ?>_caution_remboursee" id="type_frais_id_<?php echo $ligne[2]; ?>_caution_remboursee" value="1" <?php echo $valeur; ?> ></td>
                                                                        <?php
                                                                        }
																		}
                                                                        ?>
                                                                        -->
                    													<!-- ******************************************** -->
                                                                    </tr>
                                                                <?php
                                                                    }
                                                                ?>
                                                                    <tr class='tabnormal2'>
                                                                        <td align="left" nowrap="nowrap">&nbsp;</td>
                                                                        <td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_TFRA_014); ?></b></td>
                                                                        <td align="right" nowrap="nowrap"><b><span id="total_lisses"><?php echo montant_depuis_bdd($montant_lisses); ?></span>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
                                                                        <td align="right" nowrap="nowrap" colspan="4">&nbsp;</td>
                                                                    </tr>
                                                                    <tr class='tabnormal2'>
                                                                        <td align="left" nowrap="nowrap">&nbsp;</td>
                                                                        <td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_TFRA_018); ?></b></td>
                                                                        <td align="right" nowrap="nowrap"><b><span id="total_non_lisses"><?php echo montant_depuis_bdd($montant_non_lisses); ?></span>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
                                                                        <td align="right" nowrap="nowrap" colspan="4">&nbsp;</td>
                                                                    </tr>
                                                                    <tr class='tabnormal2'>
                                                                        <td align="left" nowrap="nowrap">&nbsp;</td>
                                                                        <td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
                                                                        <td align="right" nowrap="nowrap"><b><span id="total_final"><?php echo montant_depuis_bdd($montant_frais_total); ?></span>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
                                                                        <td align="right" nowrap="nowrap" colspan="4">&nbsp;</td>
                                                                    </tr>
                                                                    
                                                                    
                                                                    
                                                                    
                                                                    
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
                                                        	</td>
                                                    	</tr>
                                                        <tr>
                                                            <td align="left">
                                                                <table cellspacing="0" cellpadding="0" border="0" align="left">
                                                                    <tr>	
                                                                        <td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="1"></td>
                                                                        <td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/fleche_droite_vers_haut.png"; ?>" border="0"></td>
                                                                        <td valign="middle">&nbsp;</td>
                                                                        <td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="1"></td>
                                                                        <td valign="middle">
                                                                            <select name="actions_frais" id="actions_frais" onChange="onchange_actions_frais()">
                                                                                <option value="rien" selected="selected"><?php echo LANG_FIN_GENE_050; ?></option>
                                                                                <option value="supprimer"><?php echo LANG_FIN_FBAR_011; ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
												
													<?php
													// 20100518 - AP : Pour l'instant le bouton est desactive
													$disabled = 'disabled="disabled"=""';
													/*
													$disabled = '';
													if($echeances_sans_reglement <= 0) {
														$disabled = 'disabled="disabled"=""';
													}
													*/
													?>
													
													<?php
													// 20100526 - AP : on ne donne plus la possibilite de recalculer automatiquement les echeances (ce sera fait manuellement)
													if(false) {
													?>
													<p align="center">
													<input type="button" class="button" value="<?php echo LANG_FIN_INSC_022; ?>" onClick="onclick_recalculer_echeances();" <?php echo $disabled; ?>  >
													</p>
													<?php
													}
													?>
                                                    
                                                    
													<?php
                                                    // ******************* AJOUT D'UN NOUVEAU FRAIS ******************
                                                    ?>
                                                    
                                                    <table cellspacing="1" cellpadding="3" border="0" width="90%" align="center">
                                                        <tr>
                                                            <td align="left">&nbsp;
                                                                
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left">
                                                                 <a href="javascript:;" onClick="onclick_frais_ajout(<?php echo $types_frais_disponibles->numRows(); ?>);">[+] <?php echo LANG_FIN_FBAR_007;?></a>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_ajouter_frais" style="display:none">
                                                            <td align="center">
                                                                <div style="border:#0099CC dashed 1px; padding:5px; margin-top:5px;">
                                                                    <br>
                                                                    <table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">
                                                                        <tr bgcolor="#ffffff">
                                                                            <td align="left" valign="middle"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
                                                                            <td align="right" valign="middle"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
                                                                            <td align="center" valign="middle"><b><?php echo LANG_FIN_GENE_012; ?></b></td>
                                                                            <td align="center" valign="middle"><b><?php echo LANG_FIN_GENE_028; ?></b></td>
                                                                            <td align="center" valign="middle"><b><?php echo LANG_FIN_TFRA_014; ?></b></td>
                                                                        </tr>
                                                                        <?php
																		if($types_frais_disponibles->numRows() > 0) {
																		?>
                                                                        <tr bgcolor="#ffffff">
                                                                            <td align="left" nowrap="nowrap" valign="top">
                                                                                <select name="nouveau_frais_type_frais_id" id="nouveau_frais_type_frais_id" onChange="onchange_nouveau_frais_type_frais_id();">
                                                                                <?php
                                                                                for($l=0; $l<$types_frais_disponibles->numRows(); $l++) {
																					$res = $types_frais_disponibles->fetchInto($ligne_type_frais, DB_FETCHMODE_DEFAULT, $l);
																					$selected = '';
																					if($l == 0) {
																						$selected = 'selected';
																					}
																				?>
                                                                                	<option value="<?php echo $ligne_type_frais[0]; ?>" <?php echo $selected; ?> ><?php echo $ligne_type_frais[1]; ?></option>
                                                                                                                                                       																	 <script language="javascript">
                                                                        				tab_types_frais_disponibles[tab_types_frais_disponibles.length] = {
                                                                                                    "type_frais_id" : "<?php echo $ligne_type_frais[0]; ?>",
                                                                                                    "lisse" : "<?php echo $ligne_type_frais[2]; ?>"
                                                                                                                                    };
                                                                    </script>

                                                                                <?php
																				}
																				?>
                                                                                </select>
                                                                            </td>
                                                                            <?php
                                                                            $valeur = "0,00";
                                                                            ?>
                                                                            <td align="right" nowrap="nowrap" valign="top">
                                                                                <input type="text" name="nouveau_frais_montant" id="nouveau_frais_montant" size="8" maxlength="12" value="<?php echo $valeur; ?>" style="text-align:right;" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019; ?>
                                                                            </td>
                                                                            <td align="center" nowrap="nowrap" valign="top">
                                                                                <input type="checkbox" name="nouveau_frais_optionnel" id="nouveau_frais_optionnel" value="">
                                                                            </td>
                                                                            <td align="center" nowrap="nowrap" valign="top">
                                                                                <input type="checkbox" name="nouveau_frais_selectionne" id="nouveau_frais_selectionne" value="">
                                                                            </td>
                                                                            <td align="center" nowrap="nowrap" valign="top">
                                                                                <input type="checkbox" name="nouveau_frais_lisse" id="nouveau_frais_lisse" value="">
                                                                            </td>
                                                                        </tr>
                                                                        <?php
																		} else {
																		?>
                                                                         <tr bgcolor="#ffffff">
                                                                            <td align="left" nowrap="nowrap" valign="top" colspan="5">
                                                                            	<?php echo LANG_FIN_FBAR_012; ?>
                                                                            </td>
                                                                         </tr>
                                                                        <?php
																		}
																		?>
                                                                    </table>
                                                                    <br>
                                                                    
                                                                    <input type="button" class="button" value="<?php echo LANG_FIN_GENE_040; ?>" onClick="onclick_frais_ajout_annuler();" >
                                                                    <br>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <?php
                                                    // ***********************************************************
                                                    ?>	                                                                   
                                                    
												</td>
											</tr>
										</table>

												
										<input type="hidden" name="montant_frais_total" id="montant_frais_total" value="<?php echo $montant_frais_total; ?>" >
										<input type="hidden" name="montant_frais_sans_optionnel" id="montant_frais_sans_optionnel" value="<?php echo $montant_frais_sans_optionnel; ?>" >
                                        
                                        
                                        
                         

									</fieldset>

									<?php
									//*******************  ECHEANCIER ****************
									?>
									
									<br>
									<fieldset id="fieldset_echeancier" style="z-index:3;margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_FIN_ECHE_002; ?></legend>

										<table cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">
														<tr bgcolor="#ffffff">
															<td align="center" nowrap="nowrap">&nbsp;</td>
															<td align="right" nowrap="nowrap"><b>#</b></td>
															<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_030; ?></b></td>
															<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
															<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_TREG_015; ?></b></td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_034; ?></b></td>
															
															<td align="left" nowrap="nowrap">&nbsp;</td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_GROUPE_016; ?></b></td>
															
														</tr>
														<?php
														$montant_normales_total = 0.0;
														$montant_remises_total = 0.0;
														$montant_normales_et_remises_total = 0.0;
														$montant_additionnelles_total = 0.0;
														$total_a_payer_total = 0.0;
														$reste_a_payer_total = 0.0;
														$solde_previsionnel = 0.0;
														$nombre_de_checkbox = 0;
														//echo '[' . $date_depart . ']';
														
														if($echeances->numRows() > 0) {
			
															// Calculer le timestamp de la date du jour (sans l'heure)
															$date_jour = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
														
															for($i=0; $i<$echeances->numRows(); $i++) {
															
																$disabled = '';
															
																$res = $echeances->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);

																// Calcul du total a payer pour l'echeance courante
																$total_a_payer = reglement_total_a_payer("echeance", $ligne[0]);
																// Date de l'echeance au format timestamp
																$date_echeance = mktime(0, 0, 0, substr($ligne[2], 5, 2), substr($ligne[2], 8, 2), substr($ligne[2], 0, 4));
			
																// Total des echeances (normales ou additionneles ou remise)
																switch($ligne[8]) {
																	case 0:
																		// Normale
																		$montant_normales_total += $ligne[3];
																		// Calcul du total a payer et du solde previsonnel
																		if($date_depart == '' || $date_echeance < $date_depart) {
																			$total_a_payer_total += $ligne[3];
																			// Solde previsonnel => Sommes payées pour les échéances passées + montants des échéances à venir (antérieure a la date de depart)
																			if($date_echeance < $date_jour) {
																				$solde_previsionnel += $total_a_payer - reglement_reste_a_payer("echeance", $ligne[0]);
																			} else {
																				$solde_previsionnel += $total_a_payer;
																			}
																		}
																		break;
																	case 1:
																		// Aditionnelle
																		$montant_additionnelles_total += $ligne[3];
																		// Calcul du total a payer et du solde previsonnel
																		if($date_depart == '' || $date_echeance < $date_depart) {
																			$total_a_payer_total += $ligne[3];
																			// Solde previsonnel => Sommes payées pour les échéances passées + montants des échéances à venir (antérieure a la date de depart)
																			if($date_echeance < $date_jour) {
																				$solde_previsionnel += $total_a_payer - reglement_reste_a_payer("echeance", $ligne[0]);
																			} else {
																				$solde_previsionnel += $total_a_payer;
																			}
																		}
																		break;
																	case 2:
																		// Remise exceptionelle
																		$montant_remises_total += $ligne[3];
																		break;
																}
																//$ligne = $echeances->fetchRow();
																
																// Calcul du reste a payer pour l'echeance courante
																$reste_a_payer = reglement_reste_a_payer("echeance", $ligne[0]);
																// Ajout au total du reste a payer (seulement si anterieure a la date de depart)
																if($date_depart == '' || $date_echeance < $date_depart) {
																	$reste_a_payer_total += $reste_a_payer;
																}
																
																/*
																// Calcul du solde previsonnel
																// Sommes payées pour les échéances passées + montants des échéances à venir
																if($date_echeance < $date_jour) {
																	$solde_previsionnel += $total_a_payer - reglement_reste_a_payer("echeance", $ligne[0]);
																} else {
																	$solde_previsionnel += $total_a_payer;
																}
																*/
																
																// ***************************************
																// Verifier si on doit bloquer ou non les champs de l'echeance
																
																$disabled_checkbox = '';
																$disabled_date = '';
																$disabled_montant = '';
																$disabled_type_reglement = '';
																$disabled_reste_a_payer = '';
																$disabled_reglements = '';
																
																// 20100518 - AP : Bloquer la checkbox, la date et le type de reglement si l'echeance a ete payee et pas de reglements et pas une exceptionnelle
																if($reste_a_payer <= 0 && $tab_infos_echeances[$ligne[0]]['reglements'] == 0 && $ligne[8] != 2) {
																	$disabled_checkbox = 'disabled="disabled"';
																	$disabled_date = 'disabled="disabled"';
																	$disabled_type_reglement = 'disabled="disabled"';
																}
																
																// Bloquer tous les champs si l'echeance est posterieure a la date de depart
																if($date_depart != '' && $date_echeance >= $date_depart) {
																	$disabled_checkbox = 'disabled="disabled"';
																	$disabled_date = 'disabled="disabled"';
																	$disabled_montant = 'disabled="disabled"';
																	$disabled_type_reglement = 'disabled="disabled"';
																	$disabled_reste_a_payer = 'disabled="disabled"';
																	$disabled_reglements = 'disabled="disabled"';
																}
																
																// Bloquer la checkbox et le bouton des reglements si l'echeance est une remise exceptionelle
																//20100518 - AP : Bloquer seulement le bouton des reglements si l'echeance est une remise exceptionelle 
																if($ligne[8] == 2) {
																	//$disabled_checkbox = 'disabled="disabled"';
																	$disabled_reglements = 'disabled="disabled"';
																}
																// ***************************************
																
																
																// Verifier si on doit griser le fond de la ligne ou non
																$classe_ligne = 'tabnormal2';
																//if($date_echeance < $date_jour) {
																//	$classe_ligne = 'tabover';
																//}
																
																// Definir l'image de fond de ligne
																$fond_ligne = "";
																if($ligne[8] == 0) {
																	// => echeance normale
																
																	if($ligne[11] == 1) {
																		// => normale - lissee
																		
																		if($date_echeance >= $date_jour) {
																			// => echeance non passe
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_blanc.jpg";	
																		} else {
																			// => echeance passee
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_gris.jpg";	
																		}
																		
																	} else {
																		// => normale - non-lissee
																		
																		if($date_echeance >= $date_jour) {
																			// => echeance non passe
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_vert.jpg";	
																		} else {
																			// => echeance passee
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_gris_vert.jpg";	
																		}
																	}
																} else {
																
																	if($ligne[8] == 1) {
																		// => echeance additionnelle
																	
																		if($date_echeance >= $date_jour) {
																			// => echeance non passe
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_rose.jpg";	
																		} else {
																			// => echeance passee
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_gris_rose.jpg";	
																		}
																		
																	} else {
																		// => echeance de remise exceptionelle

																		if($date_echeance >= $date_jour) {
																			// => echeance non passe
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_bleu.jpg";	
																		} else {
																			// => echeance passee
																			$fond_ligne = "./" . $g_chemin_relatif_module . "images/fond_ligne_gris_bleu.jpg";	
																		}

																	}
																	
																}
																
																// Recuperer la liste des RIB pour l'eleve courant
																$tab_rib = liste_rib($ligne[9]);
																
																// Definir les dates interdites
																$date_autorisee_jusqua = 'null';
																if($ligne[8] == 0) {
																	// => echeance normale : date superieure au egale a la date d'inscription
																	
																	$date_autorisee_jusqua = date("m/d/Y", $date_insctiption_timestamp);
																}
																
																
														?>
														<input type="hidden" name="echeance_<?php echo $i+1; ?>_id" id="echeance_<?php echo $i+1; ?>_id" value="<?php echo $ligne[0]; ?>">
														<input type="hidden" name="echeance_<?php echo $i+1; ?>_type" id="echeance_<?php echo $i+1; ?>_type" value="<?php echo $ligne[8]; ?>">
														<input type="hidden" name="echeance_<?php echo $i+1; ?>_disabled" id="echeance_<?php echo $i+1; ?>_disabled" value="<?php echo $disabled; ?>">
														<input type="hidden" name="echeance_<?php echo $i+1; ?>_lisse" id="echeance_<?php echo $i+1; ?>_lisse" value="<?php echo $ligne[11]; ?>">
														<input type="hidden" name="echeance_<?php echo $i+1; ?>_deja_paye" id="echeance_<?php echo $i+1; ?>_deja_paye" value="<?php echo $total_a_payer - $reste_a_payer; ?>">
			
														<tr class="<?php echo $classe_ligne; ?>" style="background-image:url('<?php echo $fond_ligne; ?>'); background-repeat:repeat;">
														
															<td align="center">
																<?php
																	if($tab_infos_echeances[$ligne[0]]['reglements'] <= 0) {
																		$nombre_de_checkbox++;
																?>
																	<input type="checkbox" name="echeance_<?php echo $nombre_de_checkbox; ?>_checkbox" id="echeance_<?php echo $nombre_de_checkbox; ?>_checkbox" value="1" <?php echo $disabled_checkbox; ?>>
																	<input type="hidden" name="echeance_<?php echo $nombre_de_checkbox; ?>_checkbox_id" id="echeance_<?php echo $nombre_de_checkbox; ?>_checkbox_id" value="<?php echo $ligne[0]; ?>">
																<?php
																	} else {
																?>
																	&nbsp;
																<?php
																	}
																?>
															</td>
															<td align="right"><?php echo $i+1; ?></td>
															<td align="left">
																<table cellspacing="0" cellpadding="0" border="0">
																	<tr>
																		<td align="left">
																			<?php
																			// Definir le nom (et id) des champs (normal et cache)
																			if($disabled_date == '') {
																				$nom_champ = 'date';
																				$nom_champ_cache = 'date_hidden';
																			} else {
																				$nom_champ = 'date_disabled';
																				$nom_champ_cache = 'date';
																			}
																			?>
																			<input type="text" name="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" size="10" maxlength="10" value="<?php echo date_depuis_bdd($ligne[2]); ?>" <?php echo $disabled_date; ?> onChange="onchange_date_echeance();">
																			<input type="hidden" name="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ_cache; ?>" id="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ_cache; ?>" value="<?php echo date_depuis_bdd($ligne[2]); ?>" >
																		</td>
																		<td>&nbsp;</td>
																		<td align="left">
																			<?php
																				if($disabled_date == '') {
																					calendarDim("div_echeance_".($i+1)."_date","document.formulaire.echeance_".($i+1)."_date",$_SESSION["langue"], "0", "0", 'fieldset_echeancier', 'null', $date_autorisee_jusqua);	
																				}
																			?>
																		</td>
																	</tr>
																</table>
			
															</td>
															<td align="right" nowrap="nowrap">
															<?php
																$valeur = $ligne[3];
																// Convertion au format francais (2 decimales, separateur de decimales : ',', pas de separateur de milliers)
																$valeur = str_replace(' ', '', montant_depuis_bdd($valeur, 2, ',', ''));
																/*
																$valeur = str_replace('.', ',', $valeur);
																$pos = strpos($valeur, ',');
																if($pos === false) {
																	$valeur = $valeur . ",00";
																}
																*/
																
																$onkeyup = 'onkeyup_montant_echeance(this);';
																/*
																// Total des echeances (normales ou additionneles)
																if($ligne[8] == 0) {
																	$onkeyup = 'onkeyup_montant_echeance();'; 
																} else {
																	$onkeyup = 'onkeyup_montant_echeance_additionnelle();'; 
																}
																*/
																$onblur = 'onblur_montant_echeance(this);';
															?>
															<?php
																// Definir le nom (et id) des champs (normal et cache)
																if($disabled_montant == '') {
																	$nom_champ = 'montant';
																	$nom_champ_cache = 'montant_hidden';
																} else {
																	$nom_champ = 'montant_disabled';
																	$nom_champ_cache = 'montant';
																}
															?>
																
															
															
															<input type="text" name="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" size="8" maxlength="12" readonly="readonly" value="<?php echo $valeur; ?>" style="text-align:right;" onChange="onchange_montant_echeance();" onKeyUp="<?php echo $onkeyup; ?>" onBlur="<?php echo $onblur; ?>" <?php echo $disabled_montant; ?> >&nbsp;<?php echo LANG_FIN_GENE_019; ?>
															<input type="hidden" name="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ_cache; ?>" id="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ_cache; ?>" value="<?php echo $valeur; ?>">
															
															
															</td>
															<td align="right">
															<?php
																// Definir le nom (et id) des champs (normal et cache)
																if($disabled_date == '') {
																	$nom_champ = 'type_reglement_id';
																	$nom_champ_cache = 'type_reglement_id_hidden';
																} else {
																	$nom_champ = 'type_reglement_id_disabled';
																	$nom_champ_cache = 'type_reglement_id';
																}
															?>
																<select name="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" id="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ; ?>" <?php echo $disabled_type_reglement; ?> onChange="onchange_type_reglement();">
																	<?php
																	for($j=0; $j<$types_reglement->numRows(); $j++) {
																		$res = $types_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $j);
																		$selected = '';
																		if($ligne_type_reglement[0] == $ligne[6]) {
																			$selected = 'selected="selected"';
																		}
																	?>
																	<option value="<?php echo $ligne_type_reglement[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_type_reglement[1]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<input type="hidden" name="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ_cache; ?>" id="echeance_<?php echo $i+1; ?>_<?php echo $nom_champ_cache; ?>" value="<?php echo $ligne[6]; ?>">
															</td>
															
															<?php
																$classe = 'texte_noir';
																$info_bulle = '';
																
																// Verifier que ce n'est pas une echeance de remise exceptionelle
																//if($ligne[8] != 2) {
																	// ROUGE : si la date de l'echeance est passee et que le motant n'a pas ete totalement paye
																	if($date_echeance < $date_jour && $reste_a_payer > 0) {
																		$classe = 'texte_rouge';
																		$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_004 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle"	style="display: block;"></a>';
																	} else {
																		// VERT : < a 0 si il a ete paye plus que le montant de l'echeance
																		if($reste_a_payer < 0) {
																			$classe = 'texte_vert';
																			$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_005 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle"	style="display: block;"></a>';
																		} else {
																			if($date_echeance >= $date_jour && $reste_a_payer > 0 && $reste_a_payer < $ligne[3]) {
																				$classe = 'texte_orange';
																				$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_013 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle"	style="display: block;"></a>';
																			} else {
																				if($disabled_reste_a_payer != '') {
																					$classe = 'texte_gris';
																				}
																			}
																		}
																	}
																	$valeur = montant_depuis_bdd($reste_a_payer);
																/*
																} else {
																	$valeur = montant_depuis_bdd('0.0');
																}
																*/
															?>												
															<td nowrap="nowrap" align="right" valign="middle">
																<table border="0" align="right" cellpadding="0" cellspacing="0">
																	<tr>
																		<td nowrap="nowrap"><?php echo $info_bulle; ?></td>
																		<td nowrap="nowrap">&nbsp;</td>
																		<td valign="middle" nowrap="nowrap">										
																			<span class="<?php echo $classe; ?>"><?php echo $valeur; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></span>
																		</td>
																	</tr>
																</table>
															</td>
															
															<td align="left">
																<table border="0" cellpadding="0" cellspacing="0">
																
																	<tr>
																		<?php
																		// Afficher le bon bouton
			
																		if(true || $reste_a_payer > 0) {
																			// => reste quelquechose a payer : voir et ajouter
																		?>
																		<td align="left"><input type="button" class="button" value="<?php echo LANG_FIN_REGL_001 . ' (' . $tab_infos_echeances[$ligne[0]]['reglements'] . ')'; ?>" onClick="onclick_reglement_editer(<?php echo $ligne[0]; ?>, 'editer');" <?php echo $disabled_reglements; ?>></td>
																	<?php
																		} else {
																			// => tout est paye : voir seulement
																		?>
																		<td align="left"><input type="button" class="button" value="<?php echo LANG_FIN_REGL_001 . ' (' . $tab_infos_echeances[$ligne[0]]['reglements'] . ')'; ?>" onClick="onclick_reglement_editer(<?php echo $ligne[0]; ?>, 'voir');" <?php echo $disabled_reglements; ?>></td>
																		<?php
																		}
																		?>
																		<td align="left">&nbsp;
																			
																		</td>
																	
																	<?php
																	// Afficher l'info-bulle d'aide
																	$info_bulle = '&nbsp;';
																	//if(trim($ligne[8]) == 1) {	
																		$info_bulle = '<a href="javascript:;" onclick="onclick_echeance_plus_infos(\'' . $ligne[0] . '\')" alt="' . LANG_FIN_GENE_044 . '" title="' . LANG_FIN_GENE_044 . '" id="echeance_' . $ligne[0] . '_infos_lien"><span id="echeance_' . $ligne[0] . '_infos_texte_lien">[+]</span></a>';
																	//}
																	?>
																		<td align="left">
																			<?php echo $info_bulle; ?>
																		</td>
																	</tr>
																</table>
															</td>
															
															<?php
																	$info_bulle1 = '<a href="javascript:;" onclick="onclick_detail_frais(\'' . ($i+1) . '\')" alt="' . LANG_FIN_GENE_044 . '" title="' . LANG_FIN_GENE_044 . '" id="groupe_' . ($i+1) . '_detail_frais"><span id="groupe_' . ($i+1) . '_frais_detail">[+]</span></a>';
															?>
															<td align="left">
																	<?php echo $info_bulle1; ?>
															</td>
															
														</tr>
														<tr id="echeance_<?php echo $ligne[0]; ?>_infos_tr" style="display:none"  class='tabnormal2'>
															<td colspan="8" align="left">
																<table border="0" cellpadding="0" cellspacing="0" align="left">
																	<tr>
																		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
																		<td align="right"><?php echo LANG_FIN_ECHE_010; ?>&nbsp;:&nbsp;</td>
																		<td align="left">
																			<input type="text" name="echeance_<?php echo $i+1; ?>_libelle" id="echeance_<?php echo $i+1; ?>_libelle" size="64" maxlength="64" value="<?php echo $ligne[7]; ?>" >
																		</td>
																	</tr>
																	<tr>
																		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
																		<td align="right"><?php echo LANG_FIN_RIB_017; ?>&nbsp;:&nbsp;</td>
																		<td align="left">
																			<table border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td>
																						<select name="echeance_<?php echo $i+1; ?>_numero_rib" id="echeance_<?php echo $i+1; ?>_numero_rib">
																							<?php
																							$selected = '';
																							if($ligne[10] == 0) {
																								$selected = 'selected="selected"';
																							}
																							?>
																							<option value="0" <?php echo $selected; ?>><?php echo LANG_FIN_GENE_050; ?></option>
																							<?php
																							for($j=0; $j<count($tab_rib); $j++) {
																								$selected = '';
																								if($ligne[10] == ($j+1)) {
																									$selected = 'selected="selected"';
																								}
																							?>
																							<option value="<?php echo ($j+1); ?>" <?php echo $selected; ?>><?php echo ($j+1); ?> - <?php echo $tab_rib[$j]; ?></option>
																							<?php
																							}
																							?>
																						</select>
																					</td>
																					<td>&nbsp;</td>
																					<td>
																						<input type="button" class="button" onClick="onclick_editer_rib(<?php echo $elev_id; ?>);" value='<?php echo LANG_FIN_RIB_001; ?>' >																		
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														
														<tr id="liste_<?php echo $i+1; ?>_detail_frais" style="display:none"  class='tabnormal2'>
																<td colspan="8" align="left">
																<table border="0" cellpadding="0" cellspacing="0" align="left">
																<?php
																	$num=0;
																	$nb_montant= $res1_nb->numRows();
																	for($grp=0; $grp<$resgroupe->numRows(); $grp++)
																	{
																		$res1 = $resgroupe->fetchInto($ligne1, DB_FETCHMODE_DEFAULT, $grp);
																		
																		if($ligne[0] == $ligne1[1])
																		{
																		$num++;?>
																		<tr>		
																			<td>
																					<td align="right">
																					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ligne1[4];/////////////;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																					</td>
																			</td>
																			<td>
																					<td align="right">
																					<?php
																					$valeur1  = number_format($ligne1[3],2, '.', '');  
																					$valeur1  = str_replace('.', ',', $valeur1 );
																					?>
																					<input type="text" name="montant_groupe_echeancier_<?php echo $i+1;?>_<?php echo $num; ?>" id="montant_groupe_echeancier_<?php echo $i+1;?>_<?php echo $num; ?>" size="8" maxlength="12" value="<?php echo $valeur1; ?>" style="text-align:right;" onChange="Change_montant(<?php echo $i+1; ?>,<?php echo $nb_montant; ?>)" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019;"</br>" ?></td>
																			</td>
																		</tr>
																		<input type="hidden" name="groupe_<?php echo ($i+1);?>_<?php echo $num;?>_id" id="groupe_<?php echo ($i+1);?>_<?php echo $num;?>_id" value="<?php echo $ligne1[2]; ?>">
																	<?php 
																		}
																	}
																	?>	
																</table>
																</td>
														</tr>
														
														<?php
															}
														} else {
														?>
														<tr class='tabnormal2'>
															<td align="left" colspan="8"><?php echo LANG_FIN_INSC_008; ?></td>
														</tr>
														<?php
														}
														?>
													</table>
												</td>
											</tr>
											
											<?php
											// ******************* AFFICHAGE DES ACTIONS POSSIBLES ******************
											?>
											
											<?php
											if($echeances->numRows() > 0 && $nombre_de_checkbox > 0) {
											?>
											<tr>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0" align="left">
														<tr>	
															<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="1"></td>
															<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/fleche_droite_vers_haut.png"; ?>" border="0"></td>
															<td valign="middle">&nbsp;</td>
															<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="1"></td>
															<td valign="middle">
																<select name="actions_echeance" id="actions_echeance" onChange="onchange_actions_echeance()">
																	<option value="rien" selected="selected"><?php echo LANG_FIN_GENE_050; ?></option>
																	<option value="supprimer"><?php echo LANG_FIN_ECHE_019; ?></option>
																	<option value="diviser"><?php echo LANG_FIN_ECHE_020; ?></option>
																	<option value="fusionner"><?php echo LANG_FIN_ECHE_021; ?></option>
																</select>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<?php
											}
											?>
										</table>
										
										
										<?php
										// ******************* AFFICHAGE DES TOTAUX ******************
										?>
										
										<br>
                                        
										<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">
											<tr class="tabnormal2">
												<td align="center" nowrap="nowrap" colspan="4"><b><?php echo ucfirst(LANG_FIN_GENE_043); ?></b></td>
											</tr>
											<tr class="tabnormal2">
												<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_REGL_017); ?></b></td>
												<?php
												$classe = 'texte_noir';
												// Total pour les echeances normales
												$valeur = montant_depuis_bdd($montant_normales_total);
												?>												
												
												<td align="right" nowrap="nowrap"><b><span id="montant_normales_total_chiffres" class="<?php echo $classe; ?>"><?php echo $valeur; ?></span>&nbsp;<span id="montant_normales_total_monnaie" class="<?php echo $classe; ?>"><?php echo LANG_FIN_GENE_019; ?></span></b></td>
												<td align="right" nowrap="nowrap">&nbsp;</td>
												<td align="right" nowrap="nowrap">&nbsp;</td>
											</tr>
											<tr class="tabnormal2">
												<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_REGL_022); ?></b></td>
												<?php
												$classe = 'texte_noir';
												// Total pour les remises exceptionelles
												$valeur = montant_depuis_bdd($montant_remises_total);
												?>												
												
												<td align="right" nowrap="nowrap"><b><span id="montant_remises_total_chiffres" class="<?php echo $classe; ?>"><?php echo $valeur; ?></span>&nbsp;<span id="montant_remises_total_monnaie" class="<?php echo $classe; ?>"><?php echo LANG_FIN_GENE_019; ?></span></b></td>
												<td align="right" nowrap="nowrap">&nbsp;</td>
												<td align="right" nowrap="nowrap">&nbsp;</td>
											</tr>
											<tr class="tabnormal2">
												<?php
												$classe = 'texte_noir';
												// Total pour les echeances normales + les remises
												$montant_normales_et_remises_total = $montant_normales_total + $montant_remises_total;

												?>
												<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_REGL_023); ?></b></td>
												<?php
												$valeur = montant_depuis_bdd($montant_normales_et_remises_total);
												$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_007 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle"	style="display: block;"></a>';

												// Verifier que le montant des echeances normales correpond au total des frais
												$classe = 'texte_noir';
												if(number_format($montant_normales_et_remises_total, 2) != number_format($montant_frais_total, 2)) {
													$classe = 'texte_rouge';
												}


												?>												
												<td align="right" nowrap="nowrap">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="middle" nowrap="nowrap"><?php echo $info_bulle; ?></td>
															<td valign="middle" nowrap="nowrap">&nbsp;</td>
															<td valign="middle" nowrap="nowrap">
																<b><span id="montant_normales_et_remises_total_chiffres" class="<?php echo $classe; ?>"><?php echo $valeur; ?></span>&nbsp;<span id="montant_normales_et_remises_total_monnaie" class="<?php echo $classe; ?>"><?php echo LANG_FIN_GENE_019; ?></span></b>
															</td>
														</tr>
													</table>
												</td>
												
												<?php
												?>
												<td align="right" nowrap="nowrap">&nbsp;</td>
												<td align="right" nowrap="nowrap">&nbsp;</td>
												
											</tr>

											<tr class="tabnormal2">
												
												<?php
												// Montant des echeances additionnelles
												?>
												<td align="right" nowrap="nowrap" valign="middle"><b><?php echo ucfirst(LANG_FIN_REGL_018); ?></b></td>
												<?php
												$valeur = montant_depuis_bdd($montant_additionnelles_total);
												$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_019 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle"	style="display: block;"></a>';
												?>												
												<td align="right" nowrap="nowrap">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="middle" nowrap="nowrap"><?php echo $info_bulle; ?></td>
															<td valign="middle" nowrap="nowrap">&nbsp;</td>
															<td valign="middle" nowrap="nowrap">
																<b><span id="montant_additionnelles_total_chiffres"><?php echo $valeur; ?></span>&nbsp;<span id="montant_additionnelles_total_monnaie"><?php echo LANG_FIN_GENE_019; ?></span></b>
															</td>
														</tr>
													</table>
												</td>


												<?php
												// Solde previsionnel
												?>
												<td align="right" nowrap="nowrap" valign="middle"><b><?php echo ucfirst(LANG_FIN_GENE_035); ?></b></td>
												<?php
												$classe = 'texte_noir';
												// $solde_previsionnel > $total_a_payer_total => solde crediteur (plus de paiement que necessaire)
												if($solde_previsionnel > $total_a_payer_total) {
													$classe = 'texte_vert';
												}
												// $solde_previsionnel < $total_a_payer_total => solde debiteur (manque des paiements)
												if($solde_previsionnel < $total_a_payer_total) {
													$classe = 'texte_rouge';
												}
												$valeur = montant_depuis_bdd($solde_previsionnel);
												$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_006 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"	></a>';
												?>												
												<td align="right" nowrap="nowrap">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="middle" nowrap="nowrap"><?php echo $info_bulle; ?></td>
															<td valign="middle" nowrap="nowrap">&nbsp;</td>
															<td valign="middle" nowrap="nowrap">
																<b><span id="solde_previsionnel_chiffres" class="<?php echo $classe; ?>"><?php echo $valeur; ?></span>&nbsp;<span id="solde_previsionnel_monnaie" class="<?php echo $classe; ?>"><?php echo LANG_FIN_GENE_019; ?></span></b>
															</td>
														</tr>
													</table>
												</td>
												
											</tr>
											<tr class="tabnormal2">
												
												<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_036); ?></b></td>
												<?php
												// Total a payer
												$classe = 'texte_noir';
												$valeur = montant_depuis_bdd($total_a_payer_total);
												$info_bulle = '<a href="javascript:;"  onMouseOver="AffBulle3(\'' . LANG_FIN_GENE_002 . '\',\'./image/commun/info.jpg\',\'' . LANG_FIN_REGL_024 . '\', \'fieldset_echeancier\');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"	></a>';
												?>												
												<td align="right" nowrap="nowrap">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="middle" nowrap="nowrap"><?php echo $info_bulle; ?></td>
															<td valign="middle" nowrap="nowrap">&nbsp;</td>
															<td valign="middle" nowrap="nowrap">
																<b><span id="total_a_payer_total_chiffres" class="<?php echo $classe; ?>"><?php echo $valeur; ?></span>&nbsp;<span id="total_a_payer_total_monnaie" class="<?php echo $classe; ?>"><?php echo LANG_FIN_GENE_019; ?></span></b>
															</td>
														</tr>
													</table>
												
												
													
												</td>
												<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_034); ?></b></td>
												<?php
												$classe = 'texte_noir';
												// < a 0 si il a ete paye plus que le montant de l'echeance
												if($reste_a_payer_total < 0) {
													$classe = 'texte_vert';
												}
												$valeur = montant_depuis_bdd($reste_a_payer_total);
												?>												
												<td align="right" nowrap="nowrap"><b><span id="reste_a_payer_total_chiffres" class="<?php echo $classe; ?>"><?php echo $valeur; ?></span>&nbsp;<span id="reste_a_payer_total_monnaie" class="<?php echo $classe; ?>"><?php echo LANG_FIN_GENE_019; ?></span></b></td>

												
											</tr>											
										</table>										
										<?php
										// ***********************************************************
										?>	
									
									
										<?php
										// ******************* STOCKAGE DES TOTAUX (format bdd) ******************
										?>
										<input type="hidden" name="echeances_total" id="echeances_total" value="<?php echo $echeances->numRows(); ?>">
										<input type="hidden" name="montant_normales_et_remises_total" id="montant_normales_et_remises_total" value="<?php echo $montant_normales_et_remises_total; ?>">
										<input type="hidden" name="montant_additionnelles_total" id="montant_additionnelles_total" value="<?php echo $montant_additionnelles_total; ?>">
										<input type="hidden" name="nombre_de_checkbox" id="nombre_de_checkbox" value="<?php echo $nombre_de_checkbox; ?>">
									
										<?php
										// ***********************************************************
										?>	
																		
										
										<?php
										// ******************* AJOUT D'UNE NOUVELLE ECHEANCE ******************
										?>
										
										<table cellspacing="1" cellpadding="3" border="0" width="90%" align="center">
											<tr>
												<td align="left">&nbsp;
													
												</td>
											</tr>
											<tr>
												<td align="left">
													 <a href="javascript:;" onClick="onclick_echeance_ajout();">[+] <?php echo LANG_FIN_ECHE_005;?></a>
												</td>
											</tr>
											
											<tr id="tr_ajouter" style="display:none">
												<td align="center">
													<div style="border:#0099CC dashed 1px; padding:15px; margin-top:5px;">
														<br>
														<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">
															<tr bgcolor="#ffffff">
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_030; ?></b></td>
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_ECHE_010; ?></b></td>
																<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
																<td align="right" nowrap="nowrap">
																	<table border="0" cellspacing="0" cellpadding="0" align="center">
																		<tr>
																			<td valign="middle">
																				<b><?php echo LANG_FIN_TECH2_002; ?></b>
																			</td>
																			<td valign="middle">&nbsp;</td>
																			<td valign="middle">
																				<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_TECH2_006; ?>', 'fieldset_echeancier');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
																			</td>
																		</tr>
																	</table>																
																</td>
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_TREG_015; ?></b></td>
															</tr>
															<tr bgcolor="#ffffff">
																<td align="left" nowrap="nowrap" valign="top">
																	<table cellspacing="0" cellpadding="0" border="0">
																		<tr>
																			<td align="left">
																				<input type="text" name="nouvelle_echeance_date_echeance" id="nouvelle_echeance_date_echeance" size="10" maxlength="10" value="<?php echo date("d/m/Y"); ?>" readonly="">
																			</td>
																			<td>&nbsp;</td>
																			<td align="left">
																				<?php
																				calendarDim("div_nouvelle_echeance_date_echeance","document.formulaire.nouvelle_echeance_date_echeance",$_SESSION["langue"], "0", "0", 'fieldset_echeancier', 'null', 'null');	
																				?>
																			</td>
																		</tr>
																	</table>
																				
																</td>
																<td align="left" nowrap="nowrap" valign="top">
																	<input type="text" name="nouvelle_echeance_libelle" id="nouvelle_echeance_libelle" size="20" maxlength="64" value="">
																</td>
																<?php
																$valeur = "0,00";
																?>
																
																<td align="right" nowrap="nowrap" valign="top">
																	<input type="text" name="nouvelle_echeance_montant" id="nouvelle_echeance_montant" size="8" maxlength="12" readonly="readonly" value="<?php echo $valeur; ?>" style="text-align:right;" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																</td>
																<td align="left" nowrap="nowrap" valign="top">
																	<select name="nouvelle_echeance_type_echeance" id="nouvelle_echeance_type_echeance"  >
																		<option value="normale_lissee" selected="selected"><?php echo LANG_FIN_TECH2_003; ?></option>																		
																			<option value="normale_non_lissee"><?php echo LANG_FIN_TECH2_004; ?></option>																		
																		<option value="additionnelle"><?php echo LANG_FIN_TECH2_005; ?></option>																		
																		<option value="remise_exceptionelle"><?php echo LANG_FIN_TECH2_007; ?></option>																		
																	
																	</select>
																</td>
																<td align="left" nowrap="nowrap" valign="top">
																	<select name="nouvelle_echeance_type_reglement_id" id="nouvelle_echeance_type_reglement_id"  >
																		<?php
																		for($j=0; $j<$types_reglement->numRows(); $j++) {
																			$res = $types_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $j);
																			$selected = '';
																			if($j == 0) {
																				$selected = 'selected="selected"';
																			}
																		?>
																		<option value="<?php echo $ligne_type_reglement[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_type_reglement[1]; ?></option>
																		<?php
																		}
																		?>
																	</select>
																</td>
															</tr>
																
															<tr bgcolor="#ffffff">
																<td align="left" colspan="5" nowrap="nowrap" valign="top">
																	<table border="0" cellpadding="0" cellspacing="0" align="left">
																		<?php
																		for($grp=0; $grp<$res1_nb->numRows(); $grp++)
																		{
																			$nb_montant=$res1_nb->numRows();
																			$res5 = $res1_nb->fetchInto($ligne6, DB_FETCHMODE_DEFAULT, $grp);?>
																		<tr>		
																			<td>
																					<td align="right">
																					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ligne6[1];/////////////;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																					</td>
																			</td>
																			<td>
																					<td align="right">
																					<?php
																					$valeur1  = "0,00";
																					?>
																					<input type="text" name="montant_groupe_echeancier_<?php echo $grp; ?>" id="montant_groupe_echeancier_<?php echo $grp; ?>" size="8" maxlength="12" value="<?php echo $valeur1; ?>" style="text-align:right;" onChange="Change_montant1(<?php echo $nb_montant; ?>)" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019;"</br>" ?></td>
																			</td>
																		</tr>
																		<input type="hidden" name="groupe_<?php echo $grp;?>_id" id="groupe_<?php echo $grp+1;?>_id" value="<?php echo $ligne6[0]; ?>">
																	<?php }?>
																	</table>
																</td>
															</tr>
														</table>
														<br>
														<input type="button" class="button" value="<?php echo LANG_FIN_GENE_040; ?>" onClick="onclick_echeance_ajout_annuler();" >
														<br>
													</div>
												</td>
											</tr>
										</table>
										<?php
										// ***********************************************************
										?>	
									</fieldset>
								
								</td>
							</tr>
							
							<?php
							// ******************* LEGENDE ******************
							?>

							<tr>
								<td align="center">
									<br>
									<br>
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" align="center" style="z-index:10;">
										<tr class="tabnormal2">
											<td align="center" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_046); ?></b></td>
										</tr> 
										<tr class="tabnormal2">
											<td align="left">
												<table cellspacing="0" cellpadding="3" border="0" align="left">
													<tr>
														<td>&nbsp;</td>
														<td align="center" nowrap="nowrap" colspan="2"><?php echo LANG_FIN_ECHE_012; ?> :</td>
													</tr>
													<tr class="tabnormal2">
														<td>&nbsp;</td>
														<td align="left">
															<table cellspacing="0" cellpadding="3" border="0" align="left">
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_blanc.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_013; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_gris.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_014; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_vert.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_017; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_gris_vert.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_018; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_rose.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_015; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_gris_rose.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_016; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_bleu.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_027; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<table cellspacing="0" cellpadding="0" border="0" align="center">
																			<tr>
																				<td style="background-image:url('<?php echo $g_chemin_relatif_module; ?>images/fond_ligne_gris_bleu.jpg'); background-repeat:repeat;"><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_028; ?></td>
																</tr>
																<tr>
																	<td>&nbsp;</td>
																	<td align="center">
																		<span class="texte_gris">abdc-1234</span>
																	</td>
																	<td>&nbsp;</td>
																	<td align="left" valign="middle"><?php echo LANG_FIN_ECHE_029; ?></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
							// ***********************************************************
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
                                    	<?php
										if($liste_reglements->numRows() == 0) {
										?>
										<tr>
											<td align="center" colspan="2">
                                            	<table border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
                                                    	<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_FIN_INSC_030?>","onclick_supprimer()");</script>
                                                        </td>
                                                    </tr>
                                                </table>
											</td>
										</tr>
                                        <?php
										}
										?>
										<tr>
											<td align="center">
                                            	<table border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
                                                    	<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_004?>","onclick_enregistrer()");</script>
                                                        </td>
                                                    </tr>
                                                </table>
											</td>
											<!--
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print Actualiser?>","actualiser()");</script>
											</td>
											-->
											<td align="center">
                                            	<table border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
                                                    	<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
                                                        </td>
                                                    </tr>
                                                </table>
                                            
											</td>
										</tr>
									</table>
								</td>
							</tr>
								
								
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
						<input type="hidden" name="fenetre" id="fenetre" value="<?php echo $fenetre; ?>">

					</form>
					<?php
					}
					?>
                    
                    <?php
					if(!$afficher_tableaux && $operation == 'supprimer_inscription_sans_reglements') {
					?>
                         <table border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center">
									<a name="MESSAGE"></a>
									<?php 
									msg_util_afficher();
									msg_util_attente_init(); 
									?>
									</td>
							</tr>
                            <tr>
                                <td align="center">
                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>


					<?php
					}
					?>
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						var un_element_modifie = false;
						var liste_fenetre = new Array();

						var ajouter_frais = false;
						var ajouter_echeance = false;
						
						// Montrer le tableau permettant d'ajouter un frais
						function onclick_frais_ajout(nb_types_frais_disponibles) {
							document.getElementById('tr_ajouter_frais').style.display = '';
							if(nb_types_frais_disponibles > 0) {
								ajouter_frais = true;
							} else {
								ajouter_frais = false;
							}
						}
						
						// Cacher le tableau permettant d'ajouter un frais
						function onclick_frais_ajout_annuler() {
							document.getElementById('tr_ajouter_frais').style.display = 'none';
							ajouter_frais = false;
						}
						
						function onchange_nouveau_frais_type_frais_id() {
						
						}

						// Montrer le tableau permettant d'ajouter une echeance
						function onclick_echeance_ajout() {
							document.getElementById('tr_ajouter').style.display = '';
							ajouter_echeance = true;
						}
						
						// Cacher le tableau permettant d'ajouter une echeance
						function onclick_echeance_ajout_annuler() {
							document.getElementById('tr_ajouter').style.display = 'none';
							ajouter_echeance = false;
						}
						
						
						function onclick_annuler() {
							<?php
							if(inscription_total_frais($inscription_id, -1) == (inscription_total_echeances($inscription_id, 0) + inscription_total_echeances($inscription_id, 2))) {
							?>
								msg_util_attente_montrer(true);
								document.getElementById('formulaire_annuler').submit();
							<?php
							} else {
							?>
								messsage = "<?php echo LANG_FIN_INSC_033; ?>";
								messsage = messsage.replace('#s1#', montant_depuis_bdd('<?php echo inscription_total_echeances($inscription_id, 0) + inscription_total_echeances($inscription_id, 2); ?>', 2, ',', ' '));
								messsage = messsage.replace('#s2#', montant_depuis_bdd('<?php echo inscription_total_frais($inscription_id, -1); ?>', 2, ',', ' '));
								alert(messsage);
							<?php
							}
							?>							
							
						
						}



						// Click sur une option d'un frais : inlus ou lisse (on recalcule le montant total des frais)
						function onclick_frais_option(indice, type_frais_id, option) {
							var valeur;
							var obj_champ;
							
							obj_champ = document.getElementById('type_frais_' + indice + '_' + option);
							switch(option) {
								case 'montant':
									// Recuperer la valeur du champ
									formatage_montant(obj_champ);
									valeur = obj_champ.value;
									valeur = valeur.replace(",", ".");
									valeur = valeur.replace(" ", "");
									break;
								case 'optionnel':
									// Recuperer la valeur du champ
									if(obj_champ.checked) {
										valeur = 1;
									} else {
										valeur = 0;
									}
									break;
								case 'selectionne':
									// Recuperer la valeur du champ
									if(obj_champ.checked) {
										valeur = 1;
									} else {
										valeur = 0;
									}
									break;
								case 'lisse':
									// Recuperer la valeur du champ
									if(obj_champ.checked) {
										valeur = 1;
									} else {
										valeur = 0;
									}
									break;
							}
							tab_type_frais[indice-1][option] = valeur;

							calcul_frais_lisses_non_lisses();
							
						}
						 
						function calcul_frais_lisses_non_lisses() {
							var i;
							var total_lisses = 0.0;
							var total_non_lisses = 0.0;
							var total_final = 0.0;
							var obj_champ_optionnel, obj_champ_selectionne, obj_champ_lisse, obj_montant;
							//alert('ggg');
							// forcer l'option 'selectionne' si l'option 'optionnel' est decochee
							for (i=0; i<tab_type_frais.length; i++) {
							
								// Cocher/decocher l'option 'optionnel' et 'selectionne' (en fonction de ce qui est dans le tableau 'tab_type_frais')
								obj_champ_optionnel = document.getElementById('type_frais_' + (i+1) + '_optionnel');
								obj_champ_selectionne = document.getElementById('type_frais_' + (i+1) + '_selectionne');
								if(tab_type_frais[i]['optionnel'] == 0) {
									obj_champ_optionnel.checked = false;
									obj_champ_selectionne.checked = true;
									obj_champ_selectionne.disabled = true;
									tab_type_frais[i]['selectionne'] = 1;
									
								} else {
									obj_champ_optionnel.checked = true;
									obj_champ_selectionne.disabled = false;
								}
								
							}

							// forcer l'option 'lise' si l'option 'selectionne' est decochee
							for (i=0; i<tab_type_frais.length; i++) {
								obj_champ_lisse = document.getElementById('type_frais_' + (i+1) + '_lisse');
								if(tab_type_frais[i]['selectionne'] == 0) {
									//obj_champ_lisse.checked = false;
									obj_champ_lisse.disabled = true;
								} else {
									obj_champ_lisse.disabled = false;
								}
							}
							
							// Calculer les totaux (global, lisses, non-lisses)
							for (i=0; i<tab_type_frais.length; i++) {
								if(tab_type_frais[i]['selectionne'] == 1) {
									if(tab_type_frais[i]['lisse'] == 1) {
										total_lisses += tab_type_frais[i]['montant'] * 1;
									} else {
										total_non_lisses += tab_type_frais[i]['montant'] * 1;
									}
								}
							}
							
							total_final = total_lisses + total_non_lisses;
							
							//alert(total_lisses + ' + ' + total_non_lisses + ' = ' + total_final);
							
							// Afficher les totaux
							document.getElementById('total_lisses').innerHTML = montant_depuis_bdd(total_lisses, 2, ',', ' ');
							document.getElementById('total_non_lisses').innerHTML = montant_depuis_bdd(total_non_lisses, 2, ',', ' ');
							document.getElementById('total_final').innerHTML = montant_depuis_bdd(total_final, 2, ',', ' ');
							
							// Mettre a jour la valeur du champ cache contenant le montant total des frais
							document.getElementById('montant_frais_total').value = total_final;
							
							// Verifier si le total des frais correspond toujours a celui des echeances (normales + remises)
							montant_normales_et_remises_total = document.getElementById('montant_normales_et_remises_total').value * 1;
							if(montant_normales_et_remises_total == total_final) {
								document.getElementById('montant_normales_et_remises_total_chiffres').className = "texte_noir";
								document.getElementById('montant_normales_et_remises_total_monnaie').className = "texte_noir";
							} else {
								document.getElementById('montant_normales_et_remises_total_chiffres').className = "texte_rouge";
								document.getElementById('montant_normales_et_remises_total_monnaie').className = "texte_rouge";
							}
							
						}

						// Click sur une option d'un frais : inlus ou lisse (on recalcule le montant total des frais)
						function onclick_frais_option_old() {
							var total_lisses = 0.0;
							var total_non_lisses = 0.0;
							var total_final = 0.0;
							var obj_inclus, obj_lisse, montant_normales_et_remises_total;
							var i;
							
							
							
							
							/*
							
							for (i=0; i<tab_type_frais.length; i++) {

								// Verifier si le frais est optionnel ou non
								if(tab_type_frais[i]['optionnel'] == '0') {
									// => pas optionnel : automatiquement inclus
									
									// Verifier si il est lisse ou non et faire la somme
									eval('obj_lisse = document.getElementById("type_frais_id_' + tab_type_frais[i]['type_frais_id'] + '_lisse");');
									if(obj_lisse.checked) {
										total_lisses += tab_type_frais[i]['montant'] * 1;
									} else {
										total_non_lisses += tab_type_frais[i]['montant'] * 1;
									}
								} else {
									// Optionnel

									// Verifier si il est inclus ou non
									eval('obj_inclus = document.getElementById("type_frais_id_' + tab_type_frais[i]['type_frais_id'] + '");');
									if(obj_inclus.checked) {
										// Verifier si il est lisse ou non et faire la somme
										eval('obj_lisse = document.getElementById("type_frais_id_' + tab_type_frais[i]['type_frais_id'] + '_lisse");');
										if(obj_lisse.checked) {
											total_lisses += tab_type_frais[i]['montant'] * 1;
										} else {
											total_non_lisses += tab_type_frais[i]['montant'] * 1;
										}
									}
								}
							}

							// Calculer le total global (lisses et non lisses)
							total_final = total_lisses + total_non_lisses;
							
							// Afficher les totaux
							document.getElementById('total_lisses').innerHTML = montant_depuis_bdd(total_lisses, 2, ',', ' ');
							document.getElementById('total_non_lisses').innerHTML = montant_depuis_bdd(total_non_lisses, 2, ',', ' ');
							document.getElementById('total_final').innerHTML = montant_depuis_bdd(total_final, 2, ',', ' ');
							
							// Mettre a jour la valeur du champ cache contenant le montant total des frais
							document.getElementById('montant_frais_total').value = total_final;
							
							// Verifier si le total des frais correspond toujours a celui des echeances (normales + remises)
							montant_normales_et_remises_total = document.getElementById('montant_normales_et_remises_total').value * 1;
							if(montant_normales_et_remises_total == total_final) {
								document.getElementById('montant_normales_et_remises_total_chiffres').className = "texte_noir";
								document.getElementById('montant_normales_et_remises_total_monnaie').className = "texte_noir";
							} else {
								document.getElementById('montant_normales_et_remises_total_chiffres').className = "texte_rouge";
								document.getElementById('montant_normales_et_remises_total_monnaie').className = "texte_rouge";
							}
							
							*/
							
						}

						// A chaque fois que le montant d'une echeance est modifie (a la sortie du champ)
						// Pour calculer le total des montans des echeances
						function onblur_montant_echeance(obj) {
							var montant = "";
							montant = obj.value;
							
							//alert(montant);
							//montant = montant_echeance.replace(".", ",");
							
							if(montant == "") {
								montant = "0";
							}
							if(montant.indexOf(",") < 0) {
								montant = montant + ",00";
							}
							montant = montant.replace(" ", "");
							obj.value = montant;
						}
						
						function get_cursor_position(obj) {
							var currentRange=document.selection.createRange();   
							var workRange=currentRange.duplicate();
							
							obj.select();
							var allRange=document.selection.createRange();
							var len=0;   
							
							while(workRange.compareEndPoints("StartToStart",allRange)>0)   
							{   
								workRange.moveStart("character",-1);   
								len++;   
							}   
							
							currentRange.select();   
							return(len); 
						}
						
						
						function set_cursor_position(obj, caretPos) {
							if(obj != null) {
								if(obj.createTextRange) {
									var range = obj.createTextRange();
									range.move('character', caretPos);
									range.select();
								}
								else {
									if(obj.selectionStart) {
										obj.focus();
										obj.setSelectionRange(caretPos, caretPos);
									}
									else
										obj.focus();
								}
							}
						}	
											
						// A chaque fois que le montant d'une echeance est modifie (a la relache de la touche)
						// Pour calculer le total des montans des echeances
						function onkeyup_montant_echeance(obj) {
							var i;
							var montant_normales_total = 0.0;
							var montant_additionnelles_total = 0.0;
							var montant_remises_total = 0.0;
							var montant_normales_et_remises_total = 0.0;
							var total_a_payer_total = 0.0;
							var type_echeance, date_echeance, montant_echeance;

							//formatage_montant(obj);
							
							var pos_curseur = get_cursor_position(obj);
							
							montant_echeance = obj.value;
							montant_echeance = montant_echeance.replace(".", ",");
							montant_echeance = montant_echeance.replace(" ", "");
							obj.value = montant_echeance;
							
							set_cursor_position(obj, pos_curseur);
							
							// Recuperer la date de depart
							var date_depart = document.formulaire.date_depart.value;
							if(trim(date_depart) != '') {
								if(est_date(trim(date_depart), false)) {
									date_depart = Date.parse(date_fr_vers_us(date_depart));
								} else {
									date_depart = '';
								}
							}

							// Recuperer le total des frais
							var montant_frais_total = document.formulaire.montant_frais_total.value * 1;

							// Recuperer le nom d'echeances
							var echeances = document.formulaire.echeances_total.value;
							
							un_element_modifie = true;

							// Verifier chaque echeance
							for (i=1; i<=echeances; i++) {
								// Recuperer les donnees de l'echeance
								type_echeance = document.getElementById('echeance_' + i + '_type').value;
								
								date_echeance = document.getElementById('echeance_' + i + '_date').value;
								date_echeance = Date.parse(date_fr_vers_us(date_echeance));
								montant_echeance = document.getElementById('echeance_' + i + '_montant').value;
								montant_echeance = montant_echeance.replace(',', '.') * 1;
								
								// Verifier que le montant est un nombre valide
								if(est_nombre(montant_echeance, 'decimal', ',')) {
									// Ajouter le montant aux totaux
									switch(type_echeance) {
										case '0':
											//alert(i + ' : ' + montant_echeance);
											montant_normales_total += montant_echeance;
											if(date_depart == '' || date_echeance < date_depart) {
												total_a_payer_total += montant_echeance;
											}
											break;
										case '1':
											montant_additionnelles_total += montant_echeance;
											if(date_depart == '' || date_echeance < date_depart) {
												total_a_payer_total += montant_echeance;
											}
											break;
										case '2':
											montant_remises_total += montant_echeance;
											break;
										default:
											alert('type inconnu echeance n°' + i + ' (type=' + type_echeance + ')');
											break;
									}
								} else {
									alert('erreur n°' + i)
								}

							}
							//alert(montant_normales_total);
							
							// Somme des normales et remises
							montant_normales_et_remises_total = montant_normales_total + montant_remises_total;
							
							// Affichage du total des normales
							document.getElementById('montant_normales_total_chiffres').innerHTML = montant_depuis_bdd(montant_normales_total, 2, ',', ' ');


							// Affichage du total des remises
							document.getElementById('montant_remises_total_chiffres').innerHTML = montant_depuis_bdd(montant_remises_total, 2, ',', ' ');
							//alert(montant_additionnelles_total);

							// Affichage du total des normales + remises
							if(montant_normales_et_remises_total == montant_frais_total) {
								document.getElementById('montant_normales_et_remises_total_chiffres').innerHTML = montant_depuis_bdd(montant_normales_et_remises_total, 2, ',', ' ');
								document.getElementById('montant_normales_et_remises_total_chiffres').className = "texte_noir";
								document.getElementById('montant_normales_et_remises_total_monnaie').className = "texte_noir";
							} else {
								document.getElementById('montant_normales_et_remises_total_chiffres').innerHTML = montant_depuis_bdd(montant_normales_et_remises_total, 2, ',', ' ');
								document.getElementById('montant_normales_et_remises_total_chiffres').className = "texte_rouge";
								document.getElementById('montant_normales_et_remises_total_monnaie').className = "texte_rouge";
							}
							
							// Mettre a jour le total (normales + remises) dans le champ cache
							// (utilise en suite pour comparer avec le total des frais)
							document.formulaire.montant_normales_et_remises_total.value = montant_normales_et_remises_total;


							// Affichage du total des additionnelles
							document.getElementById('montant_additionnelles_total_chiffres').innerHTML = montant_depuis_bdd(montant_additionnelles_total, 2, ',', ' ');

							// Affichage du total a payer
							document.getElementById('total_a_payer_total_chiffres').innerHTML = montant_depuis_bdd(total_a_payer_total, 2, ',', ' ');
							
							
						}


							// Quand une date d'echeance es modifiee (pour savoir que quelque chose a ete modifie)
						function onchange_date_echeance() {
							un_element_modifie = true;
							onkeyup_montant_echeance();
						}
						
						// Chaque fois que le montant d'une echeance est modifie (a la sortie du champ)
						//  Pour verifier que le montant est valide			
						function onchange_montant_echeance() {
							var montant_frais_total = document.formulaire.montant_frais_total.value;
							var echeances = document.formulaire.echeances_total.value;
							var montant_total_echeances = 0.0;
							var montant_echeance, i;
							var message_erreur = '';
							var separateur = '';
							var obj;
							var valide = true;
							
							un_element_modifie = true;
							
							//alert(echeances);
							if(echeances > 0) {
								// Verifier chaque echeance
								for (i=1; i<=echeances; i++) {
								
									// Recuperation du montant
									obj = document.getElementById('echeance_' + i + '_montant');
									
									//alert(montant_echeance);
									// On verifie que le montant est valide
									if(!est_nombre(obj.value, 'decimal', ',')) {
										//alert('ff');
										messsage = "<?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_INSC_013); ?>";
										messsage = messsage.replace('#i#', i);
										message_erreur += separateur + "     - " + messsage;
										separateur = "\n";
										if(valide) {
											obj.focus();
										}
										valide = false;
									}

								}
								if(!valide) {
									alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
								}
							}
						}
						
						// Quand un type de reglement (pour savoir que quelque chose a ete modifie)
						function onchange_type_reglement() {
							un_element_modifie = true;
						}
						
						// Click sur le bouton "Enregistrer"
						// Verifier la validite des dates et des montants
						// Verifier que le montant total des echeances correspond au total des frais
						function onclick_enregistrer() {
							var montant_frais_total = document.formulaire.montant_frais_total.value;
							var montant_normales_et_remises_total = document.formulaire.montant_normales_et_remises_total.value;
							var echeances = document.formulaire.echeances_total.value;
							var date_echeance, type_echeance, libelle;
							var date_courante = null;
							var date_precedente = null;
							var message_erreur = '';
							var separateur = '';
							var valide = true;
							var i;


							// On verifie que la date de depart est valide
							obj = document.getElementById('date_depart');
							if(trim(obj.value) != '') {
								if(!est_date(trim(obj.value), false)) {
									messsage = "<?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_INSC_024); ?>";
									message_erreur += separateur + "     - " + messsage;
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
							}
							
							if(echeances > 0) {
								// Verifier chaque echeance
								for (i=1; i<=echeances; i++) {
									date_echeance = document.getElementById('echeance_' + i + '_date').value;
									type_echeance = document.getElementById('echeance_' + i + '_type').value;
									lisse_echeance = document.getElementById('echeance_' + i + '_lisse').value;

									// On verifie que la date est valide
									if(!est_date(date_echeance, false)) {
										messsage = "<?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_INSC_012); ?>";
										messsage = messsage.replace('#i#', i);
										message_erreur += separateur + "     - " + messsage;
										separateur = "\n";
										valide = false;
									} else {
										
										// => date valide : verifier qu'elle est superieure (ou egale) a la precedente
										//                  (seulement a partir de la deuxieme
										// 20100709 - AP : maintenant on peut avoir deux dates egales
										
										// On verifie seulement pour les echeances lissees
										if(type_echeance == 0 && lisse_echeance == 1) {
										
											//alert('dp=' + date_precedente);
											if(date_precedente != null) {
												
												date_courante = Date.parse(date_fr_vers_us(date_echeance));
												//alert(date_courante + ' - ' +  date_precedente);
												if(date_courante >= date_precedente) {
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
									
									// Traitement specifique aux echeances additionnelles
									//alert(i);
									type_echeance = document.getElementById('echeance_' + i + '_type').value;
									//alert(type_echeance);
									if(type_echeance == 1) {
										// Verifier si le libelle est present
										obj = document.getElementById('echeance_' + i + '_libelle');
										if(trim(obj.value) == '') {
										
											messsage = "<?php echo LANG_FIN_ECHE_011; ?>";
											messsage = messsage.replace('#i#', i);
											message_erreur += separateur + "     - " + messsage;
											separateur = "\n";
											valide = false;
										}
									}
									
									

								}



								
							}

							// Si l'utilisateur veut ajouter un frais, verifier les donnees entrees
							if(ajouter_echeance) {

							}

							
							// Si l'utilisateur veut ajouter une echeance, verifier les donnees entrees
							if(ajouter_echeance) {

								// Verifier si la date est presente
								obj = document.getElementById('nouvelle_echeance_date_echeance');
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_ECHE_006); ?>";
									separateur = "\n";
									if(valide) {
										document.getElementById('anchor18div_nouvelle_echeance_date_echeance').onclick();
									}
									valide = false;
								} else {
									if(!est_date(obj.value, false)) {
										message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_ECHE_006); ?>";
										separateur = "\n";
										if(valide) {
											document.getElementById('anchor18div_nouvelle_echeance_date_echeance').onclick();
										}
										valide = false;
									}
								}


								// Verifier que le libelle n'est pas vide
								obj = document.getElementById('nouvelle_echeance_libelle');
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_ECHE_007); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
								
								// Verifier que le montant est un decimal valide
								obj = document.getElementById('nouvelle_echeance_montant');
								if(!est_nombre(obj.value, 'decimal_moins', ',')) {
									//alert('ff');
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_ECHE_008); ?>";
									separateur = "\n";
									valide = false;
								} else {
									// Verifier que le montant est superieur s 0
									/*
									montant_echeance_tmp = obj.value;
									montant_echeance_tmp = montant_echeance_tmp.replace(',', '.') * 1;
									if(montant_echeance_tmp <= 0.0) {
										//alert('ff');
										message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_007, LANG_FIN_ECHE_008); ?>";
										separateur = "\n";
										if(valide) {
											obj.focus();
										}

										valide = false;
									}
									*/
								}
							
							}

							if(valide) {
							
								// Verifier les totaux

								// Arrondir
								montant_frais_total = Math.round(montant_frais_total*100) / 100;
								montant_normales_et_remises_total = Math.round(montant_normales_et_remises_total*100) / 100;
								//alert(montant_frais_total + ' - ' + montant_normales_et_remises_total);
								// Verifier que le montant total des echeances correspond au total des frais
								if(montant_normales_et_remises_total != montant_frais_total) {
									messsage = "<?php echo LANG_FIN_INSC_015; ?>";
									messsage = messsage.replace('#s1#', montant_depuis_bdd(montant_normales_et_remises_total, 2, ',', ' '));
									messsage = messsage.replace('#s2#', montant_depuis_bdd(montant_frais_total, 2, ',', ' '));
									alert(messsage);
								}
							
							
								msg_util_attente_montrer(true);
								document.formulaire.operation.value = 'enregistrer';
								if(ajouter_frais) {
									document.formulaire.nouveau_frais.value = '1';
								}
								if(ajouter_echeance) {
									document.formulaire.nouvelle_echeance.value = '1';
								}

								document.formulaire.but_actualiser.click();
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}
								
						}

						// Pour ouvrir le popup d'edition des reglements d'une echeance
						function onclick_reglement_editer(echeancier_id, mode) {
							var continuer = true;
							if(un_element_modifie) {
								if(confirm("<?php echo LANG_FIN_INSC_021; ?>")) {
									msg_util_attente_montrer(true);
									document.formulaire.operation.value = 'enregistrer';
									document.formulaire.but_actualiser.click();
									continuer = false;
								}
							}
							if(continuer) {
								try {
									for(i=0; i<liste_fenetre.length; i++) {
										liste_fenetre[i].close();
									}
									
								}
								catch(e) {
								}
								liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/reglement_editer.php?echeancier_id=' + echeancier_id + '&mode=' + mode,'fenetre_editer_' + liste_fenetre.length,'width=850,height=600,resizable=yes,scrollbars=no');
								liste_fenetre[liste_fenetre.length].focus();
								document.formulaire.fenetre.value = liste_fenetre[liste_fenetre.length];
							}
						}

						// Pour actualiser la page courante
						function actualiser() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = "actualiser";
							document.formulaire.but_actualiser.click();
						}

						// Pour recalculer le montant des echeances normales apres avoir change les frais
						function onclick_recalculer_echeances() {
							if(confirm("<?php echo sprintf(LANG_FIN_INSC_023, $echeances_sans_reglement); ?>")) {
								msg_util_attente_montrer(true);
								document.formulaire.operation.value = "recalculer_echeances";
								document.formulaire.but_actualiser.click();
							}
						}
						
						// Pour afficher ou cacher les infos supplementaires d'une echeance
						function onclick_echeance_plus_infos(echeancier_id) {
							var infos = document.getElementById('echeance_' + echeancier_id + '_infos_tr');
							var lien = document.getElementById('echeance_' + echeancier_id + '_infos_lien');
							var texte_lien = document.getElementById('echeance_' + echeancier_id + '_infos_texte_lien');
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

						function onclick_voir_cacher_liste_frais() {
							obj_liste_frais_tr = document.getElementById('tr_liste_frais');
							obj_voir_cacher_liste_frais_texte = document.getElementById('voir_cacher_liste_frais_texte');
							if(obj_liste_frais_tr.style.display == 'none') {
								obj_liste_frais_tr.style.display = '';
								obj_voir_cacher_liste_frais_texte.innerHTML = "[-] <?php echo LANG_FIN_INSC_027; ?>";
							} else {
								obj_liste_frais_tr.style.display = 'none';
								obj_voir_cacher_liste_frais_texte.innerHTML = "[+] <?php echo LANG_FIN_INSC_026; ?>";
							}
						}

						// Ouvrir le popup pour editer le RIB
						function onclick_editer_rib(elev_id) {
							for(i=0; i<liste_fenetre.length; i++) {
								try {
									liste_fenetre[i].close();
								}
								catch(e) {
								}
							}
							liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE) . FIN_REP_MODULE; ?>/rib_editer.php?elev_id=' + elev_id + '&actualiser_parent=1','editer_rib_' + elev_id,'width=550,height=400');
						}

						// Actualiser le formulaire
						function actualiser() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = "enregistrer";
							document.formulaire.but_actualiser.click();
						}


						// Quand l'utilisateur selectionne une action a faire sur un frais
						function onchange_actions_frais() {
							obj_actions_echeance = document.getElementById('actions_frais');
							switch(obj_actions_echeance.options[obj_actions_echeance.selectedIndex].value) {
								case 'rien':
									break;
								case 'supprimer':
									supprimer_frais();
									break;
							}
							obj_actions_echeance.selectedIndex = 0;
						}

						// Quand l'utilisateur veut supprimer un frais
						function supprimer_frais() {
							var obj_checkbox  = document.formulaire.frais_inscription_id;
							var frais_inscription_id = radio_lire_valeur(obj_checkbox);
						
							if(confirm("<?php echo LANG_FIN_FBAR_010; ?>")) {
								msg_util_attente_montrer(true);
								document.formulaire.operation.value = "frais_supprimer";
								document.formulaire.id_operation.value = frais_inscription_id;
								document.formulaire.but_actualiser.click();
							}

						}



						// Quand l'utilisateur selectionne une action a faire sur une echeance
						function onchange_actions_echeance() {
							obj_actions_echeance = document.getElementById('actions_echeance');
							switch(obj_actions_echeance.options[obj_actions_echeance.selectedIndex].value) {
								case 'rien':
									break;
								case 'supprimer':
									supprimer_echeance();
									break;
								case 'diviser':
									diviser_echeance();
									break;
								case 'fusionner':
									fusionner_echeance();
									break;
							}
							obj_actions_echeance.selectedIndex = 0;
						}
						
						// Quand l'utilisateur veut supprimer une echeance
						function supprimer_echeance() {
							var echeancier_id = '';
							var nombre_selectionnes = 0;
							var obj_checkbox, obj_checkbox_id;
							var obj_nombre_de_checkbox = document.getElementById('nombre_de_checkbox');
							var separateur = '';
							
							// Verifier chaque checkbox selectionnee
							separateur = '';
							for(i=1; i<=obj_nombre_de_checkbox.value; i++) {
								obj_checkbox = document.getElementById('echeance_' + i + '_checkbox');
								// Si la checkbox est selectionnee
								if(obj_checkbox.checked) {
									nombre_selectionnes++;
									obj_checkbox_id = document.getElementById('echeance_' + i + '_checkbox_id');
									echeancier_id += separateur + obj_checkbox_id.value;
									separateur = ',';
								}
							}
							
							// On ne doit avoir qu'une seule echeance selectionnee
							// 20100709 - AP : Maintenant on peut selectionner plusieurs echeances
							//if(nombre_selectionnes == 1) {
								if(confirm("<?php echo LANG_FIN_ECHE_023; ?>")) {
									msg_util_attente_montrer(true);
									document.formulaire.operation.value = "echeance_supprimer";
									document.formulaire.id_operation.value = echeancier_id;
									document.formulaire.but_actualiser.click();
								}
							//} else {
							//	alert("<?php echo LANG_FIN_ECHE_022; ?>");
							//}
						}
						
						// Quand l'utilisateur veut diviser une echeance
						function diviser_echeance() {
							var echeancier_id = '';
							var nombre_selectionnes = 0;
							var obj_checkbox, obj_checkbox_id;
							var obj_nombre_de_checkbox = document.getElementById('nombre_de_checkbox');
							var separateur = '';
							
							// Verifier chaque checkbox selectionnee
							separateur = '';
							for(i=1; i<=obj_nombre_de_checkbox.value; i++) {
								obj_checkbox = document.getElementById('echeance_' + i + '_checkbox');
								// Si la checkbox est selectionnee
								if(obj_checkbox.checked) {
									nombre_selectionnes++;
									obj_checkbox_id = document.getElementById('echeance_' + i + '_checkbox_id');
									echeancier_id += separateur + obj_checkbox_id.value;
									separateur = ',';
								}
							}
							
							// On ne doit avoir qu'une seule echeance selectionnee
							// 20100709 - AP : Maintenant on peut selectionner plusieurs echeances
							//if(nombre_selectionnes == 1) {
								if(confirm("<?php echo LANG_FIN_ECHE_024; ?>")) {
									msg_util_attente_montrer(true);
									document.formulaire.operation.value = "echeance_diviser";
									document.formulaire.id_operation.value = echeancier_id;
									document.formulaire.but_actualiser.click();
								}
							//} else {
							//	alert("<?php echo LANG_FIN_ECHE_022; ?>");
							//}
						}						

						// Quand l'utilisateur veut fusionner deux echeance
						function fusionner_echeance() {
							var echeancier_id = 0;
							var nombre_selectionnes = 0;
							var obj_checkbox, obj_checkbox_id;
							var obj_nombre_de_checkbox = document.getElementById('nombre_de_checkbox');
							var separateur = '';
							
							// Verifier chaque checkbox selectionnee
							echeancier_id = '';
							for(i=1; i<=obj_nombre_de_checkbox.value; i++) {
								obj_checkbox = document.getElementById('echeance_' + i + '_checkbox');
								// Si la checkbox est selectionnee
								if(obj_checkbox.checked) {
									nombre_selectionnes++;
									obj_checkbox_id = document.getElementById('echeance_' + i + '_checkbox_id');
									//alert(obj_checkbox_id);
									//nombre_selectionnes = 7;
									echeancier_id += separateur + obj_checkbox_id.value;
									separateur = ',';
								}
							}
							
							// On ne doit avoir qu'une seule echeance selectionnee
							if(nombre_selectionnes == 2) {
								if(confirm("<?php echo LANG_FIN_ECHE_026; ?>")) {
									msg_util_attente_montrer(true);
									document.formulaire.operation.value = "echeance_fusionner";
									document.formulaire.id_operation.value = echeancier_id;
									document.formulaire.but_actualiser.click();
								}
							} else {
								alert("<?php echo LANG_FIN_ECHE_025; ?>");
							}
						}
						
						function onclick_supprimer() {
							if(confirm("<?php echo LANG_FIN_INSC_031; ?>")) {
								document.formulaire.operation.value = 'supprimer_inscription_sans_reglements';
								document.formulaire.but_actualiser.click();
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
							
							//alert ('Valeur : ' + i);
							document.getElementById('echeance_'+i+'_montant').value = montant_total1;
						}
						
						
						
						function Change_montant1(nb)
						{
							montant_total1 = 0.0;
							montant_detail = 0.0;
							for(j=0;j< nb;j++)
							{
								montant_detail = document.getElementById('montant_groupe_echeancier_'+ j).value;	
								montant_total1 = montant_total1 + (montant_detail.replace(',', '.') * 1);
							}
							montant_total1 = montant_total1.toFixed(2);
							montant_total1 = montant_total1.replace('.', ',');
							
							//alert ('Valeur : ' + i);
							document.getElementById('nouvelle_echeance_montant').value = montant_total1;
						}
						
						
					</script>


					<?php 
					
					//********** LE SCRIPT DE RETOUR ET LES PARAMETRES DEPENDENT DE L'APPELANT **********
					
					switch($appelant) {
						case 'impayes_liste':
					?>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>impayes_liste.php" method="post">
					</form>
					<?php
						break;
						case 'cautions_non_remboursees_liste':
					?>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>cautions_non_remboursees_liste.php" method="post">
					</form>
					<?php
						break;
						case 'edit_eleve':
					?>
					<form name="formulaire_annuler" id="formulaire_annuler" action="edit_eleve.php" method="get">
						<input type="hidden" name="eid" id="eid" value="<?php echo $elev_id; ?>">
					</form>
					<?php
						default:
					?>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>inscription_rechercher.php" method="post">
						<input type="hidden" name="operation" id="operation" value="<?php echo $operation_rech; ?>">
						<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class_rech; ?>">
						<input type="hidden" name="nom_eleve" id="nom_eleve" value="<?php echo $nom_eleve_rech; ?>">
						<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire_rech; ?>">
					<?php
					}
					?>
					
					
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
				
				calcul_frais_lisses_non_lisses();
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
