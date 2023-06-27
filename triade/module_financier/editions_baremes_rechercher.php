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
	$code_class = lire_parametre('code_class', 0, 'REQUEST');
	$annee_scolaire = lire_parametre('annee_scolaire', '', 'REQUEST');
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	//***************************************************************************

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Initialisation sur changement de classe
	if($operation == "reload_code_class") {
		$annee_scolaire = '';
	}

	//***************************************************************************
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_BAREME." b ON c.code_class = b.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	//echo $sql;
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_BAREME." ";
	$sql.="WHERE code_class = " . $code_class . " ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	
	//echo $annee_scolaire;
	
	// Rechercher les baremes
	$sql ="SELECT b.bareme_id, b.libelle, b.annee_scolaire, c.libelle ";
	$sql.="FROM ".FIN_TAB_BAREME." b ";
	$sql.="INNER JOIN ".FIN_TAB_CLASSES." c ON b.code_class = c.code_class ";
	$sql.="WHERE 1 = 1 ";
	if($code_class != '0' && $code_class != '') {
		$sql.="AND b.code_class = " . $code_class . " ";
	}
	if($annee_scolaire != '') {
		$sql.="AND b.annee_scolaire = '" . $annee_scolaire . "' ";
	}
	$sql.="ORDER BY  b.annee_scolaire, c.libelle, b.libelle";
	$baremes=execSql($sql);
	//echo $sql;
	
	
	
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

			<?php //********** GENERATION DU DEBUT DE LA PAGE ET DES MENUS PRINCIPAUX ********** ?>
		
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
			<table border="0" cellpadding="3" cellspacing="1" width="<?php echo $largeur_cadre; ?>" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;" >
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_002; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
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
	
											<?php //********** CLASSES ********** ?>
										
											<fieldset>
												<legend><?php echo LANG_FIN_GENE_021; ?></legend>
												<table cellpadding="0" cellspacing="2" align="center">
													<tr>
														<td align="right"><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<select name="code_class" id="code_class" onChange="onchange_code_class()" <?php echo $disabled_cadre; ?>>
																<?php
																	$selected = '';
																	if($code_class == '' || $code_class == '0') {
																		$selected = 'selected="selected"';
																	}
																?>
																<option value="0" <?php echo $selected; ?>><?php echo ucfirst(LANG_FIN_GENE_025); ?></option>
																<?php
																// Verifier si on a au moins une classe
																if($classes->numRows() > 0) {
																	for($i=0; $i<$classes->numRows(); $i++) {
																		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																		$selected = '';
																		if($code_class == $ligne[0]) {
																			$selected = 'selected="selected"';
																		}
																?>
																<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
																<?php
																	}
																}
																?>
															</select>
														</td>
													</tr>
		
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
																<option value="" <?php echo $selected; ?>><?php echo ucfirst(LANG_FIN_GENE_025); ?></option>
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
														<td colspan="2" align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
														</td>
													</tr>
												</table>
												<br>
											</fieldset>
										</form>
									</td>
								</tr>
								<tr>
									<td valign="top" align="center">
									<?php
									// Verifier si on a au moins une classe
									if($baremes->numRows() > 0) {
										//echo $baremes->numRows();
									?>
										<p align="left">
											&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
										</p>
									
									
										<table width="75%" border="0" cellpadding="0" cellspacing="0" align="center">
									<?php
											for($i=0; $i<$baremes->numRows(); $i++) {
												$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
									?>
											<tr>
												<td align="left" nowrap="nowrap" colspan="3"><b><?php echo $ligne[1]; ?> : </b>&nbsp;&nbsp;(<?php echo LANG_FIN_CLAS_003; ?> : <?php echo $ligne[3]; ?> - <?php echo LANG_FIN_GENE_011; ?> : <?php echo $ligne[2]; ?>)</td>
											</tr>
											<tr>
												<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
												<td align="left">
									<?php
												// Rechercher la liste des frais du bareme courant
												$sql ="SELECT fb.type_frais_id, fb. montant, fb.optionnel , fb.lisse, tf.libelle  ";
												$sql.="FROM ".FIN_TAB_FRAIS_BAREME." fb ";
												$sql.="INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
												$sql.="WHERE fb.bareme_id = " . $ligne[0] . " ";
												$sql.="ORDER BY tf.libelle";
												$frais=execSql($sql);
									?>
													<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc">
														<tr bgcolor="#ffffff">
															<td align="left" valign="top"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
															<td align="right" valign="top"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
															<td align="center" valign="top"><b><?php echo LANG_FIN_GENE_012; ?></b></td>
															<td align="center" valign="top"><b><?php echo LANG_FIN_TFRA_014; ?></b></td>
														</tr>
														<?php
														for($j=0; $j<$frais->numRows(); $j++) {
															$res = $frais->fetchInto($ligne_frais, DB_FETCHMODE_DEFAULT, $j);
														?>
														<tr bgcolor="#ffffff">
															<td align="left" nowrap="nowrap"><?php echo $ligne_frais[4]; ?></td>
															<td align="right" nowrap="nowrap"><?php echo montant_depuis_bdd($ligne_frais[1], 2); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<?php
															if($ligne_frais[2] == '1') {
																$src = "./" . $g_chemin_relatif_module . "images/case_cocher_on.png";
															} else {
																$src = "./" . $g_chemin_relatif_module . "images/case_cocher_off.png";
															}
															?>
															<td align="center" nowrap="nowrap"><img src="<?php echo $src; ?>" border="0"></td>
															<?php
															if($ligne_frais[3] == '1') {
																$src = "./" . $g_chemin_relatif_module . "images/case_cocher_on.png";
															} else {
																$src = "./" . $g_chemin_relatif_module . "images/case_cocher_off.png";
															}
															?>
															<td align="center" nowrap="nowrap"><img src="<?php echo $src; ?>" border="0"></td>
														</tr>
														<?php
														}
														?>
													</table>
														
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
									<?php
											}
									?>
										</table>
									<?php
									} else {
										echo LANG_FIN_EBAR_001;
									}
									?>
	
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
									</td>
								</tr>
									
									
							</table>
						
						
						<?php //********** VALIDATION FORMULAIRES ********** ?>
				
				
						<?php //********** GESTION NAVIGATION ********** ?>
						
						<script language="javascript">
							var fenetre = null;
							var liste_fenetre = new Array();
							
							function onclick_annuler() {
								msg_util_attente_montrer(true);
								document.getElementById('formulaire_annuler').submit();
							}
							function onchange_code_class() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "reload_code_class";
								document.formulaire_criteres.submit();
							}
							function onchange_annee_scolaire() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "reload_annee_scolaire";
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
									liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/editions_baremes_rechercher.php?mode_affichage=impression&code_class=<?php echo urlencode($code_class); ?>&annee_scolaire=<?php echo urlencode($annee_scolaire); ?>','fenetre_editer_' + liste_fenetre.length,'width=800,height=600,resizable=yes,scrollbars=yes');
									liste_fenetre[liste_fenetre.length].focus();						
							}
							
							function onclic_export_excel() {
							document.getElementById('formulaire_export_excel').submit();
							}
							
							function onclick_fermer() {
								window.close();
							}
							
							function onclick_ouvrir_impession() {
								window.print();
							}
							
						</script>
						<form name="formulaire_modif" id="formulaire_modif" action="<?php echo $g_chemin_relatif_module; ?>type_frais_modif.php" method="post">
							<input type="hidden" name="type_frais_id" id="type_frais_id" value="0">
						</form>
						<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>editions.php" method="post">
						</form>
						<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>editions_baremes_rechercher_excel.php" method="post" target="">
							<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire?>">
							<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class?>">
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