<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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
$nofooterPDF=NOFOOTERPDF;

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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
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
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();

$nbe=$_POST["nbe"];
for($p=0;$p<$nbe;$p++){
	$pt=$_POST["ptabs_$p"];
	$ideleveP=$_POST["ideleve_$p"];
	$pt=preg_replace('/,/','.',$pt);
	enrPtAbsBulletin($ideleveP,$pt,$_POST["saisie_trimestre"],$_POST["annee_scolaire"],$_POST["saisie_classe"]);
}

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
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre="ANNUEL"; $triabsconet="T4"; $sem="1 et 2"; }
	if ($_POST["saisie_trimestre"] == "annuel" ) { $textTrimestre="ANNUEL"; $triabsconet="T4"; $sem="1 et 2"; }
}



if ($triabsconet == "T1") {
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebut=$dateRecup[$j][0];
		$dateFin=$dateRecup[$j][1];
	}
	$dateDebut=dateForm($dateDebut);
	$dateFin=dateForm($dateFin);
}


if ($triabsconet == "T2") {
	$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebut=$dateRecup[$j][0];
		$dateFin=$dateRecup[$j][1];
	}
	$dateDebut=dateForm($dateDebut);
	$dateFin=dateForm($dateFin);
}


if ($triabsconet == "T3") {
	$dateRecup=recupDateTrimByIdclasse("trimestre3",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebut=$dateRecup[$j][0];
		$dateFin=$dateRecup[$j][1];
	}
	$dateDebut=dateForm($dateDebut);
	$dateFin=dateForm($dateFin);
}

if ($triabsconet == "T4") {
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
        for($j=0;$j<count($dateRecup);$j++) {
                $dateDebut=$dateRecup[$j][0];
        }
        $dateDebut=dateForm($dateDebut);

	$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
        for($j=0;$j<count($dateRecup);$j++) {
                $dateFin=$dateRecup[$j][1];
        }
        $dateFin=dateForm($dateFin);
}

// recupe du nom de la classe
$idclasse=$_POST["saisie_classe"];
$data=chercheClasse($_POST["saisie_classe"]); // code_class,trim(libelle),trim(desclong)
$classe_nom=stripslashes($data[0][1]);
$classe_long=stripslashes($data[0][2]);

// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
$eleveT=recupEleve($_POST["saisie_classe"]);

if (isset($_POST["createadmis"])) {
	$idclasse=$_POST["idclasse"];
	$bulletinProvisoire=$_POST["bulletinProvisoire"];

	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		$idEleve=$eleveT[$j][4];
		$admis=$_POST["admis_$idEleve"];
		saveIpacBulletin($idEleve,$admis,$anneeScolaire,$idclasse,$bulletinProvisoire);
	}
}

?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
      <?php print "Période "?> : <?php print "$dateDebut au $dateFin" ?><br><br>
</font>
</ul>

<?php


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


// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$examen="Partiel";
$rattrapage="Rattrapage";
$idClasse=$_POST["saisie_classe"];

$niveauClasse=chercherNiveauClasse($idClasse);

$affExam=$_POST["affExam"];
config_param_ajout($affExam,"affExam");

$affEtudeDeCas=$_POST["affEtudeDeCas"];
config_param_ajout($affEtudeDeCas,"affEtudeDeCas");

$hauteurMatiere=$_POST["hauteurMatiere"];
config_param_ajout($hauteurMatiere,"hauteurMatiereUE4");


$bulletinProvisoire=$_POST["bulletinProvisoire"];
config_param_ajout($bulletinProvisoire,"bulletinProvisoire");

$rattrapageprovisoire=$_POST["rattrapageprovisoire"];
if ($bulletinProvisoire == "non") $rattrapageprovisoire="oui" ;  
config_param_ajout($rattrapageprovisoire,"rattrapageprovisoire");

$penalitemoyenne=$_POST['penalitemoyenne'];
config_param_ajout($penalitemoyenne,"${idClasse}_penabs");


// creation PDF
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
//$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

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
	$moyenneGenerale="";
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
	$noteNonValide=0;
	$dejapasse=0;

	$admisManuel=recupIpacBulletin($idEleve,$anneeScolaire,$idclasse,$bulletinProvisoire);
	
	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	$idprofp=rechercheprofp($_POST["saisie_classe"]);

	// insertion de la Annee SCOLAIRE
	$Pdate="Année scolaire / Academic ";
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(150,3);
	$pdf->MultiCell(70,5,"$Pdate\nyear : $anneeScolaire",0,'C',0);
	// fin d'insertion

	// mise en place du logo
	$photo=recup_photo_bulletin_idsite(chercheIdSite($_POST["saisie_classe"]));
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xcoor0=30;
			$ycoor0=3;
			$xtitre=90; // avec logo

			list($width, $height) = getimagesize("$logo");
			$largeurlogo=$width*0.15;
			$hauteurlogo=$height*0.1;
			if ($width == 400) { $largeurlogo=$width*0.15; }
			if ($width == 1240) { $largeurlogo=$width*0.05; }
			
			$pdf->Image($logo,3,3,$largeurlogo,$hauteurlogo);
		}
	}

	$pdf->SetTextColor(255,0,0);	
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY(0,3);

	if ($bulletinProvisoire == "non") {
		$pdf->MultiCell(210,5,"Bulletin de notes annuel\nAnnual transcript of records",0,'C',0);
	}else{
		$pdf->MultiCell(210,5,"Bulletin de notes provisoire\nProvisionaly transcript of records",0,'C',0);
	}

	$pdf->SetTextColor(0,0,0);	

	$Y=0;
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(0,$Y+=18);
	$pdf->MultiCell(210,5,"$classe_long",0,'C',0);


	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
	$numero_eleve=$dataadresse[0][9];
	$datenaissance=$dataadresse[0][11];
	if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="$prenomEleve $nomEleve";

	// fin cadre du haut
	//$pdf->RoundedRect($x+115, $y, 68, 20, 3.5, 'DF');
	
	$pdf->SetXY(10,$Y+=7);
	$pdf->SetFillColor(43,164,136);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Arial','B',12);
	$datenaissance=trim($datenaissance);
	if (($datenaissance != "") && ($datenaissance != "0000-00-00")  && ($datenaissance != "00/00/0000")) { 
		$pdf->MultiCell(190,8,"Etudiant / Student : $numero_eleve - $nomprenom Né(e) le / Born : $datenaissance",1,'L',1);
	}else{
		$pdf->MultiCell(190,8,"Etudiant / Student : $numero_eleve - $nomprenom",1,'L',1);
	}

	$pdf->SetTextColor(0);
        $pdf->SetFillColor(255);

	$Y+=10;
	$X=$Xorigine=3;


