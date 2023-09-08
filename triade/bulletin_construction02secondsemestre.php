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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");

$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(20000000);
}

//ab le 10062009
/*
### DEBUT AJOUT CIE FORMATION ###
*/
// Bulletin adapté Version 1.8 (09/01/2009) - Par Philippe GISCLON
// Bulletin adapté Version 1.12 (09/07/2009) - Par Equipe Triade
$CIE_FORM_version_bulletin_triade = "1.11";
$CIE_FORM_date_bulletin_triade = "10/06/2009";
// Philippe GISCLON - La Compagnie de Formation
/* DESCRIPTION : paramètres de configuration */
// Couleur des moyennes inférieures à 10
$CIE_FORM_coul_moy_inf_10 = "#000000"; // rouge : #FF0000
// Couleur des moyennes supérieures à 10
$CIE_FORM_coul_moy_sup_10 = "#000000"; // noir :  #000000
// Couleur des moyennes non significatives pour cause d'absences
$CIE_FORM_coul_moy_abs = "#000000"; // bleu :  #000099
// Couleur des flèches indiquant une progression
$CIE_FORM_coul_fleche_progr = "#000000"; // vert :  #00CC00
// Couleur des flèches indiquant une régression
$CIE_FORM_coul_fleche_regr = "#000000"; // rouge :  #FF0000
// Couleur des flèches indiquant une stagnation
$CIE_FORM_coul_fleche_stagn = "#000000"; // gris :  #666666
/*
### FIN AJOUT CIE FORMATION ###
*/

//fin ab
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

//ab le 10062009
		/*	
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : Modification de l'intitulé semestre => période */
$_POST["typetrisem"] = "semestre";
$_POST["saisie_trimestre"] = "trimestre2";		
if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="Première période"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="Seconde période"; }
}
// Test des données sélectionnées dans le formulaire : si erreur, affich message + renvoi au sélecteur
//$_POST["typetrisem"] = "semestre";
$_POST["type_pdf"] = "global";
$_POST["plageEleve"] = "tous";

if ($_POST["typetrisem"] == "trimestre" || $_POST["type_pdf"] != "global" || $_POST["plageEleve"] != "tous") {
echo "
<script language=\"javascript\">
alert(\"Le bulletin édité par la Compagnie de Formation fonctionne uniquement avec les paramètres suivants : mode semestriel, un PDF pour l'ensemble, Tous les élèves.\");
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

//fin

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      Bulletin du : <?php print ucwords($textTrimestre)?><br> <br>
      Section :  <?php print $classe_nom?><br> <br>
      Année Scolaire : <?php print $anneeScolaire?></br></br>
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l'etablissement
$data=visu_param();
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
}
// fin de la recup


// recherche des dates de debut et fin
//$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

//ab le 10062009

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

		$CIE_FORM_connexion = mysql_connect($CIE_FORM_hostname_connexion, $CIE_FORM_username_connexion, $CIE_FORM_password_connexion) or trigger_error(mysql_error(),E_USER_ERROR); 		
		mysql_select_db($CIE_FORM_database_connexion, $CIE_FORM_connexion);
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
		echo '<div align="center"></br><b>La Compagnie de Formation</b></br><i>Version bulletin v.'.$CIE_FORM_version_bulletin_triade.' - '.$CIE_FORM_date_bulletin_triade.'</i></br></br>Bulletins édités par '.$_SESSION[nom].' '.$_SESSION[prenom].'</br></br></div>';
		echo "- Dénombrement des absences lors d'évaluations : ok</br>"; // affichage d'informations
		/*
		### FIN AJOUT CIE FORMATION ###
		*/

	/*
    ### DEBUT AJOUT CIE FORMATION ###
    */
    /* DESCRIPTION : récup des dates de P1 s'il s'agit d'un bulletin de P2 */
$_POST["saisie_trimestre"] = "trimestre2";
if ($_POST["saisie_trimestre"] == "trimestre2" ) {
	$dateRecup_P1=recupDateTrim("trimestre1");
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

//fin ab

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

// pour le calcul de moyenne classe
$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// ----------------------------

// calcul min et max general
//-------------------------
	$max="";
	$min=1000;
//ab le 10062009
	/*
    ### DEBUT AJOUT CIE FORMATION ###
    */
    /* DESCRIPTION : création de 2 tableaux */
    $CIE_FORM_moy = array();
	$CIE_FORM_rang = array();
    /*
    ### FIN AJOUT CIE FORMATION ###
    */	
//fin ab	
	
	for($g=0;$g<count($eleveT);$g++) {
		// variable eleve
		$idEleveMoyen=$eleveT[$g][4];
		$noteMoyEleG=0;
		$coefEleG=0;
		$moyenEleve2="";
		$ii=0;
		for($t=0;$t<count($ordre);$t++) {
			$idMatiere=$ordre[$t][0];
			$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$t][2]);
			$noteaff=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]);
			$ii++;
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coeffaff;
		                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                		$coefEleG=$coefEleG + $coeffaff;
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

//ab le 10062009
		
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
		$CIE_FORM_string_tmp_abs = $CIE_FORM_moy[$idEleveMoyen]."ABS".$CIE_FORM_nb_total_absences;
		$CIE_FORM_moy[$idEleveMoyen] = $CIE_FORM_string_tmp_abs;
		}
		/*
		### FIN AJOUT CIE FORMATION ###
		*/
		
//fin ab
		if (trim($moyenEleve2) != "") {
			$moyenEleve2=preg_replace('/,/','.',$moyenEleve2);
			$min=preg_replace('/,/','.',$min);
			$max=preg_replace('/,/','.',$max);
			if ($moyenEleve2 <= $min) { $min=$moyenEleve2; }
			if ($moyenEleve2 >= $max) { $max=$moyenEleve2; }
		}
	}
//ab 
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

//fin
	if ($min == 1000) { $min=""; }
	$min=preg_replace('/\./',',',$min);
	$max=preg_replace('/\./',',',$max);
	$moyenClasseMin=$min;
	$moyenClasseMax=$max;
// fin min et max
// -------------

