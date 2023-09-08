<?php
session_start();
//error_reporting(0);
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
	set_time_limit(900);
}
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
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
$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
if (count($valeur)) {
    if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3"; }
    }

    if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; }
    }

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
$classe_nom_aff=preg_replace('/_/','',$data[0][1]);

$hauteurphoto=$_POST["hauteurphoto"];
$largeurphoto=$_POST["largeurphoto"];
$hauteurlogo=$_POST["hauteurlogo"];
$largeurlogo=$_POST["largeurlogo"];
$avecexamenblanc=$_POST["avecexamenblanc"];
$moyensousmatiere=$_POST["moyensousmatiere"];
$affichemoyengeneral=$_POST["affichemoyengeneral"];
$affichematierecoefzero=$_POST["affichematierecoefzero"];
$abssconet=$_POST["abssconet"];
$hauteurMatiere=$_POST["hauteurmatiere"];
$separation=$_POST["separation"];
$affichesignatureprofp=$_POST["affichesignatureprofp"];
$affichecommentaireprofp=$_POST["affichecommentaireprofp"];
$affichedistinction=$_POST["affichedistinction"];
$affichevisascolaire=$_POST["affichevisascolaire"];


if (trim($hauteurphoto) == "") {
	$hauteurphoto=16.3;
	$largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
	$hauteurlogo=25;
	$largeurlogo=25;
}

config_param_ajout($hauteurlogo,"hauteurlogo");
config_param_ajout($largeurlogo,"largeurlogo");
config_param_ajout($hauteurphoto,"hauteurphoto");
config_param_ajout($largeurphoto,"largeurphoto");
config_param_ajout($avecexamenblanc,"avecexamenblanc");
config_param_ajout($moyensousmatiere,"moyensousmatiere");
config_param_ajout($affichemoyengeneral,"affichemoyengeneral");
config_param_ajout($affichematierecoefzero,"affichematierecoefzero");
config_param_ajout($hauteurMatiere,"hauteurMatBull208");
config_param_ajout($abssconet,"abssconet");
config_param_ajout($separation,"separation");
config_param_ajout($affichesignatureprofp,"affichesignatureprofp");
config_param_ajout($affichecommentaireprofp,"affichecommentaireprofp");
config_param_ajout($affichedistinction,"affichedistinction");
config_param_ajout($affichevisascolaire,"affichevisascolaire");


// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom_aff?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l etablissement
$data=visu_paramViaIdSite(chercheIdSite($_POST["saisie_classe"]));
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
}
// fin de la recup


$idliste="";
$data=aff_grp_bull_leap("bulletinLeap_1",$_POST["saisie_classe"]);
$idliste=$data[0][1];
$nomdugroupe1=$data[0][2];
$idliste=preg_replace("/[\{\}]/",'',$idliste);
$tabnomdugroupe1=explode(",",$idliste);
foreach($tabnomdugroupe1 as $value) {
	// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
	$listenomdugroupe1.=chercheMatiereNom($value).",";
}


$idliste="";
$data=aff_grp_bull_leap("bulletinLeap_2",$_POST["saisie_classe"]);
$idliste=$data[0][1];
$nomdugroupe2=$data[0][2];
$idliste=preg_replace("/[\{\}]/",'',$idliste);
$tabnomdugroupe2=explode(",",$idliste);
foreach($tabnomdugroupe2 as $value) {
	// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
	$listenomdugroupe2.=chercheMatiereNom($value).",";
}

$idliste="";
$data=aff_grp_bull_leap("bulletinLeap_3",$_POST["saisie_classe"]);
$idliste=$data[0][1];
$nomdugroupe3=$data[0][2];
$idliste=preg_replace("/[\{\}]/",'',$idliste);
$tabnomdugroupe3=explode(",",$idliste);
foreach($tabnomdugroupe3 as $value) {
	// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
	$listenomdugroupe3.=chercheMatiereNom($value).",";
}
$nomdugroupe1=$nomdugroupe1;
$nomdugroupe2=$nomdugroupe2;
$nomdugroupe3=$nomdugroupe3;


if (MODNAMUR0 == "oui") {
	$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
}

// recherche des dates de debut et fin
//$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);


// Recuperation date Trimestre 1
if ($triabsconet == 'T2') {
	$dateRecupT1=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecupT1);$j++) {
		$dateDebutT1=$dateRecupT1[$j][0];
		$dateFinT1=$dateRecupT1[$j][1];
	}
	$dateDebutT1=dateForm($dateDebutT1);
	$dateFinT1=dateForm($dateFinT1);
}
// -----------------------------------------------------
if ($triabsconet == 'T3') {
	$dateRecupT2=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecupT2);$j++) {
		$dateDebutT2=$dateRecupT2[$j][0];
		$dateFinT2=$dateRecupT2[$j][1];
	}
	$dateDebutT2=dateForm($dateDebutT2);
	$dateFinT2=dateForm($dateFinT2);
//--------------//
	$dateRecupT1=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecupT1);$j++) {
		$dateDebutT1=$dateRecupT1[$j][0];
		$dateFinT1=$dateRecupT1[$j][1];
	}
	$dateDebutT1=dateForm($dateDebutT1);
	$dateFinT1=dateForm($dateFinT1);
	
}
// -----------------------------------------------------


$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere

$nblimitMatiere=24;
if ($hauteurMatiere <= 20) { $nblimitMatiere=8; }
if ($hauteurMatiere <= 12) { $nblimitMatiere=18; }
if ($hauteurMatiere <= 11) { $nblimitMatiere=19; }
if ($hauteurMatiere <= 10) { $nblimitMatiere=21; }
if ($hauteurMatiere <= 8) { $nblimitMatiere=24; }

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');


include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();

if ($_SESSION["langue"] == "bret") {
        $lang01="Ganet d'an";
        $lang02="Skoliata";
        $lang03="Penngelenner(ez)";
	$lang04="Evezhiadenn ar vuhez skol";
	$lang05="Rener(ez)";
}else{
        $lang01="Né(e) le";
        $lang02="Régime";
	$lang03="Professeur Principal";
	$lang04="Appréciation vie scolaire";
	$lang05="Le Chef d'établissement";
}



