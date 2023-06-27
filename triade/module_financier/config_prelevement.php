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
	
	
	$libelle = lire_parametre('libelle', '', 'POST');
	$nomfichier = lire_parametre('nomfichier', '', 'POST');
	$emetteur = lire_parametre('emetteur', '', 'POST');
	$titulaire = lire_parametre('titulaire', '', 'POST');
	$banque = lire_parametre('banque', '', 'POST');
	$codeguichet = lire_parametre('codeguichet', '', 'POST');
	$codebanque = lire_parametre('codebanque', '', 'POST');
	$numcompte = lire_parametre('numcompte', '', 'POST');
	$ref = lire_parametre('ref', '', 'POST');
	//***************************************************************************
	//*************** TRAITER L'OPERATION DEMANDEE ****************
	if($operation == "enregistrer") {
			$sql1=	"UPDATE ".FIN_TAB_CONFIG_ECOLE." ";
			$sql1.="SET libelle = '" . $libelle . "' ";
			$sql1.=", nom_fichier = '" . $nomfichier . "' ";
			$sql1.=", numemet = '". $emetteur ."' ";
			$sql1.=", icb = '". $titulaire ."' ";
			$sql1.=", dom  = '". $banque ."' ";
			$sql1.=", cg  = '". $codeguichet ."' ";
			$sql1.=", cb  = '". $codebanque ."' ";
			$sql1.=", compt = '". $numcompte ."' ";
			$sql1.=", ref  = '". $ref ."' ";
		
			$res1=execSql($sql1);
			
			msg_util_ajout(LANG_FIN_GENE_001);
	}
	//***************************************************************************
	
	$sql = "SELECT nom_fichier, numemet, icb, dom, cg, compt, libelle, cb, ref ";
	$sql.= "FROM ".FIN_TAB_CONFIG_ECOLE." ";
	
	$res = execSql($sql);
	
	$ligne = $res->fetchRow();
							
	
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
					<b><font id="menumodule1" ><?php echo LANG_FIN_PARP_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="return valider_le_formulaire();">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<input type="hidden" name="operation" id="operation" value="enregistrer">
							
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
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_002; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="nomfichier" id="nomfichier" size="30" maxlength="32" value="<?php echo $ligne[0]; ?>">
											</td>
										</tr>
										
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_009; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="libelle" id="libelle" size="30" maxlength="31" value="<?php echo $ligne[6]; ?>">
											</td>
										</tr>
										
										
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_003; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="emetteur" id="emetteur" size="30" maxlength="6" value="<?php echo $ligne[1]; ?>">
											</td>
										</tr>
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_004; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="titulaire" id="titulaire" size="30" maxlength="24" value="<?php echo $ligne[2]; ?>">
											</td>
										</tr>
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_005; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="banque" id="banque" size="30" maxlength="24" value="<?php echo $ligne[3]; ?>">
											</td>
										</tr>
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_006; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="codeguichet" id="codeguichet" size="30" maxlength="5" value="<?php echo $ligne[4]; ?>">
											</td>
										</tr>
										
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_007; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="codebanque" id="codebanque" size="30" maxlength="5" value="<?php echo $ligne[7]; ?>">
											</td>
										</tr>
										
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_008; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="numcompte" id="numcompte" size="30" maxlength="11" value="<?php echo $ligne[5]; ?>">
											</td>
										</tr>
									
										<tr>
											<td align="center" colspan="3">&nbsp;</td>
										</tr>
										
										<tr>
											<td valign="top" align="right"><?php echo LANG_FIN_PARP_010; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="ref" id="ref" size="30" maxlength="7" value="<?php echo $ligne[8]; ?>">
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
							
							<?php //********** BOUTONS ********** ?>
						
							<tr>
								<td align="center" colspan="3">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<?php
											if($res->numRows() > 0) {
											?>
											<td align="center">
												<script language="javascript">buttonMagicSubmit("<?php print LANG_FIN_PARP_011?>","create");</script>
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
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004,LANG_FIN_GROUPE_004); ?>";
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
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
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