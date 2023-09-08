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
<!-- // fin  -->
<br><br>
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

$opt1livretscolaire=$_POST["opt1livretscolaire"];
config_param_ajout($opt1livretscolaire,"opt1livretscolaire");
$opt2livretscolaire=$_POST["opt2livretscolaire"];
config_param_ajout($opt2livretscolaire,"opt2livretscolaire");


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

if ($_POST["typetrisem"] == "cycle") {
	if ($_POST["saisie_trimestre"] == "cycle1" ) { $nbcycle="1"; }
	if ($_POST["saisie_trimestre"] == "cycle2" ) { $nbcycle="2"; }
	if ($_POST["saisie_trimestre"] == "cycle3" ) { $nbcycle="3"; }
	if ($_POST["saisie_trimestre"] == "cycle4" ) { $nbcycle="4"; }
}

/*
$hauteurphoto=$_POST["hauteurphoto"];
$largeurphoto=$_POST["largeurphoto"];
$hauteurlogo=$_POST["hauteurlogo"];
$largeurlogo=$_POST["largeurlogo"];
$avecexamenblanc=$_POST["avecexamenblanc"];
$affichemoyengeneral=$_POST["affichemoyengeneral"];
$affichematierecoefzero=$_POST["affichematierecoefzero"];
$abssconet=$_POST["abssconet"];
$afficherang=$_POST["afficherang"];
$npAfficheSousMatiere=$_POST["npAfficheSousMatiere"];
$hauteurMatiere=$_POST["hauteurMatiere"];
$npAfficheCoef=$_POST["npAfficheCoef"];
$coef100=$_POST["coef100"];
$adressebulletin=$_POST["adressebulletin"];
$noteviescolairedansmoyennegeneral=$_POST["noteviescolairedansmoyennegeneral"];
*/

if (trim($hauteurphoto) == "") {
	$hauteurphoto=16.3;
	$largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
	$hauteurlogo=25;
	$largeurlogo=25;
}

if ($hauteurMatiere == "") $hauteurMatiere=10; 
// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print ucwords($classe_nom)?><br> <br>
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

$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull_trim($_POST["saisie_classe"],$_POST["saisie_trimestre"]); // recup ordre matiere

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$pdf=new PDF();  // declaration du constructeur

$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

$moyenClasseGen=""; // pour le calcul moyenne classe
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

$plageEleve=$_POST["plageEleve"];
if ($plageEleve == "") $plageEleve="tous"; 
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
	}
	//---------------------------------//



	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	// declaration variable
	$coordonne00=strtoupper($academie);
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	$coordonne4="E-mail : ".$mail;


	$titre="<B><U>".LANGBULL30."</U> <U>".ucwords($textTrimestre)."</u></B>";

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",40);


	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=ucwords($classe_nom);

	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	$appreciation=LANGBULL39;
	if ($abssconet == "oui") {
		$appreciationbis="($nbretard retard(s) / $nbabs absence(s) / $nbabsnonjustifier absence(s) non justifié(s) ) " ;
	}else{
		$appreciationbis="($nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ) " ;
	}

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
	$logo="./image/commun/logo-educ-fr.jpg";
	if (file_exists($logo)) {
		$xlogo=30;
		$ylogo=50;
		$xcoor0=35;
		$ycoor0=3;
		$xtitre=90; // avec logo
		$pdf->Image($logo,3,3,$xlogo,$ylogo);
	}
	// fin du logo
	//

	$idprofp=rechercheprofp($_POST["saisie_classe"],$anneeScolaire);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML($coordonne00);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY($xcoor0,$ycoor0+7);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0+14);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY($xcoor0,$ycoor0+21);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY($xcoor0,$ycoor0+28);
	$pdf->WriteHTML($coordonne3);
	$pdf->SetXY($xcoor0,$ycoor0+36);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate="Année scolaire $anneeScolaire";
	// fin d'insertion

        $Y=60;

	$pdf->SetFillColor(226,240,250);
	$pdf->SetXY(3,$Y); // placement du cadre du nom de l eleve
	$pdf->MultiCell(203,45,'',0,'L',1);
	$pdf->SetXY(3,$Y+=3); // placement du cadre du nom de l eleve
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(103,3,"$Pdate",0,'C',1);
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$cycle=chercherNiveauClasse($idClasse);
	if (trim($cycle) != "") $cycleL="du $cycle";
	$pdf->MultiCell(103,3,"Bilan Trimestriel $cycleL - ".ucwords($textTrimestre)." ",0,'C',0);

	


	// adresse de l'élève
	// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance,email_eleve,adr_eleve,ccp_eleve,commune_eleve
	$dataadresse=chercheadresse($idEleve);
	$ik=0;
	$nomtuteur=$dataadresse[$ik][1];
	$prenomtuteur=$dataadresse[$ik][2];
	$adr1=$dataadresse[$ik][3];
	$code_post_adr1=$dataadresse[$ik][4];
	$commune_adr1=$dataadresse[$ik][5];
	$numero_eleve=$dataadresse[$ik][9];
	$datenaissance=$dataadresse[$ik][11];
	$adr_eleve=$dataadresse[$ik][21];
	$ccp_eleve=$dataadresse[$ik][22];
	$commune_eleve=$dataadresse[$ik][23];
	$regime=$dataadresse[$ik][12];
	$class_ant=trim(trunchaine($dataadresse[$ik][10],20));
	if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }

