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
	set_time_limit(300);
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
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; $sem=1; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2"; $sem=2; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; $sem=1; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; $sem=2; }
}

if (trim($hauteurphoto) == "") {
	$hauteurphoto=16.3;
	$largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
	$hauteurlogo=25;
	$largeurlogo=40;
}

$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$affcommentaire=$_POST["affcommentaire"];
config_param_ajout($affcommentaire,"affcommentaire");

$affnbabsmat=$_POST["affnbabsmat"];
config_param_ajout($affnbabsmat,"affnbabsmat");

$hauteurmatiere=$_POST["hauteurmatiere"];
config_param_ajout($hauteurmatiere,"hauteurmatiere");

$calculmoyenbrute=$_POST["calculmoyenbrute"];
config_param_ajout($calculmoyenbrute,"calculmoyenbrute");

$affabsrecup=$_POST["affabsrecup"];
config_param_ajout($affabsrecup,"affabsrecup");


// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];



// if ($_POST["saisie_trimestre"] != "trimestre1" ) $affECTS="oui";

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

$idClasse=$_POST["saisie_classe"];

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
$noteMoyEleG2=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

// pour le calcul de moyenne classe


if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// -------------------------------------------------------------------------------------------------------------------------------------

// calcul min et max general
//-------------------------
/*
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

			$noteaff=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);

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
                }
        }

        if ($min == 1000) { $min=""; }
        $min=preg_replace('/\./',',',$min);
        $max=preg_replace('/\./',',',$max);
        $moyenClasseMin=$min;
	$moyenClasseMax=$max;
 */
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

	$moyenGenEleveNonBrute=0;
	$moyenGenP1NonBrute=0;
	$moyenGenClasseNonBrute=0;
	$moyenGenMinNonBrute=0;
	$moyenGenMaxNonBrute=0;
	$nbNoteNonBrute=0;
	$nbNoteNonBruteP1=0;
	$nbNoteNonBruteClasse=0;
	$nbNoteMoyenGeneralNonBrute=0;
	$moyenGeneralNonBrute=0;
	$dejapasse=0;
	
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


	$titre=LANGBULL30." ".ucwords($textTrimestre);

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="$prenomEleve $nomEleve";


	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	$appreciation=LANGBULL39;

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
			$xlogo=$largeurlogo;
			$ylogo=$hauteurlogo;
			$xcoor0=43;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$pdf->Image($logo,3,3,$xlogo,$ylogo);
		}
	}
	// fin du logo
	//
	$appreciationbis="";
	if ($affabsrecup == "oui") {
		//---------------------------------//
	        // recherche le nombre de retard
	        $nbretard=0;
	        $nbretard1=0;
	        $nbheureabs=0;
	        $nbjoursabs=0;
	        $nbabs=0;
	        $nbabsnonjustifier=0;
		$nbrattrapage=0;
	        if ($abssconet == "oui") {
	                $nbretard=nombre_retard_sconet($idEleve,$triabsconet);
	                $nbabs=nombre_abs_sconet($idEleve,$triabsconet);
        	        $nbabsnonjustifier=nombre_abs_nonjustifie_sconet($idEleve,$triabsconet);
	        }else{
			$nbabsnonjustifier=count(nombre_absNonJustifie2($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)));
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
		$nbrattrapage=nbRattrapageAbsRtd($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
		$appreciationbis="$nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) / $nbabsnonjustifier absence(s) non justifiée(s) / $nbrattrapage rattrapage(s) effectué(s)" ;
	}
        //---------------------------------//


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
	$pdf->SetXY($xcoor0,$ycoor0+=20);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate="Année scolaire ".$anneeScolaire;
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(108,3);
	$pdf->MultiCell(70,3,"$Pdate",0,'R',0);
	// fin d'insertion


	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;
	if ($photo == "") { $photo="image/commun/photo_vide.jpg"; }
	$xphoto=180;
	$yphoto=3;
	$photowidth=20;
	$photoheight=20;

	$Xv1=110;
	if (!empty($photo)) {
		if (file_exists($photo)) {
			$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
		}
	}
	
	$Y=3;
	$pdf->SetXY(108,$Y+=5);
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(70,3,"$nomprenom",0,'R',0);
	$pdf->SetXY(108,$Y+=7);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(70,3,"$classe_nom",0,'R',0);

	
	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY(3,$ycoor0+=7);
	$pdf->MultiCell(70,3,"$titre",0,'L',0);


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
	}

	// fin cadre du haut
	$Y=$ycoor0+=5;
	$Xorigine=3;
	// -------------------------------------------------------------------------------------------
	// Barre des titres
	$X=$Xorigine;
	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(220);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(70,5,"Matières",1,'C',0);
	$pdf->SetXY($X+=70,$Y);
	$pdf->MultiCell(30,5,"Etudiant",1,'C',0);
	$pdf->SetXY($X+=30,$Y); 
	$pdf->MultiCell(30,5,"Classe",1,'C',0);
	$pdf->SetXY($X+=30,$Y); 

	if ($affcommentaire == "oui") { $pdf->MultiCell(70,10,"Appréciations et conseils pour progresser",1,'C',0); }

	$X=$Xorigine;
	$Y+=5;
	$pdf->SetXY($X,$Y); 
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(60,5,"Intitulé",1,'C',0);
	$pdf->SetXY($X+=60,$Y); 
	$pdf->MultiCell(10,5,"Coef.",1,'C',0);
	$pdf->SetXY($X+=10,$Y); 
	$pdf->MultiCell(10,5,"DS",1,'C',0);
	$pdf->SetXY($X+=10,$Y); 
	$pdf->MultiCell(10,5,"",1,'C',0);
	$pdf->SetXY($X,$Y-1);
	$pdf->MultiCell(10,5,"Exam.",0,'C',0);
	$pdf->SetXY($X,$Y+1.5);
	$pdf->MultiCell(10,5,"Blanc",0,'C',0);
	$pdf->SetXY($X+=10,$Y); 
	$pdf->MultiCell(10,5,"Moy.",1,'C',0);
	$pdf->SetXY($X+=10,$Y);	

	// Pour la classe
	$pdf->MultiCell(10,5,"Moy.",1,'C',0);
	$pdf->SetXY($X+=10,$Y);
	$pdf->MultiCell(10,5,"Mini",1,'C',0);
	$pdf->SetXY($X+=10,$Y);
	$pdf->MultiCell(10,5,"Maxi",1,'C',0);
	$pdf->SetXY($X+=10,$Y);

	$Y+=5;

