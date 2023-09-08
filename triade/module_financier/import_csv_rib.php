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
include("./librairie_php/lib_init_module.inc.php");

// Verification autorisations acces au module
if(autorisation_module()) {

	$debug = true;

	$bouton_importer = true;

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation = lire_parametre('operation', '', 'POST');
	$type_fichier = lire_parametre('type_fichier', 'corrige', 'GET');
	
	if($type_fichier == 'corrige') {
		$fichier_import = "2009BIS_corrige.txt";
		//$fichier_import = "2009BIS_court.txt";
	} else {
		$fichier_import = "2009BIS_original.txt";
	}
	//***************************************************************************
	
	$fichier_import_chemin = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'import_rib' . DIRECTORY_SEPARATOR . $fichier_import;
	$lignes_brut = array();
	$lignes_associatif = array();
	if(file_exists($fichier_import_chemin)) {
		$lignes_brut = file($fichier_import_chemin);
		//print_r($lignes_brut);
		$numero_ligne = 0;
		for($i=1;$i<count($lignes_brut);$i++) {
			if(trim($lignes_brut[$i]) != '') {
			
				$numero_ligne++;
				
				$erreur = '';
			
				$une_ligne = preg_split('/;/', $lignes_brut[$i]);
					
				// Verifier si la ligne contient suffisament d'elements
				if(count($une_ligne) >= 13) {					
					$nom = $une_ligne[1];
					$nom = str_replace('  ', ' ', $nom);
					$prenom = $une_ligne[2];
					$prenom = str_replace('  ', ' ', $prenom);
					$titulaire = $une_ligne[9];
					$banque = $une_ligne[10];
					$code_banque = $une_ligne[11];
					$code_guichet = $une_ligne[12];
					$numero_compte = $une_ligne[13];
					$cle_rib = calculerCleRib($une_ligne[11], $une_ligne[12], $une_ligne[13]);
					$numero_rib = 1;
				} else {
					$nom = '-';
					$prenom = '-';
					$titulaire = '-';
					$banque = '-';
					$code_banque = '-';
					$code_guichet = '-';
					$numero_compte = '-';
					$cle_rib = '-';
					$numero_rib = 1;
					$erreur = "ERR_LIGNE_INCOMPLETE";
				}		

				// Verifier si le code banque est valide
				if($erreur == '') {
					if(!is_numeric($code_banque) || $code_banque == 0) {
						$erreur = 'ERR_CODE_BANQUE_INCORRECT';
					}
				}

				// Verifier si le code guichet est valide
				//if($erreur == '') {
				//	if(!is_numeric($code_guichet) || $code_guichet == 0) {
				//		$erreur = 'ERR_CODE_GUICHET_INCORRECT';
				//	}
				//}


				// Verifier si la cle RIB est valide
				if($erreur == '') {
					if($cle_rib < 1 || $cle_rib > 97) {
						$erreur = 'ERR_CLE_RIB_INCORRECTE';
					}
				}
				
				// Verifier si l'eleve existe
				$elev_id = 0;
				if($erreur == '') {
					$sql  = "SELECT elev_id ";
					$sql .= "FROM ".FIN_TAB_ELEVES."  ";
					$sql .= "WHERE UPPER(nom) = '" . strtoupper($nom) ."' AND UPPER(prenom) = '" . strtoupper($prenom) ."'";
					$eleve = execSql($sql);
					if($eleve->numRows() > 0) {
						$ligne = $eleve->fetchRow();
						$elev_id = $ligne[0];
					} else {
						$erreur = 'ERR_ELEVE_INTROUVABLE';
					}
				}				
				
				$lignes_associatif[count($lignes_associatif)] = array(
												'numero_ligne' => $numero_ligne,
												'elev_id' => $elev_id,
												'nom' => $nom,
												'prenom' => $prenom,
												'titulaire' => $titulaire,
												'banque' => $banque,
												'code_banque' => $code_banque,
												'code_guichet' => $code_guichet,
												'numero_compte' => $numero_compte,
												'cle_rib' => $cle_rib,
												'erreur' => $erreur,
												'numero_rib' => 1
											);
											
				if($numero_ligne > 1) {
					// Incrementer ke numero de RIB si c'est pour le meme eleve que la ligne precedente
					if(strtoupper($lignes_associatif[count($lignes_associatif) - 2]['nom']) == strtoupper($une_ligne[1]) && strtoupper($lignes_associatif[count($lignes_associatif) - 2]['prenom']) == strtoupper($une_ligne[2])) {
						$lignes_associatif[count($lignes_associatif) - 1]['numero_rib'] = $lignes_associatif[count($lignes_associatif) - 2]['numero_rib'] + 1;
					}
				}	
				
						
			}
		}
		
	
	} else {
		msg_util_ajout('Impossible d\'ouvrir le fichier d\'importation', 'erreur');
		$bouton_importer = false;
	}

	//print_r($lignes_associatif);

	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	$nb_rib_importes = 0;
	// Initialisation sur changement de classe
	if($operation == "changement_code_class") {
		$bareme_id = 0;
		$frais_bareme_id = 0;
	}
	if($operation == "importer") {
		
		if(!$debug) {
			for($i=0;$i<count($lignes_associatif);$i++) {
				if($lignes_associatif[$i]['erreur'] == '') {
					$sql= "INSERT INTO ".FIN_TAB_RIB." (elev_id, numero_rib, code_banque, code_guichet, numero_compte, cle_rib, libelle, titulaire, banque, iban, bic, swift) ";
					$sql.="VALUES(";
					$sql.= "" . $lignes_associatif[$i]['elev_id'] . ", ";
					$sql.= "" . $lignes_associatif[$i]['numero_rib'] . ", ";
					$sql.= "'" . esc($lignes_associatif[$i]['code_banque']) . "', ";
					$sql.= "'" . esc($lignes_associatif[$i]['code_guichet']) . "', ";
					$sql.= "'" . esc($lignes_associatif[$i]['numero_compte']) . "', ";
					$sql.= "'" . esc($lignes_associatif[$i]['cle_rib']) . "', ";
					$sql.= "'" . esc($lignes_associatif[$i]['titulaire']) . "', ";
					$sql.= "'" . esc($lignes_associatif[$i]['titulaire']) . "', ";
					$sql.= "'" . esc($lignes_associatif[$i]['banque']) . "', ";
					$sql.= "'',";
					$sql.= "'',";
					$sql.= "''";
					$sql.=");";
					$res=execSql($sql);
	
					$nb_rib_importes++;
				}
			}
		}
		
		msg_util_ajout('IMPORTATION TERMINÉE : ' . $nb_rib_importes . ' RIB importés', 'message');
		
	}
	//***************************************************************************
	
	
	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	//***************************************************************************
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ../' . FIN_SCRIPT_PAS_AUTORISATION) ;
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" >Importation fichier CSV des RIB</font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire_principal" id="formulaire_principal" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						<input type="hidden" name="operation" id="operation" value="">
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
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">

									<p align="left">Fichier : <?php echo $fichier_import; ?></p>

									<?php //********** AFFICHAGE DES DONNEES DU FICHIER ********** ?>
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%" >
                                    	<tr bgcolor="#ffffff" style="font-size:12px;">
                                         	<th nowrap="nowrap">N° ligne</th>
                                        	<th nowrap="nowrap">ID élève</th>
                                      		<th nowrap="nowrap">Nom</th>
                                        	<th nowrap="nowrap">Prénom</th>
                                        	<th nowrap="nowrap">N° RIB</th>
                                        	<th nowrap="nowrap">Titulaire</th>
                                         	<th nowrap="nowrap">Banque</th>
                                        	<th nowrap="nowrap">Code banque</th>
                                        	<th nowrap="nowrap">Code guichet</th>
                                        	<th nowrap="nowrap">Compte</th>
                                        	<th nowrap="nowrap">Clé RIB</th>
                                        	<th nowrap="nowrap">Erreur</th>
                                       </tr>
                                       <?php
									   $total_erreur = 0;
                                       for($i=0;$i<count($lignes_associatif);$i++) {
									   		if($lignes_associatif[$i]['erreur'] != '') {
												$bouton_importer = false;
												$style = 'color:#FF0000';
												$total_erreur++;
											} else {
												$style = '';
											}
									   ?>
                                       <tr class="tabnormal2" onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';" style="font-size:8px;<?php echo $style; ?>">
                                         	<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['numero_ligne']; ?></td>
                                      <?php
									   if($lignes_associatif[$i]['numero_rib'] == 1) {
									   ?>
                                        	<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['elev_id']; ?></td>
                                        	<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['nom']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['prenom']; ?></td>
                                      <?php
									   } else {
									   ?>
                                        	<td align="left" nowrap="nowrap" colspan="3">&nbsp;</td>
                                       <?php
									   }
									   ?>
                                        	<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['numero_rib']; ?></td>
                                      		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['titulaire']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['banque']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['code_banque']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['code_guichet']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['numero_compte']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['cle_rib']; ?></td>
                                       		<td align="left" nowrap="nowrap"><?php echo $lignes_associatif[$i]['erreur']; ?></td>
                                       </tr>
                                       <?php
									   }
									   ?>
                                    </table>

									<p align="left">Nombre d'erreurs : <?php echo $total_erreur; ?></p>
								</td>
							</tr>
		
				
				
							<?php //********** BOUTONS ********** ?>
							
							<tr>
								<td align="center">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<td align="center">
                                            	<?php
												if($bouton_importer) {
												?>
												<script language="javascript">buttonMagic3("Importer","onclick_importer()");</script>
                                                <?php
												}
												?>
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
						var fenetre = null;
						var liste_fenetre = new Array();
						
						function onclick_importer() {
							msg_util_attente_montrer(true);
							document.formulaire_principal.operation.value = "importer";
							document.getElementById('formulaire_principal').submit();
						}

					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
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
