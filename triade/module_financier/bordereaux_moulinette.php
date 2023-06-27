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
	$operation = lire_parametre('operation', 'rechercher', 'POST');
	$date_remise = lire_parametre('date_remise', '', 'POST');
	$numero_bordereau = trim(lire_parametre('numero_bordereau', '', 'POST'));
	//***************************************************************************

	
	//*************** TRAITER L'OPERATION DEMANDEE ******************************
	$tab_echeances = array();
	if($operation == 'rechercher') {

		$sql  = "SELECT el.elev_id, el.nom, el.prenom, ec.echeancier_id, ec.date_echeance, ec.montant, ec.numero_rib, cl.code_class, cl.libelle, i.annee_scolaire, r.type_reglement_id ";
		$sql.=  "FROM ".FIN_TAB_REGLEMENT." r "; 
		$sql .= "INNER JOIN ".FIN_TAB_ECHEANCIER." ec ON ec.echeancier_id = r.echeancier_id ";
		$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON ec.inscription_id = i.inscription_id ";
		$sql .= "INNER JOIN ".FIN_TAB_ELEVES." el ON i.elev_id = el.elev_id ";
		$sql .= "INNER JOIN ".FIN_TAB_CLASSES." cl ON i.code_class = cl.code_class ";
		//$sql .= "WHERE ec.date_echeance <= '" . date_vers_bdd($date_remise) . "' ";	
		// $sql .= "AND numero_bordereau = '' ";
		// $sql .= "AND ec.type <> 2 ";
		$sql .= "WHERE numero_bordereau = '' ";
		$sql .= "ORDER BY el.nom, el.prenom, ec.date_echeance ASC";
		//echo $sql;
		$echeances = execSql($sql);
		
		if($echeances->numRows() > 0) {
			$echeancier_id_courant = 0;
			for($i=0; $i<$echeances->numRows(); $i++) {
			// Recuperer la ligne du resultat
			$res = $echeances->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);	
				if($ligne[10] == $g_tab_type_reglement_id['cheque'] OR $ligne[10] == $g_tab_type_reglement_id['espece'])
				{
					if($echeancier_id_courant != $ligne[3]) {
					
						$reste_a_payer = reglement_reste_a_payer('echeance', $ligne[3]);
					
						$tab_echeances[count($tab_echeances)] = array(
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
															
						$echeancier_id_courant = $ligne[3];								
					}	
				}
			}
			/*
			echo "<pre>";
			print_r($tab_echeances);
			echo "</pre>";
			*/
			
			// Cumuler les echeances par eleve
			if(count($tab_echeances) > 0) {
				$tab_echeances_tmp = $tab_echeances;
				$tab_echeances = array();
				$elev_id_courant = 0;
				for($i=0; $i<count($tab_echeances_tmp); $i++) {
				
					
						// Rechercher les reglements de l'echeance
						$tab_reglements = array();
						$sql  = "SELECT r.reglement_id, r.date_reglement, r.montant, r.realise, r.numero_cheque, r.numero_bordereau, tr.type_reglement_id, tr.libelle ";
						$sql .= "FROM ".FIN_TAB_REGLEMENT." r ";
						$sql .= "LEFT JOIN ".FIN_TAB_TYPE_REGLEMENT." tr ON r.type_reglement_id = tr.type_reglement_id ";
						$sql .= "WHERE r.echeancier_id = " . $tab_echeances_tmp[$i]["echeancier_echeancier_id"];
						//echo $sql;
						$res_reglements=execSql($sql);
						$nombre_reglements = $res_reglements->numRows();
						for($k=0; $k<$res_reglements->numRows(); $k++) {
							$res = $res_reglements->fetchInto($ligne_reglement, DB_FETCHMODE_DEFAULT, $k);
							$tab_reglements[count($tab_reglements)] = array(
									"reglement_reglement_id" => $ligne_reglement[0],
									"reglement_date_reglement" => $ligne_reglement[1],
									"reglement_montant" => $ligne_reglement[2],
									"reglement_realise" => $ligne_reglement[3],
									"reglement_numero_cheque" => $ligne_reglement[4],
									"reglement_numero_bordereau" => $ligne_reglement[5],
									"type_reglement_type_reglement_id" => $ligne_reglement[6],
									"type_reglement_libelle" => $ligne_reglement[7]
							);
							
							
							// Mettre a jour le numero de bordereau dans les reglements
								$sql  = "UPDATE ".FIN_TAB_REGLEMENT." ";
								$sql .= "SET numero_bordereau = 0 ";
								
								$sql .= "WHERE reglement_id = $ligne_reglement[0] ";
								$res=execSql($sql);
							//print_r($tab_echeances);
							
							
						}
					
						if($elev_id_courant != $tab_echeances_tmp[$i]["eleves_elev_id"]) {
							// => Ajouter un nouvel enregistrement avec une nouvelle echeance
							
							$echeancier = array();
							$echeancier[0] = array(
									"echeancier_echeancier_id" => $tab_echeances_tmp[$i]["echeancier_echeancier_id"],
									"echeancier_date_echeance" => $tab_echeances_tmp[$i]["echeancier_date_echeance"],
									"echeancier_montant" => $tab_echeances_tmp[$i]["echeancier_montant"],
									"reste_a_payer" => $tab_echeances_tmp[$i]["reste_a_payer"],
									"nombre_reglements" => $nombre_reglements,
									"reglements" => $tab_reglements
									);
							$tab_echeances[count($tab_echeances)] = array(
									"eleves_elev_id" => $tab_echeances_tmp[$i]["eleves_elev_id"],
									"eleves_nom" => $tab_echeances_tmp[$i]["eleves_nom"],
									"eleves_prenom" => $tab_echeances_tmp[$i]["eleves_prenom"],
									"echeancier" => $echeancier,
									"classes_code_class" => $tab_echeances_tmp[$i]["classes_code_class"],
									"classes_libelle" => $tab_echeances_tmp[$i]["classes_libelle"],
									"inscription_annee_scolaire" => $tab_echeances_tmp[$i]["inscription_annee_scolaire"],
									"total" => $tab_echeances_tmp[$i]["reste_a_payer"]
									);
							$elev_id_courant = $tab_echeances_tmp[$i]["eleves_elev_id"];
						} else {
							// => Ajouter une nouvelle echeance dans l'enregistrement precedent
							
							// Recuperer les donnees de l'enregistrement precedent
							$echeancier = $tab_echeances[count($tab_echeances)-1]["echeancier"];
							$total = $tab_echeances[count($tab_echeances)-1]["total"];
							
							// Ajouter
							$echeancier[count($echeancier)] = array(
									"echeancier_echeancier_id" => $tab_echeances_tmp[$i]["echeancier_echeancier_id"],
									"echeancier_date_echeance" => $tab_echeances_tmp[$i]["echeancier_date_echeance"],
									"echeancier_montant" => $tab_echeances_tmp[$i]["echeancier_montant"],
									"reste_a_payer" => $tab_echeances_tmp[$i]["reste_a_payer"],
									"nombre_reglements" => $nombre_reglements,
									"reglements" => $tab_reglements
									);
							$total += $tab_echeances_tmp[$i]["reste_a_payer"];
							
							// Remettre les infos dans l'enregistrement precedent
							$tab_echeances[count($tab_echeances)-1]["echeancier"] = $echeancier;
							$tab_echeances[count($tab_echeances)-1]["total"] = $total;
							
						}
					
				}
			}
		}
	}
	
	
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