// ----------------------------------------------------

	$recupUE=recupUE($idClasse,$sem); //code_ue,nom_ue,coef_ue,ects_ue
	
	$ectsTOTALP1=0;
	$ectsTOTALP2=0;
	// mise en place des matieres
	$largeurMat=60;
	$hauteurMatiere=$hauteurmatiere; // taille du cadre matiere


	for($f=0;$f<count($recupUE);$f++) {
		$code_ue=$recupUE[$f][0];
		$nom_ue=$recupUE[$f][1];
		$coef_ue=$recupUE[$f][2];
		$ects_ue=$recupUE[$f][3];
		$dejapasse=0;
		$listeMatiere=recupMatiereUE($code_ue,$idClasse);  // u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull, a.langue
	
		// Verification si saut de page
		// ---------------------------
		$nbmatiere=0;
		for($i=0;$i<count($listeMatiere);$i++) {
			$idmatiere=$idMatiere=$listeMatiere[$i][0];
			$ordreaffichage=$listeMatiere[$i][3];
			$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
			if ($verifGroupe) { continue; } 
			$nbmatiere++;
		}
		if ($nbmatiere*$hauteurMatiere+$Y > 230) {
			$pdf->AddPage();
			$Y=7;
		}
		// ---------------------------
 		$X=$Xorigine;
		$pdf->SetFont('Arial','B',7);
		$pdf->SetFillColor(220);
		$pdf->SetXY($X,$Y);
		$nom_ue=trunchaine($nom_ue,68);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,"",1,'L',1);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell($largeurMat,3,"$nom_ue",0,'L',0);
		$pdf->SetXY($X+=$largeurMat,$Y); 
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(10,$hauteurMatiere,"$coef_ue",1,'C',1);	

		$Xmemo=$X;	
		$Ymemo=$Y;
		
		$Y+=$hauteurMatiere;

		$pdf->SetFillColor(255);


		$moyUEP1="";
		$coefUEP1="";

		$moyUEEB="";
		$coefEB="";

		$moyUEDS="";
		$coefDS="";
		

		$moyUEP2="";
		$coefUEP2="";

		
		$moyUECLASS="";
		$minUECLASS="";
		$maxUECLASS="";
		$coefUECLASS="";

		unset($coefTEX);
		unset($noteexamenT);

		// u.code_matiere,m.libelle
		for($i=0;$i<count($listeMatiere);$i++) {
			$X=$Xorigine;
			
			$idmatiere=$listeMatiere[$i][0];
			$idMatiere=$listeMatiere[$i][0];
			$matierelong=chercheMatiereLong($idMatiere);
			$matiere=$listeMatiere[$i][1];
			$idprof=$listeMatiere[$i][2];
			$ordreaffichage=$listeMatiere[$i][3];
			$option=$listeMatiere[$i][5];

			$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
			if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

			// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffichage);

			// gestion pour les sous matiere
			// -----------------------------
			// cod_mat,sous_matiere,libelle
			$datasousmatiere=verifsousmatierebull($idMatiere);
			//	print $datasousmatiere;
			if ($datasousmatiere != "0") {
				$nomMatierePrincipale=$datasousmatiere[0][2];
				$nomSousMatiere=$datasousmatiere[0][1];
				$matiere="$nomMatierePrincipale $nomSousMatiere";
			}

			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($X,$Y);
			$matiere=$matierelong;
			$pdf->MultiCell($largeurMat,$hauteurMatiere,"",1,'L',0);
			$pdf->SetXY($X,$Y+1);
			
			// mise en place du nom du prof
			$profAff=recherche_personne2($idprof);


			$pdf->MultiCell($largeurMat,2,"$matiere",0,'L',0);
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY($X,$Y+5);
			$pdf->MultiCell($largeurMat,3,"$profAff",0,'L',0);

			$pdf->SetXY($X+=$largeurMat,$Y);
			$pdf->SetFont('Arial','',8);
			$ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);

			$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
			$coef=preg_replace('/\.00$/','',$coef);
			$pdf->MultiCell(10,$hauteurMatiere,"$coef",1,'C',0);  // coef 
			
			if (($coef == 0) && (PRODUCTID=="1f0a58528091321949de2226ba72ad18")) $coef=1; // specif pigier bordeau 

			$pdf->SetXY($X+=10,$Y);
			// mise en place du cadre note P1
			// ------------------------------------------------------
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {
				$noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
			}else{
				$noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
			unset($noteGen);
			// ---------------------------------------------------------------------------------------
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {
				$classement=Rangs($idmatiere,$dateDebut,$dateFin,$idClasse,$idprof);
			
    			}else {
        			$classement=RangsGroupe($idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
			$noterang=$noteaffP1;
			// ---------------------------------------------------------------------------------------
			$noteaffP11=$noteaffP1;
			if (($noteaffP1 < 10) && ($noteaffP1 != "")) { $noteaffP11="0".$noteaffP1; }
			if ($affnbabsmat == "oui") { 
				$nbabs=nombre_abs_devoir_matiere($idEleve,$dateDebut,$dateFin,$idmatiere,$idprof,$idgroupe);
				$nbabs=count($nbabs);
			}
			if ($nbabs > 0) { $nbabs="$nbabs Abs"; }else{ $nbabs=""; }
		
			$noteds=recupNoteExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,"DS");
		//	$noteexamen=recupNoteExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,"examen blanc");
			$noteexamen=moyenneEleveMatiereExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,"examen blanc");

			$pdf->SetFont('Arial','',6);
			$pdf->MultiCell(10,$hauteurMatiere,"",1,'C',0);  // DS
			$pdf->SetXY($X,$Y+0.5); 
			$pdf->MultiCell(10,2.5,"$noteds",0,'L',0);  // DS
			$pdf->SetXY($X+=10,$Y); 

			$pdf->SetFont('Arial','',9);
			if (($noteexamen < 10) && ($noteexamen != "")) { $noteexamen="0".$noteexamen; }
	                $noteexamen = number_format($noteexamen, 2, '.', '');
			$pdf->MultiCell(10,$hauteurMatiere,"$noteexamen",1,'C',0);  // Exam.
			$pdf->SetXY($X+=10,$Y);




			if ($noteds != "") {
				$tabds=explode(',',$noteds);
				foreach($tabds as $key=>$value) {
					$value=trim($value);
					$moyUEDS+=($value*$coef);
					$coefDS+=$coef;
					if ($calculmoyenbrute == "brute") {
						$moyEleveDS+=($value*$coef);
						$nbnoteds+=$coef;
					}
				}
			}
			
			if (is_numeric($noteexamen)) {
				$moyUEEB+=($noteexamen*$coef);
				$coefEB+=$coef;
				if ($calculmoyenbrute == "brute") {
					$moyEleveExamB+=($noteexamen*$coef);
					$nbnoteexamen+=$coef;
				}
			}


			$pdf->SetFont('Arial','B',9);
			if (($noteaffP11 < 10) && ($noteaffP11 != "")) {
                        	$pdf->SetFillColor(255,192,130);
                        }else{
				$pdf->SetFillColor(159,219,255);
			}
			$pdf->MultiCell(10,$hauteurMatiere,"$noteaffP11",1,'C',1);
			$pdf->SetFont('Arial','',9);
			$pdf->SetFillColor(255);
			$pdf->SetXY($X+=10,$Y);

			if (is_numeric($noteaffP11)) {
				$moyUEP1+=$noteaffP1*$coef;
				$coefUEP1+=$coef;
			}

			// mise en place des moyennes de classe	
			if (($idgroupe == "0") || (trim($idgroupe) == "")) { 
				// idMatiere,datedebut,dateFin,idclasse
	       	  		$moyeMatGen=moyeMatGen($idmatiere,$dateDebut,$dateFin,$idClasse,$idprof);
			}else {
        			$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}

			$moyeMatGenaff=$moyeMatGen;
			if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }

			// calcul du min et du max
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {    // non matiere affectée à un groupe
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
			}else{
				$max="";
				$min=1000;
				$eleveTg=listeEleveDansGroupe($idgroupe);
				for($g=0;$g<count($eleveTg);$g++) {
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
			$moyeMatGenMinaff=$moyeMatGenMin;
			$moyeMatGenMaxaff=$moyeMatGenMax;
			if (($moyeMatGenMin < 10) && ($moyeMatGenMin != "")) { $moyeMatGenMinaff="0".$moyeMatGenMin; }
			if (($moyeMatGenMax < 10) && ($moyeMatGenMax != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMax; }
			// fin de la calcul de min et max

		
			$pdf->SetXY($X,$Y);
			$pdf->MultiCell(10,$hauteurMatiere,"$moyeMatGenaff",1,'C',0); // moy classe
			$pdf->SetXY($X+=10,$Y);
			$pdf->MultiCell(10,$hauteurMatiere,"$moyeMatGenMinaff",1,'C',0); // mini 
			$pdf->SetXY($X+=10,$Y);
			$pdf->MultiCell(10,$hauteurMatiere,"$moyeMatGenMaxaff",1,'C',0); // maxi


			if (is_numeric($moyeMatGenaff)) {
				$coefUECLASS+=$coef;
				$moyUECLASS+=$moyeMatGenaff*$coef;
				$minUECLASS+=$moyeMatGenMinaff*$coef;
				$maxUECLASS+=$moyeMatGenMaxaff*$coef;
			}

			if ($affcommentaire == "oui") { 
				// mise en place des commentaires
				$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
				$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
				$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy

				$pdf->SetXY($X+=10,$Y);
				$pdf->MultiCell(70,$hauteurMatiere,"",1,'C',0); // maxi
				$pdf->SetXY($X+1,$Y+0.5);
				$pdf->SetFont('Arial','',$confPolice[0]);
				$pdf->MultiCell(70,2.5,$commentaireeleve,'','','L',0);
				$pdf->SetFont('Arial','',8);
			}


			if (is_numeric($noteaffP1)) {
	    			$noteMoyEleGTempo = $noteaffP1 * $coef;
           		     	$noteMoyEleG1=$noteMoyEleG1 + $noteMoyEleGTempo;
           		    	$coefEleG=$coefEleG + $coef;
			}

			$Y+=$hauteurMatiere;

		}


	
	


		// ligne Unité enseignement


		// ----------
		if (is_numeric($moyUEP1)) {
			$moyUEP1=$moyUEP1/$coefUEP1;
			$moyUEP1Aff=$moyUEP1;

	        	$moyenGenEleveNonBrute+=$moyUEP1*$coef_ue;
			$nbNoteNonBrute+=$coef_ue;

			if (($moyUEP1Aff < 10) && ($moyUEP1Aff != "")) { $moyUEP1Aff="0".$moyUEP1; }
			$moyUEP1Aff = number_format($moyUEP1Aff, 2, '.', '');

		}else{
			$moyUEP1Aff="";
		}

                if (is_numeric($moyUEDS)) {
                        $moyUEDS=$moyUEDS/$coefDS;
                        $moyUEDSAff=$moyUEDS;
                        if (($moyUEDSAff < 10) && ($moyUEDSAff != "")) { $moyUEDSAff="0".$moyUEDS; }
                        $moyUEDSAff = number_format($moyUEDSAff, 2, '.', '');
                }else{
                        $moyUEDSAff="";
                }

                if (is_numeric($moyUEEB)) {
                        $moyUEEB=$moyUEEB/$coefEB;
                        $moyUEEBAff=$moyUEEB;
                        if (($moyUEEBAff < 10) && ($moyUEEBAff != "")) { $moyUEEBAff="0".$moyUEEB; }
                        $moyUEEBAff = number_format($moyUEEBAff, 2, '.', '');
                }else{
                        $moyUEEBAff="";
                }


		$pdf->SetFont('Arial','',9);
		$pdf->SetFillColor(220);
		$pdf->SetXY($Xmemo+=10,$Ymemo); 
		$pdf->MultiCell(10,$hauteurMatiere,"$moyUEDSAff",1,'C',1);   // Moyenne DS	
		$pdf->SetXY($Xmemo+=10,$Ymemo); 
		$pdf->MultiCell(10,$hauteurMatiere,"$moyUEEBAff",1,'C',1);   // Moyenne Examen Blanc	
		$pdf->SetXY($Xmemo+=10,$Ymemo); 
		$pdf->MultiCell(10,$hauteurMatiere,"$moyUEP1Aff",1,'C',1);   // Moyenne Semestre P1	
		$pdf->SetXY($Xmemo+=10,$Ymemo);   

		$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);


                if ($calculmoyenbrute == "nonbrute") {
                	if (is_numeric($moyUEDSAff)) {
                        	$moyEleveDS+=($moyUEDSAff*$coef_ue);
				$nbnoteds+=$coef_ue;
                        }
                        if (is_numeric($moyUEEBAff)) {
                                $moyEleveExamB+=($moyUEEBAff*$coef_ue);
				$nbnoteexamen+=$coef_ue;
                        }
                }


		if (is_numeric($moyUECLASS)) {

			$moyUECLASS=$moyUECLASS/$coefUECLASS;
			$moyUECLASSG+=$moyUECLASS*$coef;
			$coefUECLASSG+=$coef;
			$moyUECLASSAff=$moyUECLASS;

			if (($moyUECLASSAff < 10) && ($moyUECLASSAff != "")) { $moyUECLASSAff="0".$moyUECLASS; }
			$moyUECLASSAff = number_format($moyUECLASSAff, 2, '.', '');
			$moyenGenClasseNonBrute+=$moyUECLASSAff*$coef_ue;
			$nbNoteNonBruteClasse+=$coef_ue;
		}else{
			$moyUECLASSAff="";
		}

		if (is_numeric($minUECLASS)) {
			$minUECLASS=$minUECLASS/$coefUECLASS;
			$minUECLASSAff=$minUECLASS;
			$minUECLASSG+=$minUECLASS*$coef;

			if (($minUECLASSAff < 10) && ($minUECLASSAff != "")) { $minUECLASSAff="0".$minUECLASS; }
			$minUECLASSAff = number_format($minUECLASSAff, 2, '.', '');
			$moyenGenMinNonBrute+=$minUECLASSAff*$coef_ue;
		}else{
			$minUECLASSAff="";
		}

		if (is_numeric($maxUECLASS)) {
			$maxUECLASS=$maxUECLASS/$coefUECLASS;
			$maxUECLASSAff=$maxUECLASS;
			$maxUECLASSG+=$maxUECLASS*$coef;

			if (($maxUECLASSAff < 10) && ($maxUECLASSAff != "")) { $maxUECLASSAff="0".$maxUECLASS; }
			$maxUECLASSAff = number_format($maxUECLASSAff, 2, '.', '');
        		$moyenGenMaxNonBrute+=$maxUECLASSAff*$coef_ue;
		}else{
			$maxUECLASSAff="";
		}

		// Pour la classe
		$pdf->MultiCell(10,$hauteurMatiere,"$moyUECLASSAff",1,'C',1);
		$pdf->SetXY($Xmemo+=10,$Ymemo);
		$pdf->MultiCell(10,$hauteurMatiere,"$minUECLASSAff",1,'C',1);
		$pdf->SetXY($Xmemo+=10,$Ymemo);
		$pdf->MultiCell(10,$hauteurMatiere,"$maxUECLASSAff",1,'C',1);

		// commentaire
		if ($affcommentaire == "oui") { 
			$pdf->SetXY($Xmemo+=10,$Ymemo); 
			$pdf->MultiCell(70,$hauteurMatiere,"",1,'C',1);
			$pdf->SetFillColor(255);
		}
	
}
// fin de la mise en place des matiere