//ab le 11062009
$plageEleve=$_POST["plageEleve"]="tous";
if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
if ($plageEleve == "10") { $dep=0; $nbEleveT=9; }
if ($plageEleve == "20") { $dep=9; $nbEleveT=19; }
if ($plageEleve == "30") { $dep=19; $nbEleveT=29; }
if ($plageEleve == "40") { $dep=29; $nbEleveT=39; }
if ($plageEleve == "50") { $dep=39; $nbEleveT=49; }
if ($plageEleve == "60") { $dep=49; $nbEleveT=59; }
if ($nbEleveT > count($eleveT)) { $nbEleveT=count($eleveT); }

//fin ab0
for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];
	$date_naissance=$eleveT[$j][5];
	$lieu_naissance=$eleveT[$j][6];
//---------------------------------//
// recherche le nombre de retard
$nbretard=0;
$nbretard1=0;
$nbretard2=0;//
$nbabs=0;
$nbabsnj=0;
$nbsanctions=0;

$nbretard=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
$nbretard=count($nbretard);

// recherche le nombre d'absence justif
$nbabs=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
for($o=0;$o<=count($nbabs);$o++) {
        $nbretard1= $nbretard1 + $nbabs[$o][4];
}
if ($nbabs > 1) {
        $nbabs=$nbretard1;
}else {
        $nbabs=$nbretard1;
}

