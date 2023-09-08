<?php
session_start();
error_reporting(0);
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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(120);
}
/*
### DEBUT AJOUT CIE FORMATION ###
*/
// Bulletin adapté Version 1.13 (03/06/2009) - Par Philippe GISCLON
$CIE_FORM_version_bulletin_triade = "1.16";
$CIE_FORM_date_bulletin_triade = "02/07/2014"; // Par l'équipe Triade 
// Philippe GISCLON - La Compagnie de Formation
/* DESCRIPTION : paramètres de configuration */
// Couleur des moyennes inférieures à 10
$CIE_FORM_coul_moy_inf_10 = "#FF0000"; // rouge : #FF0000
// Couleur des moyennes supérieures à 10
$CIE_FORM_coul_moy_sup_10 = "#000000"; // noir :  #000000
// Couleur des moyennes non significatives pour cause d'absences
$CIE_FORM_coul_moy_abs = "#000099"; // bleu :  #000099
// Couleur des flèches indiquant une progression
$CIE_FORM_coul_fleche_progr = "#00CC00"; // vert :  #00CC00
// Couleur des flèches indiquant une régression
$CIE_FORM_coul_fleche_regr = "#FF0000"; // rouge :  #FF0000
// Couleur des flèches indiquant une stagnation
$CIE_FORM_coul_fleche_stagn = "#666666"; // gris :  #666666
/*
### FIN AJOUT CIE FORMATION ###
*/
			
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >



<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
	$data=aff_enr_parametrage("autorisebulletinprof"); 
	if ($data[0][1] == "oui") {
		validerequete("3");
	}else{
		verif_profp_class($_SESSION["id_pers"],$_POST["saisie_classe"]);
	}
}else{
	validerequete("2");
}
$debut=deb_prog();
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
if (count($valeur)) {

	if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; }
}

$affadmission=$_POST["affadmission"];

		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : Modification de l'intitulé semestre => période */
		
if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="Première période"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="Seconde période"; }
}
// Test des données sélectionnées dans le formulaire : si erreur, affich message + renvoi au sélecteur
if (($_POST["typetrisem"] == "trimestre") || ($_POST["plageEleve"] != "tous")) {
echo "
<script language=\"javascript\">
alert(\"Le bulletin édité par Pigier fonctionne uniquement avec les paramètres suivants : mode semestriel, tous les élèves.\");
window.location.href = \"imprimer_trimestre.php\";
</script>
";
exit();
}
		/*
		Contenu original
		
if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; }
}
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/



// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
$classe_nom_bulletin=$classe_nom;
// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l etablissement
$data=visu_paramViaIdSite(chercheIdSite($_POST["saisie_classe"]));
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim(TextNoAccent($data[$i][0]));
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
}
// fin de la recup


if (MODNAMUR0 == "oui") {
	$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
}

// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}


$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

		/*
		### DEBUT AJOUT CIE FORMATION ###
		*/
		/* DESCRIPTION : recherche des évaluations pour lesquelles les étudiants ont été absents sur la période donnée */
		
		// on convertit les dates dans le bon format pour la recherche dans la BDD
		$CIE_FORM_date_deb_rech_temp = explode("/", $dateDebut);
		$CIE_FORM_date_fin_rech_temp = explode("/", $dateFin);
		
		$CIE_FORM_date_deb_rech = $CIE_FORM_date_deb_rech_temp[2]."-".$CIE_FORM_date_deb_rech_temp[1]."-".$CIE_FORM_date_deb_rech_temp[0];
		$CIE_FORM_date_fin_rech = $CIE_FORM_date_fin_rech_temp[2]."-".$CIE_FORM_date_fin_rech_temp[1]."-".$CIE_FORM_date_fin_rech_temp[0];
		
		$CIE_FORM_hostname_connexion = HOST; // Nom du serveur défini dans ./common/config.inc.php
		$CIE_FORM_database_connexion = DB; // Nom de la base de donnée défini dans ./common/config.inc.php
		$CIE_FORM_username_connexion = USER; // Nom de l'utilisateur MySQL défini dans ./common/config.inc.php
		$CIE_FORM_password_connexion = PWD; // Mot de passe MySQL défini dans ./common/config.inc.php
		$CIE_FORM_connexion = mysql_pconnect($CIE_FORM_hostname_connexion, $CIE_FORM_username_connexion, $CIE_FORM_password_connexion) or trigger_error(mysql_error(),E_USER_ERROR); 		mysql_select_db($CIE_FORM_database_connexion, $CIE_FORM_connexion);
		$CIE_FORM_query_abs = 'SELECT * FROM `tria_notes` WHERE `note` = - 1 AND `date` >= "'.$CIE_FORM_date_deb_rech.'" AND `date` <= "'.$CIE_FORM_date_fin_rech.'"';
		$CIE_FORM_abs = mysql_query($CIE_FORM_query_abs, $CIE_FORM_connexion) or die(mysql_error());
		$CIE_FORM_row_abs = mysql_fetch_assoc($CIE_FORM_abs);
		$CIE_FORM_totalRows_abs = mysql_num_rows($CIE_FORM_abs);

		do {
			
			if (!isset($CIE_FORM_tableau_absences[$CIE_FORM_row_abs['elev_id']][$CIE_FORM_row_abs['code_mat']])) {
			 $CIE_FORM_tableau_absences[$CIE_FORM_row_abs['elev_id']][$CIE_FORM_row_abs['code_mat']] = 1;
				 }
				
			 else {
				$CIE_FORM_tableau_absences[$CIE_FORM_row_abs['elev_id']][$CIE_FORM_row_abs['code_mat']]++;
			}
		} while ($CIE_FORM_row_abs = mysql_fetch_assoc($CIE_FORM_abs));
		echo '<div align="center"></br><b>Pigier</b></br><i>Version bulletin v.'.$CIE_FORM_version_bulletin_triade.' - '.$CIE_FORM_date_bulletin_triade.'</i></br></br>Bulletins édités par '.$_SESSION[nom].' '.$_SESSION[prenom].'</br></br></div>';
		echo "- Dénombrement des absences lors d'évaluations : ok</br>"; // affichage d'informations
		/*
		### FIN AJOUT CIE FORMATION ###
		*/


	/*
    ### DEBUT AJOUT CIE FORMATION ###
    */
    /* DESCRIPTION : récup des dates de P1 s'il s'agit d'un bulletin de P2 */
if ($_POST["saisie_trimestre"] == "trimestre2" ) {
	$dateRecup_P1=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup_P1);$j++) {
		$dateDebut_P1=$dateRecup_P1[$j][0];
		$dateFin_P1=$dateRecup_P1[$j][1];
	}
	$dateDebut_P1=dateForm($dateDebut_P1);
	$dateFin_P1=dateForm($dateFin_P1);
	echo "- Détection bulletin de période 2 : ok</br>"; // affichage d'informations
}
    /*
    ### FIN AJOUT CIE FORMATION ###
    */	


