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
set_time_limit(300);

// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");

// Verification autorisations acces au module
if(autorisation_module()) {

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation = lire_parametre('operation', '', 'REQUEST');
	$date_debut = lire_parametre('date_debut', '01/01/' . date('Y'), 'REQUEST');
	$date_fin = lire_parametre('date_fin', '31/12/' . date('Y'), 'REQUEST');
	$code_class = lire_parametre('code_class', 0, 'REQUEST');
	$ordre_tri = lire_parametre('ordre_tri', 'libelle_classe', 'REQUEST');
	$mode_affichage = lire_parametre('mode_affichage', 'normal', 'REQUEST');
	//***************************************************************************


	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	/*
	if($operation != "reload_annee_scolaire") {
		if($annee_scolaire == 'aucune') {
			$annee_scolaire = annee_scolaire_courante();
		}
	}
	*/
	//***************************************************************************
	
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);

	if($operation != '')
	{
			$tab_types_reglement = array();
			// Rechercher la liste des types de reglement
			$sql ="SELECT type_reglement_id, libelle ";
			$sql.="FROM ".FIN_TAB_TYPE_REGLEMENT." ";
			$sql.="ORDER BY libelle";
			$types_reglement=execSql($sql);
			for($i=0; $i<$types_reglement->numRows(); $i++) {
				// Acces s l'enregistrement courant
				$res = $types_reglement->fetchInto($ligne_type_reglement, DB_FETCHMODE_DEFAULT, $i);
				$tab_types_reglement[count($tab_types_reglement)] = array(
																		'type_reglement_id' => $ligne_type_reglement[0],
																		'libelle' => $ligne_type_reglement[1],
																		'a_payer' => 0.0,
																		'reste_a_payer' => 0.0,
																		'encaisse' => 0.0
															);
			}

			
			$tab_groupe_type = array();
			// Rechercher la liste des groupes de frais
			$sql ="SELECT groupe_id, libelle ";
			$sql.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
			$sql.="ORDER BY groupe_id";
			$groupe_type=execSql($sql);
			
			for($i=0; $i<$groupe_type->numRows(); $i++) {
				// Acces s l'enregistrement courant
				$res = $groupe_type->fetchInto($ligne_groupe_type, DB_FETCHMODE_DEFAULT, $i);
				$tab_groupe_type[count($tab_groupe_type)] = array(
																		'groupe_id' => $ligne_groupe_type[0],
																		'libelle' => $ligne_groupe_type[1],
																		'total' => 0.0,
																		'reste_a_payer' => 0.0,
																		'encaisse' => 0.0
															);
			}


			// Rechercher la liste des eleves
			$sql ="SELECT el.elev_id, el.nom, el.prenom, i.inscription_id, i.annee_scolaire, cl.code_class, cl.libelle as libelle_classe, ec.echeancier_id, ec.date_echeance, ec.montant, tr.libelle as libelle_type_reglement, ec.type_reglement_id, i.date_depart ";
			$sql.="FROM (((".FIN_TAB_INSCRIPTIONS." i INNER JOIN ".FIN_TAB_ELEVES." el ON el.elev_id = i.elev_id) ";
			$sql.="INNER JOIN ".FIN_TAB_ECHEANCIER." ec ON ec.inscription_id = i.inscription_id) ";
			$sql.="INNER JOIN ".FIN_TAB_CLASSES." cl ON cl.code_class = i.code_class) ";
			$sql.="INNER JOIN ".FIN_TAB_TYPE_REGLEMENT." tr ON tr.type_reglement_id = ec.type_reglement_id ";
			$sql.="WHERE 1 = 1 ";
			
			if($code_class != '0') {
				$sql.="AND i.code_class = " . $code_class . " ";
			}
			if($date_debut != '') {
				$sql.="AND ec.date_echeance >= '" . date_vers_bdd($date_debut) . " 00:00:00' ";
			}
			if($date_fin != '') {
				$sql.="AND ec.date_echeance <= '" . date_vers_bdd($date_fin) . " 23:59:59' ";
			}
			$sql.="ORDER BY el.nom ASC, el.prenom ASC, i.annee_scolaire ASC, cl.libelle ASC, ec.date_echeance ASC";
			$echeances=execSql($sql);
			//echo $sql;
			
			$tab_eleves = array();
			
			$total_eleves_general = 0;
			$total_a_payer_general = 0.0;
			$total_reste_a_payer_general = 0.0;
			$total_encaisse_general = 0.0;
			$total_impaye_general = 0.0;
			$elev_id_courant = 0;
			$pourcentage = 0;
			
			for($i=0; $i<$echeances->numRows(); $i++) {
				// Acces s l'enregistrement courant
				$res = $echeances->fetchInto($ligne_echeance, DB_FETCHMODE_DEFAULT, $i);

				if(($ligne_echeance[12] == '') OR ($ligne_echeance[8] < $ligne_echeance[12]))
				{
					// Ajouter un nouvel eleve si le ID est different du precedent
					if($ligne_echeance[0] != $elev_id_courant) {
						$total_eleves_general++;
						$tab_eleves[count($tab_eleves)] = array(
													'elev_id' => $ligne_echeance[0],
													'nom' => $ligne_echeance[1],
													'prenom' => $ligne_echeance[2],
													'echeances' => array(),
													'total_a_payer' => 0.0,
													'reste_a_payer' => 0.0,
													'encaisse' => 0.0,
													'impaye' => 0.0
												);
						$elev_id_courant = $ligne_echeance[0];
					}
					
					// Total a payer pour cet eleve et cette echeance
					$total_a_payer_pour_cet_eleve_echeance = $ligne_echeance[9];
					// Reste a payer pour cet eleve et cette echeance
					$reste_a_payer_pour_cet_eleve_echeance = reglement_reste_a_payer('echeance', $ligne_echeance[7]);
					// Encaisse pour cet eleve et cette echeance
					$encaisse_pour_cet_eleve_echeance = $total_a_payer_pour_cet_eleve_echeance - $reste_a_payer_pour_cet_eleve_echeance;
					// Impaye si il reste quelque chose a payer et que l'echeance est passee
					$impaye_pour_cet_eleve_echeance = 0.0;
					if($reste_a_payer_pour_cet_eleve_echeance > 0.0) {
						if(strtotime($ligne_echeance[8]) < strtotime(date('Y-m-d'))) {
							$impaye_pour_cet_eleve_echeance = $reste_a_payer_pour_cet_eleve_echeance;
						}
					}
					
					$tab_eleves[count($tab_eleves) - 1]['echeances'][count($tab_eleves[count($tab_eleves) - 1]['echeances'])] = array(
										'annee_scolaire' => $ligne_echeance[4],
										'libelle_classe' => $ligne_echeance[6],
										'date_echeance' => $ligne_echeance[8],
										'libelle_type_reglement' => $ligne_echeance[10],
										'total_a_payer_echeance' => $total_a_payer_pour_cet_eleve_echeance,
										'reste_a_payer_echeance' => $reste_a_payer_pour_cet_eleve_echeance,
										'encaisse_echeance' => $encaisse_pour_cet_eleve_echeance,
										'impaye_echeance' => $impaye_pour_cet_eleve_echeance
									);
					
					$tab_eleves[count($tab_eleves) - 1]['total_a_payer'] += $total_a_payer_pour_cet_eleve_echeance;
					$tab_eleves[count($tab_eleves) - 1]['reste_a_payer'] += $reste_a_payer_pour_cet_eleve_echeance;
					$tab_eleves[count($tab_eleves) - 1]['encaisse'] += $encaisse_pour_cet_eleve_echeance;
					$tab_eleves[count($tab_eleves) - 1]['impaye'] += $impaye_pour_cet_eleve_echeance;
					
					$total_a_payer_general += $total_a_payer_pour_cet_eleve_echeance;
					$total_reste_a_payer_general += $reste_a_payer_pour_cet_eleve_echeance;
					$total_encaisse_general += $encaisse_pour_cet_eleve_echeance;
					$total_impaye_general += $impaye_pour_cet_eleve_echeance;
					
					// Rechercher le type de reglement pour cette echeance
					$type_reglement_trouve = false;
					for($k=0; $k<count($tab_types_reglement); $k++) {
						if($tab_types_reglement[$k]['type_reglement_id'] == $ligne_echeance[11]) {
							$type_reglement_trouve = true;
							break;
						}
					}
					if($type_reglement_trouve) {
						$tab_types_reglement[$k]['a_payer'] += $total_a_payer_pour_cet_eleve_echeance;
						$tab_types_reglement[$k]['reste_a_payer'] += $reste_a_payer_pour_cet_eleve_echeance;
						$tab_types_reglement[$k]['encaisse'] += $encaisse_pour_cet_eleve_echeance;
					}
					
					$sql1 ="SELECT groupe_id, echeancier_id, montant ";
					$sql1.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
					$sql1.="WHERE echeancier_id = $ligne_echeance[7] ";
					$sql1.="ORDER BY groupe_id ";
					$groupes=execSql($sql1);
					// echo $sql1;
					$pourcentage = 0;
					if($reste_a_payer_pour_cet_eleve_echeance != 0){
						$pourcentage = $reste_a_payer_pour_cet_eleve_echeance / $total_a_payer_pour_cet_eleve_echeance;
					}
					for($v=0; $v <$groupes->numRows();$v++)
					{
						$resg = $groupes->fetchInto($ligne_groupe, DB_FETCHMODE_DEFAULT, $v);
					
						for($l=0; $l< count($tab_groupe_type);$l++)
						{
							if($tab_groupe_type[$l]['groupe_id'] == $ligne_groupe[0]) {
							
								$tab_groupe_type[$l]['total'] += $ligne_groupe[2];
								
								if($pourcentage != 0)
								{
									$temp = $ligne_groupe[2] * $pourcentage;
									$tab_groupe_type[$l]['reste_a_payer'] += $temp;
									$tab_groupe_type[$l]['encaisse'] += ($ligne_groupe[2] - $temp);
									
									
								}
								else
								{
										$tab_groupe_type[$l]['encaisse'] += $ligne_groupe[2];
									
								}
							}
						}
					}
					
				}
			}
			
			for($j=0;$j<count($tab_types_reglement);$j++)
			{
				$reglement_type_total += $tab_types_reglement[$j]['a_payer'];
				$reglement_type_reste += $tab_types_reglement[$j]['reste_a_payer'];
				$reglement_type_encaisse += $tab_types_reglement[$j]['encaisse'];
			}
			
			
			for($j=0;$j<count($tab_groupe_type);$j++)
			{
				$groupe_type_total += $tab_groupe_type[$j]['total'];
				$groupe_type_reste += $tab_groupe_type[$j]['reste_a_payer'];
				$groupe_type_encaisse += $tab_groupe_type[$j]['encaisse'];
			}
	}
			//echo "<pre>";
			//print_r($tab_eleves);
			//echo "</pre>";
			//echo $sql;
			
			//echo $annee_scolaire;
			//echo "<br>";
			//echo $code_class;


			
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

		<?php
		$largeur_cadre = 600;
		$alignement_cadre = 'center';
		$date_heure_impression = '(' . date('d/m/Y H:i:s') . ')';
		$disabled_cadre = 'disabled';
		$bordure_tableau_impession = '1';
		
		if($mode_affichage != 'impression') {
		?>
		
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		 //Verification droits acces groupe
		validerequete("2");
		?>

		
		<?php
			$largeur_cadre = 468;
			$alignement_cadre = '';
			$date_heure_impression = '';
			$disabled_cadre = '';
			$bordure_tableau_impession = '0';
		?>
			<?php //********** GENERATION DU DEBUT DE LA PAGE ET DES MENUS PRINCIPAUX ********** ?>
		
			<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
		
			<?php include("./librairie_php/lib_defilement.php"); ?>
			</td>
			<td width="472" valign="middle" rowspan="3" align="center">
				<div align='center'>
					<?php top_h(); ?>
					<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>
		<?php
		} else {
		?>
			<style type="text/css" media="print">
			.Cacher {
				display:;
			}
			
			.Montrer {
				display:none;
			}
			
			</style>
		<?php
		}
		?>

		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		<?php
		if($mode_affichage != 'impression') {
		?>
		<div align="<?php echo $alignement_cadre; ?>">
			<!-- TITRE ET CADRE CENTRAL -->
			<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_006; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
					</td>
				</tr>
				<tr id="cadreCentral0">
					<td valign="top" align="center">
		<?php
		} else {
		?>
			<!-- TITRE -->
			<table border="0" cellpadding="3" cellspacing="1" bgcolor="#0B3A0C" align="center" style="background-color:#0B3A0C">
				<tr id="coulBar0">
					<td height="2" align="left">
						<b><font id="menumodule1" ><?php echo LANG_FIN_EDIT_006; ?></font></b>&nbsp;<span style="font-size:10px"><?php echo $date_heure_impression; ?></span>
					</td>
				</tr>
			</table>
		<?php
		}
		?>
					
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						
								<?php //********** AFFICHAGE DES DONNEES ********** ?>
								
								<tr>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td valign="top" align="center">
										<form name="formulaire_criteres" id="formulaire_criteres" action="<?php echo url_script(); ?>" method="post" onSubmit="">
											<input type="hidden" name="operation" id="operation" value="">
	
											<?php
											// Pour la gestion des calendriers
											include_once("./" . $g_chemin_relatif_module . "librairie_php/lib_calendar.php");
		
											//*******************  CRITERES DE RECHERCHE *********************
											
											?>
										
											<fieldset id="fieldset_criteres">
												<legend><?php echo LANG_FIN_GENE_021; ?></legend>
												<table cellpadding="0" cellspacing="2" align="center">

													<tr>
														<td align="right"><?php echo LANG_FIN_EENC_001; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<table cellspacing="0" cellpadding="0" border="0">
																<tr>
																	<td align="left">
																		<?php
																		$valeur = $date_debut;
																		?>
																		<input type="text" name="date_debut" id="date_debut" size="10" maxlength="10" value="<?php echo $valeur; ?>" <?php echo $disabled_cadre; ?>>
																	</td>
																	<td>&nbsp;</td>
																	<?php
																	if($disabled_cadre == '') {
																	?>
																	<td align="left">
																		<?php
																		calendarDim("div_date_debut","document.formulaire_criteres.date_debut",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
																		?>
																	</td>
																	<td>&nbsp;</td>
																	<td valign="middle">
																	<a href="javascript:;"  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg',' <?php echo LANG_FIN_EENC_003; ?>', 'fieldset_criteres');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"></a>
		
																	</td>
																	<?php
																	}
																	?>
																</tr>
															</table>
														</td>
													</tr>

													<tr>
														<td align="right"><?php echo LANG_FIN_EENC_002; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<table cellspacing="0" cellpadding="0" border="0">
																<tr>
																	<td align="left">
																		<?php
																		$valeur = $date_fin;
																		?>
																		<input type="text" name="date_fin" id="date_fin" size="10" maxlength="10" value="<?php echo $valeur; ?>" <?php echo $disabled_cadre; ?>>
																	</td>
																	<td>&nbsp;</td>
																	<?php
																	if($disabled_cadre == '') {
																	?>
																	<td align="left">
																		<?php
																		calendarDim("div_date_fin","document.formulaire_criteres.date_fin",$_SESSION["langue"], "0", "0", 'fieldset_criteres', 'null', 'null');	
																		?>
																	</td>
																	<td>&nbsp;</td>
																	<td valign="middle">
																	<a href="javascript:;"  onMouseOver="AffBulle3('<?php echo LANG_FIN_GENE_002; ?>','./image/commun/info.jpg',' <?php echo LANG_FIN_EENC_004; ?>', 'fieldset_criteres');"  onMouseOut="HideBulle();"><img src="./image/help.gif" border="0" align="middle" style="display: block;"></a>
		
																	</td>
																	<?php
																	}
																	?>
																</tr>
															</table>
														</td>
													</tr>


													
													<tr>
														<td align="right"><?php echo LANG_FIN_CLAS_003; ?>&nbsp;:&nbsp;</td>
														<td align="left">
															<select name="code_class" id="code_class" onChange="" <?php echo $disabled_cadre; ?>>
																<?php
																	$selected = '';
																	if($code_class == '' || $code_class == '0') {
																		$selected = 'selected="selected"';
																	}
																?>
																<option value="0" <?php echo $selected; ?>><?php echo ucfirst(LANG_FIN_GENE_062); ?></option>
																<?php
																// Verifier si on a au moins une classe
																if($classes->numRows() > 0) {
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
																}
																?>
															</select>
														</td>
													</tr>

													<tr>
														<td colspan="2" align="center">&nbsp;</td>
													</tr>
													
													<tr>
														<td colspan="2" align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_020; ?>" onClick="onclick_rechercher();" <?php echo $disabled_cadre; ?>>
														</td>
														<td colspan="0" align="center">
															<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
														</td>
													</tr>
													<tr>
														<td colspan="2" align="center">
															<?php 
															msg_util_afficher();
															msg_util_attente_init(); 
															?>
														</td>
													</tr>
												</table>
												<br>
											</fieldset>
										</form>
									</td>
								</tr>
							</table>
							<?php
							if($mode_affichage != 'impression') {
							?>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td valign="top" align="left" nowrap="nowrap">&nbsp;
										
									</td>
								</tr>
								<tr>
									<td valign="top" align="center">
							<?php
							}
							?>
								
								
											<!--
											<tr bgcolor="#ffffff">
												<td align="right" valign="top"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
											</tr>
											-->
											<?php
											if(true) {
											
												// Verifier si on a au moins une ligne de donnees
												if(count($tab_eleves) > 0) {
											?>
											<p align="left">
												&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="button" class="button" value="<?php echo LANG_FIN_GENE_061; ?>" onClick="onclic_export_excel();">
											</p>
											<?php
													for($i=0; $i<count($tab_eleves); $i++) {
													//for($i=0; $i<5; $i++) {
														// Saut de page a l'impression
														if($i > 0 && $mode_affichage == 'impression') {
											?>
											<!--
											<p align="center" class="Cacher" STYLE="page-break-before: always">
											Pour que le saut de page fonctionne sur IE7, Firefox, ...
											-->
											<div style="page-break-after: always; height:1px;">&nbsp;</div>
											<?php
														}
											?>
											<table cellspacing="0" cellpadding="3" border="0" width="100%">

												<tr>
													<td align="left" nowrap="nowrap" colspan="2"><b><?php echo LANG_FIN_ELEV_002; ?>&nbsp;:&nbsp;<?php echo strtoupper($tab_eleves[$i]['nom']); ?>&nbsp;<?php echo ucfirst($tab_eleves[$i]['prenom']); ?></b></td>
												</tr>
												<tr>
													<td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;</td>
													<td align="left" nowrap="nowrap" width="90%">
													
														<table cellspacing="0" cellpadding="3" border="0" width="100%">
															<tr>
																<td>
													
																	<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" width="70%" align="left">
																		<tr bgcolor="#ffffff">
																			<td align="left" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_GENE_011; ?></b></td>
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_CLAS_003; ?></b></td>
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_EENC_005; ?></b></td>
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_TREG_015; ?></b></td>
			
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_EENC_006; ?></b></td>
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_EENC_007; ?></b></td>
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_EENC_008; ?></b></td>
																			<td align="right" valign="top" nowrap="nowrap"><b><?php echo LANG_FIN_EENC_009; ?></b></td>
																		</tr>
																		<?php
																		for($j=0; $j<count($tab_eleves[$i]['echeances']); $j++) {
																		//for($j=0; $j<5; $j++) {
																		?>
																		<tr bgcolor="#ffffff">
																			<td align="left" nowrap="nowrap" width="15%">
																				<?php echo $tab_eleves[$i]['echeances'][$j]['annee_scolaire']; ?>
																			</td>
																			<td align="right" nowrap="nowrap" width="20%">
																				<?php echo ucfirst($tab_eleves[$i]['echeances'][$j]['libelle_classe']); ?>
																			</td>
																			<td align="left" nowrap="nowrap" width="15%">
																				<?php echo $tab_eleves[$i]['echeances'][$j]['date_echeance']; ?>
																			</td>
																			<td align="right" nowrap="nowrap" width="20%">
																				<?php echo ucfirst($tab_eleves[$i]['echeances'][$j]['libelle_type_reglement']); ?>
																			</td>
																			<td align="right" nowrap="nowrap" width="15%">
																				<?php echo montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['total_a_payer_echeance']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																			</td>
																			<td align="right" nowrap="nowrap" width="15%">
																				<?php echo montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['reste_a_payer_echeance']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																			</td>
																			<td align="right" nowrap="nowrap" width="15%">
																				<?php echo montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['encaisse_echeance']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																			</td>
																			<td align="right" nowrap="nowrap" width="15%">
																				<?php echo montant_depuis_bdd($tab_eleves[$i]['echeances'][$j]['impaye_echeance']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?>
																			</td>
																		</tr>
																		<?php
																		}
																		?>
																	</table>
																</td>
															</tr>
															<tr>
																<td>
																	<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
																		<tr bgcolor="#ffffff">
																			<td align="right"><b><?php echo LANG_FIN_EENC_006; ?></b></td>
																			<td align="right"><b><?php echo LANG_FIN_EENC_007; ?></b></td>
																			<td align="right"><b><?php echo LANG_FIN_EENC_008; ?></b></td>
																			<td align="right"><b><?php echo LANG_FIN_EENC_009; ?></b></td>
																		</tr>
																		<tr bgcolor="#ffffff">
																			<td align="right"><?php echo montant_depuis_bdd($tab_eleves[$i]['total_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
																			<td align="right"><?php echo montant_depuis_bdd($tab_eleves[$i]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
																			<td align="right"><?php echo montant_depuis_bdd($tab_eleves[$i]['encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
																			<td align="right"><?php echo montant_depuis_bdd($tab_eleves[$i]['impaye']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														
													</td>
												</tr>
											</table>
											<?php
													}
												} else {
											?>
											<?php //echo LANG_FIN_EINS_001; ?>
											<?php
												
												}
											} else {
											?>
											<?php //echo LANG_FIN_EINS_001; ?>
											<?php
											}
											?>
											<br>

							<?php
							if($mode_affichage != 'impression') {
							?>
	
									</td>
								</tr>
							</table>
							<?php
							}
							?>
							<?php
							// Saut de page a l'impression
							if($mode_affichage == 'impression') {
							?>
							<!--
							<p align="center" class="Cacher" STYLE="page-break-before: always">
							Pour que le saut de page fonctionne sur IE7, Firefox, ...
							-->
							<div style="page-break-after: always; height:1px;">&nbsp;</div>
							<?php
							
							}
							if(count($tab_eleves) > 0) {
							?>
							<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo LANG_FIN_ESCO_005; ?> : &nbsp;</b></td>
									<td align="right"><?php echo $total_eleves_general; ?></td>
								</tr>
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo LANG_FIN_EENC_006; ?> : &nbsp;</b></td>
									<td align="right"><?php echo montant_depuis_bdd($total_a_payer_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
								</tr>
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo LANG_FIN_EENC_007; ?> : &nbsp;</b></td>
									<td align="right"><?php echo montant_depuis_bdd($total_reste_a_payer_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
								</tr>
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo LANG_FIN_EENC_008; ?> : &nbsp;</b></td>
									<td align="right"><?php echo montant_depuis_bdd($total_encaisse_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
								</tr>
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo LANG_FIN_EENC_009; ?> : &nbsp;</b></td>
									<td align="right"><?php echo montant_depuis_bdd($total_impaye_general); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
								</tr>
							</table>
							<br>
							
							<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
								<tr bgcolor="#ffffff">
									<td align="right">&nbsp;</td>
									<td align="right"><b><?php echo LANG_FIN_EENC_006; ?></b></td>
									<td align="right"><b><?php echo LANG_FIN_EENC_007; ?></b></td>
									<td align="right"><b><?php echo LANG_FIN_EENC_008; ?></b></td>
								</tr>
								<?php
								for($k=0; $k<count($tab_types_reglement); $k++) {
								?>
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo $tab_types_reglement[$k]['libelle']; ?></b></td>
									<td align="right"><?php echo montant_depuis_bdd($tab_types_reglement[$k]['a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									<td align="right"><?php echo montant_depuis_bdd($tab_types_reglement[$k]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									<td align="right"><?php echo montant_depuis_bdd($tab_types_reglement[$k]['encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
								</tr>
								<?php
								}
								?>
								<tr bgcolor="#ffffff">
									<td align="right">&nbsp;</td>
									<td align="right"><b><?php echo montant_depuis_bdd($reglement_type_total); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
									<td align="right"><b><?php echo montant_depuis_bdd($reglement_type_reste); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
									<td align="right"><b><?php echo montant_depuis_bdd($reglement_type_encaisse); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
								</tr>
							</table>
							<br>
							<table cellspacing="1" cellpadding="3" border="<?php echo $bordure_tableau_impession; ?>" bgcolor="#cccccc" align="center">
								<tr bgcolor="#ffffff">
									<td align="right">&nbsp;</td>
									<td align="right"><b><?php echo LANG_FIN_EENC_010; ?></b></td>
									<td align="right"><b><?php echo LANG_FIN_EENC_007; ?></b></td>
									<td align="right"><b><?php echo LANG_FIN_EENC_008; ?></b></td>
								</tr>
								<?php
								for($k=0; $k<count($tab_groupe_type); $k++) {
								?>
								<tr bgcolor="#ffffff">
									<td align="right"><b><?php echo $tab_groupe_type[$k]['libelle']; ?></b></td>
									<td align="right"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['total']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									<td align="right"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
									<td align="right"><?php echo montant_depuis_bdd($tab_groupe_type[$k]['encaisse']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
								</tr>
								<?php
								}
								?>
								<tr bgcolor="#ffffff">
									<td align="right">&nbsp;</td>
									<td align="right"><b><?php echo montant_depuis_bdd($groupe_type_total); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
									<td align="right"><b><?php echo montant_depuis_bdd($groupe_type_reste); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
									<td align="right"><b><?php echo montant_depuis_bdd($groupe_type_encaisse); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
								</tr>
							</table>
	
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
			
								<?php //********** MESSAGES UTILISATEUR ********** ?>
								
								<tr>
									<td align="center">&nbsp;</td>
								</tr>
								<tr>
									<td align="center"></td>
								</tr>
					
					
								<?php //********** BOUTONS ********** ?>
								
								<tr>
									<td align="center">
										<table border="0" align="center" cellpadding="4" cellspacing="0">
											<?php
											if($mode_affichage != 'impression') {
											?>									
											<tr>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_060?>","onclick_imprimer()");</script>
												</td>
												<td align="center">&nbsp;</td>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
												</td>
											</tr>
											<?php
											} else {
											?>
											<tr>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_060?>","onclick_ouvrir_impession()");</script>
												</td>
												<td align="center">&nbsp;</td>
												<td align="center">
													<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_041?>","onclick_fermer()");</script>
												</td>
											</tr>
											<?php
											}
											?>											
										</table>
									</td>
								</tr>
									
									
							</table>
						<?php }//********** VALIDATION FORMULAIRES ********** ?>
				
				
						<?php //********** GESTION NAVIGATION ********** ?>
						
						<script language="javascript">
							var fenetre = null;
							var liste_fenetre = new Array();
							
							function onclick_annuler() {
								msg_util_attente_montrer(true);
								document.getElementById('formulaire_annuler').submit();
							}
							function onchange_code_class() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "reload_code_class";
								document.formulaire_criteres.submit();
							}
							function onchange_annee_scolaire() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "reload_annee_scolaire";
								document.formulaire_criteres.submit();
							}
							
							function onclick_rechercher() {
								msg_util_attente_montrer(true);
								document.formulaire_criteres.operation.value = "rechercher";
								document.formulaire_criteres.submit();
							}
	
		
							function onclick_imprimer() {										
									try {
										for(i=0; i<liste_fenetre.length; i++) {
											liste_fenetre[i].close();
										}
										
									}
									catch(e) {
									}								
									liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/editions_encaisses_impayes.php?mode_affichage=impression&operation=rechercher&code_class=<?php echo urlencode($code_class); ?>&date_debut=<?php echo urlencode($date_debut); ?>&date_fin=<?php echo urlencode($date_fin); ?>&ordre_tri=<?php echo urlencode($ordre_tri); ?>','fenetre_editer_' + liste_fenetre.length,'width=600,height=600,resizable=yes,scrollbars=yes,toolbar=no, menubar=yes');
									liste_fenetre[liste_fenetre.length].focus();						
							}
							
							function onclic_export_excel() {
							document.getElementById('formulaire_export_excel').submit();
							}
							
							function onclick_fermer() {
								window.close();
							}
							
							function onclick_ouvrir_impession() {
								window.print();
							}
							
						</script>
						<form name="formulaire_modif" id="formulaire_modif" action="<?php echo $g_chemin_relatif_module; ?>type_frais_modif.php" method="post">
							<input type="hidden" name="type_frais_id" id="type_frais_id" value="0">
						</form>
						<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>editions.php" method="post">
						</form>
						<form name="formulaire_export_excel" id="formulaire_export_excel" action="<?php echo $g_chemin_relatif_module; ?>editions_encaisses_impayes_excel.php" method="post" target="">
							<input type="hidden" name="date_debut" id="date_debut" value="<?php echo $date_debut?>">
							<input type="hidden" name="date_fin" id="date_fin" value="<?php echo $date_fin?>">
							<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class?>">
							<input type="hidden" name="ordre_tri" id="ordre_tri" value="">
						</form>
		<?php
		if($mode_affichage != 'impression') {
		?>
						
					</td>
				</tr>
			</table>
		</div>
		<?php
		}
		?>
		<?php
			if($mode_affichage != 'impression') {
		?>
		
		<?php //********** GENERATION DES MENUS ADMINISTRATEUR ********** ?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></script>
		

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