$pdf=new PDF();  // declaration du constructeur

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

if  ($avecexamenblanc == "oui") {
	$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
}else{
	$moyenClasseGen=calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
}
if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// ----------------------------


// calcul min et max general
//-------------------------
	$max="";
	$min=1000;
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

			if ($affichematierecoefzero != "oui") {
				$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
				if ($coeffaff == "0.00") { continue; } 
			}


			if ($avecexamenblanc == "oui") {
				$noteaff=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			}else{
				$noteaff=moyenneEleveMatiereSansExam($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			}

			if ( $noteaff != "" ) {
				$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$t][2]);
				$noteMoyEleGTempo = $noteaff * $coeffaff;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coeffaff;
			}
			unset($noteaff);
			unset($coeffaff);
			
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
		}
		if (trim($moyenEleve2) != "") {
			$moyenEleve2=preg_replace('/,/','.',$moyenEleve2);
			$min=preg_replace('/,/','.',$min);
			$max=preg_replace('/,/','.',$max);
			if ($moyenEleve2 <= $min) { $min=$moyenEleve2; }
			if ($moyenEleve2 >= $max) { $max=$moyenEleve2; }
			$moyenEleveaff=$moyenEleve2;
		}
	}

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
	
	unset($tabsous);

	//---------------------------------//
	// recherche le nombre de retard
	$nbretard=0;
	$nbretard1=0;
	$nbheureabs=0;
	$nbjoursabs=0;
	$nbabs=0;
	$nbabsnonjustifier=0;
	if ($abssconet == "oui") {
		$nbretard=nombre_retard_sconet($idEleve,$triabsconet);
		$nbabs=nombre_abs_sconet($idEleve,$triabsconet);
		$nbabsnonjustifier=nombre_abs_nonjustifie_sconet($idEleve,$triabsconet);
	}else{
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
	}

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
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",30);
	
	$effbordure=1;

	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=trim($classe_nom_aff);

	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	if (trim($nbabs) == "") $nbabs=0;
	if (trim($nbretard) == "") $nbretard=0;

	$appreciation=LANGBULL39;
	if ($abssconet == "oui") {
		$appreciationbis="($nbretard retard(s) / $nbabs absence(s) / $nbabsnonjustifier absence(s) non justifié(s) ) " ;
	}else{
		$appreciationbis="($nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ) " ;
	}

	if ($_SESSION["langue"] == "bret") {
		$appreciation="Heuliañ ar c'hentelioù: $nbretard dale / $nbabs hanter-devezh ezvezañs / $nbheureabs eurvezh ezvezañs";
	}else{
		$appreciation="Bilan Assiduité : $nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ";
	}



	$barre="____________________________________________________________________________________________";
	$appreciation2=LANGBULL40;
