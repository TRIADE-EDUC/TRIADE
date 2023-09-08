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
	header('Location: ' . CHA_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>
<html>
	<head>
		<meta http-equiv="CacheControl" content = "no-cache">
		<meta http-equiv="pragma" content = "no-cache">
		<meta http-equiv="expires" content = -1>
		<meta name="Copyright" content="Triade©, 2001">
		<base href="<?php echo site_url_racine(CHA_REP_MODULE); ?>">
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
					<b><font id="menumodule1" ><?php echo LANG_CHA_PARA_001; ?></font></b>
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
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_CHA_BATI_001; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_009?>","onclick_batiment()");</script>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_CHA_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_CHA_BATI_002; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>
		
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="right" valign="middle"><font class="T2"><?php echo LANG_CHA_CHAM_001; ?>&nbsp;:&nbsp;</font></td>
											<td align="left" valign="middle">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_009?>","onclick_chambre()");</script>
											</td>
											<td align="left" valign="middle">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_CHA_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_CHA_CHAM_002; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
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

							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									
								</td>
							</tr>
							<tr>
								<td align="center">
									<table border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td align="center" colspan="2"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
										</tr>
										<tr>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_PLAN_001?>","onclick_planning()");</script>
											</td>
											<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="30" height="1"></td>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_RESA_001?>","onclick_reservation()");</script>
											</td>
										</tr>
										<tr>
											<td align="center" colspan="2"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
						
					</form>
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>


					<?php //********** GESTION NAVIGATION ********** ?>
					<script language="javascript">
						function onclick_batiment() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_batiment').submit();
						}
						function onclick_chambre() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_chambre').submit();
						}
						function onclick_planning() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_planning').submit();
						}
						function onclick_reservation() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_reservation').submit();
						}
					</script>
					<form name="formulaire_batiment" id="formulaire_batiment" action="<?php echo $g_chemin_relatif_module; ?>batiment_ajout.php" method="post">
					</form>
					<form name="formulaire_chambres" id="formulaire_chambre" action="<?php echo $g_chemin_relatif_module; ?>chambre_ajout.php" method="post">
					</form>
					<form name="formulaire_planning" id="formulaire_planning" action="<?php echo $g_chemin_relatif_module; ?>planning_liste.php" method="post">
					</form>
					<form name="formulaire_reservation" id="formulaire_reservation" action="<?php echo $g_chemin_relatif_module; ?>reservation_liste.php" method="post">
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
