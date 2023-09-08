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
	$id = lire_parametre('id', 0, 'POST');
	$libelle = lire_parametre('libelle', '', 'POST');
	$adresse_1 = lire_parametre('adresse_1', '', 'POST');
	$adresse_2 = lire_parametre('adresse_2', '', 'POST');
	$adresse_3 = lire_parametre('adresse_3', '', 'POST');
	$code_postal = lire_parametre('code_postal', '', 'POST');
	$ville = lire_parametre('ville', '', 'POST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	if($operation == "enregistrer") {
		if($id > 0) {
			$sql= "UPDATE ".CHA_TAB_BATIMENT." ";
			$sql.="SET libelle = '" . esc($libelle) . "' ";
			$sql.=", adresse_1 = '" . esc($adresse_1) . "' ";
			$sql.=", adresse_2 = '" . esc($adresse_2) . "' ";
			$sql.=", adresse_3 = '" . esc($adresse_3) . "' ";
			$sql.=", code_postal = '" . esc($code_postal) . "' ";
			$sql.=", ville = '" . esc($ville) . "' ";
			$sql.="WHERE batiment_id = $id ";
			$res=execSql($sql);
			//echo $sql;
			// Verifier si la requete sql a reussi ou non
			if(is_object($res) || (is_numeric($res) && $res > 0))
			{
				msg_util_ajout(LANG_CHA_GENE_001);
			} else {
				msg_util_ajout(LANG_CHA_GENE_057, 'erreur');
			}
		} else {
			msg_util_ajout(LANG_CHA_GENE_006, 'erreur');
		}
	}
	//***************************************************************************
	
		// Rechercher la liste des batiments
	$sql ="SELECT batiment_id, libelle, adresse_1, adresse_2, adresse_3, code_postal, ville ";
	$sql.="FROM ".CHA_TAB_BATIMENT." ";
	$sql.="WHERE batiment_id = $id";
	$res=execSql($sql);

	// Verifier si la requete sql a reussi ou non
	if(is_object($res) || (is_numeric($res) && $res > 0)) {
		if($res->numRows() <= 0) {
			//*************** GESTION DES AVERTISSEMENTS/ERREURS **************************************
			// Erreur : Informer l'utilisateur qu'il n'y a pas de type de frais disponible.
			if($type_frais_id <= 0) {
				msg_util_ajout(LANG_CHA_GENE_006, 'erreur');
			}
			//***************************************************************************
		}
	
	} else {
		msg_util_ajout(LANG_CHA_GENE_006, 'erreur');
	}
	
	
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
					<b><font id="menumodule1" ><?php echo LANG_CHA_BATI_009; ?></font></b>
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
							<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
							
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
											<td valign="top" align="right"><?php echo LANG_CHA_BATI_012; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="libelle" id="libelle" size="30" maxlength="64" value="<?php echo $ligne[1]; ?>">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_BATI_013; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="adresse_1" id="adresse_1" size="30" maxlength="64" value="<?php echo $ligne[2]; ?>">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right">&nbsp;</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="adresse_2" id="adresse_2" size="30" maxlength="64" value="<?php echo $ligne[3]; ?>">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right">&nbsp;</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="adresse_3" id="adresse_3" size="30" maxlength="64" value="<?php echo $ligne[4]; ?>">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_BATI_014; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="code_postal" id="code_postal" size="5" maxlength="5" value="<?php echo $ligne[5]; ?>">
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_BATI_015; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="ville" id="ville" size="30" maxlength="64" value="<?php echo $ligne[6]; ?>">
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
												<script language="javascript">buttonMagicSubmit("<?php print LANG_CHA_BATI_006?>","create");</script>
											</td>
											<?php
											}
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_003?>","onclick_annuler()");</script>
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
								message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_BATI_012); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}

							obj = document.getElementById('adresse_1');
							obj.value = trim(obj.value);
							if(obj.value == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_BATI_013 . ' n°1'); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
							
							obj = document.getElementById('code_postal');
							obj.value = trim(obj.value);
							if(obj.value == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_BATI_014); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							} else {
								if(!est_nombre(obj.value, 'entier', '')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_002, LANG_CHA_BATI_014); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								} else {
									if(obj.value.length != 5) {
										message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_008, LANG_CHA_BATI_014, 5); ?>";
										separateur = "\n";
										if(valide) {
											obj.focus();
										}
										valide = false;
									}
								}
							}

							obj = document.getElementById('ville');
							obj.value = trim(obj.value);
							if(obj.value == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_BATI_015); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
														
							if(valide) {
								msg_util_attente_montrer(true);
							} else {
								alert("<?php echo LANG_CHA_VALI_001; ?> : \n" + message_erreur);
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
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>batiment_liste.php" method="post">
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
											"lien_avec" : '<?php echo site_url_racine(CHA_REP_MODULE); ?>#',
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