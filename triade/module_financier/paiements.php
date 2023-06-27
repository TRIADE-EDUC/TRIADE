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
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	if($operation == "enregistrer") {

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
					<b><font id="menumodule1" ><?php echo LANG_FIN_PAIE_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">

									<table border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_FIN_IMPA_001; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_009?>","onclick_impayes()");</script>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_IMPA_003; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>

										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_FIN_PAIE_003; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_009?>","onclick_generation_prelevements()");</script>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_PAIE_004; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>
<tr>
										<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_FIN_PAIE_011; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_009?>","onclick_ancien_prelevements()");</script>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_PAIE_012; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>
										
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_FIN_PAIE_007; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_009?>","onclick_cautions_non_remboursees()");</script>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_PAIE_008; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>
		
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_FIN_PAIE_005; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_009?>","onclick_generation_bordereaux()");</script>
											</td>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_PAIE_006; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>	
										
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_FIN_PAIE_013; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_009?>","onclick_ancien_bordereaux()");</script>
											</td>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_FIN_PAIE_006; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>	
										
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
												
									</table>
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
							
						</table>
						
					</form>
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>


					<?php //********** GESTION NAVIGATION ********** ?>
					<script language="javascript">
						function onclick_impayes() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_impayes').submit();
						}
						function onclick_generation_prelevements() {
							//alert("Fonctionalité en cours de développement...");
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_generation_prelevements').submit();
						}
						function onclick_cautions_non_remboursees() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_cautions_non_remboursees').submit();
						}
						function onclick_generation_bordereaux() {
							//alert("Fonctionalité en cours de développement...");
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_generation_bordereaux').submit();
						}
						function onclick_ancien_prelevements() {
							//alert("Fonctionalité en cours de développement...");
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_ancien_prelevements').submit();
						}
						function onclick_ancien_bordereaux() {
							//alert("Fonctionalité en cours de développement...");
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_ancien_bordereaux').submit();
						}
						
						
					</script>
					<form name="formulaire_impayes" id="formulaire_impayes" action="<?php echo $g_chemin_relatif_module; ?>impayes_liste.php" method="post">
					<input type="hidden" name="operation" id="operation" value="<?php echo ""?>">
					
					</form>
					<form name="formulaire_generation_prelevements" id="formulaire_generation_prelevements" action="<?php echo $g_chemin_relatif_module; ?>prelevements_rechercher.php" method="post">
					</form>
					<form name="formulaire_ancien_prelevements" id="formulaire_ancien_prelevements" action="<?php echo $g_chemin_relatif_module; ?>export.php" method="post">
					</form>
					<form name="formulaire_cautions_non_remboursees" id="formulaire_cautions_non_remboursees" action="<?php echo $g_chemin_relatif_module; ?>cautions_non_remboursees_liste.php" method="post">
					</form>
					<form name="formulaire_generation_bordereaux" id="formulaire_generation_bordereaux" action="<?php echo $g_chemin_relatif_module; ?>bordereaux_generer_rechercher.php" method="post">
					</form>
					<form name="formulaire_ancien_borderaux" id="formulaire_ancien_bordereaux" action="<?php echo $g_chemin_relatif_module; ?>export_bordereau.php" method="post">
					</form>
					<form name="formulaire_bareme" id="formulaire_bareme" action="<?php echo $g_chemin_relatif_module; ?>bordereaux.php" method="post">
					</form>

					<br>
					
				</td>
			</tr>
		</table>

		<?php
		}
		?>
		
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></script>
		
		
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
