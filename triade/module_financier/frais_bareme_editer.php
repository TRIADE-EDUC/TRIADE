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
	$mode = lire_parametre('mode', '', 'GET');
	$code_class = lire_parametre('code_class', 0, 'GET');
	$bareme_id = lire_parametre('bareme_id', 0, 'GET');
	$frais_bareme_id = lire_parametre('frais_bareme_id', 0, 'GET');
	$type_frais_id = lire_parametre('type_frais_id', 0, 'GET');
	$montant = lire_parametre('montant', '', 'GET');
	$optionnel = lire_parametre('optionnel', 0, 'GET');
	$lisse = lire_parametre('lisse', 0, 'GET');
	//***************************************************************************

	//*************** TRAITER L'OPERATION DEMANDEE ****************
	$rafraichir_parent = '';
	
	switch($operation) {
		case "ajout":
			$sql= "INSERT INTO ".FIN_TAB_FRAIS_BAREME." (bareme_id, type_frais_id, montant, optionnel, lisse) ";
			$sql.="VALUES(".$bareme_id.",".$type_frais_id.",'".montant_vers_bdd($montant)."',".$optionnel.",".$lisse."); ";
			$res_lock=execSql("LOCK TABLES ".FIN_TAB_FRAIS_BAREME." WRITE");
			//echo $sql;
			$res=execSql($sql);
			$frais_bareme_id = dernier_id($cnx->connection);
			$res_lock=execSql("UNLOCK TABLES ");
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			break;
		case "modif":
			$sql= "UPDATE ".FIN_TAB_FRAIS_BAREME." ";
			$sql.="SET type_frais_id = " . $type_frais_id . " ";
			$sql.=", montant = '" . montant_vers_bdd($montant) . "' ";
			$sql.=", optionnel = " . $optionnel . " ";
			$sql.=", lisse = " . $lisse . " ";
			$sql.="WHERE frais_bareme_id = $frais_bareme_id ";
			$res=execSql($sql);
			//echo $sql;
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			break;
		case "supp":
			$sql= "DELETE FROM ".FIN_TAB_FRAIS_BAREME." ";
			$sql.="WHERE frais_bareme_id = $frais_bareme_id ";
			$res=execSql($sql);
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			$frais_bareme_id = 0;
			break;
	}
	//***************************************************************************
	
	
	// Rechercher le frais de bareme
	$sql ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle, fb.lisse ";
	$sql.="FROM ".FIN_TAB_FRAIS_BAREME." fb ";
	$sql.="INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
	$sql.="WHERE fb.frais_bareme_id = $frais_bareme_id ";
	$sql.="ORDER BY tf.libelle ASC";
	//echo $sql;
	$res=execSql($sql);
	if($res->numRows() > 0) {
		$ligne = & $res->fetchRow();
	}
	

	// Rechercher les types de frais deja utilises
	$sql ="SELECT fb.type_frais_id ";
	$sql.="FROM ".FIN_TAB_FRAIS_BAREME." fb ";
	$sql.="INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
	$sql.="WHERE fb.bareme_id = $bareme_id ";
	$sql.="ORDER BY fb.type_frais_id ASC";
	//echo $sql;
	$types_frais_utilises=execSql($sql);
	
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
	//echo "<br>". $type_frais_id_utilises;

	// Rechercher les types de frais disponibles
	$sql ="SELECT type_frais_id, libelle, lisse, caution ";
	$sql.="FROM ".FIN_TAB_TYPE_FRAIS." ";
	$sql.="WHERE 1=1 ";
	if($type_frais_id_utilises != '') {
		$sql.="AND (type_frais_id NOT IN (" . $type_frais_id_utilises . ") ";
		if($res->numRows() > 0) {
			$sql.=" OR type_frais_id = " . $ligne[2];
		}
		$sql.=") ";
	}
	$sql.="ORDER BY libelle ASC";
	//echo $sql;
	$types_frais_disponibles=execSql($sql);


	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	if($mode == 'modif' || $mode == 'supp') {
		if($res->numRows() == 0) {
			msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
		}
		if($types_frais_utilises->numRows() == 0) {
			msg_util_ajout(LANG_FIN_TFRA_011, 'avertissement');
		}
	}
	//***************************************************************************

} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