$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve


$moyenClasseGen=""; // pour le calcul moyenne classe
$moyenClasseMin=1000; // pour la calcul moyenne min classe
$moyenClasseMax=""; // pour la calcul moyenne max  classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

$examen="pigiercanne";
// pour le calcul de moyenne classe
$moyenClasseGen=calculMoyenClasseBlanc($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,$examen);
if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// ----------------------------


// calcul min et max general
//-------------------------
	$max="";
	$min=1000;
	/*
    ### DEBUT AJOUT CIE FORMATION ###
    */
    /* DESCRIPTION : création de 2 tableaux */
    $CIE_FORM_moy = array();
	$CIE_FORM_rang = array();
    /*
    ### FIN AJOUT CIE FORMATION ###
    */	
	for($g=0;$g<count($eleveT);$g++) {
		// variable eleve
		$idEleveMoyen=$eleveT[$g][4];
		$noteMoyEleG=0;
		$coefEleG=0;
		$moyenEleve2="";

		for($t=0;$t<count($ordre);$t++) {
			$idMatiere=$ordre[$t][0];
			$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$t][2]);
			
			$verifGroupe=verifMatiereAvecGroupe($ordre[$t][0],$idEleveMoyen,$idClasse,$ordre[$t][2]);
			if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

			$noteaff=moyenneEleveMatiereExamen($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
			       //moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$t][2]);
			
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coeffaff;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coeffaff;
			}
			
		}
				

		if (MODNAMUR0 == "oui") {
			$noteaff=calculNoteVieScolaire($idEleveMoyen,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coefBull;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefBull;
			}
		}

		if ($noteMoyEleG != "") {
			$moyenEleve2=moyGenEleve($noteMoyEleG,$coefEleG);
			/*
            ### DEBUT AJOUT CIE FORMATION ###
            */
            /* DESCRIPTION : on remplit le tableau avec les moyennes générales de chaque étudiant */
            $CIE_FORM_moy[$idEleveMoyen] = floatval(str_replace(",", ".", $moyenEleve2)); // ok
if (strlen($CIE_FORM_moy[$idEleveMoyen]) == 0) {$CIE_FORM_moy[$idEleveMoyen] = "00".$CIE_FORM_moy[$idEleveMoyen].".00";}
if (strlen($CIE_FORM_moy[$idEleveMoyen]) == 1) {$CIE_FORM_moy[$idEleveMoyen] = "0".$CIE_FORM_moy[$idEleveMoyen].".00";}
if (strlen($CIE_FORM_moy[$idEleveMoyen]) == 2) {$CIE_FORM_moy[$idEleveMoyen] = $CIE_FORM_moy[$idEleveMoyen].".00";}
			if ($CIE_FORM_moy[$idEleveMoyen] < 10) {$CIE_FORM_moy[$idEleveMoyen] = "0".$CIE_FORM_moy[$idEleveMoyen];}
			if (strlen($CIE_FORM_moy[$idEleveMoyen]) < 5) {$CIE_FORM_moy[$idEleveMoyen] = $CIE_FORM_moy[$idEleveMoyen]."0";}
            /*
            ### FIN AJOUT CIE FORMATION ###
            */
		}
		
		/*
		### DEBUT AJOUT CIE FORMATION ###
		*/
		/* DESCRIPTION : Pour le cas où un étudiant n'ai aucune note sur la période, on lui attribue une moyenne égale à 0 pour le placer en fin de classement */
		if ($noteMoyEleG == "") {
			$CIE_FORM_moy[$idEleveMoyen] = "00.00";
		}
		/*
		### FIN AJOUT CIE FORMATION ###
		*/
		
		/*
		### DEBUT AJOUT CIE FORMATION ###
		*/
		/* DESCRIPTION : recherche des évaluations pour lesquelles l'étudiant a été absent */
		if (isset($CIE_FORM_tableau_absences[$idEleveMoyen])) { // tableau constitué d'après la BDD au début du fichier
		// L'étudiant a eu au moins une absence à des évalusations
			// on éclate le tableau des absences de l'élève
			$CIE_FORM_nb_temp_absences = 0;
			$CIE_FORM_nb_total_absences = 0;
			foreach($CIE_FORM_tableau_absences[$idEleveMoyen] as $k => $v) {
				$CIE_FORM_nb_temp_absences = $v;
				$CIE_FORM_nb_total_absences = $CIE_FORM_nb_total_absences + $CIE_FORM_nb_temp_absences;
			}
//		$CIE_FORM_string_tmp_abs = $CIE_FORM_moy[$idEleveMoyen]."ABS".$CIE_FORM_nb_total_absences;
		$CIE_FORM_moy[$idEleveMoyen] = $CIE_FORM_string_tmp_abs;
		}
		/*
		### FIN AJOUT CIE FORMATION ###
		*/
		
		if (trim($moyenEleve2) != "") {
			$moyenEleve2=preg_replace('/,/','.',$moyenEleve2);
			$min=preg_replace('/,/','.',$min);
			$max=preg_replace('/,/','.',$max);
			if ($moyenEleve2 <= $min) { $min=$moyenEleve2; }
			if ($moyenEleve2 >= $max) { $max=$moyenEleve2; }
		}
	}
			echo "- Calcul des moyennes pour le classement sur la période : ok</br>"; // affichage d'informations
	/*
    ### DEBUT AJOUT CIE FORMATION ###
    */
    /* DESCRIPTION : on classe le tableau et on affecte le numéro de rang de chaque élève */
    arsort($CIE_FORM_moy);
	// tableau des noms des élèves
	foreach($eleveT as $k => $v) {
		$nom_eleve_LCF[$eleveT[$k][4]] = ucwords($eleveT[$k][1]).' '.strtoupper($eleveT[$k][0]);
	}
		echo "- Classement (<i>merci de vérifier qu'il n'existe pas d'incohérences</i>) :"; // affichage d'informations
		echo '
		<table width="402" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#666666">
  <tr>
    <th width="64" bgcolor="#999999" scope="col">Rang</th>
    <th width="67" bgcolor="#999999" scope="col">Moyenne</th>
    <th width="67" bgcolor="#999999" scope="col">Absences</th>
    <th width="168" bgcolor="#999999" scope="col">Nom &eacute;tud. </th>
  </tr>
		';
		$CIE_FORM_i = 0;
	foreach ($CIE_FORM_moy as $k=> $v) {
	$CIE_FORM_i++;
		echo '
		  <tr>
    <td><div align="center">'.$CIE_FORM_i.'</div></td>
    <td><div align="left">'.substr($v, 0, 5).'</div></td>
    <td><div align="center">'.substr($v, 8).'&nbsp;</div></td>
    <td><div align="center">'.$nom_eleve_LCF[$k].'</div></td>
  </tr>
		';
	}	
	echo '</table>';
	echo "- Préparation du classement : ok</br>"; // affichage d'informations
    // calcul du nb d'étudiants dans la classe
    $CIE_FORM_nb_etudiant_classe = count($CIE_FORM_moy);
    $rang_a_donner = 0;
    foreach($CIE_FORM_moy as $k => $v) {
    $rang_a_donner++;
		if (is_numeric($v)) {
			$CIE_FORM_rang[$k] = $rang_a_donner;}
		else {
			$CIE_FORM_nb_abs_tmp = explode('ABS',$v);
			$CIE_FORM_nb_abs_tmp2 = $CIE_FORM_nb_abs_tmp[1];
			$CIE_FORM_rang[$k] = $rang_a_donner."ABS".$CIE_FORM_nb_abs_tmp2;
		}
    }
    /*
    ### FIN AJOUT CIE FORMATION ###
    */

	if ($min == 1000) { $min=""; }
	$min=preg_replace('/\./',',',$min);
	$max=preg_replace('/\./',',',$max);
	$moyenClasseMin=$min;
	$moyenClasseMax=$max;