// recherche le nombre d'absence non justif
$nbabsnj=nombre_absnj($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
for($o=0;$o<=count($nbabsnj);$o++) {
        $nbretard2= $nbretard2 + $nbabsnj[$o][4];

}
if ($nbabsnj > 1) {
        $nbabsnj=$nbretard2;
}else {
        $nbabsnj=$nbretard2;
}

// recherche le nombre de sanction

$nbsanctions=nombre_Sanc($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
$nbsanctions=count($nbsanctions);
//

$nbexclusions=nombre_Exclu($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
$nbexclusions=count($nbexclusions);


//---------------------------------//



$pdf->AddPage();
$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
$pdf->SetCreator("DELAFOSSE");
$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
$pdf->SetAuthor("DELAFOSSE. - http://delafosse.martyrsdkr.net"); 


// declaration variable
$coordonne0=strtoupper($nom_etablissement."<br> Lycee Technique d'Industrie");
$coordonne1=$adresse;
$coordonne2=$postal." - ".ucwords($ville);
$coordonne3="Téléphone : ".$tel;
$coordonne4="E-mail : ".$mail;


$titre="<B>Bulletin scolaire du ".$textTrimestre."</B>";

$nomEleve=strtoupper(trim($nomEleve));
$prenomEleve=trim($prenomEleve);
$nomprenom=trunchaine("<b>$nomEleve $prenomEleve</b>",40);
$abnais=explode('-',$date_naissance);
$abdtnjj=$abnais[2];
$abdtnmm=$abnais[1];
$abdtnaa=$abnais[0];
$datenaissance=$abdtnjj."/".$abdtnmm."/".$abdtnaa;
$lieu_naissance=trim($lieu_naissance);

$infoeleve="Nom de l'élève : $nomprenom    Date Naissance: <b>$datenaissance</b>   A: <b>$lieu_naissance</b>";
$infoeleve2="Classe : ";
$infoeleveclasse=strtoupper($classe_nom);

$titrenote1="Disciplines";
$titrenote2="Elève";
$titrenote3="Classe";
$titrenote4="Appréciations des enseignants :";
$soustitre4b="C.C.";
$soustitre5="Exam.";
$soustitre6="Moy.";
//AB
$soustitre61="Coef.";
$soustitre62="Coef*Moy";
$soustitre63="Moy S1.";
//
$soustitre7="Mini";
$soustitre8="Maxi";
$soustitre9="Moy.";


$idprofp=rechercheprofp($_POST["saisie_classe"]);
$profp=recherche_personne($idprofp);

//modification abadji 19032008

///FIN MODIFICATION

$appreciation="Bilan des absences et retards : <br>    | Absence(s)   |                 |";
$apprec1="| Retard(s)       |                 |";

$apprecsanc="Sanctions : ";
$apprecsanc1="| Travail         |                                    |";
$apprecsanc2="| Assiduité     |                                    |";
$apprecsanc3="| Conduite     |                                    |";

/*
<br>|--------------------------|<br>|Absence(s)  |              |<br>|--------------------------|".$nbretard."  / ".$nbabs." absence(s) justifiée(s) par cours / ".$nbabsnj." absence(s) non justifiée(s) par cours / <br><br>  ".$nbsanctions." Avertissement(s) / ".$nbexclusions."  Journée(s) d'exclusion.
*/
$barre="_____________________";
$barresanc="______________________________";
//$barre="|--------------|--------------|-";

//$barre="____________________________________________________________________________________________";
$appreciation2="<br>Observations et appréciations de l'équipe pédagogique : ";

$apprecobs1="| Félicitation               |                   |";
$apprecobs2="| Encouragement       |                   |";
$apprecobs3="| Tableau d'honneur  |                   |";
$apprecobs4="| Passable                   |                   |";
$apprecobs5="| Faible                        |                   |";
$apprecobs6="| Avertissement          |                   |";
$apprecobs7="| Blâme                        |                   |";
$barreobser="____________________________";
//FIN D'ANNEEE
$apprecobs8="| Admis en classe supérieure |         |";
$apprecobs9="| Autorisé à redoubler             |         |";
$apprecobs10="| Non autorisé à redoubler      |         |";
$apprecobs11="| Ne sera pas repris                 |         |";
$apprecobs12="| Proposé à être réorienté       |         |";
//$apprecobs13="| Moyenne 1er Semestre   |                     |";
//$apprecobs14="| Moyenne Annuelle          |                     |";
$apprecobs15="| Rang                                 |                     |";
$barreobser2="_______________________________";
$barreobser3="__________________________________";
$barrevert="|                                           |                     |";

//

$duplicata=LANGBULL41;
$signature="Le Proviseur : Babacar Wagane FAYE";
// FIN variables

$devise1="EFFICACITE - RENTABILITE";
$devise2="TECHNIQUE INDUSTRIELLE";

$xtitre=150;  // sans logo
$xcoor0=3;  // sans logo
$ycoor0=3;   // sans logo



// mise en place du 
if (file_exists("./data/image_pers/logologo-bull.jpg")) {
	$ximaged=12;
	$yimaged=11;
	$imagedwidth=38;//28 FM
	$pdf->SetFillColor(255,255,255);
	$pdf->SetXY(10,10); // placement du cadre gauche
	$pdf->MultiCell(48,48,'',1,'L',1);
	$imaged="./data/image_pers/image-droite.jpg";
	$pdf->Image($imaged,$ximaged,$yimaged,$imagedwidth);
	$xlogo=60;
	$ylogo=10;
	$logowidth=59;
	$xcoor0=22;
	$ycoor0=3;
	$xtitre=60; // avec logo
	$pdf->SetFillColor(000);
	$pdf->SetXY($xlogo,$ylogo); // placement du cadre imageLogo-Lisaa.jpg
	$pdf->MultiCell(140,38,'',1,'L',1);
	$logo="./data/image_pers/logo_bulletin.jpg";
	$pdf->Image($logo,$xlogo,$ylogo,$logowidth);

}
// fin du logo
else {

//ABADJI LOGO
	$xlogo=170;
	$ylogo=10;
	$logowidth=30;
	//$xtitre=60; // avec logo
	//$pdf->SetFillColor(000);
	//$pdf->SetXY($xlogo,$ylogo); // placement du cadre imageLogo-Lisaa.jpg
	//$pdf->MultiCell(140,38,'',1,'L',1);
	if (file_exists("./data/image_pers/logo_bulletin.jpg")) {
		$logo="./data/image_pers/logo_bulletin.jpg";
		$pdf->Image($logo,$xlogo-5,$ylogo,$logowidth);
	}

//ABADJI FON

//FIN
// Debut création PDF
// mise en place des coordonnées
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML($coordonne0);
$pdf->SetFont('Arial','',8);
$pdf->SetXY($xcoor0,$ycoor0+10);
$pdf->WriteHTML($coordonne1);
$pdf->SetXY($xcoor0,$ycoor0+15);
$pdf->WriteHTML($coordonne2);
$pdf->SetXY($xcoor0,$ycoor0+20);
$pdf->WriteHTML($coordonne3);
$pdf->SetXY($xcoor0,$ycoor0+25);
$pdf->WriteHTML($coordonne4);
//fin coordonnees

//AJOUT AB 030408
$pdf->SetFont('Arial','B',7);
$pdf->SetXY($xlogo-10,$ylogo-3);
$pdf->WriteHTML($devise1);
$pdf->SetXY($xlogo-10,$ylogo+20);
$pdf->WriteHTML($devise2);


}

//Cadre noir
/*$pdf->SetFillColor(000);
$pdf->SetXY(120,10); // placement du cadre noir droite
$pdf->MultiCell(80,28,'',1,'L',1);
*/

// cadre du haut
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(220);
$pdf->SetXY(60,38); // placement du cadre du Annee de l eleve
$pdf->MultiCell(140,8,'',1,'L',1);



$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(60,46); // placement du cadre du nom de l eleve
$pdf->MultiCell(140,8,'',1,'L',1);
$pdf->SetXY(55+8,50); // placement du nom de l'eleve
$pdf->WriteHTML($infoeleve);
$pdf->SetXY(55+8,46); // placement du prenom de l'eleve
$pdf->WriteHTML($infoeleve2);
$pdf->SetX(70+18+3);
$pdf->SetFont('Arial','',12);
$pdf->WriteHTML($infoeleveclasse);

// Titre
$Pdate="Année Scolaire ".$anneeScolaire;
$periode=$titre." - ".$Pdate;
$pdf->SetXY(76,40);
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(0,0,0);
$pdf->WriteHTML($periode);
// fin titre

/* insertion de la Annee SCOLAIRE
$Pdate="Année Universitaire ".$anneeScolaire;
$pdf->SetFont('Arial','',10);
$pdf->SetXY(150,40);
$pdf->SetTextColor(255,255,255);
$pdf->WriteHTML($Pdate);
// fin d'insertion */



// fin cadre du haut

// cadre des notes
// ---------------
// Barre des titres
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(220);
$pdf->SetXY(10,55.5); //  placement  cadre titre
$pdf->MultiCell(190,10,'',1,'C',1);
$pdf->SetXY(20,56); // placement contenu titre
$pdf->WriteHTML($titrenote1);
$pdf->SetX(58);
$pdf->WriteHTML($titrenote2);
$pdf->SetX(90+20);
$pdf->WriteHTML($titrenote3);
$pdf->SetX(132);
$pdf->WriteHTML($titrenote4);
// fin des titres

// possition des sous-titres
$pdf->SetFont('Arial','',7);
$pdf->SetXY(52,60);
$pdf->WriteHTML($soustitre4b);
$pdf->SetXY(62,60);
$pdf->WriteHTML($soustitre5);
$pdf->SetX(72);
$pdf->WriteHTML($soustitre9.'       '.$soustitre62.' '.$soustitre63);
$pdf->SetX(87+18);
$pdf->WriteHTML($soustitre6);
$pdf->SetX(97+18);
$pdf->WriteHTML($soustitre7);
$pdf->SetX(107+18);
$pdf->WriteHTML($soustitre8);
// fin des sous-titres



// Mise en place des matieres et nom de prof
$Xmat=10;//15
$Ymat=65;
$Xmatcont=11;//16
$Ymatcont=65;

$Xprof=50;//55
$Yprof=$Ymat;
$Xcc=50;//55
$Ycc=$Ymat;
$Xcoeff=$Xcc+10;
$Ycoeff=$Ymat;
$Xmoyeleve=$Xcoeff + 10;
$Ymoyeleve=$Ymat;
$Xmoyclasse=$Xmoyeleve + 15;
$Ymoyclasse=$Ymat;
$Xmoyclassemin=$Xmoyclasse+10;
$Ymoyclassemin=$Ymat;
$Xmoyclassemmaxi=$Xmoyclasse+20;
$Ymoyclassemmaxi=$Ymat;

$XnomProfcont=56;
$YnomProfcont=$Ymatcont;
$Xnote=$Xmoyclasse + 30;
$Ynote=$Ymat;
$XccVal=$Xcc;
$XnotVal=$Xcoeff + 12;
$YnotVal=$Ycoeff + 3;
$XcoeffVal=$Xcoeff + 2;
$YcoeffVal=$Ymat + 3;
$XprofVal=15; // x en nom prof
$YprofVal=$Ymat + 3; // y en nom du prof
$XmoyMatGVal=$Xcoeff + 25 ;
$YmoyMatGVal=$Ycoeff + 3 ;

$nbNoteMin=0;
$nbNotemax=0;
$noteMoyEleG=0;
$coefEleG=0;
$ii=0;
//
//J'enlève du décompte la dernière matière (ORDRE D'AFFICHAGE) qui doit obligatoirement être les commentaires généraux avec ma variable $ledecompte
//$ledecompte=count($ordre) - 1;
$ledecompte=count($ordre);
	for($i=0;$i<$ledecompte;$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$idMatiere=$ordre[$i][0];
	$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
	$nomprof=recherche_personne($ordre[$i][1]);
	$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]); //AB AJOUT COEFF
	$totcoefab=$totcoefab+$coeffaff;
	if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere

        // recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
        $idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);
	
	//print $ledecompte;

	// mise en place des matieres
	$largeurMat=40;
	$hauteurMatiere=9; // taille du cadre matiere
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

	if ($ii == 22) {
		$pdf->AddPage();
		$Xmat=10;//15
		$Ymat=11;
		$Xmatcont=11;//16
		$Ymatcont=11;

		$Xprof=50;//55
		$Yprof=$Ymat;
		$Xcc=50;//55
		$Ycc=$Ymat;
		$Xcoeff=$Xcc+10;
		$Ycoeff=$Ymat;
		$Xmoyeleve=$Xcoeff + 10;
		$Ymoyeleve=$Ymat;
		$Xmoyclasse=$Xmoyeleve + 15;
		$Ymoyclasse=$Ymat;

		$Xmoyclassemin=$Xmoyclasse+10;
		$Ymoyclassemin=$Ymat;
		$Xmoyclassemmaxi=$Xmoyclasse+20;
		$Ymoyclassemmaxi=$Ymat;

		$XnomProfcont=56;
		$YnomProfcont=$Ymatcont;
		$Xnote=$Xmoyclasse + 30;
		$Ynote=$Ymat;
		$XnotVal=$Xcoeff + 12;
		$YnotVal=$Ycoeff + 3;
		$XcoeffVal=$Xcoeff + 1;
		$YcoeffVal=$Ymat + 3;
		$XprofVal=15; // x en nom prof
		$YprofVal=$Ymat + 3-2; // y en nom du prof
		$XmoyMatGVal=$Xcoeff + 25 ;
		$YmoyMatGVal=$Ycoeff + 3 ;

	}


	$pdf->SetFont('Arial','',7);
	$pdf->SetTextColor(000);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetXY($Xmat,$Ymat+0.5);
	$pdf->MultiCell($largeurMat,$hauteurMatiere-2,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<b>'.trunchaine(ucfirst($matiere." ".$coeffaff),29).'<b>');// .strtoupper......
	$Ymat=$Ymat-2 + $hauteurMatiere;
	$Ymatcont=$Ymatcont-2 + $hauteurMatiere;

//ab le 11062009

		/*
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : modification de la taille du texte */
			// ab le 11062009 $pdf->SetFont('Arial','',6);
		/*
		Contenu original$ledecompte
			$pdf->SetFont('Arial','',8);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);*/
		/*
		### DEBUT MODIF CIE FORMATION ###
		*/
		/* DESCRIPTION : augmentation du nombre de caractères pour le nom des matières 
			$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),25).'</B>');*/
		/*
		Contenu original
			$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),19).'</B>');
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/
		// $pdf->WriteHTML('<B>'.trunchaine(sansaccentmajuscule(strtoupper($matiere)),20).'</B>');
		//$Ymat=$Ymat + $hauteurMatiere;
		//$Ymatcont=$Ymatcont + $hauteurMatiere;
		
/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : ajout d'une colonne et déplacement des autres si P2 */
/*if ($_POST["saisie_trimestre"] == "trimestre2" ) {
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve-10,$Ycoeff);
		$pdf->MultiCell(7,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;*/
		// mise en place moyenne eleve P1
		$pdf->SetFont('Arial','',8);
		//$pdf->SetXY($Xmoyeleve+120,$Ymoyeleve);
		//$pdf->MultiCell(7,$hauteurMatiere,'',1,'L',0);
		// mise en place moyenne eleve P2
/*		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+4,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		$pdf->MultiCell(11,$hauteurMatiere,'',1,'L',1);
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
} else {
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
}*/
/*
Contenu original
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
*/
/*
### FIN MODIF CIE FORMATION ###
*/

//fin ab 11062009

	// mise en place de la colonne moyenccmatiere
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcc,$Ycc+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',0);
	$Ycc=$Ycc-2 + $hauteurMatiere;

	// mise en place de la colonne Exam
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff,$Ycoeff+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',0);
	$Ycoeff=$Ycoeff-2 + $hauteurMatiere;

	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->MultiCell(15,$hauteurMatiere-2,'',1,'L',0);
	//$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;

	// mise en place moyenne eleve * COEEF AB
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve+15,$Ymoyeleve+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->MultiCell(15,$hauteurMatiere-2,'',1,'L',0);
	$Ymoyeleve=$Ymoyeleve-2 + $hauteurMatiere;
//FIN AB

//ab le 11062009
/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : recup des moyennes + celles de P1 si bulletin de P2 + moyenne des deux */
$_POST["saisie_trimestre"] = "trimestre2";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
if ($_POST["saisie_trimestre"] == "trimestre2" ) {
			$noteaff_P1=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idprof);
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
}
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
$_POST["saisie_trimestre"] = "trimestre2";
if ($_POST["saisie_trimestre"] == "trimestre2" ) {			
			$noteaff_P1=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut_P1,$dateFin_P1,$idgroupe,$idprof);
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
}
		}