$X=$Xorigine;
$YY=$Y;
// Mise en place légende 
$pdf->SetXY($X,$Y+=2); 
$pdf->MultiCell(35,15,"",1,'C',1);

$pdf->SetFont('Arial','B',6);
$pdf->SetTextColor(0);
$pdf->SetXY($X,$Y+1); 
$pdf->MultiCell(15,3,"Légende",0,'L',0);
$pdf->SetXY($X,$Y+=5);
$pdf->SetFont('Arial','',5);
$pdf->SetTextColor(255,0,0);
$pdf->MultiCell(10,3,"1 Abs",0,'L',0);

$pdf->SetTextColor(0);
$pdf->SetXY($X+8,$Y);
$pdf->MultiCell(30,2,"Moyenne non significative en raison d'absence(s) lors d'évaluation",0,'L',0);


$pdf->SetTextColor(0);
$pdf->SetFillColor(255,192,130);
$pdf->SetXY($X+1,$Y+=6);
$pdf->MultiCell(5,3,"",1,'L',1);
$pdf->SetTextColor(0);
$pdf->SetFillColor(255);
$pdf->SetXY($X+8,$Y+1);
$pdf->MultiCell(30,2,"Moyenne inférieure à 10",0,'L',0);
$pdf->SetXY($X+30,$Y+1);