// fin min et max
// -------------
	
$plageEleve=$_POST["plageEleve"];
if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
if ($plageEleve == "10") { $dep=0; $nbEleveT=9; }
if ($plageEleve == "20") { $dep=9; $nbEleveT=19; }
if ($plageEleve == "30") { $dep=19; $nbEleveT=29; }
if ($plageEleve == "40") { $dep=29; $nbEleveT=39; }
if ($plageEleve == "50") { $dep=39; $nbEleveT=49; }
if ($plageEleve == "60") { $dep=49; $nbEleveT=59; }
if ($nbEleveT > count($eleveT)) { $nbEleveT=count($eleveT); }
for($j=$dep;$j<$nbEleveT;$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];


	//---------------------------------//
	// recherche le nombre de retard
	$nbretard=0;
	$nbretard1=0;
	$nbheureabs=0;
	$nbjoursabs=0;
	$nbabs=0;
	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbretard=count($nbretard);
	// recherche le nombre d absence
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	for($o=0;$o<=count($nbabs);$o++) {
		if ($nbabs[$o][4] > 0) {
	       		$nbjoursabs = $nbjoursabs + $nbabs[$o][4];
		}else{
			$nbheureabs = $nbheureabs + $nbabs[$o][7];	
		}
	}
	$nbabs=$nbjoursabs * 2;
	//---------------------------------//



	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	$coordonne4="E-mail : ".$mail;


	$titre="<B><U>".LANGBULL30."</U> <U>".ucwords($textTrimestre)."</u></B>";

	$nomEleve=strtoupper(trim($nomEleve));
	/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : première lettre des prénoms en majuscule */
	$prenomEleve=ucwords(trim($prenomEleve));
	
		/*
		Contenu original
	$prenomEleve=trim($prenomEleve);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	


	
		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : suppression tiret + suppression de la limite du nb de lettres pour nom et prénom */
	$nomprenom="<b>$nomEleve</b> $prenomEleve";
	$infoeleve="Etudiant(e)"." : $nomprenom";
	
		/*
		Contenu original
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",30);
	$infoeleve=LANGBULL31." : $nomprenom";
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=trim($classe_nom_bulletin);

	
		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : suppression tiret */
	$titrenote1=LANGBULL32;
	$titrenote2="Etud.";
	$titrenote3=LANGBULL33;
	$titrenote4="Appréciations et conseils pour progresser";
		/*
		Contenu original
	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	$appreciation=LANGBULL39;

	$appreciationbis="($nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ) " ;

	$barre="____________________________________________________________________________________________";
	$appreciation2=LANGBULL40;
	$duplicata=LANGBULL41 . " - $urlsite - $mail";
	$signature=LANGBULL42;
	$signature2="";
	$signature="";
	// FIN variables

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo

	// mise en place du logo
	$photo=recup_photo_bulletin_idsite(chercheIdSite($_POST["saisie_classe"]));
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xlogo=25;
			$ylogo=25;
			$xcoor0=30;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$pdf->Image($logo,3,3,$xlogo,$ylogo);
		}
	}
	// fin du logo
	//

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($xcoor0,$ycoor0+5);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY($xcoor0,$ycoor0+10);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY($xcoor0,$ycoor0+15);
	$pdf->WriteHTML($coordonne3);
	$pdf->SetXY($xcoor0,$ycoor0+20);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate=LANGBULL43." ".$anneeScolaire;
	
		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : suppression titre */
	$pdf->SetFont('Arial','',10);
		/*
		Contenu original
	$pdf->SetFont('Courier','',10);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	
	$pdf->SetXY(130,3);
	$pdf->WriteHTML($Pdate);
	// fin d'insertion

	// Titre
	$pdf->SetXY($xtitre,20);
	
		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : suppression tiret */
	$pdf->SetFont('Arial','',18);
		/*
		Contenu original
	$pdf->SetFont('Courier','',18);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	
	$pdf->WriteHTML($titre);
	// fin titre

	// cadre du haut
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(220);
	$pdf->SetXY(5,35); // placement du cadre du nom de l eleve
	$pdf->MultiCell(204,20,'',1,'L',1);

	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;
	/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : déplacement photo étud */
	$xphoto=16;
		/*
		Contenu original
	$xphoto=17;
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	
	$yphoto=36;
	//$photowidth=18;
	//$photoheight=18;
	
	/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : Modif taille photo étud */
	$photowidth=18;
	$photoheight=18;
		/*
		Contenu original
	$photowidth=10.8;
	$photoheight=16.3;
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
	$Xv1=20;
	$Xv11=111;
	if (file_exists($photo)) {
		if (natureImage($photo) == "JPG") {
			$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
			/*	
			### DEBUT MODIF CIE FORMATION ###
			*/
			/* DESCRIPTION : déplacement à droite infos étudiant */
			$Xv1=20+9+6;
			/*
			Contenu original
			$Xv1=20+9;
			*/
			/*
			### FIN MODIF CIE FORMATION ###
			*/
			
			$Xv11=110;
		}
	}
	$pdf->SetXY($Xv1,36); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	/*	
	  ### DEBUT MODIF CIE FORMATION ###
	*/
	/* DESCRIPTION : Déplacement du nom de la classe */
	$pdf->SetXY($Xv1,40);
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetXY($Xv1+14,40);
	$pdf->WriteHTML($infoeleveclasse);
		/*
		Contenu original
	$pdf->SetXY($Xv1+80,36);
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetXY($Xv1+94,36);
	$pdf->WriteHTML($infoeleveclasse);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/


	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
	for($ik=0;$ik<=count($dataadresse);$ik++) {
		$nomtuteur=$dataadresse[$ik][1];
		$prenomtuteur=$dataadresse[$ik][2];
		$adr1=$dataadresse[$ik][3];
		$code_post_adr1=$dataadresse[$ik][4];
		$commune_adr1=$dataadresse[$ik][5];
		$numero_eleve=$dataadresse[$ik][9];
		$datenaissance=$dataadresse[$ik][11];
		if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
		$regime=$dataadresse[$ik][12];
		$class_ant=trim(trunchaine($dataadresse[$ik][10],20));

		$pdf->SetXY($Xv1,40); 
		$pdf->SetFont('Arial','',8);
		/* $pdf->WriteHTML("N°: $numero_eleve "); */ // MISE EN COMMENTAIRE CIE FORMATIONS
		$pdf->SetXY($Xv1,44);
		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : suppression tiret */
			$pdf->WriteHTML("Né(e) le $datenaissance");
		/*
		Contenu original
			$pdf->WriteHTML("- Né(e) le $datenaissance");
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
		$pdf->SetXY($Xv1,48); 
		/* $pdf->WriteHTML("Regime: $regime ");*/ // MISE EN COMMENTAIRE CIE FORMATIONS
		$pdf->SetXY($Xv1+80,44);
		$class_ant=trunchaine($class_ant,40);
		/* $pdf->WriteHTML("Classe ant.: $class_ant "); */  // MISE EN COMMENTAIRE CIE FORMATIONS

		/*
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($Xv11,36);
		$chaine=LANGBULL44." ".trim(strtoupper($nomtuteur))." ".trim(ucwords(strtolower($prenomtuteur)));
		$pdf->WriteHTML(trunchaine($chaine,30));
		$pdf->SetXY($Xv11,42);
		$chaine=trim($num_adr1)." ".trim($adr1);
		$pdf->WriteHTML(trunchaine($chaine,30));;
		$pdf->SetXY($Xv11,48);
		$chaine=trim($code_post_adr1)." ".trim($commune_adr1);
		$pdf->WriteHTML(trunchaine($chaine,30));
		*/
	}

	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(220);
	$pdf->SetXY(5,60); //  placement  cadre titre
	$pdf->MultiCell(204,11,'',1,'C',1);
	$pdf->SetXY(15,62); // placement contenu titre
	$pdf->WriteHTML($titrenote1);
	$pdf->SetX(57);
	$pdf->WriteHTML($titrenote2);
	$pdf->SetX(80+30);
	$pdf->WriteHTML($titrenote3);
	$pdf->SetX(115+23);
	$pdf->WriteHTML($titrenote4);
	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(45,66);
	$pdf->WriteHTML($soustitre5);
	/*
	### DEBUT AJOUT CIE FORMATION ###
	*/
	/* DESCRIPTION : ajout des titres des colonnes P1 et P2 */
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { // S on est en bulletin de P2			
		$pdf->SetX(53);
		$pdf->WriteHTML("P1");
		$pdf->SetX(62);
		$pdf->WriteHTML("P2");
	}
	/*
	### FIN AJOUT CIE FORMATION ###
	*/

	$pdf->SetX(70);
	$pdf->WriteHTML("N.Part");
	$pdf->SetX(82);
	$pdf->WriteHTML("D.S.");

	$pdf->SetX(72+30);
	$pdf->WriteHTML($soustitre6);
	$pdf->SetX(82+30);
	$pdf->WriteHTML($soustitre7);
	$pdf->SetX(92+30);
	$pdf->WriteHTML($soustitre8);
	// fin des sous-titres



	// Mise en place des matieres et nom de prof
	$Xmat=5;
	$Ymat=71;
	$Xmatcont=6;
	$Ymatcont=71;

	$Xprof=55;
	$Yprof=$Ymat;
	$Xcoeff=45;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15 + 30;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=56;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=$Xcoeff + 12;
	$YnotVal=$Ycoeff + 3;
	$XcoeffVal=$Xcoeff + 1;
	$YcoeffVal=$Ymat + 3;
	$XprofVal=20; // x en nom prof
	$YprofVal=$Ymat + 4; // y en nom du prof
	$XmoyMatGVal=$Xcoeff + 26 + 30 ;
	$YmoyMatGVal=$Ycoeff + 3 ;

	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
		$noteMoyEleG_P1=0; // AJOUT LCF : mise à zéro moyenne étudiant
		$noteMoyEleG_annee=0; // AJOUT LCF : mise à zéro moyenne étudiant
	$coefEleG=0;
		$coefEleG_P1=0; // AJOUT LCF : mise à zéro somme coef
		$coefEleG_annee=0; // AJOUT LCF : mise à zéro somme coef
	$ii=0;




	for($i=0;$i<count($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


		// mise en place des matieres
		$largeurMat=40;
		$hauteurMatiere=12; // taille du cadre matiere
		//si plus de 10 matieres on reinitalise les positions;

		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		//	print $datasousmatiere;
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}

		// fin de la gestion sous matiere
		// ------------------------------
		$ii++;
		if ($ii == 18) {
			$pdf->AddPage();
			$Xmat=5;
			$Ymat=20;
			$Xmatcont=6;
			$Ymatcont=20;

			$Xprof=45;
			$Yprof=$Ymat;
			$Xcoeff=45;
			$Ycoeff=$Ymat;
			$Xmoyeleve=$Xcoeff + 10;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve + 15 + 30;
			$Ymoyclasse=$Ymat;

			$XnomProfcont=46;
			$YnomProfcont=$Ymatcont;
			$Xnote=$Xmoyclasse + 32;
			$Ynote=$Ymat;
			$XnotVal=$Xcoeff + 12;
			$YnotVal=$Ycoeff + 3;
			$XcoeffVal=$Xcoeff + 1;
			$YcoeffVal=$Ymat + 3;
			$XprofVal=20; // x en nom prof
			$YprofVal=$Ymat + 4; // y en nom du prof
			$XmoyMatGVal=$Xcoeff + 26 + 30 ;
			$YmoyMatGVal=$Ycoeff + 3 ;
			$ii=0;
		}

		/*
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : modification de la taille du texte */
			$pdf->SetFont('Arial','',6);
		/*
		Contenu original
			$pdf->SetFont('Arial','',8);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		/*
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : augmentation du nombre de caractères pour le nom des matières */
			$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),29).'</B>');
		/*
		Contenu original
			$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),19).'</B>');
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
		// $pdf->WriteHTML('<B>'.trunchaine(sansaccentmajuscule(strtoupper($matiere)),20).'</B>');
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		
/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : ajout d'une colonne et déplacement des autres si P2 */
if ($_POST["saisie_trimestre"] == "trimestre2" ) {
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve-10,$Ycoeff);
		$pdf->MultiCell(7,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;
		// mise en place moyenne eleve P1
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve-3,$Ymoyeleve);
		$pdf->MultiCell(7,$hauteurMatiere,'',1,'L',0);
		// mise en place moyenne eleve P2
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+4,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		$pdf->MultiCell(11,$hauteurMatiere,'',1,'L',1);
		// mise en place moyenne eleve ND
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+11+4,$Ymoyeleve);
		$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
		// mise en place moyenne eleve NP
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+20+5,$Ymoyeleve);
		$pdf->MultiCell(20,$hauteurMatiere,'',1,'L',0);
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
}else{
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff,$Ycoeff);
		$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;
		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
		// mise en place moyenne eleve ND
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+15,$Ymoyeleve);
		$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
		// mise en place moyenne eleve NP
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+20+5,$Ymoyeleve);
		$pdf->MultiCell(20,$hauteurMatiere,'',1,'L',0);
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;

}
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
		$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;

		// mise en place du cadre note
		$pdf->SetXY($Xnote,$Ynote);
		$pdf->MultiCell(77,$hauteurMatiere,'',1,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
	
		

/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : recup des moyennes + celles de P1 si bulletin de P2 + moyenne des deux */
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$examen);
				//moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
			if ($_POST["saisie_trimestre"] == "trimestre2" ) {
				$noteaff_P1=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idprof,$examen);
					//moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idprof);
				// Moyenne des notes de P1 et P2
				if ($noteaff_P1 != "" && $noteaff != "") {
				$noteaff_moy_P1P2 = ($noteaff_P1 + $noteaff)/2;
				} elseif ($noteaff_P1 != "" && $noteaff == "") { 
					$noteaff_moy_P1P2 = $noteaff_P1;
				} elseif ($noteaff_P1 == "" && $noteaff != "") { 
					$noteaff_moy_P1P2 = $noteaff;
				} else {
					$noteaff_moy_P1P2 = "";
				}
				if (trim($noteaff_P1) == "") $noteaff_P1=verifSiAbsExamen($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idprof,$examen);
			}
			if (trim($noteaff) == "") $noteaff=verifSiAbsExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$examen);
		}else{
			$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
				// moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			if ($_POST["saisie_trimestre"] == "trimestre2" ) {			
				$noteaff_P1=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idgroupe,$idprof,$examen);
				//moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idgroupe,$idprof);
				// Moyenne des notes de P1 et P2
				if ($noteaff_P1 != "" && $noteaff != "") {
					$noteaff_moy_P1P2 = ($noteaff_P1 + $noteaff)/2;
				} elseif ($noteaff_P1 != "" && $noteaff == "") { 
					$noteaff_moy_P1P2 = $noteaff_P1;
				} elseif ($noteaff_P1 == "" && $noteaff != "") { 
					$noteaff_moy_P1P2 = $noteaff;
				} else {
					$noteaff_moy_P1P2 = "";
				}
				
				if (trim($noteaff_P1) == "") $noteaff_P1=verifSiAbsExamen($idEleve,$ordre[$i][0],$dateDebut_P2,$dateFin_P1,$idprof,$examen);
			}
			if (trim($noteaff) == "") $noteaff=verifSiAbsExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$examen);
		}