/*
Contenu original
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		}
*/
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
			if (($noteaff1 < 10) && ($noteaff1 != "")) {
				$noteaff1="0".$noteaff1;
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
			}
			else {
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
			}
			//$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'">'.$noteaff1.'</font>');
			
			
		} else {
			// On affiche le nb d'absences dans la matière
			$pdf->SetFont('Arial','',6);
				if ($_POST["saisie_trimestre"] == "trimestre2" ) {// décallage des notes pour la période 2
							$pdf->SetXY($XnotVal+1.2,$YnotVal-3);
				} else {
							$pdf->SetXY($XnotVal,$YnotVal-3);
				}
			//$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy_abs.'">ABS '.$CIE_FORM_tableau_absences[$idEleve][$ordre[$i][0]].' éval.</font>');
			// On affiche la moyenne non significative
			$pdf->SetFont('Arial','',12);
				if ($_POST["saisie_trimestre"] == "trimestre2" ) {// décallage des notes pour la période 2
							$pdf->SetXY($XnotVal+1.2,$YnotVal);
				} else {
							$pdf->SetXY($XnotVal,$YnotVal);
				}
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1 != "")) {
				$noteaff1="0".$noteaff1;
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
			}
			else {
				$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
			}
			//$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy_abs.'">'.$noteaff1.'</font>');
		}
			// On affiche la moyenne de P1 si on est sur un bulletin de P2  -5.5
			if ($_POST["saisie_trimestre"] == "trimestre2" ) { 				
				//$pdf->SetFont('Arial','',7);
				//$pdf->SetXY($XnotVal+15.5,$YnotVal-3);
				$noteaff1_P1=$noteaff_P1;
				// on formate et affiche la note de P1
				if (($noteaff1_P1 < 10) && ($noteaff1_P1 != "")) {
					$noteaff1_P1="0".$noteaff1_P1;
					$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
				}
				else {
					$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
				}
				//$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'">'.$noteaff1_P1.'</font>');
				// On affiche la flèche de progression entre P1 et P2 et la différence
				if ($noteaff1_P1 != "" && $noteaff1 != "") {
				$difference_P1_P2 = round($noteaff1 - $noteaff1_P1,2);
				if ($difference_P1_P2 >= 0) {$difference_P1_P2 = "+".$difference_P1_P2;}
					$pdf->SetFont('Symbol','',7);
					$pdf->SetXY($XnotVal-1.8,$YnotVal+1);
					if ($noteaff1_P1 < $noteaff1) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_progr.'"><b>­</b></font>');}
					if ($noteaff1_P1 == $noteaff1) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_stagn.'"><b>®</b></font>');}
					if ($noteaff1_P1 > $noteaff1) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_regr.'"><b>¯</b></font>');}
					$pdf->SetFont('Arial','',5);
						if ($difference_P1_P2 == "+0") {
						$pdf->SetXY($XnotVal+1,$YnotVal+1);
						} else {
						$pdf->SetXY($XnotVal-0.2,$YnotVal+1);
						}
					$pdf->WriteHTML('<font color="#000000">'.$difference_P1_P2.'</font>');
				}
			}
		
		/*
		Contenu original
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($XnotVal,$YnotVal);
		$noteaff1=$noteaff;
		if (($noteaff1 < 10) && ($noteaff1 != "")) { $noteaff1="0".$noteaff1; }
		$pdf->WriteHTML($noteaff1);
		*/
		/*
		### FIN MODIF CIE FORMATION ###
		*/