//	$duplicata=LANGBULL41 . " - $urlsite - $mail";
	$signature=LANGBULL42;
	$signature2="";
	$signature="";
	// FIN variables

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo

	// mise en place du logo
	$photo=recup_photo_bulletin();
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xlogo=$largeurlogo;
			$ylogo=$hauteurlogo;
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
	$pdf->SetXY($xcoor0,$ycoor0+3.5);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY($xcoor0,$ycoor0+6.5);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY($xcoor0,$ycoor0+9.5);
	$pdf->WriteHTML($coordonne3);
	$pdf->SetXY($xcoor0,$ycoor0+12.5);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate=LANGBULL43." ".$anneeScolaire;
	$pdf->SetFont('Courier','',10);
	$pdf->SetXY(130,3);
	$pdf->WriteHTML($Pdate);
	// fin d'insertion

	// Titre
	$pdf->SetXY($xtitre,15);
	$pdf->SetFont('Courier','',18);
	$pdf->WriteHTML($titre);
	// fin titre

	// cadre du haut
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(220);
	$pdf->SetXY(5,25); // placement du cadre du nom de l eleve
	$pdf->MultiCell(201,20,'',1,'L',1);

	$photoeleve=image_bulletin($idEleve);

	$photo=$photoeleve;
	$xphoto=7;
	$yphoto=26;
	//$photowidth=18;
	//$photoheight=18;
	$photowidth=$largeurphoto;
	$photoheight=$hauteurphoto;
	$Xv1=10;
	$Xv11=111;
	if (!empty($photo)) {
		$photo=$photoeleve;
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
		$Xv1=10+$photowidth;
		$Xv11=110;
	}
	$pdf->SetXY($Xv1,26); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	$pdf->SetXY($Xv1+80,26);
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetXY($Xv1+94,26);
	$pdf->WriteHTML($infoeleveclasse);
	$pdf->SetXY($Xv1+80,30);
	$pdf->WriteHTML("$lang03 : $profp");


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
		if ($_SESSION["langue"] == "bret") {
        		$regime=preg_replace('/demi pension/','Hanter-diabarzhiad(ez)',$regime);
		        $regime=preg_replace('/pensionnaire/','Diabarzhiad(ez)',$regime);
		        $regime=preg_replace('/externe/','Diavaeziad(ez)',$regime);
		        $regime=preg_replace('/interne/','Diabarzhiad(ez)',$regime);
		}
		$class_ant=trim(trunchaine($dataadresse[$ik][10],20));

		$moyennomdugroupe1="";
		$nbnotenomdugroupe1="";
		$moyennomdugroupe2="";
		$nbnotenomdugroupe2="";
		$moyennomdugroupe3="";
		$nbnotenomdugroupe3="";
		    

		$pdf->SetXY($Xv1,30); 
		$pdf->SetFont('Arial','',8);
		$pdf->WriteHTML("N° INE : $numero_eleve ");
		$pdf->SetXY($Xv1,34);
		$pdf->WriteHTML("$lang01 $datenaissance");
		$pdf->SetXY($Xv1,38); 
		$pdf->WriteHTML("$lang02 : $regime ");
		$pdf->SetXY($Xv1+80,34);
		//$class_ant=trunchaine($class_ant,40);
		//$pdf->WriteHTML("Classe ant.: $class_ant ");

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
	$pdf->SetXY(5,46); //  placement  cadre titre
	$pdf->MultiCell(201,8,'',1,'C',1);
	$pdf->SetXY(15,47); // placement contenu titre
	$pdf->WriteHTML($titrenote1);
	$pdf->SetX(60);
	$pdf->WriteHTML($titrenote2);
	$pdf->SetX(87);
	$pdf->WriteHTML($titrenote3);
	$pdf->SetX(120);
	$pdf->WriteHTML($titrenote4);
	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY(48,50);
	$pdf->WriteHTML($soustitre5);
	$pdf->SetX(82);
	$pdf->WriteHTML($soustitre6);
	$pdf->SetX(89);
	$pdf->WriteHTML($soustitre7);
	$pdf->SetX(97);
	$pdf->WriteHTML($soustitre8);
	$pdf->SetX(106);
	$pdf->WriteHTML("T-1");
	$pdf->SetX(113);
	$pdf->WriteHTML("T-2");
	// fin des sous-titres

	$nbs=0;


	// Mise en place des matieres et nom de prof
	$Xmat=5;
	$Ymat=54;
	$Xmatcont=6;
	$Ymatcont=$Ymat;

	$Xprof=45;
	$Yprof=$Ymat;
	$Xcoeff=45;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=$Xmatcont;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32+10;
	$Ynote=$Ymat;
	$XnotVal=$Xcoeff + 12 +10 ;
	$YnotVal=$Ycoeff + 3;
	$XcoeffVal=$Xcoeff + 1;
	$YcoeffVal=$Ymat + 3;
	$XprofVal=20; // x en nom prof
	$YprofVal=$Ymat + 4; // y en nom du prof
	$XmoyMatGVal=$Xcoeff + 26 +10 ;
	$YmoyMatGVal=$Ycoeff + 3 ;

	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
	$coefEleG=0;
	$ii=0;
	$iiii=0;

	$TT=1;

	$XDebut=$Xmat;
	$YDebut=$Ymat;
	
	$posiNoteSous=1;

	for($i=0;$i<count($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);


		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
		
		if (trim($matiere) == "") { continue; }


		if ($affichematierecoefzero != "oui") {
			$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
			if ($coeffaff == "0.00") { continue; } 
		}

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


		// mise en place des matieres
		$largeurMat=42;
		$hauteurTotal+=$hauteurMatiere;
		//si plus de 10 matieres on reinitalise les positions;

		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}

		// fin de la gestion sous matiere
		// ------------------------------
		$iiii++;
		if ($iiii == $nblimitMatiere) {

			$pdf->SetXY($XDebut,$YDebut);
			$hauteurTotal-=$hauteurMatiere;
			$pdf->MultiCell($largeurMat,$hauteurTotal,'','1','L',0);

			$pdf->AddPage();
			$Xmat=5;
			$Ymat=20;
			$YDebut=$Ymat;
			$Xmatcont=6;
			$Ymatcont=20;

			$Xprof=45;
			$Yprof=$Ymat;
			$Xcoeff=45;
			$Ycoeff=$Ymat;
			$Xmoyeleve=$Xcoeff + 10;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve + 15;
			$Ymoyclasse=$Ymat;

			$XnomProfcont=46;
			$YnomProfcont=$Ymatcont;
			$Xnote=$Xmoyclasse + 32 + 10;
			$Ynote=$Ymat;
			$XnotVal=$Xcoeff + 12 +10;
			$YnotVal=$Ycoeff + 3;
			$XcoeffVal=$Xcoeff + 1;
			$YcoeffVal=$Ymat + 3;
			$XprofVal=20; // x en nom prof
			$YprofVal=$Ymat + 4; // y en nom du prof
			$XmoyMatGVal=$Xcoeff + 26 +10	;
			$YmoyMatGVal=$Ycoeff + 3 ;
			$iiii=0;
			$hauteurTotal=0;
		}

		$sousmatiere=trim($ordre[$i][4]);   
		$libelleMatiere=$ordre[$i][5]; 
		$ordrematiere=$ordre[$i][3]; 
		$ii=$i;
		//print "<b>$libelleMatiere</b><br>";
		if (!array_key_exists($libelleMatiere, $tabsous)) { 
		  while(true) {
			$ii++;
			if (verifMatiereAvecGroupe($ordre[$ii][0],$idEleve,$idClasse,$ordre[$ii][2])) {$TT=1;break;}
			if (($sousmatiere != "0") && ($sousmatiere != "")){
				if(!verifMatiereSuivanteCommeSousmatiere($ordre[$ii][0])) { $TT=1;break; }
				$matiereSuivante=chercheMatiereNom3($ordre[$ii][0]);
			//	 print "TT:$TT $libelleMatiere -- $matiereSuivante <br>";
				if ( trim($libelleMatiere) == trim($matiereSuivante)) {
					$TT=$TT+1;
					$tabsous["$libelleMatiere"]=$TT;
				}else{
					$TT=1;
					break;
				}
			}else{
				$TT=1;
				break;
			}
			$sousmatiere=trim($ordre[$ii][4]);
		   }
		}
//		print_r($tabsous);

		$sousmatiere=$ordre[$i][4];
		if (($sousmatiere == "0") && ($sousmatiere=="")) { $sousmatiere=""; }

		//
		$nbs--;
		if ($nbs < 0) { $nbs=0; } 
		if($nbs == 0) {
		$deja=0;
		foreach($tabsous as $key4=>$val4) {
			if ($key4 == $libelleMatiere) {
				$effbordure=0;
				$nbs=$val4;
			}
		}
	}else{
		$effbordure=1;
		$effbordure2=0;
	}
