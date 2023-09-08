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
	$batiment_id = lire_parametre('batiment_id', 0, 'POST');
	$numero = lire_parametre('numero', '', 'POST');
	$type_chambre_id = lire_parametre('type_chambre_id', 1, 'POST');
	$etage_id = lire_parametre('etage_id', 1, 'POST');
	$libelle = lire_parametre('libelle', '', 'POST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	if($operation == "enregistrer") {
		if($id > 0) {
			$sql= "UPDATE ".CHA_TAB_CHAMBRE." ";
			$sql.="SET batiment_id = " . $batiment_id . " ";
			$sql.=", numero = '" . esc($numero) . "' ";
			$sql.=", libelle = '" . esc($libelle) . "' ";
			$sql.=", type_chambre_id = " . $type_chambre_id . " ";
			$sql.=", etage_id = " . $etage_id . " ";
			$sql.="WHERE chambre_id = $id ";
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
	
	// Rechercher l'enregistrement
	$sql ="SELECT chambre_id, batiment_id, numero, libelle, type_chambre_id, etage_id ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." ";
	$sql.="WHERE chambre_id = $id";
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

	// Rechercher la liste des types de chambre disponibles
	$sql ="SELECT type_chambre_id ";
	$sql.="FROM ".CHA_TAB_TYPE_CHAMBRE." ";
	$sql.="ORDER BY ordre";
	$res_types_chambre=execSql($sql);

	// Rechercher la liste des etages disponibles
	$sql ="SELECT etage_id ";
	$sql.="FROM ".CHA_TAB_ETAGE." ";
	$sql.="ORDER BY ordre";
	$res_etages=execSql($sql);

	// Rechercher la liste des batiments disponibles
	$sql ="SELECT batiment_id, libelle, adresse_1, adresse_2, adresse_3, code_postal, ville ";
	$sql.="FROM ".CHA_TAB_BATIMENT." ";
	$sql.="ORDER BY libelle";
	$res_batiments=execSql($sql);
	
	
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
					<b><font id="menumodule1" ><?php echo LANG_CHA_CHAM_009; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

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
											<td valign="top" align="right"><?php echo LANG_CHA_CHAM_012; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<select name="batiment_id" id="batiment_id" <?php echo $disabled; ?>>
												<?php
													for($i=0; $i<$res_batiments->numRows(); $i++) {
														$une_option = &$res_batiments->fetchRow();
														$selected = '';
														if($une_option[0] == $ligne[1]) {
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
											<td valign="top" align="right"><?php echo LANG_CHA_ETAG_001; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<select name="etage_id" id="etage_id" <?php echo $disabled; ?>>
												<?php
													for($i=0; $i<$res_etages->numRows(); $i++) {
														$une_option = &$res_etages->fetchRow();
														$selected = '';
														if($une_option[0] == $ligne[5]) {
															$selected = 'selected';
														}
														
														$classe = '';
														
														eval('$texte = LANG_CHA_ETAG_ID_' . $une_option[0] .'_LIBELLE;');
														eval('$exposant = LANG_CHA_ETAG_ID_' . $une_option[0] .'_EXPOSANT;');
														if(trim($exposant) != '') {
															$texte .= ' ' . $exposant . '';
														}

												?>
													<option value="<?php echo $une_option[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo $texte; ?></option>
												<?php
													}
												?>
												</select>
											</td>
										</tr>


										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_CHAM_014; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="numero" id="numero" size="5" maxlength="5" value="<?php echo $ligne[2]; ?>" <?php echo $disabled; ?>>
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_TCHA_001; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<select name="type_chambre_id" id="type_chambre_id" <?php echo $disabled; ?>>
												<?php
													for($i=0; $i<$res_types_chambre->numRows(); $i++) {
														$une_option = &$res_types_chambre->fetchRow();
														$selected = '';
														if($une_option[0] == $ligne[4]) {
															$selected = 'selected';
														}
														
														$classe = '';
														
														eval('$texte = LANG_CHA_TCHA_ID_' . $une_option[0] .';');

												?>
													<option value="<?php echo $une_option[0]; ?>" <?php echo $selected; ?>  class="<?php echo $classe; ?>"><?php echo $texte; ?></option>
												<?php
													}
												?>
												</select>
											</td>
										</tr>
										<tr>
											<td valign="top" align="right"><?php echo LANG_CHA_CHAM_013; ?> :</td>
											<td valign="top">&nbsp;</td>
											<td valign="top" align="left">
												<input type="text" name="libelle" id="libelle" size="30" maxlength="64" value="<?php echo $ligne[3]; ?>" <?php echo $disabled; ?>>
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
											if($res->numRows() > 0 && $res_batiments->numRows() > 0) {
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_CHA_CHAM_006?>","valider_le_formulaire()");</script>
												
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
							
						var message_erreur = '';
						var separateur = '';
						var valide = true;

						function valider_le_formulaire() {
							var obj;
							
							valide = true;
							message_erreur = '';
							separateur = '';
							
							obj = document.getElementById('numero');
							obj.value = trim(obj.value);
							if(obj.value == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_004, LANG_CHA_CHAM_014); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							} else {
								if(!valider_chaine(obj.value, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_CHA_VALI_003, LANG_CHA_CHAM_014); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
							}

														
							if(valide) {
								// Verifier par Ajax que le numero de chambre n'est pas deja utilise (plutot que recharger la page)					
								verification_form_ajax();
							} else {
								alert("<?php echo LANG_CHA_VALI_001; ?> : \n" + message_erreur);
							}
			
							return(valide);
						}


						//##################### APPEL AJAX POUR VALIDER LES ELEMENTS ADDITIONELS DU FORMULAIRE #############
						var ajax_verification_form_ajax;

						function verification_form_ajax() {
						
							var ajax_verification_form_ajax = new Ajax();
							
							msg_util_attente_montrer(false);
						
							var form = document.getElementById("formulaire");
							var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
							var numero = form.numero.value;
					
							// Parametres de l'Ajax
							ajax_verification_form_ajax.setParam ({
								url : "<?php echo url_module(); ?>ajax_chambre_edit_verif_form.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									chambre_id : <?php echo $id; ?>,
									batiment_id : batiment_id,
									numero : numero
								},
								asynchronus : true,
								onComplete : "verification_form_ajax_reussite(response)",
								onFailure : "verification_form_ajax_eche(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_verification_form_ajax.execute();

						}
						
						function verification_form_ajax_reussite(response) {
							
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
									//alert(donnees_decoupees[1]);
									
									message_erreur = '';
									separateur = '';
									valide = true;
					
									// Verifier si le numero saisi existe deja pour ce batimetn
									if(donnees_decoupees[1] == '1') {
										message_erreur += separateur + "     - <?php echo LANG_CHA_CHAM_103; ?>";
										separateur = "\n";
										valide = false;
									}
									
									if(valide) {
										// Pas d'erreur => envoyer le formulaire
										msg_util_attente_montrer(true);
										document.getElementById("formulaire").submit();
									} else {
										// Au moins une erreur => afficher les messages
										alert("<?php echo LANG_CHA_VALI_001; ?> : \n" + message_erreur);
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
						
						function verification_form_ajax_eche(errorCode) {
							msg_util_attente_cacher();
							alert("<?php echo LANG_CHA_AJAX_003; ?>");
						}
						//##################################################################################################

			
					</script>	
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						function onclick_annuler() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_annuler').submit();
						}
						document.getElementById('libelle').focus();
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>chambre_liste.php" method="post">
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