/*
### FIN MODIF CIE FORMATION ###
*/



		/*
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : modification de la couleur des notes dans le cas < 10 + précision du nb d'absences aux evals si nécessaire */
		if(!isset($CIE_FORM_tableau_absences[$idEleve][$ordre[$i][0]])) { // L'étudiant n'a pas eu d'absences dans cette matière
			$pdf->SetFont('Arial','',12);
				if ($_POST["saisie_trimestre"] == "trimestre2" ) {// décallage des notes pour la période 2
							$pdf->SetXY($XnotVal+1.2,$YnotVal);
				} else {
							$pdf->SetXY($XnotVal,$YnotVal);
				}
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1 != "") && ($noteaff1 != "ABS")) {
				$noteaff1="0".$noteaff1;
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
			}
			else {
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
			}
			$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'">'.$noteaff1.'</font>');
			
			
		} else {
			// On affiche le nb d'absences dans la matière
			$pdf->SetFont('Arial','',6);
				if ($_POST["saisie_trimestre"] == "trimestre2" ) {// décallage des notes pour la période 2
							$pdf->SetXY($XnotVal+1.2,$YnotVal-3);
				} else {
							$pdf->SetXY($XnotVal,$YnotVal-3);
				}
//			$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy_abs.'">ABS '.$CIE_FORM_tableau_absences[$idEleve][$ordre[$i][0]].' éval.</font>');
			// On affiche la moyenne non significative
			$pdf->SetFont('Arial','',12);
				if ($_POST["saisie_trimestre"] == "trimestre2" ) {// décallage des notes pour la période 2
							$pdf->SetXY($XnotVal+1.2,$YnotVal);
				} else {
							$pdf->SetXY($XnotVal,$YnotVal);
				}
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1 != "") && ($noteaff1 != "ABS")) {
				$noteaff1="0".$noteaff1;
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
			}
			else {
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
			}
			$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy_abs.'">'.$noteaff1.'</font>');
		}
			// On affiche la moyenne de P1 si on est sur un bulletin de P2
			if ($_POST["saisie_trimestre"] == "trimestre2" ) { 				
				$pdf->SetFont('Arial','',7);
				$pdf->SetXY($XnotVal-5.5,$YnotVal-3);
				$noteaff1_P1=$noteaff_P1;
				// on formate et affiche la note de P1
				if (($noteaff1_P1 < 10) && ($noteaff1_P1 != "ABS") && ($noteaff1_P1 != "")) {
					$noteaff1_P1="0".$noteaff1_P1;
					$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
				}
				else {
					$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
				}
				$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'">'.$noteaff1_P1.'</font>');
				// On affiche la flèche de progression entre P1 et P2 et la différence
				if ($noteaff1_P1 != "" && $noteaff1 != "") {
				$difference_P1_P2 = round($noteaff1 - $noteaff1_P1,2);
				if ($difference_P1_P2 >= 0) {$difference_P1_P2 = "+".$difference_P1_P2;}
					$pdf->SetFont('Symbol','',7);
					$pdf->SetXY($XnotVal-5.8,$YnotVal+1);
					if ($noteaff1_P1 < $noteaff1) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_progr.'"><b>­</b></font>');}
					if ($noteaff1_P1 == $noteaff1) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_stagn.'"><b>®</b></font>');}
					if ($noteaff1_P1 > $noteaff1) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_regr.'"><b>¯</b></font>');}
					$pdf->SetFont('Arial','',5);
						if ($difference_P1_P2 == "+0") {
						$pdf->SetXY($XnotVal-3,$YnotVal+1);
						} else {
						$pdf->SetXY($XnotVal-4.2,$YnotVal+1);
						}
					$pdf->WriteHTML('<font color="#000000">'.$difference_P1_P2.'</font>');
				}
			}
		
		/*
		### FIN MODIF CIE FORMATION ###
		*/


		$YnotVal=$YnotVal + $hauteurMatiere;
		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetFont('Arial','',9);

