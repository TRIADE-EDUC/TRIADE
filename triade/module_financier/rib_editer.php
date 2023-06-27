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
	$rib_id = lire_parametre('rib_id', 0, 'GET');
	$elev_id = lire_parametre('elev_id', 0, 'GET');
	$numero_rib = lire_parametre('numero_rib', 1, 'GET');
	$libelle = stripslashes(lire_parametre('libelle', '', 'GET'));
	$code_banque = lire_parametre('code_banque', '', 'GET');
	$code_guichet = lire_parametre('code_guichet', '', 'GET');
	$numero_compte = strtoupper(lire_parametre('numero_compte', '', 'GET'));
	$cle_rib = lire_parametre('cle_rib', '', 'GET');
	$titulaire = stripslashes(lire_parametre('titulaire', '', 'GET'));
	$banque = lire_parametre('banque', '', 'GET');
	$iban = lire_parametre('iban', '', 'GET');
	$bic = lire_parametre('bic', '', 'GET');
	$swift = lire_parametre('swift', '', 'GET');
	$actualiser_parent = lire_parametre('actualiser_parent', 0, 'GET');
	$mode = lire_parametre('mode', '', 'GET');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ****************
	$operation_effectuee = '';
	if($operation == "enregistrer") {
		switch($mode) {
			case "modif":
				if($elev_id > 0 && $rib_id > 0) {
					$sql= "UPDATE ".FIN_TAB_RIB." ";
					$sql.="SET libelle = '" . esc($libelle) . "', ";
					$sql.="    code_banque = '" . esc($code_banque) . "', ";
					$sql.="    code_guichet = '" . esc($code_guichet) . "', ";
					$sql.="    numero_compte = '" . esc($numero_compte) . "', ";
					$sql.="    cle_rib = '" . esc($cle_rib) . "', ";
					$sql.="    titulaire = '" . esc($titulaire) . "', ";
					$sql.="    banque = '" . esc($banque) . "', ";
					$sql.="    iban = '" . esc($iban) . "', ";
					$sql.="    bic = '" . esc($bic) . "', ";
					$sql.="    swift = '" . esc($swift) . "' ";
					$sql.="WHERE rib_id = $rib_id ";
					$sql.="AND elev_id = $elev_id ";
					$sql.="AND numero_rib = $numero_rib ";
					$res=execSql($sql);
					msg_util_ajout(LANG_FIN_GENE_001);
					$operation_effectuee = 'enregistrement_termine';
				} else {
					msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
				}
				break;
		
			case "ajout":
				// Ajouter un enregistrement
				$sql= "INSERT INTO ".FIN_TAB_RIB."(elev_id, numero_rib, libelle, code_banque, code_guichet, numero_compte, cle_rib, titulaire, banque, iban, bic, swift) ";
				$sql.="VALUES(";
				$sql.="$elev_id";
				$sql.=",  " . $numero_rib . "";
				$sql.=", '" . esc($libelle) . "'";
				$sql.=", '" . esc($code_banque) . "'";
				$sql.=", '" . esc($code_guichet) . "'";
				$sql.=", '" . esc($numero_compte) . "'";
				$sql.=", '" . esc($cle_rib) . "'";
				$sql.=", '" . esc($titulaire) . "'";
				$sql.=", '" . esc($banque) . "'";
				$sql.=", '" . esc($iban) . "'";
				$sql.=", '" . esc($bic) . "'";
				$sql.=", '" . esc($swift) . "'";
				$sql.=");";
				//echo $sql;
				$res_lock=execSql("LOCK TABLES ".FIN_TAB_RIB." WRITE");
				$res=execSql($sql);
				$rib_id = dernier_id($cnx->connection);
				$res_lock=execSql("UNLOCK TABLES ");
				msg_util_ajout(LANG_FIN_GENE_001);
				$operation_effectuee = 'enregistrement_termine';
				break;
		}
		
		
	}
	//***************************************************************************
	
	// Compter le nombre de RIB existants
	$sql ="SELECT rib_id, elev_id, numero_rib, code_banque, code_guichet, numero_compte, cle_rib, titulaire, banque, iban, bic, swift ";
	$sql.="FROM ".FIN_TAB_RIB." ";
	$sql.="WHERE elev_id = $elev_id ";
	//echo $sql;
	$res=execSql($sql);
	$nombre_rib = $res->numRows();
	
	// Rechercher le RIB de l'eleve
	$sql ="SELECT rib_id, elev_id, numero_rib, code_banque, code_guichet, numero_compte, cle_rib, libelle, titulaire, banque, iban, bic, swift ";
	$sql.="FROM ".FIN_TAB_RIB." ";
	$sql.="WHERE elev_id = $elev_id ";
	$sql.="AND numero_rib = $numero_rib ";
	//echo $sql;
	$res=execSql($sql);
	
	$prenom_nom = "";
	
	// Verifier si on a trouve l'eleve
	if($elev_id > 0) {
		//Rechercher les infos de l'eleve 
		$sql_eleve  = "SELECT elev_id, prenom, nom ";
		$sql_eleve .= "FROM " . FIN_TAB_ELEVES . " ";
		$sql_eleve .= "WHERE elev_id = " . $elev_id . " ";
		$res_eleve=execSql($sql_eleve);
		
		// on verifie si on a bien trouve l'eleve
		if($res_eleve->numRows() > 0) {
			$ligne_eleve = &$res_eleve->fetchRow();
			$prenom_nom = ucfirst($ligne_eleve[1]) . " " . strtoupper($ligne_eleve[2]);
		}
	
		// Verifier si l'eleve a deja le RIB demande
		if($res->numRows() > 0) {
			$mode = "modif";
			$ligne = & $res->fetchRow();
			$rib_id = $ligne[0];
		} else {
			$mode = "ajout";
		}
	}

	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	if($elev_id <= 0) {
		msg_util_ajout(LANG_FIN_GENE_006, 'erreur');
	}
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
		<title><?php echo LANG_FIN_RIB_001; ?></title>
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
					<b><font class="T2"><?php echo LANG_FIN_RIB_001; ?></font></b>
                    <?php
					if($prenom_nom != '') {
					?>
                    <br>(<?php echo $prenom_nom; ?>)
                    <?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" method="get" action="<?php echo url_script(); ?>" onSubmit="">
						<input type="hidden" name="actualiser_parent" id="actualiser_parent" value="<?php echo $actualiser_parent; ?>">
						<input type="hidden" name="mode" id="mode" value="<?php echo $mode; ?>">
						
						<input type="hidden" name="operation" id="operation" value="">
						
						<input type="hidden" name="rib_id" id="rib_id" value="<?php echo $rib_id; ?>">
						
						<input type="hidden" name="elev_id" id="elev_id" value="<?php echo $elev_id; ?>">
						
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
			
							
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td align="center"><?php echo LANG_FIN_RIB_014; ?>&nbsp;:&nbsp;</td>
											<td>
												<select name="numero_rib" id="numero_rib" onChange="onchange_numero_rib();">
													<?php
													// Definir le n° du dernier RIB (3 maximum)
													$maximum = $nombre_rib + 1;
													if($maximum > 3) {
														$maximum = 3;
													}
													for($j=1; $j<=$maximum; $j++) {
														$selected = '';
														if($j == $numero_rib) {
															$selected = 'selected="selected"';
														}
													?>
													<option value="<?php echo $j; ?>" <?php echo $selected; ?>>N°<?php echo $j; ?></option>
													<?php
													}
													?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							
							<tr>
								<td align="center">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td align="center"><?php echo LANG_FIN_RIB_015; ?>&nbsp;:&nbsp;</td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[7];
											}
											if ($valeur == '') $valeur=$prenom_nom;
											?>
											<td align="center"><input type="text" name="libelle" id="libelle" value="<?php echo $valeur; ?>" size="32" maxlength="32"></td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							
							<tr>
								<td align="center">
								
									<table cellspacing="1" cellpadding="3" border="1" width="100%" align="center">
									<!--<table border="1" align="center" width="100%" bordercolor="#000000">-->
										<tr>
											<td align="center"><b><?php echo LANG_FIN_RIB_003; ?></b></td>
											<td align="center"><b><?php echo LANG_FIN_RIB_004; ?></b></td>
											<td align="center"><b><?php echo LANG_FIN_RIB_005; ?></b></td>
											<td align="center"><b><?php echo LANG_FIN_RIB_006; ?></b></td>
										</tr>
										<tr>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[3];
												$codebanque=$valeur;
											}
											?>
											<td align="center"><input type="text" name="code_banque" id="code_banque" value="<?php echo $valeur; ?>" size="5" maxlength="5"></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[4];
												$codeguichet=$valeur;
											}
											?>
											<td align="center"><input type="text" name="code_guichet" id="code_guichet" value="<?php echo $valeur; ?>" size="5" maxlength="5"></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[5];
												$numerocompte=$valeur;
											}
											?>
											<td align="center"><input type="text" name="numero_compte" id="numero_compte" value="<?php echo $valeur; ?>" size="11" maxlength="11"></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[6];
												$cle=$valeur;
											}
											?>
											<td align="center"><input type="text" name="cle_rib" id="cle_rib" value="<?php echo $valeur; ?>" size="2" maxlength="2"></td>
										</tr>
										<tr>
											<td align="center"><b><?php echo LANG_FIN_RIB_012; ?></b></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[8];
											}
											?>
											<td align="left" colspan="3"><input type="text" name="titulaire" id="titulaire" value="<?php echo $valeur; ?>" size="24" maxlength="24"></td>
										</tr>
										<tr>
											<td align="center"><b><?php echo LANG_FIN_RIB_013; ?></b></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[9];
												
											}
											?>
											<td align="left" colspan="3"><input type="text" name="banque" id="banque" value="<?php echo $valeur; ?>" size="24" maxlength="24"></td>
										</tr>
										
										<tr>
											<td align="center"><b><?php echo LANG_FIN_RIB_007; ?></b>
											</td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[10];
											}
											if (trim($valeur) == '') {
												$valeur=Rib2Iban($codebanque,$codeguichet,$numerocompte,$cle);
											}
											?>
											<td align="left" colspan="3"><input type="text" name="iban" id="iban" value="<?php echo $valeur; ?>" size="30" maxlength="27"></td>
										</tr>
										<tr>
											<td align="center"><b><?php echo LANG_FIN_RIB_008; ?></b></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[11];
											}
											?>
											<td align="left" colspan="3"><input type="text" name="bic" id="bic" value="<?php echo $valeur; ?>" size="11" maxlength="11"></td>
										</tr>
										<tr>
											<td align="center"><b><?php echo LANG_FIN_RIB_009; ?></b></td>
											<?php
											$valeur = '';
											if($mode = 'modif') {
												$valeur = $ligne[12];
											}
											?>
											<td align="left" colspan="3"><input type="text" name="swift" id="swift" value="<?php echo $valeur; ?>" size="16" maxlength="16"></td>
										</tr>
										
									</table>
								</td>
							</tr>
							<?php
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
											//if($res->numRows() > 0) {
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANGENR?>","onclick_enregistrer()");</script>
											</td>
											<?php
											//}
											?>
											<td align="center">
												<script language="javascript">buttonMagicFermeture();</script>
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
					
					<script language="javascript">
							
						function onclick_enregistrer() {
							var valide = true;
							var obj;
							var message_erreur = '';
							var separateur = '';
							
							if(trim(document.getElementById('code_banque').value) != '' && trim(document.getElementById('code_guichet').value) != '' && trim(document.getElementById('numero_compte').value) != '' && trim(document.getElementById('cle_rib').value) != '') {
							
								// On verifie que le 'libelle' n'est pas vide
								obj = document.getElementById('libelle');
								//alert(obj.value);
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_RIB_015); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}

								// On verifie que le 'code_banque' est numerique
								obj = document.getElementById('code_banque');
								if(!est_nombre(obj.value, 'entier', '')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_002, LANG_FIN_RIB_003); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
				
								// On verifie que le 'code_guichet' est numerique
								obj = document.getElementById('code_guichet');
								if(!est_nombre(obj.value, 'entier', '')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_002, LANG_FIN_RIB_004); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
				
								// On verifie que le 'numero_compte' est alphanumerique
								obj = document.getElementById('numero_compte');
								if(!valider_chaine(strtoupper(obj.value), '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_003, LANG_FIN_RIB_005); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
				
								// On verifie que la 'cle_rib' est numerique
								obj = document.getElementById('cle_rib');
								if(!est_nombre(obj.value, 'entier', '')) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_002, LANG_FIN_RIB_006); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}

								// On verifie que la 'cle_rib' saisie correspond a celle calculee
								obj_code_banque = document.getElementById('code_banque');
								obj_code_guichet = document.getElementById('code_guichet');
								obj_numero_compte = document.getElementById('numero_compte');
								obj_cle_rib = document.getElementById('cle_rib');
								if(calculer_cle_rib(obj_code_banque.value , obj_code_guichet.value , obj_numero_compte.value) != obj_cle_rib.value) {
									message_erreur += separateur + "     - <?php echo LANG_FIN_RIB_018; ?>";									separateur = "\n";
									if(valide) {
										obj_cle_rib.focus();
									}
									valide = false;
								}
								
								// On verifie que le 'titulaire' n'est pas vide
								obj = document.getElementById('titulaire');
								//alert(obj.value);
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_RIB_012); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
								// On verifie que la 'banque' n'est pas vide
								obj = document.getElementById('banque');
								if(trim(obj.value) == '') {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_RIB_013); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
								
							} else {
								message_erreur += separateur + "     - <?php echo LANG_FIN_RIB_011; ?>";
								separateur = "\n";
								if(valide) {
									document.getElementById('code_banque').focus();
								}
								valide = false;
							}
							
							if(valide) {
								msg_util_attente_montrer(true);
								document.formulaire.operation.value = "enregistrer";
								document.formulaire.but_actualiser.click();
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}
			
							return(valide);
						}
			
						// Actualiser la page quant l'utilisateur change d numero de RIB
						function onchange_numero_rib() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = "";
							document.formulaire.but_actualiser.click();
						}
			
					</script>		
							
				</td>
			</tr>		
		</table>
		<?php
		}
		?>

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
				
				<?php
				if($operation_effectuee == 'enregistrement_termine' && $actualiser_parent == '1') {
				?>
				window.opener.actualiser();

				<?php
				}
				?>
			}
			
			// Executer initialisation_page() au chargement de la page
			if (window.addEventListener) {
				window.addEventListener("load",initialisation_page,false);
			} else if (window.attachEvent) { 
				window.attachEvent("onload",initialisation_page);
			}	
					
		</script>


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