// ----------------------------------------------------

	$recupUE=recupUE($idClasse,$sem); //code_ue,nom_ue,coef_ue,ects_ue,nom_ue_en
	$ectsTOTAL=0;
	// mise en place des matieres
	$largeurMat=50;
	for($f=0;$f<count($recupUE);$f++) {
		$code_ue=$recupUE[$f][0];
		$nom_ue=$recupUE[$f][1];
		$coef_ue=$recupUE[$f][2];
		$ects_ue=$recupUE[$f][3];
		$nom_ue_en=$recupUE[$f][4];
		$dejapasse=0;
		if (($sem == '1') || ($sem == '2')) {
			$listeMatiere=recupMatiereUE($code_ue,$idClasse);
  		}else{
			$listeMatiere=recupMatiereUE2($nom_ue,$idClasse);
			// u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull , a.langue , a.specif_etat , e.semestre, a.ects , a_num_semestre_info
		}
	
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

		if ($nbmatiere*$hauteurMatiere+$Y > 210) {
			$pdf->AddPage();
			$Y=7;
		}

		// ---------------------------
 		$X=$Xorigine;
		$pdf->SetFont('Arial','B',11);
		$pdf->SetFillColor(220);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(205,8,trunchaine("$nom_ue / $nom_ue_en",80),1,'L',1);
		$Y+=8;
		


		// Etude de cas
		// ------------
		

		$X=$Xorigine;
		$pdf->SetFont('Arial','',8);
		
		// test si etude de cas A FAIRE !!!!
		if ($affEtudeDeCas == "oui") {
			$pdf->SetXY($X,$Y);
			$pdf->MultiCell(205,10,"",1,'C',0);
	
			if (($sem == '1') || ($sem == '2')) {
				$verif=verifSiEtudeDeCas($code_ue,$idClasse);		
			}else{
				$verif=verifSiEtudeDeCas2(addslashes($nom_ue),$idClasse);
			}
			unset($noteEtudeDeCas);
			if (!$verif) {
				$pdf->SetXY($X,$Y);
				$pdf->MultiCell(205,10,"Pas d'étude de cas / No case study",1,'C',0);
				$Y+=10;
			}else{

			//  u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull , a.langue , a.specif_etat , e.semestre , a.ects
				for($i=0;$i<count($listeMatiere);$i++) {
					if ($listeMatiere[$i][6] != "etudedecasipac") continue;
			               	$X=$Xorigine;
	        	               	$idmatiere=$listeMatiere[$i][0];
	                	       	$idMatiere=$listeMatiere[$i][0];
		                       	$matierelong=chercheMatiereLong($idMatiere);
		                       	$matiere=$listeMatiere[$i][1];
		                       	$idprof=$listeMatiere[$i][2];
		                       	$ordreaffichage=$listeMatiere[$i][3];
		                       	$option=$listeMatiere[$i][5];
		                       	$ordre=$listeMatiere[$i][3];
					$ects=$listeMatiere[$i][8];
					$semestreInfo=$listeMatiere[$i][9];
	
	                        	$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
	                        	if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
	
	                        	// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	                        	$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffichage);
	
		                        // gestion pour les sous matiere
		                        // -----------------------------
		                        // cod_mat,sous_matiere,libelle
		                        $datasousmatiere=verifsousmatierebull($idMatiere);
		                        //      print $datasousmatiere;
		                        if ($datasousmatiere != "0") {
		                                $nomMatierePrincipale=$datasousmatiere[0][2];
		                                $nomSousMatiere=$datasousmatiere[0][1];
		                                $matiere="$nomMatierePrincipale $nomSousMatiere";
		                        }
				
					if ($triabsconet == "T4") {
		                        	if ($ects == "") $ects=recupECTS($idmatiere,$idClasse,"T4",$anneeScolaire);
					}else{
						if ($ects == "") $ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"],$anneeScolaire);
					}
		                        $codematiere=recupCodeMatiere($idmatiere);
		                        $matiereen=recupMatiereEn($idmatiere);
		//                      $coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
		                        $coef=recupCoeff($idmatiere,$idClasse,$ordre);
		                        $coef=preg_replace('/\.00$/','',$coef);
					$notePlanche=recupNotePlanche($idmatiere,$idClasse,$ordre,$anneeScolaire);
		
		                        // mise en place du cadre note
		                        // ------------------------------------------------------
		                        if (($idgroupe == "0") || (trim($idgroupe) == "")) {
		                                $note=moyenneEleveMatiereSansRattrapage($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
						$noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,'',$idprof,'');
		                        }else{
		                                $note=moyenneEleveMatiereGroupeSansRattrapage($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
						$noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,'');
					}
					unset($noteGen);

					if ($note == "") {
                	                        $absValider=verifSiAbsExamen2($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$examen);
		                        }

		                        // ---------------------------------------------------------------------------------------
		                        // Recup note exam
		                        // ---------------
		       /*               if (($idgroupe == "0") || (trim($idgroupe) == "")) {
		                                $noteexam=moyenneEleveMatiereExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$examen);
		                        }else{
		                                $noteexam=moyenneEleveMatiereGroupeExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
		                        } */
		                        // ---------------------------------------------------------------------------------------
		                        // Recup note rattrapage
		                        $noterattrapage="";
					if ($rattrapageprovisoire == "oui") {
		        	                if (($idgroupe == "0") || (trim($idgroupe) == "")) {
			                                $noterattrapage=moyenneEleveMatiereExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$rattrapage);
			                        }else{
		        	                        $noterattrapage=moyenneEleveMatiereGroupeExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$rattrapage);
		                	        }
		
		                        	if (trim($noterattrapage) != "") {
		                                	$note=$noterattrapage;
		                                	$noteexam=$noterattrapage;
		                        	}
					}
					$noteEtudeDeCas=$note;
		
		                        // ---------------------------------------------------------------------------------------
					if ($absValider != "VALIDE") {
			                        if (($note < 10) && ($note != "")) { $note="0".$note; }
			                        if ($note >= $notePlanche) {
			                                if (preg_match('/pratique professionnelle/i',$matiere)) {
			                                        if ($note < 10) {
			                              //                  $ects="";
			                                        }
			                                }
			                        }else{
			                                $ects="";
			                        }
					}
					if ($absValider == "VALIDE") {
						$note="VAL";
					}

					if ($noteNonValide == 1) {
						$note="NVAL";
						$ects="0";
						$noteNonValide=0;
					}

					if ($absValider == "ABS") { $note="ABS"; $etudecasABS="oui";  }


					if (($absValider == "ABS") && ($rattrapageprovisoire == "oui") && ($noterattrapage != "")) {
						$note=$noterattrapage;
					}
		
					unset($absValider);
					// GRADE
		                        // ---------------------------------------------------
		                        if (($note >= 17) && ($note <= 20))  {
		                                $grade="A";
		                        }elseif (($note >= 14) && ($note <= 16.99))  {
	        	                        $grade="B";
		                        }elseif (($note >= 12) && ($note <= 13.99))  {
		                                $grade="C";
		                        }elseif (($note >= 10) && ($note <= 11.99))  {
		                                $grade="D";
		                        }elseif ($note == 10) {
		                                $grade="E";
		                        }elseif (($note >= 6) && ($note <= 9.99))  {
		                                $grade="FX";
		                        }elseif (($note <= 5.99) && ($note >= 0) && ($note != ""))  {
		                                $grade="F";
		                        }else{
		                                $grade="";
		                        }

					if ($rattrapageprovisoire == "oui") {
			                        if ($noterattrapage != "") { $grade="E"; }
					}	
						
					$pdf->SetXY($X,$Y);
					$pdf->MultiCell(20,10,"ECMC2",0,'C',0);  // code matiere
					$pdf->SetXY($X+=20,$Y);
					$pdf->MultiCell(15,10,"Sem.6",0,'C',0);   // Sem.
					$pdf->SetXY($X+=15,$Y);
					$pdf->MultiCell(15,5,"Coef. /\nweight $coef",0,'C',0);   // coef /
					$pdf->SetXY($X+=15,$Y);
					$pdf->MultiCell(110,5,"Etude de cas $nom_ue\n$nom_ue_en",0,'C',0);   // libelle matiere
					$pdf->SetXY($X+=110,$Y);
					$pdf->MultiCell(20,5,"note / mark $note",0,'C',0);   // libelle matiere
					$pdf->SetXY($X+=20,$Y);
					$pdf->MultiCell(25,5,"$ects Credit(s)\nECTS",0,'C',0);   // libelle matiere
					$Y+=10;
	
					 // ------------------------------
		                        if ($noteexam != "") {
		                              //  $moyenneUE+=$noteexam;
		                              //  $nbNoteUE++;
		                        }
		
		                        if (($note != "") && ($note != "VAL")) {
		                                $moyenneUE+=$note;
		                                $nbNoteUE++;
		                        }
		                        // ------------------------------
		                        if (($note >= $notePlanche) || ($note == "VAL"))  $ectsEU+=$ects; 
				}
			}
		}
	
		$X=$Xorigine;
		// ------------------------------------------------------------------------
		unset($note);
		unset($ects);
                unset($nbNoteUE);
                unset($moyenneUE);			

		$pdf->SetFillColor(220);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(205,10,"",1,'C',1);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(20,10,"Code",0,'C',0);  
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell(15,10,"Sem.",0,'C',0);   
		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(15,5,"Coef. /\nweight",0,'C',0);   
		$pdf->SetXY($X+=15,$Y+3);
		$pdf->MultiCell(110,5,"Matière / Subject",0,'C',0);   
		$pdf->SetXY($X+=110,$Y+3);
		if ($affExam == "oui") {
			$pdf->MultiCell(10,5,"Exam.",0,'C',0);  
		}else{
			$pdf->MultiCell(10,5,"C.C.",0,'C',0);  
		}
		$pdf->SetXY($X+=10,$Y);
		$pdf->MultiCell(10,5,"Note /\nMark",0,'C',0);   
		$pdf->SetXY($X+=10,$Y);
		$pdf->MultiCell(20,5,"ECTS",0,'C',0);  
		$pdf->SetXY($X,$Y+5);
		$pdf->MultiCell(12,5,"Grade",0,'C',0);   
		$pdf->SetXY($X+=12,$Y+5);
		$pdf->MultiCell(13,5,"Credits",0,'C',0);   
		$pdf->SetFillColor(255);




		$Xmemo=$X;	
		$Ymemo=$Y+=10;		




		//  // u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull, a.langue
		for($i=0;$i<count($listeMatiere);$i++) {
			$X=$Xorigine;
			
			if ($listeMatiere[$i][6] == "etudedecasipac") continue;
			$idmatiere=$listeMatiere[$i][0];
			$idMatiere=$listeMatiere[$i][0];
			$matierelong=chercheMatiereLong($idMatiere);
			$matiere=$listeMatiere[$i][1];
			$idprof=$listeMatiere[$i][2];
			$ordreaffichage=$listeMatiere[$i][3];
			$option=$listeMatiere[$i][5];
			$ordre=$listeMatiere[$i][3];
		//	$semAff=recupSemMatiereUE($idMatiere,$code_ue);
			$semAff=$listeMatiere[$i][9];
			$ects=$listeMatiere[$i][8];

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

			if ($triabsconet == "T4") {
	                      	if ($ects == "") $ects=recupECTS($idmatiere,$idClasse,"T4");
			}else{
				if ($ects == "") $ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
			}
			$codematiere=recupCodeMatiere($idmatiere);
			$matiereen=recupMatiereEn($idmatiere);
//			$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
			$coef=recupCoeff($idmatiere,$idClasse,$ordre);
			$coef=preg_replace('/\.00$/','',$coef);
			$notePlanche=recupNotePlanche($idmatiere,$idClasse,$ordre,$anneeScolaire);

			// mise en place du cadre note 
			// ------------------------------------------------------
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {
				$note=moyenneEleveMatiereSansRattrapage($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
				if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,'',$idprof,'');
			//	$note=moyenneEleveMatiereSansExam($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
			}else{
				$note=moyenneEleveMatiereGroupeSansRattrapage($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,'');
			//	$note=moyenneEleveMatiereGroupeSansExam($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
			$noteSave=$note;
			unset($noteGen);	
			// ---------------------------------------------------------------------------------------
			// Recup note exam
			// ---------------
			if ($affExam == "oui") {
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
					$noteexam=moyenneEleveMatiereExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$examen);
					if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,'',$idprof,$examen);
	                        }else{
					$noteexam=moyenneEleveMatiereGroupeExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
					if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
	                        }
				if ($noteexam == "") {
					$absValider=verifSiAbsExamen2($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$examen);
				}
			}else{ /* controle contigu */ 
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
                                        $noteexam=moyenneEleveMatiereExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,"Contrôle continu");
					if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,'',$idprof,"Contrôle continu");
                                }else{
                                        $noteexam=moyenneEleveMatiereGroupeExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,"Contrôle continu");
					if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,"Contrôle continu");
                                }

				if (trim($noteexam) == "") {
					if (($idgroupe == "0") || (trim($idgroupe) == "")) {
                		                $noteexam=moyenneEleveMatiereSansRattrapage($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
						if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,'',$idprof,'');
		                        }else{
                                		$noteexam=moyenneEleveMatiereGroupeSansRattrapage($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
						if ($noteNonValide == 0) $noteNonValide=verifNoteNonValide($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,'');
                        		}
				}
				if ($noteexam == "") {
					$absValider=verifSiAbsExamen2($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,"Contrôle continu");
				}
			}
			// ---------------------------------------------------------------------------------------
			// Recup note rattrapage
			
			$noterattrapage="";
			if ($rattrapageprovisoire == "oui") {
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
	                                $noterattrapage=moyenneEleveMatiereExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$rattrapage);
	                        }else{
	                                $noterattrapage=moyenneEleveMatiereGroupeExamen($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$rattrapage);
	                        }
	
				if (trim($noterattrapage) != "") {
					$note=$noterattrapage;
					$noteexam=$noterattrapage;
					if ($absValider == "ABS" ) $absValider="";
				}
			}
			// ------------------------------------------------------------------------------------------------------------------------
			// égale = note colonne exam * coef + note etude de cas * coefec (soit exam*0.4+ecas*0.6 en général) pour les UE dans lesquelles il y a une étude de cas
			if ($noteEtudeDeCas != "") {
				if (strtoupper($absValider) == "ABS" ) {
					$note="ABS";
				}else{
					$etudecasABS="non";
					if ($noteexam >= 0) { 
						$note=($noteexam * $coef) + ($noteEtudeDeCas*0.6);
					}
				}
				if (($affExam != "oui") && (strtoupper($absValider) == "ABS" ))  {
					$noteexam="ABS";
					$note=$noteSave;
				}
			}else{
				if (strtoupper($absValider) == "ABS" ) {
					$noteexam="ABS";
				}
			}

			
			if ($noteNonValide == 1) {
				$note="NVAL";
				$ects="0";	
				$noteNonValide=0;
			}

			if ($absValider == "VALIDE") {
				$note="VAL";
				$noteexam="-";
			}
		
			if ($absValider != "VALIDE") {
				if (($note < 10) && ($note != "")) { $note="0".$note;  }
				if ($note >= $notePlanche) {
					if (preg_match('/pratique professionnelle/i',$matiere)) {
						if ($note < 10) {
							//$ects="";
						}
					}
				}else{
					$ects="";
				}
			}

			if (($note >= 0) && (is_numeric($note))) { $note=number_format($note,'2','.',''); }
			if (($noteexam >= 0) && (is_numeric($noteexam))) { $noteexam=number_format($noteexam,'2','.',''); }
			// GRADE
			// ---------------------------------------------------
			if (($note >= 17) && ($note <= 20))  {
				$grade="A";
			}elseif (($note >= 14) && ($note <= 16.99))  {
				$grade="B";
			}elseif (($note >= 12) && ($note <= 13.99))  {
				$grade="C";
			}elseif (($note >= 10) && ($note <= 11.99))  {
				$grade="D";
			}elseif ($note == 10) {
				$grade="E";
			}elseif (($note >= 6) && ($note <= 9.99))  {
				$grade="FX";
			}elseif (($note <= 5.99) && ($note >= 0) && ($note != ''))  {
				$grade="F";
			}else{
				$grade="";
				$ects="0";
			}

			if ($rattrapageprovisoire == "oui") { if ($noterattrapage != "") { $grade="E"; } } 

			if ($absValider == "VALIDE") { $grade=""; } 
			
			if ($note == "0NVAL") { $ects="0" ; }
			unset($absValider);

			//$moyenEU=moyenEleveUE($code_ue,$idClasse,$idEleve,$_POST["saisie_trimestre"],$dateDebutP1,$dateFinP1,$ordreaffichage);
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($X,$Y);	
			$pdf->MultiCell(20,$hauteurMatiere,"$codematiere",1,'C',0);  
			$pdf->SetXY($X+=20,$Y);
			$pdf->MultiCell(15,$hauteurMatiere,"$semAff",1,'C',0);   
			$pdf->SetXY($X+=15,$Y);
			$pdf->MultiCell(15,$hauteurMatiere,"$coef",1,'C',0);   
			$pdf->SetXY($X+=15,$Y);
			$pdf->MultiCell(110,$hauteurMatiere,trunchaine("$matiere / $matiereen",70),1,'C',0);   
			$pdf->SetXY($X+=110,$Y);
			if ($etudecasABS == "oui") { $note="ABS"; $ects=""; $grade=""; } 
			$noteexam=preg_replace('/0ABS/','ABS',$noteexam);
			$pdf->MultiCell(10,$hauteurMatiere,"$noteexam",1,'C',0);  
			$pdf->SetXY($X+=10,$Y);
			$note=preg_replace('/0ABS/','ABS',$note);
			$note=preg_replace('/0NVAL/','NVAL',$note);
			$pdf->MultiCell(10,$hauteurMatiere,"$note",1,'C',0);   
			$pdf->SetXY($X+=10,$Y);
			$pdf->MultiCell(12,$hauteurMatiere,"$grade",1,'C',0);   
			$pdf->SetXY($X+=12,$Y);
			$pdf->MultiCell(13,$hauteurMatiere,"$ects",1,'C',0); 
			$pdf->SetXY($X+=10,$Y);	
			$Y+=$hauteurMatiere;
			
			// ------------------------------

			if ($noteexam != "") {
			//	$moyenneUE+=$noteexam;
			//	$nbNoteUE++;
			}

			if ((trim($note) != "") && ($note != "VAL"))  {
				$moyenneUE+=($note*$coef);
                                $nbNoteUE+=$coef;
			}
			// ------------------------------
			$ectsEU+=$ects;

			unset($noteexam);
			unset($note);

		}


		unset($etudecasABS);

		if ($nbNoteUE > 0) { 
			$moyenneUE=$moyenneUE/$nbNoteUE;
			$moyenneUEAff=number_format($moyenneUE,'2',',',' ');		
		}
		$moyenneUE=number_format($moyenneUE,'2',',',' ');		
	// ------------------------------


		if (($moyenneUE >= 17) && ($moyenneUE <= 20))  {
			$gradeUE="A";
                }elseif (($moyenneUE >= 14) && ($moyenneUE <= 16.99))  {
			$gradeUE="B";
                }elseif (($moyenneUE >= 12) && ($moyenneUE <= 13.99))  {
			$gradeUE="C";
                }elseif (($moyenneUE >= 10) && ($moyenneUE <= 11.99))  {
			$gradeUE="D";
                }elseif ($moyenneUE == 10) {
			$gradeUE="E";
                }elseif (($moyenneUE >= 6) && ($moyenneUE <= 9.99))  {
			$gradeUE="FX";
                }elseif (($moyenneUE <= 5.99) && ($moyenneUE >= 0) && ($moyenneUE != '')) {
			$gradeUE="F";
                }else{
			$gradeUE="";
                }
	

		if ($moyenneUEAff == "") $gradeUE="";

		$X=$Xorigine;
		$pdf->SetFillColor(220);
		$pdf->SetXY($X,$Y);	
		$pdf->MultiCell(205,20,"",1,'C',1);  
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($X,$Y+2);	
		$pdf->MultiCell(120,4,"Moyenne $nom_ue /\n Average mark for $nom_ue_en",0,'R',0); 
		$pdf->SetXY($X+120,$Y+2);	

		// pour les niveaux 3, 4 et 5
		if (($niveauClasse == "A3") || ($niveauClasse == "A4") || ($niveauClasse == "A5")) {
			if (( $moyenneUEAff < 10) && ( $moyenneUEAff != "")) { $ectsEU=""; 	}
		}

		if ($admisManuel == 1) {
			$pdf->MultiCell(85,4,"$moyenneUEAff        ECTS : Grade $gradeUE                         ",0,'R',0); 
		}else{
			$pdf->MultiCell(85,4,"$moyenneUEAff        ECTS : Grade $gradeUE        $ectsEU Credit(s)",0,'R',0); 
		}

		$pdf->SetFillColor(255);
		$Y+=21;

		if (trim($moyenneUEAff) != "" ) {
			$moyenneUEAff=preg_replace('/,/','.',$moyenneUEAff);
			$moyenneGenerale+=$moyenneUEAff;
			$nbmoyenneGenerale++;
		}

		$ectsGenerale+=$ectsEU;

		unset($moyenneUE);
		unset($moyenneUEAff);
		unset($gradeUE);
		unset($nbNoteUE);
		unset($ectsEU);
}
// fin de la mise en place des matiere

