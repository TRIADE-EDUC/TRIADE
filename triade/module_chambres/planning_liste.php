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

	$position_du_jour = date('N', time());
	$date_debut_semaine_courante = date('Y-m-d', mktime(0, 0, 0, date('m', time()), date('d', time()) - ($position_du_jour - 1), date('Y', time())));


	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation = lire_parametre('operation', '', 'POST');
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	
	$date_debut = $_SESSION[CHA_REP_MODULE]['planning_liste']['date_debut'];
	//if($date_debut == '') {
		$date_debut_formulaire = lire_parametre('date_debut', '', 'POST');
		if($operation == 'rechercher' && $date_debut_formulaire == '') {
			$date_debut = $date_debut_semaine_courante;
		} else {
			if($date_debut_formulaire != '') {
				$date_debut = date_vers_bdd($date_debut_formulaire);
			} else {
				if($date_debut == '') {
					$date_debut = $date_debut_semaine_courante;
				}
			}
		}
	//}
	

	/*
	if(false && $operation == 'rechercher') {
		$date_debut_formulaire = lire_parametre('date_debut', '-1', 'POST');
		if($date_debut_formulaire != '-1') {
			$date_debut = date_vers_bdd($date_debut_formulaire);
			//echo $date_debut;
			$position_du_jour = date('N', strtotime($date_debut));
			$date_debut = date('Y-m-d', mktime(0, 0, 0, date('m', strtotime($date_debut)), date('d', strtotime($date_debut)) - ($position_du_jour - 1), date('Y', strtotime($date_debut))));
			//echo $date_debut;
		} else {
			$date_debut = $date_debut_semaine_courante;
		}
	}
	*/
	
	$batiment_id = $_SESSION[CHA_REP_MODULE]['planning_liste']['batiment_id'];
	$batiment_id_formulaire = lire_parametre('batiment_id', -1, 'POST');
	//echo $batiment_id_formulaire;
	if($batiment_id_formulaire != -1) {
		$batiment_id = $batiment_id_formulaire;
	}
	
	$etage_id = $_SESSION[CHA_REP_MODULE]['planning_liste']['etage_id'];
	$etage_id_formulaire = lire_parametre('etage_id', -1, 'POST');
	//echo $etage_id_formulaire;
	if($etage_id_formulaire != -1) {
		$etage_id = $etage_id_formulaire;
	}

	$occupation_chambre = $_SESSION[CHA_REP_MODULE]['planning_liste']['occupation_chambre'];
	$occupation_chambre_formulaire = lire_parametre('occupation_chambre', -1, 'POST');
	//echo $occupation_chambre_formulaire;
	if($occupation_chambre_formulaire != -1) {
		$occupation_chambre = $occupation_chambre_formulaire;
	}

	$type_id = $_SESSION[CHA_REP_MODULE]['planning_liste']['type'];
	$type_id_formulaire = lire_parametre('type_id', -1, 'POST');

	if($type_id_formulaire != -1) {
		$type_id = $type_id_formulaire;
	}
	
	$id = lire_parametre('id', 0, 'POST');
	//***************************************************************************


	$_SESSION[CHA_REP_MODULE]['planning_liste']['batiment_id'] = $batiment_id;
	$_SESSION[CHA_REP_MODULE]['planning_liste']['etage_id'] = $etage_id;
	$_SESSION[CHA_REP_MODULE]['planning_liste']['date_debut'] = $date_debut;
	$_SESSION[CHA_REP_MODULE]['planning_liste']['occupation_chambre'] = $occupation_chambre;
	$_SESSION[CHA_REP_MODULE]['planning_liste']['type'] = $type_id;

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	if($operation == "semaine_precedente") {
		$date_debut = date('Y-m-d', mktime(0,0,0,date('m',strtotime($date_debut)),date('d',strtotime($date_debut)) - 14,date('Y',strtotime($date_debut)))); 
		$_SESSION[CHA_REP_MODULE]['planning_liste']['date_debut'] = $date_debut;
	}
	if($operation == "semaine_suivante") {
		$date_debut = date('Y-m-d', mktime(0,0,0,date('m',strtotime($date_debut)),date('d',strtotime($date_debut)) + 14,date('Y',strtotime($date_debut)))); 
		$_SESSION[CHA_REP_MODULE]['planning_liste']['date_debut'] = $date_debut;
	}
	if($operation == "supprimer_reservation") {
		$sql= "DELETE FROM ".CHA_TAB_RESERVATION." WHERE reservation_id = " . $id;
		$res_ope=execSql($sql);
		// Verifier si la requete sql a reussi ou non
		if(is_object($res_ope) || (is_numeric($res_ope) && $res_ope > 0))
        {
			msg_util_ajout(LANG_CHA_GENE_007);
		} else {
			msg_util_ajout(LANG_CHA_GENE_057, 'erreur');
			// Oblige de refaire une connexion a la bdd car il y a un bug
			// Des qu'une requete plante, toutes les suivante (meme valides) sur la meme connexion vont echouer !
			$cnx=cnx();
		}
	}
	
	//***************************************************************************

	// Generer les timestamp des jours de la semaine
	$jours_de_la_semaine = array();
	$jours_de_la_semaine[1] = strtotime($date_debut);
	$jours_de_la_semaine[2] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 1,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[3] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 2,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[4] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 3,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[5] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 4,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[6] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 5,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[7] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 6,date('Y',$jours_de_la_semaine[1])); 