/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : décallage texte coef si bulletin de P2 */
if ($_POST["saisie_trimestre"] == "trimestre2" ) {
		$pdf->SetXY($XcoeffVal-1.5,$YcoeffVal);
} else {
		$pdf->SetXY($XcoeffVal,$YcoeffVal);
}






// 
/*
Contenu original
		$pdf->SetXY($XcoeffVal,$YcoeffVal);
*/
/*
### FIN MODIF CIE FORMATION ###
*/
		$pdf->WriteHTML("$coeffaff");
		


		// Ajout Colonne afficher moyen Note participation du semestre en cours
		if ($idgroupe == "0") {
			$noteND=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"NP");
		}else{
			$noteND=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"NP");
		}
		if (trim($noteND) == "") $noteND=verifSiAbsExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"NP");
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XmoyMatGVal-31,$YmoyMatGVal);
		$pdf->WriteHTML($noteND);


		// afficher devoir surveille  du semestre en cours 
		if ($idgroupe == "0") {
			$listenote=recupNoteExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"ND");
		}else{
			$listenote=recupNoteGroupeExamen($idEleve,$ordre[$i][0],$idgroupe,$dateDebut,$dateFin,$idprof,"ND");
		}
		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($XmoyMatGVal-21,$YmoyMatGVal);
		$pdf->WriteHTML("$listenote");



		$YcoeffVal=$YcoeffVal + $hauteurMatiere;
		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyeMatGenExamen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof,$examen);
				//moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
			$moyeMatGen=moyeMatGenGroupeExamen($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
				//moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
    		}

		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);

		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->WriteHTML("$moyeMatGenaff");






		// calcul du min et du max
		if ($idgroupe == "0") {   // non matiere affectée à un groupe
			$max="";
			$min=1000;
			for($g=0;$g<count($eleveT);$g++) {
				// variable eleve
				$idEleveMoyen=$eleveT[$g][4];
				$valeur=moyenneEleveMatiereExamen($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
				      //moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
				if (trim($valeur) != "") {
					if ($valeur >= $max) { $max=$valeur; }
					if ($valeur <= $min) { $min=$valeur; }
				}

			}
			if ($min == 1000) { $min=""; }
			$moyeMatGenMin=$min;
			$moyeMatGenMax=$max;
		}else {
			$max="";
			$min=1000;
			$eleveTg=listeEleveDansGroupe($idgroupe);
			for($g=0;$g<count($eleveTg);$g++) {
				$idEleveMoyen=$eleveTg[$g];
				$valeur=moyenneEleveMatiereGroupeExamen($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
				//      moyenneEleveMatiereGroupe($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				if (trim($valeur) != "") {	
					if ($valeur >= $max) { $max=$valeur; }
					if ($valeur <= $min) { $min=$valeur; }
				}
			}
			if ($min == 1000) { $min=""; }
			$moyeMatGenMin=$min;
			$moyeMatGenMax=$max;
		}
		// fin de la calcul de min et max


	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(77,$confPolice[1],$commentaireeleve,'','','L',0);
	//$pdf->WriteHTML($commentaireeleve);
	
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal,$YprofVal);
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine($profAff,30));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}
	
	// Calcul de la moyenne de P1 sur bulletin de P2 + moyenne année
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { // On affiche la moyenne de P1
		if ( $noteaff_P1 != "" ) {
				$noteMoyEleGTempo_P1 = $noteaff_P1 * $coeffaff;
					$noteMoyEleG_P1=$noteMoyEleG_P1 + $noteMoyEleGTempo_P1;
					$coefEleG_P1=$coefEleG_P1 + $coeffaff;
		}
		// Calcul de la moyenne de l'année
		if ( $noteaff_moy_P1P2 != "" ) {
				$noteMoyEleGTempo_annee = $noteaff_moy_P1P2 * $coeffaff;
					$noteMoyEleG_annee=$noteMoyEleG_annee + $noteMoyEleGTempo_annee;
					$coefEleG_annee=$coefEleG_annee + $coeffaff;
		}
	}

}
// fin de la mise en place des matiere