//print $effbordure." $libelleMatiere <br>";

		if ($deja >= 1) {
			$libelleMatiere="";
			$effbordure2=0;
		}else{
			$effbordure2=1;
		}
	
		$YmatComm=$Ymat;
		if (($effbordure == 0 ) && ($matiereSuivante != "")) {
			$pdf->SetXY($Xmat,$Ymat);
			$H=$hauteurMatiere*$nbs;
			$posiNoteSous=$nbs;	
			$pdf->MultiCell($largeurMat,$H,'',1,'L',0);
			$deja++;
			$effbordure2=1;	
		}


                $pdf->SetFillColor(255,255,255);

                $pdf->SetXY($Xmat,$Ymat);
                foreach($tabnomdugroupe1 as $value) {
                        if (trim($idMatiere) == trim($value)) {
                                $pdf->SetFillColor(214,227,188);
                                break;
                        }
                }
                foreach($tabnomdugroupe2 as $value) {
                       if (trim($idMatiere) == trim($value)) {
                               $pdf->SetFillColor(198,217,241);
                               break;
                       }
	        }
	        foreach($tabnomdugroupe3 as $value) {
	              if (trim($idMatiere) == trim($value)) {
	                       $pdf->SetFillColor(253,233,217);
	                       break;
	             }
	       }
		
		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($Xmat,$Ymat);
		if ($separation == "oui") { $effbordure2='1'; }
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',$effbordure2,'L',1);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$libelleMatiere=preg_replace('/0$/','',$libelleMatiere);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($libelleMatiere))),28).'</B>');

		if ($sousmatiere != "") {
			$pdf->SetXY($Xmat+($largeurMat/2),$Ymat+2.5);
			$pdf->SetFont('Arial','',6);
			$sousmatiere=preg_replace('/0$/','',$sousmatiere);
			$pdf->WriteHTML('<I>'.trunchaine(strtolower($sousmatiere),17).'</I>');
		}
		$pdf->SetFont('Arial','',8);
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff+2,$Ycoeff);
		$pdf->MultiCell(8,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;
		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		$pdf->SetFillColor(255,255,0);
		
		if (($effbordure == 0) && ($matiereSuivante != "")) {
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(25,$H,'',1,'L',1);	
		}else{
			if($nbs == 0) {
				$pdf->MultiCell(25,$hauteurMatiere,'',1,'L',1);
			}else{
				$pdf->MultiCell(25,$hauteurMatiere,'',1,'L',1);
			}
		}

		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse+10,$Ymoyclasse);
		$pdf->MultiCell(25,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;


		if (($effbordure == 0 ) && ($matiereSuivante != "")) {
			$pdf->SetXY($Xnote+7,$YmatComm);
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(87,$H,'',1,'L',0);
			$effbordure2=0;
		}

		// mise en place du cadre commentaire
		$pdf->SetXY($Xnote+7,$YmatComm);
		if ($separation == "oui") { $effbordure2='1'; }
		$pdf->MultiCell(87,$hauteurMatiere,'',$effbordure2,'L',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
		unset($noteaff);	
		if ($idgroupe == "0") {
			if ($avecexamenblanc == "oui") {
				$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
				if ($triabsconet == 'T2'){$noteaffT1=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof); }
				if ($triabsconet == 'T3'){
					$noteaffT2=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof); 
					$noteaffT1=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof);
				}
			}else{
				$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
				if ($triabsconet == 'T2'){$noteaffT1=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof); }
				if ($triabsconet == 'T3'){
					$noteaffT2=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof); 
					$noteaffT1=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof);
				}
			}
		}else{
			if ($avecexamenblanc == "oui") {
				$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
				if ($triabsconet == 'T2'){$noteaffT1=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof); }
				if ($triabsconet == 'T3'){
					$noteaffT2=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof); 
					$noteaffT1=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof);
				}
			}else{
				$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
				if ($triabsconet == 'T2'){$noteaffT1=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof); }
				if ($triabsconet == 'T3'){
					$noteaffT2=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof); 
					$noteaffT1=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof);
				}
			}
		}

		if (($sousmatiere != "0") && ($sousmatiere != "")){
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($XnotVal-12,$YnotVal);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			if ($posiNoteSous != 1) $pdf->WriteHTML($noteaff1);
			unset($noteaff1);
			$coefsous=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
			if (( $coefsous != "" ) && ($moyensousmatiere == "non")) {
//				$coefEleGMat=$coefEleGMat + $coefsous;
			}
			if ($noteaff != "") {
				$notesous=$noteaff*$coefsous;
				$notesoustotal1=$notesoustotal1+$notesous;
				$coefsoustotal1=$coefsoustotal1+$coefsous;
			}
			$ip=$i+1;
			if (verifMatiereAvecGroupe($ordre[$ip][0],$idEleve,$idClasse,$ordre[$ip][2])) {
				$matiereSuivante="";
			}else{
				$matiereSuivante=chercheMatiereNom3($ordre[$ip][0]);
			}
			$matiereEnCours=$ordre[$i][5];
			if (trim($matiereEnCours) != trim($matiereSuivante)) {
				$matierepre=$matiereEnCours;
				if ($notesoustotal1 != "") {
					$notesousmoyen=$notesoustotal1/$coefsoustotal1;
					$notesousmoyen=number_format($notesousmoyen,2,'.','');
					$noteaff1=$notesousmoyen;
					if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
				}
				$pdf->SetFont('Arial','',12);
				if ($posiNoteSous == 5) {
					$ajus=$posiNoteSous*4;  //OK
				}elseif($posiNoteSous == 6){
					$ajus=$posiNoteSous*4;  //OK
				}elseif($posiNoteSous == 7){
					$ajus=$posiNoteSous*4;  //OK
				}elseif($posiNoteSous == 4){
					$ajus=$posiNoteSous*3;  //OK
				}elseif($posiNoteSous == 3){
					$ajus=$posiNoteSous*4;
				}elseif($posiNoteSous == 2){
					$ajus=$posiNoteSous*2;  //OK
				}elseif($posiNoteSous == 1){
					$ajus=$posiNoteSous;  //OK
				}elseif($posiNoteSous == 0){
					$ajus=$posiNoteSous;  //OK
				}elseif($posiNoteSous == ""){
					$ajus=$posiNoteSous;  //OK
				}else{
					$ajus=$posiNoteSous*2;
				}
				$pdf->SetXY($XnotVal-5,$YnotVal-$ajus);
				$pdf->WriteHTML($noteaff1);
				$posiNoteSous=1;
				if (( $noteaff1 != "" ) && ($moyensousmatiere == "non")) {
					$coefEleGMat=$coefEleGMat + $coeffaff;
					$noteMoyEleGTempo = $noteaff1 * $coefEleGMat;
					$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
					$coefEleG=$coefEleG + 1;
				}
				unset($coefEleGMat);
				unset($noteMoyEleGTempo);
				unset($notesoustotal1);
				unset($coefsoustotal1);
				$pdf->SetXY(5,$Ymat);
//				$pdf->MultiCell($largeurMat,$hauteurMatiere*$nbs,'',$effbordure2,'L',0);
		
		if ($moyensousmatiere == "non") {

		foreach($tabnomdugroupe1 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff1) != "") {
					$moyennomdugroupe1+= $noteaff1 ;
					$nbnotenomdugroupe1++ ;
				}
			}
		}
		foreach($tabnomdugroupe2 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff1) != "") {
					$moyennomdugroupe2+= $noteaff1 ;
					$nbnotenomdugroupe2++ ;
				}
			}
		}
		foreach($tabnomdugroupe3 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff1) != "") {
					$moyennomdugroupe3+= $noteaff1 ;
					$nbnotenomdugroupe3++ ;
				}
			}
		}
		// ----------------------------------------------------------------------------------------------
		}		
			unset($noteaff1);
			
			}
			$YnotVal=$YnotVal + $hauteurMatiere;

		}else{
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY($XnotVal-5,$YnotVal);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			$pdf->WriteHTML($noteaff1);
			unset($noteaff1);
			$YnotVal=$YnotVal + $hauteurMatiere;
			unset($matiereSuivante);
			$ajus=0;
			if (( $noteaff != "" ) && ($moyensousmatiere == "non")) {
				$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
			        $noteMoyEleGTempo = $noteaff * $coeffaff;
		                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                		$coefEleG=$coefEleG + 1;
			}

		if ($moyensousmatiere == "non") {
			// --------------------------------------------------------------------------------------------------
		foreach($tabnomdugroupe1 as $value) {
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff) != "") {
		//if ($nomEleve == "GASSAMA") 
		//print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
					$moyennomdugroupe1+= $noteaff * $coeffaff;
					$nbnotenomdugroupe1+= $coeffaff ;
				}
			}
		}
		foreach($tabnomdugroupe2 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff) != "") {
					$moyennomdugroupe2+= $noteaff * $coeffaff;
					$nbnotenomdugroupe2+= $coeffaff ;
				}
			}
		}
		foreach($tabnomdugroupe3 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff) != "") {
					$moyennomdugroupe3+= $noteaff * $coeffaff;
					$nbnotenomdugroupe3+= $coeffaff ;
				}
			}
		}
		// ----------------------------------------------------------------------------------------------
		}
	

			
		}

		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($XcoeffVal+2,$YcoeffVal);
		$pdf->WriteHTML($coeffaff);
		$YcoeffVal=$YcoeffVal + $hauteurMatiere;

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
			// idMatiere,datedebut,dateFin,idclasse
			if ($avecexamenblanc == "oui") {
	           		$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
			}else{
				$moyeMatGen=moyeMatGenSansExam($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
			}
		}else {
			if ($avecexamenblanc == "oui") {
	           		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			}else{
				$moyeMatGen=moyeMatGenGroupeSansExam($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			}
    		}
		
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff!="")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->WriteHTML($moyeMatGenaff);

		if ($moyensousmatiere == "oui") {
			// --------------------------------------------------------------------------------------------------
		foreach($tabnomdugroupe1 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff) != "") {
					$moyennomdugroupe1+= $noteaff * $coeffaff;
					$nbnotenomdugroupe1+= $coeffaff ;
				}
			}
		}
		foreach($tabnomdugroupe2 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff) != "") {
					$moyennomdugroupe2+= $noteaff * $coeffaff;
					$nbnotenomdugroupe2+= $coeffaff ;
				}
			}
		}
		foreach($tabnomdugroupe3 as $value) {
		// print "Note : $noteaff - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if (trim($idMatiere) == trim($value)) {
				if (trim($noteaff) != "") {
					$moyennomdugroupe3+= $noteaff * $coeffaff;
					$nbnotenomdugroupe3+= $coeffaff ;
				}
			}
		}
		// ----------------------------------------------------------------------------------------------
		}

		// calcul du min et du max
		if ($idgroupe == "0") {   // non matiere affectée à un groupe
			$max="";
			$min=1000;
			for($g=0;$g<count($eleveT);$g++) {
				// variable eleve
				$idEleveMoyen=$eleveT[$g][4];
				if ($avecexamenblanc == "oui") {
					$valeur=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
				}else{
					$valeur=moyenneEleveMatiereSansExam($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
				}
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
				if ($avecexamenblanc == "oui") {
					$valeur=moyenneEleveMatiereGroupe($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}else{
					$valeur=moyenneEleveMatiereGroupeSansExam($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}
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
	$pdf->SetXY($XmoyMatGenMinVal-3,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff!="")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal-5,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff!="")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;


	// mise en place T-15
	$XcomT=$XmoyMatGenMaxVal+3;
	$pdf->SetFont('Arial','',5.5);
	$pdf->SetXY($XcomT,$Ycom);
	if (($noteaffT1 < 10) && ($noteaffT1!="")) { $noteaffT1="0".$noteaffT1; }
	$pdf->MultiCell(7,$hauteurMatiere,"$noteaffT1",'1','C',0);


	// mise en place T-2
	$XcomT=$XmoyMatGenMaxVal+3;
	$pdf->SetFont('Arial','',5.5);
	$pdf->SetXY($XcomT+7,$Ycom);
	if (($noteaffT2 < 10) && ($noteaffT2!="")) { $noteaffT2="0".$noteaffT2; }
	$pdf->MultiCell(7,$hauteurMatiere,"$noteaffT2",'1','C',0);



	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPoliceViaHauteur($commentaireeleve,$hauteurMatiere);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy
	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom+7,$Ycom+0.5);
	$pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	$pdf->SetFont('Arial','',6);
	//$pdf->WriteHTML($commentaireeleve);
	
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',6);
	if ($hauteurMatiere >= 10) { 
		$pdf->SetXY($XprofVal-14,$YprofVal+1.5);
	}else{
		$pdf->SetXY($XprofVal-14,$YprofVal);
	}
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if (( $noteaff != "" ) && ($moyensousmatiere == "oui")) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}
}
// fin de la mise en place des matiere
//

$pdf->SetXY($XDebut,$YDebut);
$pdf->MultiCell($largeurMat,$hauteurTotal,'','1','L',0);
$pdf->SetXY($Xnote+7,$YDebut);
$pdf->MultiCell(87,$hauteurTotal,'','1','L',0);
$hauteurTotal=0;


// Note Vie Scolaire
if (MODNAMUR0 == "oui") {
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<B>'.'VIE SCOLAIRE'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place de la colonne coeff
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+2,$Ycoeff);
	$pdf->MultiCell(8,$hauteurMatiere,'',1,'L',0);
	$Ycoeff=$Ycoeff + $hauteurMatiere;
	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
	$pdf->SetFillColor(255,255,0);  // couleur du cadre de l'eleve
	$pdf->MultiCell(25,$hauteurMatiere,'',1,'L',1);
	$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse+10,$Ymoyclasse);
	$pdf->MultiCell(25,$hauteurMatiere,'',1,'L',0);
	$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;
	// mise en place du cadre note
	$pdf->SetXY($Xnote+7,$Ynote);
	$pdf->MultiCell(87,$hauteurMatiere,'',1,'',0);
	$Ynote=$Ynote + $hauteurMatiere;

	// mise en place des notes
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
	if ($triabsconet == 'T2') { $noteaffT1=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,"trimestre1"); }
	if ($triabsconet == 'T3') { 
		$noteaffT2=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,"trimestre2"); 
		$noteaffT1=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,"trimestre1");
	}
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XnotVal-5,$YnotVal);
	$pdf->WriteHTML($noteaff);


	$YnotVal=$YnotVal + $hauteurMatiere;
	// mise en place des coeff
	$coeffaff=$coefBull;
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XcoeffVal+2,$YcoeffVal);
	$pdf->WriteHTML($coeffaff);
	$YcoeffVal=$YcoeffVal + $hauteurMatiere;

	// mise en place des moyennes de classe
        $moyeMatGen1=moyeMatGenVieScolaire($_POST["saisie_trimestre"],$idClasse); 
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
	$moyeMatGenaff=$moyeMatGen1;
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

	$pdf->SetFont('Arial','',7);
	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal-3,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff!="")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal-5,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff!="")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	// mise en place T-1
	$XcomT=$XmoyMatGenMaxVal+3;
	$pdf->SetFont('Arial','',5.5);
	$pdf->SetXY($XcomT,$Ycom);
	if (($noteaffT1 < 10) && ($noteaffT1!="")) { $noteaffT1="0".$noteaffT1; }
	$pdf->MultiCell(7,$hauteurMatiere,"$noteaffT1",'1','C',0);
	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place T-2
	$XcomT=$XmoyMatGenMaxVal+3+7;
	$pdf->SetFont('Arial','',5.5);
	$pdf->SetXY($XcomT,$Ycom);
	if (($noteaffT2 < 10) && ($noteaffT2!="")) { $noteaffT2="0".$noteaffT2; }
	$pdf->MultiCell(7,$hauteurMatiere,"$noteaffT2",'1','C',0);
	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;


	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPoliceViaHauteur($commentaireeleve,$hauteurMatiere);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy



	$Xcom=$XmoyMatGenMaxVal + 10+7;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	$pdf->SetFont('Arial','',6);
	
	// mise en place du nom du prof
	$profAff=$persVieScolaire;
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal-14,$YprofVal);
	$pdf->WriteHTML(trunchaine($profAff,20));
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
	if (count($ordre) >= 18) {
		$YmoyenneGeneral=$Ymoyclasse + 2;
	}else{
		$YmoyenneGeneral=$Ymoyclasse + 5;
	}
	if ($YmoyenneGeneral > 242) {
		$pdf->AddPage();
		$YmoyenneGeneral=20;
	}


	$LargeurMG=50;
	$YmoyenneGeneralT=$YmoyenneGeneral + 2;
	$XMoyGE= 10 + 15 + $LargeurMG;
	$YMoyGE=$YmoyenneGeneral - 10;