if ($Y >= 240) {
	$pdf->AddPage();
	$Y=10;
}

if ($nbmoyenneGenerale > 0) { 
	$moyenneGenerale=$moyenneGenerale/$nbmoyenneGenerale;
	$moyenneGeneraleAff=number_format($moyenneGenerale,'2',',',' ');		
}

/*
if (($moyenneGenerale >= 17) && ($moyenneGenerale <= 20))  {
	$gradeGeneral="A";
}elseif (($moyenneGenerale >= 14) && ($moyenneGenerale <= 16.99))  {
	$gradeGeneral="B";
}elseif (($moyenneGenerale >= 12) && ($moyenneGenerale <= 13.99))  {
	$gradeGeneral="C";
}elseif (($moyenneGenerale >= 10) && ($moyenneGenerale <= 11.99))  {
	$gradeGeneral="D";
}elseif ($moyenneGenerale == 10) {
	$gradeGeneral="E";
}elseif (($moyenneGenerale >= 6) && ($moyenneGenerale <= 9.99))  {
	$gradeGeneral="FX";
}elseif (($moyenneGenerale <= 5.99) && ($moyenneGenerale >= 0) && ($moyenneGenerale != ''))  {
	$gradeGeneral="F";
}else{
	$gradeGeneral="";
}
*/