// mise en place moyenne
$Y=$YY;



$pdf->SetXY($X+=35,$Y+=2); 
$Xannul=$X;
$pdf->SetFont('Arial','B',7);
$pdf->MultiCell(35,10,"Moyenne générale : ",0,'R',0);




$pdf->SetXY($X+=35,$Y); 
if ($calculmoyenbrute == "nonbrute") {
	$moyEleveG1=$moyenGenEleveNonBrute/$nbNoteNonBrute;
        if (($moyEleveG1 < 10) && ($moyEleveG1 != "")) { $moyEleveG1="0".$moyEleveG1; }
        $moyEleveG1 = number_format($moyEleveG1, 2, '.', '');
}else{
	$moyEleveG1=$noteMoyEleG1/$coefEleG;
	if (($moyEleveG1 < 10) && ($moyEleveG1 != "")) { $moyEleveG1="0".$moyEleveG1; }
	$moyEleveG1 = number_format($moyEleveG1, 2, '.', '');
}


if ($nbnoteds > 0) {
	$moyEleveDS=$moyEleveDS/$nbnoteds;
	$moyEleveDS=number_format($moyEleveDS, 2, '.', '');
}


if ($nbnoteexamen > 0) {
        $moyEleveExamB=$moyEleveExamB/$nbnoteexamen;
        $moyEleveExamB=number_format($moyEleveExamB, 2, '.', '');
}


