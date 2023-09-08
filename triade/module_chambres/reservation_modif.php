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
	$eleve_id = lire_parametre('eleve_id', 0, 'POST');
	$chambre_id = lire_parametre('chambre_id', 0, 'POST');
	$date_debut = lire_parametre('date_debut', '', 'POST');
	$date_fin = lire_parametre('date_fin', '', 'POST');
	$commentaire = lire_parametre('commentaire', '', 'POST');
	$id = lire_parametre('id', 0, 'POST');
	$retour_vers = lire_parametre('retour_vers', '', 'POST');
	//***************************************************************************

	

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	if($operation == "enregistrer") {
		$sql= "UPDATE ".CHA_TAB_RESERVATION." ";
		$sql.="SET ";
		$sql.=" elev_id=" . $eleve_id . ", ";
		$sql.=" chambre_id=" . $chambre_id . ", ";
		$sql.=" date_debut='"  . date_vers_bdd($date_debut) . "', ";
		$sql.=" date_fin='"  . date_vers_bdd($date_fin) . "', ";
		$sql.=" commentaire='"  . esc($commentaire) . "' ";
		$sql.=" WHERE reservation_id="  . $id . " ";
		$res_ope=execSql($sql);
		//echo $sql;

		// Verifier si la requete sql a reussi ou non
		if(is_object($res_ope) || (is_numeric($res_ope) && $res_ope > 0))
        {
			msg_util_ajout(LANG_CHA_GENE_001);
		} else {
			msg_util_ajout(LANG_CHA_GENE_057, 'erreur');
			// Oblige de refaire une connexion a la bdd car il y a un bug
			// Des qu'une requete plante, toutes les suivante (meme valides) sur la meme connexion vont echouer !
			$cnx=cnx();
			//echo $sql;
		}
	}
	//***************************************************************************


	//*************** RECHERCHER L'ENREGISTEMENT ****************
	// Rechercher la liste des batiments disponibles
	$sql ="SELECT reservation_id, elev_id, chambre_id, date_debut, date_fin, commentaire, date_reservation ";
	$sql.="FROM ".CHA_TAB_RESERVATION." ";
	$sql.="WHERE reservation_id = " . $id;
	//echo $sql;
	$res_reservation=execSql($sql);
	if($res_reservation->numRows() > 0) {
		$ligne = &$res_reservation->fetchRow();
		$reservation_id = $ligne[0];
		$eleve_id_defaut = $ligne[1];
		$batiment_id_defaut = 0;
		$chambre_id_defaut = $ligne[2];
		$date_debut_defaut = $ligne[3];
		if($date_debut_defaut != '') {
			$date_debut_defaut = date_depuis_bdd($date_debut_defaut);
		}
		$date_fin_defaut = $ligne[4];
		if($date_fin_defaut != '') {
			$date_fin_defaut = date_depuis_bdd($date_fin_defaut);
		}
		$commentaire_defaut = $ligne[5];
		$date_reservation_defaut = $ligne[6];
		
		// Rechercher le batiment de la chambre selectionee
		$sql ="SELECT batiment_id ";
		$sql.="FROM ".CHA_TAB_CHAMBRE." ";
		$sql.="WHERE chambre_id = " . $chambre_id_defaut;
		$res_chambre=execSql($sql);
		if($res_chambre->numRows() > 0) {
			$ligne = &$res_chambre->fetchRow();
			$batiment_id_defaut = $ligne[0];
		} else {
			$batiment_id_defaut = 0;
		}
	} else {
		$reservation_id = 0;
		$eleve_id_defaut = 0;
		$batiment_id_defaut = 0;
		$chambre_id_defaut = 0;
		$date_debut_defaut = '';
		$date_fin_defaut = '';
		$commentaire_defaut = '';
		$date_reservation_defaut = '';
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
					<b><font id="menumodule1" ><?php echo LANG_CHA_RESA_014; ?></font></b>
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
									<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
									<input type="hidden" name="retour_vers" id="retour_vers" value="<?php echo $retour_vers ?>">
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
																<input type="hidden" name="eleve_id" id="eleve_id" value="0">
																<input type="hidden" name="eleve_id_type" id="eleve_id_type" value="hidden">
																<?php echo LANG_CHA_RESA_010; ?>
															</td>
															<td align="left">
																<input type="hidden" name="prenom" id="prenom" size="15" maxlength="50" value="<?php echo $prenom_defaut; ?>">
															</td>
														</tr>
														<tr>
															<td align="left">
																<input type="hidden" name="nom" id="nom" size="15" maxlength="30" value="<?php echo $nom_defaut; ?>">
															</td>
														</tr>
													</table>
													
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_CHA_RESA_005; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_batiment_id">
													<select name="batiment_id" id="batiment_id" onChange="recuperer_liste_chambres()">
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
												<td align="right"><?php echo LANG_CHA_RESA_006; ?></td>
												<td>&nbsp;:&nbsp;</td>
												<td align="left" id="conteneur_chambre_id">
													<select name="chambre_id" id="chambre_id" onChange="">
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
												<td align="right" valign="top"><?php echo LANG_CHA_RESA_018; ?></td>
												<td valign="top">&nbsp;:&nbsp;</td>
												<td align="left">
													<?php echo date_depuis_bdd($date_reservation_defaut); ?>
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
														<script language="javascript">buttonMagic3("<?php print LANG_CHA_RESA_013?>","verification_form_ajax()");</script>
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
			
			
									function initialisation_recherche_eleve() {
										// Indiquer que on est en mode 'initialisation_recherche' => apres le chargement de la page
										mode_page = 'initialisation_recherche';
										// Lancer la recherche de l'eleve
										recherche_eleve();
									}
									function initialisation_recherche_batiment() {
										// Essayer de pre-selectionner le batiment
										var form = document.getElementById("formulaire");
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
										
										// Lancer la recherche des chambres
										recuperer_liste_chambres();
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
									
										var form = document.getElementById("formulaire");
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
												maj_variables_session : 0
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
										
										switch(donnees_decoupees[0]) {
											case '0': // Pas d'erreur
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
									//####################################################################################
			
			
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
									
										var form = document.getElementById("formulaire");
										var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
								
										// Parametres de l'Ajax
										ajax_recuperer_liste_chambres.setParam ({
											url : "<?php echo url_module(); ?>ajax_chambre_recherche.php",
											returnFormat : "txt",
											method : "POST",
											data : {
												mode_page : mode_page,
												chambre_id_defaut : <?php echo $chambre_id_defaut; ?>,
												batiment_id : batiment_id,
												maj_variables_session : 0
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
												mode_page = 'normal';
			
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
									//#################################################################################
			
			



									//############### APPEL AJAX POUR VERIFIER LES INFOS DE LA RESERVATION ##########
									
									var ajax_verification_form_ajax;
			
									function verification_form_ajax() {
									
										var ajax_verification_form_ajax = new Ajax();
										
										msg_util_attente_montrer(true);
										
										var form = document.getElementById("formulaire");
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
										var chambre_id = form.chambre_id.options[form.chambre_id.selectedIndex].value;
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
												reservation_id_courante : <?php echo $id; ?>
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
											
										// Debug
										//alert(donnees_decoupees[10]);
										
										msg_util_attente_cacher();

										switch(donnees_decoupees[0]) {
											case '0': // Pas d'erreur
												//alert('pas derreur');
												msg_util_attente_montrer(true);
												document.getElementById("formulaire").submit();
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
											valide = false;
										}										

										// Une chambre selectionnee
										if(donnees_decoupees[3] == '1') {
											message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_009, LANG_CHA_RESA_006); ?>";
											separateur = "\n";
											valide = false;
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
										if('<?php echo $retour_vers; ?>' != '') {
											document.getElementById('formulaire_annuler').action = '<?php echo $g_chemin_relatif_module; ?><?php echo $retour_vers; ?>';
										
										}
										document.getElementById('formulaire_annuler').submit();
									}
								</script>
								<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>reservation_liste.php" method="post">
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