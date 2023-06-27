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
	$type_reglement_id = lire_parametre('type_reglement_id','', 'POST');
	$suppression = lire_parametre('supp', '', 'GET');
	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Initialisation sur changement de classe
	if($operation == "reload_code_class") {
		$annee_scolaire = '';
	}

	//***************************************************************************
	
	// Recherche des type de reglements existants
	$tab_type_reglement = array();
	$sql  = "SELECT type_reglement_id, libelle ";
	$sql .= "FROM ".FIN_TAB_TYPE_REGLEMENT." ";
	$sql .= "ORDER BY libelle ";
	//echo $sql;
	$res_type_reglement=execSql($sql);
	for($i=0; $i<$res_type_reglement->numRows(); $i++) {
		$res = $res_type_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $i);
		$tab_type_reglement[$ligne_type_reglement[0]] = $ligne_type_reglement[1];
	}

	if($operation == 'rechercher') {
		$sql = "SELECT numero_bordereau, date_remise_bordereau ";
		$sql.= "FROM ".FIN_TAB_REGLEMENT." ";
		$sql.= "WHERE type_reglement_id = $type_reglement_id ";
		$sql.= "GROUP BY numero_bordereau ";
		$sql.= "ORDER BY 1 DESC ";
		$res1=execSql($sql);
	}
	
	if($suppression != '')
	{
		$vide = '';
		$sql = "UPDATE ".FIN_TAB_REGLEMENT." ";
		$sql.= "SET numero_bordereau = ' ' ";
		$sql.= "WHERE numero_bordereau = $suppression";
		$res=execSql($sql);
		
		header('Location: export_bordereau.php') ;
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

		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		// Verification droits acces groupe
		validerequete("2");
		?>
		<?php //********** GENERATION DU DEBUT DE LA PAGE ET DES MENUS PRINCIPAUX ********** ?>
		
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
		<div align="<?php echo $alignement_cadre; ?>">
			<!-- TITRE ET CADRE CENTRAL -->
			<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" >
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo "Rééditer bordereau"; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
					</td>
				</tr>
				<tr id="cadreCentral0">
					<td valign="top" align="center">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
								<?php //********** AFFICHAGE DES DONNEES ********** ?>
								
								<tr>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" valign="top" align="center">
										<form name="formulaire_criteres" id="formulaire_criteres" action="<?php echo url_script(); ?>" method="post" onSubmit="">
											<input type="hidden" name="operation" id="operation" value="">
	
											<?php //********** TYPE DE REGLEMENT ********** ?>
										
											<fieldset id="fieldset_criteres" style="z-index:5; margin-left:15px; margin-right:15px;">
												<legend><?php echo LANG_FIN_GENE_021; ?></legend>
												<table cellpadding="0" cellspacing="2" align="center">
													<tr>
														<td align="right"><?php echo LANG_FIN_TREG_015; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<select name="type_reglement_id" id="type_reglement_id" onChange="onchange_type_reglement_id()">
																<?php
																$selected = '';
																if($type_reglement_id == $g_tab_type_reglement_id['cheque']) {
																	$selected = 'selected="selected"';
																}
																?>
																<option value="<?php echo $g_tab_type_reglement_id['cheque']; ?>" <?php echo $selected; ?> ><?php echo $tab_type_reglement[$g_tab_type_reglement_id['cheque']]; ?></option>
																<?php
																$selected = '';
																if($type_reglement_id == $g_tab_type_reglement_id['espece']) {
																	$selected = 'selected="selected"';
																}
																?>
																<option value="<?php echo $g_tab_type_reglement_id['espece']; ?>" <?php echo $selected; ?> ><?php echo $tab_type_reglement[$g_tab_type_reglement_id['espece']]; ?></option>
															</select>
														</td>
													</tr>
		
													<tr>
														<td colspan="2" align="center">&nbsp;</td>
													</tr>
													
													<tr>
														<td colspan="2" align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_rechercher();" <?php echo $disabled_cadre; ?>>
														</td>
														<td colspan="0" align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
														</td>
													</tr>
												</table>
												<br>
											</fieldset>
										</form>
									</td>
								</tr>
								
								<tr>
									<td valign="top" align="left" nowrap="nowrap">&nbsp;
										
									</td>
								</tr>
								
								<tr>
									<td colspan="2" valign="top" align="center">
										<table>
										<?php
											if($operation == 'rechercher') {
												// if($res1->numRows() > 2)
												// {
													for($i=0;$i<$res1->numRows();$i++) {
														$res2 = $res1->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
														
														if($ligne[0] != 0)
														{
															$date_remise = $ligne[1];
															$date = date_depuis_bdd($date_remise);
															switch($type_reglement_id) {
																case $g_tab_type_reglement_id['cheque']:
																	$nom_fichier = LANG_FIN_GBOR_017 . '_' . substr($date_remise, 8, 2) . '-' . substr($date_remise, 5, 2) . '-' . substr($date_remise, 0, 4);
																	break;
																case $g_tab_type_reglement_id['espece']:
																	$nom_fichier = LANG_FIN_GBOR_018 . '_' . substr($date_remise, 8, 2) . '-' . substr($date_remise, 5, 2) . '-' . substr($date_remise, 0, 4);
																	break;
															}
															$numero = $ligne[0];
															if($i == 0)
															{
																print "
																<tr>
																	<td>
																		<img src='./image/commun/on1.gif' width='8' height='8'>
																		<span id='disp$i'>$nom_fichier</span>
																		<a href='module_financier/bordereaux_reediter.php?num=$numero&type=$type_reglement_id' title=\"Générer bordereau\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:blue;font-weight:bold;'\"  
																		onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/pdf.png' border='0' align='center'/></a>
														
																		<a href='module_financier/export_bordereau.php?supp=$numero' title=\"Supprimer\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:red;font-weight:bold;'\"  
																		onclick=\"return(confirm('Etes-vous sûr de vouloir supprimer ce fichier du $date ?'))\"
																		onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/supprimer.png' border='0' align='center' width='24' height='24'/></a>
																	</td>
																</tr>";
															}
															else
															{
																
																print "
																<tr>
																	<td>
																		<img src='./image/commun/on1.gif' width='8' height='8'>
																		<span id='disp$i'>$nom_fichier</span>
																		<a href='module_financier/bordereaux_reediter.php?num=$numero&type=$type_reglement_id' title=\"Générer fichier de prélèvement\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:blue;font-weight:bold;'\"  
																		onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/pdf.png' border='0' align='center'/></a>
																		
																	</td>
																</tr>";
															}
														}
													}
												// }else
												// {
												// msg_util_ajout("Aucun bordereau", 'erreur');
												// }
											}
											?>	
										</table>
									</td>
								</tr>
	
			
								<?php //********** MESSAGES UTILISATEUR ********** ?>
								
								
								<tr>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<a name="MESSAGE"></a>
										<?php 
										msg_util_afficher();
										msg_util_attente_init(); 
										?>
										</td>
								</tr>
					
							</table>
						
						
						<?php //********** VALIDATION FORMULAIRES ********** ?>
				
				
						<?php //********** GESTION NAVIGATION ********** ?>
						
						<script language="javascript">
							var fenetre = null;
							var liste_fenetre = new Array();
							
							function onclick_annuler() {
								msg_util_attente_montrer(true);
								document.getElementById('formulaire_annuler').submit();
							}
							
							function onchange_type_reglement_id() {
							onclick_rechercher();
							}
							function onclick_rechercher() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "rechercher";
								document.formulaire_criteres.submit();
							}

							
						</script>
						<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>paiements.php" method="post">
						</form>
					</td>
				</tr>
			</table>
		</div>

		
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