unset($nbnoteds);
unset($nbnoteexamen);

$pdf->SetFont('Arial','B',9);
$pdf->MultiCell(10,10,"$moyEleveDS",1,'C',1);
$X1=$X;
$pdf->SetXY($X+=10,$Y);
$pdf->MultiCell(10,10,"$moyEleveExamB",1,'C',1);
$nbT=0;
if ($moyEleveDS != "") {  $moyenT+=$moyEleveDS; $nbT++; }
if ($moyEleveExamB != "") { $moyenT+=$moyEleveExamB; $nbT++; }

if ($nbT > 0) {
	$moyenT=$moyenT/$nbT;
	$moyenT=number_format($moyenT, 2, '.', '');
}
$pdf->SetXY($X1,$Y+10);
$pdf->MultiCell(20,6,"$moyenT",1,'C',1);
unset($moyenT);


$pdf->SetXY($X+=10,$Y);
$pdf->SetFillColor(226,226,226);
$pdf->MultiCell(10,10,"$moyEleveG1",1,'C',1);
$pdf->SetFillColor(255);
$pdf->SetXY($X+=10,$Y);


unset($moyEleveDS);
unset($moyEleveExamB);

$pdf->MultiCell(100,30,"",1,'C',0);


if ($calculmoyenbrute == "nonbrute") {
	$maxUECLASSG=$moyenGenMaxNonBrute/$nbNoteNonBruteClasse;
	if (($maxUECLASSG < 10) && ($maxUECLASSG != "")) { $maxUECLASSG="0".$maxUECLASSG; }
        $maxUECLASSGAff = number_format($maxUECLASSG, 2, '.', '');

        $minUECLASSG=$moyenGenMinNonBrute/$nbNoteNonBruteClasse;
        if (($minUECLASSG < 10) && ($minUECLASSG != "")) { $minUECLASSG="0".$minUECLASSG; }
        $minUECLASSGAff = number_format($minUECLASSG, 2, '.', '');

        $moyUECLASSG=$moyenGenClasseNonBrute/$nbNoteNonBruteClasse;
        if (($moyUECLASSG < 10) && ($moyUECLASSG != "")) { $moyUECLASSG="0".$moyUECLASSG; }
        $moyUECLASSGAff = number_format($moyUECLASSG, 2, '.', '');
}else{
	$maxUECLASSG=$maxUECLASSG/$coefUECLASSG;
	if (($maxUECLASSG < 10) && ($maxUECLASSG != "")) { $maxUECLASSG="0".$maxUECLASSG; }
	$maxUECLASSGAff = number_format($maxUECLASSG, 2, '.', '');
	$minUECLASSG=$minUECLASSG/$coefUECLASSG;
	if (($minUECLASSG < 10) && ($minUECLASSG != "")) { $minUECLASSG="0".$minUECLASSG; }
	$minUECLASSGAff = number_format($minUECLASSG, 2, '.', '');
	$moyUECLASSG=$moyUECLASSG/$coefUECLASSG;
	if (($moyUECLASSG < 10) && ($moyUECLASSG != "")) { $moyUECLASSG="0".$moyUECLASSG; }
	$moyUECLASSGAff = number_format($moyUECLASSG, 2, '.', '');
}

