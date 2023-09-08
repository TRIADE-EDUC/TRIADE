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
$nofooterPDF=NOFOOTERPDF;
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
nettoyage_repertoire("./data/tmp/");
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


$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutP1=$dateRecup[$j][0];
	$dateFinP1=$dateRecup[$j][1];
}
$dateDebutP1=dateForm($dateDebutP1);
$dateFinP1=dateForm($dateFinP1);

$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutP2=$dateRecup[$j][0];
	$dateFinP2=$dateRecup[$j][1];
}
$dateDebutP2=dateForm($dateDebutP2);
$dateFinP2=dateForm($dateFinP2);

$dateRecup=recupDateTrimByIdclasse("trimestre3",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutP3=$dateRecup[$j][0];
	$dateFinP3=$dateRecup[$j][1];
}
$dateDebutP3=dateForm($dateDebutP3);
$dateFinP3=dateForm($dateFinP3);

$hauteurmatiere=$_POST["hauteurmatiere"];
$hauteurphotoE=$_POST["hauteurphoto"];
$affphotoeleve=$_POST["affphotoeleve"];
$largeurphotoE=$_POST["largeurphoto"];

config_param_ajout($hauteurmatiere,"hauteurmatiere");
config_param_ajout($hauteurphotoE,"hauteurphoto");
config_param_ajout($affphotoeleve,"affphotoeleve");
config_param_ajout($largeurphotoE,"largeurphoto");

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];


if (trim($hauteurphoto) == "") {
        $hauteurphoto=16.3;
        $largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
        $hauteurlogo=25;
        $largeurlogo=40;
}


// recup année scolaire
$anneeScolaire=$_COOKIE['anneeScolaire'];
if ($anneeScolaire == "") $anneeScolaire=$_COOKIE['anneeScolaire'];

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
include_once('bulletin_construction05UE-graph.php');

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

$idClasse=$_POST["saisie_classe"];

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF('L');  // declaration du constructeur

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

$moyenClasseGen=""; // pour le calcul moyenne classe
$moyenClasseMin=1000; // pour la calcul moyenne min classe
$nbeleve=0;

// pour le calcul de moyenne classe

if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }

 
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
	$random=rand(1000,9999);
	$ficGraph=md5($random.$idEleve).".png";

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
	$coordonne4="E-mail : $mail / $urlsite";


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
	//$photo=recup_photo_bulletin_idsite(chercheIdSite($_POST["saisie_classe"]));
	$logo="./image/banniere/banniere-clesi.jpg";
	if (file_exists($logo)) {
		$xlogo=$largeurlogo;
		$ylogo=$hauteurlogo;
		$xcoor0=45;
		$ycoor0=3;
		$xtitre=90; // avec logo
		$pdf->Image($logo,3,3,$xlogo,$ylogo);
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
	$pdf->SetXY($xcoor0,$ycoor0+=20);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees

	$photo=image_bulletin($idEleve);
	$xphoto=270;
	$yphoto=5;
	if (($affphotoeleve == "oui") && (file_exists($photo))) {
		if (($largeurphotoE != "") && ($hauteurphotoE != "")) { 
			$photowidth=$largeurphotoE/2;
			$photoheight=$hauteurphotoE/2;
			$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
			
		}
	}
	// fin d'insertion
	
	$pdf->SetFont('Arial','B',12);
	$Pdate="ANNEE ".$anneeScolaire."   ".$textTrimestre;
	$pdf->SetXY(3,$ycoor0+=7);
	$pdf->MultiCell(90,3,"$Pdate",0,'L',0);


	// adresse de l'élève
	//  elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance,email_eleve
	$dataadresse=chercheadresse($idEleve);
	$nomtuteur=$dataadresse[0][1];
	$prenomtuteur=$dataadresse[0][2];
	$adr1=trim($dataadresse[0][3]);
	$code_post_adr1=trim($dataadresse[0][4]);
	$commune_adr1=trim($dataadresse[0][5]);
	$numero_eleve=$dataadresse[0][9];
	$datenaissance=$dataadresse[0][11];
	if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
	$regime=$dataadresse[0][12];
	$class_ant=trim(trunchaine($dataadresse[0][10],20));
	$emailEleve=recupEmail("menueleve",$idEleve,'');
	// fin cadre du haut
	$X=3;
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X,$ycoor0+=7);
	$pdf->MultiCell(70,5,"Nom : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+10,$ycoor0);
	$pdf->MultiCell(60,5,"$nomEleve",0,'L',0);


	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+=70,$ycoor0);
	$pdf->MultiCell(70,5,"Prénom : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+13,$ycoor0);
	$pdf->MultiCell(60,5,"$prenomEleve",0,'L',0);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+=70,$ycoor0);
	$pdf->MultiCell(70,5,"Date de naissance : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+27,$ycoor0);
	$pdf->MultiCell(50,5,"$datenaissance",0,'L',0);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+=70,$ycoor0);
	$pdf->MultiCell(80,5,"E-mail : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+12,$ycoor0);
	$pdf->MultiCell(70,5,"$emailEleve",0,'L',0);


	$X=3;
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X,$ycoor0+=7);
	$pdf->MultiCell(90,5,"Adresse : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+15,$ycoor0);
	$pdf->MultiCell(80,5,"$adr1",0,'L',0);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+=90,$ycoor0);
	$pdf->MultiCell(50,5,"Code Postal : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+20,$ycoor0);
	$pdf->MultiCell(30,5,"$code_post_adr1",0,'L',0);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+=50,$ycoor0);
	$pdf->MultiCell(70,5,"Ville : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+12,$ycoor0);
	$pdf->MultiCell(50,5,"$commune_adr1",0,'L',0);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+=70,$ycoor0);
	$pdf->MultiCell(80,5,"Filière : ",1,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X+12,$ycoor0);
	$classeNom=preg_replace('/_/',' ',$classe_nom);
	$pdf->MultiCell(70,5,"$classeNom",0,'L',0);

	$nbnoteaffiche=6;

