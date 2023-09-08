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
	$jours_de_la_semaine[8] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 7,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[9] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 8,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[10] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 9,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[11] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 10,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[12] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 11,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[13] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 12,date('Y',$jours_de_la_semaine[1])); 
	$jours_de_la_semaine[14] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + 13,date('Y',$jours_de_la_semaine[1])); 
	
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
			$ligne = &$res->fetchRow();
			
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
					$texte .= '' . $exposant . '';
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
					$ligne = &$res->fetchRow();
				
					$infos_resa_html = '';
					
					$infos_resa_html .= LANG_CHA_RESA_017 . ' : n°' . $ligne[0] . '<br>';
		
					// Rechercher l'eleve qui a deja une reservation
					$sql ="SELECT prenom, nom ";
					$sql.="FROM ".CHA_TAB_ELEVES." ";
					$sql.="WHERE elev_id = " . $ligne[1] . " ";
					$res_eleve=execSql($sql);
					if($res_eleve->numRows() > 0) {
						$ligne_eleve = &$res_eleve->fetchRow();	
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
						$ligne = &$res->fetchRow();
						
						$infos_resa_html .= LANG_CHA_RESA_017 . ' : n°' . $ligne[0] . '<br>';
						
						// Rechercher l'eleve qui a deja une reservation
						$nom_eleve = '';
						$sql ="SELECT prenom, nom ";
						$sql.="FROM ".CHA_TAB_ELEVES." ";
						$sql.="WHERE elev_id = " . $ligne[1] . " ";
						$res_eleve=execSql($sql);
						if($res_eleve->numRows() > 0) {
							$ligne_eleve = &$res_eleve->fetchRow();	
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
		
	
	
	
	
	$nom_fichier_excel = 'reservation_eleve_' . $eleve_id . '_' . date('Ymd') . '.xls';
	
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
	$nb_jours_defaut = 7;
	
	$xls->xlsWriteLabel($ligne_courante, 0, LANG_CHA_RESA_006);
	$xls->xlsWriteLabel($ligne_courante, 1, LANG_CHA_JOURS_001);
	$xls->xlsWriteLabel($ligne_courante, 2, LANG_CHA_JOURS_002);
	$xls->xlsWriteLabel($ligne_courante, 3, LANG_CHA_JOURS_003);
	$xls->xlsWriteLabel($ligne_courante, 4, LANG_CHA_JOURS_004);
	$xls->xlsWriteLabel($ligne_courante, 5, LANG_CHA_JOURS_005);
	$xls->xlsWriteLabel($ligne_courante, 6, LANG_CHA_JOURS_006);
	$xls->xlsWriteLabel($ligne_courante, 7, LANG_CHA_JOURS_007);
	$xls->xlsWriteLabel($ligne_courante, 8, LANG_CHA_JOURS_001);
	$xls->xlsWriteLabel($ligne_courante, 9, LANG_CHA_JOURS_002);
	$xls->xlsWriteLabel($ligne_courante, 10, LANG_CHA_JOURS_003);
	$xls->xlsWriteLabel($ligne_courante, 11, LANG_CHA_JOURS_004);
	$xls->xlsWriteLabel($ligne_courante, 12, LANG_CHA_JOURS_005);
	$xls->xlsWriteLabel($ligne_courante, 13, LANG_CHA_JOURS_006);
	$xls->xlsWriteLabel($ligne_courante, 14, LANG_CHA_JOURS_007);

	for($chambre=0;$chambre<count($chambres);$chambre++) {
		$ligne_courante++;
		$xls->xlsWriteLabel($ligne_courante, 0, $chambres[$chambre]['libelle']);
		
		$date_courante = $date_debut;
		for($jour=1;$jour<=14;$jour++) {
			$classe = 'jours';
			$texte = '';
			for($reservation=0;$reservation<count($reservations);$reservation++) {
				if($reservations[$reservation]['id_ligne'] == $chambres[$chambre]['id_ligne']) {
					if(strtotime($date_courante) >= strtotime($reservations[$reservation]['date_debut']) && strtotime($date_courante) <= strtotime($reservations[$reservation]['date_fin'])) {
						$classe = $reservations[$reservation]['classe_cellule'];
						
						if($classe_cellule == 'jours_actif_orange')
						{
							$texte = "P"; 
						}
						else
						{
							if($classe_cellule == 'jours_actif_rouge')
							{
								$texte = "O"; 
							}
						}
						break;
					}
				}
			}
			$xls->xlsWriteLabel($ligne_courante, $jour, $texte);
			$timestamp = strtotime($date_courante);
			$date_courante = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('d',$timestamp) + 1,date('Y',$timestamp))); 
		}
	}
	$ligne_courante = $ligne_courante+2;
	$xls->xlsWriteLabel($ligne_courante, 0, utf8_decode("Occupé = O"));
	$xls->xlsWriteLabel($ligne_courante, 1, "Partiellement Libre = P");
	
	$xls->xlsEOF();
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . CHA_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>
