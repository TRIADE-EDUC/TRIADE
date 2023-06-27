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
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	$elev_id = lire_parametre('elev_id_insc', '', 'POST');
	
	if($elev_id != '')
	{
		$sql ="SELECT elev_id, prenom, nom ";
		$sql.="FROM ".CHA_TAB_ELEVES." ";
		$sql.="WHERE elev_id = ". $elev_id . " ";
		$res=execSql($sql);
		if($res->numRows() > 0) 
		{
			$ligne = &$res->fetchRow();
			$nom_defaut = $ligne[2];
			$prenom_defaut = $ligne[1];
			
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['prenom'] = $prenom_defaut;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['nom'] = $nom_defaut;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'] = $elev_id;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'] = 0;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'] = 0;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'] = 0;
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['date_debut'] = '';
			$_SESSION[CHA_REP_MODULE]['reservation_liste']['date_fin'] = '';
		}
	}
	
	$eleve_id_defaut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['eleve_id'];
	$batiment_id_defaut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'];
	$etage_id_defaut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['etage_id'];
	$chambre_id_defaut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['chambre_id'];
	$date_debut_defaut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['date_debut'];
	$date_fin_defaut = $_SESSION[CHA_REP_MODULE]['reservation_liste']['date_fin'];
	$id = lire_parametre('id', 0, 'POST');
	
	$eleve_id_forcer = lire_parametre('eleve_id_forcer', -1);
	$batiment_id_forcer = lire_parametre('batiment_id_forcer', -1);
	$etage_id_forcer = lire_parametre('etage_id_forcer', -1);
	$chambre_id_forcer = lire_parametre('chambre_id_forcer', -1);
	$date_debut_forcer = lire_parametre('date_debut_forcer', '');
	$date_fin_forcer = lire_parametre('date_fin_forcer', '');
	if($eleve_id_forcer != -1) {
		$eleve_id_defaut = $eleve_id_forcer;
	}
	if($batiment_id_forcer != -1) {
		$batiment_id_defaut = $batiment_id_forcer;
	}
	if($etage_id_forcer != -1) {
		$etage_id_defaut = $etage_id_forcer;
	}
	if($chambre_id_forcer != -1) {
		$chambre_id_defaut = $chambre_id_forcer;
	}
	if($date_debut_forcer == 'null') {
		$date_debut_defaut = '';
	}
	if($date_fin_forcer == 'null') {
		$date_fin_defaut = '';
	}
	//echo $date_fin_forcer;
	//***************************************************************************
