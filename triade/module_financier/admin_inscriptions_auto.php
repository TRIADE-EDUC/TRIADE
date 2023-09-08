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
	//***************************************************************************

	// En mode debug (affichage seulement) ou non (affichage + insertion des enregistrements)
	$debug = true;
	
	// Liste des classes contenant les eleves a inscrire avec :
	//     - code_class : le code de la classe concernee
	//     - bareme_id : le id du bareme a utiliser pour cette classe (l'annee scolaire est dans le bareme)
	//     - type_echeancier_id : le id du type d'echeancier a utiliser
	//     - date_depart (yyyy-mm-jj) : la date de depart de l'echeancier
	$classes = array();
	$classes[count($classes)] = array(
					'code_class' => 35,
					'bareme_id' => 1 ,
					'type_echeancier_id' => 8 ,
					'date_debut' => '01/09/2009'
						);			

	
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
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" >Inscriptions automatiques</font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="left">
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">

						<input type="hidden" name="operation" id="operation" value="">
						
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							
						<?php
						for($classe=0;$classe<count($classes);$classe++) {
						?>
							<tr>
								<td align="left" colspan="2">Traitement classe n°<?php echo $classe + 1; ?></td>
							</tr>
						<?php
							// Rechercher le libelle de la classe
							$sql ="SELECT libelle ";
							$sql.="FROM ".FIN_TAB_CLASSES." ";
							$sql.="WHERE code_class = " . $classes[$classe]['code_class'];
							$res_classe = execSql($sql);
							if($res_classe->numRows() > 0) {
								$ligne_classe = &$res_classe->fetchRow();
								
								$date_debut = $classes[$classe]['date_debut'];
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Libellé&nbsp;:&nbsp;<?php echo $ligne_classe[0]; ?></td>
							</tr>
							<?php
							
								// Recherche du bareme
								$sql ="SELECT libelle, annee_scolaire ";
								$sql.="FROM ".FIN_TAB_BAREME." ";
								$sql.="WHERE bareme_id = " . $classes[$classe]['bareme_id'];
								$res_bareme = execSql($sql);
								if($res_bareme->numRows() > 0) {
									$ligne_bareme = &$res_bareme->fetchRow();
									$annee_scolaire = $ligne_bareme[1];
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Bareme&nbsp;:&nbsp;<?php echo $ligne_bareme[0]; ?></td>
							</tr>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Liste des frais&nbsp;:&nbsp;(seuls les non-optionnels seront inclus dans le calcul des échéances)</td>
							</tr>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="75%" align="center">
										<tr bgcolor="#ffffff">
											<td align="right" nowrap="nowrap"><b>ID</b></td>
											<td align="left" nowrap="nowrap"><b>Frais</b></td>
											<td align="right" nowrap="nowrap"><b>Montant</b></td>
											<td align="center" nowrap="nowrap"><b>Optionnel ?</b></td>
											<td align="center" nowrap="nowrap"><b>Lissé</b></td>
										</tr>
							<?php
									$total_frais_lisses = 0.0;
									$total_frais_non_lisses = 0.0;
									$echeancier = array();
									$frais = array();
									// Rechercher les frais du bareme selectionne
									$sql  ="SELECT fb.frais_bareme_id, fb.bareme_id, fb.type_frais_id, fb.montant, fb.optionnel, tf.libelle, fb.lisse ";
									$sql .= "FROM ".FIN_TAB_FRAIS_BAREME." fb ";
									$sql .= "INNER JOIN ".FIN_TAB_TYPE_FRAIS." tf ON fb.type_frais_id = tf.type_frais_id ";
									$sql .= "WHERE fb.bareme_id = " . $classes[$classe]['bareme_id'] . " ";
									$sql .= "ORDER BY tf.libelle ASC";
									//echo $sql;
									$res_frais_bareme=execSql($sql);
									$tab_liste_frais = array();
									if($res_frais_bareme->numRows() > 0) {
										for($frais_bareme=0;$frais_bareme<$res_frais_bareme->numRows();$frais_bareme++) {
										
											// Recuperer les infos de la ligne
											$res = $res_frais_bareme->fetchInto($ligne_frais_bareme, DB_FETCHMODE_DEFAULT, $frais_bareme);

											$frais[count($frais)] = array(
																"type_frais_id" => $ligne_frais_bareme[2],
																"montant" => $ligne_frais_bareme[3],
																"optionnel" => $ligne_frais_bareme[4],
																"selectionne" => 0,
																"lisse" => $ligne_frais_bareme[6],
																"caution_remboursee" => 0,
																"libelle" => $ligne_frais_bareme[5]
																	);
					
											// Ajouter une echeance pour les non-lissees (si pas optionnel) ET POUR LES NON-LISSES
											if($ligne_frais_bareme[4] == 0 && $ligne_frais_bareme[6] == 0) {
												$echeancier[count($echeancier)] = array(
																	"date" => date('Y-m-d'),
																	"montant" => $ligne_frais_bareme[3],
																	"lisse" => 0
																		);
											}
											
											// Verifier si le frais est lisse ou non
											if($ligne_frais_bareme[6] == 1) {
												// Somme seulement si pas optionnel
												if($ligne_frais_bareme[4] == 0) {
													$total_frais_lisses += $ligne_frais_bareme[3];
												}
											} else {
												// Somme seulement si pas optionnel
												if($ligne_frais_bareme[4] == 0) {
													$total_frais_non_lisses += $ligne_frais_bareme[3];
												}
											}

										}
										
										// Afficher les frais
										for($j=0;$j<count($frais);$j++) {
							?>
										<tr class='tabnormal2'>
											<td align="right" nowrap="nowrap"><?php echo $frais[$j]['type_frais_id']; ?></td>
											<td align="left" nowrap="nowrap"><?php echo $frais[$j]['libelle']; ?></td>
											<td align="right" nowrap="nowrap"><?php echo $frais[$j]['montant']; ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											<?php
											$valeur = LANG_FIN_GENE_018;
											if($frais[$j]['optionnel'] == '1') {
												// Optionnel
												$valeur = LANG_FIN_GENE_017;
											}
											?>
											<td align="center" nowrap="nowrap"><?php echo $valeur; ?></td>
											<?php
											$valeur = LANG_FIN_GENE_018;
											if($frais[$j]['lisse'] == '1') {
												// Lisse
												$valeur = LANG_FIN_GENE_017;
											}
											?>
											<td align="center" nowrap="nowrap"><?php echo $valeur; ?></td>
										</tr>
							<?php

										}
									}
							?>
										<tr class='tabnormal2'>
											<td colspan="2" nowrap="nowrap">&nbsp;</td>
											<td align="right" nowrap="nowrap"><b>Total lissés :</b></td>
											<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_frais_lisses); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
											<td>&nbsp;</td>
										</tr>
										<tr class='tabnormal2'>
											<td colspan="2" nowrap="nowrap">&nbsp;</td>
											<td align="right" nowrap="nowrap"><b>Total non-lissés :</b></td>
											<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_frais_non_lisses); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
											<td>&nbsp;</td>
										</tr>
										<tr class='tabnormal2'>
											<td colspan="2" nowrap="nowrap">&nbsp;</td>
											<td align="right" nowrap="nowrap"><b>Total :</b></td>
											<td align="right" nowrap="nowrap"><b><?php echo montant_depuis_bdd($total_frais_lisses + $total_frais_non_lisses); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></b></td>
											<td>&nbsp;</td>
										</tr>
									</table>
								</td>
							</td>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Année scolaire&nbsp;:&nbsp;<?php echo $annee_scolaire; ?></td>
							</tr>
							<?php
							
									// Rechercher le type d'echeancier
									$sql ="SELECT libelle, echeances, intervale_mois ";
									$sql.="FROM ".FIN_TAB_TYPE_ECHEANCIER." ";
									$sql.="WHERE type_echeancier_id = " . $classes[$classe]['type_echeancier_id'];
									$res_type_echeancier = execSql($sql);
									if($res_type_echeancier->numRows() > 0) {
										$ligne_type_echeancier = &$res_type_echeancier->fetchRow();
										
										// Infos pour calculer l'echeancier qui viennent du type d'echeancier
										$echeances = $ligne_type_echeancier[1];
										$intervale_mois = $ligne_type_echeancier[2];
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Type d'echeancier&nbsp;:&nbsp;<?php echo $ligne_type_echeancier[0]; ?></td>
							</tr>
							<?php
			
								// Calcul d'une echeance (arrondi a 2 chiffres apres la virgule)
								$montant_echeance = number_format($total_frais_lisses / $echeances, 2);
								$montant_echeance = str_replace(',', '', $montant_echeance);
			
								// Calcul de la derniere echeance (peut etre inferieure aux autres echeances a cause des arrondis)
								if(($montant_echeance * $echeances) == $total_frais_lisses || $echeances <= 1) {
									// => toutes les echeances sont egales
									$montant_derniere_echeance = $montant_echeance;
								} else {
									// => la derniere echeance est differente
									// Ajuster l'echeance pour que la dernier echeance soit inferieure
									if((($echeances - 1) * $montant_echeance) > $montant_echeance) {
										$montant_echeance += 0.01;
									}
									// Calcul de la derniere echeance
									$montant_derniere_echeance = $total_frais_lisses - (($echeances - 1) * $montant_echeance);
								}



								// Remplir le tableau des echeances (qui sera utiliser pour inscrire) => POUR LES LISSES
								$date_echeance = $date_debut;
								for($j=1;$j<=$echeances;$j++) {
									// Verifier si on est sur la derniere echeance ou non
									if($j < $echeances) {
										$montant = $montant_echeance;
									} else {
										$montant = $montant_derniere_echeance;
									}
									
									// Stockage dans le tableau
									$echeancier[count($echeancier)] = array(
																		"date" => date_vers_bdd($date_echeance),
																		"montant" => $montant,
																		"lisse" => 1
																			);
									// Calcul de la prochaine date d'echeance (date precedente + "$intervale_mois" mois)			
									$timestamp = strtotime("+$intervale_mois month", mktime(0, 0, 0, substr($date_echeance, 3, 2), substr($date_echeance, 0, 2), substr($date_echeance, 6, 4)));
									$date_echeance = date('d/m/Y', $timestamp);
								}
							
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Date départ échéancier&nbsp;:&nbsp;<?php echo $date_debut; ?></td>
							</tr>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Echéancier&nbsp;:&nbsp;</td>
							</tr>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="75%" align="center">
										<tr bgcolor="#ffffff">
											<td align="right" nowrap="nowrap"><b>Date</b></td>
											<td align="right" nowrap="nowrap"><b>Montant</b></td>
											<td align="center" nowrap="nowrap"><b>Lissé</b></td>
										</tr>
								<?php
									// Afficher l'echeancier
									for($j=0;$j<count($echeancier);$j++) {
								?>	
										<tr class='tabnormal2'>
											<td align="right" nowrap="nowrap"><?php echo date_depuis_bdd($echeancier[$j]['date']); ?></td>
											<td align="right" nowrap="nowrap"><?php echo montant_depuis_bdd($echeancier[$j]['montant']); ?>&nbsp;<?php echo LANG_FIN_GENE_019; ?></td>
											<?php
											$valeur = LANG_FIN_GENE_018;
											if($echeancier[$j]['lisse'] == 1) {
												// Lisse
												$valeur = LANG_FIN_GENE_017;
											} else {
												// Pas lisse
											}
											?>
											<td align="center" nowrap="nowrap"><?php echo $valeur; ?></td>
										</tr>
								<?php
									}
								?>
									</table>
								</td>
							</tr>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left">Elèves :</td>
							</tr>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="center">
									<table cellspacing="1" cellpadding="3" border="0" bgcolor="#0b3a0c" width="75%" align="center">
										<tr bgcolor="#ffffff">
											<td align="right" nowrap="nowrap"><b>ID</b></td>
											<td align="left" nowrap="nowrap"><b>Nom</b></td>
											<td align="left" nowrap="nowrap"><b>Prénom</b></td>
											<td align="left" nowrap="nowrap"><b>Inscrit ?</b></td>
											<td align="left" nowrap="nowrap"><b>Commentaire</b></td>
										</tr>
							<?php
										// Rechercher les eleves de la classe
										$sql ="SELECT elev_id, nom, prenom ";
										$sql.="FROM ".FIN_TAB_ELEVES." ";
										$sql.="WHERE classe = " . $classes[$classe]['code_class'];
										$res_eleve = execSql($sql);
										$commentaire = '';
										if($res_eleve->numRows() > 0) {
											// Traiter chaque eleve
											for($eleve=0; $eleve<$res_eleve->numRows(); $eleve++) {
												$res = $res_eleve->fetchInto($ligne_eleve, DB_FETCHMODE_DEFAULT, $eleve);
												
												$commentaire = '';
												// Verifier si l'eleve est deja inscrit ou non
												$sql ="SELECT inscription_id ";
												$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
												$sql.="WHERE elev_id = " . $ligne_eleve[0] . " ";
												$sql.="AND code_class = " . $classes[$classe]['code_class'] . " ";
												$sql.="AND annee_scolaire = '" . $annee_scolaire . "' ";
												$res_inscription = execSql($sql);
												if($res_inscription->numRows() > 0) {
													$ligne_inscription = &$res_inscription->fetchRow();

													if(!$debug) {
														$inscrit = '<font color="#CC0000">Déja inscrit</font>';
													} else {
														$inscrit = '<font color="#CC0000">Déja inscrit (debug)</font>';
													}
													
													// Rechercher les frais d'inscription
													$sql ="SELECT frais_inscription_id ";
													$sql.="FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
													$sql.="WHERE inscription_id = " . $ligne_inscription[0] . " ";
													$sql.="ORDER BY frais_inscription_id ";
													$res_frais_inscription = execSql($sql);
													$frais_inscription_id = '';
													$separateur = '';
													if($res_frais_inscription->numRows() > 0) {
														for($frais_inscription=0; $frais_inscription<$res_frais_inscription->numRows(); $frais_inscription++) {
															$res = $res_frais_inscription->fetchInto($ligne_frais_inscription, DB_FETCHMODE_DEFAULT, $frais_inscription);
															$frais_inscription_id .= $separateur . $ligne_frais_inscription[0];
															$separateur = ',';
															
														}
													}

													// Rechercher les echeances
													$sql ="SELECT echeancier_id ";
													$sql.="FROM ".FIN_TAB_ECHEANCIER." ";
													$sql.="WHERE inscription_id = " . $ligne_inscription[0] . " ";
													$sql.="ORDER BY echeancier_id ";
													$res_echeancier = execSql($sql);
													$echeancier_id = '';
													$separateur = '';
													if($res_echeancier->numRows() > 0) {
														for($pos_echeancier=0; $pos_echeancier<$res_echeancier->numRows(); $pos_echeancier++) {
															$res = $res_echeancier->fetchInto($ligne_echeancier, DB_FETCHMODE_DEFAULT, $pos_echeancier);
															$echeancier_id .= $separateur . $ligne_echeancier[0];
															$separateur = ',';
															
														}
													}
													
													// Preparer les donnes a afficher dans le commentaire
													$commentaire = 'inscription_id=' . $ligne_inscription[0] . '<br> frais_inscription_id=' . $frais_inscription_id . '<br>echeancier_id=' . $echeancier_id;
													
												} else {
												
													// Verifier si l'eleve a un RIB
													$numero_rib = 0;
													$sql ="SELECT numero_rib ";
													$sql.="FROM ".FIN_TAB_RIB." ";
													$sql.="WHERE elev_id = " . $ligne_eleve[0] . " ";
													$sql.="ORDER BY numero_rib ";
													$res_rib = execSql($sql);
													if($res_rib->numRows() > 0) {
														// Recuperer la ligne avec les info du RIB
														$ligne_rib = &$res_rib->fetchRow();
														
														// Recuperer la numero du RIB
														$numero_rib = $ligne_rib[0];
														
														if($operation == 'inscrire') {
														
															if(!$debug) {
																// 1 - L'eleve n'est pas inscrit
																// 2 - L'utilisateur a demandé l'inscription
																// 3 - On est pas en mode 'debug'
																// => Essayer d'inscrire l'eleve (inscriptions, frais_inscription, echeancier)
																$inscription_id = '0';
																$frais_inscription_id = '';
																$echeancier_id = '';
																
																// Table 'inscriptions'
																$sql  = "INSERT INTO ".FIN_TAB_INSCRIPTIONS." (elev_id, code_class, annee_scolaire, date_inscription, type_echeancier_id, commentaire) ";
																$sql .= "VALUES (";
																$sql .= "".$ligne_eleve[0].", ";
																$sql .= "".$classes[$classe]['code_class'].", ";
																$sql .= "'".$annee_scolaire."', ";
																$sql .= "'".date("Y-m-d H:i:s")."', ";
																$sql .= "".$classes[$classe]['type_echeancier_id'].", ";
																$sql .= "'' ";
																$sql .= ") ";
																//echo $sql;
																$res_lock=execSql("LOCK TABLES ".FIN_TAB_INSCRIPTIONS." WRITE");
																$inscription = execSql($sql);
																// Recuperer le id
																$inscription_id = dernier_id($cnx->connection);
																$res_lock=execSql("UNLOCK TABLES ");
																
																
																// Table 'frais_inscription'
																$separateur = '';
																for($k=0;$k<count($frais);$k++) {
																	$sql  = "INSERT INTO ".FIN_TAB_FRAIS_INSCRIPTION." (inscription_id, type_frais_id, montant, optionnel, selectionne, lisse, caution_remboursee) ";
																	$sql .= "VALUES (";
																	$sql .= "".$inscription_id.", ";
																	$sql .= "".$frais[$k]['type_frais_id'].", ";
																	$sql .= "".$frais[$k]['montant'].", ";
																	$sql .= "".$frais[$k]['optionnel'].", ";
																	$sql .= "".$frais[$k]['selectionne'].", ";
																	$sql .= "".$frais[$k]['lisse'].", ";
																	$sql .= "".$frais[$k]['caution_remboursee']." ";
																	$sql .= ") ";
																	//echo $sql . '<br>';
																	$res_lock=execSql("LOCK TABLES ".FIN_TAB_FRAIS_INSCRIPTION." WRITE");
																	$frais_inscription = execSql($sql);
																	$frais_inscription_id .= $separateur . dernier_id($cnx->connection);
																	$separateur = ',';
																	$res_lock=execSql("UNLOCK TABLES ");
																}	



																// Table 'echeancier'
																//print_r($echeancier);
																$type_reglement_id = $g_tab_type_reglement_id['prelevement'];
																$type_echeance = 0;
																
																$separateur = '';
																for($k=0;$k<count($echeancier);$k++) {
																	$sql  = "INSERT INTO ".FIN_TAB_ECHEANCIER." (inscription_id, date_echeance, montant, impaye, type_reglement_id, libelle, type, numero_rib, lisse) ";
																	$sql .= "VALUES (";
																	$sql .= "".$inscription_id.", ";
																	$sql .= "'".($echeancier[$k]['date'])."', ";
																	$sql .= "".($echeancier[$k]['montant']).", ";
																	$sql .= "0, ";
																	$sql .= "".$type_reglement_id.", ";
																	$sql .= "'', ";
																	$sql .= "".$type_echeance.",";
																	$sql .= "".$numero_rib.",";
																	$sql .= "".$echeancier[$k]['lisse']."";
																	$sql .= ") ";
																	//echo $sql . "<br>";
																	$res_lock=execSql("LOCK TABLES ".FIN_TAB_ECHEANCIER." WRITE");
																	$res_echeance = execSql($sql);
																	$echeancier_id .= $separateur . dernier_id($cnx->connection);
																	$separateur = ',';
																	$res_lock=execSql("UNLOCK TABLES ");
												
																}	
																
																
																$inscrit = '<font color="#33a90b">Inscription réussie</font>';
															} else {
																$inscription_id = '0';
																$frais_inscription_id = '0';
																$echeancier_id = '0';
																$inscrit = '<font color="#d8a713">Inscription simulée (debug)</font>';
															}
															
															// Preparer les donnes a afficher dans le commentaire
															$commentaire = 'inscription_id=' . $inscription_id . '<br> frais_inscription_id=' . $frais_inscription_id . '<br>echeancier_id=' . $echeancier_id;

															
														} else {
															if(!$debug) {
																$inscrit = '<font color="">Prêt à inscrire</font>';
															} else {
																$inscrit = '<font color="">Prêt à inscrire (debug)</font>';
															}
														}
													} else {
														if(!$debug) {
															$inscrit = '<font color="#CC0000">Pas de RIB disponible</font>';
														} else {
															$inscrit = '<font color="#CC0000">Pas de RIB disponible (debug)</font>';
														}
													}
												}
												
												// Afficher les donnees de l'eleve
							?>
										<tr class='tabnormal2'>
											<td align="right" nowrap="nowrap" valign="top"><?php echo $ligne_eleve[0]; ?></td>
											<td align="left" nowrap="nowrap" valign="top"><?php echo $ligne_eleve[1]; ?></td>
											<td align="left" nowrap="nowrap" valign="top"><?php echo $ligne_eleve[2]; ?></td>
											<td align="left" nowrap="nowrap" valign="top"><?php echo $inscrit; ?></td>
											<td align="left" nowrap="nowrap" valign="top"><?php echo $commentaire; ?></td>
										</tr>
							<?php
											}
										} else {
							?>
										<tr class='tabnormal2'>
											<td align="left" colspan="4" nowrap="nowrap">Pas d'élèves dans cette classe</td>
										</tr>
							<?php
										}
							?>
									</table>
								</td>
							</tr>
							<?php		
									} else {
										// Type d'echeancier introuvable
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left" width="95%"><font color="#CC0000">Type d'échéancier introuvable (type_echeancier_id=<?php echo $classes[$classe]['type_echeancier_id']; ?>)</font></td>
							</tr>
							<?php
									}	
								} else {
									// Bareme introuvable
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left" width="95%"><font color="#CC0000">Barème introuvable (bareme_id=<?php echo $classes[$classe]['bareme_id']; ?>)</font></td>
							</tr>
							<?php
								}
							} else {
								// Classe introuvable
							?>
							<tr>
								<td><img src="<?php echo "./" . $g_chemin_relatif_module . "images/espaceur.gif"; ?>" border="0" width="25" height="1"></td>
								<td align="left" width="95%"><font color="#CC0000">Classe n°<?php echo $classe; ?> introuvable (code_class=<?php echo $classes[$classe]['code_class']; ?>)</font></td>
							</tr>
							<?php
							}
						}
						?>
							<tr>
								<td align="center" colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td align="center" colspan="2">
									<a name="MESSAGE"></a>
									<?php 
									msg_util_afficher();
									msg_util_attente_init(); 
									?>
									</td>
							</tr>
							<?php //********** BOUTONS ********** ?>
							
							<tr>
								<td align="center" colspan="2">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<td align="center">
											<?php
											if($operation == '') {
											?>
											<td align="center">
												<script language="javascript">buttonMagic3("Inscrire les élèves prêts","onclick_inscrire()");</script>
											</td>
											<?php
											} else {
											?>
												<script language="javascript">buttonMagic3("Retour","onclick_retour()");</script>
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

					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">

						function onclick_inscrire() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = 'inscrire';
							document.formulaire.but_actualiser.click();
						}

						function onclick_retour() {
							msg_util_attente_montrer(true);
							document.formulaire.operation.value = '';
							document.formulaire.but_actualiser.click();
						}

					</script>

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