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
	
	$chambre_id = lire_parametre('chambre', '', 'GET');
	$annee = lire_parametre('annee', '', 'GET');
	
	$chambre_id_1 = lire_parametre('chambre', '', 'POST');
	$annee_1 = lire_parametre('annee', '', 'POST');
	
	if($annee_1 != '')
	{
		$chambre_id = $chambre_id_1;
		$annee = $annee_1;
	}
	//***************************************************************************

	//*************** RECUPERATION DU LIBELLE DE LA CHAMBRE****************
	$sql ="SELECT tb.batiment_id, tb.libelle, tb.adresse_1, tb.adresse_2, tb.adresse_3, tb.code_postal, tb.ville, tc.chambre_id, tc.numero, tc.libelle, tc.type_chambre_id ";
	$sql.="FROM ".CHA_TAB_BATIMENT." tb ";
	$sql.="INNER JOIN ".CHA_TAB_CHAMBRE." tc ON tb.batiment_id = tc.batiment_id ";
	$sql.="WHERE tc.chambre_id = $chambre_id";
	$res=execSql($sql);
	if($res->numRows() > 0)
	{
		$ligne = &$res->fetchRow();
		$separateur = '';
		$chambre_texte = trim($ligne[8]);
		if(trim($chambre_texte) != '') {
			$chambre_texte = 'n°' . $chambre_texte;
			$separateur = ' - ';
		}
				
		eval('$chambre_texte .= $separateur . LANG_CHA_TCHA_ID_' . $ligne[10] .';');
		$separateur = ' - ';
		if(trim($ligne[9]) != '') {
		$chambre_texte .= $separateur . trim($ligne[9]);
			$separateur = ' - ';
		}
	}
	//************************************************************************
	
	//*******************Recherche du nombre de lit***************************
	
	$sql ="SELECT tc.nombre_lits ";
	$sql.="FROM ".CHA_TAB_CHAMBRE." c  ";
	$sql.="INNER JOIN ".CHA_TAB_TYPE_CHAMBRE." tc ON c.type_chambre_id = tc.type_chambre_id ";
	$sql.="WHERE c.chambre_id = $chambre_id ";
	$res=execSql($sql);
	$ligne = &$res->fetchRow();
	$nb_lits = $ligne[0];

	//************************************************************************
	$tab_res_1 = array();
	$tab_res_deb_1 = array();
	$tab_res_fin_1 = array();
	
	$tab_res_2 = array();
	$tab_res_deb_2 = array();
	$tab_res_fin_2 = array();
	
	$date_debut = $annee.'/01/01';
	$date_fin = $annee.'/12/31';
	
	$time = strtotime($date_fin) - strtotime($date_debut);
	$nb_jour = intval($time/86400)+1;
	
	$jours_de_la_semaine = array();
	$jours_de_la_semaine[1] = strtotime($date_debut);
	
	for($jour=2;$jour<=$nb_jour;$jour++)
	{
		$jours_de_la_semaine[$jour] = mktime(0,0,0,date('m',$jours_de_la_semaine[1]),date('d',$jours_de_la_semaine[1]) + ($jour-1),date('Y',$jours_de_la_semaine[1])); 
	}
	
	for($j=1; $j<=count($jours_de_la_semaine); $j++) {
			// Rechercher les reservation pour cette chambre et ce jour
			// (il peut y en avoir plusieurs dans le cas des chambres doubles, triples, ...)
			$sql ="SELECT reservation_id, elev_id, date_debut, date_fin, chambre_id, date_reservation ";
			$sql.="FROM ".CHA_TAB_RESERVATION." ";
			$sql.="WHERE ";
			$sql.="'" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' >= date_debut AND '" . date('Y-m-d', $jours_de_la_semaine[$j]) . "' <= date_fin ";
			$sql.="AND chambre_id = " . $chambre_id . " ";
			$sql.="ORDER BY reservation_id";
			//echo $sql;
			$res=execSql($sql);
			
			if($res->numRows() > 0) {
			
				if($res->numRows() < $nb_lits) {
					// Partiellement libres
					$tab_res_2[] = date('Y-m-d', $jours_de_la_semaine[$j]);
				} else {
					// Occuppé
					$tab_res_1[] = date('Y-m-d', $jours_de_la_semaine[$j]);
				}
			}
	}

	//*********************************************************************************
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . CHA_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>