if ($affichemoyengeneral == "oui") {
	
	$largeurMat=50;

	$YMoyGE=$YmoyenneGeneral;
	$XMoyCL=$XMoyGE + 15;

	$XmoyClasseGValue=$XMoyGE + 10 + 6;
	$YmoyClasseGValue=$YmoyenneGeneralT;
	$XmoyClasseMinValue=$XmoyClasseGValue + 10;
	$YmoyClasseMinValue=$YmoyenneGeneralT;
	$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
	$YmoyClasseMaxValue=$YmoyenneGeneralT;

        if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
        if ($moyenClasseGen == 0) {$moyenClasseGen="";}
        $moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
        $moyenClasseGenaff=$moyenClasseGen;
        if ($moyenClasseGenaff < 10) { $moyenClasseGenaff="0".$moyenClasseGenaff; }
        $moyenClasseMinaff=$moyenClasseMin;
        if ($moyenClasseMinaff < 10) { $moyenClasseMinaff="0".$moyenClasseMinaff; }
        $moyenClasseMaxaff=$moyenClasseMax;
        if ($moyenClasseMaxaff < 10) { $moyenClasseMaxaff="0".$moyenClasseMaxaff; }

	$YmoyenneGeneral=$Ymoyclasse + 1;
	$pdf->SetFont('Arial','B',9);
	$pdf->SetXY(5,$YmoyenneGeneral);
	$pdf->SetFillColor(255,255,0);
	$pdf->MultiCell($largeurMat,7,'MOYENNE GENERALE',1,'L',1);
	$pdf->SetFont('Arial','B',12);
	$moyenEleveaff=moyGenEleve($noteMoyEleG,$coefEleG);
	if (($moyenEleveaff < 10) && ($moyenEleveaff!="")) { $moyenEleveaff="0".$moyenEleveaff; }
	$pdf->SetXY($largeurMat+5,$YmoyenneGeneral);
	$pdf->MultiCell(25,7,"$moyenEleveaff",1,'C',1);
	$pdf->SetXY($largeurMat+15+15,$YmoyenneGeneral);
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(8.5,7,"$moyenClasseGenaff",1,'C',1);
	$pdf->SetXY($largeurMat+8.5+15+15,$YmoyenneGeneral);
	$pdf->MultiCell(8.5,7,"$moyenClasseMinaff",1,'C',1);
	$pdf->SetXY($largeurMat+8.5+8+15+15,$YmoyenneGeneral);
	$pdf->MultiCell(8.5,7,"$moyenClasseMaxaff",1,'C',1);
 
	$pdf->SetFillColor(255,255,255);


// cadre moyenne 
// $YmoyenneGeneral=$YmoyenneGeneral + 11;

if ($moyennomdugroupe1 != ""){
	$notenomdugroupe1 = $moyennomdugroupe1 / $nbnotenomdugroupe1;
	$notenomdugroupe1=number_format($notenomdugroupe1,2,'.','');
	$notenomdugroupe1=preg_replace('/\./',',',$notenomdugroupe1);
}
if ($moyennomdugroupe2 != ""){
	$notenomdugroupe2 = $moyennomdugroupe2 / $nbnotenomdugroupe2;
	$notenomdugroupe2=number_format($notenomdugroupe2,2,'.','');
	$notenomdugroupe2=preg_replace('/\./',',',$notenomdugroupe2);
}
if ($moyennomdugroupe3 != ""){
	$notenomdugroupe3 = $moyennomdugroupe3 / $nbnotenomdugroupe3;
	$notenomdugroupe3=number_format($notenomdugroupe3,2,'.','');
	$notenomdugroupe3=preg_replace('/\./',',',$notenomdugroupe3);
}


$moyennomdugroupe1="";
$nbnotenomdugroupe1="";
$moyennomdugroupe2="";
$nbnotenomdugroupe2="";
$moyennomdugroupe3="";
$nbnotenomdugroupe3="";


$LargeurMG=$largeurMat;
$YmoyenneGeneral=$YmoyenneGeneral+8;
$YmoyenneGeneralT=$YmoyenneGeneral + 2; 
$XMoyGE= 15 + 15 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral ;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 15 + 6;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 15;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 15 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;

$hauteursousgroupe=0;

if ($nomdugroupe1 != "") {
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(214,227,188);
	$pdf->SetXY(3+5+$LargeurMG/2,$YmoyenneGeneral);
	$pdf->MultiCell(35,7,'',1,'L',1);  
	$pdf->SetXY(3+5+$LargeurMG/2,$YmoyenneGeneral+2);
	$pdf->MultiCell(35,3,"$nomdugroupe1",0,'L',0);  
	$pdf->SetXY(35+3+5+$LargeurMG/2,$YmoyenneGeneral);
	$pdf->SetFont('Arial','B',7);
	$pdf->MultiCell(10,7,"$notenomdugroupe1",1,'C',0);   // note
	$hauteursousgroupe=8.5;
}
if ($nomdugroupe2 != "") {
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(198,217,241);
	$pdf->SetXY(5+5+35+3+15+$LargeurMG/2,$YmoyenneGeneral);
	$pdf->MultiCell(35,7,'',1,'L',1);  
	$pdf->SetXY(5+5+35+3+15+$LargeurMG/2,$YmoyenneGeneral+2);
	$pdf->MultiCell(35,3,"$nomdugroupe2",0,'L',0);  
	$pdf->SetXY(35+5+5+35+3+15+$LargeurMG/2,$YmoyenneGeneral);
	$pdf->SetFont('Arial','B',7);
	$pdf->MultiCell(10,7,"$notenomdugroupe2",1,'C',0);   // note
	$hauteursousgroupe=8.5;
}
if ($nomdugroupe3 != "") {
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(253,233,217);
	$pdf->SetXY(5+5+35+15+5+35+3+15+$LargeurMG/2,$YmoyenneGeneral);
	$pdf->MultiCell(35,7,'',1,'L',1);  
	$pdf->SetXY(5+5+35+15+5+35+3+15+$LargeurMG/2,$YmoyenneGeneral+2);
	$pdf->MultiCell(35,3,"$nomdugroupe3",0,'L',0);  
	$pdf->SetXY(35+5+5+35+15+5+35+3+15+$LargeurMG/2,$YmoyenneGeneral);
	$pdf->SetFont('Arial','B',7);
	$pdf->MultiCell(10,7,"$notenomdugroupe3",1,'C',0);   // note
	$hauteursousgroupe=8.5;
}
// fin affichage

$notenomdugroupe3="";
$notenomdugroupe2="";
$notenomdugroupe1="";

}

