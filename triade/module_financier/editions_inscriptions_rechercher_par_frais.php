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
	$type_frais_id = lire_parametre('type_frais_id', array(), 'REQUEST');
	$type_frais_id1 = lire_parametre('type_frais_id1', array(), 'REQUEST');
	if($type_frais_id1 != array())
	{
		$type_frais_id = explode(',',$type_frais_id1);
	}
	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Initialisation sur changement de classe
	if($operation == "reload_code_class") {
		$annee_scolaire = '';
	}

	//***************************************************************************
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	//echo $sql;
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	// 20100708 - AP : Afficher la liste dea annees meme si aucune classe n'est selectionnee
	if($code_class != "0") {
		$sql.="WHERE code_class = " . $code_class . " ";
	}
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;

	// Rechercher les types de frais
	$sql ="SELECT type_frais_id, libelle ";
	$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ";
	$sql.="ORDER BY libelle";
	$types_frais=execSql($sql);
	
	//echo $annee_scolaire;
	$total_inscriptions_trouvees = 0;
	if($operation == 'rechercher' || $operation == 'reload_code_class' || $operation == 'reload_annee_scolaire') {
	
		// Rechercher les inscriptions
		$sql ="SELECT i.inscription_id, i.annee_scolaire, c.code_class, c.libelle, e.elev_id, e.nom, e.prenom ";
		$sql.="FROM ((".FIN_TAB_INSCRIPTIONS." i ";
		$sql.="INNER JOIN ".FIN_TAB_ELEVES." e ON i.elev_id = e.elev_id) ";
		$sql.="INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class) ";
		//$sql.="INNER JOIN ".FIN_TAB_FRAIS_INSCRIPTION." fi ON i.inscription_id = fi.inscription_id ";
		$sql.="WHERE 1 = 1 ";
		if($code_class != '0' && $code_class != '') {
			$sql.="AND i.code_class = " . $code_class . " ";
		}
		if($annee_scolaire != '') {
			$sql.="AND i.annee_scolaire = '" . $annee_scolaire . "' ";
		}
		//if(count($type_frais_id) > 0) {
		//	$sql.="AND fi.type_frais_id IN (" . implode(',', $type_frais_id) . ") ";
		//}
		$sql.="GROUP BY i.annee_scolaire, c.code_class, c.libelle, e.elev_id, e.nom, e.prenom ";
		$sql.="ORDER BY i.annee_scolaire, c.libelle, e.nom, e.prenom ";
		$inscriptions=execSql($sql);
		//echo $sql;
		
		$tab_inscriptions = array();
	
		// print_r($type_frais_id);
		// Verifier pour chaque inscription, si tous ses frais font partie de ceux selectionnes
		for($i=0; $i<$inscriptions->numRows(); $i++) {
			$res = $inscriptions->fetchInto($ligne_inscription, DB_FETCHMODE_DEFAULT, $i);
			
			$tous_les_frais_trouves = true;
			
			if(count($type_frais_id) > 0) {
			
				// Rechercher les types de frais de l'inscription
				$sql ="SELECT type_frais_id ";
				$sql.="FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
				$sql.="WHERE inscription_id = " . $ligne_inscription[0] . " ";
				$sql.="AND ((optionnel = 0) OR (optionnel = 1 AND selectionne = 1)) ";
				$frais_de_inscription=execSql($sql);
				
				// Stocker la liste des frais de l'inscription courante
				$tab_frais_de_cette_inscription = array();
				for($j=0; $j<$frais_de_inscription->numRows(); $j++) {
					$res = $frais_de_inscription->fetchInto($ligne_un_frais, DB_FETCHMODE_DEFAULT, $j);
					$tab_frais_de_cette_inscription[count($tab_frais_de_cette_inscription)] = $ligne_un_frais[0];
				}
				
				// Verifier que tous les frais selectionnes sont dans les frais de l'inscription
				$total_frais_selectionnes_trouves = 0;
				for($k=0; $k<count($type_frais_id); $k++) {
					$ce_frais_trouve = false;
					for($l=0; $l<count($tab_frais_de_cette_inscription); $l++) {
						if($type_frais_id[$k] == $tab_frais_de_cette_inscription[$l]) {
							$ce_frais_trouve = true;
							break;
						}
					}
					if($ce_frais_trouve) {
						$total_frais_selectionnes_trouves++;
					}
				}
	
				if($total_frais_selectionnes_trouves != count($type_frais_id)) {
					$tous_les_frais_trouves = false;
				}
	
			}
				
			// guarder l'inscription si tous ses frais sont valides
			if($tous_les_frais_trouves) {
				$tab_inscriptions[count($tab_inscriptions)] = array (
												'inscription_id' => $ligne_inscription[0],
												'annee_scolaire' => $ligne_inscription[1],
												'code_class' => $ligne_inscription[2],
												'libelle' => $ligne_inscription[3],
												'elev_id' => $ligne_inscription[4],
												'nom' => $ligne_inscription[5],
												'prenom' => $ligne_inscription[6]
											);
			}
				
		//echo $sql;
		}
		$total_inscriptions_trouvees = count($tab_inscriptions);
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
			<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_003; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
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
									<td colspan="2" valign="top" align="center">
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
																<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo ucfirst($ligne[1]); ?></option>
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
														<td align="right" valign="top"><?php echo LANG_FIN_FBAR_004; ?>&nbsp;<small>(<?php echo count($type_frais_id); ?>)</small>&nbsp;:</td>
														<td align="left">
															<div style="height:100px; overflow:auto">
																<?php
																//echo $type_frais_id;
																// Verifier si on a au moins un type de frais
																if($types_frais->numRows() > 0) {
																?>
																<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc">
																<?php
																	for($i=0; $i<$types_frais->numRows(); $i++) {
																		$res = $types_frais->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																?>
																	<tr bgcolor="#ffffff">
																		<td align="left">
																			<?php
																			$checked = '';
																			//if(strpos($liste_id, $ligne[0]) !== false) {
																			if(in_array($ligne[0], $type_frais_id)) {
																				$checked = 'checked';
																			}
																			?>
																			<input type="checkbox" name="type_frais_id[]" id="type_frais_id[]" value="<?php echo $ligne[0]; ?>" <?php echo $checked; ?> <?php echo $disabled_cadre; ?>>
																		</td>
																		<td align="left"><?php echo $ligne[1]; ?></td>
																	</tr>
																<?php
																	}
																?>
																</table>
																<?php
																} else 
																{
																?>
																<input type="hidden" name="type_frais_id" id="type_frais_id" value="">
																<?php
																}
																?>
															</div>
														</td>
													</tr>
													
													<tr>
														<td colspan="2" align="center">&nbsp;</td>
													</tr>
													
													<tr>
														<td colspan="2" align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_rechercher();" <?php echo $disabled_cadre; ?>>
														</td>
														<td align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
														</td>
													</tr>
													
												</table>
												<br>
											</fieldset>
										</form>
									</td>
								</tr>
								
								<tr><?php
									if($mode_affichage != 'impression') {?>
									<td>
										<p align="left">
										&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
										</p>
									</td>
									<td valign="top" align="right" nowrap="nowrap">
										<?php echo $total_inscriptions_trouvees; ?> <?php echo LANG_FIN_EINS_003; ?>
									</td>
									<?php } else { ?> 
									<td valign="top" align="left" nowrap="nowrap">
										<?php echo $total_inscriptions_trouvees; ?> <?php echo LANG_FIN_EINS_003; ?>
									</td>
									<?php } ?> 
								</tr>
								<tr>
									<td valign="top" align="left" nowrap="nowrap">&nbsp;
										
									</td>
								</tr>
								<tr>
									<td colspan="2" valign="top" align="center">
										<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc">
											<tr bgcolor="#ffffff">
												<td align="left" valign="top"><b><?php echo LANG_FIN_GENE_011; ?></b></td>
												<td align="right" valign="top"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
												<td align="center" valign="top"><b><?php echo LANG_FIN_ELEV_005; ?></b></td>
												<td align="center" valign="top"><b><?php echo LANG_FIN_ELEV_004; ?></b></td>
											</tr>
											<?php
											if($operation == 'rechercher' || $operation == 'reload_code_class' || $operation == 'reload_annee_scolaire') {
											
												// Verifier si on a au moins une ligne de donnees
												if($inscriptions->numRows() > 0) {
											?>
											<?php
													for($i=0; $i<count($tab_inscriptions); $i++) {
											?>
											<tr bgcolor="#ffffff">
												<td align="left" nowrap="nowrap"><?php echo $tab_inscriptions[$i]['annee_scolaire']; ?></td>
												<td align="left" nowrap="nowrap"><?php echo ucfirst($tab_inscriptions[$i]['libelle']); ?></td>
												<td align="left" nowrap="nowrap"><?php echo ucfirst($tab_inscriptions[$i]['nom']); ?></td>
												<td align="left" nowrap="nowrap"><?php echo ucfirst($tab_inscriptions[$i]['prenom']); ?></td>											
											</tr>
											<?php
													}
												} else {
											?>
											<tr bgcolor="#ffffff">
												<td align="left" colspan="4"><?php echo LANG_FIN_EINS_001; ?></td>
											</tr>
											<?php
												
												}
											} else {
											?>
											<tr bgcolor="#ffffff">
												<td align="left" colspan="4"><?php echo LANG_FIN_EINS_002; ?></td>
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
									<td colspan="2" align="center">
										<a name="MESSAGE"></a>
										<?php 
										msg_util_afficher();
										msg_util_attente_init(); 
										?>
										</td>
								</tr>
					
					
								<?php //********** BOUTONS ********** ?>
								
								<tr>
									<td colspan="2" align="center">
										<table border="0" align="center" cellpadding="4" cellspacing="0">
											<?php
											if($mode_affichage != 'impression') {
											?>									
											<tr>
												<td align="center">
												<?php print_r(); ?>
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
									liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/editions_inscriptions_rechercher_par_frais.php?mode_affichage=impression&operation=rechercher&code_class=<?php echo urlencode($code_class); ?>&annee_scolaire=<?php echo urlencode($annee_scolaire); ?>&type_frais_id1=<?php echo implode(',',$type_frais_id); ?>','fenetre_editer_' + liste_fenetre.length,'width=800,height=600,resizable=yes,scrollbars=yes');
									
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
						<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>editions_inscriptions_rechercher_par_frais_excel.php" method="post" target="">
							<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire?>">
							<input type="hidden" name="type_frais_id" id="type_frais_id" value="<?php echo implode(',',$type_frais_id)?>">
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