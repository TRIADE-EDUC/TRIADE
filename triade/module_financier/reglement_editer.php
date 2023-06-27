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
	$operation = lire_parametre('operation', '', 'GET');
	$echeancier_id = lire_parametre('echeancier_id', 0, 'GET');
	$mode = lire_parametre('mode', 'voir', 'GET');
	
	$reglements = lire_parametre('reglements', 0, 'GET');
	$nouveau_reglement = lire_parametre('nouveau_reglement', 0, 'GET');
	$nouveau_reglement_date_reglement = lire_parametre('nouveau_reglement_date_reglement', '', 'GET');
	$nouveau_reglement_libelle = lire_parametre('nouveau_reglement_libelle', '', 'GET');
	$nouveau_reglement_montant = lire_parametre('nouveau_reglement_montant', 0, 'GET');
	$nouveau_reglement_type_reglement_id = lire_parametre('nouveau_reglement_type_reglement_id', 0, 'GET');
	$nouveau_reglement_realise = lire_parametre('nouveau_reglement_realise', 0, 'GET');
	$nouveau_reglement_commentaire = lire_parametre('nouveau_reglement_commentaire', '', 'GET');
	$nouveau_reglement_numero_cheque = lire_parametre('nouveau_reglement_numero_cheque', '', 'GET');
	$nouveau_reglement_numero_bordereau = lire_parametre('nouveau_reglement_numero_bordereau', '', 'GET');
	
	//***************************************************************************

	//*************** TRAITER L'OPERATION DEMANDEE ****************
	$rafraichir_parent = '';
	
	switch($operation) {
		case "enregistrer":
		
			// Enregistrement des modifications pour les reglements existants
			for($i=1; $i<=$reglements; $i++) {
				// Recuperer les infos du reglement
				$reglement_id = lire_parametre('reglement_'.$i.'_id', 0, 'GET');
				$reglement_date_reglement = lire_parametre('reglement_'.$i.'_date_reglement', '', 'GET');
				$reglement_libelle = lire_parametre('reglement_'.$i.'_libelle', '', 'GET');
				$reglement_montant = lire_parametre('reglement_'.$i.'_montant', 0, 'GET');
				$reglement_type_reglement_id = lire_parametre('reglement_'.$i.'_type_reglement_id', 0, 'GET');
				$reglement_realise = lire_parametre('reglement_'.$i.'_realise', 0, 'GET');
				$reglement_commentaire = lire_parametre('reglement_'.$i.'_commentaire', '', 'GET');
				$reglement_numero_cheque = lire_parametre('reglement_'.$i.'_numero_cheque', '', 'GET');
				$reglement_numero_bordereau = lire_parametre('reglement_'.$i.'_numero_bordereau', '', 'GET');

				$sql= "UPDATE ".FIN_TAB_REGLEMENT." ";
				$sql.="SET date_reglement = '" . date_vers_bdd($reglement_date_reglement) . "' ";
				$sql.=",libelle = '" . esc($reglement_libelle) . "' ";
				$sql.=",montant = " . montant_vers_bdd(esc($reglement_montant)) . " ";
				$sql.=",type_reglement_id = " . $reglement_type_reglement_id . " ";
				$sql.=",realise = " . $reglement_realise . " ";
				$sql.=",commentaire = '" . esc($reglement_commentaire) . "' ";
				$sql.=",numero_cheque = '" . esc($reglement_numero_cheque) . "' ";
				$sql.=",numero_bordereau = '" . esc($reglement_numero_bordereau) . "' ";
				$sql.="WHERE reglement_id = $reglement_id ";
				//echo $sql . '<br>';
				$res=execSql($sql);
			
			}
			
		
			// Ajouter du reglement si necessaire
			if($nouveau_reglement == 1) {
				$sql= "INSERT INTO ".FIN_TAB_REGLEMENT." (echeancier_id, libelle, date_reglement, montant, type_reglement_id, realise, commentaire, date_enregistrement, numero_cheque, numero_bordereau) ";
				$sql.="VALUES(";
				$sql.="".$echeancier_id.", ";
				$sql.="'".esc($nouveau_reglement_libelle)."', ";
				$sql.="'".date_vers_bdd($nouveau_reglement_date_reglement)."', ";
				$sql.="".montant_vers_bdd(esc($nouveau_reglement_montant)).", ";
				$sql.="".$nouveau_reglement_type_reglement_id.", ";
				$sql.="".$nouveau_reglement_realise.", ";
				$sql.="'".esc($nouveau_reglement_commentaire)."', ";
				$sql.="'".date("Y-m-d H:i:s")."', ";
				$sql.="'".esc($nouveau_reglement_numero_cheque)."', ";
				$sql.="'".esc($nouveau_reglement_numero_bordereau)."' ";
				$sql.="); ";
				//echo $sql;
				$res=execSql($sql);
			}
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';

			break;
		case "ajout":
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			break;
		case "supp":
			$rafraichir_parent = 'oui';
			$frais_bareme_id = 0;
			break;
	}
	//***************************************************************************
	
	
	// Rechercher l'echeance
	$sql ="SELECT echeancier_id, inscription_id, date_echeance, montant, impaye, type_reglement_id ";
	$sql.="FROM ".FIN_TAB_ECHEANCIER." ";
	$sql.="WHERE echeancier_id = $echeancier_id ";
	//echo $sql;
	$echeance=execSql($sql);

	if($echeance->numRows() > 0) {
		// Recuperer les infos de l'echeance
		$infos_echeance = $echeance->fetchRow();
		
		// Rechercher le type de reglement
		$sql ="SELECT type_reglement_id, libelle ";
		$sql.="FROM ".FIN_TAB_TYPE_REGLEMENT." ";
		$sql.="WHERE type_reglement_id = " . $infos_echeance[5];
		//echo $sql;
		$type_reglement=execSql($sql);
		
		if($type_reglement->numRows() > 0) {
			// Recuperer les infos du type de reglement
			$infos_type_reglement = $type_reglement->fetchRow();

			// Rechercher les reglements
			$sql ="SELECT reglement_id, echeancier_id, libelle, date_reglement, montant, type_reglement_id, realise, commentaire, date_enregistrement, numero_cheque, numero_bordereau ";
			$sql.="FROM ".FIN_TAB_REGLEMENT." ";
			$sql.="WHERE echeancier_id = $echeancier_id ";
			$sql.="ORDER BY date_reglement ";
			//echo $sql;
			$reglements=execSql($sql);
			
			// Rechercher les type de reglements
			$sql  = "SELECT type_reglement_id, libelle ";
			$sql .= "FROM ".FIN_TAB_TYPE_REGLEMENT." e ";
			$sql .= "ORDER BY libelle ASC";
			//echo $sql;
			$types_reglement=execSql($sql);
			
		}

	}

	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	if($echeance->numRows() == 0) {
		msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
	}
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
		<META http-equiv="CacheControl" content = "no-cache">
		<META http-equiv="pragma" content = "no-cache">
		<META http-equiv="expires" content = -1>
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
		<title><?php echo LANG_FIN_REGL_003; ?></title>
	</head>
	
	<body id="bodyfond2" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
				
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		// Verification droits acces groupe
		validerequete("2");
		?>
		
		
		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>
		
		<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
			<tr>
				<td align="center">&nbsp;</td>
			</tr>
			<tr>
				<td align="center">
					<b><font class="T2"><?php echo LANG_FIN_REGL_003; ?></font></b>
				</td>
			</tr>
			<tr>
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" method="get" action="<?php echo url_script(); ?>" onSubmit="">
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
			
							<?php
							
							include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");
							
							$montant_echeance = 0.0;
							
							if($echeance->numRows() > 0) {
							?>
							<input type="hidden" name="operation" id="operation" value="">
							<input type="hidden" name="echeancier_id" id="echeancier_id" value="<?php echo $echeancier_id; ?>">
							<input type="hidden" name="mode" id="mode" value="<?php echo $mode; ?>">
							<input type="hidden" name="montant_echeance" id="montant_echeance" value="<?php echo $infos_echeance[3]; ?>">
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									
									
									<?php
									// ***************** INFORMATIONS DE L'ECHENANCE **************
									?>
									
									<fieldset style="z-index:5;">
										<legend><?php echo LANG_FIN_ECHE_004; ?></legend>
										<table cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td align="right"><b><?php echo LANG_FIN_GENE_030; ?>&nbsp;:&nbsp;</b></td>
												<td align="left"><?php echo date_depuis_bdd($infos_echeance[2]); ?></td>
											</tr>
											<tr>
												<td align="right"><b><?php echo LANG_FIN_GENE_013; ?>&nbsp;:&nbsp;</b></td>
												<?php
												$montant_echeance = $infos_echeance[3];
												?>
												<td align="left" nowrap="nowrap"><?php echo montant_depuis_bdd($infos_echeance[3], 2, ',', ' '); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											</tr>
											<?php
											if($type_reglement->numRows() > 0) {
											?>
											<tr>
												<td align="right"><b><?php echo LANG_FIN_TREG_015; ?>&nbsp;:&nbsp;</b></td>
												<td align="left"><?php echo $infos_type_reglement[1]; ?></td>
											</tr>
											<?php
											}
											?>
										</table>
										
										
									</fieldset>
									
									
									<?php
									// ***************** LISTE DES REGLEMENT EXISTANTS **************
									?>
									
									<br>
									<fieldset id="fieldset_reglements" style="z-index:4;">
										<legend><?php echo LANG_FIN_REGL_001; ?></legend>
										
										<table cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td align="center">
													<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" align="center">
														<tr bgcolor="#ffffff">
															<td align="right" nowrap="nowrap"><b>#</b></td>
															<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_030; ?></b></td>
															<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
															<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_TREG_015; ?></b></td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_REGL_020; ?></b></td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_REGL_021; ?></b></td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_038; ?></b></td>
															<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_039; ?></b></td>
														</tr>
														<?php
														$montant_reglements = 0.0;
														if($reglements->numRows() > 0) {
															for($i=0; $i<$reglements->numRows(); $i++) {
																$ligne = $reglements->fetchRow();
														
																if($ligne[6] == 1) {
																	$montant_reglements += $ligne[4];
																}
														?>
														<input type="hidden" name="reglement_<?php echo ($i+1); ?>_id" id="reglement_<?php echo $i+1; ?>_id" value="<?php echo $ligne[0]; ?>">
														<tr bgcolor="#ffffff">
															<td align="right" valign="top"><?php echo $i+1; ?></td>
															<td align="left" nowrap="nowrap" valign="top">
																<table cellspacing="0" cellpadding="0" border="0">
																	<tr>
																		<td align="left">
																			<input type="text" name="reglement_<?php echo ($i+1); ?>_date_reglement" id="reglement_<?php echo ($i+1); ?>_date_reglement" size="10" maxlength="10" value="<?php echo date_depuis_bdd($ligne[3]); ?>" readonly="">
																		</td>
																		<td>&nbsp;</td>
																		<td align="left">
																			<?php
																			calendarDim("div_reglement_" . ($i+1) . "_date_reglement","document.formulaire.reglement_" . ($i+1) . "_date_reglement",$_SESSION["langue"], "0", "0", 'fieldset_reglements', 'null', 'null');	
																			?>
																		</td>
																	</tr>
																</table>
																			
															</td>
															<td align="left" nowrap="nowrap" valign="top">
																<input type="text" name="reglement_<?php echo ($i+1); ?>_libelle" id="reglement_<?php echo ($i+1); ?>_libelle" size="20" maxlength="64" value="<?php echo $ligne[2]; ?>">
															</td>
															<?php
															$valeur = $ligne[4];
															$valeur = str_replace('.', ',', $valeur);
															$pos = strpos($valeur, ',');
															if($pos === false) {
																$valeur = $valeur . ",00";
															}
															?>
															<td align="right" nowrap="nowrap" valign="top">
																<input type="text" name="reglement_<?php echo ($i+1); ?>_montant" id="reglement_<?php echo ($i+1); ?>_montant" size="8" maxlength="12" value="<?php echo $valeur; ?>" style="text-align:right;" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019; ?>
															</td>
															<td align="left" nowrap="nowrap" valign="top">
																<select name="reglement_<?php echo ($i+1); ?>_type_reglement_id" id="reglement_<?php echo ($i+1); ?>_type_reglement_id" onChange="onchange_type_reglement_id(<?php echo ($i+1); ?>)"  >
																	<?php
																	for($j=0; $j<$types_reglement->numRows(); $j++) {
																		$res = $types_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $j);
																		$selected = '';
																		if($ligne[5] == $ligne_type_reglement[0]) {
																			$selected = 'selected="selected"';
																		}
																	?>
																	<option value="<?php echo $ligne_type_reglement[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_type_reglement[1]; ?></option>
																	<?php
																	}
																	?>
																</select>
															</td>
															<td align="center" nowrap="nowrap" valign="top">
																<?php
																$disabled = '';
																if($ligne[5] != $g_tab_type_reglement_id['cheque']) {
																	$disabled = 'disabled="disabled"';
																}
																$valeur = $ligne[9];
																?>
																<input type="text" name="reglement_<?php echo ($i+1); ?>_numero_cheque" id="reglement_<?php echo ($i+1); ?>_numero_cheque" size="10" maxlength="10" value="<?php echo $valeur; ?>" <?php echo $disabled; ?>>
															</td>
															<td align="center" nowrap="nowrap" valign="top">
																<?php
																$disabled = '';
																if($ligne[5] != $g_tab_type_reglement_id['cheque'] && $ligne[5] != $g_tab_type_reglement_id['espece']) {
																	$disabled = 'disabled="disabled"';
																}
																$valeur = $ligne[10];
																?>
																<input type="text" name="reglement_<?php echo ($i+1); ?>_numero_bordereau" id="reglement_<?php echo ($i+1); ?>_numero_bordereau" size="16" maxlength="32" value="<?php echo $valeur; ?>" <?php echo $disabled; ?>>
															</td>
															<td align="center" nowrap="nowrap" valign="top">
																<?php
																$valeur = '';
																if($ligne[6] == 1) {
																	$valeur = 'checked="checked"';
																}
																?>
																<input type="checkbox" name="reglement_<?php echo ($i+1); ?>_realise" id="reglement_<?php echo ($i+1); ?>_realise" value="1" <?php echo $valeur; ?>>
															</td>
															<td align="left" nowrap="nowrap" valign="top">
																<textarea name="reglement_<?php echo ($i+1); ?>_commentaire" id="reglement_<?php echo ($i+1); ?>_commentaire" cols="30" rows="3"><?php echo $ligne[7]; ?></textarea>
															</td>
														</tr>
														<?php
															}
														} else {
														?>
														<tr bgcolor="#FFFFFF">
															<td align="left" colspan="9"><?php echo LANG_FIN_REGL_008;?></td>
														</tr>
														<?php
														}
														?>
													</table>
													<input type="hidden" name="montant_reglements" id="montant_reglements" value="<?php echo $montant_reglements; ?>">
													<input type="hidden" name="reglements" id="reglements" value="<?php echo $reglements->numRows(); ?>">

												</td>
											</tr>

									
											
											<?php
											// ***************** NOUVEAU REGLEMENT **************
											?>
									

											<?php
											if($mode == 'editer') {
											?>
											<tr>
												<td align="left">&nbsp;
													
												</td>
											</tr>
											<tr>
												<td align="left">
													[+] <a href="javascript:;" onClick="onclick_reglement_ajout();"><?php echo LANG_FIN_REGL_009;?></a>
												</td>
											</tr>
											<tr id="tr_ajouter" style="display:none">
												<td align="center">
													<div style="border:#0099CC dashed 1px; padding:5px; margin-top:5px;">
														<br>
														<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c">
															<tr bgcolor="#ffffff">
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_030; ?></b></td>
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
																<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_TREG_015; ?></b></td>
																<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_REGL_020; ?></b></td>
																<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_REGL_021; ?></b></td>
																<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_038; ?></b></td>
																<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_039; ?></b></td>
															</tr>
															<tr bgcolor="#ffffff">
																<td align="left" nowrap="nowrap" valign="top">
																	<table cellspacing="0" cellpadding="0" border="0">
																		<tr>
																			<td align="left">
																				<input type="text" name="nouveau_reglement_date_reglement" id="nouveau_reglement_date_reglement" size="10" maxlength="10" value="<?php echo date("d/m/Y"); ?>" readonly="">
																			</td>
																			<td>&nbsp;</td>
																			<td align="left">
																				<?php
																				calendarDim("div_nouveau_reglement_date_reglement","document.formulaire.nouveau_reglement_date_reglement",$_SESSION["langue"], "0", "0", 'fieldset_reglements', 'null', 'null');	
																				?>
																			</td>
																		</tr>
																	</table>
																				
																</td>
																<td align="left" nowrap="nowrap" valign="top">
																	<input type="text" name="nouveau_reglement_libelle" id="nouveau_reglement_libelle" size="20" maxlength="64" value="">
																</td>
																<?php
																$valeur = $montant_echeance - $montant_reglements;
																?>
																
																<td align="right" nowrap="nowrap" valign="top">
																	<input type="text" name="nouveau_reglement_montant" id="nouveau_reglement_montant" size="8" maxlength="12" value="<?php echo number_format($valeur, 2, ',', ''); ?>" style="text-align:right;" onBlur="formatage_montant(this);" >&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																</td>
																<td align="left" nowrap="nowrap" valign="top">
																	<select name="nouveau_reglement_type_reglement_id" id="nouveau_reglement_type_reglement_id" onChange="onchange_nouveau_reglement_type_reglement_id()"  >
																		<?php
																		for($j=0; $j<$types_reglement->numRows(); $j++) {
																			$res = $types_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $j);
																			$selected = '';
																			if($type_reglement->numRows() > 0) {
																				if($ligne_type_reglement[0] == $infos_type_reglement[0]) {
																					$selected = 'selected="selected"';
																				}
																			} else {
																				if($j == 0) {
																					$selected = 'selected="selected"';
																				}
																			}
																		?>
																		<option value="<?php echo $ligne_type_reglement[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_type_reglement[1]; ?></option>
																		<?php
																		}
																		?>
																	</select>
																</td>
																<td align="center" nowrap="nowrap" valign="top">
																	<input type="text" name="nouveau_reglement_numero_cheque" id="nouveau_reglement_numero_cheque" size="10" maxlength="10" value="" >
																<td align="center" nowrap="nowrap" valign="top">
																	<input type="text" name="nouveau_reglement_numero_bordereau" id="nouveau_reglement_numero_bordereau" size="16" maxlength="32" value="" >
																</td>
																<td align="center" nowrap="nowrap" valign="top">
																	<input type="checkbox" name="nouveau_reglement_realise" id="nouveau_reglement_realise" value="1" checked="checked">
																</td>
																<td align="left" nowrap="nowrap" valign="top">
																	<textarea name="nouveau_reglement_commentaire" id="nouveau_reglement_commentaire" cols="30" rows="3"></textarea>
																</td>
															</tr>
														</table>
																
														<br>
														
														<input type="button" class="button" value="<?php echo LANG_FIN_GENE_040; ?>" onClick="onclick_reglement_ajout_annuler();" >
														
														<br>
														<br>
													</div>
												</td>
											</tr>
																						
											<?php
											}
											?>
										</table>
										<input type="hidden" name="nouveau_reglement" id="nouveau_reglement" value="0">

									</fieldset>
									
								</td>
							</tr>
							<?php
							}
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
								<td align="center" colspan="3">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<td align="center" colspan="2">
												<table border="0" align="center" cellpadding="0" cellspacing="0">
													<tr>
														<td align="center">
															<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_064?>","onclick_facture()");</script>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<?php
											if($mode == 'editer') {
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_004?>","onclick_enregistrer()");</script>
											</td>
											<?php
											}?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_fermer_fenetre()");</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
					</form>

					<?php
					// Rafraichir le parent et fermer ?
					if($rafraichir_parent == 'oui') {
					?>
						<script language="javascript">
							window.opener.actualiser();
							window.close();
						</script>
					<?php
					}
					?>
					

					<?php //********** VALIDATION FORMULAIRES ********** ?>
					
					<script language="javascript">
						var ajouter_reglement = false;
						
						function onclick_reglement_ajout() {
							// Montrer le tableau permettant d'ajouter un reglement
							document.getElementById('tr_ajouter').style.display = '';
							ajouter_reglement = true;
						}
						
						function onclick_reglement_ajout_annuler() {
							// Cacher le tableau permettant d'ajouter un reglement
							document.getElementById('tr_ajouter').style.display = 'none';
							ajouter_reglement = false;
						}
							
						function onclick_enregistrer() {
							var valide = true;
							var obj;
							var message_erreur = '';
							var separateur = '';
							var montant_echeance_tmp;
							var reglements;
							var i;
							
							// Si l'utilisateur veut ajouter un reglement, verifier les donnees entrees
							if(ajouter_reglement) {
								// Verifier si la date est presente
								obj = document.getElementById('nouveau_reglement_date_reglement');
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_REGL_010); ?>";
									separateur = "\n";
									if(valide) {
										document.getElementById('anchor18div_nouveau_reglement_date_reglement').onclick();
									}
									valide = false;
								} else {
									if(!est_date(obj.value, false)) {
										message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_REGL_010); ?>";
										separateur = "\n";
										if(valide) {
											document.getElementById('anchor18div_nouveau_reglement_date_reglement').onclick();
										}
										valide = false;
									}
								}


								// Verifier que le libelle n'est pas vide
								obj = document.getElementById('nouveau_reglement_libelle');
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_REGL_011); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
								
								// Verifier que le montant est un decimal valide
								obj = document.getElementById('nouveau_reglement_montant');
								if(!est_nombre(obj.value, 'decimal', ',')) {
									//alert('ff');
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_REGL_012); ?>";
									separateur = "\n";
									valide = false;
								} else {
									// Verifier que le montant est superieur s 0
									montant_echeance_tmp = obj.value;
									montant_echeance_tmp = montant_echeance_tmp.replace(',', '.') * 1;
									if(montant_echeance_tmp <= 0.0) {
										//alert('ff');
										message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_007, LANG_FIN_REGL_012); ?>";
										separateur = "\n";
										if(valide) {
											obj.focus();
										}

										valide = false;
									}
								}
								
								
							}
			
							// Verifier les reglements existants
							reglements = document.getElementById('reglements').value;
							
							for (i=1; i<=reglements; i++) {

								// Verifier si la date est presente
								obj = document.getElementById('reglement_' + i + '_date_reglement');
								if(trim(obj.value) == '') {
									messsage = "<?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_REGL_014); ?>";
									messsage = messsage.replace('#i#', i);
									message_erreur += separateur + "     - " + messsage;
									separateur = "\n";
									if(valide) {
										document.getElementById('anchor18div_reglement_'+i+'_date_reglement').onclick();
									}
									valide = false;
								} else {
									if(!est_date(obj.value, false)) {
										messsage = "<?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_REGL_014); ?>";
										messsage = messsage.replace('#i#', i);
										message_erreur += separateur + "     - " + messsage;
										separateur = "\n";
										if(valide) {
											document.getElementById('anchor18div_reglement_'+i+'_date_reglement').onclick();
										}
										valide = false;
									}
								}


								// Verifier que le libelle n'est pas vide
								obj = document.getElementById('reglement_' + i + '_libelle');
								if(trim(obj.value) == '') {
									messsage = "<?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_REGL_015); ?>";
									messsage = messsage.replace('#i#', i);
									message_erreur += separateur + "     - " + messsage;
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
								
								// Verifier que le montant est un decimal valide
								obj = document.getElementById('reglement_' + i + '_montant');
								if(!est_nombre(obj.value, 'decimal', ',')) {
									messsage = "<?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_REGL_016); ?>";
									messsage = messsage.replace('#i#', i);
									message_erreur += separateur + "     - " + messsage;
									separateur = "\n";
									valide = false;
									if(valide) {
										obj.focus();
									}
								} else {
									// Verifier que le montant est superieur s 0
									montant_echeance_tmp = obj.value;
									montant_echeance_tmp = montant_echeance_tmp.replace(',', '.') * 1;
									if(montant_echeance_tmp <= 0.0) {
										messsage = "<?php echo sprintf(LANG_FIN_VALI_007, LANG_FIN_REGL_016); ?>";
										messsage = messsage.replace('#i#', i);
										message_erreur += separateur + "     - " + messsage;
										separateur = "\n";
										if(valide) {
											obj.focus();
										}

										valide = false;
									}
								}								
							}
			
			
							if(valide) {
								msg_util_attente_montrer(false);
								document.formulaire.operation.value = 'enregistrer';
								if(ajouter_reglement) {
									document.formulaire.nouveau_reglement.value = '1';
								}
								document.formulaire.but_actualiser.click();
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}
			
							return(valide);
						}
						
						// Bloquer ou non le numero de cheque quant l'utilisateur change de type de reglement d'un reglement
						function onchange_type_reglement_id(pos_reglement) {
							obj_type_reglement = document.getElementById('reglement_' + pos_reglement + '_type_reglement_id');
							obj_numero_cheque = document.getElementById('reglement_' + pos_reglement + '_numero_cheque');
							obj_numero_bordereau = document.getElementById('reglement_' + pos_reglement + '_numero_bordereau');
							if(obj_type_reglement.options[obj_type_reglement.selectedIndex].value == '<?php echo $g_tab_type_reglement_id['cheque']; ?>') {
								obj_numero_cheque.disabled = false;
							} else {
								obj_numero_cheque.disabled = true;
							}
							if(obj_type_reglement.options[obj_type_reglement.selectedIndex].value == '<?php echo $g_tab_type_reglement_id['cheque']; ?>' || obj_type_reglement.options[obj_type_reglement.selectedIndex].value == '<?php echo $g_tab_type_reglement_id['espece']; ?>') {
								obj_numero_bordereau.disabled = false;
							} else {
								obj_numero_bordereau.disabled = true;
							}
						}
						

						// Bloquer ou non le numero de cheque quant l'utilisateur change de type de reglement d'un nouveau reglement
						function onchange_nouveau_reglement_type_reglement_id() {
							obj_type_reglement = document.getElementById('nouveau_reglement_type_reglement_id');
							obj_numero_cheque = document.getElementById('nouveau_reglement_numero_cheque');
							obj_numero_bordereau = document.getElementById('nouveau_reglement_numero_bordereau');
							if(obj_type_reglement.options[obj_type_reglement.selectedIndex].value == '<?php echo $g_tab_type_reglement_id['cheque']; ?>') {
								obj_numero_cheque.disabled = false;
							} else {
								obj_numero_cheque.disabled = true;
							}
							if(obj_type_reglement.options[obj_type_reglement.selectedIndex].value == '<?php echo $g_tab_type_reglement_id['cheque']; ?>' || obj_type_reglement.options[obj_type_reglement.selectedIndex].value == '<?php echo $g_tab_type_reglement_id['espece']; ?>') {
								obj_numero_bordereau.disabled = false;
							} else {
								obj_numero_bordereau.disabled = true;
							}
						}

						function onclick_fermer_fenetre() {
							<?php
							if($rafraichir_parent == 'oui') {
							?>
							window.opener.actualiser();
							<?php
							}
							?>
							window.close();
						}
						
						function onclick_facture() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_facture').submit();
						}
						
						function fermer_fenetre() {
							//alert('ffffff');
							window.close();
						}	
					</script>		
					<form name="formulaire_facture" id="formulaire_facture" action="<?php echo $g_chemin_relatif_module; ?>editer_facture.php" method="post">
							<input type="hidden" name="date" id="date" value="<?php echo date_depuis_bdd($infos_echeance[2])?>">
							<input type="hidden" name="montant" id="montant" value="<?php echo montant_depuis_bdd($infos_echeance[3], 2, ',', ' ');?>">
							<input type="hidden" name="type" id="type" value="<?php echo $infos_type_reglement[1]; ?>">
							<input type="hidden" name="echeancier_id" id="echeancier_id" value="<?php echo $echeancier_id; ?>">
					</form>	
				</td>
			</tr>		
		</table>
		<?php
		}
		?>

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
				
				// Initialiser le disabled du numero de cheque du nouveau reglement
				onchange_nouveau_reglement_type_reglement_id();
			}
			
			// Executer initialisation_page() au chargement de la page
			if (window.addEventListener) {
				window.addEventListener("load",initialisation_page,false);
			} else if (window.attachEvent) { 
				window.attachEvent("onload",initialisation_page);
			}	
					
		</script>

	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>
