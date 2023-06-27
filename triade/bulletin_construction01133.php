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
	set_time_limit(0);
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
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
if (count($valeur)) {

if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; }
}

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

$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere


$hauteurmatiereP=$_POST["hauteurmatiere"];
config_param_ajout($hauteurmatiereP,"hauteurMatBull209");
$policecaractere=$_POST["policecaractere"];
config_param_ajout($policecaractere,"policecaractere");
$photobulleleve=$_POST["photobulleleve"];
config_param_ajout($photobulleleve,"photobulleleve");

//INSERTION RANG AB LE 310309


//

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
			//$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]);
			$coeffaff=1;
			$ii++;
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coeffaff;
		                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                		$coefEleG=$coefEleG + $coeffaff;
			}
		}
		if ($noteMoyEleG != "") {
			$moyenEleve2=moyGenEleve($noteMoyEleG,$coefEleG);
		}
		if (trim($moyenEleve2) != "") {
			$moyenEleve2=preg_replace('/,/','.',$moyenEleve2);
			$classementG[]=$moyenEleve2; //ab 310309
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
// fin min et max
// -------------

if (($moyenClasseMax < 10) && ($moyenClasseMax!="")) { $moyenClasseMax="0".$moyenClasseMax; }
if (($moyenClasseMin < 10) && ($moyenClasseMin!="")) { $moyenClasseMin="0".$moyenClasseMin; }
if (($moyenClasseGen < 10) && ($moyenClasseGen!="")) { $moyenClasseGen="0".$moyenClasseGen; }

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
$nbretardJ=0;
$nbretardNonJ=0;
$nbretard1=0;
$nbretard2=0;//
$nbabs=0;
$nbabsnj=0;
$nbsanctions=0;
$nbheureabs="0";

$nbretardJ=nombre_retardJustifie($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
$nbretardNonJ=nombre_retardNonJustifie($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate


// recherche le nombre d'absence
$tabnbabs=nombre_absJustifie2($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); 
// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier
$nbheureabs=0;
$nbabstotal=0;
for($o=0;$o<=count($tabnbabs);$o++) {
	if ($tabnbabs[$o][4] == "-1") { 
		$nbheureabs+=$tabnbabs[$o][7]; 
	}else{
		$nbabstotal+=$tabnbabs[$o][4];
	}
}
if ($nbabstotal != 0) {	$nbabs=$nbabstotal*2; }
if (trim($nbabs) == "") { $nbabs=0; }

$tabnbabs=nombre_absNonJustifie2($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); 
// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier
$nbheureabsNJ=0;
$nbabstotalNJ=0;
for($o=0;$o<=count($tabnbabs);$o++) {
	if ($tabnbabs[$o][4] == "-1") { 
		$nbheureabsNJ+=$tabnbabs[$o][7]; 
	}else{
		$nbabstotalNJ+=$tabnbabs[$o][4];
	}
}
if ($nbabstotalNJ != 0) {	$nbabsNJ=$nbabstotalNJ*2; }
if (trim($nbabsNJ) == "") { $nbabsNJ=0; }


// recherche le nombre de sanction

$nbsanctions=nombre_Sanc($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
$nbsanctions=count($nbsanctions);
//

$nbexclusions=nombre_Exclu($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
$nbexclusions=count($nbexclusions);


//---------------------------------//



$pdf->AddPage();
$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
$pdf->SetCreator("Pigier Nimes");
$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
$pdf->SetAuthor("Pigier Nimes - http://bulletins.ltidelafos.net"); 


// declaration variable
$coordonne0=strtoupper($nom_etablissement);
$coordonne1=$adresse;
$coordonne2=$postal." - ".ucwords($ville);
$coordonne3="Téléphone : ".$tel;
$coordonne4="";


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

$infoeleve="Nom de l'élève : $nomprenom     Date Naissance: <b>$datenaissance</b>";
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
$soustitre62="Coef.";
//
$soustitre7="Mini";
$soustitre8="Maxi";
$soustitre9="Moy.";


$idprofp=rechercheprofp($_POST["saisie_classe"]);
$profp=recherche_personne($idprofp);

///FIN MODIFICATION
$appreciation="Bilan des absences et retards : ";
$appreciation2="<br>Observations et appréciations de l'équipe pédagogique : ";

$duplicata=LANGBULL41;
$signature="La Directrice : $directeur";
// FIN variables

$xtitre=150;  // sans logo
$xcoor0=3;  // sans logo
$ycoor0=3;   // sans logo

$xlogo=5;
$ylogo=3;
$logowidth=30;
if (file_exists("./data/image_pers/logo_bulletin.jpg")) {
	$logo="./data/image_pers/logo_bulletin.jpg";
	$pdf->Image($logo,$xlogo,$ylogo,$logowidth);
}
 
//FIN
// Debut création PDF
// mise en place des coordonnées
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0,$ycoor0);
//$pdf->WriteHTML($coordonne0);
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


// cadre du haut
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,230,255);
//$pdf->SetFillColor(220);
$pdf->SetXY(60,11); // placement du cadre du Annee de l eleve
//$pdf->MultiCell(148,8,'',1,'L',1);
$pdf->RoundedRect(60,3, 148, 26, 3.5, 'DF');

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(60,12); // placement du cadre du nom de l eleve
$pdf->MultiCell(148,18,'',1,'L',1);
$pdf->SetXY(55+8,14); // placement du nom de l'eleve
$pdf->WriteHTML($infoeleve);
$pdf->SetXY(55+8,22); // placement du prenom de l'eleve
$pdf->WriteHTML($infoeleve2);
$pdf->SetX(77);
$pdf->SetFont('Arial','',12);
$pdf->WriteHTML($infoeleveclasse);


if ($photobulleleve == "oui") {
	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;
	$xphoto=195;
	$yphoto=13;
	$photowidth=10.8;
	$photoheight=16.3;
	if (!empty($photo)) {
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
	}
}
												
// Titre
$Pdate="Année Scolaire ".$anneeScolaire;
$periode=$titre." - ".$Pdate;
$pdf->SetXY(70,6);
$pdf->SetFont('Arial','B',11);
$pdf->SetTextColor(0,0,0);
$pdf->WriteHTML($periode);
// fin titre


// cadre des notes
// ---------------
// Barre des titres
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(230,230,255);
$pdf->SetXY(3,31.5); //  placement  cadre titre
$pdf->MultiCell(205,10,'',1,'C',1);
$pdf->SetXY(20,32); // placement contenu titre
$pdf->WriteHTML($titrenote1);
$pdf->SetX(70);
$pdf->WriteHTML($titrenote2);
$pdf->SetX(90+15);
$pdf->WriteHTML($titrenote3);
$pdf->SetX(127);
$pdf->WriteHTML($titrenote4);
// fin des titres

// possition des sous-titres
$pdf->SetFont('Arial','',7);
$pdf->SetXY(66,36);
$pdf->WriteHTML($soustitre4b);
$pdf->SetX(75);
$pdf->WriteHTML($soustitre5);
$pdf->SetX(85);
$pdf->WriteHTML($soustitre9);
$pdf->SetX(90+5);
$pdf->WriteHTML($soustitre6);
$pdf->SetX(97+10);
$pdf->WriteHTML($soustitre7);
$pdf->SetX(107+10);
$pdf->WriteHTML($soustitre8);
// fin des sous-titres

// Mise en place des matieres et nom de prof
$Xmat=3;//15
$Ymat=41;
$Xmatcont=4;//16
$Ymatcont=41;

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
$moyenEleveExam=0;
$noteMoyEleG=0;
$coefEleG=0;
$noteMoyEleExamG=0;
$coefEleExamG=0;
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
//ab le 310309 classement
		if ($idgroupe == "0") {
			$classement=Rangs($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
        		$classement=RangsGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
    		}	

//
	// mise en place des matieres
	$largeurMat=62;
	$hauteurMatiere=$hauteurmatiereP; // taille du cadre matiere
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

	if (($ii == 22) || ($Ymat >= 250)) {
		$pdf->AddPage();
		$Xmat=3;//15
		$Ymat=11;
		$Xmatcont=4;//16
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

	$pdf->SetFont('Arial','',9);
	$pdf->SetTextColor(000);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetXY($Xmat,$Ymat+0.5);
	$pdf->MultiCell($largeurMat,$hauteurMatiere-2,'',1,'L',0);
	$pdf->SetXY($Xmatcont-1.5,$Ymatcont+2);
	$comAff3=$Ymatcont;
	$pdf->SetFont('Arial','B',9);
	$pdf->MultiCell($largeurMat-2,3,"$matiere ($coeffaff)",0,'L',0);
//	$pdf->WriteHTML("<b>".trunchaine(ucfirst($matiere),30)." $coeffaff </b>");// .strtoupper......
	$pdf->SetFont('Arial','',9);
	$Ymat=$Ymat-2 + $hauteurMatiere;
	$Ymatcont=$Ymatcont-2 + $hauteurMatiere;

	// mise en place de la colonne moyenccmatiere
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcc+15,$Ycc+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',0);
	$Ycc=$Ycc-2 + $hauteurMatiere;

	// mise en place de la colonne Exam
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+15,$Ycoeff+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',0);
	$Ycoeff=$Ycoeff-2 + $hauteurMatiere;

	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve+0.5);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	//$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;

	// mise en place moyenne eleve * COEEF AB
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve+15,$Ymoyeleve+0.5);
	$pdf->SetFillColor(230,230,255);
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',1);
	$Ymoyeleve=$Ymoyeleve-2 + $hauteurMatiere;
//FIN AB
	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	$pdf->SetXY($Xmoyclasse+10,$Ymoyclasse+0.5);
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',0);
	$Ymoyclasse=$Ymoyclasse-2 + $hauteurMatiere;

	$pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)
	// mise en place moyenne classemin
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclassemin+10,$Ymoyclassemin+0.5);
	$pdf->MultiCell(12,$hauteurMatiere-2,'',1,'L',1);
	$Ymoyclassemin=$Ymoyclassemin-2 + $hauteurMatiere;
	// mise en place moyenne classemin
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclassemmaxi+11,$Ymoyclassemmaxi+0.5);
	$pdf->MultiCell(10,$hauteurMatiere-2,'',1,'L',1);
	$Ymoyclassemmaxi= $Ymoyclassemmaxi-2 + $hauteurMatiere;
	// mise en place du cadre commentaire
	$pdf->SetXY($Xnote+11,$Ynote+0.5);
	$pdf->MultiCell(82,$hauteurMatiere-2,'',1,'',1);
	$Ynote=$Ynote-2 + $hauteurMatiere;

	// mise en place des moyenCCmatiere
	$moyencc=moyenneCCMatierePigierNimes($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XccVal+15,$YcoeffVal-2.5);
	if (($moyencc < 10) && ($moyencc != "")) { $moyencc="0".$moyencc; }
	$pdf->WriteHTML($moyencc);
	//$YcoeffVal=$YcoeffVal + $hauteurMatiere; COMMENTAIRE ENLEVE LE 270308

	// mise en place des Exam
	//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
	$examaff=recupExamPigierNimes($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+15,$YcoeffVal-2.5);
	if (($examaff < 10) && ($examaff != "")) { $examaff="0".$examaff; }
	$pdf->WriteHTML($examaff);
	$YcoeffVal=$YcoeffVal-2 + $hauteurMatiere;