/*	$jours_de_la_semaine[8] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 7,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[9] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 8,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[10] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 9,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[11] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 10,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[12] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 11,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[13] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 12,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[14] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 13,date('Y',$jours_de_la_semaine[1])); 
	 */	
	// Rechercher la liste des chambres
	$sql ="SELECT b.libelle as libelle_batiment, c.numero, c.libelle as libelle_chambre, c.chambre_id, c.type_chambre_id, c.etage_id, tc.nombre_lits ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." c INNER JOIN " . CHA_TAB_BATIMENT . " b ON c.batiment_id = b.batiment_id ";
	$sql.="INNER JOIN ".CHA_TAB_TYPE_CHAMBRE." tc ON c.type_chambre_id = tc.type_chambre_id ";
	$sql.="WHERE 1=1 ";
	if($batiment_id != 0) {
		$sql.="AND b.batiment_id = " . $batiment_id . ' ';
	}
	if($etage_id	 != 0) {
		$sql.="AND c.etage_id = " . $etage_id . ' ';
	}
	if($type_id	 != 0) {
		$sql.="AND c.type_chambre_id = " . $type_id . ' ';
	}
	$sql.="ORDER BY b.libelle, c.numero, c.libelle ";
	//echo $sql;
	$res=execSql($sql);
	
	$chambres = array();
	if($res->numRows() > 0) {
		for($i=0; $i<$res->numRows(); $i++) {
			$ligne = $res->fetchRow();
			
			$nombre_lits = $ligne[6];
			
			// Verifier si la chambre est valide para rapport au critere $occupation_chambre
			$chambre_a_traiter = true;
			switch($occupation_chambre) {
				case "1": // Toutes
					$chambre_a_traiter = true;
					break;
				case "2": // Libres
					$chambre_a_traiter = false;
					for($j=1; $j<=count($jours_de_la_semaine); $j++) {
						// Rechercher les reservation pour cette chambre et ce jour
						$sql ="SELECT reservation_id, elev_id, date_debut, date_fin, chambre_id, date_reservation ";
						$sql.="FROM ".CHA_TAB_RESERVATION." ";
						$sql.="WHERE ";
						$sql.="'" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' >= date_debut AND '" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' <= date_fin ";
						$sql.="AND chambre_id = " . $ligne[3] . " ";
						$sql.="ORDER BY reservation_id";
						//echo $sql;
						$res_resas=execSql($sql);
						//echo $res_resas->numRows() . ' - ' . $nombre_lits . '<br>';
						
						// On verifie si il y a au moins une reservation pour cette cellule
						if($res_resas->numRows() == 0) {
							$chambre_a_traiter = true;
							break;
						}				
					}
					break;
				case "3": // Partiellement libres
					$chambre_a_traiter = false;
					for($j=1; $j<=count($jours_de_la_semaine); $j++) {
						// Rechercher les reservation pour cette chambre et ce jour
						$sql ="SELECT reservation_id, elev_id, date_debut, date_fin, chambre_id, date_reservation ";
						$sql.="FROM ".CHA_TAB_RESERVATION." ";
						$sql.="WHERE ";
						$sql.="'" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' >= date_debut AND '" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' <= date_fin ";
						$sql.="AND chambre_id = " . $ligne[3] . " ";
						$sql.="ORDER BY reservation_id";
						//echo $sql;
						$res_resas=execSql($sql);
						
						// On verifie si il y a au moins une reservation et moins que le nombre de lits
						if($res_resas->numRows() > 0 && $res_resas->numRows() < $nombre_lits) {
							$chambre_a_traiter = true;
							break;
						}				
					}
					break;
				case "4": // Occupees
					$chambre_a_traiter = false;
					for($j=1; $j<=count($jours_de_la_semaine); $j++) {
						// Rechercher les reservation pour cette chambre et ce jour
						$sql ="SELECT reservation_id, elev_id, date_debut, date_fin, chambre_id, date_reservation ";
						$sql.="FROM ".CHA_TAB_RESERVATION." ";
						$sql.="WHERE ";
						$sql.="'" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' >= date_debut AND '" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' <= date_fin ";
						$sql.="AND chambre_id = " . $ligne[3] . " ";
						$sql.="ORDER BY reservation_id";
						//echo $sql;
						$res_resas=execSql($sql);
						
						// On verifie si il y a moins de reservations que le nombre de lits
						if($res_resas->numRows() >= $nombre_lits) {
							$chambre_a_traiter = true;
							break;
						}				
					}
					break;
			}
			
			if($chambre_a_traiter) {
				$separateur1 = ' - ';
				$separateur2 = '';
				$texte = '';
	
				eval('$libelle = LANG_CHA_ETAG_ID_' . $ligne[5] .'_LIBELLE;');
				if($libelle != '') {
					$texte .= $libelle;
					$separateur1 = ' - ';
				}
				eval('$exposant = LANG_CHA_ETAG_ID_' . $ligne[5] .'_EXPOSANT;');
				if(trim($exposant) != '') {
					$texte .= ' <sup>' . $exposant . '</sup>';
				}
				
				$texte .= $separateur1 . $ligne[0];
				
				
				if($ligne[1] != '') {
					$texte .= $separateur1 . 'n°' . $ligne[1];
					$separateur2 = ' - ';
				}
				
				eval('$texte .= $separateur2 . LANG_CHA_TCHA_ID_' . $ligne[4] .';');
				$separateur2 = ' - ';
				
				if($ligne[2] != '') {
					$separateur2 = ' - ';
					$texte .= $separateur2 . $ligne[2];
				}
				
				$chambres[count($chambres)] = array(
					"id_ligne" => $ligne[3],
					"libelle" => $texte,
					"nombre_lits" => $nombre_lits
				);
			}
		}
	}

	

	$reservations = array();
	$liste_reservations_par_cellule = array();

	// On va parcourir la matrice  chambres/jours pour en extraire les plages reservees
	for($i=0; $i<count($chambres); $i++) {
		for($j=1; $j<=count($jours_de_la_semaine); $j++) {
			// Rechercher les reservation pour cette chambre et ce jour
			// (il peut y en avoir plusieurs dans le cas des chambres doubles, triples, ...)
			$sql ="SELECT reservation_id, elev_id, date_debut, date_fin, chambre_id, date_reservation ";
			$sql.="FROM ".CHA_TAB_RESERVATION." ";
			$sql.="WHERE ";
			$sql.="'" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' >= date_debut AND '" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' <= date_fin ";
			$sql.="AND chambre_id = " . $chambres[$i]['id_ligne'] . " ";
			$sql.="ORDER BY reservation_id";
			//echo $sql;
			$res=execSql($sql);
			
			// On verifie si il y a au moins une reservation pour cette cellule
			if($res->numRows() > 0) {
				
				// Verifier si il y a une seule reservation ou plusieurs
				if($res->numRows() == 1) {
				
					// Acces aus donnees de la reservation
					$ligne = $res->fetchRow();
				
					$infos_resa_html = '';
					
					$infos_resa_html .= LANG_CHA_RESA_017 . ' : n°' . $ligne[0] . '<br>';
		
					// Rechercher l'eleve qui a deja une reservation
					$sql ="SELECT prenom, nom ";
					$sql.="FROM ".CHA_TAB_ELEVES." ";
					$sql.="WHERE elev_id = " . $ligne[1] . " ";
					$res_eleve=execSql($sql);
					if($res_eleve->numRows() > 0) {
						$ligne_eleve = $res_eleve->fetchRow();	
						$infos_resa_html .= LANG_CHA_RESA_009 . ' : ' . $ligne_eleve[0] . ' ' . $ligne_eleve[1] . '<br>';
					}		
					

					$infos_resa_html .= LANG_CHA_RESA_007 . ' : ' . date_depuis_bdd($ligne[2]) . '<br>';
					$infos_resa_html .= LANG_CHA_RESA_008 . ' : ' . date_depuis_bdd($ligne[3]) . '<br>';
					$infos_resa_html .= LANG_CHA_RESA_018 . ' : ' . date_depuis_bdd($ligne[5]) . '<br>';
					
					$infos_resa_html = str_replace("'", "\'", $infos_resa_html);
					
					$cellule_html = '';
					$cellule_html .= '<table cellspacing="0" cellpadding="0" border="0">';
					$cellule_html .= '	<tr>';									
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '<a href="javascript:;"  onMouseOver="AffBulle3(\'' .  LANG_CHA_PLAN_005 . '\',\'./image/commun/affichage.gif\',\'' . $infos_resa_html . '\', \'\');"  onMouseOut="HideBulle();"><img src="./image/commun/affichage.gif" border="0" align="middle" style="display: block;"></a>';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			&nbsp;';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			<a href="javascript:;" onclick="onclick_modifier_reservation(' . $ligne[0] . ');" title="' . LANG_CHA_RESA_013 . '"><img src="image/commun/b_edit.png" border="0" alt="' . LANG_CHA_RESA_013 . '"></a>';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			&nbsp;';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			<a href="javascript:;" onclick="onclick_supprimer_reservation(' . $ligne[0] . ');" title="' . LANG_CHA_RESA_016 . '"><img src="image/commun/b_drop.png" border="0" alt="' . LANG_CHA_RESA_016 . '"></a>';
					$cellule_html .= '		</td>';		
					$cellule_html .= '	</tr>';									
					$cellule_html .= '</table>';					
				} else {
					// => quand il y a plusieurs reservations en meme temps
				
					$pos_reservations_par_cellule = count($liste_reservations_par_cellule);
					$liste_reservations_par_cellule[$pos_reservations_par_cellule] = array();
					
					$infos_resa_html = '';
					$decalage_a_droite = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					for($k=0; $k<$res->numRows(); $k++) {
						$ligne = $res->fetchRow();
						
						$infos_resa_html .= LANG_CHA_RESA_017 . ' : n°' . $ligne[0] . '<br>';
						
						// Rechercher l'eleve qui a deja une reservation
						$nom_eleve = '';
						$sql ="SELECT prenom, nom ";
						$sql.="FROM ".CHA_TAB_ELEVES." ";
						$sql.="WHERE elev_id = " . $ligne[1] . " ";
						$res_eleve=execSql($sql);
						if($res_eleve->numRows() > 0) {
							$ligne_eleve = $res_eleve->fetchRow();	
							$nom_eleve = $ligne_eleve[0] . ' ' . $ligne_eleve[1];
							$infos_resa_html .= $decalage_a_droite . LANG_CHA_RESA_009 . ' : ' . $nom_eleve . '<br>';
							//$liste_eleves .= $separateur_listes_espaces . $nom_eleve;
						}	

						$infos_resa_html .= $decalage_a_droite . LANG_CHA_RESA_007 . ' : ' . date_depuis_bdd($ligne[2]) . '<br>';
						$infos_resa_html .= $decalage_a_droite . LANG_CHA_RESA_008 . ' : ' . date_depuis_bdd($ligne[3]) . '<br>';
						$infos_resa_html .= $decalage_a_droite . LANG_CHA_RESA_018 . ' : ' . date_depuis_bdd($ligne[5]) . '<br>';
						
						$liste_reservations_par_cellule[$pos_reservations_par_cellule][count($liste_reservations_par_cellule[$pos_reservations_par_cellule])] = array(
									"reservation_id" => $ligne[0],
									"elev_id" => $ligne[1],
									"nom_eleve" => $nom_eleve,
									"date_debut" => date_depuis_bdd($ligne[2]),
									"date_fin" => date_depuis_bdd($ligne[3])
									);
					}
											
					$infos_resa_html = str_replace("'", "\'", $infos_resa_html);
					
					$cellule_html = '';
					$cellule_html .= '<table cellspacing="0" cellpadding="0" border="0">';
					$cellule_html .= '	<tr>';									
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '<a href="javascript:;"  onMouseOver="AffBulle3(\'' .  LANG_CHA_PLAN_005 . '\',\'./image/commun/affichage.gif\',\'' . $infos_resa_html . '\', \'\');"  onMouseOut="HideBulle();"><img src="./image/commun/affichage.gif" border="0" align="middle" style="display: block;"></a>';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			&nbsp;';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';		
					$cellule_html .= '			<a href="javascript:;" id="icon_modifier_' . $pos_reservations_par_cellule . '" onclick="onclick_modifier_reservation_liste(\'icon_modifier_' . $pos_reservations_par_cellule . '\',' . $pos_reservations_par_cellule . ');" title="' . LANG_CHA_RESA_013 . '"><img src="image/commun/b_edit.png" border="0" alt="' . LANG_CHA_RESA_013 . '"></a>';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			&nbsp;';
					$cellule_html .= '		</td>';		
					$cellule_html .= '		<td align="left" valign="middle">';									
					$cellule_html .= '			<a href="javascript:;" id="icon_supprimer_' . $pos_reservations_par_cellule . '" onclick="onclick_supprimer_reservation_liste(\'icon_modifier_' . $pos_reservations_par_cellule . '\',' . $pos_reservations_par_cellule . ');" title="' . LANG_CHA_RESA_016 . '"><img src="image/commun/b_drop.png" border="0" alt="' . LANG_CHA_RESA_016 . '"></a>';
					$cellule_html .= '		</td>';		
					$cellule_html .= '	</tr>';									
					$cellule_html .= '</table>';				
				}
				
				
				
				$classe_cellule = '';
				if($res->numRows() < $chambres[$i]['nombre_lits']) {
					$classe_cellule = 'jours_actif_orange';
				} else {
					$classe_cellule = 'jours_actif_rouge';
				}
				
				$reservations[count($reservations)] = array(
					"id" => $ligne[0],
					"id_ligne" => $chambres[$i]['id_ligne'],
					"libelle" => $cellule_html,
					"onlick_cellule" => '',
					"date_debut" => date('Y-m-d', $jours_de_la_semaine[$j]),
					"date_fin" => date('Y-m-d', $jours_de_la_semaine[$j]),
					"classe_cellule" => $classe_cellule
				);
			}		
		}	
	}

	//echo "<pre>";
	//print_r($reservations);
	//echo "</pre>";
	//echo "<pre>";
	//print_r($chambres);
	//echo "</pre>";

	//echo "<pre>";
	//print_r($liste_reservations_par_cellule);
	//echo "</pre>";	


	// Rechercher la liste des batiments disponibles
	$sql ="SELECT batiment_id, libelle, adresse_1, adresse_2, adresse_3, code_postal, ville ";
	$sql.="FROM ".CHA_TAB_BATIMENT." ";
	$sql.="ORDER BY libelle";
	$res_batiments=execSql($sql);

	$sql ="SELECT type_chambre_id , libelle ";
	$sql.="FROM ".CHA_TAB_TYPE_CHAMBRE." ";
	$sql.="ORDER BY type_chambre_id";
	$res_type=execSql($sql);
	
	
	include('./' . CHA_REP_MODULE . "/librairie_php/lib_planning.inc.php");
	$planning = new planning();
	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	//***************************************************************************
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . CHA_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>
<html>
	<head>
		<meta http-equiv="CacheControl" content = "no-cache">
		<meta http-equiv="pragma" content = "no-cache">
		<meta http-equiv="expires" content = -1>
		<meta name="Copyright" content="Triade©, 2001">
		<base href="<?php echo site_url_racine(CHA_REP_MODULE); ?>">
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
		<?php
		$largeur_cadre = 900;
		$alignement_cadre = 'center';
		$date_heure_impression = '(' . date('d/m/Y H:i:s') . ')';
		$disabled_cadre = 'disabled';
		$bordure_tableau_impession = '1';
		if($mode_affichage != 'impression') {
			$largeur_cadre = 468;
			$alignement_cadre = '';
			$date_heure_impression = '';
			$disabled_cadre = '';
			$bordure_tableau_impession = '0';
		}

		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		
		<!-- TITRE ET CADRE CENTRAL -->
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_CHA_PLAN_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
				
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td align="center">
<br><br>
								<?php
								// Pour la gestion des calendriers
								include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");

								//*******************  CRITERES DE RECHERCHE *********************
								
								?>
								<form name="formulaire_criteres" id="formulaire_criteres" action="" method="post">
									<input type="hidden" name="operation" id="operation" value="rechercher">
									<fieldset id="fieldset_criteres" style="z-index:5; margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_CHA_GENE_026; ?></legend>
										<table border="0" cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_007; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td align="left">
																<input type="text" name="date_debut" id="date_debut" size="10" maxlength="10" value="<?php echo date_depuis_bdd($date_debut); ?>" readonly="">
															</td>
															<td>&nbsp;</td>
															<td><a href="javascript:;" onClick="effacer_date_debut();" title="<?php echo LANG_CHA_GENE_059; ?>"><img src="image/commun/b_drop.png" border="0" alt="<?php echo LANG_CHA_GENE_059; ?>"></a></td>
															<td>&nbsp;</td>
															<td align="left">
																<?php
																calendarDim("div_date_debut","document.formulaire_criteres.date_debut",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
																?>
															</td>
															<td align="left">
																<small>(<?php echo LANG_CHA_PLAN_006; ?>)</small>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_OCCU_001; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_occupation_chambre">
													<select name="occupation_chambre" id="occupation_chambre" onChange="">
														<?php
															$selected = '';
															if($occupation_chambre == "1") {
																$selected = 'selected';
															}
														?>
														<option value="1" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_OCCU_ID_1_LIBELLE); ?></option>
														<?php
															$selected = '';
															if($occupation_chambre == "2") {
																$selected = 'selected';
															}
														?>
														<option value="2" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_OCCU_ID_2_LIBELLE); ?></option>
														<?php
															$selected = '';
															if($occupation_chambre == "3") {
																$selected = 'selected';
															}
														?>
														<option value="3" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_OCCU_ID_3_LIBELLE); ?></option>
														<?php
															$selected = '';
															if($occupation_chambre == "4") {
																$selected = 'selected';
															}
														?>
														<option value="4" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_OCCU_ID_4_LIBELLE); ?></option>
													</select>
												</td>
											</tr>	
											<tr>
												<td align="right"><?php echo LANG_CHA_TCHA_001; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="type_chambre_id">
													<select name="type_id" id="type_id">
													<?php
															$selected = '';
															if($une_option[0] == $batiment_id) {
																$selected = 'selected';
															}
														?>
														<option value="0" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_GENE_025); ?></option>
														<?php
														for($i=0; $i<$res_type->numRows(); $i++) {
															$une_option = $res_type->fetchRow();
															$selected = '';
															if($une_option[0] == $type_id) {
																$selected = 'selected';
															}
															$classe = '';
															$texte = $une_option[1];
			
													?>
														<option value="<?php echo $une_option[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo $texte; ?></option>
														<?php }?>
													</select>
												</td>
											</tr>	
											<tr>
												<td align="center" colspan="3">&nbsp;</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_005; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_batiment_id">
													<select name="batiment_id" id="batiment_id" onChange="recuperer_liste_etages()">
														<?php
															$selected = '';
															if($une_option[0] == $batiment_id) {
																$selected = 'selected';
															}
														?>
														<option value="0" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_GENE_025); ?></option>
													<?php
														for($i=0; $i<$res_batiments->numRows(); $i++) {
															$une_option = $res_batiments->fetchRow();
															$selected = '';
															if($une_option[0] == $batiment_id) {
																$selected = 'selected';
															}
															
															$classe = '';
															
															$texte = $une_option[1] . ' - ' . $une_option[5] . ' - ' . $une_option[6];
		
													?>
														<option value="<?php echo $une_option[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo $texte; ?></option>
													<?php
														}
													?>
													</select>
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_ETAG_001; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_etage_id">
													<select name="etage_id" id="etage_id" onChange="">
														<?php
															$selected = '';
															if($une_option[0] == $etage_id) {
																$selected = 'selected';
															}
														?>
														<option value="0" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_GENE_025); ?></option>
													</select>
												</td>
											</tr>	
											<tr>
												<td align="center" colspan="3">&nbsp;</td>
											</tr>
											<tr>
												<?php 
												if($mode_affichage != 'impression') {?>
												<td align="center" colspan="3">
													<table border="0" cellpadding="0" cellspacing="0" align="center">
														<tr>
															<td align="center" valign="top">
																<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_020?>","rechercher()");</script>
															</td>
															<td align="center" valign="top">
																<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_003?>","onclick_annuler()");</script>
															</td>
														</tr>
													</table>
												</td>
												<?php 
												} ?>
											</tr>
										</table>
									</fieldset>
								</form>	
							</td>
						</tr>
						<tr>
							<td align="center">		
				
				
								<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
									<input type="hidden" name="operation" id="operation" value="">
									<input type="hidden" name="id" id="id" value="0">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
								
										<?php //********** AFFICHAGE DES DONNEES ********** ?>
										
			
					
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
											
												<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
													<tr>
														<td>
															<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
																<tr>
																	<?php
																	if($mode_affichage != 'impression') {
																	?>
																	<td align="left" width="33%"><a href="javascript:;" onClick="aller_semaine_precedente()"><< <?php echo LANG_CHA_PLAN_003; ?></a></td>
																	<?php } ?>
																	<td align="center" width="34%"><b><?php echo sprintf(LANG_CHA_PLAN_002, date('d/m/Y', $jours_de_la_semaine[1]), date('d/m/Y', $jours_de_la_semaine[14])); ?></b></td>
																	<?php
																	if($mode_affichage != 'impression') {
																	?>
																	<td align="right" width="33%"><a href="javascript:;" onClick="aller_semaine_suivante()"><?php echo LANG_CHA_PLAN_004; ?> >></a></td>
																	<?php } ?>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
													</tr>
													<tr>
														<td>
															<?php
															$planning->afficher($date_debut, $chambres, $reservations);
															?>
														</td>
													</tr>
												</table>								
											
											</td>
										</tr>
										<tr>
											<td align="center">&nbsp;</td>
										</tr>
										<tr>
											<td align="center">
												<hr>
											</td>
										</tr>
										<tr>
											<td align="center">
												<table border="0" cellpadding="0" cellspacing="0" align="center">
													<tr>
														<td align="center" colspan="2"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
													</tr>
													<?php
													if($mode_affichage != 'impression') {
													?>
													<tr>
														<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_070?>","onclic_imprimer()");</script>
														</td>
														<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="30" height="1"></td>
														<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_069?>","onclic_export_excel()");</script>
														</td>
													</tr>
													<?php
													} else {
													?>
													<tr>
														<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_070?>","onclick_ouvrir_impession()");</script>
														</td>
														<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="30" height="1"></td>
														<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_003?>","onclick_fermer()");</script>
														</td>
													</tr>
													<?php
													}
													?>	
													<tr>
														<td align="center" colspan="2"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
													</tr>
												</table>
											</td>
										</tr>								
											
									</table>
									
								</form>
								
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
										<td align="center" nowrap="nowrap"><b><?php echo ucfirst(LANG_CHA_GENE_046); ?></b></td>
									</tr> 
									<tr class="tabnormal2">
										<td align="left">
											<table cellspacing="0" cellpadding="3" border="0" align="left">
												<tr class="tabnormal2">
													<td>&nbsp;</td>
													<td align="left">
														<table cellspacing="0" cellpadding="3" border="0" align="left">
															<tr>
																<td>&nbsp;</td>
																<td align="center">
																	<table cellspacing="0" cellpadding="0" border="0" align="center">
																		<tr>
																			<td style="background-color:#e1e6eb"><img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																		</tr>
																	</table>
																</td>
																<td>&nbsp;</td>
																<td align="left" valign="middle"><?php echo LANG_CHA_PLAN_007; ?></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td align="center">
																	<table cellspacing="0" cellpadding="0" border="0" align="center">
																		<tr>
																			<td style="background-color:#fdcc49"><img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																		</tr>
																	</table>
																</td>
																<td>&nbsp;</td>
																<td align="left" valign="middle"><?php echo LANG_CHA_PLAN_008; ?></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td align="center">
																	<table cellspacing="0" cellpadding="0" border="0" align="center">
																		<tr>
																			<td style="background-color:#f2888a"><img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="1" width="30" height="15"></td>
																		</tr>
																	</table>
																</td>
																<td>&nbsp;</td>
																<td align="left" valign="middle"><?php echo LANG_CHA_PLAN_009; ?></td>
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
					</table>					
						<script language="javascript">

		</script>
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
					<script language="javascript">
						var message_erreur = '';
						var separateur = '';
						var valide = true;
						var mode_page = 'normal';
						
						var popup = new popup_modules();


						function aller_semaine_precedente() {
							var form = document.getElementById('formulaire');
							form.operation.value = 'semaine_precedente';
							form.submit();
						}
						
						function aller_semaine_suivante() {
							var form = document.getElementById('formulaire');
							form.operation.value = 'semaine_suivante';
							form.submit();
						}
						
						function effacer_date_debut() {
							var form = document.getElementById('formulaire_criteres');
							form.date_debut.value = '';
						}
						
						function rechercher() {
							var form = document.getElementById('formulaire_criteres');
							form.submit();
						}

						//##################### APPEL AJAX POUR RECUPERER LES ETAGES #############

						function initialisation_recherche_etage() {
							// Indiquer que on est en mode 'initialisation_recherche' => apres le chargement de la page
							mode_page = 'initialisation_recherche';
							// Lancer la recherche des etages
							recuperer_liste_etages();
						}
						
						function init_conteneur_etage_id(mode, donnees) {
							switch(mode) {
								case 'attente_ajax':
									document.getElementById("conteneur_etage_id").innerHTML = '<img src="image/temps1.gif" border=0>';
									break;
								case 'donnees':
									document.getElementById("conteneur_etage_id").innerHTML = donnees;
									break
								default:;
									document.getElementById("conteneur_etage_id").innerHTML = '&nbsp;';
									break;
							}
						}

						var ajax_recuperer_liste_etages;

						function recuperer_liste_etages() {
						
							var ajax_recuperer_liste_etages = new Ajax();
							
							init_conteneur_etage_id('attente_ajax', '');
						
							var form = document.getElementById("formulaire_criteres");
							var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
					
							
							// Parametres de l'Ajax
							ajax_recuperer_liste_etages.setParam ({
								url : "<?php echo url_module(); ?>ajax_etage_recherche.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									mode_page : mode_page,
									etage_id_defaut : <?php echo $etage_id; ?>,
									batiment_id : batiment_id,
									maj_variables_session : 1
								},
								asynchronus : true,
								onComplete : "recuperer_liste_etages_reussite(response)",
								onFailure : "recuperer_liste_etages_echec(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_recuperer_liste_etages.execute();

						}
						
						function recuperer_liste_etages_reussite(response) {
							
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
									init_conteneur_etage_id('donnees', donnees_decoupees[2]);
									recuperer_liste_chambres();
									break;
									
								case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
									alert("<?php echo LANG_CHA_AJAX_001; ?>");
									break;
									
								default: // Erreur inconuue
									// Remplacer la liste deroulante par la nouvelle
									alert("<?php echo LANG_CHA_AJAX_002; ?>");
							}
							
						}
						
						function recuperer_liste_etages_echec(errorCode) {
							msg_util_attente_cacher();
							alert("<?php echo LANG_CHA_AJAX_003; ?>");
						}
						//##################################################################################################

					</script>
					
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						var liste_reservations_par_cellule = new Array();
						<?php
							for($i=0; $i<count($liste_reservations_par_cellule); $i++) {
						?>
								liste_reservations_par_cellule[<?php echo $i; ?>] = new Array();
						<?php
								for($j=0; $j<count($liste_reservations_par_cellule[$i]); $j++) {
						?>
									liste_reservations_par_cellule[<?php echo $i; ?>][<?php echo $j; ?>] = {
										reservation_id : "<?php echo $liste_reservations_par_cellule[$i][$j]['reservation_id']; ?>",
										elev_id : "<?php echo $liste_reservations_par_cellule[$i][$j]['elev_id']; ?>",
										nom_eleve : "<?php echo str_replace("'", "\'", $liste_reservations_par_cellule[$i][$j]['nom_eleve']); ?>",
										date_debut : "<?php echo $liste_reservations_par_cellule[$i][$j]['date_debut']; ?>",
										date_fin : "<?php echo $liste_reservations_par_cellule[$i][$j]['date_fin']; ?>"
									};
						<?php
								}
							}
						?>
					
					
						function onclick_reservation() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_reservation').submit();
						}
						function onclick_parametrage() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_parametrage').submit();
						}
						function onclick_modifier_reservation(id) {
							msg_util_attente_montrer(true);
							var form = document.getElementById('formulaire_modif_reservation');
							form.id.value = id;
							form.submit();
						}
						function onclick_supprimer_reservation(id) {
							if(confirm("<?php echo LANG_CHA_GENE_008; ?>")) {
								msg_util_attente_montrer(true);
								var form = document.getElementById('formulaire_suppr_reservation');
								form.id.value = id;
								form.submit();
							}
						}
						
						function onclick_modifier_reservation_liste(obj, pos_reservations_par_cellule) {
							//alert(id_icon);
							html = '';
							html += '<table border="0">';
							html += '	<tr>';
							html += '		<td align="right">';
							html += '			<a href="javascript:;" onclick="popup.cacher()">';
							html += '				<image src="<?php echo "./" . $g_chemin_relatif_module . "images/fermer.jpg"; ?>" border="0" alt="<?php echo LANG_CHA_GENE_041; ?>" title="<?php echo LANG_CHA_GENE_041; ?>">';
							html += '			</a>';
							html += '		</td>';
							html += '	</tr>';
							html += '	<tr>';
							html += '		<td align="center">';							
							html += '			<select name="modifier_reservation_cellule_' + pos_reservations_par_cellule + '" id="modifier_reservation_cellule_' + pos_reservations_par_cellule + '">';
							for(j=0;j<liste_reservations_par_cellule[pos_reservations_par_cellule].length;j++) {
								if(j == 0) {
									html += '			<option selected value="' + liste_reservations_par_cellule[pos_reservations_par_cellule][j]['reservation_id'] + '">';
								} else {
									html += '			<option value="' + liste_reservations_par_cellule[pos_reservations_par_cellule][j]['reservation_id'] + '">';
								}
								html += liste_reservations_par_cellule[pos_reservations_par_cellule][j]['nom_eleve'];
								html += '			</option>';
							}
							html += '			</select>';
							html += '			<br>';
							html += '			<input type="button" value="<?php echo LANG_CHA_GENE_005; ?>" onclick="onclick_modifier_reservation_liste_ok(' + pos_reservations_par_cellule + ')">';
							html += '		</td>';
							html += '	</tr>';							
							html += '</table>';		
							//alert(html);					
							popup.afficher(obj, html);
						}
						function onclick_modifier_reservation_liste_ok(pos_reservations_par_cellule) {
							var obj_select = document.getElementById('modifier_reservation_cellule_' + pos_reservations_par_cellule);
							var id = obj_select.options[obj_select.selectedIndex].value;
							popup.cacher();
							onclick_modifier_reservation(id);
						}
						
						function onclick_supprimer_reservation_liste(id_icon, pos_reservations_par_cellule) {
							html = '';
							html += '<table border="0">';
							html += '	<tr>';
							html += '		<td align="right">';
							html += '			<a href="javascript:;" onclick="popup.cacher()">';
							html += '				<image src="<?php echo "./" . $g_chemin_relatif_module . "images/fermer.jpg"; ?>" border="0" alt="<?php echo LANG_CHA_GENE_041; ?>" title="<?php echo LANG_CHA_GENE_041; ?>">';
							html += '			</a>';
							html += '		</td>';
							html += '	</tr>';
							html += '	<tr>';
							html += '		<td align="center">';							
							html += '			<select name="supprimer_reservation_cellule_' + pos_reservations_par_cellule + '" id="supprimer_reservation_cellule_' + pos_reservations_par_cellule + '">';
							for(j=0;j<liste_reservations_par_cellule[pos_reservations_par_cellule].length;j++) {
								if(j == 0) {
									html += '			<option selected value="' + liste_reservations_par_cellule[pos_reservations_par_cellule][j]['reservation_id'] + '">';
								} else {
									html += '			<option value="' + liste_reservations_par_cellule[pos_reservations_par_cellule][j]['reservation_id'] + '">';
								}
								html += liste_reservations_par_cellule[pos_reservations_par_cellule][j]['nom_eleve'];
								html += '			</option>';
							}
							html += '			</select>';
							html += '			<br>';
							html += '			<input type="button" value="<?php echo LANG_CHA_GENE_015; ?>" onclick="onclick_supprimer_reservation_liste_ok(' + pos_reservations_par_cellule + ')">';
							html += '		</td>';
							html += '	</tr>';							
							html += '</table>';							
							popup.afficher(id_icon, html);
						}	
						
						function onclick_supprimer_reservation_liste_ok(pos_reservations_par_cellule) {
							var obj_select = document.getElementById('supprimer_reservation_cellule_' + pos_reservations_par_cellule);
							var id = obj_select.options[obj_select.selectedIndex].value;
							popup.cacher();
							onclick_supprimer_reservation(id);
						}						
						function onclic_export_excel() {
							document.getElementById('formulaire_export_excel').submit();
						}
						
						function onclick_annuler() {
								msg_util_attente_montrer(true);
								parent.window.close();
								document.getElementById('formulaire_annuler').submit();
							}
						var fenetre = null;
						var liste_fenetre = new Array();
						function onclic_imprimer() {							
							try {
								for(i=0; i<liste_fenetre.length; i++) {
									liste_fenetre[i].close();
								}
							}
							catch(e) {
							}	
							liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(CHA_REP_MODULE); ?>module_chambres/planning_liste.php?mode_affichage=impression&operation=rechercher','fenetre_editer_' + liste_fenetre.length,'width=1365,height=600,resizable=yes,scrollbars=yes');
								
							liste_fenetre[liste_fenetre.length].focus();					
							}								
						function onclick_fermer() {
							window.close();
						}
									
						function onclick_ouvrir_impession() {
							window.print();
						}					
					</script>
					<form name="formulaire_reservation" id="formulaire_reservation" action="<?php echo $g_chemin_relatif_module; ?>reservation_liste.php" method="post">
					</form>
					<form name="formulaire_parametrage" id="formulaire_parametrage" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
					</form>
					<form name="formulaire_modif_reservation" id="formulaire_modif_reservation" action="<?php echo $g_chemin_relatif_module; ?>reservation_modif.php" method="post">
						<input type="hidden" name="id" id="id" value="0">
						<input type="hidden" name="retour_vers" id="retour_vers" value="planning_liste.php">
					</form>
					<form name="formulaire_suppr_reservation" id="formulaire_suppr_reservation" action="<?php echo url_script(); ?>" method="post">
						<input type="hidden" name="operation" id="operation" value="supprimer_reservation">
						<input type="hidden" name="id" id="id" value="0">
					</form>
					<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>planning_liste_excel.php" method="post" target="">
					</form>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
						</form>
				</td>
			</tr>
		</table>

		<?php
		if($mode_affichage != 'impression') {
		?>

		<?php //********** INITIALISATION DES BULLES D'AIDE ********** ?>
		<script language="javascript">InitBulle("#000000","#FCE4BA","red",1);</script>
		<?php
		}
		?>

		<?php //********** TRAITEMENT A EFFECTUER APRES LE CHARGEMENT DE LA PAGE ********** ?>
		<script language="javascript" type="text/javascript">
		
			// Traitement a effectuer apres le chargement de la page
			function initialisation_page() {
				// Preparer la liste des liens a remplacer
				var liens_a_remplacer = new Array();
				liens_a_remplacer[0] = 	{
											"lien_avec" : '<?php echo site_url_racine(CHA_REP_MODULE); ?>#',
											"remplacer_par" : 'javascript:;'
										};
				// Traitements a effectuer sur toutes les pages
				initialisation_page_global(liens_a_remplacer);
				
				initialisation_recherche_etage();
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
