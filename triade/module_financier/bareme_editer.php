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
	$operation = lire_parametre('operation', '', 'GET');
	$mode = lire_parametre('mode', '', 'GET');
	$bareme_id = lire_parametre('bareme_id', 0, 'GET');
	$code_class = lire_parametre('code_class', 0, 'GET');
	$libelle = lire_parametre('libelle', '', 'GET');
	$annee_scolaire = lire_parametre('annee_scolaire', '', 'GET');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	$rafraichir_parent = '';
	
	switch($operation) {
		case "ajout":
			$sql= "INSERT INTO ".FIN_TAB_BAREME." (code_class, libelle, annee_scolaire) ";
			$sql.="VALUES(".$code_class.",'".esc($libelle)."','".esc($annee_scolaire)."'); ";
			$res_lock=execSql("LOCK TABLES ".FIN_TAB_BAREME." WRITE");
			$res=execSql($sql);
			$bareme_id = dernier_id($cnx->connection);
			$res_lock=execSql("UNLOCK TABLES ");
			//echo $sql;
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			break;
		case "modif":
			$sql= "UPDATE ".FIN_TAB_BAREME." ";
			$sql.="SET libelle = '" . esc($libelle) . "' ";
			$sql.=", annee_scolaire = '" . esc($annee_scolaire) . "' ";
			$sql.="WHERE bareme_id = $bareme_id ";
			$res=execSql($sql);
			//echo $sql;
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			break;
		case "supp":
			// Supprimer les frais
			$sql= "DELETE FROM ".FIN_TAB_FRAIS_BAREME." ";
			$sql.="WHERE bareme_id = $bareme_id ";
			$res=execSql($sql);		
			// Supprimer le bareme
			$sql= "DELETE FROM ".FIN_TAB_BAREME." ";
			$sql.="WHERE bareme_id = $bareme_id ";
			$res=execSql($sql);
			msg_util_ajout(LANG_FIN_GENE_001);
			$rafraichir_parent = 'oui';
			$bareme_id = 0;
			break;
	}
						
	if($operation == "enregistrer") {
		if($bareme_id > 0 && $code_class > 0) {
			msg_util_ajout(LANG_FIN_GENE_001);
		} else {
			msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
		}
	}
	//***************************************************************************
	
	
	// Rechercher le bareme
	$sql ="SELECT bareme_id, code_class, libelle, annee_scolaire ";
	$sql.="FROM ".FIN_TAB_BAREME." ";
	$sql.="WHERE bareme_id = $bareme_id";
	//echo $sql;
	$res=execSql($sql);
	

	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	if($mode == 'modif' || $mode == 'supp') {
		if($res->numRows() == 0) {
			msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
		}
	}
	//***************************************************************************

} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

// Titre de la page
switch($mode) {
	case "ajout":
		$titre = LANG_FIN_BARE_006;
		break;
	case "modif":
		$titre = LANG_FIN_BARE_007;
		break;
	case "supp":
		$titre = LANG_FIN_BARE_008;
		break;
	default:
		$titre = '';
}

