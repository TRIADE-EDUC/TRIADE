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
	$elev_id_insc = lire_parametre('elev_id_insc', 0, 'POST');
	$elev_id_insc1 = lire_parametre('elev_id', 0, 'POST');
	$type_creation = trim(lire_parametre('type_creation', 'nouvelle', 'POST'));
	$code_class = lire_parametre('code_class', 0, 'POST');
	$annee_scolaire = trim(lire_parametre('annee_scolaire', '', 'POST'));
	$inscription_id_a_dupliquer = lire_parametre('inscription_id_a_dupliquer', 0, 'POST');
	$operation_rech = lire_parametre('operation_rech', '', 'POST');
	$code_class_rech = lire_parametre('code_class_rech', 0, 'POST');
	$nom_eleve_rech = lire_parametre('nom_eleve_rech', '', 'POST');
	//***************************************************************************

	//*************** TRAITER L'OPERATION DEMANDEE ******************************

	if($elev_id_insc1 != '')
	{
		$elev_id_insc=$elev_id_insc1;
	}
	
	// essayer d'utiliser les infos de la session pour pre-rechercher
	if($operation == '') {
		if($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['type_creation'] != '') {
			$type_creation = $_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['type_creation'];
		}
		if($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['code_class'] != '') {
			$code_class = $_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['code_class'];
		}
		if($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['annee_scolaire'] != '') {
			$annee_scolaire = $_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['annee_scolaire'];
		}
		if($_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['inscription_id_a_dupliquer'] != '') {
			$inscription_id_a_dupliquer = $_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['inscription_id_a_dupliquer'];
		}
	}

	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['type_creation'] = $type_creation;
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['code_class'] = $code_class;
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['annee_scolaire'] = $annee_scolaire;
	$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['inscription_id_a_dupliquer'] = $inscription_id_a_dupliquer;

	$inscription_terminee = false;
	$inscription_id_nouvelle = 0;
	
			
	switch($operation) {
		case "dupliquer_echeancier":
			//echo $elev_id_insc . ' a dupliquer comme inscription n°' . $inscription_id_a_dupliquer;

			// 1 - rechercher l'inscription existante
			$sql ="SELECT inscription_id, elev_id, code_class, annee_scolaire, date_inscription, type_echeancier_id, date_depart, commentaire, id_bareme_initial ";
			$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
			$sql.="WHERE inscription_id = " . $inscription_id_a_dupliquer . " ";
			$inscription_a_dupliquer=execSql($sql);
			
			if($inscription_a_dupliquer->numRows() > 0) {
				$res = $inscription_a_dupliquer->fetchInto($ligne_insc_a_dupliquer, DB_FETCHMODE_DEFAULT, 0);
			}
			
			// 2 - dupliquer l'inscription (creation nouvelle inscription)
			if($inscription_a_dupliquer->numRows() > 0) {
			
				$sql  = "INSERT INTO ".FIN_TAB_INSCRIPTIONS." (elev_id, code_class, annee_scolaire, date_inscription, type_echeancier_id, commentaire, id_bareme_initial) ";
				$sql .= "VALUES (";
				$sql .= "".$elev_id_insc.", ";
				$sql .= "".$ligne_insc_a_dupliquer[2].", ";
				$sql .= "'".$ligne_insc_a_dupliquer[3]."', ";
				$sql .= "'".date("Y-m-d H:i:s")."', ";
				$sql .= "".$ligne_insc_a_dupliquer[5].", ";
				$sql .= "'', ";
				$sql .= "".$ligne_insc_a_dupliquer[8]." ";
				$sql .= ") ";
				//echo $sql . '<br>';
				$res_lock=execSql("LOCK TABLES ".FIN_TAB_INSCRIPTIONS." WRITE");
				$inscription_nouvelle = execSql($sql);
				// Recuperer le id
				$inscription_id_nouvelle = dernier_id($cnx->connection);
				$res_lock=execSql("UNLOCK TABLES ");
			}


			// 3 - rechercher la liste des echeances de l'inscription existante
			if($inscription_id_nouvelle > 0) {
				
				$sqlnb="SELECT * ";
				$sqlnb.="FROM ".FIN_TAB_GROUPE_FRAIS." ";
				$res10 = execSql($sqlnb);
				
				$nbgroupe =$res10->numRows();
				
				$sql ="SELECT date_echeance, montant, impaye, type_reglement_id , libelle , type , numero_rib, lisse, echeancier_id  ";
				$sql.="FROM ".FIN_TAB_ECHEANCIER." ";
				$sql.="WHERE inscription_id = " . $inscription_id_a_dupliquer . " ";
				//echo $sql . "<br>";
				$echeances_a_dupliquer=execSql($sql);

				$sqlgroupe ="SELECT inscription_id, echeancier_id, groupe_id, montant ";
				$sqlgroupe.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." ";
				$sqlgroupe.="WHERE inscription_id = " . $inscription_id_a_dupliquer . " ";
				$sqlgroupe.="ORDER BY echeancier_id ";
				$resgroupe = execSql($sqlgroupe);
				
				if($echeances_a_dupliquer->numRows() > 0) {
					// 4 - dupliquer les echeances
					$n_echeance=0;
					
				for($i=0; $i<$echeances_a_dupliquer->numRows(); $i++) {				
						$res = $echeances_a_dupliquer->fetchInto($ligne_echeances_a_dupliquer, DB_FETCHMODE_DEFAULT, $i);
					
						// Ajouter l'echeance
						$sql  = "INSERT INTO ".FIN_TAB_ECHEANCIER." (inscription_id, date_echeance, montant, impaye, type_reglement_id, libelle, type, numero_rib, lisse) ";
						$sql .= "VALUES (";
						$sql .= "".$inscription_id_nouvelle.", ";
						$sql .= "'".$ligne_echeances_a_dupliquer[0]."', ";
						$sql .= "".$ligne_echeances_a_dupliquer[1].", ";
						$sql .= "0, ";
						$sql .= "".$ligne_echeances_a_dupliquer[3].", ";
						$sql .= "'".$ligne_echeances_a_dupliquer[4]."', ";
						$sql .= "".$ligne_echeances_a_dupliquer[5].", ";
						$sql .= "0,";
						$sql .= "".$ligne_echeances_a_dupliquer[7]."";
						$sql .= ") ";
						//echo $sql . '<br>';
						$nouvelle_echeance = execSql($sql);
						$num_echeance = mysqli_insert_id($cnx->connection);
						
						for($grp=0; $grp<$resgroupe->numRows(); $grp++)
						{
							$res1 = $resgroupe->fetchInto($ligne_groupe_a_dupliquer, DB_FETCHMODE_DEFAULT, $grp);
							
							if($ligne_echeances_a_dupliquer[8] == $ligne_groupe_a_dupliquer[1])
							{	
								$sql9  = "INSERT INTO ".FIN_TAB_ECHEANCIER_GROUPE." (inscription_id, echeancier_id, groupe_id, montant) ";
								$sql9 .= "VALUES (";
								$sql9 .= "".$inscription_id_nouvelle.", ";
								$sql9 .= "".$num_echeance.", ";
								$sql9 .= "'".$ligne_groupe_a_dupliquer[2]."', ";
								$sql9 .= "'".$ligne_groupe_a_dupliquer[3]."'";
								$sql9 .= ") ";
								$nouveau_groupe = execSql($sql9);
								
							}
						}
					}
				}
			}

			// 5 - rechercher la liste des frais de l'inscription existante
			if($inscription_id_nouvelle > 0) {
				$sql ="SELECT inscription_id, type_frais_id, montant, optionnel , selectionne , lisse , caution_remboursee ";
				$sql.="FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
				$sql.="WHERE inscription_id = " . $inscription_id_a_dupliquer . " ";
				//echo $sql . '<br>';
				$frais_a_dupliquer=execSql($sql);
				//echo (' - ' . $frais_a_dupliquer->numRows());
				if($frais_a_dupliquer->numRows() > 0) {
					// 6 - dupliquer les frais
					for($i=0; $i<$frais_a_dupliquer->numRows(); $i++) {
						$res = $frais_a_dupliquer->fetchInto($ligne_frais_a_dupliquer, DB_FETCHMODE_DEFAULT, $i);
					
						$sql  = "INSERT INTO ".FIN_TAB_FRAIS_INSCRIPTION." (inscription_id, type_frais_id, montant, optionnel, selectionne, lisse, caution_remboursee) ";
						$sql .= "VALUES (";
						$sql .= "".$inscription_id_nouvelle.", ";
						$sql .= "".$ligne_frais_a_dupliquer[1].", ";
						$sql .= "".$ligne_frais_a_dupliquer[2].", ";
						$sql .= "".$ligne_frais_a_dupliquer[3].", ";
						$sql .= "".$ligne_frais_a_dupliquer[4].", ";
						$sql .= "".$ligne_frais_a_dupliquer[5].", ";
						$sql .= "0 ";
						$sql .= ") ";
						//echo $sql . '<br>';
						$frais_inscription = execSql($sql);
					}
				}
			}

			if($inscription_id_nouvelle > 0) {
				$inscription_terminee = true;
				// 20100526 - AP : on guarde le id de l'inscription a dupliquer dans la session pour re-utilisation au prochain
				//                 appel du script
				$_SESSION[FIN_REP_MODULE]['inscription_dupliquer_echeancier']['inscription_id_a_dupliquer'] = $inscription_id_a_dupliquer;
			}
			
			break;
	}
	
	// Rechercher la liste des classes
	$sql ="SELECT c.code_class, c.libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." c ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON c.code_class = i.code_class ";
	$sql.="GROUP BY c.code_class, c.libelle ";
	$sql.="ORDER BY c.libelle";
	$classes=execSql($sql);
	//echo $sql;
	
	// Selectionner la premiere classe (si il n'y en a pas deja une)
	if($classes->numRows() > 0 && $code_class <= 0) {
		$ligne = null;
		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$code_class = $ligne[0];
	}

	if($classes->numRows() == 0) {
		$type_creation = 'nouvelle';
	}

	// Rechercher la liste des annees scolaires
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	$sql.="WHERE code_class = " . $code_class . " ";
	$sql.="GROUP BY annee_scolaire ";
	$sql.="ORDER BY annee_scolaire";
	$annees_scolaires=execSql($sql);
	//echo $sql;
	
	// Selectionner la premiere annee scolaire (si il n'y en a pas deja une)
	if($annees_scolaires->numRows() > 0 && ($operation == 'reload_code_class' || $annee_scolaire == '')) {
		$ligne = null;
		$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$annee_scolaire = $ligne[0];
	}
	
	
	// Rechercher la liste des inscription (et donc des eleves)
	$sql ="SELECT e.elev_id, e.nom, e.prenom, i.inscription_id ";
	$sql.="FROM ".FIN_TAB_ELEVES." e ";
	$sql.="INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON e.elev_id = i.elev_id ";
	$sql.="WHERE i.code_class = " . $code_class . " ";
	$sql.="AND i.annee_scolaire = '" . $annee_scolaire . "' ";
	$sql.="AND i.elev_id <> " . $elev_id_insc . " ";
	$sql.="ORDER BY e.nom, e.prenom ";
	$inscriptions=execSql($sql);
	//echo $sql;

	// La premiere inscription (1er eleve) est selectionee
	if($inscriptions->numRows() > 0 && ($operation == 'reload_annee_scolaire' || $inscription_id_a_dupliquer == 0)) {
		$res = $inscriptions->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$inscription_id_a_dupliquer = $ligne[3];
	} else {
		// 20100526 - AP : Verifier si $inscription_id_a_dupliquer est dans la liste
		if($inscriptions->numRows() > 0) {
			$est_dans_liste = false;
			for($i=0; $i<$inscriptions->numRows(); $i++) {
				$res_dans_liste = $inscriptions->fetchInto($ligne_dans_liste, DB_FETCHMODE_DEFAULT, $i);
				if($ligne_dans_liste[3] == $inscription_id_a_dupliquer) {
					$est_dans_liste = true;
					break;
				}
			}
			if(!$est_dans_liste) {
				$res_dans_liste = $inscriptions->fetchInto($ligne_dans_liste, DB_FETCHMODE_DEFAULT, 0);
				$inscription_id_a_dupliquer = $ligne_dans_liste[3];
			}
		}
	}

	// Rechercher les infos de l'eleve
	$sql  = "SELECT nom, prenom ";
	$sql .= "FROM ".FIN_TAB_ELEVES." ";
	$sql .= "WHERE elev_id = $elev_id_insc ";
	$eleve = execSql($sql);
				
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
					<b>
						<font id="menumodule1" >
							<?php echo LANG_FIN_DUPL_01; ?>
							<?php
							if($eleve->numRows() > 0) {
								$ligne = $eleve->fetchRow();
								
								echo ' : <font id="color2">' . strtoupper($ligne[0]) . ' ' . ucfirst($ligne[1]) . '</font>';
							}
							?>
						</font>
					</b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<?php
                    // Rediriger directement vers l'edition de l'inscription, une fois l'inscription reussie
                    if($inscription_terminee) {
                    ?>
                    <form name="for_aller_inscription_editer" id="for_aller_inscription_editer" method="post" action="<?php echo $g_chemin_relatif_module; ?>inscription_editer.php">
                        <input type="hidden" name="inscription_id" id="inscription_id" value="<?php echo $inscription_id_nouvelle; ?>">
                    </form>
                    <script language="javascript">
                        document.for_aller_inscription_editer.submit();
                    </script>
                    <?php
                    }
                    ?>
					<form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						<input type="hidden" name="operation" id="operation" value="">
						<input type="hidden" name="elev_id_insc" id="elev_id_insc" value="<?php echo $elev_id_insc; ?>">
						<input type="hidden" name="operation_rech" id="operation_rech" value="<?php echo $operation_rech; ?>">
						<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class_rech; ?>">
						<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve_rech; ?>">

						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td valign=top align="center">

									<table border="0" cellpadding="0" cellspacing="2" align="center">
                                    	<tr>
                                        	<?php
											if($type_creation == 'nouvelle') {
												$checked = 'checked';
											} else {
												$checked = '';
											}
											?>
                                        	<td align="left">
                                            	<input type="radio" name="type_creation" value="nouvelle" <?php echo $checked; ?>>
                                            </td>
                                            <td align="left">&nbsp;&nbsp;&nbsp;</td>
                                            <td align="left"><?php echo LANG_FIN_DUPL_02; ?></td>
										</tr>
										<?php
										// Verifier si on a au moins une classe
										if($classes->numRows() > 0) {
										?>
                                        <tr>
                                        	<?php
											if($type_creation == 'dupliquer') {
												$checked = 'checked';
											} else {
												$checked = '';
											}
											?>
                                        	<td align="left">
                                            	<input type="radio" name="type_creation" value="dupliquer" <?php echo $checked; ?>>
                                            </td>
                                            <td align="left">&nbsp;&nbsp;&nbsp;</td>
                                            <td align="left"><?php echo LANG_FIN_DUPL_03; ?></td>
                                      	</tr>
                                        <tr>
                                            <td align="left" colspan="2">&nbsp;</td>
                                            <td align="left">
                                                <table border="0" cellpadding="0" cellspacing="2" align="center">
                                                    
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
                                                    </tr>
                                                    <tr>
                                                        <td align="right"><?php echo LANG_FIN_GENE_011; ?>&nbsp;:&nbsp;</td>
                                                        <td align="left">
                                                            <?php
                                                            // Verifier si on a au moins une annee scolaire
                                                            $disabled = '';
                                                            if($annees_scolaires->numRows() > 0) {
                                                            ?>
                                                            <select name="annee_scolaire" id="annee_scolaire" onChange="onchange_annee_scolaire()">
                                                                <?php
                                                                for($i=0; $i<$annees_scolaires->numRows(); $i++) {
                                                                    $res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
                                                                    $selected = '';
                                                                    if($annee_scolaire == $ligne[0]) {
                                                                        $selected = 'selected="selected"';
                                                                    }
                                                                ?>
                                                                <option value="<?php echo $ligne[0]; ?>" <?php echo $selected; ?>><?php echo ucfirst($ligne[0]); ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <input type="hidden" name="annee_scolaire" id="" value="annee_scolaire">
                                                            <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="right" valign="top"><?php echo LANG_FIN_ELEV_002; ?>&nbsp;:&nbsp;</td>
                                                        <td align="left" valign="top">
                                                            <?php
                                                            // Verifier si on a au moins une inscription
                                                            if($inscriptions->numRows() > 0) {
                                                            ?>
															<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc">
                                                                <?php
                                                                for($i=0; $i<$inscriptions->numRows(); $i++) {
                                                                    $res = $inscriptions->fetchInto($ligne_inscription, DB_FETCHMODE_DEFAULT, $i);
                                                                    $checked = '';
                                                                    if($inscription_id_a_dupliquer == $ligne_inscription[3]) {
                                                                        $checked = 'checked';
                                                                    }
                                                                ?>
																<tr bgcolor="#ffffff">
																	<td nowrap="nowrap" align="center"><input type="radio" name="inscription_id_a_dupliquer" id="inscription_id_a_dupliquer" value="<?php echo $ligne_inscription[3]; ?>" <?php echo $checked; ?>></td>
 																	<td nowrap="nowrap" align="left"><?php echo $ligne_inscription[2]; ?></td>
  																	<td nowrap="nowrap" align="left"><?php echo $ligne_inscription[1]; ?></td>
                                                               <?php
                                                                }
                                                                ?>
																</tr>
															</table>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                &nbsp;
                                                            <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>   
        
                                                </table>
                                         	</td>
                                       	</tr>
										<?php
										}
										?>
										
									</table>

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
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_INSC_004?>","onclick_inscrire()");</script>
											</td>
											<td align="center">
												<script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003?>","onclick_annuler()");</script>
											</td>
										</tr>
									</table>
								</td>
							</tr>
								
						</table>

					</form>
					
				</td>
			</tr>
		</table>





		<?php //********** VALIDATION FORMULAIRES ********** ?>


		<?php //********** GESTION NAVIGATION ********** ?>
		
		<script language="javascript">
			var fenetre = null;
			var timeout = null;
			var contenu_target = '';

			function onclick_annuler() {
				msg_util_attente_montrer(true);
				document.getElementById('formulaire_annuler').submit();
			}

			function onchange_code_class() {
				document.formulaire.operation.value = 'reload_code_class';
				msg_util_attente_montrer(true);
				document.formulaire.submit();
			}

			function onchange_annee_scolaire() {
				document.formulaire.operation.value = 'reload_annee_scolaire';
				msg_util_attente_montrer(true);
				document.formulaire.submit();
			}



			function onclick_inscrire() {
				var type_creation = radio_lire_valeur(document.formulaire.type_creation);
				var code_class = '0';
				var annee_scolaire = '';
				var inscription_id_a_dupliquer = '0';
				
				<?php
                if($classes->numRows() > 0) {
               	?>
				code_class = document.formulaire.code_class.options[document.formulaire.code_class.selectedIndex].value;
				<?php
				}
				?>
				
				if(code_class != '0') {
					annee_scolaire = document.formulaire.annee_scolaire.options[document.formulaire.annee_scolaire.selectedIndex].value;
				} 
				
				<?php
                if($inscriptions->numRows() > 0) {
               	?>
				inscription_id_a_dupliquer = radio_lire_valeur(document.formulaire.inscription_id_a_dupliquer);
				<?php
				}
				?>
				
				//alert(type_creation + ' . ' + code_class + ' . ' + annee_scolaire + ' . ' + inscription_id_a_dupliquer);
				
				if(type_creation == 'nouvelle') {
					document.formulaire_inscrire.submit();
				} else {
					
					document.formulaire_dupliquer.type_creation.value = type_creation;
					document.formulaire_dupliquer.code_class.value = code_class;
					document.formulaire_dupliquer.annee_scolaire.value = annee_scolaire;
					document.formulaire_dupliquer.inscription_id_a_dupliquer.value = inscription_id_a_dupliquer;
					document.formulaire_dupliquer.submit();
				}

			}

		</script>

		<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>inscription_rechercher.php" method="post">
		</form>
		<form name="formulaire_inscrire" id="formulaire_inscrire" action="<?php echo $g_chemin_relatif_module; ?>inscription_inscrire.php" method="post">
			<input type="hidden" name="elev_id_insc" id="elev_id_insc" value="<?php echo $elev_id_insc; ?>">
			<input type="hidden" name="operation_rech" id="operation_rech" value="<?php echo $operation_rech; ?>">
			<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class_rech; ?>">
			<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve_rech; ?>">
		</form>
		<form name="formulaire_dupliquer" id="formulaire_dupliquer" action="<?php echo $g_chemin_relatif_module; ?>inscription_dupliquer_echeancier.php" method="post">
			<input type="hidden" name="operation" id="operation" value="dupliquer_echeancier">
			<input type="hidden" name="elev_id_insc" id="elev_id_insc" value="<?php echo $elev_id_insc; ?>">
			<input type="hidden" name="code_class_rech" id="code_class_rech" value="<?php echo $code_class; ?>">
			<input type="hidden" name="nom_eleve_rech" id="nom_eleve_rech" value="<?php echo $nom_eleve; ?>">
			<input type="hidden" name="type_creation" id="type_creation" value="<?php echo $type_creation; ?>">
			<input type="hidden" name="code_class" id="code_class" value="<?php echo $code_class; ?>">
			<input type="hidden" name="annee_scolaire" id="annee_scolaire" value="<?php echo $annee_scolaire; ?>">
			<input type="hidden" name="inscription_id_a_dupliquer" id="inscription_id_a_dupliquer" value="0">
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