// Note Vie Scolaire
if (MODNAMUR0 == "oui") {

	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<B>'.'Vie Scolaire'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
		
	// mise en place de la colonne coeff
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff,$Ycoeff);
	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	$Ycoeff=$Ycoeff + $hauteurMatiere;
	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
	$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
	$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
	$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
	$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;
	// mise en place du cadre note
	$pdf->SetXY($Xnote,$Ynote);
	$pdf->MultiCell(87,$hauteurMatiere,'',1,'',0);
	$Ynote=$Ynote + $hauteurMatiere;

	// mise en place des notes
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XnotVal,$YnotVal);
	$pdf->WriteHTML($noteaff);


	$YnotVal=$YnotVal + $hauteurMatiere;
	// mise en place des coeff
	$coeffaff=$coefBull;
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XcoeffVal,$YcoeffVal);
	$pdf->WriteHTML($coeffaff);
	$YcoeffVal=$YcoeffVal + $hauteurMatiere;

	// mise en place des moyennes de classe
        $moyeMatGen1=moyeMatGenVieScolaire($_POST["saisie_trimestre"],$idClasse); 
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
	$moyeMatGenaff=$moyeMatGen1;
	if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
	$pdf->WriteHTML($moyeMatGenaff);


	// calcul du min et du max
	$max="";
	$min=1000;
	for($g=0;$g<count($eleveT);$g++) {
		// variable eleve
		$idEleveMoyen=$eleveT[$g][4];
		$valeur=calculNoteVieScolaire($idEleveMoyen,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
		if (trim($valeur) != "") {
			if ($valeur >= $max) { $max=$valeur; }
			if ($valeur <= $min) { $min=$valeur; }
		}
	}
	if ($min == 1000) { $min=""; }
	$moyeMatGenMin=$min;
	$moyeMatGenMax=$max;
	// fin de la calcul de min et max

	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	
	// mise en place du nom du prof
	$profAff=$persVieScolaire;
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal,$YprofVal);
	$pdf->WriteHTML(trunchaine($profAff,30));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}

}

// fin notes
// --------






// cadre moyenne generale
$YmoyenneGeneral=$Ymoyclasse + 3;
if ($YmoyenneGeneral > 219) { // Valeur réduite pour générer un saut de page + haut car cadre commentaires + grand
	$pdf->AddPage();
	$YmoyenneGeneral=20;
}


$LargeurMG=$largeurMat;
$YmoyenneGeneralT=$YmoyenneGeneral + 2;
$XMoyGE= 10 + 5 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 10 + 6 + 30;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;


$pdf->SetFont('Arial','',9);
$pdf->SetXY(5,$YmoyenneGeneral);
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	$pdf->MultiCell($LargeurMG,16,'',1,'L',0);
} else {
	$pdf->MultiCell($LargeurMG,10,'',1,'L',0);
}
$pdf->SetXY(7,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>MOYENNE GENERALE</B>");
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(7,$YmoyenneGeneralT+3.8);
	$pdf->WriteHTML("Rappel moy. générale P1");
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY(7,$YmoyenneGeneralT+7.6);
	$pdf->WriteHTML("<B>MOYENNE ANNUELLE</B>");
}
$pdf->SetXY($XMoyGE,$YMoyGE);
$pdf->SetFillColor(220);
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	$pdf->MultiCell(15,16,'',1,'L',1);
	$pdf->SetXY($XMoyCL,$YMoyGE);
	$pdf->MultiCell(62,16,'',1,'L',0);
} else {
	$pdf->MultiCell(15,10,'',1,'L',1);
	$pdf->SetXY($XMoyCL,$YMoyGE);
	$pdf->MultiCell(62,10,'',1,'L',0);
}