//print_r($_SESSION[CHA_REP_MODULE]);

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	if($operation == "supprimer") {
		$sql= "DELETE FROM ".CHA_TAB_RESERVATION." WHERE reservation_id = '".$id."'";
//echo $sql;
		$res_ope=execSql($sql);
		// Verifier si la requete sql a reussi ou non
		if(is_object($res_ope) || (is_numeric($res_ope) && $res_ope > 0)) {
			msg_util_ajout(LANG_CHA_GENE_007);
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
/*
	// Rechercher la liste des etages disponibles
	$sql ="SELECT etage_id ";
	$sql.="FROM ".CHA_TAB_ETAGE." ";
	$sql.="ORDER BY ordre";
	$res_etages=execSql($sql);

	// Rechercher la liste des chambres disponibles
	$sql ="SELECT chambre_id, numero, libelle ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." ";
	$sql.="ORDER BY libelle";
	$res_chambre=execSql($sql);
*/
	
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
		
		
		<?php
		$largeur_cadre = 900;
		$alignement_cadre = 'center';
		$date_heure_impression = '(' . date('d/m/Y H:i:s') . ')';
		$disabled_cadre = 'disabled';
		$bordure_tableau_impession = '1';
		if($mode_affichage != 'impression') {
			$largeur_cadre = 468;
			$alignement_cadre = '';
			$date_heure_impression = '';
			$disabled_cadre = '';
			$bordure_tableau_impession = '0';
		?>
		
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
		<?php include("./librairie_php/lib_defilement.php"); ?>
		</td>
		<td width="472" valign="middle" rowspan="3" align="center">
			<div align='center'>
				<?php top_h(); ?>
				<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>

		<?php
		}
		?>
				
		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		
		<!-- TITRE ET CADRE CENTRAL -->
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" >
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_CHA_RESA_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
						<tr>
							<td><br><br>
								<?php
								// Pour la gestion des calendriers
								include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");

								//*******************  CRITERES DE RECHERCHE *********************
								
								?>
								<form name="formulaire_criteres" id="formulaire_criteres" action="" method="post">
									<fieldset id="fieldset_criteres" style="z-index:5; margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_CHA_GENE_026; ?></legend>
										<table border="0" cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td align="right" valign="top"><?php echo LANG_CHA_RESA_009; ?></td>
												<td valign="top">&nbsp;:&nbsp;</td>
												<td align="left">
													<table border="0" cellpadding="0" cellspacing="0" align="left">
														<tr>
															<td align="left" valign="top" id="conteneur_eleve_id" nowrap="nowrap">
																<input type="hidden" name="eleve_id" id="eleve_id" value="0">
																<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="hidden">
																<?php echo LANG_CHA_RESA_010; ?>
															</td>
															<td align="left">
																<input type="hidden" name="nom" id="nom" size="15" maxlength="30" value="<?php echo $nom_defaut; ?>">
																<input type="hidden" name="prenom" id="prenom" size="15" maxlength="50" value="<?php echo $prenom_defaut; ?>">
															</td>
														</tr>
													</table>
													
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_005; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_batiment_id">
													<select name="batiment_id" id="batiment_id" onChange="recuperer_liste_etages()">
														<?php
															$selected = '';
															if($une_option[0] == $batiment_id) {
																$selected = 'selected';
															}
														?>
														<option value="0" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_GENE_025); ?></option>
													<?php
														for($i=0; $i<$res_batiments->numRows(); $i++) {
															$une_option = &$res_batiments->fetchRow();
															$selected = '';
															if($une_option[0] == $batiment_id) {
																$selected = 'selected';
															}
															
															$classe = '';
															
															$texte = $une_option[1] . ' - ' . $une_option[5] . ' - ' . $une_option[6];
		
													?>
														<option value="<?php echo $une_option[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo $texte; ?></option>
													<?php
														}
													?>
													</select>
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_ETAG_001; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_etage_id">
													<select name="etage_id" id="etage_id"  <?php echo $disabled_cadre; ?> onChange="recuperer_liste_chambres()">
														<?php
															$selected = '';
															if($une_option[0] == $etage_id) {
																$selected = 'selected';
															}
														?>
														<option value="0" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_GENE_025); ?></option>
													</select>
												</td>
											</tr>											
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_006; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_chambre_id">
													<select name="chambre_id" id="chambre_id" onChange="" <?php echo $disabled_cadre;?>>
														<?php
															$selected = '';
															if($une_option[0] == $chambre_id) {
																$selected = 'selected';
															}
														?>
														<option value="0" <?php echo $selected; ?>  class=""><?php echo ucfirst(LANG_CHA_GENE_060); ?></option>
													</select>
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
																calendarDim("div_date_debut","document.formulaire_criteres.date_debut",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
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
																calendarDim("div_date_fin","document.formulaire_criteres.date_fin",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
																?>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="center" colspan="3">&nbsp;</td>
											</tr>
											<tr>
												<?php 
												if($mode_affichage != 'impression') {?>
													<td align="center" colspan="3">
														<table border="0" cellpadding="0" cellspacing="0" align="center">
															<tr>
																<td align="left" valign="top">
																	<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_020?>","recuperer_liste_reservations()");</script>
																</td>
																<td align="center" valign="top">
																	<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_003?>","onclick_annuler()");</script>
																</td>
															</tr>
														</table>
													</td>
												<?php 
												} ?>
												
											</tr>
										</table>
									</fieldset>
								</form>
							</td>
						</tr>
						<tr>
							<td align="center">
								<fieldset id="fieldset_enregistrements" style="z-index:4; margin-left:15px; margin-right:15px;">
									<legend><?php echo LANG_CHA_RESA_001; ?></legend>
									<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
				
										<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
									
											<?php //********** AFFICHAGE DES DONNEES ********** ?>
											
											<tr>
												<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
											</tr>
											<tr>
												<td align="left" id="">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td align="left" valign="top" nowrap="nowrap">
																<?php echo LANG_CHA_GENE_058; ?> : <span id="conteneur_total_enregistrements">0</span>
															</td>
															<?php if($mode_affichage != 'impression') {?>
															<td align="right" valign="top">
																<table border="0" cellpadding="0" cellspacing="0" align="right">
																	<tr>
																		<td align="right" valign="top">
																			<script language="javascript">buttonMagic3("<?php echo LANG_CHA_RESA_002?>","onclick_ajouter()");</script>
																		</td>
																	</tr>
																</table>
															</td>
															<?php } ?>
														</tr>
													</table>
												
												
												</td>
											</tr>
											<tr>
												<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
											</tr>
											<tr>
												<td align="center" id="conteneur_donnees">
												
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
								
												
										</table>
										
									</form>
								</fieldset>
									
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
					<script language="javascript">
							
						var message_erreur = '';
						var separateur = '';
						var valide = true;
						var mode_page = 'normal';


						function initialisation_recherche_eleve() {
							// Indiquer que on est en mode 'initialisation_recherche' => apres le chargement de la page
							mode_page = 'initialisation_recherche';
							// Lancer la recherche de l'eleve
							recherche_eleve();
						}
						function initialisation_recherche_batiment() {
							// Essayer de pre-selectionner le batiment
							var form = document.getElementById("formulaire_criteres");
							var batiment_id = form.batiment_id;
							var trouve = false;
							var i;
							for(i=0;i<batiment_id.options.length;i++) {
								if(batiment_id.options[i].value == '<?php echo $batiment_id_defaut; ?>') {
									trouve = true;
									batiment_id.selectedIndex = i;
								}
							}
							// Si on a pas trouve le batiment, on selectionne le premier
							if(!trouve) {
								batiment_id.selectedIndex = 0;
							}
							
							// Lancer la recherche des etages
							recuperer_liste_etages();
						}

						function effacer_eleve() {
							init_conteneur_eleve_id('aucun', '');
						}
						
						function effacer_date_debut() {
							document.getElementById("date_debut").value = '';
						}
						
						function effacer_date_fin() {
							document.getElementById("date_fin").value = '';
						}


						//##################### APPEL AJAX POUR RECHERCHER L'ELEVE #############
						
						function init_conteneur_eleve_id(mode, donnees) {
							switch(mode) {
								case 'attente_ajax':
									document.getElementById("conteneur_eleve_id").innerHTML = '<img src="image/temps1.gif" border=0>';
									document.getElementById("conteneur_eleve_id").innerHTML += '<input type="hidden" name="eleve_id" id="eleve_id" value="0">';
									break;
								case 'donnees':
									document.getElementById("conteneur_eleve_id").innerHTML = donnees;
									break;
								case 'aucun':
									document.getElementById("conteneur_eleve_id").innerHTML = "<?php echo LANG_CHA_RESA_010; ?>";
									document.getElementById("conteneur_eleve_id").innerHTML += '<input type="hidden" name="eleve_id" id="eleve_id" value="0">';
									document.getElementById("conteneur_eleve_id").innerHTML += '<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="hidden">';
								
									break;
								default:
									document.getElementById("conteneur_eleve_id").innerHTML = '&nbsp;';
									break;
							}
						}
						
						var ajax_recherche_eleve;

						function recherche_eleve() {
						
							var ajax_recherche_eleve = new Ajax();
							
							//msg_util_attente_montrer(true);
							init_conteneur_eleve_id('attente_ajax', '');
						
							var form = document.getElementById("formulaire_criteres");
							var prenom = form.prenom.value;
							var nom = form.nom.value;
					
							// Parametres de l'Ajax
							ajax_recherche_eleve.setParam ({
								url : "<?php echo url_module(); ?>ajax_eleve_recherche.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									mode_page : mode_page,
									eleve_id_defaut : <?php echo $eleve_id_defaut; ?>,
									prenom : prenom,
									nom : nom,
									maj_variables_session : 1
								},
								asynchronus : true,
								onComplete : "recherche_eleve_reussite(response)",
								onFailure : "recherche_eleve_echec(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_recherche_eleve.execute();

						}
						
						function recherche_eleve_reussite(response) {
							var total_enregistrements = 0;
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							//alert(response);
							//alert(donnees_decoupees[2]);

							
							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
								
									// Debug
									//alert(donnees_decoupees[3]);
									
									init_conteneur_eleve_id('donnees', donnees_decoupees[2]);
									if(mode_page == 'initialisation_recherche') {
										initialisation_recherche_batiment()
									}
									
									break;
									
								case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
									alert("<?php echo LANG_CHA_AJAX_001; ?>");
									break;
									
								default: // Erreur inconuue
									// Remplacer la liste deroulante par la nouvelle
									alert("<?php echo LANG_CHA_AJAX_002; ?>");
							}
							
						}
						
						function recherche_eleve_echec(errorCode) {
							msg_util_attente_cacher();
							alert("<?php echo LANG_CHA_AJAX_003; ?>");
						}
						//##################################################################################################


						//##################### APPEL AJAX POUR RECUPERER LES ETAGES #############

						function init_conteneur_etage_id(mode, donnees) {
							switch(mode) {
								case 'attente_ajax':
									document.getElementById("conteneur_etage_id").innerHTML = '<img src="image/temps1.gif" border=0>';
									break;
								case 'donnees':
									document.getElementById("conteneur_etage_id").innerHTML = donnees;
									break
								default:;
									document.getElementById("conteneur_etage_id").innerHTML = '&nbsp;';
									break;
							}
						}

						var ajax_recuperer_liste_etages;

						function recuperer_liste_etages() {
						
							var ajax_recuperer_liste_etages = new Ajax();
							
							init_conteneur_etage_id('attente_ajax', '');
						
							var form = document.getElementById("formulaire_criteres");
							var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
					
							// Parametres de l'Ajax
							ajax_recuperer_liste_etages.setParam ({
								url : "<?php echo url_module(); ?>ajax_etage_recherche.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									mode_page : mode_page,
									etage_id_defaut : <?php echo $etage_id_defaut; ?>,
									batiment_id : batiment_id,
									maj_variables_session : 1
								},
								asynchronus : true,
								onComplete : "recuperer_liste_etages_reussite(response)",
								onFailure : "recuperer_liste_etages_echec(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_recuperer_liste_etages.execute();

						}
						
						function recuperer_liste_etages_reussite(response) {
							
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							//alert(response);
							
							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
									init_conteneur_etage_id('donnees', donnees_decoupees[2]);
									/*
									if(mode_page != 'normal') {
										mode_page = 'normal';
										recuperer_liste_chambres();
									}
									*/
									recuperer_liste_chambres();
									break;
									
								case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
									alert("<?php echo LANG_CHA_AJAX_001; ?>");
									break;
									
								default: // Erreur inconuue
									// Remplacer la liste deroulante par la nouvelle
									alert("<?php echo LANG_CHA_AJAX_002; ?>");
							}
							
						}
						
						function recuperer_liste_etages_echec(errorCode) {
							msg_util_attente_cacher();
							alert("<?php echo LANG_CHA_AJAX_003; ?>");
						}
						//##################################################################################################


						//##################### APPEL AJAX POUR RECUPERER LES CHAMBRES #############

						function init_conteneur_chambre_id(mode, donnees) {
							switch(mode) {
								case 'attente_ajax':
									document.getElementById("conteneur_chambre_id").innerHTML = '<img src="image/temps1.gif" border=0>';
									break;
								case 'donnees':
									document.getElementById("conteneur_chambre_id").innerHTML = donnees;
									break
								default:;
									document.getElementById("conteneur_chambre_id").innerHTML = '&nbsp;';
									break;
							}
						}

						var ajax_recuperer_liste_chambres;

						function recuperer_liste_chambres() {
						
							var ajax_recuperer_liste_chambres = new Ajax();
							
							init_conteneur_chambre_id('attente_ajax', '');
						
							var form = document.getElementById("formulaire_criteres");
							var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
							var etage_id = form.etage_id.options[form.etage_id.selectedIndex].value;
					
							// Parametres de l'Ajax
							ajax_recuperer_liste_chambres.setParam ({
								url : "<?php echo url_module(); ?>ajax_chambre_recherche.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									mode_page : mode_page,
									chambre_id_defaut : <?php echo $chambre_id_defaut; ?>,
									batiment_id : batiment_id,
									etage_id : etage_id,
									maj_variables_session : 1
								},
								asynchronus : true,
								onComplete : "recuperer_liste_chambres_reussite(response)",
								onFailure : "recuperer_liste_chambres_echec(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_recuperer_liste_chambres.execute();

						}
						
						function recuperer_liste_chambres_reussite(response) {
							
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							//alert(response);
							
							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
									init_conteneur_chambre_id('donnees', donnees_decoupees[2]);
									if(mode_page != 'normal') {
										mode_page = 'normal';
										recuperer_liste_reservations();
									}

									break;
									
								case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
									alert("<?php echo LANG_CHA_AJAX_001; ?>");
									break;
									
								default: // Erreur inconuue
									// Remplacer la liste deroulante par la nouvelle
									alert("<?php echo LANG_CHA_AJAX_002; ?>");
							}
							
						}
						
						function recuperer_liste_chambres_echec(errorCode) {
							msg_util_attente_cacher();
							alert("<?php echo LANG_CHA_AJAX_003; ?>");
						}
						//##################################################################################################


						//##################### APPEL AJAX POUR RECUPERER LA LISTE DES RESERVATIONS #############
						function init_conteneur_total_enregistrements(mode, donnees) {
							switch(mode) {
								case 'donnees':
									document.getElementById("conteneur_total_enregistrements").innerHTML = donnees;
									break;
								default:
									document.getElementById("conteneur_total_enregistrements").innerHTML = '&nbsp;';
									break;
							}
						}
						
						function init_conteneur_donnees(mode, donnees) {
							switch(mode) {
								case 'attente_ajax':
									document.getElementById("conteneur_donnees").innerHTML = '<img src="image/temps1.gif" border=0>';
									break;
								case 'donnees':
									document.getElementById("conteneur_donnees").innerHTML = donnees;
									break;
								default:
									document.getElementById("conteneur_donnees").innerHTML = '&nbsp;';
									break;
							}
						}
						
						var ajax_recuperer_liste_reservations;

						function recuperer_liste_reservations() {
						
							var ajax_recuperer_liste_reservations = new Ajax();
							//alert('tt');
							//msg_util_attente_montrer(true);
							init_conteneur_donnees('attente_ajax', '');
							init_conteneur_total_enregistrements('donnees', '0');
							
							var form = document.getElementById("formulaire_criteres");
							var eleve_id_type = form.eleve_id_type.value;
							var eleve_id = 0;
							switch(eleve_id_type) {
								case 'hidden':
									eleve_id = form.eleve_id.value;
									break;
								case 'select':
									eleve_id = form.eleve_id.options[ form.eleve_id.selectedIndex].value;
									break;
							}
							var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
							var etage_id = form.etage_id.options[form.etage_id.selectedIndex].value;
							var chambre_id = form.chambre_id.options[form.chambre_id.selectedIndex].value;
							var date_debut = form.date_debut.value;
							var date_fin = form.date_fin.value;


							// Parametres de l'Ajax
							ajax_recuperer_liste_reservations.setParam ({
								url : "<?php echo url_module(); ?>ajax_reservation_recherche.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									eleve_id : eleve_id,
									batiment_id : batiment_id,
									etage_id : etage_id,
									chambre_id : chambre_id,
									date_debut : date_debut,
									date_fin : date_fin,
									maj_variables_session : 1
								},
								asynchronus : true,
								onComplete : "recuperer_liste_reservations_reussite(response)",
								onFailure : "recuperer_liste_reservations_echec(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_recuperer_liste_reservations.execute();

						}
						
						
						function recuperer_liste_reservations_reussite(response) {
							
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							//alert(response);
							//alert(donnees_decoupees[3]);

							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
									//alert(donnees_decoupees[1]);
									init_conteneur_total_enregistrements('donnees', donnees_decoupees[1]);
									init_conteneur_donnees('donnees', donnees_decoupees[2]);
									break;
									
								case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
									alert("<?php echo LANG_CHA_AJAX_001; ?>");
									break;
									
								default: // Erreur inconuue
									// Remplacer la liste deroulante par la nouvelle
									alert("<?php echo LANG_CHA_AJAX_002; ?>");
							}
							
						}
						
						function recuperer_liste_reservations_echec(errorCode) {
							msg_util_attente_cacher();
							alert("<?php echo LANG_CHA_AJAX_003; ?>");
						}
						//##################################################################################################

			
						function onclick_details(id) {
							var commentaire = document.getElementById(id + '_commentaire');
							if(commentaire.style.display == '') {
								commentaire.style.display = 'none';
							} else {
								commentaire.style.display = '';
							}
						}

			
					</script>							
						
								<?php //********** GESTION NAVIGATION ********** ?>
								
								<script language="javascript">
								var fenetre = null;
							var liste_fenetre = new Array();
								
									function onclick_ajouter() {
										msg_util_attente_montrer(true);
										document.getElementById('formulaire_ajout').submit();
									}
									function onclick_annuler() {
										msg_util_attente_montrer(true);
										document.getElementById('formulaire_annuler').submit();
									}
									function onclick_modifier(id) {
										msg_util_attente_montrer(true);
										document.formulaire_modif.id.value = id;
										document.getElementById('formulaire_modif').submit();
									}
									function onclick_supprimer(id) {
										if(confirm("<?php echo LANG_CHA_GENE_008; ?>")) {
											msg_util_attente_montrer(true);
											document.formulaire_suppr.id.value = id;
											document.getElementById('formulaire_suppr').submit();
										}
									}
									function onclick_planning() {
										msg_util_attente_montrer(true);
										document.getElementById('formulaire_planning').submit();
									}
									function onclick_parametrage() {
										msg_util_attente_montrer(true);
										document.getElementById('formulaire_parametrage').submit();
									}
									function onclic_export_excel() {
									document.getElementById('formulaire_export_excel').submit();
									}
									function onclic_imprimer() {							
									try {
										for(i=0; i<liste_fenetre.length; i++) {
											liste_fenetre[i].close();
										}
										
									}
									catch(e) {
									}	
									liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(CHA_REP_MODULE); ?>module_chambres/reservation_liste_suite.php?mode_affichage=impression&operation=rechercher','fenetre_editer_' + liste_fenetre.length,'width=1000,height=600,resizable=yes,scrollbars=yes');
									
									liste_fenetre[liste_fenetre.length].focus();						
									}
									function onclick_fermer() {
									window.close();
									}
									
									function onclick_ouvrir_impession() {
										window.print();
									}
									
								</script>
								<form name="formulaire_ajout" id="formulaire_ajout" action="<?php echo $g_chemin_relatif_module; ?>reservation_ajout.php" method="post">
								</form>
								<form name="formulaire_modif" id="formulaire_modif" action="<?php echo $g_chemin_relatif_module; ?>reservation_modif.php" method="post">
									<input type="hidden" name="id" id="id" value="0">
								</form>
								<form name="formulaire_suppr" id="formulaire_suppr" action="<?php echo $g_chemin_relatif_module; ?>reservation_liste.php" method="post">
									<input type="hidden" name="operation" id="operation" value="supprimer">
									<input type="hidden" name="id" id="id" value="0">
								</form>
								<form name="formulaire_planning" id="formulaire_planning" action="<?php echo $g_chemin_relatif_module; ?>planning_liste.php" method="post">
								</form>
								<form name="formulaire_parametrage" id="formulaire_parametrage" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
								</form>
								<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>reservation_liste.php" method="post">
								</form>
								<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>reservation_liste_suite_excel.php" method="post" target="">
								</form>
							</td>
						</tr>
						<tr>
							<td>
								<hr>
							</td>
						</tr>
							<tr>
								<td align="center">
									<table border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td align="center" colspan="2"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
										</tr>
										<?php
										if($mode_affichage != 'impression') {
										?>	
										<tr>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_070?>","onclic_imprimer()");</script>
											</td>
											<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="30" height="1"></td>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_069?>","onclic_export_excel()");</script>
											</td>
										</tr>
										<?php
										} else {
										?>
										<tr>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_070?>","onclick_ouvrir_impession()");</script>
											</td>
											<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="30" height="1"></td>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_GENE_003?>","onclick_fermer()");</script>
											</td>
										</tr>
										<?php
										}
										?>		
										<tr>
											<td align="center" colspan="2"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
										</tr>
									</table>
								</td>
							</tr>

					</table>
				</td>
			</tr>
		</table>

		<?php
		if($mode_affichage != 'impression') {
		?>
		
		<?php //********** GENERATION DES MENUS ADMINISTRATEUR ********** ?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."22.js'>" ?></script>
		

		<?php //********** INITIALISATION DES BULLES D'AIDE ********** ?>
		<script language="javascript">InitBulle("#000000","#FCE4BA","red",1);</script>
		<?php
			}
		?>

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