// cadre appréciation
$Ycom=$YmoyenneGeneral + $hauteursousgroupe;
$pdf->SetFont('Arial','',8);
$pdf->SetXY(15,$Ycom);
$pdf->MultiCell(185,6,"$appreciation $com_visa_scolaire",1,'L',0);


$Ycom=$Ycom + 6;
$EpaisCom=32;

$pdf->SetXY(15,$Ycom);
$pdf->MultiCell(145,$EpaisCom,'',1,'C',0);

$pdf->SetFillColor(255,255,0);

$miseengarde="0";
$miseengardetravail="0";
$encouragement="0";
$felicitation="0";
$Xfelicitation="";
$Xencouragement="";
$Xmiseengarde="";
$Xmiseengardetravail="";

if ($affichedistinction == "oui") {

	$apprecia=rechercheleap($idEleve,"leap",$_POST["saisie_trimestre"]); //leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav

	if ($apprecia[0][1] == "1") { $felicitation="1"; $Xfelicitation="X";  }
	if ($apprecia[0][0] == "1") { $encouragement="1"; $Xencouragement="X";  }
	if ($apprecia[0][2] == "1") { $miseengarde="1"; $Xmiseengarde="X";  }
	if ($apprecia[0][3] == "1") { $miseengardetravail="1"; $Xmiseengardetravail="X";  }
	
	$pdf->SetXY(18,$Ycom+4);
	$pdf->MultiCell(40,5,"Mise en garde comportement ",0,'L',0);
	$pdf->SetXY(58,$Ycom+5);
	$pdf->SetFont('Arial','B',6);
	$pdf->MultiCell(4,3,"$Xmiseengarde",1,'C',$miseengarde);
	$pdf->SetFont('Arial','',8);
	
	$pdf->SetXY(63,$Ycom+4);
	$pdf->MultiCell(40,5,"Mise en garde travail ",0,'L',0);
	$pdf->SetXY(93,$Ycom+5);
	$pdf->SetFont('Arial','B',6);
	$pdf->MultiCell(4,3,"$Xmiseengardetravail",1,'C',$miseengardetravail);
	$pdf->SetFont('Arial','',8);


	$pdf->SetXY(99,$Ycom+4);
	$pdf->MultiCell(25,5,"Encouragements ",0,'L',0);
	$pdf->SetXY(123,$Ycom+5);
	$pdf->SetFont('Arial','B',6);
	$pdf->MultiCell(4,3,"$Xencouragement",1,'C',$encouragement);
	$pdf->SetFont('Arial','',8);
	
	$pdf->SetXY(128,$Ycom+4);
	$pdf->MultiCell(20,5,"Felicitations ",0,'L',0);
	$pdf->SetXY(145,$Ycom+5);
	$pdf->SetFont('Arial','B',6);
	$pdf->MultiCell(4,3,"$Xfelicitation",1,'C',$felicitation);
	$pdf->SetFont('Arial','',8);
	
	$Y2=10;
}else{
	$Y2=5;
}

