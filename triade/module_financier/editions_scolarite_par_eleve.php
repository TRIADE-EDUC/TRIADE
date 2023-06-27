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
	$annee_scolaire = lire_parametre('annee_scolaire', annee_scolaire_courante(), 'REQUEST');
	$code_class = lire_parametre('code_class', 0, 'REQUEST');
	$ordre_tri = lire_parametre('ordre_tri', 'libelle_classe', 'REQUEST');
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	/*
	if($operation != "reload_annee_scolaire") {
		if($annee_scolaire == 'aucune') {
			$annee_scolaire = annee_scolaire_courante();
		}
	}
	*/
	//***************************************************************************
	
	
	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	if($annees_scolaires->numRows() > 0) {
		if($annee_scolaire == '') {
			$res = $annees_scolaires->fetchInto($ligne_annee, DB_FETCHMODE_DEFAULT, 0);
			$annee_scolaire = $ligne_annee[0];
		}
	}
	/*
	if($annees_scolaires->numRows() > 0) {
		if($annee_scolaire == '') {
			$annee_scolaire = annee_scolaire_courante();
			$annee_trouvee = false;
			for($i=0; $i<$annees_scolaires->numRows(); $i++) {
				$res = $annees_scolaires->fetchInto($ligne_annee, DB_FETCHMODE_DEFAULT, $i);
				if($ligne_annee[0] == $annee_scolaire) {
					$annee_trouvee = true;
				}
			}
			if(!$annee_trouvee) {
				$annee_scolaire = '';
			}
		}
	}
	*/
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="WHERE i.annee_scolaire = '" . $annee_scolaire . "' ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	// Initialisation sur changement d'annee scolaire
	if($operation == "reload_annee_scolaire") {
		$code_class = 0;
	}

	if($operation != '')
	{
	
		$tab_groupe_type = array();
				// Rechercher la liste des types de reglement
				$sql ="SELECT groupe_id, libelle ";
				$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
				$sql.="ORDER BY groupe_id";
				$groupe_type=execSql($sql);
				
				for($i=0; $i<$groupe_type->numRows(); $i++) {
					// Acces s l'enregistrement courant
					$res = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
					$tab_groupe_type[count($tab_groupe_type)] = array(
																			'groupe_id' => $ligne_groupe_type[0],
																			'libelle' => $ligne_groupe_type[1],
																			'total' => 0.0,
																			'reste_a_payer' => 0.0,
																			'encaisse' => 0.0
																);
				}
				
				
		// Rechercher la liste des inscriptions
		$sql ="SELECT c.code_class, c.libelle, e.elev_id, e.nom, e.prenom, i.inscription_id ";
		$sql.="FROM (".FIN_TAB_CLASSES." c ";
		$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class) ";
		$sql.="INNER JOIN ".FIN_TAB_ELEVES." e ON e.elev_id = i.elev_id ";
		$sql.="WHERE 1 = 1 ";
		if($annee_scolaire != '') {
			$sql.="AND i.annee_scolaire = '" . $annee_scolaire . "' ";
		}
		if($code_class != '0') {
			$sql.="AND i.code_class = " . $code_class . " ";
		}
		//$sql.="GROUP BY c.code_class, c.libelle ";
		if($ordre_tri == 'libelle_classe') {
			$sql.="ORDER BY c.libelle, e.nom, e.prenom";
		} else {
			$sql.="ORDER BY e.nom, e.prenom, c.libelle";
		}
		$inscriptions=execSql($sql);
	//echo $sql;
		$tab_classes = array();
		$code_class_courant = 0;
		$total_classes_general = 0;
		$total_eleves_general = 0;
		$total_scolarite_general = 0.0;
		$total_reste_a_payer_general = 0.0;
		$total_groupe = 0.0;
		for($i=0; $i<$inscriptions->numRows(); $i++) {
			// Acces s l'enregistrement courant
			$res = $inscriptions->fetchInto($ligne_inscription, DB_FETCHMODE_DEFAULT, $i);
			// Ajouter une nouvelle classe si le ID est different du precedent
			if($ligne_inscription[0] != $code_class_courant) {
				$total_classes_general++;
				$tab_classes[count($tab_classes)] = array(
											'code_class' => $ligne_inscription[0],
											'libelle_classe' => $ligne_inscription[1],
											'eleves' => array(),
											'total_scolarite' => 0.0,
											'total_reste_a_payer' => 0.0,
											'total_encaisse' => 0.0
										);
				$code_class_courant = $ligne_inscription[0];
			}
			$total_eleves_general++;
			$frais_pour_cet_eleve = inscription_total_frais($ligne_inscription[5]);
			$reste_a_payer_pour_cet_eleve = reglement_reste_a_payer('inscription', $ligne_inscription[5]);
			$encaisse_pour_cet_eleve = $frais_pour_cet_eleve - $reste_a_payer_pour_cet_eleve;
			
			$tab_classes[count($tab_classes) - 1]['eleves'][count($tab_classes[count($tab_classes) - 1]['eleves'])] = array(
								'elev_id' => $ligne_inscription[2],
								'nom' => $ligne_inscription[3],
								'prenom' => $ligne_inscription[4],
								'total_scolarite' => $frais_pour_cet_eleve,
								'total_reste_a_payer' => $reste_a_payer_pour_cet_eleve,
								'total_encaisse' => $encaisse_pour_cet_eleve
							);
			
			
			$sql1 ="SELECT groupe_id, echeancier_id, montant ";
			$sql1.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
			$sql1.="WHERE inscription_id = $ligne_inscription[5] ";
			$sql1.="ORDER BY echeancier_id ";
			$groupes=execSql($sql1);
			// echo $sql1;
			
			$pourcentage = 0.0;
			if($reste_a_payer_pour_cet_eleve != 0){
				$pourcentage = $reste_a_payer_pour_cet_eleve /$frais_pour_cet_eleve;
			}
			for($v=0; $v <$groupes->numRows();$v++)
			{
				$resg = $groupes->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
				
				for($l=0; $l< count($tab_groupe_type);$l++)
				{
					if($tab_groupe_type[$l]['groupe_id'] == $ligne_groupe[0]) {
						
						$tab_groupe_type[$l]['total'] += $ligne_groupe[2];
						$total_groupe += $ligne_groupe[2];
						if($pourcentage != 0)
						{
							$temp = $ligne_groupe[2] * $pourcentage;
							$tab_groupe_type[$l]['reste_a_payer'] += $temp;
							$tab_groupe_type[$l]['encaisse'] += ($ligne_groupe[2] - $temp);
						}
						else
						{
							$tab_groupe_type[$l]['encaisse'] += $ligne_groupe[2];					
						}
					}
				}
			}
			
			
			$tab_classes[count($tab_classes) - 1]['total_scolarite'] += $frais_pour_cet_eleve;
			$total_scolarite_general += $frais_pour_cet_eleve;

			$tab_classes[count($tab_classes) - 1]['total_reste_a_payer'] += $reste_a_payer_pour_cet_eleve;
			$total_reste_a_payer_general += $reste_a_payer_pour_cet_eleve;
			
			$tab_classes[count($tab_classes) - 1]['total_encaisse'] += $encaisse_pour_cet_eleve;
			$total_encaisse_general += $encaisse_pour_cet_eleve;
		
		}
		for($j=0;$j<count($tab_groupe_type);$j++)
		{
			$groupe_type_total += $tab_groupe_type[$j]['total'];
			$groupe_type_reste += $tab_groupe_type[$j]['reste_a_payer'];
			$groupe_type_encaisse += $tab_groupe_type[$j]['encaisse'];
		}
	}
	//echo "<pre>";
	//print_r($tab_classes);
	//echo "</pre>";
	//echo $sql;
	
	//echo $annee_scolaire;
	//echo "<br>";
	//echo $code_class;


	
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
		$largeur_cadre = 600;
		$alignement_cadre = 'center';
		$date_heure_impression = '(' . date('d/m/Y H:i:s') . ')';
		$disabled_cadre = 'disabled';
		$bordure_tableau_impession = '1';
		
		if($mode_affichage != 'impression') {
		?>
		
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		 //Verification droits acces groupe
		validerequete("2");
		?>

		
		<?php
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
		} else {
		?>
			<style type="text/css" media="print">
			.Cacher {
				display:;
			}
			
			.Montrer {
				display:none;
			}
			
			</style>
		<?php
		}
		?>

		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		<?php
		if($mode_affichage != 'impression') {
		?>
		<div align="<?php echo $alignement_cadre; ?>">
			<!-- TITRE ET CADRE CENTRAL -->
			<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" >
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_005; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
					</td>
				</tr>
				<tr id="cadreCentral0">
					<td valign="top" align="center">
		<?php
		} else {
		?>
			<!-- TITRE -->
			<table border="0" cellpadding="3" cellspacing="1" bgcolor="#0B3A0C" align="center">
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_005; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
					</td>
				</tr>
			</table>
		<?php
		}
		?>
					
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
														<td align="right"><?php echo LANG_FIN_GENE_011; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<?php
															if($annees_scolaires->numRows() > 0) {
															?>
																<select name="annee_scolaire" id="annee_scolaire" onChange="onchange_annee_scolaire()" <?php echo $disabled_cadre; ?>>
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
															<?php
															} else {
															?>
															<?php
															}
															?>
														</td>
													</tr>
													
													<?php
													if($annees_scolaires->numRows() > 0) {
													?>
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
																<option value="0" <?php echo $selected; ?>><?php echo ucfirst(LANG_FIN_GENE_062); ?></option>
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
													<?php
													if(false) {
													?>
													<tr>
														<td align="right"><?php echo LANG_FIN_GPRE_011; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<table cellspacing="0" cellpadding="0" border="0">
																<tr>
																	<td align="left">
																		<select name="ordre_tri" id="ordre_tri" <?php echo $disabled_cadre; ?>>
																			<?php
																				$selected = '';
																				if($ordre_tri == 'libelle_classe') {
																					$selected = 'selected="selected"';
																				}
																			?>
																			<option value="libelle_classe" <?php echo $selected; ?>><?php echo LANG_FIN_ESCO_001; ?></option>
																			<?php
																				$selected = '';
																				if($ordre_tri == 'nom_eleve') {
																					$selected = 'selected="selected"';
																				}
																			?>
																			<option value="nom_eleve" <?php echo $selected; ?>><?php echo LANG_FIN_ESCO_002; ?></option>
																		</select>																
																	</td>
																	<td colspan="4">&nbsp;</td>
																</tr>
															</table>
														</td>
													</tr>
													<?php
													}
													?>
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
													
													
													<tr>
														<td colspan="2" align="center">&nbsp;</td>
													</tr>
													<?php
													}
													?>
												</table>
												<br>
											</fieldset>
										</form>
									</td>
								</tr>
							</table>
							<?php
							if($mode_affichage != 'impression') {
							?>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td valign="top" align="left" nowrap="nowrap">&nbsp;
										
									</td>
								</tr>
								<tr>
									<td valign="top" align="center">
							<?php
							}
						
							?>
										
											<!--
											<tr bgcolor="#ffffff">
												<td align="right" valign="top"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
											</tr>
											-->
											<?php
											if($annees_scolaires->numRows() > 0) {
											
												// Verifier si on a au moins une ligne de donnees
												if(count($tab_classes) > 0) {
											?>
											<p align="left">
												&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
											</p>
											<?php
													for($i=0; $i<count($tab_classes); $i++) {
													//for($i=0; $i<5; $i++) {
														// Saut de page a l'impression
														if($i > 0 && $mode_affichage == 'impression') {
											?>
											<!--
											<p align="center" class="Cacher" STYLE="page-break-before: always">
											Pour que le saut de page fonctionne sur IE7, Firefox, ...
											-->
											<div style="page-break-after: always; height:1px;">&nbsp;</div>
											<?php
														}
											?>
											
											<table cellspacing="0" cellpadding="03" border="0" width="100%">

												<tr>
													<td align="left" nowrap="nowrap" colspan="2"><b><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;<?php echo ucfirst($tab_classes[$i]['libelle_classe']); ?></b></td>
												</tr>
												<tr>
													<td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;</td>
													<td align="left" nowrap="nowrap" width="90%">
														<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" width="70%" align="left">
															<tr bgcolor="#ffffff">
																<td align="left" valign="top"><b><?php echo LANG_FIN_ELEV_002; ?></b></td>
																<td align="right" valign="top"><b><?php echo LANG_FIN_GENE_036; ?></b></td>
																<td align="right" valign="top"><b><?php echo LANG_FIN_EENC_008; ?></b></td>
																<td align="right" valign="top"><b><?php echo LANG_FIN_GENE_034; ?></b></td>
															</tr>
															<?php
															for($j=0; $j<count($tab_classes[$i]['eleves']); $j++) {
															//for($j=0; $j<5; $j++) {
															?>
															<tr bgcolor="#ffffff">
																<td align="left" nowrap="nowrap" width="60%">
																	<?php echo strtoupper($tab_classes[$i]['eleves'][$j]['nom']); ?>&nbsp;<?php echo ucfirst($tab_classes[$i]['eleves'][$j]['prenom']); ?>
																</td>
																<td align="right" nowrap="nowrap" width="40%">
																	<?php echo montant_depuis_bdd($tab_classes[$i]['eleves'][$j]['total_scolarite']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																</td>
																<td align="right" nowrap="nowrap" width="40%">
																	<?php echo montant_depuis_bdd($tab_classes[$i]['eleves'][$j]['total_encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																</td>
																<td align="right" nowrap="nowrap" width="40%">
																	<?php echo montant_depuis_bdd($tab_classes[$i]['eleves'][$j]['total_reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																</td>
															</tr>
															<?php
															}
														
															?>
														</table>
													</td>
												</tr>
												<tr>
													<td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;</td>
													<td align="left" nowrap="nowrap" width="90%">
														<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="left">
															<tr bgcolor="#ffffff">
																<td align="right"><?php echo LANG_FIN_ESCO_003; ?> : &nbsp;</td>
																<td align="right"><?php echo count($tab_classes[$i]['eleves']); ?></td>
															</tr>
															<tr bgcolor="#ffffff">
																<td align="right"><?php echo LANG_FIN_ESCO_004; ?> : &nbsp;</td>
																<td align="right"><?php echo montant_depuis_bdd($tab_classes[$i]['total_scolarite']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															</tr>
															<tr bgcolor="#ffffff">
																<td align="right"><?php echo LANG_FIN_EENC_008; ?> : &nbsp;</td>
																<td align="right"><?php echo montant_depuis_bdd($tab_classes[$i]['total_encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															</tr>
															<tr bgcolor="#ffffff">
																<td align="right"><?php echo LANG_FIN_GENE_034; ?> : &nbsp;</td>
																<td align="right"><?php echo montant_depuis_bdd($tab_classes[$i]['total_reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															</tr>
														</table>
													</td>
												</tr>												
											</table>
											
											<br><br>
											<?php } 
												} else {
											?>
											<?php //echo LANG_FIN_EINS_001; ?>
											<?php
												
												}
											} else {
											?>
											<?php //echo LANG_FIN_EINS_001; ?>
											<?php
											}
								
											?>

							<?php
							if($mode_affichage != 'impression') {
							?>
	
									</td>
								</tr>
							</table>
							<?php
							}
							?>
							<?php
							// Saut de page a l'impression
							if($mode_affichage == 'impression') {
							?>
							<!--
							<p align="center" class="Cacher" STYLE="page-break-before: always">
							Pour que le saut de page fonctionne sur IE7, Firefox, ...
							-->
							<div style="page-break-after: always; height:1px;">&nbsp;</div>
							<?php
							}
							if($operation != '')
							{	
								?>
								<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
									<tr bgcolor="#ffffff">
										<td align="right"><b><?php echo LANG_FIN_ESCO_008; ?> : &nbsp;</b></td>
										<td align="right"><?php echo $total_classes_general; ?></td>
									</tr>
									<tr bgcolor="#ffffff">
										<td align="right"><b><?php echo LANG_FIN_ESCO_005; ?> : &nbsp;</b></td>
										<td align="right"><?php echo $total_eleves_general; ?></td>
									</tr>
									<tr bgcolor="#ffffff">
										<td align="right"><b><?php echo LANG_FIN_ESCO_006; ?> : &nbsp;</b></td>
										<td align="right"><?php echo montant_depuis_bdd($total_scolarite_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									</tr>
									<tr bgcolor="#ffffff">
										<td align="right"><b><?php echo LANG_FIN_EENC_008; ?> : &nbsp;</b></td>
										<td align="right"><?php echo montant_depuis_bdd($total_encaisse_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									</tr>
									<tr bgcolor="#ffffff">
										<td align="right"><b><?php echo LANG_FIN_ESCO_007; ?> : &nbsp;</b></td>
										<td align="right"><?php echo montant_depuis_bdd($total_reste_a_payer_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									</tr>
								</table>
								<br>
								<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
											<tr bgcolor="#ffffff">
												<td align="right">&nbsp;</td>
												<td align="right"><b><?php echo LANG_FIN_EENC_010; ?></b></td>
												<td align="right"><b><?php echo LANG_FIN_EENC_007; ?></b></td>
												<td align="right"><b><?php echo LANG_FIN_EENC_008; ?></b></td>
											</tr>
											<?php
											for($k=0; $k<count($tab_groupe_type); $k++) {
											?>
											<tr bgcolor="#ffffff">
												<td align="right"><b><?php echo $tab_groupe_type[$k]['libelle']; ?></b></td>
												<td align="right"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['total']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
												<td align="right"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
												<td align="right"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											</tr>
											<?php
											}
											?>
											<tr bgcolor="#ffffff">
												<td align="right">&nbsp;</td>
												<td align="right"><b><?php echo montant_depuis_bdd($groupe_type_total); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
												<td align="right"><b><?php echo montant_depuis_bdd($groupe_type_reste); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
												<td align="right"><b><?php echo montant_depuis_bdd($groupe_type_encaisse); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
											</tr>
											</table>
												
		
								<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
				
									<?php //********** MESSAGES UTILISATEUR ********** ?>
									
									<tr>
										<td align="center">&nbsp;</td>
									</tr>
									<tr>
										<td align="center"></td>
									</tr>
						
						
									<?php //********** BOUTONS ********** 
								?>
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
							
						
						<?php } //********** VALIDATION FORMULAIRES ********** ?>
				
				
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
									liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/editions_scolarite_par_eleve.php?mode_affichage=impression&operation=rechercher&code_class=<?php echo urlencode($code_class); ?>&annee_scolaire=<?php echo urlencode($annee_scolaire); ?>&ordre_tri=<?php echo urlencode($ordre_tri); ?>','fenetre_editer_' + liste_fenetre.length,'width=600,height=600,resizable=yes,scrollbars=yes,toolbar=no, menubar=yes');
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
						
						<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>editions_scolarite_par_eleve_excel.php" method="post" target="">
							<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire?>">
							<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class?>">
							<input type="hidden" name="ordre_tri" id="ordre_tri" value="">
						</form>
		<?php
		if($mode_affichage != 'impression') {
		?>
						
					</td>
				</tr>
			</table>
		</div>
		<?php
		}
		?>
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