// Titre de la page
switch($mode) {
	case "ajout":
		$titre = LANG_FIN_FBAR_007;
		break;
	case "modif":
		$titre = LANG_FIN_FBAR_008;
		break;
	case "supp":
		$titre = LANG_FIN_FBAR_009;
		break;
	default:
		$titre = '';
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
		<title><?php echo $titre; ?></title>
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
					<b><font class="T2"><?php echo $titre; ?></font></b>
				</td>
			</tr>
			<tr>
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" method="get" action="<?php echo url_script(); ?>" onSubmit="return valider_le_formulaire();">
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
			
							<?php
							
							// Verifier si on peut afficher
							if(($mode == 'ajout' || (($mode == 'modif' || $mode == 'supp') && $res->numRows() > 0)) && $types_frais_disponibles->numRows() > 0) {
								if($mode == 'modif' || $mode == 'supp') {
									
								}
							
							?>
							
							<input type="hidden" name="operation" id="operation" value="<?php echo $mode; ?>">
							<input type="hidden" name="bareme_id" id="bareme_id" value="<?php echo $bareme_id; ?>">
							<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class; ?>">
							<?php
								if($mode == 'ajout') {
									$valeur = 0;
								} else {
									$valeur = $ligne[0];
								}
							?>
							<input type="hidden" name="frais_bareme_id" id="frais_bareme_id" value="<?php echo $valeur; ?>">
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
								
									<table cellspacing="1" cellpadding="3" border="0" width="100%" align="center">
										<tr>
											<td align="right"><?php echo LANG_FIN_FBAR_004; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<?php
												$type_frais_id_lisse = 0;
												$type_frais_id_caution = 0;
												switch($mode) {
													case "ajout":
														$ligne_tmp = $types_frais_disponibles->fetchRow();
														$valeur = $ligne_tmp[0];
														break;
													case "modif":
														$valeur = $ligne[2];
														break;
													case "supp":
														$valeur = $ligne[2];
														$disabled = 'disabled';
														break;
													default:
														$disabled = '';
												}
												?>
												<select name="type_frais_id" id="type_frais_id" <?php echo $disabled; ?> onChange="onchange_type_frais_id();">
												<?php
												for($i=0;$i<$types_frais_disponibles->numRows();$i++) {
													$res_tmp = $types_frais_disponibles->fetchInto($ligne_tmp, DB_FETCHMODE_DEFAULT, $i);
													$selected = '';
													if($valeur == $ligne_tmp[0]) {
														$selected = 'selected="selected"';
														if($ligne_tmp[2] == 1) {
															$type_frais_id_lisse = 1;
														}
														if($ligne_tmp[3] == 1) {
															$type_frais_id_caution = 1;
														}

													}
												?>	
													<option value="<?php echo $ligne_tmp[0]; ?>" <?php echo $selected; ?>><?php echo $ligne_tmp[1]; ?></option>
												<?php
												}
												?>
												</select>
												
											</td>
										</tr>
										<tr>
											<td align="right"><?php echo LANG_FIN_GENE_013; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<?php
												switch($mode) {
													case "ajout":
														$valeur = '';
														break;
													case "modif":
														$valeur = $ligne[3];
														break;
													case "supp":
														$valeur = $ligne[3];
														$disabled = 'disabled';
														break;
													default:
														$disabled = '';
												}
												// Remplacer le separateur de decimal bdd, par le francais
												$valeur = montant_depuis_bdd($valeur, 2, ',', '');
												?>
												<input type="text" name="montant" id="montant" value="<?php echo $valeur; ?>" size="15" maxlength="15"onBlur="formatage_montant(this);"  <?php echo $disabled; ?>>
											</td>
										</tr>
										<tr>
											<td align="right"><?php echo LANG_FIN_GENE_012; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<?php
												$valeur = '';
												switch($mode) {
													case "ajout":
														break;
													case "modif":
														if($ligne[4] == 1) {
															$valeur = 'checked';
														}
														break;
													case "supp":
														if($ligne[4] == 1) {
															$valeur = 'checked';
														}
														$disabled = 'disabled';
														break;
													default:
														$disabled = '';
												}
												?>
												<input type="checkbox" name="optionnel" id="optionnel" <?php echo $valeur; ?>  <?php echo $disabled; ?> value="1">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_TFRA_014; ?> :</td>
											<td valign="top" align="left">
												<table border="0" cellspacing="0" cellpadding="0" align="left">
													<tr>
														<td valign="middle">
															<?php
															$valeur = '';
															switch($mode) {
																case "ajout":
																	if($type_frais_id_lisse == 1) {
																		$valeur = 'checked';
																	}
																	break;
																case "modif":
																	if($ligne[6] == 1) {
																		$valeur = 'checked';
																	}
																	break;
																case "supp":
																	if($ligne[6] == 1) {
																		$valeur = 'checked';
																	}
																	$disabled = 'disabled';
																	break;
																default:
																	$disabled = '';
															}
															?>
															<input type="checkbox" name="lisse" id="lisse" <?php echo $valeur; ?>  <?php echo $disabled; ?> value="1">
														</td>
														<td valign="middle">&nbsp;</td>
														<td valign="middle">
															<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_TFRA_015; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
														</td>
													</tr>
												</table>											
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_TFRA_016; ?> :</td>
											<td valign="top" align="left">
												<table border="0" cellspacing="0" cellpadding="0" align="left">
													<tr>
														<td valign="middle">
															<?php
															$valeur = LANG_FIN_GENE_018;
															if($type_frais_id_caution == 1) {
																$valeur = LANG_FIN_GENE_017;
															}
															?>
															<span id="caution"><?php echo $valeur; ?></span>
														</td>
														<td valign="middle">&nbsp;</td>
														<td valign="middle">
															<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_TFRA_017; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
														</td>
													</tr>
												</table>											
											</td>
										</tr>
									</table>
									
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
											<td align="center">
											<?php
												switch($mode) {
													case "ajout":
											?>
												<script language="javascript">buttonMagicSubmit('<?php print LANG_FIN_GENE_014?>','create');</script>
											<?php
														break;
													case "modif":
											?>
												<script language="javascript">buttonMagicSubmit('<?php print LANG_FIN_GENE_005?>','create');</script>
											<?php
														break;
													case "supp":
											?>
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_015?>","onclick_supprimer()");</script>
												<script language="javascript">
													function onclick_supprimer() {
														if(confirm("<?php echo LANG_FIN_FBAR_010; ?>")) {
															document.getElementById('formulaire').submit();
														}
													}
												</script>
											<?php
														break;
												}
											?>
											</td>
											<td align="center">
												<script language="javascript">buttonMagicFermeture();</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					</form>

					<?php
					// Rafraichir le parent et fermer ?
					if($rafraichir_parent == 'oui') {
					?>
						<script language="javascript">
							//alert('oui');
							msg_util_attente_montrer(false);
							window.opener.actualiser(<?php echo $bareme_id; ?>);
							window.close();
						</script>
					<?php
					}
					?>
					

					<?php //********** VALIDATION FORMULAIRES ********** ?>
					
					<script language="javascript">
							
						function valider_le_formulaire() {
							var valide = true;
							var obj;
							var message_erreur = '';
							var separateur = '';
							
							<?php
							if($mode == 'ajout' || $mode == 'modif') {
							?>
							
							
							// On verifie que le 'montant' est numerique
							obj = document.getElementById('montant');
							if(trim(obj.value) == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_GENE_013); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}

							if(valide) {
								// On verifie que le 'montant' est correct
								obj = document.getElementById('montant');
								if(!valider_chaine(obj.value, '0123456789,')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_GENE_013); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
							}

							if(valide) {
								// On verifie que le 'montant' est correct (avec une ',')
								obj = document.getElementById('montant');
								str_tmp = obj.value;
								if(str_tmp.indexOf(',') == -1) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_005, LANG_FIN_GENE_013); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
							}
							
							
							<?php
							}
							?>
			
			
							if(valide) {
								msg_util_attente_montrer(false);
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}
			
							return(valide);
						}

						
						var tab_types_frais = new Array();
						<?php
						// Recuperer les differentes options de chaque type de frais
						for($i=0;$i<$types_frais_disponibles->numRows();$i++) {
							$res_tmp = $types_frais_disponibles->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
						?>
						tab_types_frais[tab_types_frais.length] = {
											type_frais_id : <?php echo $ligne[0]; ?>,
											lisse : <?php echo $ligne[2]; ?>,
											caution : <?php echo $ligne[3]; ?>
																	};
						<?php
						}
						?>

						function onchange_type_frais_id() {
							var obj_type_frais_id = document.getElementById('type_frais_id');
							var obj_lisse = document.getElementById('lisse');
							var obj_caution = document.getElementById('caution');
							var i, trouve;
							
							// Rechercher le type de frais
							trouve = false;
							for(i=0;i<tab_types_frais.length;i++) {
								if(obj_type_frais_id.options[obj_type_frais_id.selectedIndex].value == tab_types_frais[i]['type_frais_id']) {
									trouve = true;
									break;
								}
							}
							if(trouve) {
							
								if(tab_types_frais[i]['lisse'] == 1) {
									obj_lisse.checked = true;
								} else {
									obj_lisse.checked = false;
								}
																
								if(tab_types_frais[i]['caution'] == 1) {
									obj_caution.innerHTML = "<?php echo LANG_FIN_GENE_017; ?>";
								} else {
									obj_caution.innerHTML = "<?php echo LANG_FIN_GENE_018; ?>";
								}
								
							}
						}


						<?php
						if($mode == 'ajout' || $mode == 'modif') {
						?>
						document.getElementById('libelle').focus();
						<?php
						}
						?>
			
					</script>		
							
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
			}
			
			// Executer initialisation_page() au chargement de la page
			if (window.addEventListener) {
				window.addEventListener("load",initialisation_page,false);
			} else if (window.attachEvent) { 
				window.attachEvent("onload",initialisation_page);
			}	
					
		</script>


				<!-- BOUTONS -->
				<!--
				<tr>
					<td align="center">
						<table border="0" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td align="right">
									<script language="javascript">buttonMagicSubmit('<?php print LANGENR?>','create');</script>
								</td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td align="left">
									<script language="javascript">buttonMagicFermeture();</script>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				-->
		
	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>
