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
	$type_frais_id = lire_parametre('type_frais_id', 0, 'POST');
	$libelle = lire_parametre('libelle', '', 'POST');
	$lisse = lire_parametre('lisse', 0, 'POST');
	$caution = lire_parametre('caution', 0, 'POST');
	$groupe = lire_parametre('groupe_frais_id', 0, 'POST');
	
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	if($operation == "enregistrer") {
		if($type_frais_id > 0) {
			$sql= "UPDATE ".FIN_TAB_TYPE_FRAIS." ";
			$sql.="SET libelle = '" . esc($libelle) . "' ";
			$sql.=", lisse = " . $lisse . " ";
			$sql.=", caution = " . $caution . " ";
			$sql.=", groupe_id = " . $groupe . " ";
			$sql.="WHERE type_frais_id = $type_frais_id ";
			$res=execSql($sql);
			//echo $sql;
			msg_util_ajout(LANG_FIN_GENE_001);
		} else {
			msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
		}
	}
	//***************************************************************************
	
		// Rechercher la liste des types de frais
		
	$sql ="SELECT tf.type_frais_id, tf.libelle, tf.lisse, tf.caution, tf.groupe_id ";
	$sql.="FROM " .FIN_TAB_TYPE_FRAIS." tf LEFT JOIN ".FIN_TAB_GROUPE_FRAIS." gf ";
	$sql.="ON tf.groupe_id = gf.groupe_id ";
	$sql.="WHERE tf.type_frais_id = $type_frais_id";
	$res = execSql($sql);

		// Recherche des groupe de frais
		
	$sql1 ="SELECT groupe_id, libelle ";
	$sql1.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
	$res1 = execSql($sql1);
	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS **************************************
	// Erreur : Informer l'utilisateur qu'il n'y a pas de type de frais disponible.
	if($type_frais_id <= 0) {
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_FIN_TFRA_010; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="return valider_le_formulaire();">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<?php
							if($res->numRows() > 0) {
								$ligne = &$res->fetchRow();
							?>
							
							<input type="hidden" name="operation" id="operation" value="enregistrer">
							<input type="hidden" name="type_frais_id" id="type_frais_id" value="<?php echo $type_frais_id; ?>">
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">

									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_TFRA_007; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="libelle" id="libelle" size="30" maxlength="32" value="<?php echo $ligne[1]; ?>">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_TFRA_014; ?> :</td>
											<td valign="top">&nbsp;</td>
											<?php
											$valeur = '';
											if($ligne[2] == 1) {
												$valeur = 'checked="checked"';
											}
											?>
											<td valign="top" align="left">
												<table border="0" cellspacing="0" cellpadding="0" align="left">
													<tr>
														<td valign="middle">
															<input type="checkbox" name="lisse" id="lisse" value="1" <?php echo $valeur; ?>>
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
											<td valign="top">&nbsp;</td>
											<?php
											$valeur = '';
											if($ligne[3] == 1) {
												$valeur = 'checked="checked"';
											}
											?>
											<td valign="top" align="left">
												<table border="0" cellspacing="0" cellpadding="0" align="left">
													<tr>
														<td valign="middle">
															<input type="checkbox" name="caution" id="caution" value="1" <?php echo $valeur; ?>>
														</td>
														<td valign="middle">&nbsp;</td>
														<td valign="middle">
															<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_TFRA_017; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
														</td>
													</tr>
												</table>
											</td>
										</tr>									
										
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_GROUPE_013; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<table border="0" cellspacing="0" cellpadding="0" align="left">
													<tr>
														<td valign="middle">
															<select name="groupe_frais_id" id="groupe_frais_id">
																													
															<?php
															$ok = false;
															
															for($i=0; $i<$res1->numRows(); $i++) {
															
																$ligne1 = &$res1->fetchRow();
																
																$selected = '';
																if($ligne1[0] == $ligne[4]) {
																	$selected = 'selected';
																}
																
																if ($ok == false)
																{
																	if($ligne[4] == 0)
																	{
																		$selected = 'selected';
																		?>
																		<option value="" <?php echo $selected; ?>  class="<?php echo $classe; ?>"></option>
																		<?php
																	}
																	$selected = '';
																	$ok = true;
																}

																$classe = '';
															?>
															<option value="<?php echo $ligne1[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo  $ligne1[1]; ?></option>
															<?php
															}
															?>

															</select>
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
											<?php
											if($res->numRows() > 0) {
											?>
											<td align="center">
												<script language="javascript">buttonMagicSubmit("<?php print LANG_FIN_TFRA_006?>","create");</script>
											</td>
											<?php
											}
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					
					</form>

					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
					<script language="javascript">
							
						function valider_le_formulaire() {
							var valide = true;
							var obj;
							var message_erreur = '';
							var separateur = '';
							
							obj = document.getElementById('libelle');
							obj.value = trim(obj.value);
							if(obj.value == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_TFRA_007); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
							
							obj = document.getElementById('groupe_frais_id');
							obj.value = trim(obj.value);
							if(obj.value == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_GROUPE_013); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
							
							if(valide) {
								msg_util_attente_montrer(true);
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}
			
							return(valide);
						}
			
					</script>	
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						function onclick_annuler() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_annuler').submit();
						}
						document.getElementById('libelle').focus();
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>type_frais_liste.php" method="post">
					</form>
		
					<br>
				</td>
			</tr>
		</table>
		<?php
		}
		?>


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