//	$nomEleve
//	$prenomEleve

	$Y=$ycoor0+=10;
	$Xorigine=3;
	$largeurMat=80;

// -------------------------------------------------------------------------------------------
	// Barre des titres
	$X=45;
	$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(220);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell($largeurMat,10,"MATIERES",1,'C',1);
	$pdf->SetXY($X+=$largeurMat,$Y);
	$pdf->MultiCell(10,10,"ECTS",1,'C',1);
	$pdf->SetXY($X+=10,$Y);
	$pdf->MultiCell(10*$nbnoteaffiche,10,"NOTES",1,'C',1);
	$pdf->SetXY($X+=10*$nbnoteaffiche,$Y); 

	$pdf->MultiCell(40,5,"MOYENNE DE",1,'C',1);
	$pdf->SetXY($X,$Y+5); 
	$pdf->MultiCell(20,5,"L'ETUDIANT",1,'C',1);
	$pdf->SetXY($X+=20,$Y+5);
	$pdf->MultiCell(20,5,"LA CLASSE",1,'C',1);
	$pdf->SetXY($X+=20,$Y);
	$pdf->MultiCell(20,10,"VALIDATION",1,'C',1);
	$pdf->SetXY($X+=20,$Y);
	$pdf->MultiCell(10,10,"ECTS",1,'C',1);
	$pdf->SetFillColor(255);
	$pdf->SetFont('Arial','',8);

	$X=$Xorigine;
	$Y+=10;
		// ----------------------------------------------------

	
		$ordre=ordre_matiere_visubull_trim($_POST["saisie_classe"],$_POST["saisie_trimestre"]);
		$hauteurMatiere=$hauteurmatiere; // taille du cadre matiere
		$jj=0;
		for($i=0;$i<count($ordre);$i++) {
			$matiere=chercheMatiereNom($ordre[$i][0]);


			$idMatiere=$idmatiere=$ordre[$i][0];
			$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
			//$nomprof=recherche_personne2($ordre[$i][1]);
			$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
			if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
	   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);	
			$X=45;
			$matierelong=chercheMatiereLong($idMatiere);
			$ordreaffichage=$ordre[$i][2];
			
			if ($Y >= 175) {
				$pdf->AddPage();
				$Y=5;
			}
			
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
	
	
			$ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
			$codematiere=recupCodeMatiere($idmatiere);
			$matiereen=recupMatiereEn($idmatiere);
			$coef=recupCoeff($idmatiere,$idClasse,$ordre[$i][2]);
			$coef=preg_replace('/\.00$/','',$coef);

			// ---------------------------------------------------------------------------------------
			// mise en place du cadre note 
			// ---------------------------------------------------------------------------------------
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {
				$note=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
			}else{
				$note=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
	
			$admisFond=1;
			if ($note >= 10) { $admisFond=0; }
			$pdf->SetFillColor(220);

			$pdf->SetFont('Arial','',10);
			$pdf->SetXY($X,$Y);
			$matiere=$matierelong;
			$pdf->MultiCell($largeurMat,$hauteurMatiere,"",1,'C',$admisFond); 
			$pdf->SetXY($X,$Y+2);
				
			// mise en place du nom du prof
			$profAff=recherche_personne2($idprof);
			$jj++;
			$listMAT.=$jj.",";

			$pdf->MultiCell($largeurMat,2,"$matiere N° $jj ",0,'L',0);
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY($X,$Y+5);
			//$pdf->MultiCell($largeurMat,3,"$profAff",0,'L',0);
			$pdf->SetXY($X+=$largeurMat,$Y);
			$pdf->SetFont('Arial','B',9);
			$ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);	
			$pdf->MultiCell(10,$hauteurMatiere,"$ects",1,'C',$admisFond); 
			$pdf->SetFont('Arial','',9);
			
			$ectsAuTotal+=$ects;
	
			if ($note == "") {
				$listMOE.="0,";
			}else{
				$listMOE.="$note,";
			}

			$admis="Non Admis";
			if ($note >= 10) {
				$admis="Admis"; 
				$totalEcts+=$ects;
			}
			// mise en place des moyennes de classe
			if ($idgroupe == "0") {
	           	// idMatiere,datedebut,dateFin,idclasse
	           		$moyeMatGen=moyeMatGen($idmatiere,$dateDebut,$dateFin,$idClasse,$idprof);
	    		}else {
	           		$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
	    		}
			
			//$dataMOC[]="'".number_format($moyeMatGen,'2','.','')."'";
			if ($moyeMatGen == "") { 
				$listMOC.="0,";
			}else{
				$listMOC.="$moyeMatGen,";
			}

			$listeNotes=recupNote2($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
			for($ii=0;$ii<$nbnoteaffiche;$ii++) {
				if ($listeNotes[$ii][0] != "") {
					if ($listeNotes[$ii][0] < 0) {
						$listeNotes[$ii][0]=preg_replace('/.00/','',$listeNotes[$ii][0]);
						$listeNotes[$ii][0]=preg_replace('/-1/','abs',$listeNotes[$ii][0]);
						$listeNotes[$ii][0]=preg_replace('/-2/','disp',$listeNotes[$ii][0]);
						$listeNotes[$ii][0]=preg_replace('/-3/',' ',$listeNotes[$ii][0]);
						$listeNotes[$ii][0]=preg_replace('/-4/','DNN',$listeNotes[$ii][0]);
						$listeNotes[$ii][0]=preg_replace('/-5/','DNR',$listeNotes[$ii][0]);
						$listeNotes[$ii][0]=preg_replace('/-6/','VAL',$listeNotes[$ii][0]);
						$notA=$listeNotes[$ii][0];
						$notAA=1;
					}else{
						$notA=number_format($listeNotes[$ii][0],'2',',',' ');
						$notAA=0;
					}
				}else{
					$notA="";		
				}
				$pdf->SetXY($X+=10,$Y);
				if (($notA >= 0) && ($notA < 10) && ($notAA == 0)) {				     	
					$pdf->SetTextColor(255,0,0); 
				}else{ 
					$pdf->SetTextColor(0,0,0); 
				}
				$pdf->MultiCell(10,$hauteurMatiere,"$notA",1,'C',$admisFond);   // notes
				$pdf->SetTextColor(0,0,0);
			}
			$pdf->SetXY($X+=10,$Y);
			if (($note >= 0) && ($note < 10)) {				     	
				$pdf->SetTextColor(255,0,0); 
			}else{ 
				$pdf->SetTextColor(0,0,0); 
			}

			if ($note != "") {
				$moyenEleve+=$note*$coef;
				$coefTotal+=$coef;
			}

			$pdf->SetFont('Arial','B',9);
			$pdf->MultiCell(20,$hauteurMatiere,"$note",1,'R',$admisFond);   // moyen eleve
			$pdf->SetXY($X+=20,$Y);
			$pdf->SetTextColor(0,0,0); 
			$pdf->MultiCell(20,$hauteurMatiere,"$moyeMatGen",1,'R',$admisFond);   // moyen classe
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($X+=20,$Y);
			$pdf->SetFont('Arial','',9);
			$pdf->MultiCell(20,$hauteurMatiere,"$admis",1,'R',$admisFond);   // validation
			$pdf->SetXY($X+=20,$Y);
			$pdf->SetFont('Arial','B',9);
			$pdf->MultiCell(10,$hauteurMatiere,"$ects",1,'C',$admisFond);  // ECTS
			$pdf->SetFont('Arial','',9);


			$Y+=$hauteurMatiere;

		}
	
	$moyenEleveGen=moyGenEleve($moyenEleve,$coefTotal);
	unset($moyenEleve);
	unset($coefTotal);
		
	$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$listeMatiere);
	$pdf->SetFillColor(220);
		
	$pdf->SetFont('Arial','B',9);
	$pdf->SetXY($X=45+$largeurMat,$Y);
	$pdf->MultiCell(10,$hauteurMatiere,"$ectsAuTotal",1,'C',1);   // total ECTS 
	unset($ectsAuTotal);

	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($X+=($nbnoteaffiche*10)-30,$Y);
	$pdf->MultiCell(40,$hauteurMatiere,"Moyenne général : ",1,'C',1);   
	
	$pdf->SetXY($X+=40,$Y);
	if (($moyenEleveGen >= 0) && ($moyenEleveGen < 10)) { 
		$pdf->SetTextColor(255,0,0); 
	}else{ 
		$pdf->SetTextColor(0,0,0); 
	}
	$pdf->SetFont('Arial','B',9);
	$pdf->MultiCell(20,$hauteurMatiere,"$moyenEleveGen",1,'R',1);   // moyenne general eleve

	$pdf->SetTextColor(0,0,0);
	$pdf->SetXY($X+=20,$Y);
	$pdf->MultiCell(20,$hauteurMatiere,"$moyenClasseGen",1,'R',1);   // moyenne general classe

	$pdf->SetXY($X+=40,$Y);
	$pdf->MultiCell(10,$hauteurMatiere,"$totalEcts",1,'C',1);   // total ECTS par eleve
	unset($totalEcts);

	$pdf->SetFillColor(255);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY($X=3+$largeurMat+60,$Y+=$hauteurMatiere*2);
	$Yimg=$Y;
	$pdf->MultiCell(120,5,"Centre Libre d'Enseignement Supérieur International",0,'C',0);   
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($X,$Y+=5);
	$pdf->MultiCell(120,5,"Responsable pédagogique",0,'C',0);
	
	$pdf->SetXY($X,$Y+=$hauteurMatiere);
	$pdf->MultiCell(120,30,"",1,'C',0);   // moyenne general classe
	

	$listMOE=preg_replace('/,$/','',$listMOE);
	$listMOC=preg_replace('/,$/','',$listMOC);
	$listMAT=preg_replace('/,$/','',$listMAT);
	$listMAT=preg_replace('/\'/','',$listMAT);
		
	$dataMOE=explode(',',$listMOE);
	$dataMOC=explode(',',$listMOC);
	$dataMAT=explode(',',$listMAT);

	unset($listMOE);
	unset($listMOC);
	unset($listMAT);

//	$dataMOE=array(47,80,40);
//        $dataMOC=array(61,30,82);
//        $dataMAT=array('A','B','C');


	graphBulletin($ficGraph,$dataMOE,$dataMOC,$dataMAT);
	$fic="./data/tmp/$ficGraph";
	if (file_exists("$fic")) {
        	$pdf->Image($fic,5,$Yimg,130,50);
        }

	
	// fin de la mise en place des matiere



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
	$pdf=new PDF('L');
} // fin du for on passe à l'eleve suivant

$merge->output("./data/pdf_bull/$classe_nom/liste_complete.pdf");
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
	$listing=preg_replace('/\(/',"\(",$listing);
	$listing=preg_replace('/\)/',"\)",$listing);
	$cmd="gs -q -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=./data/pdf_bull/$classe_nom/liste_complete.pdf -dBATCH $listing";
	$null=system("$cmd",$retval);
}

include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/'.$classe_nom.'.zip';
$bttexte="Récupérer le fichier ZIP des bulletins";
nettoyage_repertoire('./data/pdf_bull/'.$classe_nom);
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