//	$pdf->WriteHTML("Né(e) le $datenaissance");

	$YY=$Y;
	$pdf->SetXY(3,$Y+=10);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(103,3,"$nomEleve $prenomEleve",0,'C',0);
	$pdf->SetXY(3,$Y+=5);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(103,3,"Né(e) le $datenaissance",0,'C',0);

	$pdf->SetXY(3,$Y+=10);
	$pdf->MultiCell(103,3,"Professeur principal : $profp",0,'C',0);
	$pdf->SetXY(3,$Y+=5);
	$pdf->MultiCell(103,3,"Classe de $classe_nom ",0,'C',0);
	

	$Y=$YY;
	$pdf->SetXY(115,$Y);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(100,3,"$nomtuteur $prenomtuteur",0,'L',0);
	$pdf->SetXY(115,$Y+=5);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(100,3,"$adr1",0,'L',0);
	$pdf->SetXY(115,$Y+=10);
	$pdf->MultiCell(100,3,"$code_post_adr1  $commune_adr1",0,'L',0);
	
	/*****************************************************************************/
	$Y+=20;
	$pdf->SetFillColor(178,210,53);
        $pdf->SetXY(3,$Y+=10); // placement du cadre du nom de l eleve
	$pdf->SetTextColor(255);
	$pdf->SetFont('Arial','B',12);
        $pdf->MultiCell(203,8,"Maîtrise des composantes du socle en fin de cycle $nbcycle",0,'C',1);
	$pdf->SetTextColor(0);
	
	/*****************************************************************************/
	$Y+=8;

	$pdf->SetFont('Arial','',6);
	
	$pdf->SetXY(103+3,$Y+=10);
	$pdf->SetFillColor(235,238,249);
	$pdf->MultiCell(25,5,"Maîtrise insuffisante",0,'C',1);

	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(199,225,245);
	$pdf->MultiCell(25,5,"Maîtrise fragile",0,'C',1);

	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(172,212,241);
	$pdf->MultiCell(25,5,"Maîtrise satisfaisante",0,'C',1);

	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(145,201,237);
	$pdf->MultiCell(25,5,"Très bonne maîtrise",0,'C',1);

	$pdf->SetFont('Arial','',6);
	

	$dataCycle=recupInfoCyclePropP($idEleve,$nbcycle); //ideleve,cycle,q1,q2,q3,q4,q5,q6,q7,commentaire,idprofp,q4bis


	$commentaire=$dataCycle[0][9];

	$q11=($dataCycle[0][2] == "mi") ? "X" : "" ;
	$q12=($dataCycle[0][2] == "mf") ? "X" : "" ;
	$q13=($dataCycle[0][2] == "ms") ? "X" : "" ;
	$q14=($dataCycle[0][2] == "tbm") ? "X" : "" ;

	$q21=($dataCycle[0][3] == "mi") ? "X" : "" ;
	$q22=($dataCycle[0][3] == "mf") ? "X" : "" ;
	$q23=($dataCycle[0][3] == "ms") ? "X" : "" ;
	$q24=($dataCycle[0][3] == "tbm") ? "X" : "" ;
			
	$q31=($dataCycle[0][4] == "mi") ? "X" : "" ;
	$q32=($dataCycle[0][4] == "mf") ? "X" : "" ;
	$q33=($dataCycle[0][4] == "ms") ? "X" : "" ;
	$q34=($dataCycle[0][4] == "tbm") ? "X" : "" ;

	$q41=($dataCycle[0][5] == "mi") ? "X" : "" ;
	$q42=($dataCycle[0][5] == "mf") ? "X" : "" ;
	$q43=($dataCycle[0][5] == "ms") ? "X" : "" ;
	$q44=($dataCycle[0][5] == "tbm") ? "X" : "" ;

	$q41bis=($dataCycle[0][11] == "mi") ? "X" : "" ;
	$q42bis=($dataCycle[0][11] == "mf") ? "X" : "" ;
	$q43bis=($dataCycle[0][11] == "ms") ? "X" : "" ;
	$q44bis=($dataCycle[0][11] == "tbm") ? "X" : "" ;

	$q51=($dataCycle[0][6] == "mi") ? "X" : "" ;
	$q52=($dataCycle[0][6] == "mf") ? "X" : "" ;
	$q53=($dataCycle[0][6] == "ms") ? "X" : "" ;
	$q54=($dataCycle[0][6] == "tbm") ? "X" : "" ;

	$q61=($dataCycle[0][7] == "mi") ? "X" : "" ;
	$q62=($dataCycle[0][7] == "mf") ? "X" : "" ;
	$q63=($dataCycle[0][7] == "ms") ? "X" : "" ;
	$q64=($dataCycle[0][7] == "tbm") ? "X" : "" ;

	$q71=($dataCycle[0][8] == "mi") ? "X" : "" ;
	$q72=($dataCycle[0][8] == "mf") ? "X" : "" ;
	$q73=($dataCycle[0][8] == "ms") ? "X" : "" ;
	$q74=($dataCycle[0][8] == "tbm") ? "X" : "" ;


	/* -------- */
	$pdf->SetFillColor(238,245,252);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Comprendre, sexprimer en utilisant la langue française à loral et à lécrit",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(238,245,252);
	$pdf->MultiCell(25,5,"$q11",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(212,231,247);
	$pdf->MultiCell(25,5,"$q12",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(186,218,243);
	$pdf->MultiCell(25,5,"$q13",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(159,206,239);
	$pdf->MultiCell(25,5,"$q14",0,'C',1);
	

	$pdf->SetFillColor(225,238,249);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Comprendre, sexprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(223,238,249);
	$pdf->MultiCell(25,5,"$q21",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(199,225,245);
	$pdf->MultiCell(25,5,"$q22",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(172,212,241);
	$pdf->MultiCell(25,5,"$q23",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(145,201,237);
	$pdf->MultiCell(25,5,"$q24",0,'C',1);


	$pdf->SetFillColor(238,245,252);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Comprendre, sexprimer en utilisant les langages mathématiques, scientifiques et informatiques",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(238,245,252);
	$pdf->MultiCell(25,5,"$q31",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(212,231,247);
	$pdf->MultiCell(25,5,"$q32",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(186,218,243);
	$pdf->MultiCell(25,5,"$q33",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(159,206,239);
	$pdf->MultiCell(25,5,"$q34",0,'C',1);
	

	$pdf->SetFillColor(225,238,249);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Comprendre, sexprimer en utilisant les langages des arts et du corps",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(223,238,249);
	$pdf->MultiCell(25,5,"$q41",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(199,225,245);
	$pdf->MultiCell(25,5,"$q42",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(172,212,241);
	$pdf->MultiCell(25,5,"$q43",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(145,201,237);
	$pdf->MultiCell(25,5,"$q44",0,'C',1);


	$pdf->SetFillColor(238,245,252);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Les méthodes et outils pour apprendre",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(238,245,252);
	$pdf->MultiCell(25,5,"$q41bis",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(212,231,247);
	$pdf->MultiCell(25,5,"$q42bis",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(186,218,243);
	$pdf->MultiCell(25,5,"$q43bis",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(159,206,239);
	$pdf->MultiCell(25,5,"$q44bis",0,'C',1);
	

	$pdf->SetFillColor(225,238,249);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"La formation de la personne et du citoyen",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(223,238,249);
	$pdf->MultiCell(25,5,"$q51",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(199,225,245);
	$pdf->MultiCell(25,5,"$q52",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(172,212,241);
	$pdf->MultiCell(25,5,"$q53",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(145,201,237);
	$pdf->MultiCell(25,5,"$q54",0,'C',1);


	$pdf->SetFillColor(238,245,252);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Les systèmes naturels et les systèmes techniques",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(238,245,252);
	$pdf->MultiCell(25,5,"$q61",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(212,231,247);
	$pdf->MultiCell(25,5,"$q62",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(186,218,243);
	$pdf->MultiCell(25,5,"$q63",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(159,206,239);
	$pdf->MultiCell(25,5,"$q64",0,'C',1);
	

	$pdf->SetFillColor(225,238,249);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$pdf->MultiCell(103,5,"Les représentations du monde et lactivité humaine",0,'L',1);
	$pdf->SetXY(103+3,$Y);
	$pdf->SetFillColor(223,238,249);
	$pdf->MultiCell(25,5,"$q71",0,'C',1);
	$pdf->SetXY(103+3+25,$Y);
	$pdf->SetFillColor(199,225,245);
	$pdf->MultiCell(25,5,"$q72",0,'C',1);
	$pdf->SetXY(103+3+50,$Y);
	$pdf->SetFillColor(172,212,241);
	$pdf->MultiCell(25,5,"$q73",0,'C',1);
	$pdf->SetXY(103+3+75,$Y);
	$pdf->SetFillColor(145,201,237);
	$pdf->MultiCell(25,5,"$q74",0,'C',1);









	/*****************************************************************************/

	$Y+=10;
	$pdf->SetFillColor(0,148,218);
        $pdf->SetXY(3,$Y+=10); // placement du cadre du nom de l eleve
	$pdf->SetTextColor(255);
	$pdf->SetFont('Arial','B',12);
        $pdf->MultiCell(203,8,"Synthèse des acquis scolaires de lélève en fin de cycle $nbcycle",0,'C',1);
	$pdf->SetTextColor(0);
	$pdf->SetFillColor(237,244,213);
	$pdf->SetXY(3,$Y+=10);
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(203,30,"",0,'C',1);
	$pdf->SetXY(3,$Y+1);
	$pdf->MultiCell(203,3.5,"$commentaire",0,'L',0);
	/*****************************************************************************/

	$Y+=30+2;
        $pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(254,208,158);
        $pdf->SetXY(3,$Y); // placement du cadre du nom de l eleve
        $pdf->MultiCell(67,30,"",0,'L',1);
        $pdf->SetXY(3,$Y+2); // placement du cadre du nom de l eleve
        $pdf->MultiCell(66,3.5,"Visa du professeur principal \n$profp",0,'L',0);
	

        $pdf->SetXY(3+68,$Y); // placement du cadre du nom de l eleve
	$pdf->MultiCell(67,30,"",0,'L',1);
        $pdf->SetXY(3+68,$Y+2); // placement du cadre du nom de l eleve
	$pdf->MultiCell(66,3.5,"Visa du principal du collège \n$directeur",0,'L',0);


        $pdf->SetXY(3+68+68,$Y); // placement du cadre du nom de l eleve
        $pdf->MultiCell(67,30,"",0,'L',1);
        $pdf->SetXY(3+68+68,$Y+2); // placement du cadre du nom de l eleve
        $pdf->MultiCell(66,3.5,"Visa des parents / responsable légal\nPris connaissance le : ",0,'L',0);



	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$X=3;
	$Y+=3;

	

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