// mise en place des notes
	$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($XnotVal+13,$YnotVal-2.5);
	if (($noteaff < 10) && ($noteaff != "")) { $noteaff="0".$noteaff; }
	$pdf->WriteHTML("<B>".$noteaff."<B>");//AB
	$YnotVal=$YnotVal-2 + $hauteurMatiere;

// mise en place des MOY * COEF
//	$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
//	$pdf->SetFont('Arial','',8);
//	$pdf->SetXY($XnotVal+15,$YnotVal-2.5);
//	$pdf->WriteHTML("$coeffaff");//AB
	$totcoefab2=$totcoefab2 + $coeffaff;
	$totnotab2=$totnotab2 + $noteaff * $coeffaff;
	// mise en place des moyennes de classe
	if ($idgroupe == "0") {
           // idMatiere,datedebut,dateFin,idclasse
           $moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
        }else {
           $moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
        }
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($XmoyMatGVal+10,$YmoyMatGVal-2.5);
	if (($moyeMatGen < 10) && ($moyeMatGen != "")) { $moyeMatGen="0".$moyeMatGen; }
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
	$XmoyMatGenMinVal=$XmoyMatGVal + 21;//11
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal-2.5);
	if (($moyeMatGenMin < 10) && ($moyeMatGenMin != "")) { $moyeMatGenMin="0".$moyeMatGenMin; }
	$pdf->WriteHTML($moyeMatGenMin);
	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 32;//21
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal-2.5);
	if (($moyeMatGenMax < 10) && ($moyeMatGenMax != "")) { $moyeMatGenMax="0".$moyeMatGenMax; }
	$pdf->WriteHTML($moyeMatGenMax);
	$YmoyMatGVal=$YmoyMatGVal-2 + $hauteurMatiere;
	$Ycom=$YmoyMatGVal - 12;
	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
