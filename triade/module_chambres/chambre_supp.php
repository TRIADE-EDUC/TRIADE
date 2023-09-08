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
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	if($operation == "enregistrer") {
		if($id > 0) {
			$sql= "DELETE FROM ".CHA_TAB_CHAMBRE." ";
			$sql.="WHERE chambre_id = $id ";
			$res=execSql($sql);
			//echo $sql;
			msg_util_ajout(LANG_CHA_GENE_007);
		} else {
			msg_util_ajout(LANG_CHA_GENE_006, 'erreur');
		}
	}
	//***************************************************************************

	// Rechercher les enregistrement
	$sql ="SELECT c.chambre_id, b.libelle as libelle_batiment, c.numero, c.libelle as libelle_chambre, c.type_chambre_id, c.etage_id ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." c LEFT JOIN ".CHA_TAB_BATIMENT." b ON c.batiment_id = b.batiment_id ";
	$sql.="ORDER BY b.libelle ASC, c.numero ASC, c.libelle ASC";
	$res=execSql($sql);


	// Rechercher la liste des chambres deja utilisees dans les inscriptions (=> elles ne pourront pas etre supprimees)
	$sql ="SELECT c.chambre_id ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." c INNER JOIN ".CHA_TAB_RESERVATION." i ON c.chambre_id = i.chambre_id ";
	$sql.="GROUP BY c.chambre_id ";
	$res_chambres_utilises=execSql($sql);
	//echo $sql;
	
	$chambres_utilises = array();
	for($i=0;$i<$res_chambres_utilises->numRows();$i++) {
		$ligne_tmp = $res_chambres_utilises->fetchRow();
		$chambres_utilises[$i] = $ligne_tmp[0];
	}
	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS **************************************
	// Avertissement : Informer l'utilisateur qu'il n'y a pas de type de frais disponible.
	if($res->numRows() == 0) {
		msg_util_ajout(LANG_CHA_CHAM_010, 'avertissement');
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
					<b><font id="menumodule1" ><?php echo LANG_CHA_CHAM_007; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="return valider_le_formulaire();">
					
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
							<?php
							if($res->numRows() > 0) {
							?>
							
							<input type="hidden" name="operation" id="operation" value="enregistrer">
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">
							
									<table border="0" cellpadding="0" cellspacing="0" align="center">							
										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_CHAM_012; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<select name="id" id="id">
												<?php
													for($i=0; $i<$res->numRows(); $i++) {
														$ligne = &$res->fetchRow();
														$selected = '';
														if($i == 0) {
															$selected = 'selected';
														}
														
														$classe = '';
														if(in_array($ligne[0], $chambres_utilises)) {
															$classe = 'element_liste_grise';
														}
														
														$separateur = '';
														$texte = trim($ligne[1]);
														if(trim($texte) != '') {
															$separateur = ' - ';
														}
														
														eval('$texte .= $separateur . LANG_CHA_ETAG_ID_' . $ligne[5] .'_LIBELLE;');
														eval('$exposant = LANG_CHA_ETAG_ID_' . $ligne[5] .'_EXPOSANT;');
														if(trim($exposant) != '') {
															$texte .= ' ' . $exposant . '';
														}
														if(trim($texte) != '') {
															$separateur = ' - ';
														}
														
														
														if(trim($ligne[2]) != '') {
															$texte .= $separateur . 'n°' .trim($ligne[2]);
															$separateur = ' - ';
														}

														eval('$texte .= $separateur . LANG_CHA_TCHA_ID_' . $ligne[4] .';');
														$separateur = ' - ';
															
														if(trim($ligne[3]) != '') {
															$texte .= $separateur . trim($ligne[3]);
															$separateur = ' - ';
														}
														
												?>
													<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo $texte; ?></option>
												<?php
													}
												?>
												</select>
											</td>
											<td>&nbsp;</td>
											<td valign="top">
												<a href='javascript:;'  onMouseOver="AffBulle3('<?php echo LANG_CHA_GENE_002; ?>','./image/commun/info.jpg','<?php echo LANG_CHA_CHAM_100; ?>', '');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
											</td>
										</tr>
									</table>
									
								</td>
							<tr>
							<?php
							}
							?>
							<tr>
								<td align="center">&nbsp;</td>
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
											<?php
											if($res->numRows() > 0) {
											?>
											<td align="center">
												<script language="javascript">buttonMagicSubmit("<?php print LANG_CHA_CHAM_011?>","create"); //text,nomInput</script>
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
							
							// On verifie que le 'type de reglement' peut etre supprime
							obj = document.getElementById('id');
							if(obj.options[obj.selectedIndex].className == 'element_liste_grise') {
								alert("<?php echo LANG_CHA_CHAM_101; ?>");
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
							
							if(valide) {
								if(confirm("<?php echo LANG_CHA_GENE_008; ?>")) {
									msg_util_attente_montrer(true);
								} else {
									valide = false;
									msg_util_attente_cacher();
								}
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
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>chambre_ajout.php" method="post">
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
