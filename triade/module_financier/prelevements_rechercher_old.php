<?php
session_start();
set_time_limit(0);
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
set_time_limit(0);
ini_set('memory_limit', '256M');


// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation = lire_parametre('operation', '', 'POST');
	$date_limite = lire_parametre('date_limite', '', 'POST');
	$date_reglement = lire_parametre('date_reglement', '', 'POST');
	$code_class = lire_parametre('code_class', 0, 'POST');
	$bareme_id = lire_parametre('bareme_id', 0, 'POST');
	$ordre_tri = lire_parametre('ordre_tri', 'nom_eleve', 'POST');
	//***************************************************************************

	
	$tab_groupe_type = array();
	
	$sql ="SELECT groupe_id, libelle ";
	$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
	$sql.="ORDER BY groupe_id";
	$groupe_type=execSql($sql);
			
	for($i=0; $i<$groupe_type->numRows(); $i++) {
		
		$res = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
		$tab_groupe_type[count($tab_groupe_type)] = array(
													'groupe_id' => $ligne_groupe_type[0],
													'libelle' => $ligne_groupe_type[1],
													'total' => 0.0,
													'reste_a_payer' => 0.0,
													);
	}
	// /*
	// 20100708 - AP : Avant on initialisait la date limite au cinq du mois, mais maintenant c'est la date du jour
	// Calculer la date au cinq du mois
	$jour = date("j");
	$mois = date("n");
	$annee = date("Y");
	// Si on est apres le cinq, on passe au moi
	if($jour > 5) {
		$mois++;
	}
	$jour = "05";
	$mois = substr('00' . $mois, strlen('00' . $mois) - 2);
	$date_au_cinq_du_mois = $jour . "/" . $mois . "/" . $annee;

	// Initialiser la date limite si elle est vide
	if($date_limite == '') {
		$date_limite = $date_au_cinq_du_mois;
	}
	
	// Initialiser la date de reglement si elle est vide
	if($date_reglement == '') {
		$date_reglement = $date_au_cinq_du_mois;
	}	
	
	
	// Initialiser la date limite si elle est vide
	if($date_limite == '') {
		$date_limite = date('d/m/Y');
	}
		// Initialiser la date de reglement si elle est vide
	if($date_reglement == '') {
		$date_reglement = date('d/m/Y');
	}
	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	$tab_echeances = array();
	if($operation == 'rechercher') {

		// Rechercher la liste des echeances
		$sql  = "SELECT el.elev_id, el.nom, el.prenom, ec.echeancier_id, ec.date_echeance, ec.montant, ec.numero_rib, cl.code_class, cl.libelle, i.annee_scolaire, i.date_depart ";
		$sql .= "FROM ".FIN_TAB_ECHEANCIER." ec ";
		$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON ec.inscription_id = i.inscription_id ";
		$sql .= "INNER JOIN ".FIN_TAB_ELEVES." el ON i.elev_id = el.elev_id ";
		$sql .= "INNER JOIN ".FIN_TAB_CLASSES." cl ON i.code_class = cl.code_class ";
		$sql .= "WHERE ec.date_echeance <= '" . date_vers_bdd($date_limite) . "' ";
		$sql .= "AND ec.type_reglement_id = " . $g_tab_type_reglement_id['prelevement'] . " ";
		$sql .= "AND ec.type <> 2 ";  // => Ne pas inclure les remises exceptionnelles 
		if($ordre_tri == 'nom_eleve') {
			$sql .= "ORDER BY el.nom ASC, el.prenom ASC, ec.date_echeance ASC ";
		} else {
			$sql .= "ORDER BY ec.date_echeance ASC, el.nom ASC, el.prenom ASC ";
		}
		// 20100708 - AP : Maintenant on ordone par date d'echeance, puis par nom de l'eleve
		//$sql .= "ORDER BY el.nom, el.prenom, ec.date_echeance ASC";
	
		//$sql.=" LIMIT 50 ";	
		$echeances = execSql($sql);
		$nombre_echeances_total = 0;
		if($echeances->numRows() > 0) {
			// Recuperer les infos de chaque echeance a traiter
			for($i=0; $i<$echeances->numRows(); $i++) {
			
				$res = $echeances->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
				
				// 20100805 - AP : on doit verifier que l'echeance n'est pas posterieure a la date de depart 
				//                 (date de sortie)
				$posterieure_date_sortie = false;
				$date_echeance_tmp = trim($ligne[4]);
				$date_echeance_tmp = str_replace("-", "", $date_echeance_tmp);
				$date_depart_tmp = trim($ligne[10]);
				$date_depart_tmp = str_replace("-", "", $date_depart_tmp);
				if($date_depart_tmp != '' && $date_echeance_tmp > $date_depart_tmp) {
					$posterieure_date_sortie = true;
				}
				
				if(!$posterieure_date_sortie) {
				
					// 20100708 - AP : Maintenant les echeances sont triees par date : il faut donc rechercher la 
					// position ou inserer chaque nouvelle echeance trouvee
				
					// Verifier si on a deja une echeance pour le meme eleve
					$derniere_position = -1;
					for($j=0; $j<count($tab_echeances); $j++) {
						// Il y a deja l'eleve dans le tableau
						if(trim($tab_echeances[$j]["eleves_elev_id"]) == trim($ligne[0])) {
							$derniere_position = $j;
							// Chercher la derniere position pour cet eleve si on est pas deja a la fin du tableau
							if($derniere_position != (count($tab_echeances) - 1)) {
								for($k=$derniere_position; $k<count($tab_echeances); $k++) {
									if(trim($tab_echeances[$k]["eleves_elev_id"]) != trim($ligne[0])) {
										break;
									}
									$derniere_position = $k;
								}
							}
							break;
						}
						
					}
				
					// Si aucune position trouvee (eleve pas dans le tableau) ou bien si 
					// c'est le dernier element du tableau , on ajoute a la fin
					if($derniere_position == -1 || $derniere_position == (count($tab_echeances) - 1)) {
						$nouvelle_position = count($tab_echeances);
					} else {
						// => Dans ce cas, on va ajouter au milieu du tableau
						
						// Decaler les elements de la fin du tableau (une position vers l'exterieur)
						for($j=(count($tab_echeances) - 1); $j>$derniere_position; $j--) {
							$tab_echeances[$j + 1] = $tab_echeances[$j];
						}
						// La nouvelle position est celle de l'emplacement maintenant libre.
						$nouvelle_position = $derniere_position + 1;
					}
					
					// Recuperer la ligne du resultat
						
					$reste_a_payer = reglement_reste_a_payer('echeance', $ligne[3]);
					
					if($reste_a_payer > 0.0) {
						$nombre_echeances_total++;
					}
						
					$tab_echeances[$nouvelle_position] = array(
														"eleves_elev_id" => $ligne[0],
														"eleves_nom" => $ligne[1],
														"eleves_prenom" => $ligne[2],
														"echeancier_echeancier_id" => $ligne[3],
														"echeancier_date_echeance" => $ligne[4],
														"echeancier_montant" => $ligne[5],
														"echeancier_numero_rib" => $ligne[6],
														"classes_code_class" => $ligne[7],
														"classes_libelle" => $ligne[8],
														"inscription_annee_scolaire" => $ligne[9],
														"reste_a_payer" => $reste_a_payer
														);
				}
			}
		
			// Cumuler les echeances par eleve
			if(count($tab_echeances) > 0) {
				$tab_echeances_tmp = $tab_echeances;
				$tab_echeances = array();
				$elev_id_courant = 0;
				for($i=0; $i<count($tab_echeances_tmp); $i++) {
					// Verifier que l'echeance n'a pas encore ete completement payee
					if($tab_echeances_tmp[$i]["reste_a_payer"] > 0) {
						if($elev_id_courant != $tab_echeances_tmp[$i]["eleves_elev_id"]) {
							// => Ajouter un nouvel enregistrement avec une nouvelle echeance
							// Verifier si l'eleve a un RIB
							$sql  = "SELECT rib_id ";
							$sql .= "FROM ".FIN_TAB_RIB." ";
							$sql .= "WHERE elev_id = " . $tab_echeances_tmp[$i]["eleves_elev_id"];
							//echo $sql;
							$rib=execSql($sql);
							if($rib->numRows()) {
								$rib_existe = true;
							} else {
								$rib_existe = false;
							}
							
							$echeancier = array();
							$echeancier[0] = array(
									"echeancier_echeancier_id" => $tab_echeances_tmp[$i]["echeancier_echeancier_id"],
									"echeancier_date_echeance" => $tab_echeances_tmp[$i]["echeancier_date_echeance"],
									"echeancier_montant" => $tab_echeances_tmp[$i]["echeancier_montant"],
									"echeancier_numero_rib" => $tab_echeances_tmp[$i]["echeancier_numero_rib"],
									"reste_a_payer" => $tab_echeances_tmp[$i]["reste_a_payer"]
									);
							$tab_echeances[count($tab_echeances)] = array(
									"eleves_elev_id" => $tab_echeances_tmp[$i]["eleves_elev_id"],
									"eleves_nom" => $tab_echeances_tmp[$i]["eleves_nom"],
									"eleves_prenom" => $tab_echeances_tmp[$i]["eleves_prenom"],
									"echeancier" => $echeancier,
									"classes_code_class" => $tab_echeances_tmp[$i]["classes_code_class"],
									"classes_libelle" => $tab_echeances_tmp[$i]["classes_libelle"],
									"inscription_annee_scolaire" => $tab_echeances_tmp[$i]["inscription_annee_scolaire"],
									"total" => $tab_echeances_tmp[$i]["reste_a_payer"],
									"rib_existe" => $rib_existe
									);
							$elev_id_courant = $tab_echeances_tmp[$i]["eleves_elev_id"];
						} 
						else 
						{
							// => Ajouter une nouvelle echeance dans l'enregistrement precedent
							
							// Recuperer les donnees de l'enregistrement precedent
							$echeancier = $tab_echeances[count($tab_echeances)-1]["echeancier"];
							$total = $tab_echeances[count($tab_echeances)-1]["total"];
							
							// Ajouter
							$echeancier[count($echeancier)] = array(
									"echeancier_echeancier_id" => $tab_echeances_tmp[$i]["echeancier_echeancier_id"],
									"echeancier_date_echeance" => $tab_echeances_tmp[$i]["echeancier_date_echeance"],
									"echeancier_montant" => $tab_echeances_tmp[$i]["echeancier_montant"],
									"echeancier_numero_rib" => $tab_echeances_tmp[$i]["echeancier_numero_rib"],
									"reste_a_payer" => $tab_echeances_tmp[$i]["reste_a_payer"]
									);
							$total += $tab_echeances_tmp[$i]["reste_a_payer"];
							
							// Remettre les infos dans l'enregistrement precedent
							$tab_echeances[count($tab_echeances)-1]["echeancier"] = $echeancier;
							$tab_echeances[count($tab_echeances)-1]["total"] = $total;
							
						}

						$temp_ech =$tab_echeances_tmp[$i]["echeancier_echeancier_id"];

						$sql1 ="SELECT groupe_id, echeancier_id, montant ";
						$sql1.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
						$sql1.="WHERE echeancier_id = $temp_ech ";
						$sql1.="ORDER BY groupe_id ";
						$groupes=execSql($sql1);
						// echo $sql1;
						$pourcentage = 0;
						if($tab_echeances_tmp[$i]["reste_a_payer"] != 0){
							$pourcentage = $tab_echeances_tmp[$i]["reste_a_payer"] / $tab_echeances_tmp[$i]["echeancier_montant"];
						}
						for($v=0; $v <$groupes->numRows();$v++)
						{
							$resg = $groupes->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
						
							for($l=0; $l< count($tab_groupe_type);$l++)
							{
								if($tab_groupe_type[$l]['groupe_id'] == $ligne_groupe[0]) {
									if($pourcentage != 0)
									{
										$temp = $ligne_groupe[2] * $pourcentage;
										$tab_groupe_type[$l]['reste_a_payer'] += $temp;
									}
								}
							}
						}
					}
				}
				for($l=0; $l< count($tab_groupe_type);$l++)
				{
					$total_groupe_type += $tab_groupe_type[$l]['reste_a_payer'];
				}
			}
		}
	}
	//echo "<pre>";
	//print_r($tab_echeances);
	//echo "</pre>";
	//***************************************************************************
	
	
	
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
					<b><font id="menumodule1" ><?php echo LANG_FIN_GPRE_001; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td colspan="2" valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<input type="hidden" name="operation" id="operation" value="">
						<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class; ?>">
						<input type="hidden" name="liste_elev_id" id="liste_elev_id" value="">
						<input type="hidden" name="liste_numero_rib" id="liste_numero_rib" value="">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style=" z-index:1">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">
								
									<?php
									// Pour la gestion des calendriers
									include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");

									//*******************  CRITERES DE RECHERCHE *********************
									
									?>
									<fieldset id="fieldset_criteres" style="z-index:5; margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_FIN_GENE_026; ?></legend>
										<table cellpadding="0" cellspacing="2" align="center">
											<tr>
												<td align="right"><?php echo LANG_FIN_GPRE_002; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td align="left">
																<?php
																$valeur = $date_limite;
																?>
																<input type="text" name="date_limite" id="date_limite" size="10" maxlength="10" value="<?php echo $valeur; ?>">
															</td>
															<td>&nbsp;</td>
															<td align="left">
																<?php
																calendarDim("div_date_limite","document.formulaire.date_limite",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
																?>
															</td>
															<td>&nbsp;</td>
															<td valign="middle">
															<a href="javascript:;"  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg',' <?php echo LANG_FIN_GPRE_003; ?>', 'fieldset_criteres');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"></a>

															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="right"><?php echo LANG_FIN_GPRE_011; ?>&nbsp;:&nbsp;</td>
												<td align="left">
													<table cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td align="left">
																<select name="ordre_tri" id="ordre_tri">
																	<?php
																		$selected = '';
																		if($ordre_tri == 'nom_eleve') {
																			$selected = 'selected="selected"';
																		}
																	?>
																	<option value="nom_eleve" <?php echo $selected; ?>><?php echo LANG_FIN_GPRE_012; ?></option>
																	<?php
																		$selected = '';
																		if($ordre_tri == 'date_echeance') {
																			$selected = 'selected="selected"';
																		}
																	?>
																	<option value="date_echeance" <?php echo $selected; ?>><?php echo LANG_FIN_GPRE_013; ?></option>
																</select>																
															</td>
															<td colspan="4">&nbsp;</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" align="center">
													<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_rechercher();">
												</td>
												<td align="center">
													<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
												</td>
											</tr>
												
										</table>
									</fieldset>
							
									
									<?php
									//*******************  LISTE DES ECHEANCES *********************
									
									if($operation == 'rechercher') {
									?>
									<br>
									<fieldset id="fieldset_echeances" style="z-index:4; margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_FIN_ECHE_003; ?></legend>
											<?php
											if(count($tab_echeances) > 0) {
											?>
											<p align="left">
												<?php echo $nombre_echeances_total; ?> <?php echo strtolower(LANG_FIN_ECHE_003); ?>
												&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
											</p>
											<?php
											}
											?>
											<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%" style="width:100%">
												<tr bgcolor="#ffffff">
													<td align="right" nowrap="nowrap">#</td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_CLAS_003); ?></b></td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_011); ?></b></td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_ELEV_004); ?></b></td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_ELEV_005); ?></b></td>
													<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_030); ?></b></td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_034); ?></b></td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_RIB_017); ?></b></td>
													<td align="left" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
													<td align="center" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_RIB_010); ?></b></td>
												</tr>
												<input type="hidden" name="eleves_total" id="eleves_total" value="<?php echo count($tab_echeances); ?>">

											<?php
											$total_global = 0.0;
											$nombre_de_liste_deroulantes_rib = 0;
											if(count($tab_echeances) > 0) {
												for($i=0; $i<count($tab_echeances); $i++) {
													 $lignes_pour_eleve = count($tab_echeances[$i]['echeancier']);
													 if($tab_echeances[$i]['rib_existe']) {
														$rib_existe = 1;
													 } else {
														$rib_existe = 0;
													 }
													 
													// Recuperer la liste des RIB pour l'eleve courant
													$tab_rib = liste_rib($tab_echeances[$i]['eleves_elev_id']);
													//print_r($tab_rib);
													
													$rib_id_par_defaut = 0;
													
													$total_global += $tab_echeances[$i]['total'];

											?>
												<?php // Afficher la premiere ligne ?>
												<input type="hidden" name="rib_existe_<?php echo ($i+1); ?>" id="rib_existe_<?php echo ($i+1); ?>" value="<?php echo $rib_existe; ?>">
												<input type="hidden" name="elev_id_<?php echo ($i+1); ?>" id="elev_id_<?php echo ($i+1); ?>" value="<?php echo $tab_echeances[$i]['eleves_elev_id']; ?>">
												<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
													<td valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ($i+1); ?></td>
													<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ucfirst($tab_echeances[$i]['classes_libelle']); ?></td>
													<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ucfirst($tab_echeances[$i]['inscription_annee_scolaire']); ?></td>
													<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ucfirst($tab_echeances[$i]['eleves_prenom']); ?></td>
													<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo strtoupper($tab_echeances[$i]['eleves_nom']); ?></td>
													
													
													<td align="right" valign="top" nowrap="nowrap"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][0]['echeancier_date_echeance']); ?></td>
													<td align="right" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][0]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
													<?php
													$nombre_de_liste_deroulantes_rib++;
													$rib_id_par_defaut = $tab_echeances[$i]['echeancier'][0]['echeancier_numero_rib'];
													//$valeur = $tab_echeances[$i]['echeancier'][0]['echeancier_numero_rib'];
													//$rib_id_par_defaut = $valeur;
													//if($valeur == 0) {
													//	$valeur = LANG_FIN_GENE_049;
													//} else {
													//	$valeur = $tab_rib[$valeur - 1];
													//}
													$id_select = "numero_rib_" . $tab_echeances[$i]['eleves_elev_id'] . "_" . $tab_echeances[$i]['echeancier'][0]['echeancier_echeancier_id'];
													?>
													<td align="left" valign="top" nowrap="nowrap">
														
														<select name="<?php echo $id_select; ?>" id="<?php echo $id_select; ?>">
															<?php
															// Si il n'y a pas de rib selectionne => essayer de preselectionner le premier si il y en a un seul dans la liste
															if($rib_id_par_defaut == 0 && count($tab_rib) > 0 && count($tab_rib) == 1) {
																$rib_id_par_defaut = 1;
															}
															
															
															$selected = '';
															if($rib_id_par_defaut == 0) {
																$selected = 'selected="selected"';
															}
															?>
															<option value="0" <?php echo $selected; ?>><?php echo LANG_FIN_GENE_050; ?></option>
															<?php
															for($j=0; $j<count($tab_rib); $j++) {
																$selected = '';
																if($rib_id_par_defaut == ($j+1)) {
																	$selected = 'selected="selected"';
																}
															?>
															<option value="<?php echo ($j+1); ?>" <?php echo $selected; ?>><?php echo ($j+1); ?> - <?php echo $tab_rib[$j]; ?></option>
															<?php
															}
															?>
														</select>
														<input type="hidden" name="<?php echo $id_select; ?>_numero_ligne_tableau" id="<?php echo $id_select; ?>_numero_ligne_tableau" value="<?php echo ($i+1); ?>">
													
													
													</td>
													
													
													<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo montant_depuis_bdd($tab_echeances[$i]['total']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
													<?php
													if($tab_echeances[$i]['rib_existe']) {
														$valeur = ucfirst(LANG_FIN_GENE_017);
													} else {
														$valeur = ucfirst(LANG_FIN_GENE_018);
													}
													?>
													<td align="center" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>">
														<table border="0" cellpadding="0" cellspacing="0" align="center">
															<tr>
																<td align="center">
																</td>
															</tr>
															<tr>
																<td><img src="															./<?php echo $g_chemin_relatif_module; ?>images/espaceur.gif" border="0" width="1" height="4"></td>
															</tr>
															<tr>
																<td align="center">
																	<input type="button" class="button" onClick="onclick_editer_rib(<?php echo $tab_echeances[$i]['eleves_elev_id'];?>);" value='<?php echo LANG_FIN_RIB_001; ?>' >
																</td>
															</tr>
														</table>
														
													</td>
												</tr>	
																	
												<?php // Afficher les autres lignes d'echeance ?>
												<?php
													for($j=1; $j<count($tab_echeances[$i]['echeancier']); $j++) {
												?>
												<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
													<td align="right" valign="top" nowrap="nowrap"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['echeancier_date_echeance']); ?></td>
													<td align="right" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
													<?php
														$nombre_de_liste_deroulantes_rib++;
														$rib_id_par_defaut = $tab_echeances[$i]['echeancier'][$j]['echeancier_numero_rib'];
													
														//$valeur = $tab_echeances[$i]['echeancier'][$j]['echeancier_numero_rib'];
														//if($valeur == 0) {
														//	$valeur = LANG_FIN_GENE_049;
														//} else {
														//	$valeur = $tab_rib[$valeur - 1];
														//}
														$id_select = "numero_rib_" . $tab_echeances[$i]['eleves_elev_id'] . "_" . $tab_echeances[$i]['echeancier'][$j]['echeancier_echeancier_id'];
													?>
													<td align="left" valign="top" nowrap="nowrap">
														<select name="<?php echo $id_select; ?>" id="<?php echo $id_select; ?>">
															<?php
															// Si il n'y a pas de rib selectionne => essayer de preselectionner le premier si il y en a un seul dans la liste
															if($rib_id_par_defaut == 0 && count($tab_rib) > 0 && count($tab_rib) == 1) {
																$rib_id_par_defaut = 1;
															}															
															
															$selected = '';
															if($rib_id_par_defaut == 0) {
																$selected = 'selected="selected"';
															}
															?>
															<option value="0" <?php echo $selected; ?>><?php echo LANG_FIN_GENE_050; ?></option>
															<?php
															for($k=0; $k<count($tab_rib); $k++) {
																$selected = '';
																if($rib_id_par_defaut == ($k+1)) {
																	$selected = 'selected="selected"';
																}
															?>
															<option value="<?php echo ($k+1); ?>" <?php echo $selected; ?>><?php echo ($k+1); ?> - <?php echo $tab_rib[$k]; ?></option>
															<?php
															}
															?>
														</select>
														<input type="hidden" name="<?php echo $id_select; ?>_numero_ligne_tableau" id="<?php echo $id_select; ?>_numero_ligne_tableau" value="<?php echo ($i+1); ?>">
													</td>
												</tr>
												<?php
													}
												?>
											<?php
												}
											?>
											<tr class="tabnormal2">
												<td colspan="7">&nbsp;</td>
												<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
												<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_global); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
												<td>&nbsp;</td>
											</tr>
											<?php
											} else {
											?>
											<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
												<td align="left" colspan="10"><?php echo LANG_FIN_GPRE_004; ?></td>
											</tr>
											<?php
											}
											?>
											</table>
											
											<br>
										<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
											<tr bgcolor="#ffffff">
												<td align="right">&nbsp;</td>
												<td align="right"><b><?php echo LANG_FIN_GPRE_014; ?></b></td>
											</tr>
											<?php
											for($k=0; $k<count($tab_groupe_type); $k++) {
											?>
											<tr bgcolor="#ffffff">
												<td align="right"><b><?php echo $tab_groupe_type[$k]['libelle']; ?></b></td>
												<td align="center"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											</tr>
											<?php
											}
											?>
											<tr bgcolor="#ffffff"></tr><tr bgcolor="#ffffff"></tr>
											<tr bgcolor="#ffffff">
												<td align="right"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
												<td align="center"><b><?php echo montant_depuis_bdd($total_groupe_type); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
											</tr>
										</table>
											<?php
											if(count($tab_echeances) > 0) {
											?>
											<table border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo LANG_FIN_GPRE_008; ?>&nbsp;:&nbsp;</td>
													<td>
														<table cellspacing="0" cellpadding="0" border="0">
															<tr>
																<td align="left">
																	<?php
																	$valeur = $date_reglement;
																	?>
																	<input type="text" name="date_reglement" id="date_reglement" size="10" maxlength="10" value="<?php echo $valeur; ?>">
																</td>
																<td>&nbsp;</td>
																<td align="left">
																	<?php
																	calendarDim("div_date_reglement","document.formulaire.date_reglement",$_SESSION["langue"], "0", "0", 'fieldset_echeances', 'null', 'null');	
																	?>
																</td>
																<td>&nbsp;</td>
																<td valign="middle">
																<a href="javascript:;"  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg',' <?php echo LANG_FIN_GPRE_009; ?>', 'fieldset_echeances');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"></a>
	
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="2" align="center">
														<input type="button" class="button" onClick="onclick_generer_fichier();" value='<?php echo LANG_FIN_GPRE_005; ?>' >
													</td>
												</tr>
											</table>
											<?php
											}
											?>

									</fieldset>
									<?php
									}
									?>
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
				
				
							<?php //********** BOUTONS ********** 
							if($operation == 'rechercher') {?>
							<tr>
								<td align="center">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<?php } ?>
								
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
					</form>
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						var liste_fenetre = new Array();
					
						function onclick_annuler() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_annuler').submit();
						}

						
						// Validation donnees avant recherche des echeances
						function onclick_rechercher() {
							var message_erreur = '';
							var separateur = '';
							var valide = true;
							var messsage;
							var obj;
							var i=0;

							obj = document.getElementById('date_limite');
							// Verifier que la date limite a ete fournie
							if(trim(obj.value) == '') {
								message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_GPRE_002); ?>";
								separateur = "\n";
								if(valide) {
									obj.focus();
								}
								valide = false;
							} else {

								if(!est_date(obj.value, false)) {
									message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_006, LANG_FIN_GPRE_002); ?>";
									separateur = "\n";
									if(valide) {
										obj.focus();
									}
									valide = false;
								}
							}
							
							if(valide) {
								msg_util_attente_montrer(true);
								document.formulaire.operation.value = 'rechercher';
								document.formulaire.action = "<?php echo url_script(); ?>";
								document.formulaire.target = "";
								document.formulaire.but_actualiser.click();

							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}


						}
						
						// Quand l'utilisateur clique sur le bouton pour generer le fichier de prelevement pour la banque
						function onclick_generer_fichier() {
							var message_erreur = '';
							var separateur = '';
							var valide = true;
							var messsage;
							var obj;
							var obj2;
							var i=0;
							var rib_existe;
							var liste_elev_id = '';
							var liste_numero_rib = '';
							var separateur_liste;
							var listes_deroulantes;
							var id_objet = '';
							
							// Recueperer le nombre totoal d'eleves affiches
							var eleves_total = document.getElementById('eleves_total').value;
							


							// Verifier qu'ils ont tous un RIB (rechercher les listes deroulantes des RIB)
							if (document.getElementsByTagName) {
							
								// Recuperer tous les object avec tag 'select'.
								eval('listes_deroulantes = document.getElementsByTagName("select")');
								//alert(listes_deroulantes.length);
								for (i=0; i<listes_deroulantes.length; i++) {
									// Ne traiter que les listes deroulantes correspondant a un numero de RIB
									id_objet = listes_deroulantes[i].id;
									if(id_objet.substring(0,11) == 'numero_rib_') {
										// Verifier si un RIB est selectionne ou non
										obj = document.getElementById(listes_deroulantes[i].id);
										if(obj.options[obj.selectedIndex].value == '0') {
											obj2 = document.getElementById(id_objet + '_numero_ligne_tableau');
											//alert(obj2.value);
											messsage = "<?php echo LANG_FIN_GPRE_006; ?>";
											messsage = messsage.replace('#i#', obj2.value);
											message_erreur += separateur + "     - " + messsage;
											separateur = "\n";
											valide = false;
										}									
									}
								}
					
							}

							for(i=1;i<=eleves_total;i++) {
								//rib_existe = document.getElementById('rib_existe_' + i).value;

							}
							
							if(valide) {
								if(confirm("<?php echo LANG_FIN_GPRE_007; ?>")) {
									// Recuperer la date limite et la passer au formulaire de generation du fichier
									//document.formulaire_generer.date_limite.value = document.formulaire.date_limite.value;
									
									// Recuperer la date de reglement et la passer au formulaire de generation du fichier
									//document.formulaire_generer.date_reglement.value = document.formulaire.date_reglement.value;
									
							
									// Recuperer le nombre totoal d'eleves affiches
									//var eleves_total = document.getElementById('eleves_total').value;
							
									// Construire les liste de elev_id et numero_rib
									separateur_liste = '';
									for(i=1;i<=eleves_total;i++) {
										obj = document.getElementById('elev_id_' + i);
										liste_elev_id += separateur_liste + obj.value;
										//obj = document.getElementById('numero_rib_' + i);
										//liste_numero_rib += separateur_liste + obj.options[obj.selectedIndex].value;
										separateur_liste = ',';
									}
									// Passer la liste de elev_id au formulaire de generation du fichier
									document.formulaire.liste_elev_id.value = liste_elev_id;
									
									// Passer la liste de elev_id au formulaire de generation du fichier
									//document.formulaire_generer.liste_numero_rib.value = liste_numero_rib;
								

									document.formulaire.action = "<?php echo $g_chemin_relatif_module; ?>prelevements_generer.php";
									document.formulaire.target = "_blank";
									
									//alert("ATENTION : ce script est en phase de test.\nC'est à dire que le fichier de prélèvement est généré,\mais les règlements ne sont pas générés automatiquement.\nCeci permet de tester sans riques.\nUne fois ce script validé il passera en mode production et les règlements seront générés.");

									document.formulaire.submit();
								}
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}


						}
						
						// Ouvrir le popup pour editer le RIB
						function onclick_editer_rib(elev_id) {
							for(i=0; i<liste_fenetre.length; i++) {
								try {
									liste_fenetre[i].close();
								}
								catch(e) {
								}
							}
							liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE) . FIN_REP_MODULE; ?>/rib_editer.php?elev_id=' + elev_id + '&actualiser_parent=1','editer_rib_' + elev_id,'width=550,height=300');
						}
						
						// Actualiser le formulaire
						function actualiser() {
							onclick_rechercher();
						}
						
						function onclic_export_excel() {
							//document.formulaire_export_excel.date_limite.value = document.formulaire.date_limite.value;
							//alert(document.formulaire.date_limite.value);
							//document.formulaire_export_excel.ordre_tri.value = document.formulaire.ordre_tri.options[document.formulaire.ordre_tri.selectedIndex].value;
							//document.formulaire_export_excel.submit();
							
							document.formulaire.action = "<?php echo $g_chemin_relatif_module; ?>prelevements_rechercher_excel.php";
							document.formulaire.target = "_blank";

							document.formulaire.submit();

						}
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>paiements.php" method="post">
					</form>
					<form name="formulaire_generer" id="formulaire_generer" action="<?php echo $g_chemin_relatif_module; ?>prelevements_generer.php" method="post" target="_blank">
						<input type="hidden" name="date_limite" id="date_limite" value="">
						<input type="hidden" name="date_reglement" id="date_reglement" value="">
						<input type="hidden" name="liste_elev_id" id="liste_elev_id" value="">
						<input type="hidden" name="liste_numero_rib" id="liste_numero_rib" value="">
					</form>
					
					<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>prelevements_rechercher_excel.php" method="post" target="">
						<input type="hidden" name="date_limite" id="date_limite" value="">
						<input type="hidden" name="ordre_tri" id="ordre_tri" value="">
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