//fin ab 11062009

//ab le 11062009
/*
### DEBUT MODIF CIE FORMATION ###
*/
/* DESCRIPTION : décallage texte coef si bulletin de P2 */
if ($_POST["saisie_trimestre"] == "trimestre2" ) {
		$pdf->SetXY($XcoeffVal-1.5,$YcoeffVal);
} else {
		$pdf->SetXY($XcoeffVal,$YcoeffVal);
}
/*
Contenu original
		$pdf->SetXY($XcoeffVal,$YcoeffVal);
*/
/*
### FIN MODIF CIE FORMATION ###
*/

//fin ab le 11062009

	// mise en place cadre moyenne 1er semestre
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse+10,$Ymoyclasse+0.5);
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',1);
	//$Ymoyclasse=$Ymoyclasse-2 + $hauteurMatiere;

	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse+20,$Ymoyclasse+0.5);
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',1);
	$Ymoyclasse=$Ymoyclasse-2 + $hauteurMatiere;

	// mise en place moyenne classemin
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclassemin+20,$Ymoyclassemin+0.5);
	$pdf->MultiCell(12,$hauteurMatiere-2,'',1,'L',1);
	$Ymoyclassemin=$Ymoyclassemin-2 + $hauteurMatiere;
	// mise en place moyenne classemin
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclassemmaxi+21,$Ymoyclassemmaxi+0.5);
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',1);
	$Ymoyclassemmaxi= $Ymoyclassemmaxi-2 + $hauteurMatiere;
	// mise en place du cadre commentaire
	$pdf->SetXY($Xnote+21,$Ynote+0.5);
	$pdf->MultiCell(64,$hauteurMatiere-2,'',1,'',1);
	$Ynote=$Ynote-2 + $hauteurMatiere;

	// mise en place des moyenCCmatiere
	$moyencc=moyenneCCMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XccVal+1,$YcoeffVal-2.5);
	$pdf->WriteHTML($moyencc);
	//$YcoeffVal=$YcoeffVal + $hauteurMatiere; COMMENTAIRE ENLEVE LE 270308

	// mise en place des Exam
	//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
	$examaff=recupExam($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+1,$YcoeffVal-2.5);
	$pdf->WriteHTML($examaff);
	$YcoeffVal=$YcoeffVal-2 + $hauteurMatiere;

// mise en place des notes
	$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XnotVal+1,$YnotVal-2.5);
	$pdf->WriteHTML("<B>".$noteaff."<B>");//AB

// mise en place des MOY * COEF
	$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XnotVal+15,$YnotVal-2.5);
	$pdf->WriteHTML("<B>".$noteaff * $coeffaff."<B>");//AB
	
//ab le 11062009
// mise en place Moyenne premier semestre
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XnotVal+24,$YnotVal-2.5);
	$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'">'.$noteaff1_P1.'</font>');
	//$pdf->WriteHTML("<B>".$noteaff * $coeffaff."<B>");//AB$noteaff1_P1.' '. * $coeffaff
	$totcoefab2=$totcoefab2 + $coeffaff;
	$totnotab2=$totnotab2 + $noteaff * $coeffaff;
	$YnotVal=$YnotVal-2 + $hauteurMatiere;

//
/*	$totcoefab2=$totcoefab2 + $coeffaff;
	$totnotab2=$totnotab2 + $noteaff * $coeffaff;
	$YnotVal=$YnotVal-2 + $hauteurMatiere;*/