//	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Policy ; $confPolice[1] ->  Cadre

	$Xcom=$XmoyMatGenMaxVal + 10;//10

	$pdf->SetFont('Arial','I','6');
	$pdf->SetXY($Xcom-3,$comAff3+$hauteurmatiereP-5);
	$pdf->MultiCell(83,3,"$nomprof",'0','R',0);

	$pdf->SetFont('Arial','',$policecaractere);
	$pdf->SetXY($Xcom-1,$comAff3+1);
	$pdf->MultiCell(83,3,"$commentaireeleve",'0','L',0);
	//$nomprof


	$pdf->SetFont('Arial','',$policecaractere);


	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	//$coeffaffmoyab=$coeffaffmoyab+recupCoeff($ordre[$i][0],$idClasse,$ordre[$ii][2]); //AB----------
	//$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]);
	$pdf->SetFont('Arial','',$policecaractere);
	$YprofVal=$comAff3+3 ;
	$pdf->SetXY($XprofVal,$YprofVal+7);
	//$profAff=recherche_personne($profAff);
	//$pdf->WriteHTML(trunchaine($profAff,37));

	// pour le calcul de la moyenne general examen de l'eleve
	if ( $examaff != "" ) {
	        $noteMoyEleGTempo = $examaff * $coeffaff;
                $noteMoyEleExamG=$noteMoyEleExamG + $noteMoyEleGTempo;
                $coefEleExamG=$coefEleExamG + $coeffaff;
	}

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
		$coeffaff=1;
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}

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
$LargeurMG=$largeurMat+10;
$YmoyenneGeneralT=$YmoyenneGeneral + 2;
$XMoyGE= 3 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 5;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;