$pdf->SetXY($X,$Y);
$pdf->MultiCell(10,10,"$moyUECLASSGAff",1,'C',1); // Moyen Classe Générale
$pdf->SetXY($X+=10,$Y);
$pdf->MultiCell(10,10,"$minUECLASSGAff",1,'C',1); // Moyenne Mini Générale
$pdf->SetXY($X+=10,$Y);
$pdf->MultiCell(10,10,"$maxUECLASSGAff",1,'C',1); // Moyenne Maxi Générale

$coefUECLASS=0;
unset($maxUECLASSG);
unset($minUECLASSG);
unset($moyUECLASSG);
unset($moyEleveG1);
unset($moyEleveG2);
unset($noteMoyEleG1);
unset($noteMoyEleG2);
unset($coefEleG);
unset($coefGen2);
unset($coefEleG2);
unset($noteGen2);
unset($coefUECLASSG);


$pdf->SetXY($X+10,$Y+1);
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(68,3,$appreciationbis,'','','L',0);


// Signature
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY($X-18,$Y+15);
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(98,3,$commentairedirection,'','','L',0);


$photo=recup_photo_signature_idsite(chercheIdSite($_POST["saisie_classe"]));
if ((file_exists("./data/image_pers/".$photo[0][0])) && (trim($photo[0][0]) != "")) {
	$taille = getimagesize("./data/image_pers/".$photo[0][0]);
	$logox=$taille[0]/15;
	$logoy=$taille[1]/27;
	$photo=$photo[0][0];
	$pdf->Image("./data/image_pers/$photo",$Xorigine+40,$Y+10,$logox,$logoy);
}