$X=$Xorigine;
$pdf->SetFont('Arial','B',14);
$pdf->SetFillColor(220);
$pdf->SetXY($X+5,$Y+=2);
if (($niveauClasse == "A3") || ($niveauClasse == "A4") || ($niveauClasse == "A5")) {
	if ( $moyenneGeneraleAff < 10) { $ectsGenerale=""; 	}
}

if (($moyenneGeneraleAff >= 17) && ($moyenneGeneraleAff <= 20))  {
        $gradeGeneral="A";
}elseif (($moyenneGeneraleAff >= 14) && ($moyenneGeneraleAff <= 16.99))  {
        $gradeGeneral="B";
}elseif (($moyenneGeneraleAff >= 12) && ($moyenneGeneraleAff <= 13.99))  {
        $gradeGeneral="C";
}elseif (($moyenneGeneraleAff >= 10) && ($moyenneGeneraleAff <= 11.99))  {
        $gradeGeneral="D";
}elseif ($moyenneGeneraleAff == 10) {
        $gradeGeneral="E";
}elseif (($moyenneGeneraleAff >= 6) && ($moyenneGeneraleAff <= 9.99))  {
        $gradeGeneral="FX";
}elseif (($moyenneGeneraleAff <= 5.99) && ($moyenneGeneraleAff >= 0) &&  ($moyenneGeneraleAff != '')) {
        $gradeGeneral="F";
}else{
        $gradeGeneral="";
}