$photo=recup_photo_signature_idsite(chercheIdSite($_POST["saisie_classe"]));
if ((file_exists("./data/image_pers/".$photo[0][0])) && ($_POST["ajsignature"] == "oui") && (trim($photo[0][0])!= "")){
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(165,$YmoyenneGeneralT+6.5); //
	//$pdf->WriteHTML("[ <I>Signature du directeur</I> ]");
	$taille = getimagesize("./data/image_pers/".$photo[0][0]);
	$logox=$taille[0]/12; //$logox=$taille[0]/25;
	$logoy=$taille[1]/12; //$logoy=$taille[1]/25;
	if (natureImage("./data/image_pers/".$photo[0][0]) == "JPG") {
		$pdf->Image("./data/image_pers/".$photo[0][0],"160",$YmoyenneGeneralT-6,$logox,$logoy);
	}
	$pdf->SetXY(165,$YmoyenneGeneralT-2);
	$pdf->WriteHTML("[ <I>Signature du directeur</I> ]");
}

// fin du cadre moyenne generale

// affichage de la moyenne generale eleve
$XmoyElValue=$LargeurMG + 17;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$pdf->SetFont('Arial','',12);
$pdf->SetXY($XmoyElValue,$YmoyElGenValue);
$moyenEleveaff=$moyenEleve;

/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : modification de la couleur des notes dans le cas < 10  + calcul de la moyenne générale de P1 si bulletin de P2*/
if (($moyenEleveaff < 10) && ($moyenEleveaff != "")) {
	$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
}
else {
	$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
}
$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'"><B>'.$moyenEleveaff.'</B></font>');

if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	// Moy P1
	//$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	$LCF_moy_P1 = moyGenEleve($noteMoyEleG_P1,$coefEleG_P1);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XmoyElValue,$YmoyElGenValue+3.8);
		if (($LCF_moy_P1 < 10) && ($LCF_moy_P1 != "")) {
			$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
			//$LCF_moy_P1 = "0".$LCF_moy_P1;
		}
		else {
			$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
		}
		
	// Afichage de la moyennes de P1
		$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'"><B>'.$LCF_moy_P1.'</B></font>');
	
	// Calcul de la moyenne générale sur l'année
	$LCF_moy_annee = moyGenEleve($noteMoyEleG_annee,$coefEleG_annee);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XmoyElValue,$YmoyElGenValue+7.6);
		if (($LCF_moy_annee < 10) && ($LCF_moy_annee != "")) {
			$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
		}
		else {
			$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
		}
	
	// Afichage de la moyennes annuelle
		$pdf->SetFont('Arial','',12);
		$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'"><B>'.$LCF_moy_annee.'</B></font>');
	
// Flèche d'évolution des moyennes générales entre P1 et P2
	// $difference_moy_g_P1_P2 = $moyenEleveaff - $LCF_moy_P1;
	// if ($difference_moy_g_P1_P2 >= 0) {$difference_moy_g_P1_P2 = "+".$difference_P1_P2;}
	$LCF_moy_P1_num = round(str_replace(",",".",$LCF_moy_P1), 2);
	$moyenEleveaff_num = round(str_replace(",",".",$moyenEleveaff), 2);
		$pdf->SetFont('Symbol','',14);
		$pdf->SetXY(54,$YmoyenneGeneralT);
		if ($LCF_moy_P1_num < $moyenEleveaff_num) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_progr.'"><strong>­</strong></font>');}
		if ($LCF_moy_P1_num == $moyenEleveaff_num) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_stagn.'"><strong>®</strong></font>');}
		if ($LCF_moy_P1_num > $moyenEleveaff_num) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_regr.'"><strong>¯</strong></font>');}
}

/*
Contenu original
$pdf->WriteHTML("<B>".$moyenEleveaff."</B>");
*/
/*
### FIN MODIF CIE FORMATION ###
*/


/*
### DEBUT AJOUT CIE FORMATION ###
*/
/* DESCRIPTION : on affiche le numéro de classement de l'étudiant s'il n'a pas eu d'absence à des devoirs */
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2
	$LCF_num_periode = 2;		
} else {
	$LCF_num_periode = 1;		
}
$pdf->SetFont('Arial','',9);
if ($_POST["saisie_trimestre"] == "trimestre1" ) {
	$pdf->SetXY($XmoyElValue+80,$YmoyElGenValue);
}else{
	$pdf->SetXY($XmoyElValue+17,$YmoyElGenValue+7);
}
if (is_numeric($CIE_FORM_rang[$idEleve])) {
	$pdf->WriteHTML("<B>CLASSEMENT P".$LCF_num_periode."</B> : <B>".$CIE_FORM_rang[$idEleve]."</B> / ".$CIE_FORM_nb_etudiant_classe);
} else {
	$CIE_FORM_rang_temp = explode('ABS', $CIE_FORM_rang[$idEleve]);
	$CIE_FORM_rang_final[$idEleve] = $CIE_FORM_rang_temp[0];
	$CIE_FORM_abs_final[$idEleve] = $CIE_FORM_rang_temp[1];
	$pdf->WriteHTML("<B>CLASSEMENT P".$LCF_num_periode."</B> : <B>".$CIE_FORM_rang_final[$idEleve]."</B> / ".$CIE_FORM_nb_etudiant_classe);
	$pdf->SetXY($XmoyElValue+50,$YmoyElGenValue+4);
//	$pdf->WriteHTML($CIE_FORM_abs_final[$idEleve]." absence(s) lors d'évaluation(s)");
//	$pdf->SetXY($XmoyElValue+50,$YmoyElGenValue+7);
	$pdf->SetFont('Arial','',7);
//	$pdf->WriteHTML('<font color="#666666"><i>Classement non significatif en raison des</i></font>');
//	$pdf->SetXY($XmoyElValue+50,$YmoyElGenValue+10);
//	$pdf->WriteHTML('<font color="#666666"><i>absences mais donné à titre indicatif</i></font>');
	
}
/*
### FIN AJOUT CIE FORMATION ###
*/
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
// fin affichage moy eleve
if ($affadmission == "oui") {
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XmoyElValue+15,$YmoyElGenValue);
	if ($moyenEleve >= 10) {
		$pdf->WriteHTML('<font color="red"><b>ADMIS</b></font>');	
	}else{
		$pdf->WriteHTML('<font color="red"><b>NON ADMIS</b></font>');	
	}
}

//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
$pdf->SetFont('Arial','',10);
$pdf->SetXY($XmoyClasseGValue,$YmoyClasseGValue);

