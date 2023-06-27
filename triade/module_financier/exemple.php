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
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	if($operation == 'exemple') {

	}
	//***************************************************************************
	
	// Rechercher la liste des baremes disponibles (pour la classe donnee)
	$sql  = "SELECT b.bareme_id, b.libelle ";
	$sql .= "FROM ".FIN_TAB_BAREME." b ";
	$sql .= "INNER JOIN ".FIN_TAB_FRAIS_BAREME." fb ON b.bareme_id = fb.bareme_id ";
	$sql .= "WHERE code_class = $code_class ";
	$sql .= "GROUP BY b.bareme_id, b.libelle ";
	$sql .= "ORDER BY b.libelle ";
	//echo $sql;
	$baremes = execSql($sql);
	
	// Selectionner le premier bareme (si il n'y en a pas deja un)
	if($baremes->numRows() > 0 && $bareme_id <= 0) {
		$ligne = null;
		$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$bareme_id = $ligne[0];
	}
		
	
	
	if($bareme_id > 0) {
		// Rechercher les frais pour le bareme selectionne
		$sql  ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle ";
		$sql .= "FROM ".FIN_TAB_FRAIS_BAREME." fb ";
		$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
		$sql .= "WHERE fb.bareme_id = $bareme_id ";
		$sql .= "ORDER BY tf.libelle ASC";
		//echo $sql;
		$frais_bareme=execSql($sql);
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_FIN_INSC_006; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<input type="hidden" name="operation" id="operation" value="">
						
						<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class; ?>">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">
								
									<?php
									//*******************  LISTE DES BSAREMES *********************
									
									?>
									<fieldset style="z-index:5">
										<legend><?php echo LANG_FIN_BARE_004; ?></legend>
										<?php
										// Verifier si on a au moins un bareme
										if($baremes->numRows() > 0) {
										?>
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_BARE_004; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<select name="bareme_id" id="bareme_id" onChange="onchange_bareme_id()">
														<?php
														for($i=0; $i<$baremes->numRows(); $i++) {
															$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
															$selected = '';
															if($bareme_id == $ligne[0]) {
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
											<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_FIN_BARE_005; ?></span></div>
										<?php
										}
										?>
									</fieldset>
									
									
									<?php
									//*******************  LISTE DES FRAIS (OPTIONNELS OU NON) *********************
									
									if($bareme_id > 0) {
									?>
									<br>
									<fieldset style="z-index:4">
										<legend><?php echo LANG_FIN_FBAR_003; ?></legend>

											<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
												<tr bgcolor="#ffffff">
													<td align="left"><b><?php echo LANG_FIN_GENE_010; ?></b></td>
													<td align="right"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
												</tr>
											<?php
											if($frais_bareme->numRows() > 0) {
												for($i=0; $i<$frais_bareme->numRows(); $i++) {
													$ligne = $frais_bareme->fetchRow();
											?>
												<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
													<td><?php echo $ligne[5]; ?></td>
													<?php
													// Remplacer le separateur de decimal bdd, par le francais
													$valeur = montant_depuis_bdd($ligne[3]);
													?>
													<td nowrap="nowrap" align="right"><?php echo $valeur; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
												</tr>										
											<?php
												}
											} else {
											?>
											<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
												<td align="left" colspan="4"><?php echo LANG_FIN_FBAR_005; ?></td>
											</tr>
											<?php
											}
											?>
											</table>
									</fieldset>
									<?php
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
										<tr>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_INSC_004?>","onclick_inscrire()");</script>
											</td>
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

						function onclick_annuler() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_annuler').submit();
						}

						
						// Validation sonnees
						function onclick_valider() {
							var message_erreur = '';
							var separateur = '';
							var valide = true;
							var messsage;
							var obj;
							var i=0;

							obj = document.getElementById('libelle_exemple');
							if(trim(obj.value) == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_ECHE_006); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
							

							obj = document.getElementById('date_exemple');
							if(!est_date(obj.value, false)) {
								messsage = "<?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_INSC_012); ?>";
								messsage = messsage.replace('#i#', i);
								message_erreur += separateur + "     - " + messsage;
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}

							if(valide) {
								msg_util_attente_montrer(true);
								document.formulaire.operation.value = 'operation_exemple';
								document.formulaire.but_actualiser.click();

							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}


						}
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>inscription_rechercher.php" method="post">
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