// commentaire direction
// ---------------------

if ($affichecommentaireprofp == "oui") {
	$commentairedirection=recherche_com_profp($idEleve,$_POST["saisie_trimestre"]);
}else{
	$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"leap");
}


$pdf->SetXY(15,$Ycom);
$pdf->WriteHTML("<B>".$appreciation2."</B>");

$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy

$larg=140;
if (file_exists("./data/image_pers/logo_signature.jpg")){
	$taille = getimagesize("./data/image_pers/logo_signature.jpg");
	$logox=$taille[0]/15;
	$logoy=$taille[1]/15;
	$pdf->Image("./data/image_pers/logo_signature.jpg","165",$Ycom+6,$logox,$logoy);
}
if ($affichesignatureprofp == "oui") { $larg=115; }

$pdf->SetFont('Arial','',10);
$pdf->SetXY(15,$Ycom+$Y2);
$pdf->MultiCell($larg,3.5,"$commentairedirection",'','L',0); // commentaire de la direction (visa)

if (($affichevisascolaire == "oui") && ($affichedistinction != "oui" )) {
	$commentairevisascolaire=recherche_com_scolaire($idEleve,$_POST["saisie_trimestre"]);
	$pdf->SetXY(15,$Ycom+$EpaisCom/2);
	$pdf->SetFont('Arial','B',8);
	$pdf->MultiCell($larg,3,"$lang04 : ",0,'L',0);
	$pdf->SetXY(15,$Ycom+4+$EpaisCom/2);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell($larg,3.5,"$commentairevisascolaire",'','L',0);	
}