if ($admisManuel == 1) $ectsGenerale=60; 


$point=recupPtAbsBulletin($idEleve,$_POST["saisie_trimestre"],$anneeScolaire,$idclasse);
if ($point > 0) {
	$moyenneGeneraleAff=preg_replace('/,/','.',$moyenneGeneraleAff);
	$moyenneGeneraleAff=$moyenneGeneraleAff-$point; 
	$textPT="Sanction en cas d'absences injustifiées sur la moyenne générale annuelle / ";
	$textPT.="In the event of unjustified absences, penalties on the annual general grade will be applied : $point \n";
	$moyenneGeneraleAff=preg_replace('/\./',',',$moyenneGeneraleAff);
}



$pdf->MultiCell(195,15,"Moyenne Générale / General average  $moyenneGeneraleAff    ECTS : Grade $gradeGeneral     $ectsGenerale Credit(s)",1,'C',1);
$Y+=18;

unset($moyenneGenerale);
unset($moyenneGeneraleAff);
unset($gradeGeneral);
unset($nbmoyenneGenerale);
	
// $pdf->AddPage();

$provisoire=""; $provisoireEN="";
if ($bulletinProvisoire == "oui") { $provisoire="Provisoirement"; $provisoireEN="Provisionaly"; }

// A1 A2 A3 A4 A5 PREPA
$valeurIPACBULL=recupIpacBulletin($idEleve,$anneeScolaire,$idClasse,$bulletinProvisoire);