//	$YnotVal=$YnotVal + $hauteurMatiere;
	// mise en place des moyennes de classe
	if ($idgroupe == "0") {
           // idMatiere,datedebut,dateFin,idclasse
           $moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
        }else {
           $moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
        }
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($XmoyMatGVal+20,$YmoyMatGVal-2.5);
	$pdf->WriteHTML($moyeMatGen);

	$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
	// calcul du min et du max
	if ($idgroupe == "0") {   // non matiere affectée à un groupe

		$max="";
		$min=1000;
		for($g=0;$g<count($eleveT);$g++) {
			// variable eleve
			$idEleveMoyen=$eleveT[$g][4];
			
				$valeur=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
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
			// variable eleve
			$idEleveMoyen=$eleveTg[$g];
			$valeur=moyenneEleveMatiereGroupe($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
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
	$XmoyMatGenMinVal=$XmoyMatGVal + 31;//11
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal-2.5);
	$pdf->WriteHTML($moyeMatGenMin);
	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 42;//21
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal-2.5);
	$pdf->WriteHTML($moyeMatGenMax);

	$YmoyMatGVal=$YmoyMatGVal-2 + $hauteurMatiere;

	$Ycom=$YmoyMatGVal - 12;

	// mise en place des commentaires

	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy

	$Xcom=$XmoyMatGenMaxVal + 20;//10

	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom+4);
	$pdf->MultiCell(84,$confPolice[1],$commentaireeleve,'','','L',0);
	


	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	//$coeffaffmoyab=$coeffaffmoyab+recupCoeff($ordre[$i][0],$idClasse,$ordre[$ii][2]); //AB----------
	//$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal,$YprofVal);
	$profAff=recherche_personne($profAff);
	$pdf->WriteHTML(trunchaine($profAff,27));
	$YprofVal=$YprofVal-2 + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
			//$noteMoyEleGTempoab = $noteaff * recupCoeff($ordre[$i][0],$idClasse,$ordre[$ii][2]);
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
             //$noteMoyEleGab=$noteMoyEleGab + $noteMoyEleGTempoab;
                $coefEleG=$coefEleG + $coeffaff;
             //$coefEleGab=$coefEleGab + recupCoeff($ordre[$i][0],$idClasse,$ordre[$ii][2]);
			 //moyenne général DELAFOSSE AB LE 13062009
			 //$moyGElevltid = $noteMoyEleG * 2;
			 //
	}

//ab le 11062009
	// Calcul de la moyenne de P1 sur bulletin de P2 + moyenne année
$_POST["saisie_trimestre"] = "trimestre2";
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

//fin ab 11062009

}
// fin de la mise en place des matiere

// fin notes
// --------

// cadre moyenne generale
$YmoyenneGeneral=$Ymoyclasse;

if ($YmoyenneGeneral > 220) {
	$pdf->AddPage();
	$YmoyenneGeneral=20;
}
$LargeurMG=$largeurMat;
$YmoyenneGeneralT=$YmoyenneGeneral + 2;
$XMoyGE= 10 + 10 + 10 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 10 + 5;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;


$pdf->SetFont('Arial','',8);
$pdf->SetXY(10,$YmoyenneGeneral+0.5);
//ab le 11062009
$_POST["saisie_trimestre"] = "trimestre2";
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	//$pdf->MultiCell($LargeurMG,16,'',1,'L',0);
} else {
	//$pdf->MultiCell($LargeurMG,10,'',1,'L',0);
}

$pdf->MultiCell($LargeurMG,9,'',1,'L',0);
//fin ab le 11062009
$pdf->SetXY(12,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>MOYENNE GENERALE </B>");//.$totcoefab
//ab le 11062009
$_POST["saisie_trimestre"] = "trimestre2";
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(17,$YmoyenneGeneralT+3.8);
	$pdf->WriteHTML("Rappel moy. générale S1");
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY(130,$YmoyenneGeneralT+3.8);
	$pdf->WriteHTML("<B>MOYENNE ANNUELLE : </B>");
}



//
$pdf->SetXY($XMoyGE+15,$YMoyGE+0.5);
//$pdf->SetFillColor(255,255,255);// pas de couleur de fond FM
//ab le 11062009
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
//	$pdf->MultiCell(15,16,'',1,'L',1);
//	$pdf->SetXY($XMoyCL,$YMoyGE);
	//$pdf->MultiCell(32,16,'',1,'L',0);
} else {
	//$pdf->MultiCell(15,10,'',1,'L',1);
//	$pdf->SetXY($XMoyCL,$YMoyGE);
	//$pdf->MultiCell(32,10,'',1,'L',0);
}
//fin ab le 11062009
//$pdf->MultiCell(10,9-2,'',1,'L',1);
//$pdf->SetXY($XMoyCL,$YMoyGE+0.5);
//$pdf->MultiCell(41,9-2,'',1,'L',0);

// fin du cadre moyenne generale

// affichage de la moyenne generale eleve
$XmoyElValue=$LargeurMG + 32   ;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);

//$totmoyab=moyGenEleve($noteMoyEleGab,$totcoefab); //AB$noteMoyEleG
$totmoyab2=moyGenEleve($totnotab2,$totcoefab2); //AB

$pdf->SetFont('Arial','',8);
$pdf->SetXY($XmoyElValue+2,$YmoyElGenValue);
$pdf->WriteHTML("<B>".$moyenEleve."</B>");//

//POUR LES BULLETINS DE DELAFOSSE 13062009($moyenEleve+ $LCF_moy_P1)/3
$moys2lti = moyGenEleveab($noteMoyEleG,$coefEleG);
$moys1lti = moyGenEleveab($noteMoyEleG_P1,$coefEleG_P1);
$moyGElevltidaff = round(((($moys2lti * 2) + $moys1lti)/3),2);
//

//ab le 11062009
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
//$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'"><B>'.$moyenEleveaff.'</B></font>');

$_POST["saisie_trimestre"] == "trimestre2";
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2			
	// Moy P1
	//$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	$LCF_moy_P1 = moyGenEleve($noteMoyEleG_P1,$coefEleG_P1);
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XmoyElValue+22,$YmoyElGenValue+3.8);
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
	$pdf->SetXY($XmoyElValue+100,$YmoyElGenValue+3.8);
		if (($moyGElevltidaff < 10) && ($moyGElevltidaff != "")) {
			$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_inf_10;
		}
		else {
			$CIE_FORM_coul_moy = $CIE_FORM_coul_moy_sup_10;
		}
	
	// Afichage de la moyennes annuelle$LCF_moy_annee
		$pdf->SetFont('Arial','',12);
		$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_moy.'"><B>'.$moyGElevltidaff.'</B></font>');
	
