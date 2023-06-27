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
	$operation = lire_parametre('operation', '', 'POST');
	$code_class = lire_parametre('code_class', 0, 'POST');
	$bareme_id = lire_parametre('bareme_id', 0, 'POST');
	$frais_bareme_id = lire_parametre('frais_bareme_id', 0, 'POST');
	$code_class_copier = lire_parametre('code_class_copier', 0, 'POST');
	$bareme_id_copier = lire_parametre('bareme_id_copier', 0, 'POST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Initialisation sur changement de classe
	if($operation == "changement_code_class") {
		$bareme_id = 0;
		$frais_bareme_id = 0;
	}
	if($operation == "copier_bareme") {
		
		// Rechercher les infos du bareme a copier
		$sql ="SELECT bareme_id, code_class, libelle, annee_scolaire ";
		$sql.="FROM ".FIN_TAB_BAREME." ";
		$sql.="WHERE bareme_id = $bareme_id_copier ";
		$bareme_a_copier=execSql($sql);
		
		// Verifier que l'on a trouve le bareme a copier
		if($bareme_a_copier->numRows() > 0) {
			
			// Recuperer les infos du bareme a copier
			$res = $bareme_a_copier->fetchInto($ligne_bareme_a_copier, DB_FETCHMODE_DEFAULT, 0);
			
			// Creer le nouveau bareme
			$sql= "INSERT INTO ".FIN_TAB_BAREME." (code_class, libelle, annee_scolaire) ";
			$sql.="VALUES(".$code_class.",'".esc($ligne_bareme_a_copier[2])." - " . LANG_FIN_GENE_057 . "','".esc($ligne_bareme_a_copier[3])."'); ";
			$res_lock=execSql("LOCK TABLES ".FIN_TAB_BAREME." WRITE");
			$res=execSql($sql);
			$bareme_id = dernier_id($cnx->connection);
			$res_lock=execSql("UNLOCK TABLES ");
			//echo $sql;
			
			// Rechercher les frais du bareme a copier
			$sql ="SELECT frais_bareme_id, bareme_id, type_frais_id, montant, optionnel, lisse ";
			$sql.="FROM ".FIN_TAB_FRAIS_BAREME." ";
			$sql.="WHERE bareme_id = $bareme_id_copier ";
			$sql.="ORDER BY frais_bareme_id ";
			$frais_bareme_a_copier=execSql($sql);
	
			// Verifier que l'on a trouve les frais du bareme a copier
			if($frais_bareme_a_copier->numRows() > 0) {
				// Traiter chaque frais a copier
				for($i=0; $i<$frais_bareme_a_copier->numRows(); $i++) {
					// Recuperer les infos du frais
					$res = $frais_bareme_a_copier->fetchInto($ligne_frais_a_copier, DB_FETCHMODE_DEFAULT, $i);
					// Creer le nouveau frais
					$sql= "INSERT INTO ".FIN_TAB_FRAIS_BAREME." (bareme_id, type_frais_id, montant, optionnel, lisse) ";
					$sql.="VALUES(".$bareme_id.",".$ligne_frais_a_copier[2].",".$ligne_frais_a_copier[3].",".$ligne_frais_a_copier[4].",".$ligne_frais_a_copier[5]."); ";
					$res_lock=execSql("LOCK TABLES ".FIN_TAB_FRAIS_BAREME." WRITE");
					$res=execSql($sql);
					$res_lock=execSql("UNLOCK TABLES ");
				}
			}
			
			
		}
		
	}
	//***************************************************************************
	
	// Rechercher la liste des classes
	$sql ="SELECT code_class, libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." ";
	$sql.="ORDER BY libelle";
	$classes=execSql($sql);
	
	
	// Selectionner la premiere classe (si il n'y en a pas deja une)
	if($classes->numRows() > 0 && $code_class <= 0) {
		$ligne = null;
		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$code_class = $ligne[0];
	}
	
	// Rechercher la liste des baremes pour la classe courante
	if($code_class > 0) {
		$sql ="SELECT bareme_id, code_class, libelle, annee_scolaire, LEFT(annee_scolaire, 4) as premiere_annee ";
		$sql.="FROM ".FIN_TAB_BAREME." ";
		$sql.="WHERE code_class = $code_class ";
		$sql.="ORDER BY premiere_annee DESC, libelle ASC";
		$baremes=execSql($sql);
		
		// Selectionner le premier bareme (si il n'y en a pas deja un)
		if($baremes->numRows() > 0 && $bareme_id <= 0) {
			$ligne = null;
			$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			$bareme_id = $ligne[0];
		}
		
		// Rechercher les autres classes qui ont un bareme (pour copier)
		$sql ="SELECT c.code_class, c.libelle ";
		$sql.="FROM ".FIN_TAB_CLASSES." c ";
		$sql.="INNER JOIN ".FIN_TAB_BAREME." b ON c.code_class = b.code_class ";
		// 20100510 - AP : on enleve le WHERE pour pouvoir copier aussi un bareme de la classe en cours de saisie
		//$sql.="WHERE c.code_class <> $code_class ";
		$sql.="GROUP BY c.code_class, c.libelle ";
		$sql.="ORDER BY c.libelle ASC ";
		$classes_copier=execSql($sql);
		//echo $sql;

		if($classes_copier->numRows() > 0) {
			// Recuperer le code_class de la premiere classe
			$res = $classes_copier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			$code_class_copier = $ligne[0];
			
			// Rechercher les bareme a copier
			$sql ="SELECT b.bareme_id, b.code_class, b.libelle, b.annee_scolaire, LEFT(b.annee_scolaire, 4) as premiere_annee ";
			$sql.="FROM ".FIN_TAB_CLASSES." c ";
			$sql.="INNER JOIN ".FIN_TAB_BAREME." b ON c.code_class = b.code_class ";
			//$sql.="WHERE b.code_class = $code_class_copier ";
			$sql.="ORDER BY premiere_annee DESC, b.libelle ASC";
			$baremes_copier=execSql($sql);
			if($baremes_copier->numRows() > 0) {
				$res = $baremes_copier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
				$bareme_id_copier = $ligne[0];
			} else {
				$bareme_id_copier = 0;
			}
		} else {
			$code_class_copier = 0;
		}
	}


	// Rechercher la liste des frais pour le bareme courant
	if($code_class > 0 && $bareme_id > 0) {
		$sql ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle, fb.lisse, tf.caution ";
		$sql.="FROM ".FIN_TAB_FRAIS_BAREME." fb ";
		$sql.="INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
		$sql.="WHERE fb.bareme_id = $bareme_id ";
		$sql.="ORDER BY tf.libelle ASC";
		//echo $sql;
		$frais_bareme=execSql($sql);
		
		// Selectionner le premier bareme (si il n'y en a pas deja un)
		if($frais_bareme->numRows() > 0 && $frais_bareme_id <= 0) {
			$ligne = null;
			$res = $frais_bareme->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			$frais_bareme_id = $ligne[0];
		}
		
		// Rechercher les types de frais deja utilises
		$sql ="SELECT fb.type_frais_id ";
		$sql.="FROM ".FIN_TAB_FRAIS_BAREME." fb ";
		$sql.="INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
		$sql.="WHERE fb.bareme_id = $bareme_id ";
		$sql.="ORDER BY fb.type_frais_id ASC";
		//echo $sql;
		$types_frais_utilises=execSql($sql);
		
		// ********** Rechercher si il y a des types de frais non-utilises pour ce bareme **********
		// ********** => pour afficher ou non le bouton 'Ajouter'
		// Generer la liste des types de frais deja utilises
		$type_frais_id_utilises = '';
		if($types_frais_utilises->numRows() > 0) {
			$separateur = '';
			for($i=0;$i<$types_frais_utilises->numRows();$i++) {
				$ligne_tmp = & $types_frais_utilises->fetchRow();
				$type_frais_id_utilises .= $separateur . $ligne_tmp[0];
				$separateur = ', ';
			}
		}
		// Rechercher les types de frais disponibles
		$sql ="SELECT type_frais_id, libelle ";
		$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ";
		$sql.="WHERE 1=1 ";
		if($type_frais_id_utilises != '') {
			$sql.="AND type_frais_id NOT IN (" . $type_frais_id_utilises . ") ";
		}
		$sql.="ORDER BY libelle ASC";
		//echo $sql;
		$types_frais_disponibles=execSql($sql);
		// ****************************************************************************************

		
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
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
		<?php include("./librairie_php/lib_defilement.php"); ?>
		</td>
		<td width="472" valign="middle" rowspan="3" align="center">
			<div align='center'>
				<?php top_h(); ?>
				<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>


		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		
		<!-- TITRE ET CADRE CENTRAL -->
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" >
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_FIN_BARE_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire_principal" id="formulaire_principal" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						<input type="hidden" name="operation" id="operation" value="">
						<input type="hidden" name="bareme_id" id="bareme_id" value="<?php echo bareme_id; ?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">

									<?php //********** CLASSES ********** ?>
									
									<fieldset>
										<legend><?php echo LANG_FIN_CLAS_001; ?></legend>
										<?php
										// Verifier si on a au moins une classe
										if($classes->numRows() > 0) {
										?>
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="code_class" id="code_class" onChange="onchange_code_class()">
														<?php
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
														?>
													</select>
												</td>
											</tr>
										</table>
										<?php
										} else {
										?>
											<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_FIN_CLAS_002; ?></span></div>
										<?php
										}
										?>
									</fieldset>
									
									
									<?php //********** BAREMES ********** ?>

									<br>
									<fieldset>
										<legend><?php echo LANG_FIN_BARE_003; ?></legend>
										<?php
										// Verifier si une classe a ete selectionnee
										if($code_class > 0) {
										?>
										<table cellspacing="0" cellpadding="0" border="0" width="98%">
											<tr>
												<td align="right">
													<input type=button class=button value="<?php echo LANG_FIN_GENE_014; ?>" onClick="onclick_bareme_editer(0, 'ajout');">
												</td>
											</tr>
											<tr>
												<td align="right">&nbsp;</td>
											</tr>
											<tr>
												<td align="left">
													<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
														<tr bgcolor="#ffffff">
															<td width="5%">&nbsp;</td>
															<td><b><?php echo LANG_FIN_GENE_016; ?></b></td>
															<td><b><?php echo LANG_FIN_GENE_011; ?></b></td>
															<td width="5%">&nbsp;</td>
														</tr>
														<?php
														if($baremes->numRows() > 0) {
															for($i=0; $i<$baremes->numRows(); $i++) {
																$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																$checked = '';
																if($bareme_id == $ligne[0]) {
																	$checked = 'checked';
																}

														?>
														<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
															<td valign="middle"><input type="radio" name="bareme_id_radio" id="bareme_id_radio" <?php echo $checked; ?> onClick="onclick_bareme(<?php echo $ligne[0]; ?>)"></td>
															<td><?php echo $ligne[2]; ?></td>
															<td nowrap="nowrap"><?php echo $ligne[3]; ?></td>
															<td align="center" nowrap="nowrap">
																<table cellspacing="0" cellpadding="0" border="0">
																	<tr>
																		<td>
																			<input type=button class=button value="<?php echo LANG_FIN_GENE_005; ?>" onClick="onclick_bareme_editer(<?php echo $ligne[0]; ?>, 'modif');">
																		</td>
																		<td>&nbsp;</td>
																		<td>
																			<input type=button class=button value="<?php echo LANG_FIN_GENE_015; ?>" onClick="onclick_bareme_editer(<?php echo $ligne[0]; ?>, 'supp');">
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<?php
															}
														} else {
														?>
														<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
															<td align="left" colspan="4"><?php echo LANG_FIN_BARE_005; ?></td>
														</tr>
														<?php
														}
														?>
													</table>
													<br>
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td><?php echo LANG_FIN_BARE_010; ?>&nbsp;:&nbsp;</td>
															<?php
															// Verifier si on a au moins une classe
															if($classes_copier->numRows() > 0) {
															?>
															<td>
																<select name="code_class_copier" id="code_class_copier" onChange="onchange_code_class_copier();">
																	<?php
																	for($i=0; $i<$classes_copier->numRows(); $i++) {
																		$res = $classes_copier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																		
																		$selected = '';
																		if($i == 0) {
																			$selected = 'selected="selected"';
																		}
																	?>
																	<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
																	<?php
																	}
																	?>
																</select>
															</td>
															<td>&nbsp;</td>
															<td>
															<?php
																// Verifier si on a au moins une classe
																if($baremes_copier->numRows() > 0) {
															?>
																<select name="bareme_id_copier" id="bareme_id_copier" >
																</select>
															
															<?php
																} else {
																	echo LANG_FIN_BARE_005;
																}
															?>
															</td>
															<td>&nbsp;</td>
															<td><input type=button class=button value="<?php echo LANG_FIN_GENE_047; ?>" onClick="onclick_copier_bareme();"></td>

															<?php
															} else {
																echo LANG_FIN_CLAS_002;
															}
															?>
														</tr>
													</table>

												</td>
											</tr>
										</table>
										<?php
										} else {
											echo LANG_FIN_CLAS_004;
										}
										?>									
									</fieldset>
									
									
									<?php //********** FRAIS ********** ?>

									<br>
									<fieldset>
										<legend><?php echo LANG_FIN_FBAR_003; ?></legend>
										<?php
										// Verifier si une classe et un bareme ont ete selectionnes
										if($code_class > 0 && $bareme_id > 0) {
										?>
										<table cellspacing="0" cellpadding="0" border="0" width="98%">
											<tr>
												<td align="right">
												<?php
													$disabled = '';
													if($types_frais_disponibles->numRows() == 0) {
														$disabled = 'disabled';
													}
												?>
													<input type=button class=button value="<?php echo LANG_FIN_GENE_014; ?>" onClick="onclick_frais_bareme_editer(0, 'ajout');" <?php echo $disabled; ?>>
												</td>
											</tr>
											<tr>
												<td align="right">&nbsp;</td>
											</tr>
											<tr>
												<td align="left">
													<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
														<tr bgcolor="#ffffff">
															<td align="left"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
															<td align="right"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
															<td align="center"><b><?php echo LANG_FIN_GENE_012; ?></b></td>
															<td align="center"><b><?php echo LANG_FIN_TFRA_014; ?></b></td>
															<td align="center"><b><?php echo LANG_FIN_TFRA_016; ?></b></td>
															<td>&nbsp;</td>
														</tr>
														<?php
														$total_non_optionnels = 0.0;
														$total_optionnels = 0.0;
														if($frais_bareme->numRows() > 0) {
															for($i=0; $i<$frais_bareme->numRows(); $i++) {
																$res = $frais_bareme->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																if($ligne[4] == 0) {
																	$total_non_optionnels += $ligne[3];
																} else {
																	$total_optionnels += $ligne[3];
																}
																
														?>
														<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
															<td nowrap="nowrap"><?php echo $ligne[5]; ?></td>
															<?php
															// Remplacer le separateur de decimal bdd, par le francais
															$valeur = montant_depuis_bdd($ligne[3]);
															?>
															<td nowrap="nowrap" align="right"><?php echo $valeur; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<?php
															// Indiquer si c'est un frais optionnel ou non
															$valeur = LANG_FIN_GENE_018;
															if($ligne[4] == 1) {
																$valeur = LANG_FIN_GENE_017;
															}
															?>
															<td nowrap="nowrap" align="center"><?php echo $valeur; ?></td>
															<?php
															// Indiquer si c'est un frais lisse ou non
															$valeur = LANG_FIN_GENE_018;
															if($ligne[6] == 1) {
																$valeur = LANG_FIN_GENE_017;
															}
															?>
															<td nowrap="nowrap" align="center"><?php echo $valeur; ?></td>
															<?php
															// Indiquer si c'est un caution ou non
															$valeur = LANG_FIN_GENE_018;
															if($ligne[7] == 1) {
																$valeur = LANG_FIN_GENE_017;
															}
															?>
															<td nowrap="nowrap" align="center"><?php echo $valeur; ?></td>
															<td align="center" nowrap="nowrap">
																<table cellspacing="0" cellpadding="0" border="0">
																	<tr>
																		<td>
																			<input type=button class=button value="<?php echo LANG_FIN_GENE_005; ?>" onClick="onclick_frais_bareme_editer(<?php echo $ligne[0]; ?>, 'modif');">
																		</td>
																		<td>&nbsp;</td>
																		<td>
																			<input type=button class=button value="<?php echo LANG_FIN_GENE_015; ?>" onClick="onclick_frais_bareme_editer(<?php echo $ligne[0]; ?>, 'supp');">
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<?php
															}
														?>
														<tr class='tabnormal2'>
															<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_052); ?></b></td>
															<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_optionnels); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
															<td align="left" colspan="4">&nbsp;</td>
														</tr>
														<tr class='tabnormal2'>
															<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_053); ?></b></td>
															<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_non_optionnels); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
															<td align="left" colspan="4">&nbsp;</td>
														</tr>
														<tr class='tabnormal2'>
															<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
															<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_optionnels + $total_non_optionnels); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
															<td align="left" colspan="4">&nbsp;</td>
														</tr>
														<?php
														} else {
														?>
														<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
															<td align="left" colspan="6"><?php echo LANG_FIN_FBAR_005; ?></td>
														</tr>
														<?php
														}
														?>
													</table>
												</td>
											</tr>
										</table>
											
										<?php
										} else {
											echo LANG_FIN_FBAR_006;
										}
										?>
									</fieldset>

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
										<tr>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
								
								
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
					</form>
					
					
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
							document.formulaire_principal.operation.value = "changement_code_class";
							document.formulaire_principal.but_actualiser.click();
							//document.formulaire_principal.submit();
						}
						function onclick_bareme_editer(bareme_id, mode) {
							try {
								for(i=0; i<liste_fenetre.length; i++) {
									liste_fenetre[i].close();
								}
							}
							catch(e) {
							}
							liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/bareme_editer.php?bareme_id=' + bareme_id + '&mode=' + mode + '&code_class=<?php echo $code_class; ?>','fenetre_editer_' + liste_fenetre.length,'width=550,height=280');
							fenetre.focus();
						}
						function onclick_frais_bareme_editer(frais_bareme_id, mode) {
							try {
								for(i=0; i<liste_fenetre.length; i++) {
									liste_fenetre[i].close();
								}
							}
							catch(e) {
							}
							liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/frais_bareme_editer.php?frais_bareme_id=' + frais_bareme_id + '&mode=' + mode + '&code_class=<?php echo $code_class; ?>&bareme_id=<?php echo $bareme_id; ?>','fenetre_editer_' + liste_fenetre.length,'width=550,height=280');
							fenetre.focus();
						}
						function onclick_bareme(bareme_id) {
							msg_util_attente_montrer(true);
							document.formulaire_principal.bareme_id.value = bareme_id;
							//document.formulaire_principal.submit();
							document.formulaire_principal.but_actualiser.click();
						}
						function actualiser(bareme_id) {
							msg_util_attente_montrer(true);
							document.formulaire_principal.operation.value = "actualiser";
							document.formulaire_principal.bareme_id.value = bareme_id;
							// On utilise plutot le click d'un bouton 'submit' pour eviter le phenomene de page blanche lors du chargement
							//document.formulaire_principal.submit();
							document.formulaire_principal.but_actualiser.click();
						}
						
						var tab_baremes_copier = new Array();
						<?php
						// Verifier si on a au moins un bareme a copier
						if($classes_copier->numRows() > 0 && $baremes_copier->numRows() > 0) {
							for($i=0; $i<$baremes_copier->numRows(); $i++) {
								$res = $baremes_copier->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
							?>
							tab_baremes_copier[tab_baremes_copier.length] = {
																				bareme_id : "<?php echo $ligne[0]; ?>",
																				code_class : "<?php echo $ligne[1]; ?>",
																				libelle : "<?php echo $ligne[2]; ?>",
																				annee_scolaire : "<?php echo $ligne[3]; ?>"
																			};
							<?php
							}
						}
						?>
						// Mettre a jour la liste des baremes a copier en fonction de la classe a copier selectionee
						function onchange_code_class_copier() {
							var i, selected;
							var code_class_copier = document.getElementById('code_class_copier');
							select_effacer("formulaire_principal", "bareme_id_copier");
							for(i=0; i<tab_baremes_copier.length; i++) {
								if(tab_baremes_copier[i]['code_class'] == code_class_copier.options[code_class_copier.selectedIndex].value) {
									selected = false;
									if(i == 0) {
										selected = true;
									}
									select_ajouter("formulaire_principal", "bareme_id_copier", tab_baremes_copier[i]['bareme_id'], tab_baremes_copier[i]['annee_scolaire'] + " - " + tab_baremes_copier[i]['libelle'], selected);
								
								}
							}
						}
						
						function onclick_copier_bareme() {
							var code_class_copier = document.getElementById('code_class_copier');
							var bareme_id_copier = document.getElementById('bareme_id_copier');
							var message;
							messsage = "<?php echo LANG_FIN_BARE_011; ?>";
							messsage = messsage.replace('#s1#', bareme_id_copier.options[bareme_id_copier.selectedIndex].text);
							messsage = messsage.replace('#s2#', code_class_copier.options[code_class_copier.selectedIndex].text);
							if(confirm(messsage)) {
								msg_util_attente_montrer(true);
								document.formulaire_principal.operation.value = "copier_bareme";
								document.formulaire_principal.but_actualiser.click();
							}
						}						
						

					</script>
					<form name="formulaire_modif" id="formulaire_modif" action="<?php echo $g_chemin_relatif_module; ?>type_frais_modif.php" method="post">
						<input type="hidden" name="type_frais_id" id="type_frais_id" value="0">
					</form>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
					</form>
					
					
				</td>
			</tr>
		</table>


		<?php //********** GENERATION DES MENUS ADMINISTRATEUR ********** ?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></script>
		

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