if (($niveauClasse == "A1") || ($niveauClasse == "A2") || ($niveauClasse == "A4")) {
	$afficheAdmis.="<tr  class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
	$afficheAdmis.="<td align='right' >$nomEleve  $prenomEleve : <input type='hidden' name='idclasse' value='$idClasse' /></td>";
	if ((($valeurIPACBULL == "1") || ($valeurIPACBULL == "0")) && (trim($valeurIPACBULL) != ""))  {
		if ($valeurIPACBULL == "1")  {
			$texte1="Admis(e) en année supérieure / Admitted to next level";
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve' checked='checked' > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve'  > Non Admis </td></tr>"; 
		}else{
			$texte1="$provisoire Admis(e) en année supérieure sous conditions / $provisoireEN Admitted to next level under conditions";
			//$texte1="$provisoire Non Admis en année supérieure / $provisoireEN Not Admitted to next level";
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve'  > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve' checked='checked' > Non Admis</td></tr>"; 
		}
	}else{
		if ($ectsGenerale >= 60) {
			$texte1="Admis(e) en année supérieure / Admitted to next level";
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve' checked='checked' > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve'  > Non Admis </td></tr>"; 
		}else{
			$texte1="$provisoire Admis(e) en année supérieure sous conditions / $provisoireEN Admitted to next level under conditions";
			//$texte1="$provisoire Non Admis en année supérieure / $provisoireEN Not Admitted to next level";
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve'  > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve' checked='checked' > Non Admis</td></tr>"; 
		}
	}
}


