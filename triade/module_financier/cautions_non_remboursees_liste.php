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
	
	// Rechercher la liste des cautions non remboursees
	$sql  = "SELECT e.elev_id, e.nom, e.prenom, c.code_class, c.libelle, i.inscription_id, i.annee_scolaire, fi.montant, tf.libelle ";
	$sql .= "FROM (((".FIN_TAB_ELEVES." e ";
	$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON e.elev_id = i.elev_id) ";
	$sql .= "INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class) ";
	$sql .= "INNER JOIN ".FIN_TAB_FRAIS_INSCRIPTION." fi ON i.inscription_id = fi.inscription_id) ";
	$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fi.type_frais_id = tf.type_frais_id ";
	$sql .= "WHERE tf.caution = 1 ";
	$sql .= "AND fi.caution_remboursee = 0 ";
	$sql .= "ORDER BY e.nom, e.prenom, e.elev_id, c.libelle, i.annee_scolaire DESC, tf.libelle ";
	//echo $sql;
	$cautions = execSql($sql);
	
	$tab_cautions = array();
	if($cautions->numRows() > 0) {
		for($i=0; $i<$cautions->numRows(); $i++) {
			$ligne = $cautions->fetchRow();
			$tab_cautions[count($tab_cautions)] = array(
					'eleves_elev_id' => $ligne[0],
					'eleves_nom' => $ligne[1],
					'eleves_prenom' => $ligne[2],
					'classes_code_class' => $ligne[3],
					'classes_libelle' => $ligne[4],
					'inscriptions_inscription_id' => $ligne[5],
					'inscriptions_annee_scolaire' => $ligne[6],
					'frais_inscription_montant' => $ligne[7],
					'type_frais_libelle' => $ligne[8],
			);
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_FIN_CANR_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center" style="padding-left:15px; padding-right:15px;">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">
								

									
									
									<?php
									//*******************  LISTE DES CAUTIONS *********************
									?>
									
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
										<tr bgcolor="#ffffff">
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_ELEV_005; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_ELEV_004; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_011; ?></b></td>
											<td align="left" nowrap="nowrap"><b><?php echo LANG_FIN_FBAR_004; ?></b></td>
											<td align="right" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_013; ?></b></td>
											<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
											<td align="center" nowrap="nowrap">&nbsp;</td>
										</tr>
									<?php
									$elev_id_precedent = 0;
									$inscription_id_precedent = 0;
									if(count($tab_cautions) > 0) {
										for($i=0; $i<count($tab_cautions); $i++) {
											// Rechercher combien on a de lignes pour l'eleve courant (et on calcule le montant par la meme occasion)
											$nombre_lignes_eleve = 0;
											$montant_total_eleve = 0.0;
											for($j=0; $j<count($tab_cautions); $j++) {
												if($tab_cautions[$j]['eleves_elev_id'] == $tab_cautions[$i]['eleves_elev_id']) {
													$nombre_lignes_eleve++;
												}
											}
											
											// Rechercher combien de lignes on a pour cette inscription
											$nombre_lignes_inscription = 0;
											for($j=0; $j<count($tab_cautions); $j++) {
												if($tab_cautions[$j]['inscriptions_inscription_id'] == $tab_cautions[$i]['inscriptions_inscription_id']) {
													$nombre_lignes_inscription++;
													$montant_total_eleve += $tab_cautions[$j]['frais_inscription_montant'];
												}
											}
											
									?>
										<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
											<?php
											// N'afficher certaines lignes que la premiere fois pour un meme eleve
											if($tab_cautions[$i]['eleves_elev_id'] != $elev_id_precedent) {
												$elev_id_precedent = $tab_cautions[$i]['eleves_elev_id'];
											?>
											<td nowrap="nowrap" align="left" valign="top" rowspan="<?php echo $nombre_lignes_eleve; ?>"><?php echo strtoupper($tab_cautions[$i]['eleves_nom']); ?></td>
											<td nowrap="nowrap" align="left" valign="top" rowspan="<?php echo $nombre_lignes_eleve; ?>"><?php echo ucfirst($tab_cautions[$i]['eleves_prenom']); ?></td>
											<?php
											}
											?>
											
											
											<?php
											// N'afficher certaines lignes que la premiere fois pour une meme inscription
											if($tab_cautions[$i]['inscriptions_inscription_id'] != $inscription_id_precedent) {
											?>
											<td nowrap="nowrap" align="left" valign="top" rowspan="<?php echo $nombre_lignes_inscription; ?>"><?php echo strtoupper($tab_cautions[$i]['classes_libelle']); ?></td>
											<td nowrap="nowrap" align="left" valign="top" rowspan="<?php echo $nombre_lignes_inscription; ?>"><?php echo strtoupper($tab_cautions[$i]['inscriptions_annee_scolaire']); ?></td>
											<?php
											}
											?>
											<td nowrap="nowrap" align="left" valign="top"><?php echo $tab_cautions[$i]['type_frais_libelle']; ?></td>
											<?php
											// Remplacer le separateur de decimal bdd, par le francais
											$valeur = montant_depuis_bdd($tab_cautions[$i]['frais_inscription_montant']);
											?>
											<td nowrap="nowrap" align="right" valign="top"><?php echo $valeur; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											<?php
											// N'afficher certaines lignes que la premiere fois pour une meme inscription
											if($tab_cautions[$i]['inscriptions_inscription_id'] != $inscription_id_precedent) {
												$inscription_id_precedent = $tab_cautions[$i]['inscriptions_inscription_id'];
											?>
											<?php
												// Remplacer le separateur de decimal bdd, par le francais
												$valeur = $montant_total_eleve;
											?>
											<td nowrap="nowrap" align="right" valign="top" rowspan="<?php echo $nombre_lignes_inscription; ?>"><?php echo $valeur; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											<td nowrap="nowrap" align="center" valign="top" rowspan="<?php echo $nombre_lignes_inscription; ?>">
												<input type="button" class="button" value="<?php echo LANG_FIN_INSC_003; ?>" onClick="onclick_editer_inscription(<?php echo $tab_cautions[$i]['inscriptions_inscription_id']; ?>);">
											</td>
											<?php
											}
											?>
										</tr>										
									<?php
										}
									} else {
									?>
									<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
										<td align="left" colspan="8"><?php echo LANG_FIN_CANR_002; ?></td>
									</tr>
									<?php
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

						
						function onclick_editer_inscription(inscription_id) {
							msg_util_attente_montrer(true);
							document.formulaire_editer_inscription.inscription_id.value = inscription_id;
							document.formulaire_editer_inscription.submit();
						}
						
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>paiements.php" method="post">
					</form>
					<form name="formulaire_editer_inscription" id="formulaire_editer_inscription" action="<?php echo $g_chemin_relatif_module; ?>inscription_editer.php" method="post">
						<input type="hidden" name="inscription_id" id="inscription_id" value="0">
						<input type="hidden" name="appelant" id="appelant" value="cautions_non_remboursees_liste">
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
