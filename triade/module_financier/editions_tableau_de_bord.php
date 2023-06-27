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
	$operation = lire_parametre('operation', '', 'REQUEST');
	$annee_scolaire = lire_parametre('annee_scolaire', 'vide', 'REQUEST');
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************

	//***************************************************************************
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	
		// Verifier si l'annee scolaire est dans la liste
		if($annee_scolaire != 'vide') {
			$annee_trouvee = false;
			for($i=0; $i<$annees_scolaires->numRows(); $i++) {
				$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
				if($ligne[0] == $annee_scolaire) {
					$annee_trouvee = true;
					$temp = true;
					break;
				}
			}
			if(!$annee_trouvee) {
				$annee_scolaire = 'vide';
			}
		}
		
	if($annee_scolaire != 'vide')
	{	
		$donnees = array();
		
		$donnees['nb_inscriptions'] = 0;
		$donnees['montant_encaisse'] = 0.0;
		$donnees['montant_a_encaisser'] = 0.0;
		$donnees['types_frais'] = array();
		$donnees['groupes_frais'] = array();	
		
		
		// Rechercher la liste des types de frais
		$sql ="SELECT type_frais_id, libelle  ";
		$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ";
		$sql.="ORDER BY libelle";
		$frais=execSql($sql);
		for($i=0; $i<$frais->numRows(); $i++) {
			$res = $frais->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
			$donnees['types_frais'][count($donnees['types_frais'])] = array(
																			'type_frais_id' => $ligne[0],
																			'libelle' => $ligne[1],
																			'nombre' => 0,
																			'montant' => 0.0,
																			'reste_a_payer' => 0.0,
																			'encaisse' => 0.0
																			);
		}
		
		
		// Rechercher la liste des groupes de frais
		$sql ="SELECT groupe_id, libelle ";
		$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
		$sql.="ORDER BY groupe_id";
		$groupe_type=execSql($sql);
				
		for($i=0; $i<$groupe_type->numRows(); $i++) {
			// Acces s l'enregistrement courant
			$res1 = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
			
			$donnees['groupes_frais'][count($donnees['groupes_frais'])] = array(
																			'groupe_id' => $ligne_groupe_type[0],
																			'libelle' => $ligne_groupe_type[1],
																			'nombre' => 0,
																			'montant' => 0.0,
																			'reste_a_payer' => 0.0,
																			'encaisse' => 0.0
																			);
		}

		
		if($annees_scolaires->numRows() > 0) {
			// Rechercher les inscriptions
			$sql ="SELECT inscription_id ";
			$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
			$sql.="WHERE annee_scolaire = '" . $annee_scolaire . "' ";
			$sql.="ORDER BY annee_scolaire ";		

			$inscriptions=execSql($sql);
			
			$donnees['nb_inscriptions'] = $inscriptions->numRows();

			for($i=0; $i<$inscriptions->numRows(); $i++) {
				$res = $inscriptions->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
				$total_frais = inscription_total_frais($ligne[0], -1);
				$total_reglements_realises = 0.0;
				$frais_pour_cet_eleve = 0.0;
				$reste_a_payer_pour_cet_eleve = 0.0;
				// Rechercher les reglements pour cette inscription
				$sql ="SELECT SUM(r.montant) as total_reglement ";
				$sql.="FROM ".FIN_TAB_ECHEANCIER." e ";
				$sql.="INNER JOIN ".FIN_TAB_REGLEMENT." r ON e.echeancier_id = r.echeancier_id ";
				$sql.="WHERE e.inscription_id = " . $ligne[0] . " ";
				$sql.="AND r.realise = 1 ";
				$total_reglement=execSql($sql);
				if($total_reglement->numRows()) {
					$res = $total_reglement->fetchInto($ligne_total_reglement, DB_FETCHMODE_DEFAULT, 0);
					$total_reglements_realises = $ligne_total_reglement[0];
				}

				$donnees['montant_encaisse'] += $total_reglements_realises;
				
				$donnees['montant_a_encaisser'] += $total_frais - $total_reglements_realises;
				
				
			
				// Rechercher la liste des frais 
				$sql ="SELECT type_frais_id, montant  ";
				$sql.="FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
				$sql.="WHERE inscription_id = " . $ligne[0] . " ";
				$sql.="AND ((optionnel = 0) OR (optionnel = 1 AND selectionne = 1)) ";
				$frais=execSql($sql);
				for($k=0; $k<$frais->numRows(); $k++) {
					$res = $frais->fetchInto($ligne_frais, DB_FETCHMODE_DEFAULT, $k);
					
					// Rechercher la position du frais dans la liste<br>
					// Si on le trouve, on met a jour ses donnees
					for($l=0; $l<count($donnees['types_frais']); $l++) {
						if($donnees['types_frais'][$l]['type_frais_id'] == $ligne_frais[0]) {
							$donnees['types_frais'][$l]['nombre']++;
							$donnees['types_frais'][$l]['montant'] += $ligne_frais[1];
							break;
						}
					}
					
				}
				
				$frais_pour_cet_eleve = inscription_total_frais($ligne[0]);
				$reste_a_payer_pour_cet_eleve = reglement_reste_a_payer('inscription', $ligne[0]);
				
				
				// Rechercher la liste des groupes de frais 
				
				$sql ="SELECT ti.type_frais_id, fi.montant, ti.groupe_id  ";
				$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ti INNER JOIN ".FIN_TAB_FRAIS_INSCRIPTION." fi ";
				$sql.="ON ti.type_frais_id = fi.type_frais_id ";
				$sql.="WHERE fi.inscription_id = " . $ligne[0] . " ";
				$sql.="AND ((fi.optionnel = 0) OR (fi.optionnel = 1 AND fi.selectionne = 1)) ";

				$groupe1=execSql($sql);
				$pourcentage = 0.0;
				if($reste_a_payer_pour_cet_eleve != 0){
					$pourcentage = $reste_a_payer_pour_cet_eleve /$frais_pour_cet_eleve;
				}
				for($v=0; $v<$groupe1->numRows();$v++)
				{
					$resg = $groupe1->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
					
					for($l=0; $l<count($donnees['groupes_frais']);$l++)
					{
						if($donnees['groupes_frais'][$l]['groupe_id'] == $ligne_groupe[2]) {
							
							$donnees['groupes_frais'][$l]['nombre'] ++;
							$donnees['groupes_frais'][$l]['montant'] += $ligne_groupe[1];
							
							if($pourcentage != 0)
							{
								$temp = $ligne_groupe[1] * $pourcentage;
								$donnees['groupes_frais'][$l]['reste_a_payer'] += $temp;
								$donnees['groupes_frais'][$l]['encaisse'] += ($ligne_groupe[1] - $temp);
							}
							else
							{
								$donnees['groupes_frais'][$l]['encaisse'] += $ligne_groupe[1];		
							}
						}
					}
				}
			}
			
		}
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
		<?php
		$largeur_cadre = 600;
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
		?>		
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
		<?php include("./librairie_php/lib_defilement.php"); ?>
		</td>
		<td width="472" valign="middle" rowspan="3" align="center">
			<div align='center'>
				<?php top_h(); ?>
				<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>
		<?php
		}
		?>
		
		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		<div align="<?php echo $alignement_cadre; ?>">
			<!-- TITRE ET CADRE CENTRAL -->
			<table border="0" cellpadding="3" cellspacing="1" width="600" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_004; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
					</td>
				</tr>
				<tr id="cadreCentral0">
					<td valign="top" align="center">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
								<?php //********** AFFICHAGE DES DONNEES ********** ?>
								
								<tr>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td valign="top" align="center">
										<form name="formulaire_criteres" id="formulaire_criteres" action="<?php echo url_script(); ?>" method="post" onSubmit="">
											<input type="hidden" name="operation" id="operation" value="">
	
										
											<fieldset>
												<legend><?php echo LANG_FIN_GENE_021; ?></legend>
												<table cellpadding="0" cellspacing="2" align="center">
		
													<tr>
														<td align="right"><?php echo LANG_FIN_GENE_011; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<select name="annee_scolaire" id="annee_scolaire" onChange="onchange_annee_scolaire()" <?php echo $disabled_cadre; ?>>
																<?php
																	$selected = '';
																	if($annee_scolaire == '') {
																		$selected = 'selected="selected"';
																	}
																?>
																<option value="vide" <?php echo $selected; ?>><?php echo ucfirst(LANG_FIN_GENE_063); ?></option>
																<?php
																// Verifier si on a au moins une annee scolaire
																if($annees_scolaires->numRows() > 0) {
																	for($i=0; $i<$annees_scolaires->numRows(); $i++) {
																		$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																		$selected = '';
																		if($annee_scolaire == $ligne[0]) {
																			$selected = 'selected="selected"';
																		}
																?>
																<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[0]; ?></option>
																<?php
																	}
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td colspan="2" align="center">&nbsp;</td>
													</tr>
													<tr>
													<td></td>
														<td>
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_rechercher();" <?php echo $disabled_cadre; ?>>
														</td>
														<td>
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
														</td>
													</tr>
													<tr>
														<td></td>
														<td>
														<?php 
														msg_util_afficher();
														msg_util_attente_init(); 
														?>
														</td>
													</tr>									
												</table>
												<br>
											</fieldset>
										</form>
									</td>
								</tr>
								
								<tr>
									<td valign="top" align="left" nowrap="nowrap">&nbsp;
										
									</td>
								</tr>
								
						
								<tr>
									<td valign="top" align="center">
									<?php
									if($annee_scolaire != 'vide')
									{ ?>
										<p align="left">
												&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
										</p>
										<table cellspacing="0" cellpadding="0" border="0" align="center">
											<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo LANG_FIN_EBOR_005; ?>&nbsp;:&nbsp;</td>
												<td align="left" valign="top" nowrap="nowrap"><?php echo $donnees['nb_inscriptions']; ?></td>
											</tr>								
											<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo LANG_FIN_EBOR_006; ?>&nbsp;:&nbsp;</td>
												<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($donnees['montant_encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											</tr>								
											<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo LANG_FIN_EBOR_007; ?>&nbsp;:&nbsp;</td>
												<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($donnees['montant_a_encaisser']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											</tr>		
											<?php 
											$montant_total = $donnees['montant_encaisse'] + $donnees['montant_a_encaisser'];
											?>
											<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo LANG_FIN_GENE_013; ?>&nbsp;:&nbsp;</td>
												<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($montant_total); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											</tr>	
											<tr>
												<tr>
													<td colspan="2" align="center">&nbsp;</td>
												</tr>
											<tr>
											<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo LANG_FIN_FBAR_003; ?>&nbsp;:&nbsp;</td>
												<td align="left" valign="top" nowrap="nowrap">
												
													<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="left">
														<tr bgcolor="#ffffff">
															<td align="left" valign="top"><b><?php echo LANG_FIN_FBAR_004; ?></b></td>
															<td align="right" valign="top"><b><?php echo LANG_FIN_EBOR_008; ?></b></td>
															<td align="center" valign="top"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
														</tr>
														<?php
														$total_des_frais = 0.0;
														
														$type_encaisse = 0.0;
														$type_reste_a_payer = 0.0;
														for($i=0; $i<count($donnees['types_frais']); $i++) {
															$total_des_frais += $donnees['types_frais'][$i]['montant'];
														?>
														<tr bgcolor="#ffffff">
															<td align="left" valign="top"><?php echo $donnees['types_frais'][$i]['libelle']; ?></td>
															<td align="center" valign="top"><?php echo $donnees['types_frais'][$i]['nombre']; ?></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($donnees['types_frais'][$i]['montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
														</tr>
														<?php
														}
														?>
														<tr bgcolor="#ffffff">
															<td align="right" valign="top" colspan="2"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($total_des_frais); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
														</tr>
													</table>
												
												</td>
											</tr>
											<tr>
												<tr>
													<td colspan="2" align="center">&nbsp;</td>
												</tr>
											<tr>
											<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo LANG_FIN_FBAR_013; ?>&nbsp;:&nbsp;</td>
												<td align="left" valign="top" >
												
													<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="left">
														<tr bgcolor="#ffffff">
															<td align="left" valign="top"><b><?php echo LANG_FIN_GROUPE_014; ?></b></td>
															<td align="right" valign="top"><b><?php echo LANG_FIN_EBOR_008; ?></b></td>
															<td align="center" valign="top"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
															<td align="center" valign="top"><b><?php echo LANG_FIN_EENC_008;?></b></td>
															<td align="center" valign="top"><b><?php echo LANG_FIN_GENE_034;?></b></td>
														</tr>
														<?php
														$total_des_groupes = 0.0;
														$groupe_encaisse = 0.0;
														$groupe_reste_a_payer = 0.0;
														for($i=0; $i<count($donnees['groupes_frais']); $i++) {
															$total_des_groupes += $donnees['groupes_frais'][$i]['montant'];
															$groupe_encaisse += $donnees['groupes_frais'][$i]['encaisse'];
															$groupe_reste_payer += $donnees['groupes_frais'][$i]['reste_a_payer'];
														?>
														<tr bgcolor="#ffffff">
															<td align="left" valign="top"><?php echo $donnees['groupes_frais'][$i]['libelle']; ?></td>
															<td align="center" valign="top"><?php echo $donnees['groupes_frais'][$i]['nombre']; ?></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($donnees['groupes_frais'][$i]['montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($donnees['groupes_frais'][$i]['encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($donnees['groupes_frais'][$i]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
														</tr>
														<?php
														}
														?>
														<tr bgcolor="#ffffff">
															<td align="right" valign="top" colspan="2"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($total_des_groupes); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($groupe_encaisse); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="right" valign="top"><?php echo montant_depuis_bdd($groupe_reste_payer); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
														</tr>
													</table>
												</td>
											</tr>	
										</table>

											
										
									<?php }?>
									</td>
								</tr>
	
			
								<?php //********** MESSAGES UTILISATEUR ********** ?>
								
								<tr>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="center">
										<a name="MESSAGE"></a>
										</td>
								</tr>
					
					
								<?php //********** BOUTONS ********** ?>
								
								<tr>
									<td align="center">
									<?php
									if($annee_scolaire != 'vide')
									{	 ?>
										<table border="0" align="center" cellpadding="4" cellspacing="0">
											<?php
											if($mode_affichage != 'impression') {
											?>									
											<tr>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_060?>","onclick_imprimer()");</script>
												</td>
												<td align="center">&nbsp;</td>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
												</td>
											</tr>
											<?php
											} else {
											?>
											<tr>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_060?>","onclick_ouvrir_impession()");</script>
												</td>
												<td align="center">&nbsp;</td>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_041?>","onclick_fermer()");</script>
												</td>
											</tr>
											<?php
											}
											?>											
										</table>
										<?php }?>
									</td>
								</tr>
									
									
							</table>
						
						
						<?php  //********** VALIDATION FORMULAIRES ********** ?>
				
				
						<?php //********** GESTION NAVIGATION ********** ?>
						
						<script language="javascript">
							var fenetre = null;
							var liste_fenetre = new Array();
							
							function onclick_annuler() {
								msg_util_attente_montrer(true);
								document.getElementById('formulaire_annuler').submit();
							}
							function onchange_annee_scolaire() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "reload_annee_scolaire";
								document.formulaire_criteres.submit();
							}
							
							function onclick_rechercher() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "rechercher";
								document.formulaire_criteres.submit();
							}
							
							function onclick_imprimer() {										
									try {
										for(i=0; i<liste_fenetre.length; i++) {
											liste_fenetre[i].close();
										}
										
									}
									catch(e) {
									}								
									liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/editions_tableau_de_bord.php?operation=impression&mode_affichage=impression&annee_scolaire=<?php echo urlencode($annee_scolaire); ?>','fenetre_editer_' + liste_fenetre.length,'width=800,height=600,resizable=yes,scrollbars=yes');
									liste_fenetre[liste_fenetre.length].focus();						
							}
							
							function onclic_export_excel() {
							//	msg_util_attente_montrer(true);
								document.getElementById('formulaire_export_excel').submit();
							}
							
							function onclick_fermer() {
								window.close();
							}
							
							function onclick_ouvrir_impession() {
								window.print();
							}
						</script>
						<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>editions.php" method="post">
						</form>
						<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>editions_tableau_de_bord_excel.php" method="post" target="">
							<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire?>">
						</form>
						
						
					</td>
				</tr>
			</table>
		</div>
		<?php
			if($mode_affichage != 'impression') {
		?>

		<?php //********** GENERATION DES MENUS ADMINISTRATEUR ********** ?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></script>
		

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
											"lien_avec" : '<?php echo site_url_racine(FIN_REP_MODULE); ?>#',
											"remplacer_par" : 'javascript:;'
										};
				// Traitements a effectuer sur toutes les pages
				initialisation_page_global(liens_a_remplacer);
				
				onchange_code_class_copier();
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