<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Calendrier des r&eacute;servations pour la chambre : <?php echo $chambre_texte;?></title>
		<link REL="StyleSheet" TYPE="text/css" HREF="librairie_css/css_calendrier.css">
	</head>
	<body>
		<div id="wrapper"> 
			<h1>Calendrier des r&eacute;servations pour la location : <?php echo $chambre_texte;?></h1>
			
			<table width="100%" border="0" align="center">
				<tr valign="top" align="center"> 
					<td>
						<?php 
						$mois = '01';
						include ("librairie_php/calendrier.inc.php");
						include ("librairie_php/string.inc.php");
						echo showCalendar("$annee-$mois");
						?>
					</td>
					<td>
						<?php 
						$mois = '02';
						echo showCalendar("$annee-$mois");
						?>
					</td>
				</tr>
				<tr valign="top" align="center">  
					<td>
						<?php 
						$mois = '03';
						echo showCalendar("$annee-$mois");
						?>
					</td>
					<td>
						<?php 
						$mois = '04';
						echo showCalendar("$annee-$mois");
						?>
					</td>
				</tr>
				<tr valign="top" align="center">  
					<td>
						<?php 
						$mois = '05';
						echo showCalendar("$annee-$mois");
						?>
					</td>
					<td>
						<?php 
						$mois = '06';
						echo showCalendar("$annee-$mois");
						?>
					</td>
				</tr>
				<tr valign="top" align="center"> 
					<td>
						<?php 
						$mois = '07';
						echo showCalendar("$annee-$mois");
						?>
					</td>
					<td>
						<?php 
						$mois = '08';
						echo showCalendar("$annee-$mois");
						?>
					</td>
				</tr>
				<tr valign="top" align="center"> 
					<td>
						<?php 
						$mois = '09';
						echo showCalendar("$annee-$mois");
						?>
					</td>
					<td>
						<?php 
						$mois = '10';
						echo showCalendar("$annee-$mois");
						?>
					</td>
				</tr>
				<tr valign="top" align="center"> 
					<td>
						<?php 
						$mois = '11';
						echo showCalendar("$annee-$mois");
						?>
					</td>
					<td>
						<?php 
						$mois = '12';
						echo showCalendar("$annee-$mois");
						?>
					</td>
				</tr>
			</table>
			<div id="footer"> 
				<p> 
					<table id="saison">
						<tr> 
							<td class="descriptif">&nbsp;<strong>Libre</strong></td>
							<td class="couleur basse">&nbsp;</td>
						</tr>
						<tr> 
							<td class="descriptif">&nbsp;<strong>Partiellement libre</strong></td>
							<td class="option entre">&nbsp;</td>
						</tr>
						<tr> 
							<td class="descriptif">&nbsp;<strong>Réservé</strong></td>
							<td class="couleur reserv">&nbsp;</td>
						</tr>
					</table>
				</p>
				<form method="post">
				&nbsp;<a href="planning_calendrier.php?annee=<?php echo $annee-1 ?>&chambre=<?php echo $chambre_id?>"><img src="./images/arrow-gauche.png" width="40" height="40" border="0" align="absmiddle"></a> 
					<select id=servselect name=annee>
						<?php $today = $annee;?>
						<option value="<?php echo $today - 3 ?>"><?php echo $today - 3 ?></option>
						<option value="<?php echo $today - 2 ?>"><?php echo $today - 2 ?></option>
						<option value="<?php echo $today - 1 ?>"><?php echo $today - 1 ?></option>
						<option value="<?php echo $today ?>" selected><?php echo $today ?></option>
						<option value="<?php echo $today + 1 ?>"><?php echo $today + 1 ?></option>
						<option value="<?php echo $today + 2 ?>"><?php echo $today + 2 ?></option>
						<option value="<?php echo $today + 3 ?>"><?php echo $today + 3 ?></option>
					</select>
					&nbsp; 
					<input type="hidden" name="chambre" value="<?php echo "$chambre_id" ;?>">
					<input type="submit" name="Submit" value="Voir">
				&nbsp;<a href="planning_calendrier.php?annee=<?php echo $annee+1 ?>&chambre=<?php echo $chambre_id?>"><img src="./images/arrow-droite.png" width="40" height="40" border="0" align="absmiddle"></a> 
				</form>
				<form>
					<input type="button" value="Imprimer" onClick="window.print()">
				</form> 
			</div>
		</div>
	</body>
</html>

<?php
// Fermeture connexion bddd
Pgclose();
?>