$moyenClasseGenaff=$moyenClasseGen;
if (($moyenClasseGenaff < 10) && ($moyenClasseGenaff != "")) { $moyenClasseGenaff="0".$moyenClasseGenaff; }
$pdf->WriteHTML($moyenClasseGenaff);

$moyenClasseMinaff=$moyenClasseMin;
$pdf->SetXY($XmoyClasseMinValue,$YmoyClasseMinValue);
if (($moyenClasseMinaff < 10) && ($moyenClasseMinaff != "")) { $moyenClasseMinaff="0".$moyenClasseMinaff; }
$pdf->WriteHTML($moyenClasseMinaff);

$moyenClasseMaxaff=$moyenClasseMax;
$pdf->SetXY($XmoyClasseMaxValue,$YmoyClasseMaxValue);
if (($moyenClasseMaxaff < 10) && ($moyenClasseMaxaff != "")) { $moyenClasseMaxaff="0".$moyenClasseMaxaff; }
$pdf->WriteHTML($moyenClasseMaxaff);
// fin de la calcul de min et max

// fin affichage

/*
### DEBUT AJOUT CIE FORMATION ###
*/
/* DESCRIPTION : ajout de la légende */
$pdf->SetFont('Arial','',6);
$pdf->SetXY(6,$YmoyClasseGValue+14);
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
$pdf->WriteHTML("<I><U>Colonne P1</U> : moy. période 1 et évolution | <U>Colonne P2</U> : moy. période 2 | <U>Moy. rouge</U> = moy. inférieure à 10 | <U>Moy. bleue</U> = moy. non significative en raison d'absences aux évaluations</I>");
} else {
$pdf->WriteHTML("<I><U>Moy. rouge</U> = moy. inférieure à 10 | <U>Moy. bleue</U> = moy. non significative en raison d'absences aux évaluations</I>");

}
/*
### FIN AJOUT CIE FORMATION ###
*/



/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : modification du titre des responsables de filière et abaissement du cadre des commentaires*/
// cadre appréciation
$Ycom=$YMoyGE + 21;
$EpaisCom=30;
/*
Contenu original
// cadre appréciation
$Ycom=$YMoyGE + 15;
$EpaisCom=30;
*/
/*
### FIN MODIF CIE FORMATION ###
*/


$YcomP1=$Ycom + 1;
$YcomP2=$YcomP1 + 10;
$YcomP3=$YcomP2 + 5;
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(220);
$pdf->SetXY(5,$Ycom);
$pdf->MultiCell(204,$EpaisCom,'',1,'C',0);
$pdf->SetXY(6,$YcomP1);

/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : modification du titre du cadre des commentaires */
$pdf->WriteHTML("Commentaire du conseil de classe :");
/*
Contenu original
$pdf->WriteHTML($appreciation);
*/
/*
### FIN MODIF CIE FORMATION ###
*/


$pdf->SetFont('Arial','',8);
/* $pdf->WriteHTML($appreciationbis);*/ // MISE EN COMMENTAIRE CIE FORMATION

// commentaire direction
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(7,$YcomP1+5);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(170,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)

$pdf->SetFont('Arial','',10);
$pdf->SetXY(6,$YcomP2);
/* $pdf->WriteHTML($barre); */ // MISE EN COMMENTAIRE CIE FORMATION
$pdf->SetXY(6,$YcomP3);
/* $pdf->WriteHTML($appreciation2); */ // MISE EN COMMENTAIRE CIE FORMATION
$pdf->SetXY(6+74,$YcomP3);
$pdf->SetFont('Arial','',8);
/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : modification du titre des responsables de filière */
	/*$pdf->WriteHTML(" ( Responsable de filière : ". $profp ." )" );*/ // MISE EN COMMENTAIRE CIE FORMATION
/*
Contenu original
	$pdf->WriteHTML(" ( Professeur Principal : ". $profp ." )" );
*/
/*
### FIN MODIF CIE FORMATION ###
*/

$pdf->SetFont('Arial','',9);

// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetXY(7,$YcomP1+20);
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(204,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


//duplicata et signature
$YduplicaSign=$Ycom + 1 + $EpaisCom;
$pdf->SetFont('Arial','',5);
$pdf->SetXY(6,$YduplicaSign);
$pdf->WriteHTML("<I>".$duplicata."</I>");
$pdf->SetFont('Arial','',8);
$pdf->SetXY(120,$YduplicaSign);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',5);
$pdf->SetXY(6,$YduplicaSign+3);
$pdf->WriteHTML($signature2);




// ----------------------------------------------------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$nomEleve=TextNoCarac($nomEleve);
$nomEleve=TextNoAccent($nomEleve);
$prenomEleve=TextNoCarac($prenomEleve);
$prenomEleve=TextNoAccent($prenomEleve);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$nomEleve=preg_replace('/\//',"_",$nomEleve);
$prenomEleve=preg_replace('/\//',"_",$prenomEleve);
if (!is_dir("./data/pdf_bull/$classe_nom")) { mkdir("./data/pdf_bull/$classe_nom"); }
$fichier=urlencode($fichier);
$fichier="./data/pdf_bull/$classe_nom/bulletin_".$nomEleve."_".$prenomEleve."_".$_POST["saisie_trimestre"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage($_POST["saisie_trimestre"],$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { $merge->add("$fichier"); }
$listing.="$fichier ";
$pdf=new PDF();
} // fin du for on passe à l'eleve suivant
$merge->output("./data/pdf_bull/$classe_nom/liste_complete.pdf");
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
	$cmd="gs -q -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=./data/pdf_bull/$classe_nom/liste_complete.pdf -dBATCH $listing";
	$null=system("$cmd",$retval);
}
include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/'.$classe_nom.'.zip';
$bttexte="Récupérer le fichier ZIP des bulletins";
@nettoyage_repertoire('./data/pdf_bull/'.$classe_nom);
@rmdir('./data/pdf_bull/'.$classe_nom);
// --------------------------------------------------------------------------------------------------------------------------
?>
<br><ul><ul>
<input type=button onclick="open('visu_pdf_bulletin.php?id=<?php print $fichier?>&idclasse=<?php print $_POST["saisie_classe"] ?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul></ul>
<?php // ----------------------------------------------------------------------------------------------------------------------------   ?>



<br /><br />
<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION BULLETIN","Classe : $classe_nom");
        	// alertJs("Bulletin créé -- Service Triade");
}else{
	error(0);
}
Pgclose();
?>

<?php
}else {
?>
<br />
<center>
<?php print LANGMESS14?> <br>
<br><br>
<font size=3><?php print LANGMESS15?><br>
<br>
<?php print LANGMESS16?><br>
</center>
<br /><br /><br />
<?php
        }
?>
<!-- // fin  -->
</td></tr></table>
<script language=JavaScript>attente_close();</script>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
// deconnexion en fin de fichier
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();

?>
