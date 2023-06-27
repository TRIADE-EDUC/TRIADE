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
	$numero_bordereau = lire_parametre('num', '', 'GET');
	$type_reglement_id = lire_parametre('type', '', 'GET');
	//***************************************************************************

	
	// Verifier que l'on a bien toutes les donnees
	
		$total_global = 0.0;
	
		// Recuperer la liste des reglements
		$total = 0.0;
		$lignes_tableau = '';
		
		$sql  = "SELECT el.nom, el.prenom, cl.libelle, i.annee_scolaire, r.montant, r.numero_cheque, r.date_remise_bordereau ";
		$sql.=  "FROM ".FIN_TAB_REGLEMENT." r "; 
		$sql .= "INNER JOIN ".FIN_TAB_ECHEANCIER." ec ON ec.echeancier_id = r.echeancier_id ";
		$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON ec.inscription_id = i.inscription_id ";
		$sql .= "INNER JOIN ".FIN_TAB_ELEVES." el ON i.elev_id = el.elev_id ";
		$sql .= "INNER JOIN ".FIN_TAB_CLASSES." cl ON i.code_class = cl.code_class ";
		$sql .= "WHERE numero_bordereau = $numero_bordereau ";
		$sql .= "ORDER BY el.nom ASC";
		$reglements = execSql($sql);
		$echeancier_id_courant = 0;		
		$total_cheque=0;
		if($reglements->numRows() > 0) {
			// Recuperer les infos de chaque echeance a traiter
			for($i=0; $i<$reglements->numRows(); $i++) {
							
				// Recuperer les infos du reglement
				$ligne_reglement = $reglements->fetchRow();
				
				//echo $ligne_reglement[0] . '<br>';
				$total += $ligne_reglement[4];
				$total_cheque++;
				$date_remise = date_depuis_bdd($ligne_reglement[6]);
				
				// Generer chaque ligne du tableau de donnees
				switch($type_reglement_id) {
				
					case $g_tab_type_reglement_id['cheque']:
						$lignes_tableau .= '<tr>' . "\n";
						$lignes_tableau .= "	<td align='left'>" . strtoupper($ligne_reglement[0]) . '</td>' . "\n";
						$lignes_tableau .= "	<td align='left'>" . ucfirst($ligne_reglement[1]) . '</td>' . "\n";
						// $lignes_tableau .= "	<td align='left'></td>" . "\n";
						// $lignes_tableau .= "	<td align='left'></td>" . "\n";
						$lignes_tableau .= "	<td align='left'>" . $ligne_reglement[3] . '</td>' . "\n";
						$lignes_tableau .= "	<td align='left'>" . $ligne_reglement[2] . '</td>' . "\n";
						$lignes_tableau .= "	<td align='left'>" . $ligne_reglement[5] . '</td>' . "\n";
						$lignes_tableau .= "	<td align='right'>" . montant_depuis_bdd($ligne_reglement[4]) . '&nbsp;' . LANG_FIN_GENE_019 . '</td>' . "\n";
						$lignes_tableau .= "</tr>" . "\n";
						break;
					case $g_tab_type_reglement_id['espece']:
						$lignes_tableau .= '<tr>' . "\n";
						$lignes_tableau .= '	<td align="left">' . strtoupper($ligne_reglement[0]) . '</td>' . "\n";
						$lignes_tableau .= '	<td align="left">' . ucfirst($ligne_reglement[1]) . '</td>' . "\n";
						$lignes_tableau .= '	<td align="left">' . $ligne_reglement[3] . '</td>' . "\n";
						$lignes_tableau .= '	<td align="left">' . $ligne_reglement[2] . '</td>' . "\n";
						$lignes_tableau .= '	<td align="right">' . montant_depuis_bdd($ligne_reglement[4]) . '&nbsp;' . LANG_FIN_GENE_019 . '</td>' . "\n";
						$lignes_tableau .= '</tr>' . "\n";
						break;
				}


								
			}
		}
		
		// Recuperer le contenu du fichier de format et generer le titre du bordereau et le titre du fichier PDF
		$titre_bordereau = '';
		$nom_fichier = 'bordereau_remise';
		switch($type_reglement_id) {
			case $g_tab_type_reglement_id['cheque']:
				$template = file_get_contents('./' . $g_chemin_relatif_module . '/templates/pdf/bordereau_cheque.htm');
				$titre_bordereau = LANG_FIN_GBOR_015.' n° '.$numero_bordereau.' du '.$date_remise;
				$nom_fichier = LANG_FIN_GBOR_017 . '_' . substr($date_remise, 6, 4) . substr($date_remise, 3, 2) . substr($date_remise, 0, 2) . '.pdf';
				break;
			case $g_tab_type_reglement_id['espece']:
				$template = file_get_contents('./' . $g_chemin_relatif_module . '/templates/pdf/bordereau_espece.htm');
				$titre_bordereau = LANG_FIN_GBOR_016.' n° '.$numero_bordereau.' du '.$date_remise;
				$nom_fichier = LANG_FIN_GBOR_018 . '_' . substr($date_remise, 6, 4) . substr($date_remise, 3, 2) . substr($date_remise, 0, 2) . '.pdf';
				break;
		}
		
		// Remplacer les donnees dans le template
		$template = str_replace('#VALEUR_TITRE#', $titre_bordereau, $template);
		$template = str_replace('#NOM#', LANG_FIN_ELEV_005, $template);
		$template = str_replace('#PRENOM#', LANG_FIN_ELEV_004, $template);
		$template = str_replace('#ANNEE#', LANG_FIN_GENE_011, $template);
		$template = str_replace('#CLASSE#', LANG_FIN_CLAS_003, $template);
		
		$template = str_replace('#LIBELLE_NUMERO_CHEQUE#', ucfirst(LANG_FIN_REGL_020), $template);
		$template = str_replace('#LIBELLE_MONTANT#', ucfirst(LANG_FIN_GENE_013), $template);
		$template = str_replace('#VALEUR_TOTAL#', montant_depuis_bdd($total) . '&nbsp;' . LANG_FIN_GENE_019, $template);
		
		$template = str_replace('#CHEQUE_TOTAL#', $total_cheque, $template);
		$template = str_replace('#LIGNES_TABLEAU#', $lignes_tableau, $template);
	
		// Inclure la librairie de converstion HTML => PDF
		include_once('./' . $g_chemin_relatif_module . '/librairie_pdf/html2fpdf/html2fpdf.php');

		// Creer l'objet PDF
		$pdf = new HTML2FPDF(); 
		
		$pdf->SetFont('Arial','',12);
		
		// Ajouter une page
		$pdf->AddPage();
		
		// Se positionner au debut de la page
		$pdf->SetXY(0,0);
		
		// Ecrire le texte sur la page
		$pdf->WriteHTML($template);

		// Generer le fichier PDF et l'envoyer au navigateur
		$pdf->Output($nom_fichier, 'D');
		
	
	
	

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