if (($niveauClasse == "A3") || ($niveauClasse == "A5")) {
	$afficheAdmis.="<tr  class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
	$afficheAdmis.="<td align='right' >$nomEleve  $prenomEleve : <input type='hidden' name='idclasse' value='$idClasse' /></td>";
	if ((($valeurIPACBULL == "1") || ($valeurIPACBULL == "0")) && (trim($valeurIPACBULL) != ""))  {
		if ($valeurIPACBULL == "1") {
			$texte1=ucfirst("$provisoire Admis au titre \n$provisoireEN Graduated ");
			if ($provisoire == "") {
				$texte2="";
			}else{
				$texte2="- En attente de décision finale du jury \n- Waiting for jury's final decision";
			}
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve' checked='checked' > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve'  > Non Admis </td></tr>"; 
		}else{
			$texte1=ucfirst("$provisoire Non Admis(e) au titre \n$provisoireEN No Graduation ");
			$texte2="- Proposition de session de rattrapage \n- Retake session proposed.";
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve'  > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve' checked='checked' > Non Admis</td></tr>"; 
		}
	}else{
		if ($ectsGenerale >= 60) {
			$texte1=ucfirst("$provisoire Admis au titre \n$provisoireEN Graduated ");
			if ($provisoire == "") {
				$texte2="";
			}else{
				$texte2="- En attente de décision finale du jury \n- Waiting for jury's final decision";
			}
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve' checked='checked' > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve'  > Non Admis </td></tr>"; 
		}else{
			$texte1=ucfirst("$provisoire Non Admis(e) au titre \n$provisoireEN No Graduation ");
			$texte2="- Proposition de session de rattrapage \n- Retake session proposed.";
			$afficheAdmis.="<td><input type='radio' value='1' name='admis_$idEleve'  > Admis(e)</td>"; 
			$afficheAdmis.="<td><input type='radio' value='0' name='admis_$idEleve' checked='checked' > Non Admis</td></tr>"; 
		}

	}
}

unset($ectsGenerale);

if ($Y >= 210) {
	$pdf->AddPage();
	$Y=10;
}

$largeurBulle=115;

$pdf->SetFont('Arial','B',8);
$hpt=5;
if ($point > 0) { 
	$pdf->SetFont('Arial','B',6); 
	$hpt=3;
}
$pdf->SetFillColor(43,164,136);
$pdf->SetTextColor(255,255,255);
$pdf->RoundedRect($X=$Xorigine, $Y+=0, $largeurBulle, 17, 3.5, 'DF');
$pdf->SetXY($X,$Y+1);
if ($point > 0) {
	$pdf->MultiCell($largeurBulle,$hpt,"$textPT",0,'R',0);
	$pdf->SetXY($X,$Y+=7);
}

$pdf->MultiCell($largeurBulle/2,$hpt,"$texte1",0,'R',0);
$pdf->SetXY($X+($largeurBulle/2),$Y+3);
$pdf->MultiCell($largeurBulle/2,5,"$texte2",0,'L',0);

$pdf->SetFillColor(255);
$pdf->SetTextColor(0);

$YSuite=$Y+10;

// ----------------------------------------------------------------------------------------------------------------------
$largeurBulle=120;
$tailleInfo=3;
$hauteurInfo=5;
$hauteurInfo2=6;
$largeurInfo=8;

