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
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
	$maj_variables_session = lire_parametre('maj_variables_session', 0, 'POST');
	//***************************************************************************

	// On guarde le batiment selectionne dans la session
	if($maj_variables_session == '1') {
		$_SESSION[CHA_REP_MODULE]['chambre_liste']['batiment_id'] = $batiment_id;
	}
	
	//***************************************************************************
	// Recuperer la liste des chambres (filtrage par batiment)
	$html_chambre_liste = '';
	if($erreur == 0) {
		// Execution requete
		$sql ="SELECT chambre_id, b.libelle as libelle_batiment, c.numero, c.libelle as libelle_chambre, b.code_postal, b.ville, c.type_chambre_id, c.etage_id ";
		$sql.="FROM ".CHA_TAB_CHAMBRE." c LEFT JOIN ".CHA_TAB_BATIMENT." b ON c.batiment_id = b.batiment_id ";
		if($batiment_id != 0) {
			$sql.="WHERE c.batiment_id = ".$batiment_id. " ";
		}
		$sql.="ORDER BY b.libelle, b.code_postal, b.ville, c.numero ASC, c.libelle ASC";
		$res=execSql($sql);
		
		// Generer le debut du tableau
		$html_chambre_liste .= '<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">';
		// Generer les entete de colonne
		$html_chambre_liste .= '	<tr bgcolor="#ffffff">';
		$html_chambre_liste .= '		<td align="left"><b>' . LANG_CHA_CHAM_012 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left"><b>' . LANG_CHA_ETAG_001 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left"><b>' . LANG_CHA_CHAM_014 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left"><b>' . LANG_CHA_TCHA_001 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left"><b>' . LANG_CHA_CHAM_013 . '</b></td>';									
		$html_chambre_liste .= '		<td align="left">&nbsp;</td>';									
		$html_chambre_liste .= '	</tr>';									
		
		
		// Verifier si au moins une chambre a ete trouvee et generer la liste
		$total_enregistrements = 0;
		if($res->numRows() > 0) {
			$total_enregistrements = $res->numRows();
			for($i=0; $i<$res->numRows(); $i++) {
				$ligne = &$res->fetchRow();

				$html_chambre_liste .= '	<tr class="tabnormal2" onMouseOver="this.className=\'tabover\';" onMouseOut="this.className=\'tabnormal2\';" align="left">';

				$separateur = '';
				$texte = trim($ligne[1]);
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
				
				eval('$type_chambre = LANG_CHA_TCHA_ID_' . $ligne[6] .';');

				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';									

				// Etage
				eval('$texte = LANG_CHA_ETAG_ID_' . $ligne[7] .'_LIBELLE;');
				eval('$exposant = LANG_CHA_ETAG_ID_' . $ligne[7] .'_EXPOSANT;');
				if(trim($exposant) != '') {
					$texte .= '<sup>' . $exposant . '</sup>';
				}
				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $texte;									
				$html_chambre_liste .= '		</td>';									


				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $ligne[2];									
				$html_chambre_liste .= '		</td>';		

				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $type_chambre;									
				$html_chambre_liste .= '		</td>';		

				$html_chambre_liste .= '		<td align="left" nowrap="nowrap" valign="top">';									
				$html_chambre_liste .= '			' . $ligne[3];									
				$html_chambre_liste .= '		</td>';		

				$html_chambre_liste .= '		<td nowrap="nowrap" valign="top">';
				$html_chambre_liste .= '			<input type="button" class="button" value="' . LANG_CHA_GENE_005 . '" onClick="onclick_modifier(\'' . $ligne[0] . '\');" >';
				$html_chambre_liste .= '		</td>';
											
				$html_chambre_liste .= '	</tr>';									

			}
		} else {
			$html_chambre_liste .= '	<tr class="tabnormal2" onMouseOver="this.className=\'tabover\';" onMouseOut="this.className=\'tabnormal2\';">';
				$html_chambre_liste .= '	<td align="left" nowrap="nowrap" valign="top" colspan="5">';
				$html_chambre_liste .= '		<div class="messages_utilisateur">';								
				$html_chambre_liste .= '			<div class="avertissement">';								
				$html_chambre_liste .= '				' . LANG_CHA_CHAM_104;									
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
?>
