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
	
	
	$operation1 = lire_parametre('operation1', '', 'POST');
	if($operation1 != ''){
	$elev_id_defaut = lire_parametre('elev_id_res', '', 'POST');
	$chambre_id_defaut = $_SESSION[CHA_REP_MODULE]['planning_reservation']['chambre_id'];
	$date_debut_defaut = $_SESSION[CHA_REP_MODULE]['planning_reservation']['date_debut'];
	$date_fin_defaut = $_SESSION[CHA_REP_MODULE]['planning_reservation']['date_fin'];
	
	$sql ="SELECT elev_id, prenom, nom ";
		$sql.="FROM ".CHA_TAB_ELEVES." ";
		$sql.="WHERE elev_id = $elev_id_defaut";
		$res=execSql($sql);
		if($res->numRows() > 0)
		{
			$ligne = &$res->fetchRow();
			$nomprenom = $ligne[1] . ' ' . $ligne[2];
		}
		
		$sql ="SELECT tb.batiment_id, tb.libelle, tb.adresse_1, tb.adresse_2, tb.adresse_3, tb.code_postal, tb.ville, tc.chambre_id, tc.numero, tc.libelle, tc.type_chambre_id ";
		$sql.="FROM ".CHA_TAB_BATIMENT." tb ";
		$sql.="INNER JOIN ".CHA_TAB_CHAMBRE." tc ON tb.batiment_id = tc.batiment_id ";
		$sql.="WHERE tc.chambre_id = $chambre_id_defaut";
		$res=execSql($sql);
		if($res->numRows() > 0)
		{
			$ligne = &$res->fetchRow();
			$batiment_id = $ligne[0];
			$batiment_texte = $ligne[1] . ' - ' . $ligne[5] . ' - ' . $ligne[6];
			
			$separateur = '';
			$chambre_texte = trim($ligne[8]);
			if(trim($chambre_texte) != '') {
				$chambre_texte = 'n°' . $chambre_texte;
				$separateur = ' - ';
			}
				
			eval('$chambre_texte .= $separateur . LANG_CHA_TCHA_ID_' . $ligne[10] .';');
			$separateur = ' - ';

			if(trim($ligne[9]) != '') {
				$chambre_texte .= $separateur . trim($ligne[9]);
				$separateur = ' - ';
			}
		}
	
	}
	
	
	$operation = lire_parametre('operation', '', 'POST');
	$eleve_id = lire_parametre('eleve_id', 0, 'POST');
	$chambre_id = lire_parametre('chambre_id', 0, 'POST');
	$date_debut = lire_parametre('date_debut', '', 'POST');
	$date_fin = lire_parametre('date_fin', '', 'POST');
	$commentaire = lire_parametre('commentaire', '', 'POST');
	
	$date_us = date_vers_bdd($date_debut_defaut);
	$annee = date("Y", strtotime($date_us));	
	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	if($operation == "enregistrer") {
		$sql= "INSERT INTO ".CHA_TAB_RESERVATION." (elev_id, chambre_id, date_debut, date_fin, commentaire, date_reservation) ";
		$sql.="VALUES(" . $eleve_id . ", " . $chambre_id . ", '" . date_vers_bdd($date_debut) . "', '" . date_vers_bdd($date_fin) . "', '" . esc($commentaire) . "', '" . date("Y-m-d H:i:s") . "'); ";
		
		$res_ope=execSql($sql);

		// Verifier si la requete sql a reussi ou non
		if(is_object($res_ope) || (is_numeric($res_ope) && $res_ope > 0))
        {
			header('Location: planning_liste.php');
			//msg_util_ajout(LANG_CHA_GENE_001);
		} else {
			msg_util_ajout(LANG_CHA_GENE_057, 'erreur');
			// Oblige de refaire une connexion a la bdd car il y a un bug
			// Des qu'une requete plante, toutes les suivante (meme valides) sur la meme connexion vont echouer !
			$cnx=cnx();
			//echo $sql;
		}
	}
	//***************************************************************************

	// Rechercher la liste des batiments disponibles
	$sql ="SELECT batiment_id, libelle, adresse_1, adresse_2, adresse_3, code_postal, ville ";
	$sql.="FROM ".CHA_TAB_BATIMENT." ";
	$sql.="ORDER BY libelle";
	$res_batiments=execSql($sql);

	// Rechercher la liste des batiments disponibles
	$sql ="SELECT chambre_id, numero, libelle ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." ";
	$sql.="ORDER BY libelle";
	$res_chambre=execSql($sql);

	
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
					<b><font id="menumodule1" ><?php echo LANG_CHA_RESA_012; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
						<tr>
							<td>
								<?php
								// Pour la gestion des calendriers
								include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");

								//*******************  CRITERES DE RECHERCHE *********************
								
								?>
								<form name="formulaire" id="formulaire" action="" method="post">
									<input type="hidden" name="operation" id="operation" value="enregistrer">
										<table border="0" cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td align="center" colspan="3">&nbsp;</td>
											</tr>
											<tr>
												<td align="right" valign="top"><?php echo LANG_CHA_RESA_009; ?></td>
												<td valign="top">&nbsp;:&nbsp;</td>
												<td align="left">
													<table border="0" cellpadding="0" cellspacing="0" align="left">
														<tr>
															<td align="left" valign="top" id="conteneur_eleve_id" nowrap="nowrap">
																<input type="hidden" name="eleve_id" id="eleve_id" value="<?php echo $elev_id_defaut;?>">
																<?php echo $nomprenom; ?>
															</td>
														</tr>
													</table>
													
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_005; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left">
													<?php echo $batiment_texte; ?>
													<input type="hidden" name="batiment_id" id="batiment_id" value="<?php echo $batiment_id;?>">
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_006; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left">
													<?php echo $chambre_texte; ?>
													<input type="hidden" name="chambre_id" id="chambre_id" value="<?php echo $chambre_id_defaut;?>">
													<?php print"
													<a href=\"#\"  title=\"Voir le calendrier de la chambre\" 
													onclick=\"window.open('module_chambres/planning_calendrier.php?operation=$operation&chambre=$chambre_id_defaut&annee=$annee','','toolbar=0,menubar=0,location=0,scrollbars=1,width=840,height=800')\">
													<img src='module_chambres/images/calendrier.png' border='0' align='center'/>
													</a>";?>	
												
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_007; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td align="left">
																<input type="text" name="date_debut" id="date_debut" size="10" maxlength="10" value="<?php echo $date_debut_defaut; ?>" readonly="">
															</td>
															<td>&nbsp;</td>
															<td><a href="javascript:;" onClick="effacer_date_debut();" title="<?php echo LANG_CHA_GENE_059; ?>"><img src="image/commun/b_drop.png" border="0" alt="<?php echo LANG_CHA_GENE_059; ?>"></a></td>
															<td>&nbsp;</td>
															<td align="left">
																<?php
																calendarDim("div_date_debut","document.formulaire.date_debut",$_SESSION["langue"], "0", "0", '', 'null', 'null');	
																?>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_008; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td align="left">
																<input type="text" name="date_fin" id="date_fin" size="10" maxlength="10" value="<?php echo $date_fin_defaut; ?>" readonly="">
															</td>
															<td>&nbsp;</td>
															<td><a href="javascript:;" onClick="effacer_date_fin();" title="<?php echo LANG_CHA_GENE_059; ?>"><img src="image/commun/b_drop.png" border="0" alt="<?php echo LANG_CHA_GENE_059; ?>"></a></td>
															<td>&nbsp;</td>
															<td align="left">
																<?php
																calendarDim("div_date_fin","document.formulaire.date_fin",$_SESSION["langue"], "0", "0", '', 'null', 'null');	
																?>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="right" valign="top"><?php echo LANG_CHA_RESA_015; ?></td>
												<td valign="top">&nbsp;:&nbsp;</td>
												<td align="left">
													<textarea id="commentaire" name="commentaire" cols="30" rows="5"><?php echo $commentaire_defaut; ?></textarea>
												</td>
											</tr>
										</table>
								</form>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top">
								<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
									
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
														<script language="javascript">buttonMagic3("<?php print LANG_CHA_RESA_011?>","verification_form_ajax()");</script>
													</td>
													<td align="center">
														<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_003?>","onclick_annuler()");</script>
													</td>
												</tr>
											</table>
										</td>
									</tr>
										
								</table>
									
					
					
								<?php //********** VALIDATION FORMULAIRES ********** ?>
						
								<script language="javascript">
										
									var message_erreur = '';
									var separateur = '';
									var valide = true;
									var mode_page = 'normal';
			

									//############### APPEL AJAX POUR VERIFIER LES INFOS DE LA RESERVATION ##########
									
									var ajax_verification_form_ajax;
			
									function verification_form_ajax() {
									
										var ajax_verification_form_ajax = new Ajax();
										
										msg_util_attente_montrer(true);
										
										var form = document.getElementById("formulaire");
										var eleve_id = form.eleve_id.value;
										var batiment_id = form.batiment_id.value;
										var chambre_id = form.chambre_id.value;
										var date_debut = form.date_debut.value;
										var date_fin = form.date_fin.value;
			
			
										// Parametres de l'Ajax
										ajax_verification_form_ajax.setParam ({
											url : "<?php echo url_module(); ?>ajax_reservation_edit_verif_form.php",
											returnFormat : "txt",
											method : "POST",
											data : {
												eleve_id : eleve_id,
												batiment_id : batiment_id,
												chambre_id : chambre_id,
												date_debut : date_debut,
												date_fin : date_fin,
												reservation_id_courante : 0
											},
											asynchronus : true,
											onComplete : "verification_form_ajax_reussite(response)",
											onFailure : "verification_form_ajax_echec(errorCode)"
										});
													
										// Appeler l'Ajax
										ajax_verification_form_ajax.execute();
			
									}
									
									
									function verification_form_ajax_reussite(response) {
										
										var donnees = new String(response);
									
										msg_util_attente_cacher();
										
										// Decoupage de la reponse (envoyee par le script Ajax)
										donnees_decoupees = donnees.split('¬');
										
										//alert(response);
										//alert(donnees_decoupees[3]);
										
										msg_util_attente_cacher();
			
										switch(donnees_decoupees[0]) {
											case '0': // Pas d'erreur
												//alert('pas derreur');
												if(confirm("<?php echo LANG_CHA_RESA_106; ?>")) {
													msg_util_attente_montrer(true);
													document.getElementById("formulaire").submit();
												}
												break;

											case '1': // Une erreur
												
												afficher_messages_errreur(donnees);
												
												break;
												
											case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
												alert("<?php echo LANG_CHA_AJAX_001; ?>");
												break;
												
											default: // Erreur inconuue
												// Remplacer la liste deroulante par la nouvelle
												alert("<?php echo LANG_CHA_AJAX_002; ?>");
										}
										
									}
									
									function verification_form_ajax_echec(errorCode) {
										msg_util_attente_cacher();
										alert("<?php echo LANG_CHA_AJAX_003; ?>");
									}
									//###################################################################
														

			
									//############### AFFICHAGE MESSAGE DE VALIDATION DU FORMULAIRE #############
									function afficher_messages_errreur(donnees) {
										var tmp = '';
										valide = true;
										message_erreur = '';
										separateur = '';
									
										donnees_decoupees = donnees.split('¬');
										
										//alert(donnees_decoupees);
										
										// Un eleve selectionne
										if(donnees_decoupees[1] == '1') {
											message_erreur += separateur + "     - <?php echo LANG_CHA_RESA_101; ?>";
											separateur = "\n";
											valide = false;
										}

										// Un batiment selectionne
										if(donnees_decoupees[2] == '1') {
											message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_009, LANG_CHA_RESA_005); ?>";
											separateur = "\n";
											// valide = false;
										}										

										// Une chambre selectionnee
										if(donnees_decoupees[3] == '1') {
											message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_009, LANG_CHA_RESA_006); ?>";
											separateur = "\n";
											// valide = false;
										}										

										// Une date de debut selectionee
										if(donnees_decoupees[4] == '1') {
											message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_RESA_007); ?>";
											separateur = "\n";
											valide = false;
										}		
																		
										// Une date de fin selectionee
										if(donnees_decoupees[5] == '1') {
											message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_RESA_008); ?>";
											separateur = "\n";
											valide = false;
										}										
																		
										// Date de fin >= date de debut
										if(donnees_decoupees[5] == '2') {
											message_erreur += separateur + "     - <?php echo LANG_CHA_RESA_102; ?>";
											separateur = "\n";
											valide = false;
										}										

										// Une reservation existe deja
										if(donnees_decoupees[6] == '1') {
											message_erreur += separateur + "     - <?php echo LANG_CHA_RESA_103; ?>";
											separateur = "\n";
											//tmp = "<?php echo LANG_CHA_RESA_105; ?>";
											//tmp = tmp.replace("#1", donnees_decoupees[7]);
											//tmp = tmp.replace("#2", donnees_decoupees[8]);
											//tmp = tmp.replace("#3", donnees_decoupees[9]);
											tmp = donnees_decoupees[11];
											message_erreur += separateur + tmp;
											separateur = "\n";
											valide = false;
										}										
										
										alert("<?php echo LANG_CHA_VALI_001; ?> : \n" + message_erreur);
										
									}
									//###################################################################
														
															
								</script>							
						
								<?php //********** GESTION NAVIGATION ********** ?>
								
								<script language="javascript">
									function onclick_annuler() {
										msg_util_attente_montrer(true);
										document.getElementById('formulaire_annuler').submit();
									}
								</script>
								<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>planning_liste.php" method="post">
								</form>

							</td>
						</tr>
					</table>
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
											"lien_avec" : '<?php echo site_url_racine(CHA_REP_MODULE); ?>#',
											"remplacer_par" : 'javascript:;'
										};
				// Traitements a effectuer sur toutes les pages
				initialisation_page_global(liens_a_remplacer);
				
				initialisation_recherche_eleve();
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