$pdf->SetFont('Arial','B',$tailleInfo+1);
$pdf->SetFillColor(43,164,136);
$pdf->SetXY($X=$largeurBulle,$Y);
$pdf->MultiCell($largeurInfo,$hauteurInfo,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo,3,"Grade",0,'C',0);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo,"Note / Grade",1,'C',1);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo,"Définition",1,'C',1);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo,"Definition and appreciation",1,'C',1);
$pdf->SetXY($X+=$largeurInfo+24+1,$Y);
$pdf->SetFont('Arial','',$tailleInfo);
$pdf->SetFillColor(220);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"A",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell(20,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"17 <= grade <=\n20",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"EXCELLENT - performance remarquable avec seulement quelques erreurs mineures",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"EXCELLENT - outstanding performance with only minor errors",0,'C',0);
$pdf->SetFillColor(192);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo2);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"B",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"14 <= grade <=\n16.99",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"TRES BIEN - au dessus des standards généraux mais avec quelques erreurs",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"VERY GOOD - above the average standard but with some errors",0,'C',0);
$pdf->SetFillColor(220);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo2);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"C",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"12 <= grade <=\n13.99",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"BIEN - travail de bonne qualité avec un certain nombre d'erreurs",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"GOOD - generally sound work with a number of notable errors",0,'C',0);
$pdf->SetFillColor(192);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo2);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"D",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"10 <= grade <=\n11.99",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"SATISFAISANT - assez bon mais avec des lacunes significatives",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"SATISFACTORY - fair but with significant shortcomings",0,'C',0);
$pdf->SetFillColor(220);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo2);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"E",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"grade = 10",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"SUFFISANT - travail répondant aux exigences minimum ou NOTE OBTENUE AU RATTRAPAGE",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"SUFFICIENT - performance meets the minimum criteria or RESIT MARK",0,'C',0);
$pdf->SetFillColor(192);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo2);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"FX",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"06 <= grade <=\n09.99",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"ECHEC - rattrapage envisageable",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"FAIL - possible resit",0,'C',0);
$pdf->SetFillColor(220);
$pdf->SetXY($X=$largeurBulle,$Y+=$hauteurInfo2);
$pdf->MultiCell($largeurInfo,$hauteurInfo2,"F",1,'C',1);
$pdf->SetXY($X+=$largeurInfo,$Y);
$pdf->MultiCell($largeurInfo+7,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+7,2,"grade <= 05.99",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+7,$Y);
$pdf->MultiCell($largeurInfo+22,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+22,2,"ECHEC - rattrapage obligatoire",0,'C',0);
$pdf->SetXY($X+=$largeurInfo+22,$Y);
$pdf->MultiCell($largeurInfo+24,$hauteurInfo2,"",1,'C',1);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell($largeurInfo+24,2,"FAIL - subject must be resit",0,'C',0);



$pdf->SetFont('Arial','',12);
$pdf->SetFillColor(255);
$pdf->RoundedRect($X=$Xorigine, $Y=$YSuite+10, $largeurBulle-5, 27, 3.5, 'DF');
$pdf->SetXY($X+2,$Y+1);
$pdf->MultiCell(100,5,"\nVisa du Responsable pédagogique\nCourse leader's signature\n$nom_etablissement",0,'L',0);
$datedujour=date("d/m/Y");
$pdf->SetXY($X+2,$Y+20);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(110,5,"$datedujour",0,'R',0);
$pdf->SetFont('Arial','',12);

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
$trimfichier=$_POST["saisie_trimestre"];
if ($_POST["saisie_trimestre"] == "trimestre3") $trimfichier="annuel";
$fichier="./data/pdf_bull/$classe_nom/bulletin_".$nomEleve."_".$prenomEleve."_".$trimfichier.".pdf";
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
if($cr == 1) history_cmd($_SESSION["nom"],"CREATION BULLETIN","Classe : $classe_nom");
Pgclose();

$provisoire="définitives";
if ($_POST["bulletinProvisoire"] == "oui") $provisoire="provisoire";
if (($niveauClasse == "A1") || ($niveauClasse == "A2") || ($niveauClasse == "A4") || ($niveauClasse == "A3") || ($niveauClasse == "A5")  ) {
	print  "<form method='post' action='bulletin_construction04UE.php' >";
	print  "<ul><font class='T2'>Liste des admissions $provisoire actuellement positionnées : </font>";
	$saisie="Saisie AUTO";
	if (count($valeurIPACBULL)) $saisie="Saisie MANUELLE"; 
	print  "(<font id='color3'><b>$saisie</b></font>)</ul>";
	print  "<ul><table border='1' style='border-collapse: collapse;' width='80%' >$afficheAdmis" ;
	print  "</table><br><br>";
	print  "<table align='center'><tr><td><script language='JavaScript'>buttonMagicSubmit('Modifier les données','createadmis');</script></td></tr></table>";
	print  "</ul><br><br>";
	print  "<input type='hidden' name='saisie_classe' value='".$_POST["saisie_classe"]."' />";
	print  "<input type='hidden' name='saisie_trimestre' value='".$_POST["saisie_trimestre"]."' />";
	print  "<input type='hidden' name='plageEleve' value='".$_POST["plageEleve"]."' />";
	print  "<input type='hidden' name='bulletinProvisoire' value='".$_POST["bulletinProvisoire"]."' />";
	print  "<input type='hidden' name='hauteurMatiere' value='".$_POST["hauteurMatiere"]."' />";
	print  "<input type='hidden' name='affEtudeDeCas' value='".$_POST["affEtudeDeCas"]."' />";
	print  "<input type='hidden' name='affExam' value='".$_POST["affExam"]."' />";
	print  "<input type='hidden' name='typetrisem' value='".$_POST["typetrisem"]."' />";
	print  "<input type='hidden' name='annee_scolaire' value='".$_POST["annee_scolaire"]."' />";

	print  "</form>";
}
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