// reglement_afficher_checkbox() : Permet de definir si la checkbox doit etre affichee pour un reglement donne
//		entree :
//			- $tab_reglement (array) : donnees du reglement 
//			- $type_reglement_id (integer) : id du type de reglement actuellement recherche 
//		sortie :
//			- (string) : 
//               ''  : pas d'erreur, la checkbox peut etre affichee 
//               '1' : le reglement n'est pas realise
//               '2' : ce n'est pas le type de reglement recherche
//               '3' : le numero de bordereau n'est pas vide (le reglement fait partie d'un autre bordereau)
function reglement_afficher_checkbox($tab_reglement, $type_reglement_id) {
	$resultat = '';
	if($tab_reglement['reglement_realise'] == 1) {
		if($tab_reglement['type_reglement_type_reglement_id'] == $type_reglement_id) {
			if(trim($tab_reglement['reglement_numero_bordereau']) == '') {
				$afficher = true;
			} else {
				$resultat = '3';
			}
		} else {
			$resultat = '2';
		}
	} else {
		$resultat = '1';
	}
	return($resultat);
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
					<b><font id="menumodule1" ><?php echo LANG_FIN_PAIE_005; ?></font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<input type="hidden" name="operation" id="operation" value="">
						
						<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class; ?>">

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
									
									
									<?php
									//*******************  LISTE DES ECHEANCES *********************
									
									if($operation == 'rechercher') {
									?>
									<br>
									<fieldset id="fieldset_echeances" style="z-index:4; margin-left:15px; margin-right:15px;">
										<legend><?php echo LANG_FIN_ECHE_003; ?></legend>

										<table cellspacing="0" cellpadding="0" border="0" width="100%">
											<tr>
												<td align="center">
													<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="100%">
														<tr bgcolor="#ffffff">
															<td align="right" valign="top" nowrap="nowrap" rowspan="2">#</td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="2"><b><?php echo ucfirst(LANG_FIN_CLAS_003); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="2"><b><?php echo ucfirst(LANG_FIN_GENE_011); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="2"><b><?php echo ucfirst(LANG_FIN_ELEV_004); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="2"><b><?php echo ucfirst(LANG_FIN_ELEV_005); ?></b></td>
															<td align="right" valign="top" nowrap="nowrap" rowspan="2"><b><?php echo ucfirst(LANG_FIN_GENE_030); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="2"><b><?php echo ucfirst(LANG_FIN_GENE_034); ?></b></td>
															<td align="center" valign="top" nowrap="nowrap" colspan="7"><b><?php echo ucfirst(LANG_FIN_REGL_001); ?></b></td>
														</tr>
														<tr bgcolor="#ffffff">
															<td align="left" valign="top" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_055); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_030); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_013); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_056); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_REGL_020); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GBOR_003); ?></b></td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
														</tr>
														<input type="hidden" name="eleves_total" id="eleves_total" value="<?php echo count($tab_echeances); ?>">
													<?php
													$total_global = 0.0;
													$total_reglement_valides = 0.0;
													$reglements_nombre_checkbox = 0;
													if(count($tab_echeances) > 0) {
														for($i=0; $i<count($tab_echeances); $i++) {
															//if($i > 0) {
															//	break;
															//}
															//$lignes_pour_eleve = count($tab_echeances[$i]['echeancier']);
															$lignes_pour_eleve = 0;
															for($j=0; $j<count($tab_echeances[$i]['echeancier']); $j++) {
																$lignes_pour_eleve++;
																if(count($tab_echeances[$i]['echeancier'][$j]['reglements']) > 1) {
																	$lignes_pour_eleve += (count($tab_echeances[$i]['echeancier'][$j]['reglements']) - 1);
																}
															}
															 
															// Recuperer la liste des RIB pour l'eleve courant
															$tab_rib = liste_rib($tab_echeances[$i]['eleves_elev_id']);
															//print_r($tab_rib);
															
															$total_global += $tab_echeances[$i]['echeancier'][0]['reste_a_payer'];
		
													?>
														<?php // Afficher la premiere ligne ?>
														<input type="hidden" name="elev_id_<?php echo ($i+1); ?>" id="elev_id_<?php echo ($i+1); ?>" value="<?php echo $tab_echeances[$i]['eleves_elev_id']; ?>">
														<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
															<td valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ($i+1); ?></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ucfirst($tab_echeances[$i]['classes_libelle']); ?></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ucfirst($tab_echeances[$i]['inscription_annee_scolaire']); ?></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo ucfirst($tab_echeances[$i]['eleves_prenom']); ?></td>
															<td align="left" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_eleve; ?>"><?php echo strtoupper($tab_echeances[$i]['eleves_nom']); ?></td>
															
															
															
															<?php
															$lignes_pour_echeance = count($tab_echeances[$i]['echeancier'][0]['reglements']);
															?>
															<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_echeance; ?>"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][0]['echeancier_date_echeance']); ?></td>
															<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_echeance; ?>"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][0]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_echeance; ?>">
																<input type="button" class="button" value="<?php echo LANG_FIN_REGL_001 . ' (' . $tab_echeances[$i]['echeancier'][0]['nombre_reglements'] . ')'; ?>" onClick="onclick_reglement_editer(<?php echo $tab_echeances[$i]['echeancier'][0]['echeancier_echeancier_id']; ?>, 'editer');" >													
															</td>
															
															
															<?php
															// Afficher le premier reglement de la premiere echeance, si il y en a un
															if(count($tab_echeances[$i]['echeancier'][0]['reglements']) >  0) {
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][0]['reglements'][0]['reglement_date_reglement']); ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][0]['reglements'][0]['reglement_montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $tab_echeances[$i]['echeancier'][0]['reglements'][0]['type_reglement_libelle']; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][0]['reglements'][0]['reglement_numero_cheque'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][0]['reglements'][0]['reglement_numero_bordereau'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															
															
															<?php
															// *************** AFFICHAGE CHECKBOX PREMIER REGLEMENT DE LA PREMIERE ECHEANCE *************
																$afficher_checkbox = reglement_afficher_checkbox($tab_echeances[$i]['echeancier'][0]['reglements'][0], $type_reglement_id);
																if($afficher_checkbox == '') {
																	$reglements_nombre_checkbox++;
																	$total_reglement_valides += $tab_echeances[$i]['echeancier'][0]['reglements'][0]['reglement_montant'];
															?>
															<td align="center" valign="top" nowrap="nowrap">
																<input type="checkbox" name="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" id="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" value="<?php echo $tab_echeances[$i]['echeancier'][0]['reglements'][0]['reglement_reglement_id']; ?>" checked="checked">
															</td>
															<?php
																} else {
															?>
															<td align="center" valign="top" nowrap="nowrap"><sup>(<?php echo $afficher_checkbox; ?>)</sup></td>
															<?php
																}
															?>
															
															
															<?php
															} else {
															?>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<?php
															}
															?>
														</tr>	
														
														<?php
															for($k=1; $k<count($tab_echeances[$i]['echeancier'][0]['reglements']); $k++) {
														?>
														<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
		
															<?php
																$lignes_pour_echeance = count($tab_echeances[$i]['echeancier'][$j]['reglements']);
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][0]['reglements'][$k]['reglement_date_reglement']); ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][0]['reglements'][$k]['reglement_montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $tab_echeances[$i]['echeancier'][0]['reglements'][$k]['type_reglement_libelle']; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][0]['reglements'][$k]['reglement_numero_cheque'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][0]['reglements'][$k]['reglement_numero_bordereau'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															
															<?php
															// *************** AFFICHAGE CHECKBOX AUTRES REGLEMENTS DE LA PREMIERE ECHEANCE *************
																$afficher_checkbox = reglement_afficher_checkbox($tab_echeances[$i]['echeancier'][0]['reglements'][$k], $type_reglement_id);
																if($afficher_checkbox == '') {
																	$reglements_nombre_checkbox++;
																	$total_reglement_valides += $tab_echeances[$i]['echeancier'][0]['reglements'][$k]['reglement_montant'];
															?>
															<td align="center" valign="top" nowrap="nowrap">
																<input type="checkbox" name="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" id="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" value="<?php echo $tab_echeances[$i]['echeancier'][0]['reglements'][$k]['reglement_reglement_id']; ?>" checked="checked">
															</td>
															<?php
																} else {
															?>
															<td align="center" valign="top" nowrap="nowrap"><sup>(<?php echo $afficher_checkbox; ?>)</sup></td>
															<?php
																}
															?>
															
															
		
														</tr>
														<?php
															}
														?>		
														
																
														<?php // Afficher les autres lignes d'echeance ?>
														
														<?php
															for($j=1; $j<count($tab_echeances[$i]['echeancier']); $j++) {
																$total_global += $tab_echeances[$i]['echeancier'][$j]['reste_a_payer'];
														?>
														<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
														<?php
																$lignes_pour_echeance = count($tab_echeances[$i]['echeancier'][$j]['reglements']);
		
														?>
															<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_echeance; ?>"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['echeancier_date_echeance']); ?></td>
															<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_echeance; ?>"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['reste_a_payer']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="right" valign="top" nowrap="nowrap" rowspan="<?php echo $lignes_pour_echeance; ?>">
																<input type="button" class="button" value="<?php echo LANG_FIN_REGL_001 . ' (' . $tab_echeances[$i]['echeancier'][$j]['nombre_reglements'] . ')'; ?>" onClick="onclick_reglement_editer(<?php echo $tab_echeances[$i]['echeancier'][$j]['echeancier_echeancier_id']; ?>, 'editer');" >													
															</td>
															
															
															
															<?php
																// Afficher le premier reglement de l'echeance, si il y en a un
																if(count($tab_echeances[$i]['echeancier'][$j]['reglements']) >  0) {
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['reglements'][0]['reglement_date_reglement']); ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['reglements'][0]['reglement_montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $tab_echeances[$i]['echeancier'][$j]['reglements'][0]['type_reglement_libelle']; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][$j]['reglements'][0]['reglement_numero_cheque'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][$j]['reglements'][0]['reglement_numero_bordereau'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															
															<?php
															// *************** AFFICHAGE CHECKBOX PREMIER REGLEMENT DES AUTRES ECHEANCE *************
																$afficher_checkbox = reglement_afficher_checkbox($tab_echeances[$i]['echeancier'][$j]['reglements'][0], $type_reglement_id);
																if($afficher_checkbox == '') {
																	$reglements_nombre_checkbox++;
																	$total_reglement_valides += $tab_echeances[$i]['echeancier'][$j]['reglements'][0]['reglement_montant'];
															?>
															<td align="center" valign="top" nowrap="nowrap">
																<input type="checkbox" name="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" id="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" value="<?php echo $tab_echeances[$i]['echeancier'][$j]['reglements'][0]['reglement_reglement_id']; ?>" checked="checked">
															</td>
															<?php
																} else {
															?>
															<td align="center" valign="top" nowrap="nowrap"><sup>(<?php echo $afficher_checkbox; ?>)</sup></td>
															<?php
																}
															?>
		
															<?php
																} else {
															?>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<td align="left" valign="top" nowrap="nowrap">&nbsp;</td>
															<?php
																}
															?>
															
														</tr>
														
														<?php
																for($k=1; $k<count($tab_echeances[$i]['echeancier'][$j]['reglements']); $k++) {
														?>
														<tr class='tabnormal2' onMouseOver="this.className='tabover';" onMouseOut="this.className='tabnormal2';">
		
															<td align="left" valign="top" nowrap="nowrap"><?php echo date_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['reglement_date_reglement']); ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo montant_depuis_bdd($tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['reglement_montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['type_reglement_libelle']; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['reglement_numero_cheque'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
															<?php
																$valeur = $tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['reglement_numero_bordereau'];
																if(trim($valeur) == '') {
																	$valeur = '&nbsp;';
																}
															?>
															<td align="left" valign="top" nowrap="nowrap"><?php echo $valeur; ?></td>
		
															<?php
															// *************** AFFICHAGE CHECKBOX AUTRES REGLEMENTS DES AUTRES ECHEANCE *************
																$afficher_checkbox = reglement_afficher_checkbox($tab_echeances[$i]['echeancier'][$j]['reglements'][$k], $type_reglement_id);
																if($afficher_checkbox == '') {
																	$reglements_nombre_checkbox++;
																	$total_reglement_valides += $tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['reglement_montant'];
															?>
															<td align="center" valign="top" nowrap="nowrap">
																<input type="checkbox" name="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" id="reglement_checkbox_<?php echo $reglements_nombre_checkbox; ?>" value="<?php echo $tab_echeances[$i]['echeancier'][$j]['reglements'][$k]['reglement_reglement_id']; ?>" checked="checked">
															</td>
															<?php
																} else {
															?>
															<td align="center" valign="top" nowrap="nowrap"><sup>(<?php echo $afficher_checkbox; ?>)</sup></td>
															<?php
																}
															?>
		
														</tr>
														<?php
																}
														?>				
														
														
														<?php
															}
														?>
													<?php
														}
													?>
													<tr class="tabnormal2">
														<td colspan="5">&nbsp;</td>
														<td align="right" nowrap="nowrap"><b><?php echo ucfirst(LANG_FIN_GENE_024); ?></b></td>
														<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_global); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_reglement_valides); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
													<?php
													} else {
													?>
													<tr class="tabnormal2" onMouseOut="this.className='tabnormal2'" onMouseOver="this.className='tabover'">
														<td align="left" colspan="14"><?php echo LANG_FIN_GBOR_012; ?></td>
													</tr>
													<?php
													}
													?>
													</table>
												</td>
											</tr>
											<tr>
												<td align="left">
													
														<?php
														// **************** LEGENDE *************************
														?>
														<table border="0" cellpadding="0" cellspacing="0" align="left">
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															<tr>
																<td><small><sup>(1)</sup>&nbsp;:&nbsp;</small></td>
																<td><small><?php echo LANG_FIN_GBOR_005; ?></small></td>
															</tr>
															<tr>
																<td><small><sup>(2)</sup>&nbsp;:&nbsp;</small></td>
																<td><small><?php echo LANG_FIN_GBOR_006; ?></small></td>
															</tr>
															<tr>
																<td><small><sup>(3)</sup>&nbsp;:&nbsp;</small></td>
																<td><small><?php echo LANG_FIN_GBOR_007; ?></small></td>
															</tr>
														</table>

												</td>
											</tr>
											<tr>
												<td align="center">										
													<?php
													if($reglements_nombre_checkbox > 0) {
													?>
													<table border="0" cellpadding="0" cellspacing="0" align="center">
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2" align="center">
																<input type="button" class="button" onClick="onclick_generer_bordereau();" value='<?php echo LANG_FIN_GBOR_004; ?>' >
															</td>
														</tr>
													</table>
													<?php
													}
													?>
												</td>
											</tr>
										</table>

									</fieldset>
									<?php
									}
									?>

									<input type="hidden" name="reglements_nombre_checkbox" id="reglements_nombre_checkbox" value="<?php echo $reglements_nombre_checkbox; ?>">
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
							<?php
							}
							?>	
								
								
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
					</form>
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						var liste_fenetre = new Array();
						
						// Generation de la liste des numeros de bordereau deja utilises
						var tab_numero_bordereau = new Array();
						<?php
						for($i=0; $i<$res_numero_bordereau->numRows(); $i++) {
							$res = $res_numero_bordereau->fetchInto($ligne_numero_bordereau, DB_FETCHMODE_DEFAULT, $i);
						?>
							tab_numero_bordereau[<?php echo $i; ?>] = '<?php echo $ligne_numero_bordereau[0]; ?>';
						<?php
						}
						?>

					
						function onclick_annuler() {
							msg_util_attente_montrer(true);
							document.getElementById('formulaire_annuler').submit();
						}

						function onchange_type_reglement_id() {
							onclick_rechercher();
						}
						
						// Validation donnees avant recherche des echeances
						function onclick_rechercher() {
							var message_erreur = '';
							var separateur = '';
							var valide = true;
							var messsage;
							var obj;
							var i=0;

							obj = document.getElementById('date_remise');
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
								document.formulaire.but_actualiser.click();

							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}


						}
						
						// Pour ouvrir le popup d'edition des reglements d'une echeance
						function onclick_reglement_editer(echeancier_id, mode) {
							try {
								for(i=0; i<liste_fenetre.length; i++) {
									liste_fenetre[i].close();
								}
								
							}
							catch(e) {
							}
							liste_fenetre[liste_fenetre.length] = open('<?php echo site_url_racine(FIN_REP_MODULE); ?>module_financier/reglement_editer.php?echeancier_id=' + echeancier_id + '&mode=' + mode,'fenetre_editer_' + liste_fenetre.length,'width=850,height=600,resizable=yes,scrollbars=no');
							liste_fenetre[liste_fenetre.length].focus();
						}						
						
						
						// Quand l'utilisateur clique sur le bouton pour generer le bordereau
						function onclick_generer_bordereau() {
							var message_erreur = '';
							var separateur = '';
							var valide = true;
							var messsage;
							var obj;
							var i=0;
							var liste_reglement_id = '';
							var separateur_liste = '';;
							var au_moins_un_selectionne = false;
							var existe = false;
							
							// Recueperer le nombre totoal d'eleves affiches
							var nombre_checkbox = document.getElementById('reglements_nombre_checkbox').value;
							
							
							for(i=1;i<=nombre_checkbox;i++) {
								//rib_existe = document.getElementById('rib_existe_' + i).value;
								obj = document.getElementById('reglement_checkbox_' + i);
								if(obj.checked) {
									liste_reglement_id += separateur_liste + obj.value;
									separateur_liste = ",";
									au_moins_un_selectionne = true;
								}
							}

							// Verifier que au moins un reglement a ete selectionne
							if(!au_moins_un_selectionne) {
								messsage = "<?php echo LANG_FIN_GBOR_009; ?>";
								message_erreur += separateur + "     - " + messsage;
								separateur = "\n";
								valide = false;
							}
							
							
							
							
							if(valide) {
								if(confirm("<?php echo LANG_FIN_GBOR_011; ?>")) {
								
									// Recuperer la date de remise et la passer au formulaire de generation du bordereau
									document.formulaire_generer.date_remise.value = document.formulaire.date_remise.value;

									// Recuperer la date de remise et la passer au formulaire de generation du bordereau
									document.formulaire_generer.type_reglement_id.value = document.formulaire.type_reglement_id.options[document.formulaire.type_reglement_id.selectedIndex].value;
									// Recuperer la liste des id de reglements et la passer au formulaire de generation du bordereau
									document.formulaire_generer.liste_reglement_id.value = liste_reglement_id;

									document.formulaire_generer.submit();
									
									//alert('En cours de développement... (génération pour : ' + document.formulaire_generer.liste_reglement_id.value + ')');
								}
							} else {
								alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
							}


						}
						
						// Actualiser le formulaire
						function actualiser() {
							onclick_rechercher();
						}
					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>paiements.php" method="post">
					</form>
					<form name="formulaire_generer" id="formulaire_generer" action="<?php echo $g_chemin_relatif_module; ?>bordereaux_generer.php" method="post" target="_blank">
						<input type="hidden" name="date_remise" id="date_remise" value="">
						<input type="hidden" name="type_reglement_id" id="type_reglement_id" value="">
						<input type="hidden" name="numero_bordereau" id="numero_bordereau" value="">
						<input type="hidden" name="liste_reglement_id" id="liste_reglement_id" value="">
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