$pdf->SetXY(160,$Ycom);
$pdf->MultiCell(40,$EpaisCom,"",1,'C',0);
$pdf->SetXY(160,$Ycom+2);
$pdf->MultiCell(40,3,"$lang05",0,'C',0);
$pdf->SetXY(160,$Ycom+$EpaisCom-4);
$pdf->SetFont('Arial','B',8);
$pdf->MultiCell(40,3,"$directeur",0,'C',0);

if ($affichesignatureprofp == "oui") {
	if (file_exists("./data/signature/profp/$idClasse/signature.jpg")){
		$taille = getimagesize("./data/signature/profp/$idClasse/signature.jpg");
		$logox=$taille[0]/15;
		$logoy=$taille[1]/15;
		$pdf->Image("./data/signature/profp/$idClasse/signature.jpg","130",$Ycom+10,$logox,$logoy);
	}
}


$listenomdugroupe1=preg_replace('/,$/','',$listenomdugroupe1);
$listenomdugroupe2=preg_replace('/,$/','',$listenomdugroupe2);
$listenomdugroupe3=preg_replace('/,$/','',$listenomdugroupe3);

//duplicata et signature
$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,$Ycom+$EpaisCom);
$pdf->SetFont('Arial','',6);

if ($listenomdugroupe1 != "") { $ligne="* moy.des matières suivantes: ".$listenomdugroupe1; }
if ($listenomdugroupe2 != "") { $ligne.=" / ** moy.des matières suivantes: ".$listenomdugroupe2; }
if ($listenomdugroupe3 != "") { $ligne.=" / *** moy.des matières suivantes: ".$listenomdugroupe3; }

//$pdf->MultiCell(185,3,"$ligne",0,'L',0);
//$pdf->SetFont('Arial','BI',8);
//$pdf->SetXY(15,$Ycom+$EpaisCom+9);
//$pdf->MultiCell(190,3,"$duplicata",0,'C',0);
// fin duplicata
//
$liste_ens_generaux="";
$liste_spec_prof="";
$liste_sect_prof="";



// commentaire prof principal
//$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
//$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
//$pdf->SetXY(15,$Ycom+17);
//$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
//$pdf->SetFont('Arial','',$confPolice[0]);
//$pdf->MultiCell(140,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)



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
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();

?>