$pdf->SetFont('Arial','',8);
$pdf->SetXY(3,$YmoyenneGeneral+0.5);
$pdf->MultiCell($LargeurMG,9-2,'',1,'L',0);
$pdf->SetXY(5,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>MOYENNE GENERALE </B>");//.$totcoefab
$pdf->SetXY($XMoyGE+15,$YMoyGE+0.5);
//$pdf->SetFillColor(255,255,255);// pas de couleur de fond FM
//$pdf->MultiCell(10,9-2,'',1,'L',1);
$pdf->SetXY($XMoyCL+5,$YMoyGE+0.5);
$pdf->MultiCell(31,9-2,'',1,'L',0);

// fin du cadre moyenne generale

// affichage de la moyenne generale eleve
$XmoyElValue=$LargeurMG + 32   ;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$moyenEleveExam=moyGenEleve($noteMoyEleExamG,$coefEleExamG);

//$totmoyab=moyGenEleve($noteMoyEleGab,$totcoefab); //AB$noteMoyEleG
$totmoyab2=moyGenEleve($totnotab2,$totcoefab2); //AB
//ab le 310309
$moyenEleveaff=$moyenEleve;
if (($moyenEleveaff < 10) && ($moyenEleveaff!="")) { $moyenEleveaff="0".$moyenEleveaff; }
$moyenEleveaff=preg_replace('/,/','.',$moyenEleveaff);

$moyenEleveaffSansarrondi=$moyenEleveaff;
//

$pdf->SetFont('Arial','',8);
$pdf->SetXY($XmoyElValue-29,$YMoyGE+0.5);
$pdf->MultiCell(10,9-2,'',1,'L',0);
$pdf->SetXY($XmoyElValue-29,$YmoyElGenValue);
if (($moyenEleveExam < 10) && ($moyenEleveExam != "")) { $moyenEleveExam="0".$moyenEleveExam; }
$pdf->WriteHTML($moyenEleveExam);//

$pdf->SetXY($XmoyElValue-19,$YMoyGE+0.5);
$pdf->SetFillColor(230,230,255);
$pdf->MultiCell(10,9-2,'',1,'L',1);
$pdf->SetXY($XmoyElValue-19,$YmoyElGenValue);
if (($moyenEleve < 10) && ($moyenEleve != "")) { $moyenEleve="0".$moyenEleve; }
$pdf->WriteHTML("<B>".$moyenEleve."</B>");//
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
// fin affichage moy eleve
$pdf->SetFillColor(255,255,255);


//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
$pdf->SetFont('Arial','',7);
$pdf->SetXY($XmoyClasseGValue+15,$YmoyClasseGValue);
$pdf->WriteHTML($moyenClasseGen);
$pdf->SetXY($XmoyClasseMinValue+16,$YmoyClasseMinValue); 
$pdf->WriteHTML($moyenClasseMin); 
$pdf->SetXY($XmoyClasseMaxValue+17,$YmoyClasseMaxValue); 
$pdf->WriteHTML($moyenClasseMax); 
// fin de la calcul de min et max

// fin affichage

//RANG ELEVE AB 310309
// RANG
rsort($classementG);
$i=1;
$rangG="";
$rangGT=count($classementG);
foreach ($classementG as $key => $val) {	
//	print "$key => $val --- $moyenEleveaffSansarrondi ---  <br>";
	if ($val == $moyenEleveaffSansarrondi) { 
		$rangG = $key + 1; 
		break;
	}
}

//$pdf->SetXY(4,$Ycom-8);
/*
$pdf->SetXY($XmoyElValue+56,$YmoyElGenValue);
$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,255);
$pdf->WriteHTML("<B> RANG ELEVE : $rangG / $rangGT </B>");
$pdf->SetTextColor(0,0,0);
*/
//


// mise en place des commentaire_gen
$commentairegen=cherche_com_gen($idEleve,$idClasse,$_POST["saisie_trimestre"]);

// cadre appréciation
$Ycom=$YMoyGE + 9;//10
$EpaisCom=48;//30->40

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
$pdf->SetFont('Arial','B',10);
//$pdf->SetFillColor(220);/ pas de couleur de fond FM
$pdf->SetXY(10,$Ycom+1);
$pdf->MultiCell(190,$EpaisCom,'',1,'C',0);
$pdf->SetXY(10,$YcomP1+=1);
$pdf->WriteHTML("<B>".$appreciation."</B>");
$pdf->SetXY(10,$YcomPL0+4);
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML("Nbr d'absences justifiés : $nbabs demi-journée(s) - $nbheureabs  heure(s) /  Nbr de retards justifiés : $nbretardJ"); 
$pdf->SetXY(10,$YcomPL0+8);
$pdf->WriteHTML("Nbr d'absences non justifiés : $nbabsNJ demi-journée(s) - $nbheureabsNJ  heure(s) /  Nbr de retards non justifiés : $nbretardNonJ"); 

$nbabsNJ=0;
$nbheureabsNJ=0;
$nbretardNonJ=0;
$nbabs=0;
$nbheureabs=0;
$nbretardJ=0;

//FIN
//PARTIE OBSERVATION


$XX=120;
$pdf->SetFont('Arial','',10);
$pdf->SetXY($XX+3,$YcomP3+10);
$pdf->WriteHTML(": Félicitations");
$pdf->SetXY($XX,$YcomP3+11);
$pdf->MultiCell(3,3,"",1,'L',0);

$XX=120;
$YcomP3+=5;
$pdf->SetXY($XX+3,$YcomP3 + 10);
$pdf->WriteHTML(": Encouragements");
$pdf->SetXY($XX,$YcomP3 + 11);
$pdf->MultiCell(3,3,"",1,'L',0);

$YcomP3+=5;
$XX=120;
$pdf->SetXY($XX+3,$YcomP3 + 10);
$pdf->WriteHTML(": Passable");
$pdf->SetXY($XX,$YcomP3 + 11);
$pdf->MultiCell(3,3,"",1,'L',0);

$YcomP3+=5;
$XX=120;
$pdf->SetXY($XX+3,$YcomP3 + 10);
$pdf->WriteHTML(": Faible");
$pdf->SetXY($XX,$YcomP3 + 11);
$pdf->MultiCell(3,3,"",1,'L',0);

$YcomP3+=5;
$XX=120;
$pdf->SetXY($XX+3,$YcomP3 + 10);
$pdf->WriteHTML(": Avertissement");
$pdf->SetXY($XX,$YcomP3 + 11);
$pdf->MultiCell(3,3,"",1,'L',0);


//
$YcomP3+=10;
$pdf->SetXY(13,$YcomP4-5);
$pdf->WriteHTML("<B>".$appreciation2."</B>");
$pdf->SetXY(55,$YcomP4);
//$pdf->MultiCell(100,3,$commentairegen,'','','L',0);

$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(10,$YcomP4+5);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(100,4,$commentairedirection,'','','L',0); // commentaire de la direction (visa)


// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
$pdf->SetXY(10,$YcomP4+5+7);
$pdf->SetFont('Arial','',9);
//$pdf->MultiCell(100,3,$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


//duplicata et signature
$YduplicaSign=$Ycom -5 + $EpaisCom;//+1
$pdf->SetFont('Arial','',5);
$pdf->SetXY(16,$YduplicaSign +5);//+0
$pdf->WriteHTML("<I>".$duplicata."</I>");
$pdf->SetFont('Arial','',11);
$pdf->SetXY(130,$YduplicaSign+9);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',6);
//$pdf->SetXY(16,$YduplicaSign );//+15
//$pdf->WriteHTML($signature2);

// fin duplicata

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
@destruction_bulletin($fichier,$classe_nom,$_POST[saisie_trimestre],$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom,$_POST[saisie_trimestre],$dateDebut,$dateFin);
if($cr == 1){
		history_cmd($_SESSION[nom],"CREATION BULLETIN","Classe : $classe_nom");
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
