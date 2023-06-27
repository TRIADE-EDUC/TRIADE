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
	$nom_eleve = trim(lire_parametre('nom_eleve', '', 'POST'));
	$inscrits_pas_inscrits = trim(lire_parametre('inscrits_pas_inscrits', 'tous', 'POST'));
	//***************************************************************************

	function extraire_inscrits() {
		$liste_id = '';
		$separateur = '';
		$sql  = "SELECT elev_id ";
		$sql .= "FROM ".FIN_TAB_INSCRIPTIONS." ";
		$inscriptions = execSql($sql);
		for($i=0; $i<$inscriptions->numRows(); $i++) {
			$ligne = $inscriptions->fetchRow();
			$liste_id .= $separateur . $ligne[0];
			$separateur = ',';
		}
		return($liste_id);
	}


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Rechercher la liste des eleves
	$sql  = "SELECT e.elev_id, e.nom, e.prenom, c.code_class, c.libelle ";
	$sql .= "FROM ".FIN_TAB_ELEVES." e ";
	$sql .= "LEFT JOIN ".FIN_TAB_CLASSES." c ON e.classe=c.code_class ";
	// Appliquer le filtre
	$au_moins_un_de_critere = true;
	$recherche_de = '';
	
	// essayer d'utiliser les infos de la session pour pre-rechercher
	if($operation == '') {
		if($_SESSION[FIN_REP_MODULE]['inscription_rechercher']['code_class'] != '') {
			$operation = 'rechercher_code_class';
			$code_class = $_SESSION[FIN_REP_MODULE]['inscription_rechercher']['code_class'];
		} else {
			if($_SESSION[FIN_REP_MODULE]['inscription_rechercher']['nom_eleve'] != '') {
				$operation = 'rechercher_nom_eleve';
				$nom_eleve = $_SESSION[FIN_REP_MODULE]['inscription_rechercher']['nom_eleve'];
			}
		}
		if($_SESSION[FIN_REP_MODULE]['inscription_rechercher']['inscrits_pas_inscrits'] != '') {
			$inscrits_pas_inscrits = $_SESSION[FIN_REP_MODULE]['inscription_rechercher']['inscrits_pas_inscrits'];
		} else {
			$inscrits_pas_inscrits = 'tous';
		}
	}
	
	switch($operation) {
		case "rechercher_code_class":
			$sql.="WHERE c.code_class = " . $code_class ." ";
			// Rechercher le libelle de la classe : pour afficher le message dans l'entete du tableau de resultat
			$sql2  = "SELECT libelle ";
			$sql2 .= "FROM ".FIN_TAB_CLASSES." ";
			$sql2 .= "WHERE code_class = " . $code_class ." ";
			
			// Filtrer par Inscrits/Pas inscrits
			if($inscrits_pas_inscrits != 'tous') {
				$liste_id_inscrits = extraire_inscrits();
				switch($inscrits_pas_inscrits) {
					case 'inscrits':
						if($liste_id_inscrits != '') {
							$sql.="AND e.elev_id IN (" . $liste_id_inscrits .") ";
						} else {
							$sql.="AND 1=0 ";
						}
						break;
					case 'pas_inscrits':
						if($liste_id_inscrits != '') {
							$sql.="AND e.elev_id NOT IN (" . $liste_id_inscrits .") ";
						}
						break;
				}
			}
			
			$classe = execSql($sql2);
			if($classe->numRows() > 0) {
				$ligne = $classe->fetchRow();
				$recherche_de = LANG_FIN_CLAS_003 . ' \'' . ucfirst($ligne[0]) . '\'';
			}
			$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['code_class'] = $code_class;
			$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['nom_eleve'] = '';
			$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['inscrits_pas_inscrits'] = $inscrits_pas_inscrits;
			break;
		case "rechercher_nom_eleve":
		
			//$nom_slashes = addslashes($nom_eleve);
	
			$sql .= "WHERE lower(e.nom) LIKE '" . strtolower($nom_eleve) . "%' ";
			
			// Filtrer par Inscrits/Pas inscrits
			if($inscrits_pas_inscrits != 'tous') {
				$liste_id_inscrits = extraire_inscrits();
				switch($inscrits_pas_inscrits) {
					case 'inscrits':
						if($liste_id_inscrits != '') {
							$sql.="AND e.elev_id IN (" . $liste_id_inscrits .") ";
						} else {
							$sql.="AND 1=0 ";
						}
						break;
					case 'pas_inscrits':
						if($liste_id_inscrits != '') {
							$sql.="AND e.elev_id NOT IN (" . $liste_id_inscrits .") ";
						}
						break;
				}
			}			
			
			// Preparer le message pour l'entete du tableau de resultat
			if($nom_eleve != '') {
				$recherche_de = LANG_FIN_ELEV_002 . ' \'' . $nom_eleve . '\'';
			} else {
				$recherche_de = LANG_FIN_ELEV_001 . ' (' . LANG_FIN_GENE_025 . ')';
			}
			$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['code_class'] = '';
			$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['nom_eleve'] = $nom_eleve;
			$_SESSION[FIN_REP_MODULE]['inscription_rechercher']['inscrits_pas_inscrits'] = $inscrits_pas_inscrits;
			break;
		default:
			// Pour ne trouver aucun eleve
			$sql .= "WHERE 1=0 ";
			$au_moins_un_de_critere = false;
	}
	$sql.="ORDER BY c.libelle, e.nom, e.prenom ";
	//echo $sql;
	$eleves = execSql($sql);
	// Stocker la liste des eleves dans le tableau $tab_eleves
	$tab_eleves = array();
	if($eleves->numRows() > 0) {
		for($i=0; $i<$eleves->numRows(); $i++) {
			$ligne = $eleves->fetchRow();
			
			// Rechercher les inscriptions existantes pour l'eleve
			$sql  = "SELECT i.inscription_id, i.annee_scolaire, c.libelle ";
			$sql .= "FROM ".FIN_TAB_INSCRIPTIONS." i ";
			$sql .= "INNER JOIN ".FIN_TAB_CLASSES." c ON i.code_class = c.code_class ";
			$sql .= "WHERE elev_id = " . $ligne[0] ." ";
			$sql .= "ORDER BY i.annee_scolaire DESC, c.libelle ASC ";
			//echo $sql;
			$inscriptions=execSql($sql);
			$tab_inscriptions = array();
			if($inscriptions->numRows() > 0) {
				for($j=0; $j<$inscriptions->numRows(); $j++) {
					$ligne_inscriptions = $inscriptions->fetchRow();
					$tab_inscriptions[$j] = array(
									"inscription_id" => $ligne_inscriptions[0],
									"annee_scolaire" => $ligne_inscriptions[1],
									"libelle" => $ligne_inscriptions[2]
									);
				}
			}
			
			$tab_eleves[$i] = array(
									"elev_id" => $ligne[0],
									"nom" => $ligne[1],
									"prenom" => $ligne[2],
									"code_class" => $ligne[3],
									"libelle" => $ligne[4],
									"inscriptions" => $tab_inscriptions
									);
		}
	}
	//print_r($tab_eleves);
	//***************************************************************************
	
	
	// Rechercher la liste des classes
	$sql ="SELECT code_class, libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." ";
	$sql.="ORDER BY libelle";
	$classes=execSql($sql);
	
	// Selectionner la premiere classe (si il n'y en a pas deja une)
	if($classes->numRows() > 0 && $code_class <= 0) {
		$ligne = null;
		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$code_class = $ligne[0];
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
		
		<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
		<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
		
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
		include("./librairie_php/ajax.php");
		ajax_js();
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
					<b><font id="menumodule1" ><?php echo LANG_FIN_INSC_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">
									<fieldset>
										<legend><?php echo LANG_FIN_GENE_021; ?></legend>
										
											<table border="0" cellpadding="0" cellspacing="2" align="center">
												
												<form name="formulaire_classe" id="formulaire_classe" action="<?php echo url_script(); ?>" method="post" onSubmit="">
													<input type="hidden" name="operation" id="operation" value="rechercher_code_class">
													<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire; ?>">
                                                    <input type="hidden" name="inscrits_pas_inscrits" id="inscrits_pas_inscrits" value="<?php echo inscrits_pas_inscrits; ?>">
												<tr>
													<td align="right"><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;</td>
													<td align="left">
														<?php
														// Verifier si on a au moins une classe
														$disabled = '';
														if($classes->numRows() > 0) {
														?>
														<select name="code_class" id="code_class" onChange="onchange_code_class()">
															<?php
															for($i=0; $i<$classes->numRows(); $i++) {
																$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
																$selected = '';
																if($code_class == $ligne[0]) {
																	$selected = 'selected="selected"';
																}
															?>
															<option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo ucfirst($ligne[1]); ?></option>
															<?php
															}
															?>
														</select>
														<?php
														} else {
															$disabled = 'disabled';
														?>
															<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_FIN_CLAS_002; ?></span></div>
														<?php
														}
														?>
													</td>
													<td>&nbsp;</td>
													<td align="left">
														<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_chercher_code_class();" <?php echo $disabled; ?>>
													</td>
												</tr>
												</form>

												<form name="formulaire_eleve" id="formulaire_eleve" action="<?php echo url_script(); ?>" method="post" onSubmit="">
													<input type="hidden" name="operation" id="operation" value="rechercher_nom_eleve">
													<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire; ?>">
												<tr>
													<td align="right"><?php echo LANG_FIN_ELEV_002; ?>&nbsp;:&nbsp;</td>
													<td align="left">
														<input type="text" name="nom_eleve" id="nom_eleve" value="<?php echo $nom_eleve; ?>" size="20" maxlength="20" onKeyUp="onkeyup_nom_eleve(this);" style="width:15em;" autocomplete="off">
													</td>
													<td>&nbsp;</td>
													<td align="left">
														<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_chercher_nom_eleve();" >
													</td>
												</tr>
                                               	<tr>
													<td></td>
													<td style="padding-top:0px;" valign="top" colspan="3">
														<div id="resultat_recherche" style=""></div>
													</td>
												</tr>
												<tr>
													<td align="right"><?php echo LANG_FIN_ELEV_006; ?>&nbsp;:&nbsp;</td>
													<td align="left" colspan="3">
														<table border="0" cellpadding="0" cellspacing="2" align="center">
                                                        	<tr>
                                                            	<?php
																$checked = '';
																if($inscrits_pas_inscrits == '' || $inscrits_pas_inscrits == 'tous') {
																	$checked = 'checked';
																}
																?>
                                                            	<td align="right"><input type="radio" name="inscrits_pas_inscrits" value="tous" <?php echo $checked; ?> ></td>
                                                            	<td align="left"><?php echo ucfirst(LANG_FIN_GENE_025); ?></td>
                                                            	<td align="right">&nbsp;&nbsp;</td>

                                                            	<?php
																$checked = '';
																if($inscrits_pas_inscrits == 'inscrits') {
																	$checked = 'checked';
																}
																?>
                                                            	<td align="right"><input type="radio" name="inscrits_pas_inscrits" value="inscrits" <?php echo $checked; ?>></td>
                                                            	<td align="left"><?php echo ucfirst(LANG_FIN_GENE_058); ?></td>
                                                            	<td align="right">&nbsp;&nbsp;</td>

                                                            	<?php
																$checked = '';
																if($inscrits_pas_inscrits == 'pas_inscrits') {
																	$checked = 'checked';
																}
																?>
                                                            	<td align="right"><input type="radio" name="inscrits_pas_inscrits" value="pas_inscrits" <?php echo $checked; ?>></td>
                                                            	<td align="left"><?php echo ucfirst(LANG_FIN_GENE_059); ?></td>
                                                            </tr>
                                                        </table>
													</td>
												</tr>
												</form>
	
											</table>
										
									</fieldset>
									
						

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
				
				
								
						</table>
					
				</td>
			</tr>
		</table>


		<br>
		<form name="formulaire_liste" id="formulaire_liste" action="<?php echo url_script(); ?>" method="post" onSubmit="">
			<table cellspacing="0" cellpadding="0" border="0" width='100%'>
				<?php
				// Afficher les actions seulement si on a trouve au moins un eleve
				if(count($tab_eleves) > 0) {
				?>
				<tr>
					<td align="left" valign="top">
						<table cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>	
								<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="1"></td>
								<td valign="bottom"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/fleche_droite_vers_bas.png"; ?>" border="0"></td>
								<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="30"></td>
								<td valign="middle">
									<input type="button" class="button" value="<?php echo LANG_FIN_INSC_004; ?>" onClick="onclick_inscrire();" >
								</td>
							</tr>
						</table>				
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td align="left">
						<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
							<tr id="coulBar0">
								<td height="2" colspan="7">
									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										<tr>
											<td align="left">
												<?php
												// Pour indiquer combien d'eleves ont ete trouves
												$trouves = '';
												if($au_moins_un_de_critere) {
													$trouves = '&nbsp;-&nbsp;' .LANG_FIN_GENE_024 . '&nbsp;:&nbsp;' . count($tab_eleves);
												}
												?>
												<b><font id="menumodule1"><?php echo LANG_FIN_GENE_023; ?> :<font id="color2">&nbsp;<?php echo $recherche_de; ?></font><?php echo $trouves; ?></font></b>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#ffffff">
								<td align="left" valign="top" rowspan="2">&nbsp;</td>
								<td align="left" valign="top" rowspan="2"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
								<td align="left" valign="top" rowspan="2"><b><?php echo LANG_FIN_ELEV_005; ?></b></td>
								<td align="left" valign="top" rowspan="2"><b><?php echo LANG_FIN_ELEV_004; ?></b></td>
								<td align="center" valign="top" colspan="3"><b><?php echo LANG_FIN_INSC_019; ?></b></td>
							</tr>
							<tr bgcolor="#ffffff">
								<td align="center" valign="top"><b><?php echo LANG_FIN_GENE_011; ?></b></td>
								<td align="center" valign="top"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
								<td align="center" valign="top">&nbsp;</td>
							</tr>
							<?php
							if(count($tab_eleves) > 0) {
								for($i=0; $i<count($tab_eleves); $i++) {
									$ligne = $tab_eleves[$i];
									$nombre_lignes_eleve = count($ligne['inscriptions']);
							?>
							<tr  bgcolor="#ffffff">
								<?php
									$valeur = $ligne['elev_id'];
									
									$checked = '';
									if($i == 0) {
										$checked = 'checked="checked"';
									}
								?>
								<?php if ($nombre_lignes_eleve == 0) $nombre_lignes_eleve=""; ?>
								<td align="center" valign="top" rowspan="<?php echo $nombre_lignes_eleve; ?>">
									<input type="radio" name="radio_elev_id" id="radio_elev_id" value="<?php echo $valeur; ?>" <?php echo $checked; ?>>
								</td>
								<td align="left" valign="top" rowspan="<?php echo $nombre_lignes_eleve; ?>"><?php echo ucfirst($ligne["libelle"]); ?></td>
								<td align="left" valign="top" rowspan="<?php echo $nombre_lignes_eleve; ?>"><?php echo strtoupper($ligne["nom"]); ?></td>
								<td align="left" valign="top" rowspan="<?php echo $nombre_lignes_eleve; ?>"><?php echo ucfirst($ligne["prenom"]); ?></td>
								<?php
									// Afficher la premiere inscription (annee scolaire, classe, boutton)
									
									if(count($ligne['inscriptions']) > 0) {
										$valeur = $ligne['inscriptions'][0]["annee_scolaire"];
									} else {
										$valeur = '&nbsp;';
									}
								?>
								<td align="center" valign="middle"><?php echo $valeur; ?></td>
				
								<?php
									if(count($ligne['inscriptions']) > 0) {
										$valeur = ucfirst($ligne['inscriptions'][0]["libelle"]);
									} else {
										$valeur = '&nbsp;';
									}
								?>
								<td align="left" valign="middle"><?php echo $valeur; ?></td>
								<?php
									if(count($ligne['inscriptions']) > 0) {
										$valeur = $ligne['inscriptions'][0]["inscription_id"];
								?>
								<td align="center" valign="middle">
                                	<table border="0" cellpadding="0" cellspacing="0">
                                    	<tr>
                                        	<td align="left">
												<input type="button" class="button" value="<?php echo LANG_FIN_INSC_003; ?>" onClick="onclick_editer_inscription(<?php echo $valeur; ?>);" >
                                            </td>
                                            <td align="left">
                                                <img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" width="3" height="3" border="0">
                                            </td>
						<?php
                                            if(inscription_total_frais($valeur, -1) == (inscription_total_echeances($valeur, 0) + inscription_total_echeances($valeur, 2))) {
                                            ?>
                                            <td align="left">
                                                <img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" width="16" height="16" border="0">
                                            </td>
                                            <?php
                                            } else {
                                            ?>
                                            <td align="left">
                                                <img src="./<?php echo $g_chemin_relatif_module; ?>images/warning.gif" width="15" height="15" border="0">
                                            </td>
                                            <?php
                                            }
                                            ?>
                                  		</tr>
                                	</table>						
								</td>
								<?php
									} else {
								?>
										<td align="center" valign="middle">&nbsp;</td>
								<?php
									}
								?>
							</tr>
							
							<?php
							// Afficher les autres inscriptions
									if(count($ligne['inscriptions']) > 1) {
										for($j=1; $j<count($ligne['inscriptions']); $j++) {
							?>
							<tr  bgcolor="#ffffff">
								<?php
											$valeur = $ligne['inscriptions'][$j]["annee_scolaire"];
								?>
								<td align="center" valign="middle"><?php echo $valeur ?></td>
				
								<?php
									$valeur = ucfirst($ligne['inscriptions'][$j]["libelle"]);
								?>
								<td align="left" valign="middle"><?php echo $valeur ?></td>
								<?php
									$valeur = $ligne['inscriptions'][$j]["inscription_id"];
								?>
								<td align="center" valign="middle">
                                	<table border="0" cellpadding="0" cellspacing="0">
                                    	<tr>
                                        	<td align="left">
								<input type="button" class="button" value="<?php echo LANG_FIN_INSC_003; ?>" onClick="onclick_editer_inscription(<?php echo $valeur; ?>);" >
                                            </td>
                                            <td align="left">
                                                <img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" width="3" height="3" border="0">
                                            </td>
				   	   <?php
                                            if(inscription_total_frais($valeur, -1) == (inscription_total_echeances($valeur, 0) + inscription_total_echeances($valeur, 2))) {
                                            ?>
                                            <td align="left">
                                                <img src="./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif"width="16" height="16" border="0">
                                            </td>
                                            <?php
                                            }else{
                                            ?>
                                            <td align="left">
                                                <img src="./<?php echo $g_chemin_relatif_module; ?>images/warning.gif" width="15" height="15" border="0">
                                            </td>
                                            <?php
                                            }
                                            ?>
                                  		</tr>
                                	</table>						
								</td>
							</tr>
							<?php
								}
							}
						}
							} else {
							?>
							<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
								<td align="left" colspan="7">
									<?php 
									if($au_moins_un_de_critere) {
										// Aucun eleve trouve
										echo LANG_FIN_ELEV_003;
									} else {
										// Pas de critere
										echo LANG_FIN_GENE_026;
									}
									?>
								</td>
							</tr>
							<?php
							}
							?>
						</table>
					</td>
				</tr>
				<?php
				// Afficher les actions seulement si on a trouve au moins un eleve
				if(count($tab_eleves) > 0) {
				?>
				<tr>
					<td align="left" valign="top">
						<table cellspacing="0" cellpadding="0" border="0" align="left">
							<tr>	
								<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="1"></td>
								<td valign="top"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/fleche_droite_vers_haut.png"; ?>" border="0"></td>
								<td valign="middle"><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="5" height="30"></td>
								<td valign="middle">
									<input type="button" class="button" value="<?php echo LANG_FIN_INSC_004; ?>" onClick="onclick_inscrire();" >
								</td>
							</tr>
						</table>				
					</td>
				</tr>
				<?php
				}
				?>
			</table>
		</form>


		<?php //********** VALIDATION FORMULAIRES ********** ?>


		<?php //********** GESTION NAVIGATION ********** ?>
		
		<script language="javascript">
			var fenetre = null;
			var timeout = null;
			var contenu_target = '';

			function onclick_chercher_code_class() {
				msg_util_attente_montrer(true);
				document.formulaire_classe.inscrits_pas_inscrits.value = radio_lire_valeur(document.formulaire_eleve.inscrits_pas_inscrits);
				document.formulaire_classe.submit();
			}

			function onclick_chercher_nom_eleve() {
				msg_util_attente_montrer(true);
				document.formulaire_eleve.submit();
			}

			function onkeyup_nom_eleve(objet) {
				// On n'utilise plus la fonction searchRequest (probleme avec les liens), mais celle du module
				
				// contenu_target = document.getElementById("target").innerHTML;
				// searchRequest(objet,'eleve','target','formulaire_eleve','nom_eleve');
				// timeout = setTimeout("maj_liens() ", 100);
				
				
				var obj_recherche = new ajax_recherche();
			
				obj_recherche.init();
				
				obj_recherche.type_recherche = 'eleve';
				obj_recherche.id_formulaire = 'formulaire_eleve';
				
				obj_recherche.id_champ_critere_1 = 'nom_eleve';
				
				obj_recherche.msg_validation_1 = '<?php echo LANG_CHA_AJAX_001; ?>';
				obj_recherche.msg_validation_2 = '<?php echo LANG_CHA_AJAX_002; ?>';
				obj_recherche.msg_validation_3 = '<?php echo LANG_CHA_AJAX_003; ?>';

				obj_recherche.url_module = '<?php echo url_module(); ?>';
				
				obj_recherche.fonction_onclick = 'onclick_sur_nom_eleve';
				
				obj_recherche.rechercher();
				
			}
			
			function onclick_sur_nom_eleve(elev_id, prenom, nom) {
				document.formulaire_eleve.nom_eleve.value = nom;
				onclick_chercher_nom_eleve();
			}
		
			
			// Pour remplacer les liens '#'
			function maj_liens() {
				var html = document.getElementById("target").innerHTML;
				//alert(html);
				if(html != contenu_target) {
					clearTimeout(timeout);
					liens_remplacer_href('<?php echo site_url_racine(FIN_REP_MODULE); ?>#', 'javascript:;');
				} else {
					timeout = setTimeout("maj_liens() ", 100);
				}
			}
					
			function onclick_editer_inscription(inscription_id) {
				msg_util_attente_montrer(true);
				document.formulaire_editer.inscription_id.value = inscription_id;
				document.formulaire_editer.submit();
			}

			function onclick_inscrire() {
				var elev_id = radio_lire_valeur(document.formulaire_liste.radio_elev_id);
				msg_util_attente_montrer(true);
				//alert(elev_id);
				document.formulaire_inscrire.elev_id_insc.value = elev_id;
				document.formulaire_inscrire.submit();
			}

		</script>
		<form name="formulaire_inscrire" id="formulaire_inscrire" action="<?php echo $g_chemin_relatif_module; ?>inscription_dupliquer_echeancier.php" method="post">
			<input type="hidden" name="elev_id_insc" id="elev_id_insc" value="0">
			<input type="hidden" name="operation_rech" id="operation_rech" value="<?php echo $operation; ?>">
			<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class; ?>">
			<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve; ?>">
		</form>
		<form name="formulaire_editer" id="formulaire_editer" action="<?php echo $g_chemin_relatif_module; ?>inscription_editer.php" method="post">
			<input type="hidden" name="inscription_id" id="inscription_id" value="0">
			<input type="hidden" name="operation_rech" id="operation_rech" value="<?php echo $operation; ?>">
			<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class; ?>">
			<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve; ?>">
		</form>

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
