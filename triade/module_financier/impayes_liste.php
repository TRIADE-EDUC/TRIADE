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
	$type_reglement_id = lire_parametre('type_reglement_id', 0, 'POST');
	$date_limite = lire_parametre('date_limite', '', 'POST');
	//***************************************************************************

	
	// Initialiser la date limite si elle est vide
	if($date_limite == '') {
		$date_limite = date('d/m/Y');
	}

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	//***************************************************************************
	
	// Rechercher la liste des types de reglement
	$sql ="SELECT tr.type_reglement_id, tr.libelle ";
	$sql.="FROM ".FIN_TAB_TYPE_REGLEMENT." tr ";
	$sql.="INNER JOIN ".FIN_TAB_ECHEANCIER." e ON tr.type_reglement_id = e.type_reglement_id ";
	$sql.="GROUP BY tr.type_reglement_id, tr.libelle ";
	$sql.="ORDER BY tr.libelle ASC ";
	$types_reglement=execSql($sql);

	if($operation == 'rechercher') {
		if($types_reglement->numRows() > 0) {
			
			/*
			// Preselectionner le premier type de reglement si il n'y en a pas déjà un
			if($type_reglement_id == 0) {
				$res = $type_reglement_id->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
				$type_reglement_id = ligne[0];
			}
			*/
		
			// Rechercher la liste des echeances expirees
			$sql ="SELECT e.elev_id, e.nom, e.prenom, c.code_class, c.libelle, i.inscription_id, i.date_inscription, i.annee_scolaire, ec.echeancier_id, ec.date_echeance, ec.montant, tr.libelle, i.date_depart ";
			$sql.="FROM ((".FIN_TAB_INSCRIPTIONS." i ";
			$sql.="INNER JOIN ".FIN_TAB_ELEVES." e ON i.elev_id = e.elev_id) ";
			$sql.="INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class) ";
			$sql.="INNER JOIN ".FIN_TAB_ECHEANCIER." ec ON i.inscription_id = ec.inscription_id ";
			$sql.="INNER JOIN ".FIN_TAB_TYPE_REGLEMENT." tr ON ec.type_reglement_id = tr.type_reglement_id ";
			$sql.="WHERE ec.date_echeance < '" . date_vers_bdd($date_limite) . "' ";
			//$sql.="WHERE ec.date_echeance < CURDATE() ";
			$sql.="AND ec.montant > 0 ";
			$sql.="AND ec.impaye = 0 ";
			$sql.="AND ec.type <> 2 "; // => Ne pas inclure les remises exceptionnelles 
			if($type_reglement_id != 0) {
				$sql.="AND ec.type_reglement_id = $type_reglement_id ";
			}
			$sql.="ORDER BY LEFT(i.annee_scolaire, 4) DESC, ec.date_echeance DESC, tr.type_reglement_id";
			//echo $sql;
			$impayes=execSql($sql);
			
			// Verifier si l'echeance a ete payee ou non et stocker pour affichage
			$tab_impayes = array();
			$reste_a_payer = 0;
			if($impayes->numRows() > 0) {
				for($i=0; $i<$impayes->numRows(); $i++) {
					$ligne = $impayes->fetchRow();
					$reste_a_payer = reglement_reste_a_payer('echeance', $ligne[8]);
					
					// Verifier si l'eleve est partit ou non
					$eleve_partit = false;
					$date_depart = $ligne[12];
					if(!is_null($date_depart) && !empty($date_depart)) {
						if(trim($date_depart) != '') {
							$date_depart = strtotime($date_depart);
							$date_echeance = strtotime($ligne[9]);
							if($date_echeance >= $date_depart) {
								$eleve_partit = true;
							}
						}
					}
					
					if($reste_a_payer > 0 && !$eleve_partit) {
						$tab_impayes[count($tab_impayes)] = array(
															"eleves_elev_id" => $ligne[0],
															"eleves_nom" => $ligne[1],
															"eleves_prenom" => $ligne[2],
															"classes_code_class" => $ligne[3],
															"classes_libelle" => $ligne[4],
															"inscription_inscription_id" => $ligne[5],
															"inscription_date_inscription" => $ligne[6],
															"inscription_annee_scolaire" => $ligne[7],
															"echeancier_echeancier_id" => $ligne[8],
															"echeancier_date_echeance" => $ligne[9],
															"echeancier_montant" => $ligne[10],
															"reste_a_payer" => $reste_a_payer,
															"type_reglement_libelle" => $ligne[11]
															);
				
					}
				}
			}
		}	
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
					<b><font id="menumodule1" ><?php echo LANG_FIN_IMPA_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						<input type="hidden" name="operation" id="operation" value="">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							
							<tr>
								<td align="center">
									<?php
									// Pour la gestion des calendriers
									include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");

									//*******************  CRITERES DE RECHERCHE *********************
									
									?>								
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td align="right"><?php echo LANG_FIN_TREG_015; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<?php
												// Verifier si on a au moins un type de reglement
												if($types_reglement->numRows() > 0) {
												?>
												<select name="type_reglement_id" id="type_reglement_id" onChange="onchange_type_reglement_id();">
													<?php
													$selected = '';
													if($type_reglement_id == 0) {
														$selected = 'selected="selected"';
													}
													?>
													<option value="0" <?php echo $selected; ?>><?php echo ucfirst(LANG_FIN_GENE_025); ?></option>
													<?php
													for($i=0; $i<$types_reglement->numRows(); $i++) {
														$res = $types_reglement->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
														
														$selected = '';
														if($type_reglement_id != 0 && ($type_reglement_id == $ligne[0])) {
															$selected = 'selected="selected"';
														}
													?>
													<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo $ligne[1]; ?></option>
													<?php
													}
													?>
												</select>
												<?php
												} else {
													echo LANG_FIN_GENE_049;
												}
												?>
											</td>
										</tr>
										<tr>
											<td align="right"><?php echo LANG_FIN_GPRE_002; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<table cellspacing="0" cellpadding="0" border="0">
													<tr>
														<td align="left">
															<?php
															$valeur = $date_limite;
															?>
															<input type="text" name="date_limite" id="date_limite" size="10" maxlength="10" value="<?php echo $valeur; ?>">
														</td>
														<td>&nbsp;</td>
														<td align="left">
															<?php
															calendarDim("div_date_limite","document.formulaire.date_limite",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
															?>
														</td>
														<td>&nbsp;</td>
														<td valign="middle">
														<a href="javascript:;"  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg',' <?php echo LANG_FIN_GPRE_003; ?>', 'fieldset_criteres');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"></a>

														</td>
													</tr>
												</table>
											</td>
										</tr>		
											<tr>
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" align="center">
													<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_rechercher();">
												</td>
												<td align="center">
													<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
												</td>
											</tr>																		
									</table>
									
								</td>
							</tr>

							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							
							<?php if($operation == 'rechercher') { ?>
							<tr bgcolor="#ffffff">
								<td>
									<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
								</td>
							</tr>
							
							<br>
							
							<tr>
								
								<td valign=top align="center">
									<br>
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
										
										
										<tr bgcolor="#ffffff">
											<td align="right" nowrap="nowrap">#</td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_011; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_ELEV_004; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_ELEV_005; ?></b></td>
											<td align="center" nowrap="nowrap"><b><?php echo LANG_FIN_ECHE_004; ?></b></td>
											<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
											<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_034; ?></b></td>
											<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_TREG_015; ?></b></td>
											<td width="5%" nowrap="nowrap">&nbsp;</td>
										</tr>
										<?php
										$total_reste_a_payer = 0.0;
										if(count($tab_impayes) > 0) {
											for($i=0; $i<count($tab_impayes); $i++) {
												$total_reste_a_payer += $tab_impayes[$i]['reste_a_payer'];
										?>
										<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
											<td valign="middle" nowrap="nowrap"><?php echo ($i+1); ?></td>
											<td align="left" valign="middle" nowrap="nowrap"><?php echo ucfirst($tab_impayes[$i]['classes_libelle']); ?></td>
											<td align="left" valign="middle" nowrap="nowrap"><?php echo ucfirst($tab_impayes[$i]['inscription_annee_scolaire']); ?></td>
											<td align="left" valign="middle" nowrap="nowrap"><?php echo ucfirst($tab_impayes[$i]['eleves_prenom']); ?></td>
											<td align="left" valign="middle" nowrap="nowrap"><?php echo strtoupper($tab_impayes[$i]['eleves_nom']); ?></td>
											<td align="center" valign="middle" nowrap="nowrap"><?php echo date_depuis_bdd($tab_impayes[$i]['echeancier_date_echeance']); ?></td>
											<td align="right" valign="middle" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_impayes[$i]['echeancier_montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											<td align="right" valign="middle" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_impayes[$i]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											<td align="right" valign="middle" nowrap="nowrap"><?php echo $tab_impayes[$i]['type_reglement_libelle']; ?></td>
											<td align="left" nowrap="nowrap"><input type="button" class="button" value="<?php echo LANG_FIN_INSC_003; ?>" onClick="onclick_editer_inscription(<?php echo $tab_impayes[$i]['inscription_inscription_id']; ?>);"></td>
										</tr>
										<?php
											}
										?>
										<tr class='tabnormal2'>
											<td colspan="6">&nbsp;</td> 
											<td align="right"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td> 
											<td align="right"><b><?php echo montant_depuis_bdd($total_reste_a_payer); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
											<td colspan="2">&nbsp;</td> 
										</tr>
										<?php
										} else {
										?>
										<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
											<td align="left" colspan="10"><?php echo LANG_FIN_IMPA_004; ?></td>
										</tr>
										<?php
										}
										?>
									</table>

								</td>
							</tr>
		
							<?php } //********** MESSAGES UTILISATEUR ********** ?>
							
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
				
				
							<?php //********** BOUTONS **********
							if($operation == 'rechercher') {
							?>
							
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
								
							<?php  } ?>
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

						function onclick_editer_inscription(inscription_id) {
							msg_util_attente_montrer(true);
							document.formulaire_editer_inscription.inscription_id.value = inscription_id;
							document.formulaire_editer_inscription.submit();
						}
						
						function onchange_type_reglement_id() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = 'rechercher';
							document.formulaire.but_actualiser.click();
						}
						function onclick_rechercher() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = 'rechercher';
							document.formulaire.but_actualiser.click();
						}
						
						function onclic_export_excel() {
							document.getElementById('formulaire_export_excel').submit();
							}
						
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>paiements.php" method="post">
					</form>
					<form name="formulaire_editer_inscription" id="formulaire_editer_inscription" action="<?php echo $g_chemin_relatif_module; ?>inscription_editer.php" method="post">
						<input type="hidden" name="inscription_id" id="inscription_id" value="0">
						<input type="hidden" name="appelant" id="appelant" value="impayes_liste">
					</form>
					<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>impayes_liste_excel.php" method="post" target="">
						<input type="hidden" name="date_limite" id="date_limite" value="<?php echo $date_limite?>">
						<input type="hidden" name="type_reglement_id" id="type_reglement_id" value="<?php echo $type_reglement_id?>">
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