// Flèche d'évolution des moyennes générales entre P1 et P2
	// $difference_moy_g_P1_P2 = $moyenEleveaff - $LCF_moy_P1;
	// if ($difference_moy_g_P1_P2 >= 0) {$difference_moy_g_P1_P2 = "+".$difference_P1_P2;}
	$LCF_moy_P1_num = round(str_replace(",",".",$LCF_moy_P1), 2);
	$moyenEleveaff_num = round(str_replace(",",".",$moyenEleveaff), 2);
		$pdf->SetFont('Symbol','',14);
		$pdf->SetXY(56,$YmoyenneGeneralT);
		//if ($LCF_moy_P1_num < $moyenEleveaff_num) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_progr.'"><strong>­</strong></font>');}
		//if ($LCF_moy_P1_num == $moyenEleveaff_num) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_stagn.'"><strong>®</strong></font>');}
		//if ($LCF_moy_P1_num > $moyenEleveaff_num) {$pdf->WriteHTML('<font color="'.$CIE_FORM_coul_fleche_regr.'"><strong>¯</strong></font>');}
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
$_POST["saisie_trimestre"] == "trimestre2";
if ($_POST["saisie_trimestre"] == "trimestre2" ) { // Si on est en bulletin de P2
	$LCF_num_periode = 2;		
} else {
	$LCF_num_periode = 1;		
}


$pdf->SetFont('Arial','',9);
$pdf->SetXY($XmoyElValue+70,$YmoyElGenValue);
if (is_numeric($CIE_FORM_rang[$idEleve])) {
	$pdf->WriteHTML("<B>RANG S".$LCF_num_periode."</B> : <B>".$CIE_FORM_rang[$idEleve]."</B> / ".$CIE_FORM_nb_etudiant_classe);
} else {
	$CIE_FORM_rang_temp = explode('ABS', $CIE_FORM_rang[$idEleve]);
	$CIE_FORM_rang_final[$idEleve] = $CIE_FORM_rang_temp[0];
	$CIE_FORM_abs_final[$idEleve] = $CIE_FORM_rang_temp[1];
	$pdf->WriteHTML("<B>RANG S".$LCF_num_periode."</B> : <B>".$CIE_FORM_rang_final[$idEleve]."</B> / ".$CIE_FORM_nb_etudiant_classe);
	$pdf->SetXY($XmoyElValue-10,$YmoyElGenValue+7);
	$pdf->WriteHTML($CIE_FORM_abs_final[$idEleve]." absence(s) lors d'évaluation(s)");
	$pdf->SetXY($XmoyElValue+1,$YmoyElGenValue+9);
	$pdf->SetFont('Arial','',7);
	//$pdf->WriteHTML('<font color="#666666"><i>Classement non significatif en raison des</i></font>');
	$pdf->SetXY($XmoyElValue-10,$YmoyElGenValue+12);
	//$pdf->WriteHTML('<font color="#666666"><i>absences mais donné à titre indicatif</i></font>');
	
}
/*
### FIN AJOUT CIE FORMATION ###
*/


//fin ab 11062009
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$moys2lti = 0;
$moys1lti = 0;
$moyGElevltidaff = 0;
$noteMoyEleG_P1 = 0;
$coefEleG_P1 = 0;
// fin affichage moy eleve


//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
$pdf->SetFont('Arial','',7);
$pdf->SetXY($XmoyClasseGValue+20,$YmoyClasseGValue);
$pdf->WriteHTML($moyenClasseGen);
$pdf->SetXY($XmoyClasseMinValue+21,$YmoyClasseMinValue); 
$pdf->WriteHTML($moyenClasseMin); 
$pdf->SetXY($XmoyClasseMaxValue+22,$YmoyClasseMaxValue); 
$pdf->WriteHTML($moyenClasseMax); 
// fin de la calcul de min et max

// fin affichage

// mise en place des commentaire_gen
$commentairegen=cherche_com_gen($idEleve,$idClasse,$_POST["saisie_trimestre"]);



// cadre appréciation
$Ycom=$YMoyGE + 9;//10
$EpaisCom=50;//30->40


//$YcomPL2=$Ycom + 1;
//$YcomP1=$Ycom + 1;

$YcomP1=$Ycom + 0.3;
$YcomPL0=$YcomP1 + 1.9;
$YcomPL1=$YcomPL0 + 3;
$YcomPC1=$YcomPL1 + 3.5;
$YcomPL2=$YcomPC1 + 0.2;

$YcomP2=$YcomP1 + 6;//10
$YcomP3=$YcomP2 + 5;
$YcomP4=$YcomP3 + 5;//lisaa
$pdf->SetFont('Arial','',8);
//$pdf->SetFillColor(220);/ pas de couleur de fond FM
$pdf->SetXY(10,$Ycom+1);
$pdf->MultiCell(190,$EpaisCom,'',1,'C',0);
$pdf->SetXY(13,$YcomP1);
$pdf->WriteHTML("<B>".$appreciation."</B>");
$pdf->SetXY(13,$YcomPL0);
$pdf->WriteHTML($barre);
$pdf->SetXY(13,$YcomPL1);
$pdf->WriteHTML($barre);
$pdf->SetXY(13-0.5,$YcomPC1);
$pdf->WriteHTML("<B>".$apprec1."</B>");
$pdf->SetXY(13,$YcomPL2);
$pdf->WriteHTML($barre);

//PARTIE SANCTION
$pdf->SetXY(120,$YcomP1);
$pdf->WriteHTML("<B>".$apprecsanc."</B>");
$pdf->SetXY(125,$YcomPL0 - 0.5);
$pdf->WriteHTML($barresanc);
$pdf->SetXY(125,$YcomP1 + 4.5);
$pdf->WriteHTML("<B>".$apprecsanc1."</B>");
$pdf->SetXY(125,$YcomPL1);
$pdf->WriteHTML($barresanc);
$pdf->SetXY(125,$YcomP1 + 8.5);
$pdf->WriteHTML("<B>".$apprecsanc2."</B>");
$pdf->SetXY(125,$YcomPL1 + 4);
$pdf->WriteHTML($barresanc);
$pdf->SetXY(125,$YcomP1 + 12.5);
$pdf->WriteHTML("<B>".$apprecsanc3."</B>");
$pdf->SetXY(125,$YcomPL1 + 8);
$pdf->WriteHTML($barresanc);