?>
<html>
	<head>
		<META http-equiv="CacheControl" content = "no-cache">
		<META http-equiv="pragma" content = "no-cache">
		<META http-equiv="expires" content = -1>
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
		<title><?php echo $titre; ?></title>
	</head>
	
	<body id="bodyfond2" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
				
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		// Verification droits acces groupe
		validerequete("2");
		?>
		
		
		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>
		
		<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
			<tr>
				<td align="center">&nbsp;</td>
			</tr>
			<tr>
				<td align="center">
					<b><font class="T2"><?php echo $titre; ?></font></b>
				</td>
			</tr>
			<tr>
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" method="get" action="<?php echo url_script(); ?>" onSubmit="return valider_le_formulaire();">
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
			
							<?php
					
							// Verifier si on peut afficher
							if($mode == 'ajout' || (($mode == 'modif' || $mode == 'supp') && $res->numRows() > 0)) {
								if($mode == 'modif' || $mode == 'supp') {
									$ligne = & $res->fetchRow();
								}
							
							?>
							
							<input type="hidden" name="operation" id="operation" value="<?php echo $mode; ?>">
							<?php
								if($mode == 'ajout') {
									$valeur = 0;
								} else {
									$valeur = $ligne[0];
								}
							?>
							<input type="hidden" name="bareme_id" id="bareme_id" value="<?php echo $valeur; ?>">
							<?php
								if($mode == 'ajout') {
									$valeur = $code_class;
								} else {
									$valeur = $ligne[1];
								}
							?>
							<input type="hidden" name="code_class" id="code_class" value="<?php echo $valeur; ?>">
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
								
									<table cellspacing="1" cellpadding="3" border="0" width="100%" align="center">
										<tr>
											<td align="right"><?php echo LANG_FIN_GENE_016; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<?php
												switch($mode) {
													case "ajout":
														$valeur = '';
														break;
													case "modif":
														$valeur = $ligne[2];
														break;
													case "supp":
														$valeur = $ligne[2];
														$disabled = 'disabled';
														break;
													default:
														$disabled = '';
												}
												?>
												<input type="text" name="libelle" id="libelle" value="<?php echo $valeur; ?>" size="32" maxlength="64" <?php echo $disabled; ?>>
											</td>
										</tr>
										<tr>
											<td align="right"><?php echo LANG_FIN_GENE_011; ?>&nbsp;:&nbsp;</td>
											<td align="left">
												<?php
												switch($mode) {
													case "ajout":
														// Recuperer la liste des annees scolaires (a partir de maintenant)
														$annees_scolaires = liste_annee_scolaire();
														// Valeur courante du champ (=> premiere annee disponible)
														$valeur = $annees_scolaires[0];
														// Le champ reste actif
														$disabled = '';
														break;
													case "modif":
														// Recuperer la liste des annees scolaires (a partir de celle qui est dans l'enregistrement)
														$annees_scolaires = liste_annee_scolaire(date("Y") - 2, 3);
														// Valeur courante du champ
														$valeur = $ligne[3];
														// Le champs reste actif
														$disabled = '';
														break;
													case "supp":
														// Recuperer la liste des annees scolaires (a partir de celle qui est dans l'enregistrement)
														$annees_scolaires = liste_annee_scolaire(substr($ligne[3], 0, 4));
														// Valeur courante du champ
														$valeur = $ligne[3];
														// Le champs est desactive
														$disabled = 'disabled';
														break;
													default:
														// Le champ reste actif
														$disabled = '';
												}
												//print_r($annees_scolaires);
												?>
												<select name="annee_scolaire" id="annee_scolaire" <?php echo $disabled; ?>>
												<?php
												for($i=0;$i<count($annees_scolaires);$i++) {
													$selected = '';
													if($valeur == $annees_scolaires[$i]) {
														$selected = 'selected';
													}
												?>
													<option value="<?php echo $annees_scolaires[$i]; ?>" <?php echo $selected; ?>><?php echo $annees_scolaires[$i]; ?></option>
												<?php
												}
												?>
												</select>
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
											<td align="center">
											<?php
												switch($mode) {
													case "ajout":
											?>
												<script language="javascript">buttonMagicSubmit('<?php print LANG_FIN_GENE_014?>','create');</script>
											<?php
														break;
													case "modif":
											?>
												<script language="javascript">buttonMagicSubmit('<?php print LANG_FIN_GENE_005?>','create');</script>
											<?php
														break;
													case "supp":
											?>
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_015?>","onclick_supprimer()");</script>
												<script language="javascript">
													function onclick_supprimer() {
														if(confirm("<?php echo LANG_FIN_BARE_009; ?>")) {
															document.getElementById('formulaire').submit();
														}
													}
												</script>
											<?php
														break;
												}
											?>
											</td>
											<td align="center">
												<script language="javascript">buttonMagicFermeture();</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					</form>

					<?php
					// Rafraichir le parent et fermer ?
					if($rafraichir_parent == 'oui') {
					?>
						<script language="javascript">
							//alert('oui');
							msg_util_attente_montrer(false);
							window.opener.actualiser(<?php echo $bareme_id; ?>);
							window.close();
						</script>
					<?php
					}
					?>
					

					<?php //********** VALIDATION FORMULAIRES ********** ?>
					
					<script language="javascript">
							
						function valider_le_formulaire() {
							var valide = true;
							var obj;
							var message_erreur = '';
							var separateur = '';
							
							<?php
							if($mode == 'ajout' || $mode == 'modif') {
							?>
							
							// On verifie que le 'libelle' est numerique
							obj = document.getElementById('libelle');
							if(trim(obj.value) == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_GENE_016); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							}
							
							<?php
							}
							?>
			
			
							if(valide) {
								msg_util_attente_montrer(false);
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}
			
							return(valide);
						}

						<?php
						if($mode == 'ajout' || $mode == 'modif') {
						?>
						document.getElementById('libelle').focus();
						<?php
						}
						?>
			
					</script>		
							
				</td>
			</tr>		
		</table>
		

		<?php //********** TRAITEMENT A EFFECTUER APRES LE CHARGEMENT DE LA PAGE ********** ?>
		<script language="javascript" type="text/javascript">
		
			/*
		
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
			
			*/
					
		</script>

		
		
		<?php
		}
		?>

		<?php //********** INITIALISATION DES BULLES D'AIDE ********** ?>
		<script language="javascript">InitBulle("#000000","#FCE4BA","red",1);</script>


				<!-- BOUTONS -->
				<!--
				<tr>
					<td align="center">
						<table border="0" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td align="right">
									<script language="javascript">buttonMagicSubmit('<?php print LANGENR?>','create');</script>
								</td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td align="left">
									<script language="javascript">buttonMagicFermeture();</script>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				-->
		
	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>
