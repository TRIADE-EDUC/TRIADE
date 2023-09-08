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

$erreur = 0;

// Envoyer les entetes HTTP pour generer du texte
header('Content-type: text/plain; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$eleve_id = lire_parametre('eleve_id', 0, 'POST');
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
	$etage_id = lire_parametre('etage_id', 0, 'POST');
	$chambre_id = lire_parametre('chambre_id', 0, 'POST');
	$date_debut = lire_parametre('date_debut', '', 'POST');
	$date_fin = lire_parametre('date_fin', '', 'POST');
	$maj_variables_session = lire_parametre('maj_variables_session', 0, 'POST');
	//***************************************************************************

	$debug_message = '';
	
	// On guarde les criteres selectionnes dans la session
	if($maj_variables_session == '1') {
		$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = $eleve_id;
		$_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'] = $batiment_id;
		$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = $etage_id;
		$_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'] = $chambre_id;
		$_SESSION[CHA_REP_MODULE]['reservation_liste']['date_debut'] = $date_debut;
		$_SESSION[CHA_REP_MODULE]['reservation_liste']['date_fin'] = $date_fin;
		//$debug_message .= 'maj_session: eleve_id=' . $eleve_id . '';
	}
	
	//***************************************************************************
	// Recuperer la liste des chambres (filtrage par batiment)
	$html_chambre_liste = '';
	if($erreur == 0) {
		// Execution requete
		$sql ="SELECT e.prenom, e.nom, b.libelle as libelle_batiment, b.code_postal, b.ville, c.chambre_id, c.numero, c.libelle as libelle_chambre, r.reservation_id, r.date_debut, r.date_fin, r.commentaire, r.date_reservation, c.type_chambre_id, c.etage_id ";
		$sql.="FROM ((".CHA_TAB_RESERVATION." r LEFT JOIN ".CHA_TAB_ELEVES." e ON r.elev_id = e.elev_id) ";
		$sql.="INNER JOIN ".CHA_TAB_CHAMBRE." c ON r.chambre_id = c.chambre_id) ";
		$sql.="INNER JOIN ".CHA_TAB_BATIMENT." b ON c.batiment_id = b.batiment_id ";
		$sql.="WHERE 1=1 ";
		if($eleve_id != 0) {
			$sql.="AND e.elev_id = " . $eleve_id . " ";
		}
		if($batiment_id != 0) {
			$sql.="AND c.batiment_id = " . $batiment_id . " ";
		}
		if($etage_id != 0) {
			$sql.="AND c.etage_id = " . $etage_id . " ";
		}
		if($chambre_id != 0) {
			$sql.="AND c.chambre_id = " . $chambre_id . " ";
		}
		if($date_debut != '') {
			$sql.="AND r.date_fin >= '" . date_vers_bdd($date_debut) . "' ";
		}
		if($date_fin != '') {
			$sql.="AND r.date_debut <= '" . date_vers_bdd($date_fin) . "' ";
		}
		$sql.="ORDER BY b.libelle, b.code_postal, b.ville, c.numero ASC, c.libelle ASC";
		//echo $sql;
		$res=execSql($sql);

		// Generer le debut du tableau
		$html_chambre_liste .= '<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">';
		// Generer les entete de colonne
		$html_chambre_liste .= '	<tr bgcolor="#ffffff">';
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>#</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_RESA_009 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_RESA_005 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_ETAG_001 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_RESA_006 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_TCHA_001 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_RESA_007 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_RESA_008 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap"><b>' . LANG_CHA_RESA_018 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left" nowrap="nowrap">&nbsp;</td>';									
		$html_chambre_liste .= '	</tr>';									
		
		
		// Verifier si au moins une chambre a ete trouvee et generer la liste
		$total_enregistrements = 0;
		if($res->numRows() > 0) {
			$total_enregistrements = $res->numRows();
			for($i=0; $i<$res->numRows(); $i++) {
				$ligne = &$res->fetchRow();

				$html_chambre_liste .= '	<tr class="tabnormal2" onMouseOver="this.className=\'tabover\';" onMouseOut="this.className=\'tabnormal2\';" align="left">';

				$texte = $ligne[8];
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';									

				$separateur = '';
				$texte = trim($ligne[0]);
				if(trim($texte) != '') {
					$separateur = ' - ';
				}
				
				if(trim($ligne[4]) != '') {
					$texte .= $separateur . trim($ligne[4]);															
					$separateur = ' - ';
				}

				if(trim($ligne[5]) != '') {
					$texte .= $separateur . trim($ligne[5]);
					$separateur = ' - ';
				}


				$texte = trim($ligne[0]) . ' ' . trim($ligne[1]);
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';									


				$texte = trim($ligne[2]) . ' - ' . trim($ligne[3]) . ' ' . trim($ligne[4]);
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';		



				// Etage
				eval('$texte = LANG_CHA_ETAG_ID_' . $ligne[14] .'_LIBELLE;');
				eval('$exposant = LANG_CHA_ETAG_ID_' . $ligne[14] .'_EXPOSANT;');
				if(trim($exposant) != '') {
					$texte .= '<sup>' . $exposant . '</sup>';
				}
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';									


				$separateur = '';
				$texte = trim($ligne[6]);
				if(trim($texte) != '') {
					$texte = 'n°' . $texte;
					$separateur = ' - ';
				}
				if(trim($ligne[7]) != '') {
					$texte .= $separateur . trim($ligne[7]);															
					$separateur = ' - ';
				}
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';		
				
				eval('$texte = LANG_CHA_TCHA_ID_' . $ligne[13] .';');
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';		


				$texte = date_depuis_bdd(trim($ligne[9]));
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';		

				$texte = date_depuis_bdd(trim($ligne[10]));
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';		

				$texte = date_depuis_bdd(trim($ligne[12]), false);
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';		



				$html_chambre_liste .= '		<td nowrap="nowrap" valign="top">';
				$html_chambre_liste .= '			<table cellspacing="0" cellpadding="0" border="0">';
				$html_chambre_liste .= '				<tr>';									
				$html_chambre_liste .= '					<td align="left" valign="middle">';									
				$html_chambre_liste .= '						<a href="javascript:;" onclick="onclick_details(' . $ligne[8] . ');" title="' . LANG_CHA_GENE_039 . '"><img src="image/commun/affichage.gif" border="0" alt="' . LANG_CHA_GENE_039 . '"></a>';
				$html_chambre_liste .= '					</td>';		
				$html_chambre_liste .= '					<td align="left" valign="middle">';									
				$html_chambre_liste .= '						&nbsp;&nbsp;';
				$html_chambre_liste .= '					</td>';		
				$html_chambre_liste .= '					<td align="left" valign="middle">';									
				$html_chambre_liste .= '						<a href="javascript:;" onclick="onclick_modifier(' . $ligne[8] . ');" title="' . LANG_CHA_GENE_005 . '"><img src="image/commun/b_edit.png" border="0" alt="' . LANG_CHA_GENE_005 . '"></a>';
				$html_chambre_liste .= '					</td>';		
				$html_chambre_liste .= '					<td align="left" valign="middle">';									
				$html_chambre_liste .= '						&nbsp;&nbsp;';
				$html_chambre_liste .= '					</td>';		
				$html_chambre_liste .= '					<td align="left" valign="middle">';									
				$html_chambre_liste .= '						<a href="javascript:;" onclick="onclick_supprimer(' . $ligne[8] . ');" title="' . LANG_CHA_GENE_015 . '"><img src="image/commun/b_drop.png" border="0" alt="' . LANG_CHA_GENE_015 . '"></a>';
				$html_chambre_liste .= '					</td>';		
				$html_chambre_liste .= '				</tr>';									
				$html_chambre_liste .= '			</table>';									
				
				
				//$html_chambre_liste .= '			<input type="button" class="button" value="' . LANG_CHA_GENE_005 . '" onClick="onclick_modifier(\'' . $ligne[8] . '\');" >';
				$html_chambre_liste .= '		</td>';
											
				$html_chambre_liste .= '	</tr>';		
				
				// Ligne pour afficher le commentaire
				$texte = trim($ligne[11]);
				if($texte != '') {
					$texte = str_replace("\n", "<br>", $texte);
				} else {
					$texte = '<font color="#999999"><i>' . LANG_CHA_RESA_104 . '</i></font>';
				}
											
				$html_chambre_liste .= '	<tr class="tabnormal2" id="' . $ligne[8] . '_commentaire" style="display:none">';									
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top" colspan="10">';
				$html_chambre_liste .= '			' . $texte;		
				$html_chambre_liste .= '		</td>';		
				$html_chambre_liste .= '	</tr>';									

			}
		} else {
			$html_chambre_liste .= '	<tr class="tabnormal2" onMouseOver="this.className=\'tabover\';" onMouseOut="this.className=\'tabnormal2\';">';
				$html_chambre_liste .= '	<td align="left" nowrap="nowrap" valign="top" colspan="10">';
				$html_chambre_liste .= '		<div class="messages_utilisateur">';								
				$html_chambre_liste .= '			<div class="avertissement">';								
				$html_chambre_liste .= '				' . LANG_CHA_RESA_100;									
				$html_chambre_liste .= '			</div>';		
				$html_chambre_liste .= '		</div>';		
				$html_chambre_liste .= '	</td>';		
			$html_chambre_liste .= '	</tr>';									
		}

		// Generer la fin du tableau
		$html_chambre_liste .= '</table>';
	}
	//***************************************************************************
	
} else {
	// Erreur authentification (code=99)
	$erreur = 99;

	// Fermeture connexion bddd
	Pgclose();

	exit();
}

echo $erreur;
echo '¬';
echo $total_enregistrements;
echo '¬';
echo $html_chambre_liste;
echo '¬';
echo $debug_message;

?>