$pdf->SetXY($Xorigine+40,$Y+=20);
$pdf->MultiCell(40,5,"\nDirectrice pédagogique\n$directeur",0,'C',0); 

// Preparation de l'examen

if ($_POST["affprepaexam"] == "oui") {
	$X=$Xorigine;
	$Y+=8;
	$pdf->SetXY($X,$Y+=10);
	$pdf->SetFillColor(150,150,150);
	$pdf->SetTextColor(255);
	$larg=130;
	if ($affcommentaire == "oui") {
		$larg=200;
	}
	$pdf->MultiCell(200,5,"Préparation de l'examen terminal - Notes de suivi - Non pris en compte dans le contrôle continu.",1,'L',1);
	$pdf->SetFillColor(255);
	$pdf->SetTextColor(0);
	

	$listeMatiere=recupMatiereUESpecif($idClasse,"OPT4");
	$Y+=5;
	for($i=0;$i<count($listeMatiere);$i++) {
		$X=$Xorigine;
		
		$idmatiere=$listeMatiere[$i][0];
		$idMatiere=$listeMatiere[$i][0];
		$matierelong=chercheMatiereLong($idMatiere);
		$matiere=$listeMatiere[$i][1];
		$idprof=$listeMatiere[$i][2];

		$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	    	$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffichage);

		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		//	print $datasousmatiere;
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
			$matiere="$nomMatierePrincipale $nomSousMatiere";
		}

		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($X,$Y);
		$matiere=$matierelong;
		$pdf->MultiCell($largeurMat,$hauteurMatiere,"",1,'L',0);
		$pdf->SetXY($X,$Y+1);
		
		// mise en place du nom du prof
		$profAff=recherche_personne2($idprof);


		$pdf->MultiCell($largeurMat,2,"$matiere",0,'L',0);
		$pdf->SetFont('Arial','B',6);
		$pdf->SetXY($X,$Y+5);
		$pdf->MultiCell($largeurMat,3,"$profAff",0,'L',0);


		$pdf->SetXY($X+=$largeurMat,$Y);
		$pdf->SetFont('Arial','',8);
		$ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
		$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
		$coef=preg_replace('/\.00$/','',$coef);
		$pdf->MultiCell(10,$hauteurMatiere,"$coef",1,'C',0);  // coef 
		$pdf->SetXY($X+=10,$Y);


		// ------------------------------------------------------
		if (($idgroupe == "0") || (trim($idgroupe) == "")) {
			$noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
		}else{
			$noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		}

		$noteaffP11=$noteaffP1;
		if (($noteaffP1 < 10) && ($noteaffP1 != "")) { $noteaffP11="0".$noteaffP1; }

		if ($affnbabsmat == "oui") { 
			$nbabs=nombre_abs_devoir_matiere($idEleve,$dateDebut,$dateFin,$idmatiere,$idprof,$idgroupe);
			$nbabs=count($nbabs);
		}
		if ($nbabs > 0) { $nbabs="$nbabs Abs"; }else{ $nbabs=""; }


		$pdf->MultiCell(10,$hauteurMatiere,"$noteaffP11",1,'C',0);
	
		
		
		$pdf->SetFont('Arial','',4);
		$pdf->SetTextColor(255,0,0);
		$pdf->SetXY($X,$Y+2.5);
		$pdf->MultiCell(10,$hauteurMatiere,"$nbabs",0,'R',0);
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($X+=10,$Y);
		
		//--------------------------------------------------------------------------------------------------------------
		if (($idgroupe == "0") || (trim($idgroupe) == "")) {
			$noteaff=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		}
		$noteaff1=$noteaff;
		if (($noteaff1 < 10) && ($noteaff1 != "")) { $noteaff1="0".$noteaff1; }
		if (($noteaffP11 < 10) && ($noteaffP11 != "")) { 
			$pdf->SetFillColor(255,192,130);
		}else{
			$pdf->SetFillColor(255);
		}
			
		if ($affnbabsmat == "oui") { 
			$nbabs=nombre_abs_devoir_matiere($idEleve,$dateDebut,$dateFin,$idmatiere,$idprof,$idgroupe);
			$nbabs=count($nbabs);
		}	
		if ($nbabs > 0) { $nbabs="$nbabs Abs"; }else{ $nbabs=""; }
		$larg=20;
		$pdf->MultiCell($larg,$hauteurMatiere,"$noteaff1",1,'C',0);
		$pdf->SetFont('Arial','',4);
		$pdf->SetTextColor(255,0,0);
		$pdf->SetXY($X,$Y+2.5);
		$pdf->MultiCell($larg,$hauteurMatiere,"$nbabs",0,'C',0);
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial','',8);
		// --------------------------------------------------------------------------------------------------------------
		$pdf->SetXY($X+=$larg,$Y);


		// mise en place des moyennes de classe	
		if (($idgroupe == "0") || (trim($idgroupe) == "")) {
	        	$moyeMatGen=moyeMatGen($idmatiere,$dateDebut,$dateFin,$idClasse,$idprof);
		}else {
	           	$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
		}
		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }


		// calcul du min et du max
		if (($idgroupe == "0") || (trim($idgroupe) == "")) {    // non matiere affectée à un groupe
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
		}else{
			$max="";
			$min=1000;
			$eleveTg=listeEleveDansGroupe($idgroupe);
			for($g=0;$g<count($eleveTg);$g++) {
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
		$moyeMatGenMinaff=$moyeMatGenMin;
		$moyeMatGenMaxaff=$moyeMatGenMax;
		if (($moyeMatGenMin < 10) && ($moyeMatGenMin != "")) { $moyeMatGenMinaff="0".$moyeMatGenMin; }
		if (($moyeMatGenMax < 10) && ($moyeMatGenMax != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMax; }
		// fin de la calcul de min et max
	
		$pdf->MultiCell(10,$hauteurMatiere,"$moyeMatGenaff",1,'C',0); // moy classe
		$pdf->SetXY($X+=10,$Y);
		$pdf->MultiCell(10,$hauteurMatiere,"$moyeMatGenMinaff",1,'C',0); // mini 
		$pdf->SetXY($X+=10,$Y);
		$pdf->MultiCell(10,$hauteurMatiere,"$moyeMatGenMaxaff",1,'C',0); // maxi


		// mise en place des commentaires
		if ($affcommentaire == "oui") {
			$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
			$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
			$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy
			$pdf->SetXY($X+=10,$Y);
			$pdf->MultiCell(70,$hauteurMatiere,"",1,'C',0); // maxi
			$pdf->SetXY($X+1,$Y);
			$pdf->SetFont('Arial','',$confPolice[0]);
			$pdf->MultiCell(70,3,$commentaireeleve,'','','L',0);
			$pdf->SetFont('Arial','',8);
		}
		$Y+=$hauteurMatiere;
	}

} // fin du cadre de preparation de l'examen



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
$prenomEleve=preg_replace('/\(/',"_",$prenomEleve);
$prenomEleve=preg_replace('/\)/',"_",$prenomEleve);
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
