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
	$batiment_id = $_SESSION[CHA_REP_MODULE]['reservation_liste']['batiment_id'];
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	if($operation == "enregistrer") {

	}
	//***************************************************************************

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
					<b><font id="menumodule1" ><?php echo LANG_CHA_CHAM_004; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<table border="0" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td>
								<form name="formulaire_criteres" id="formulaire_criteres" action="" method="post">
									<table border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><?php echo LANG_CHA_BATI_102; ?></td>
											<td>&nbsp;:&nbsp;</td>
											<td align="left">
												<select name="batiment_id" id="batiment_id" onChange="recuperer_liste()">
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
									</table>
								</form>
							</td>
						</tr>
						<tr>
							<td align="center">
								<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
			
									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
								
										<?php //********** AFFICHAGE DES DONNEES ********** ?>
										
										<tr>
											<td align="left" id=""><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="1" height="5"></td>
										</tr>
										<tr>
											<td align="left" id="">
												<?php echo LANG_CHA_GENE_058; ?> : <span id="conteneur_total_enregistrements">0</span>
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
										
										<tr>
											<td align="center">
												<table border="0" align="center" cellpadding="4" cellspacing="0">
													<tr>
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


						//##################### APPEL AJAX POUR RECUPERER LA LISTE #############
						var ajax_liste;

						function recuperer_liste() {
						
							var ajax_liste = new Ajax();
							
							msg_util_attente_montrer(true);
							document.getElementById("conteneur_donnees").innerHTML = '';
							document.getElementById("conteneur_total_enregistrements").innerHTML = 0;
						
							var form = document.getElementById("formulaire_criteres");
							var batiment_id = form.batiment_id.options[form.batiment_id.selectedIndex].value;
					
							// Parametres de l'Ajax
							ajax_liste.setParam ({
								url : "<?php echo url_module(); ?>ajax_chambre_liste.php",
								returnFormat : "txt",
								method : "POST",
								data : {
									batiment_id : batiment_id
								},
								asynchronus : true,
								onComplete : "recuperer_liste_reussite(response)",
								onFailure : "recuperer_liste_echec(errorCode)"
							});
										
							// Appeler l'Ajax
							ajax_liste.execute();

						}
						
						function recuperer_liste_reussite(response) {
							
							var donnees = new String(response);
						
							msg_util_attente_cacher();
							
							// Decoupage de la reponse (envoyee par le script Ajax)
							donnees_decoupees = donnees.split('¬');
							
							//alert(response);
							
							switch(donnees_decoupees[0]) {
								case '0': // Pas d'erreur
									//alert(donnees_decoupees[1]);
									document.getElementById("conteneur_total_enregistrements").innerHTML = donnees_decoupees[1];
									document.getElementById("conteneur_donnees").innerHTML = donnees_decoupees[2];
									break;
									
								case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
									alert("<?php echo LANG_CHA_AJAX_001; ?>");
									break;
									
								default: // Erreur inconuue
									// Remplacer la liste deroulante par la nouvelle
									alert("<?php echo LANG_CHA_AJAX_002; ?>");
							}
							
						}
						
						function recuperer_liste_echec(errorCode) {
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
									function onclick_modifier(id) {
										msg_util_attente_montrer(true);
										document.getElementById('id').value = id;
										document.getElementById('formulaire_modif').submit();
									}
								</script>
								<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>chambre_ajout.php" method="post">
								</form>
								<form name="formulaire_modif" id="formulaire_modif" action="<?php echo $g_chemin_relatif_module; ?>chambre_modif.php" method="post">
									<input type="hidden" name="id" id="id" value="0">
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
				
				recuperer_liste();
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