//FIN
//FIN DANNEE
/*
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs8."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs9."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs10."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs11."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs12."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs13."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs14."</B>");
$pdf->SetXY(14,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(14,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs15."</B>");
*/
$pdf->SetXY(14,$YcomP3 + 8.8);
$pdf->WriteHTML($barreobser2);
$pdf->SetXY(14,$YcomP3 + 12);
$pdf->WriteHTML("<B>".$apprecobs8."</B>");
$pdf->SetXY(14,$YcomP3 + 12.8);
$pdf->WriteHTML($barreobser2);
$pdf->SetXY(14,$YcomP3 + 16);
$pdf->WriteHTML("<B>".$apprecobs9."</B>");
$pdf->SetXY(14,$YcomP3 + 16.8);
$pdf->WriteHTML($barreobser2);
$pdf->SetXY(14,$YcomP3 + 20);
$pdf->WriteHTML("<B>".$apprecobs10."</B>");
$pdf->SetXY(14,$YcomP3 + 20.8);
$pdf->WriteHTML($barreobser2);
$pdf->SetXY(14,$YcomP3 + 24);
$pdf->WriteHTML("<B>".$apprecobs11."</B>");
$pdf->SetXY(14,$YcomP3 + 24.6);
$pdf->WriteHTML($barreobser2);
$pdf->SetXY(14,$YcomP3 + 28);
$pdf->WriteHTML("<B>".$apprecobs12."</B>");
$pdf->SetXY(14,$YcomP3 + 28.8);
$pdf->WriteHTML($barreobser2);
$pdf->SetXY(142,$YcomP3 + 8.8);
//$pdf->WriteHTML($barreobser3);
$pdf->SetXY(142,$YcomP3 + 11.5);
//$pdf->WriteHTML($barrevert);

$pdf->SetXY(142,$YcomP3 + 13.8);
$pdf->WriteHTML("<B>".$apprecobs13."</B>");
$pdf->SetXY(142,$YcomP3 + 15.5);
//$pdf->WriteHTML($barrevert);

$pdf->SetXY(142,$YcomP3 + 16.8);
//$pdf->WriteHTML($barreobser3);
$pdf->SetXY(142,$YcomP3 + 19.5);
//$pdf->WriteHTML($barrevert);

$pdf->SetXY(142,$YcomP3 + 21.8);
$pdf->WriteHTML("<B>".$apprecobs14."</B>");
$pdf->SetXY(142,$YcomP3 + 23.5);
//$pdf->WriteHTML($barrevert);

$pdf->SetXY(142,$YcomP3 + 24.8);
//$pdf->WriteHTML($barreobser3);
$pdf->SetXY(142,$YcomP3 + 27.5);
//$pdf->WriteHTML($barrevert);

$pdf->SetXY(142,$YcomP3 + 29.8);
//$pdf->WriteHTML("<B>".$apprecobs15."</B>");
$pdf->SetXY(142,$YcomP3 + 31.5);
//$pdf->WriteHTML($barrevert);

$pdf->SetXY(142,$YcomP3 + 32);
$pdf->WriteHTML($barreobser3);
//
//PARTIE OBSERVATION
$pdf->SetXY(90,$YcomP3 + 5);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 8);
$pdf->WriteHTML("<B>".$apprecobs1."</B>");
$pdf->SetXY(90,$YcomP3 + 8.8);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 12);
$pdf->WriteHTML("<B>".$apprecobs2."</B>");
$pdf->SetXY(90,$YcomP3 + 12.8);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 16);
$pdf->WriteHTML("<B>".$apprecobs3."</B>");
$pdf->SetXY(90,$YcomP3 + 16.8);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 20);
$pdf->WriteHTML("<B>".$apprecobs4."</B>");
$pdf->SetXY(90,$YcomP3 + 20.6);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 24);
$pdf->WriteHTML("<B>".$apprecobs5."</B>");
$pdf->SetXY(90,$YcomP3 + 24.8);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 28);
$pdf->WriteHTML("<B>".$apprecobs6."</B>");
$pdf->SetXY(90,$YcomP3 + 28.8);
$pdf->WriteHTML($barreobser);
$pdf->SetXY(90,$YcomP3 + 32);
$pdf->WriteHTML("<B>".$apprecobs7."</B>");
$pdf->SetXY(90,$YcomP3 + 32.8);
$pdf->WriteHTML($barreobser);
//
$pdf->SetXY(13,$YcomP3);
$pdf->WriteHTML("<B>".$appreciation2."</B>");
$pdf->SetXY(55,$YcomP4);
$pdf->MultiCell(100,3,$commentairegen,'','','L',0);

$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(13,$YcomP4+5);
$pdf->SetFont('Arial','',7);
$pdf->MultiCell(170,3,$commentairedirection,'','','L',0); // commentaire de la direction (visa)


// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
$pdf->SetXY(13,$YcomP4+5+7);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(170,3,$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


//duplicata et signature
$YduplicaSign=$Ycom -5 + $EpaisCom;//+1
$pdf->SetFont('Arial','',5);
$pdf->SetXY(16,$YduplicaSign +5);//+0
$pdf->WriteHTML("<I>".$duplicata."</I>");
$pdf->SetFont('Arial','',7);
$pdf->SetXY(130,$YduplicaSign+9);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',6);
//$pdf->SetXY(16,$YduplicaSign );//+15
//$pdf->WriteHTML($signature2);

// fin duplicata


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


<br /> </br />
<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION BULLETIN","Classe : $classe_nom");
        //alertJs("Bulletin créé -- Service Triade");
}
else{
	error(0);
}
Pgclose();
?>

<?php
}
else {
?>
<center>
<br>
<?php print LANGMESS14?></br>
<br>
<?php print LANGMESS15?></br>
<br>
<?php print LANGMESS16?><br>
</br>
<?php
        }
?>
</center>
</form>

<!-- // fin  --></td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language=JavaScript>attente_close();</script>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();
?>
