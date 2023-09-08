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
	
	$operation1 = lire_parametre('operation1', '', 'GET');
	if($operation1 != '')
	{
	$_SESSION[CHA_REP_MODULE]['planning_reservation']['chambre_id'] = lire_parametre('chambre', '', 'GET');
	$_SESSION[CHA_REP_MODULE]['planning_reservation']['date_debut'] = lire_parametre('date_debut', '16/11/1990', 'GET');
	$_SESSION[CHA_REP_MODULE]['planning_reservation']['date_fin'] = lire_parametre('date_fin', '16/11/1990', 'GET');
	}	
	
	//***************************************************************************
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	// Rechercher la liste des eleves
	$sql  = "SELECT e.elev_id, e.nom, e.prenom, c.code_class, c.libelle ";
	$sql .= "FROM ".CHA_TAB_ELEVES." e ";
	$sql .= "LEFT JOIN ".CHA_TAB_CLASSES." c ON e.classe=c.code_class ";
	// Appliquer le filtre
	$au_moins_un_de_critere = true;
	$recherche_de = '';
	
	switch($operation) {
		case "rechercher_code_class":
			$sql.="WHERE c.code_class = " . $code_class ." ";
			// Rechercher le libelle de la classe : pour afficher le message dans l'entete du tableau de resultat
			$sql2  = "SELECT libelle ";
			$sql2 .= "FROM ".CHA_TAB_CLASSES." ";
			$sql2 .= "WHERE code_class = " . $code_class ." ";
			$classe = execSql($sql2);
			if($classe->numRows() > 0) {
				$ligne = $classe->fetchRow();
				$recherche_de = LANG_CHA_GENE_062 . ' \'' . ucfirst($ligne[0]) . '\'';
			}
			// $_SESSION[CHA_REP_MODULE]['inscription_rechercher']['code_class'] = $code_class;
			// $_SESSION[CHA_REP_MODULE]['inscription_rechercher']['nom_eleve'] = '';
			break;
		case "rechercher_nom_eleve":
		
			//$nom_slashes = addslashes($nom_eleve);
	
			$sql .= "WHERE lower(e.nom) LIKE '" . strtolower($nom_eleve) . "%' ";
			
			// Preparer le message pour l'entete du tableau de resultat
			if($nom_eleve != '') {
				$recherche_de = LANG_CHA_GENE_063 . ' \'' . $nom_eleve . '\'';
			} else {
				$recherche_de = LANG_CHA_GENE_064 . ' (' . LANG_CHA_GENE_025 . ')';
			}
			// $_SESSION[CHA_REP_MODULE]['inscription_rechercher']['code_class'] = '';
			// $_SESSION[CHA_REP_MODULE]['inscription_rechercher']['nom_eleve'] = $nom_eleve;
			break;
		default:
			// Pour ne trouver aucun eleve
			$sql .= "WHERE 1=0 ";
			$au_moins_un_de_critere = false;
	}
	$sql.="ORDER BY c.libelle, e.nom, e.prenom ";
	$eleves = execSql($sql);
	
	$tab_eleves = array();
	if($eleves->numRows() > 0) {
		for($i=0; $i<$eleves->numRows(); $i++) {
			$ligne = $eleves->fetchRow();
			
			$tab_eleves[$i] = array(
									"elev_id" => $ligne[0],
									"nom" => $ligne[1],
									"prenom" => $ligne[2],
									"code_class" => $ligne[3],
									"libelle" => $ligne[4]
									);
									
									
		}
	}
	
	// Rechercher la liste des classes
	$sql ="SELECT code_class, libelle ";
	$sql.="FROM ".CHA_TAB_CLASSES." ";
	$sql.="ORDER BY libelle";
	$classes=execSql($sql);
	
	// Selectionner la premiere classe (si il n'y en a pas deja une)
	if($classes->numRows() > 0 && $code_class <= 0) {
		$ligne = null;
		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$code_class = $ligne[0];
	}
	
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" ><?php echo LANG_CHA_GENE_061; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
						<tr>
							<td align="center">&nbsp;</td>
						</tr>
						<tr>
							<td valign=top align="center">
								<fieldset>
								<?php //*******************  CRITERES DE RECHERCHE ********************* ?>
										<legend><?php echo LANG_CHA_GENE_026; ?></legend>
											<table border="0" cellpadding="0" cellspacing="0" align="center">
												<form name="formulaire_classe" id="formulaire_classe" action="<?php echo url_script(); ?>" method="post" onSubmit="">
														<input type="hidden" name="operation" id="operation" value="rechercher_code_class">
														<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire; ?>">
													<tr>
														<td align="right"><?php echo LANG_CHA_GENE_062; ?>&nbsp;:&nbsp;</td>
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
																<div class="messages_utilisateur"><span class="avertissement"><?php echo LANG_CHA_GENE_065; ?></span></div>
															<?php
															}
															?>
														</td>
														<td>&nbsp;</td>
														<td align="left">
															<input type="button" class="button" value="<?php echo LANG_CHA_GENE_020; ?>" onClick="onclick_chercher_code_class();" <?php echo $disabled; ?>>
														</td>
													</tr>
												</form>
												
												
												<form name="formulaire_eleve" id="formulaire_eleve" action="<?php echo url_script(); ?>" method="post" onSubmit="">
														<input type="hidden" name="operation" id="operation" value="rechercher_nom_eleve">
														<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire; ?>">
													<tr>
														<td align="right"><?php echo LANG_CHA_GENE_063; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<input type="text" name="nom_eleve" id="nom_eleve" value="<?php echo $nom_eleve; ?>" size="20" maxlength="20" onKeyUp="onkeyup_nom_eleve(this);" style="width:15em;" autocomplete="off">
														</td>
														<td>&nbsp;</td>
														<td align="left">
															<input type="button" class="button" value="<?php echo LANG_CHA_GENE_020; ?>" onClick="onclick_chercher_nom_eleve();" >
														</td>
													</tr>
													<tr>
														<td></td>
														<td style="padding-top:0px;" valign="top" colspan="3">
															<div id="resultat_recherche" style=""></div>
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
				<table style="margin-left:15px; margin-right:15px;">
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
										<input type="button" class="button" value="<?php echo LANG_CHA_RESA_001; ?>" onClick="onclick_reservation();" >
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
														$trouves = '&nbsp;-&nbsp;' .LANG_CHA_GENE_024 . '&nbsp;:&nbsp;' . count($tab_eleves);
													}
													?>
													<b><font id="menumodule1"><?php echo LANG_CHA_GENE_066; ?> :<font id="color2">&nbsp;<?php echo $recherche_de; ?></font><?php echo $trouves; ?></font></b>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr bgcolor="#ffffff">
									<td align="left" valign="top" rowspan="2"><b>&nbsp;</b></td>
									<td align="left" valign="top" rowspan="2"><b><?php echo LANG_CHA_GENE_062; ?></b></td>
									<td align="left" valign="top" rowspan="2"><b><?php echo LANG_CHA_RESA_004; ?></b></td>
									<td align="left" valign="top" rowspan="2"><b><?php echo LANG_CHA_RESA_003; ?></b></td>
								</tr>
								<tr bgcolor="#ffffff"></tr>
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
										<?php
									if ($nombre_lignes_eleve > 0) $rowspan="rowspan='$nombre_lignes_eleve'";
										?>
										<td align="center" valign="top" <?php echo $rowspan ; ?>">
											<input type="radio" name="radio_elev_id" id="radio_elev_id" value="<?php echo $valeur; ?>" <?php echo $checked; ?>>
										</td>
										<td align="left" valign="top" rowspan="<?php echo $rowspan; ?>"><?php echo ucfirst($ligne["libelle"]); ?></td>
										<td align="left" valign="top" rowspan="<?php echo $rowspan; ?>"><?php echo strtoupper($ligne["nom"]); ?></td>
										<td align="left" valign="top" rowspan="<?php echo $rowspan; ?>"><?php echo ucfirst($ligne["prenom"]); ?></td>
									</tr>
								<?php
									}
								} else {
								?>
								<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
									<td align="left" colspan="7">
										<?php 
										if($au_moins_un_de_critere) {
											// Aucun eleve trouve
											echo LANG_CHA_GENE_068;
										} else {
											// Pas de critere
											echo LANG_CHA_GENE_067;
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
										<input type="button" class="button" value="<?php echo LANG_CHA_RESA_001; ?>" onClick="onclick_reservation();" >
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
	<script language="javascript">
			var fenetre = null;
			var timeout = null;
			var contenu_target = '';

			function onclick_chercher_code_class() {
				msg_util_attente_montrer(true);
			//	document.formulaire_classe.inscrits_pas_inscrits.value = radio_lire_valeur(document.formulaire_eleve.inscrits_pas_inscrits);
				document.formulaire_classe.submit();
			}

			function onclick_chercher_nom_eleve() {
				msg_util_attente_montrer(true);
				document.formulaire_eleve.submit();
			}

			function onkeyup_nom_eleve(objet) {
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
		
		
			function ajax_recherche() {
	
			var type_recherche;
			var id_formulaire;
			var id_champ_critere_1;
			var id_champ_critere_2;
			var id_champ_critere_3;
			var id_champ_critere_4;
			var id_champ_critere_5;
			var mode_resultat;
			var id_conteneur_resultat;
			var url_module;
			
			var msg_validation_1;
			var msg_validation_2;
			var msg_validation_3;
			
			var obj_ajax_recherche;
			var id_local;
			
			var fonction_onclick;
			
			// Initialiser
			this.init = function() {
				this.id_local = this.random_id();
				eval(this.id_local + " = this;");
				
				// Valeurs par defaut
				this.type_recherche = 'eleve';
				this.id_formulaire = 'formulaire';
				this.id_champ_critere_1 = 'critere_1';
				this.id_champ_critere_2 = '';
				this.id_champ_critere_3 = '';
				this.id_champ_critere_4 = '';
				this.id_champ_critere_5 = '';
				this.mode_resultat = 'TABLE_DANS_DIV';
				this.id_conteneur_resultat = 'resultat_recherche';
				this.url_module = '';
				this.msg_validation_1 = 'Votre session a expiré';
				this.msg_validation_2 = 'Erreur dans le script appelé';
				this.msg_validation_3 = 'Erreur communication avec serveur';
				this.obj_ajax_recherche = null;
				this.fonction_onclick = '';
			}
			
			// Lancer la recherche
			this.rechercher = function() {
				var obj_champ_critere_1 = null;
				var str_champ_critere_1 = '';
				var obj_zone_affichage = null;
				var obj_ligne;
				var obj_cellule;
				var str_html = '';
				var obj_parent;
				

				// recuperer le champ de formulaire contenant le premier critere de recherche
				try {
					eval("obj_champ_critere_1 = document." + this.id_formulaire + "." + this.id_champ_critere_1 + ";");
				}
				catch(e) {
					obj_champ_critere_1 = null;
				}

				// Recuperer le contenu du champ
				if(obj_champ_critere_1 != null) {
					switch(obj_champ_critere_1.type) {
						case "text" :
						case "TEXT" :
							str_champ_critere_1 = this.trim(obj_champ_critere_1.value);
							break;
						case "select" :
						case "SELECT" :
							if(obj_champ_critere_1.selectedIndex >= 0) {
								str_champ_critere_1 = obj_champ_critere_1.options[obj_champ_critere_1.selectedIndex].value;
							} else {
								str_champ_critere_1 = '';
							}
							break;
					}
				}
							
				
				// Recuperer le resultat de la recherche (par Ajax)
				if(obj_champ_critere_1 != null) {
					//str_html = str_champ_critere_1;
					
					if(trim(str_champ_critere_1) != '') {
						this.obj_ajax_recherche = new Ajax();
						// Parametres de l'Ajax
						this.obj_ajax_recherche.setParam ({
							url : this.url_module + "/ajax_recherche.php",
							returnFormat : "txt",
							method : "POST",
							data : {
								type_recherche : this.type_recherche,
								critere_1 : str_champ_critere_1,
								critere_2 : '',
								critere_3 : '',
								critere_4 : '',
								critere_5 : '',
								fonction_onclick : this.fonction_onclick
							},
							asynchronus : false,
							onComplete : this.id_local + ".recherche_reussite(response)",
							onFailure : this.id_local + ".recherche_echec(errorCode)"
							
						});
									
						// Appeler l'Ajax
						this.obj_ajax_recherche.execute();
					} else {
						this.afficher_resultat('');
					}
					
				}
					

				/*
				
				onComplete : this.id_local + ".recherche_reussite(response, '" + this.id_conteneur_resultat + "')",
				*/		

			}
			
			
			this.ltrim = function(s) {
			   return s.replace(/^\s+/, "");
			}
			this.rtrim = function(s) {
			   return s.replace(/\s+$/, "");
			}
			
			this.trim = function(s) {
			   return this.rtrim(this.ltrim(s));
			}
			
			// Generer un id unique
			this.random_id = function () {
				var str_available_char = new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
				var i;
				var str_id = "";
				var int_pos;
				for(i=1;i<=32;i++) {
					int_pos = Math.floor((16)*Math.random()) + 0;
					str_id += str_available_char[int_pos];
				}
				return("_" + str_id);
			}


			this.recherche_reussite = function (response) {
				var total_enregistrements = 0;
				var donnees = new String(response);
									
				// Decoupage de la reponse (envoyee par le script Ajax)
				donnees_decoupees = donnees.split('¬');
				
				 // alert('reponse=' + response);
				//document.getElementById('resultat_recherche').innerHTML = response;
				// alert(donnees_decoupees[2]);
				
				switch(donnees_decoupees[0]) {
					case '0': // Pas d'erreur
						if(donnees_decoupees[1] > 0) {
							this.afficher_resultat(donnees_decoupees[2]);
						} else {
							this.afficher_resultat('');
						}
						break;
						
					case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
						this.afficher_resultat('');
						alert(this.msg_validation_1);
						break;
						
					default: // Erreur inconuue
						this.afficher_resultat('');
						// Remplacer la liste deroulante par la nouvelle
						alert(this.msg_validation_2);
				}
				
			}
			
			this.recherche_echec = function (errorCode) {
				this.afficher_resultat('');
				alert(this.msg_validation_3);
			}
			
			this.afficher_resultat = function(str_html) {
				if(str_html != '') {
					obj_parent = document.getElementById(this.id_conteneur_resultat);
					if(obj_parent != null) {
						obj_parent.innerHTML = str_html;
					}
				} else {
					obj_parent = document.getElementById(this.id_conteneur_resultat);
					if(obj_parent != null) {
						obj_parent.innerHTML = '';
					}

				}
			}
								
		}
				
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
			
			// Pour remplacer les liens '#'
			function maj_liens() {
				var html = document.getElementById("target").innerHTML;
				//alert(html);
				if(html != contenu_target) {
					clearTimeout(timeout);
					liens_remplacer_href('<?php echo site_url_racine(CHA_REP_MODULE); ?>#', 'javascript:;');
				} else {
					timeout = setTimeout("maj_liens() ", 100);
				}
			}
				
			function onclick_reservation() {
				var elev_id = radio_lire_valeur(document.formulaire_liste.radio_elev_id);
				msg_util_attente_montrer(true);
				//alert(elev_id);
				document.formulaire_inscrire.elev_id_res.value = elev_id;
				document.formulaire_inscrire.submit();
			}
		
		</script>
		<form name="formulaire_inscrire" id="formulaire_inscrire" action="<?php echo $g_chemin_relatif_module; ?>planning_ajout_reservation.php" method="post">	
			<input type="hidden" name="elev_id_res" id="elev_id_res" value="0">
			<input type="hidden" name="operation1" id="operation1" value="ajouter">
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
		
		
		<?php
		}
		?>
		
	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>
