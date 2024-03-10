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
$valeur=visu_affectation_detail($_POST["saisie_classe"],$_POST["annee_scolaire"]);
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
      <?php print LANGBULL27?> : <?php print ucwords($textTrimestre)?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');


if (MODNAMUR0 == "oui") {
        $recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
        $persVieScolaire=$recupInfo[0][4];
        $coefBull=$recupInfo[0][1];
        $coefProf=$recupInfo[0][2];
        $coefVieScol=$recupInfo[0][3];
}


// recuperation des coordonnées
// de l etablissement
$data=visu_param();
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



// recherche des dates de debut et fin
//$dateRecup=recupDateTrim("trimestre1");
$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"],$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT1=dateForm($dateDebut);
$dateFinT1=dateForm($dateFin);

$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"],$anneeScolaire);
//$dateRecup=recupDateTrim("trimestre2");
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT2=dateForm($dateDebut);
$dateFinT2=dateForm($dateFin);

//$dateRecup=recupDateTrim("trimestre3");
$dateRecup=recupDateTrimByIdclasse("trimestre3",$_POST["saisie_classe"],$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT3=dateForm($dateDebut);
$dateFinT3=dateForm($dateFin);

$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere
// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF('L');  // declaration du constructeur
//$pdf=new PDF('P','mm','Legal');


include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve
$effectif=count($eleveT);

$hautclassant=0;
$profptab=rechercheprofpMulti($_POST["saisie_classe"]); // idprof,idclasse
for($j=0;$j<count($profptab);$j++) {
	$profp1=recherche_personne2($profptab[$j][0]);
	$profp1=trunchaine($profp1,14);
	$profp[$j]=$profp1;
}
$profp=implode(', ',$profp);



if (count($profptab) > 1) { $hautclassant=1.5; }

@nettoyage_repertoire('./data/pdf_bull/'.$classe_nom);
@rmdir('./data/pdf_bull/'.$classe_nom);

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
	$prenomEleve=ucwords($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];


	$policeT=12;

	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : $tel";
	$coordonne4="E-mail : $mail / $urlsite";

	$nomEleve=trim($nomEleve);
	$prenomEleve=trim($prenomEleve);
	$nomprenom="<b>$nomEleve $prenomEleve</b>";

	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=trim($classe_nom);

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

	// Debut création PDF
	// mise en place des coordonnées
		
	$noteMAXTotal="";
	$totalTrimestre1="";
	$totalTrimestre2="";
	$totalTrimestre3="";
	$totalCarnet1="";
	$totalCarnet2="";
	$totalCarnet3="";
	$totalTotal="";
	$moyenCarnet1="";
	$nbCarnet1="";
	$moyenCarnet2="";
	$nbCarnet2="";
	$moyenCarnet3="";
	$nbCarnet3="";
	$moyenT1="";
	$nbT1="";
	$moyenT2="";
	$nbT2="";
	$moyenT3="";
	$nbT3="";

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




	// cadre du haut
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(230,230,255);
	$pdf->RoundedRect(150, 3, 120, 25, 3.5, 'DF');

	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;

	$xphoto=150+3;
	$yphoto=5;
	$photowidth=10.8;
	$photoheight=16.3;
	$Xv1=150+3;
	$Xv11=121;
/*	if (!empty($photo)) {
		$photo=$photoeleve;
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
		$Xv1=140+18+7;
		$Xv11=110;
	}
*/
	$bis=verifGroupBis($idEleve);
	if ($bis == 1) {
		$bis=" - BIS";
	}else{
		$bis="";
	}


	$pdf->SetXY($Xv1,5); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	$pdf->SetXY($Xv1+52,10);
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetXY($Xv1+66,10);
	$pdf->WriteHTML($infoeleveclasse.$bis);
	$pdf->SetXY($Xv1+52,15);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell('53','3.5',"Titulaire : $profp",0,'L',0);
	$pdf->SetFont('Arial','',10);



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
		$YN1=10;
		$pdf->SetXY($Xv1,$YN1); 
		$pdf->SetFont('Arial','',8);
		$pdf->WriteHTML("N°: $numero_eleve ");
		$pdf->SetXY($Xv1,$YN1+=4);
		$pdf->WriteHTML("Né(e) le $datenaissance");
		$pdf->SetXY($Xv1,$YN1+=4); 
		$pdf->WriteHTML("Année scolaire : $anneeScolaire ");
		$pdf->SetXY($Xv1+52,20+$hautclassant);
		$class_ant=trunchaine($class_ant,40);
		$pdf->WriteHTML("Classe ant.: $class_ant ");
	}

	$xcoor0=1;
	$ycoor0=25;
	
	$pdf->SetXY($xcoor0+1,$ycoor0+1);

	$HT1=25;
	$LR0=70;
	$LR1=10;
	$LRT=$LR0+(4*$LR1);
	$HTT=7;

	$pdf->SetFont($police,'',$policeT);
	$pdf->SetXY($xcoor0+1,$ycoor0+7);
	$pdf->SetFillColor(230,230,255);
	$pdf->MultiCell($LRT,$HTT,'Evaluation Périodiques',1,'C',1);
	$pdf->SetFillColor(220);
	$pdf->SetXY($xcoor0+=$LRT+1,$ycoor0+7);
        $pdf->MultiCell(2,$HTT,'',1,'C',1);
	$pdf->SetFillColor(255);
	$pdf->SetFillColor(230,230,255);
	$pdf->SetXY($xcoor0+=2,$ycoor0+7);
	$pdf->MultiCell(181,$HTT,'Evaluation Trimestrielle',1,'C',1);
	$pdf->SetFillColor(255);

	$xcoor0=1;
	$ycoor0+=$HTT;
	$pdf->SetXY($xcoor0+1,$ycoor0+7);
	$pdf->MultiCell($LR0,$HT1,'Matières',1,'C',0);

	$pdf->SetFont($police,'',$policeT-2);

	$pdf->SetXY($xcoor0+=$LR0+1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1=$xcoor0+6;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"MAXIMUM","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=11;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"1er Carnet","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=10;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"2ieme Carnet","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=8;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"3ieme Carnet","U");


	$pdf->SetFillColor(220);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
        $pdf->MultiCell(2,$HT1,'',1,'C',1);
	$pdf->SetFillColor(255);


	$LARGCOM=47;

	$pdf->SetXY($xcoor0+=2,$ycoor0+7);
        $pdf->MultiCell($LR1,$HT1,'',1,'C',0);
        $xsujet1+=8+2+3;
        $ysujet1=$ycoor0+13+18;
        $pdf->TextWithDirection($xsujet1,$ysujet1,"Trimestre 1","U");
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
        $pdf->MultiCell($LARGCOM,$HT1,"Appréciation, conseils",1,'C',0);

        $pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0+7);
        $pdf->MultiCell($LR1,$HT1,'',1,'C',0);
        $xsujet1+=8+$LARGCOM+2;
        $ysujet1=$ycoor0+13+18;
        $pdf->TextWithDirection($xsujet1,$ysujet1,"Trimestre 2","U");
        $pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
        $pdf->MultiCell($LARGCOM,$HT1,"Appréciation, conseils",1,'C',0);


        $pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0+7);
        $pdf->MultiCell($LR1,$HT1,'',1,'C',0);
        $xsujet1+=8+$LARGCOM+2;
        $ysujet1=$ycoor0+13+18;
        $pdf->TextWithDirection($xsujet1,$ysujet1,"Trimestre 3","U");
        $pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
        $pdf->MultiCell($LARGCOM,$HT1,"Appréciation, conseils",1,'C',0);

	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0+7);
        $pdf->MultiCell($LR1,$HT1,'',1,'C',0);
        $xsujet1+=8+$LARGCOM+2;
        $ysujet1=$ycoor0+13+18;
        $pdf->TextWithDirection($xsujet1,$ysujet1,utf8_decode("Moy. Générale"),"U");
	$LR2=15;
	$pdf->SetFont($police,'',$policeT-2);
	$xcoor0=1;
	$ycoor0=$ycoor0+$HT1+7;
	$LR3=70;
	$LR4=$LR0/3;
	$LR5=5;
	// cadre menu matiere
	// ------------------
	$largeurMat=$LR3;
	$hauteurMaT=$hauteurMatiere=6;
	$Xmat=2;
	$Ymat=$ycoor0;
	
	// Mise en place des matieres et nom de prof
	for($i=0;$i<count($ordre);$i++) {
		$totalAnnee="";
		$totalMax="";
		$pdf->SetTextColor(0,0,0);   // noir
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);
		$Xmatcont=$Xmat+1;
		$Ymatcont=$Ymat;
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$pdf->SetFont($police,'',$policeT-2);
		$profAff=ucwords($nomprof);
		$pdf->WriteHTML('<b>'.trunchaine($matiere,35)."</b> <i>$profAff</i>");
		$pdf->SetFont($police,'',$policeT-3);

		// MAXIMUM
		$pdf->SetXY($Xmat+$largeurMat,$Ymat);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$noteMAX=$coeffaff*100;
		$pdf->MultiCell($LR1,$hauteurMatiere,"$noteMAX",1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat,$Ymat+3);
		$noteMAXTotal+=$noteMAX;
		// 1 carnet
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof,'jtc');
		}else{
			$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idgroupe,$idprof,'jtc');
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR1,$Ymat);
		if ((($noteaff*100)/20) < 60) $pdf->SetFont($police,'BI',$policeT-3);
		$pdf->MultiCell($LR1,$hauteurMatiere,coefcent($noteaff,$coeffaff,"oui"),1,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		if ($noteaff != "") {
			$totalCarnet1+=coefcent($noteaff,$coeffaff,"oui");
			$moyenCarnet1+=coefcent($noteaff,$coeffaff,"oui");
			$nbCarnet1++;	
			$totalMaxCarnet1+=$noteMAX;
		}
		// 2 ieme periode
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof,'jtc');
		}else{
			$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idgroupe,$idprof,'jtc');
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1,$Ymat);
		if ((($noteaff*100)/20) < 60) $pdf->SetFont($police,'BI',$policeT-3);
		$pdf->MultiCell($LR1,$hauteurMatiere,coefcent($noteaff,$coeffaff,"oui"),1,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		if ($noteaff != "") {
			$totalCarnet2+=coefcent($noteaff,$coeffaff,"oui");
			$moyenCarnet2+=coefcent($noteaff,$coeffaff,"oui");
			$nbCarnet2++;	
			$totalMaxCarnet2+=$noteMAX;
		}
	
		$noteaff="";
		if ($idgroupe == "0") {
                        $noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebutT3,$dateFinT3,$idprof,'jtc');
                }else{
                        $noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebutT3,$dateFinT3,$idgroupe,$idprof,'jtc');
                }
		
		// 3ieme periode
		$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat);
		if ((($noteaff*100)/20) < 60) $pdf->SetFont($police,'BI',$policeT-3);
		$pdf->MultiCell($LR1,$hauteurMatiere,coefcent($noteaff,$coeffaff,"oui"),1,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		if ($noteaff != "") {
			$totalCarnet3+=coefcent($noteaff,$coeffaff,"oui");
			$moyenCarnet3+=coefcent($noteaff,$coeffaff,"oui");
			$nbCarnet3++;
			$totalMaxCarnet3+=$noteMAX;
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat+2);
		$pdf->SetFillColor(220);
		$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1,$Ymat);
	        $pdf->MultiCell(2,$hauteurMatiere,'',1,'L',1);
		$pdf->SetFillColor(255);
		// T1
		$noteaff="";
		$nbT=0;
		$totalT="";
                if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idgroupe,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof);
		}
		if ($noteaff != "") {
			$totalT+=$noteaff;
			$nbT++;
		}
		$xcoor0=$Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1;
		$ycoor0=$Ymat;
	        $pdf->SetXY($xcoor0+=2,$ycoor0);
		if ((($noteaff*100)/20) < 60) $pdf->SetFont($police,'BI',$policeT-3);
	        $pdf->MultiCell($LR1,$hauteurMatiere,coefcent($noteaff,$coeffaff,"oui"),1,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		if ($noteaff != "") {
			$totalTrimestre1+=coefcent($noteaff,$coeffaff,"oui");
			$moyenT1+=coefcent($noteaff,$coeffaff,"oui");
			$nbT1++;
			$totalMaxT1+=$noteMAX;
		}
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,"trimestre1",$idprof,$idgroupe);
		$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
		$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'L',0);
		$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
		$pdf->SetFont($police,'',$policeT-5);
	        $pdf->MultiCell($LARGCOM,3,"$commentaireeleve",0,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		unset($commentaireeleve);
		// T2
		$noteaff="";
                if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idgroupe,$idprof);
                }else{
			$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof);
                }
		if ($noteaff != "") {
			$totalT+=$noteaff;
			$nbT++;
			$moyenT1+=
			$nbT1++;
		}
		if ($noteaff != "") {
			$moyenT2+=coefcent($noteaff,$coeffaff,"oui");
			$nbT2++;
			$totalTrimestre2+=coefcent($noteaff,$coeffaff,"oui");
			$totalMaxT2+=$noteMAX;
		}
	        $pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
		if ((($noteaff*100)/20) < 60) $pdf->SetFont($police,'BI',$policeT-3);
	        $pdf->MultiCell($LR1,$hauteurMatiere,coefcent($noteaff,$coeffaff,"oui"),1,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,"trimestre2",$idprof,$idgroupe);
	        $pdf->SetXY($xcoor0+=$LR1,$ycoor0);
		$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'L',0);
		$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
		$pdf->SetFont($police,'',$policeT-5);
	        $pdf->MultiCell($LARGCOM,3,"$commentaireeleve",0,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		unset($commentaireeleve);
		// T3
		$noteaff="";
                if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT3,$dateFinT3,$idgroupe,$idprof);
                }else{
			$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT3,$dateFinT3,$idprof);
                }
		if ($noteaff != "") {
			$totalT+=$noteaff;
			$nbT++;
		}
		if ($noteaff != "") {
			$moyenT3+=coefcent($noteaff,$coeffaff,"oui");
			$nbT3++;
			$totalTrimestre3+=coefcent($noteaff,$coeffaff,"oui");
			$totalMaxT3+=$noteMAX;
		}
	        $pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
		if ((($noteaff*100)/20) < 60) $pdf->SetFont($police,'BI',$policeT-3);
		$pdf->MultiCell($LR1,$hauteurMatiere,coefcent($noteaff,$coeffaff,"oui"),1,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,"trimestre3",$idprof,$idgroupe);
	        $pdf->SetXY($xcoor0+=$LR1,$ycoor0);
		$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'L',0);
		$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
		$pdf->SetFont($police,'',$policeT-5);
	        $pdf->MultiCell($LARGCOM,3,"$commentaireeleve",0,'L',0);
		$pdf->SetFont($police,'',$policeT-3);
		unset($commentaireeleve);
		// TOTAL
		if ($nbT > 0) {
			$totalT=$totalT/$nbT;
			$totaTaff=coefcent($totalT,$coeffaff,"oui");
			$totalTotal+=$totaTaff;
		}
	        $pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	        $pdf->MultiCell($LR1,$hauteurMatiere,"$totaTaff",1,'L',0);
		unset($totaTaff);
		// fin de la ligne
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;   
	}

	$moyenClassMatT1=calculMoyenClasseBlanc($idClasse,$eleveT,$dateDebutT1,$dateFinT1,'','jtc');
	$moyenClassMatT2=calculMoyenClasseBlanc($idClasse,$eleveT,$dateDebutT2,$dateFinT2,'','jtc');
	$moyenClassMatT3=calculMoyenClasseBlanc($idClasse,$eleveT,$dateDebutT3,$dateFinT3,'','jtc');
	$moyenClassMatT1=coefcent($moyenClassMatT1,1,"oui");
	$moyenClassMatT2=coefcent($moyenClassMatT2,1,"oui");
	$moyenClassMatT3=coefcent($moyenClassMatT3,1,"oui");
        $moyClasseT11=calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebutT1,$dateFinT1,'');
        $moyClasseT12=calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebutT2,$dateFinT2,'');
        $moyClasseT13=calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebutT3,$dateFinT3,'');
        $moyClasseT11=coefcent($moyClasseT11,1,"oui");
        $moyClasseT12=coefcent($moyClasseT12,1,"oui");
        $moyClasseT13=coefcent($moyClasseT13,1,"oui");

        $moyenVieScolaireC1=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,'trimestre1','1');
        $moyenVieScolaireC2=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,'trimestre2','1');
        $moyenVieScolaireC3=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,'trimestre3','1');
	$moyenVieScolaireT1=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,'trimestre1');
	$moyenVieScolaireT2=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,'trimestre2');
	$moyenVieScolaireT3=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,'trimestre3');

        $moyenVieScolaireC1=coefcent($moyenVieScolaireC1,$coefBull,"oui");
        $moyenVieScolaireC2=coefcent($moyenVieScolaireC2,$coefBull,"oui");
        $moyenVieScolaireC3=coefcent($moyenVieScolaireC3,$coefBull,"oui");
	$moyenVieScolaireT1=coefcent($moyenVieScolaireT1,$coefBull,"oui");
	$moyenVieScolaireT2=coefcent($moyenVieScolaireT2,$coefBull,"oui");
	$moyenVieScolaireT3=coefcent($moyenVieScolaireT3,$coefBull,"oui");

	if ($moyenVieScolaireT1 > 0) { 
		$moyenVieScolaireTotal+=$moyenVieScolaireT1; 
		$nbTotalVieScolaire++;
	}
	if ($moyenVieScolaireT2 > 0) {
		$moyenVieScolaireTotal+=$moyenVieScolaireT2;
		$nbTotalVieScolaire++;
	}
	if ($moyenVieScolaireT3 > 0) {
		$moyenVieScolaireTotal+=$moyenVieScolaireT3;
		$nbTotalVieScolaire++;
	}

	if ($nbTotalVieScolaire > 0) {
		$moyenVieScolaireTotal=$moyenVieScolaireTotal/$nbTotalVieScolaire;
		$moyenVieScolaireTotal=number_format($moyenVieScolaireTotal,1,'.','');
	}
	$nbTotalVieScolaire="";
	
	$com_visa_scolaireT1=recherche_com_scolaire($idEleve,'trimestre1');
	$com_visa_scolaireT2=recherche_com_scolaire($idEleve,'trimestre2');
	$com_visa_scolaireT1=recherche_com_scolaire($idEleve,'trimestre3');

	// fin de la mise en place des matiere

	// Banniere du bas.
	// ---------------
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->SetFont($police,'',$policeT-2);
	$profAff=ucwords($nomprof);
	$pdf->WriteHTML("Total ");
	$pdf->SetFont($police,'',$policeT-3);
	// MAXIMUM
	$pdf->SetXY($Xmat+$largeurMat,$Ymat);
	//$pdf->MultiCell($LR1,$hauteurMatiere,"$noteMAXTotal",1,'L',0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"",1,'L',0);
	// 1 carnet
	$pdf->SetXY($Xmat+$largeurMat+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalCarnet1",1,'L',0);
	// 2 ieme periode
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalCarnet2",1,'L',0);
	// 3ieme periode
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalCarnet3",1,'L',0);
	// Separateur
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat+2);
	$pdf->SetFillColor(220);
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1,$Ymat);
	$pdf->MultiCell(2,$hauteurMatiere,'',1,'C',1);
	$pdf->SetFillColor(255);
	// T1
	$xcoor0=$Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1;
	$ycoor0=$Ymat;
	$pdf->SetXY($xcoor0+=2,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalTrimestre1",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// T2
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalTrimestre2",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// T3
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalTrimestre3",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// TOTAL
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$totalTotal",1,'L',0);
	// fin de la ligne
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;   
	// -----------------------------------------------------------------------
	// ---------------
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->SetFont($police,'',$policeT-2);
	$profAff=ucwords($nomprof);
	$pdf->WriteHTML("Moyenne ");
	$pdf->SetFont($police,'',$policeT-3);
	// MAXIMUM
	$pdf->SetXY($Xmat+$largeurMat,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"100",1,'L',0);
	// 1 carnet
	$pdf->SetXY($Xmat+$largeurMat+$LR1,$Ymat);
	if ($totalCarnet1 > 0 ) { 
		$moyenCarnet1=($totalCarnet1*100)/$totalMaxCarnet1;
		$moyenCarnet1=number_format($moyenCarnet1,'1','.','');
	}
	if ($moyenCarnet1 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenCarnet1",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	// 2 ieme periode
	if ($totalCarnet2 > 0 ) {
		$moyenCarnet2=($totalCarnet2*100)/$totalMaxCarnet2;
		$moyenCarnet2=number_format($moyenCarnet2,'1','.','');
	}
	if ($moyenCarnet2 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenCarnet2",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	// 3ieme periode
	if ($totalCarnet3 > 0 ) {
		$moyenCarnet3=($totalCarnet3*100)/$totalMaxCarnet3;
		$moyenCarnet3=number_format($moyenCarnet3,'1','.','');
	}
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat);
	if ($moyenCarnet3 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenCarnet3",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	// Separateur
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat+2);
	$pdf->SetFillColor(220);
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1,$Ymat);
	$pdf->MultiCell(2,$hauteurMatiere,'',1,'C',1);
	$pdf->SetFillColor(255);
	// T1
	$xcoor0=$Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1;
	$ycoor0=$Ymat;
	$pdf->SetXY($xcoor0+=2,$ycoor0);
	if ($nbT1 > 0 ) {
		$moyenT1=($totalTrimestre1*100)/$totalMaxT1;
		$moyenT1=number_format($moyenT1,'1','.','');
	}
	if ($moyenT1 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenT1",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// T2
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	if ($nbT2 > 0 ) {
		$moyenT2=($totalTrimestre2*100)/$totalMaxT2;
		$moyenT2=number_format($moyenT2,'1','.','');
	}
	if ($moyenT2 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenT2",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// T3
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	if ($nbT3 > 0 ) {
		$moyenT3=($totalTrimestre3*100)/$totalMaxT3;
		$moyenT3=number_format($moyenT3,'1','.','');
	}
	if ($moyenT3 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenT3",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// TOTAL

	if (($moyenT1 >= 0) && ($moyenT1 != "" )) {
                $moyT111+=$moyenT1;
                $nbT111++;
        }
        if (($moyenT2 >= 0) && ($moyenT2 != "" )) {
                $moyT111+=$moyenT2;
                $nbT111++;
        }
        if (($moyenT3 >= 0) && ($moyenT3 != "" )) {
                $moyT111+=$moyenT3;
                $nbT111++;
        }

         if ($nbT111 > 0) {
                $moyT111Total=$moyT111/$nbT111;
                $moyT111Total=number_format($moyT111Total,1,'.','');
        }

	unset($nbT111);
	unset($moyT111);	

	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyT111Total",1,'C',0);
	// fin de la ligne
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;   
	// -----------------------------------------------------------------------
	// ---------------
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->SetFont($police,'',$policeT-2);
	$profAff=ucwords($nomprof);
	$pdf->WriteHTML("Moyenne de classe ");
	$pdf->SetFont($police,'',$policeT-3);
	// MAXIMUM
	$pdf->SetXY($Xmat+$largeurMat,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"100",1,'L',0);
	// 1 carnet
	$pdf->SetXY($Xmat+$largeurMat+$LR1,$Ymat);
	if ($moyenClassMatT1 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenClassMatT1",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	// 2 ieme periode
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1,$Ymat);
	if ($moyenClassMatT2 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenClassMatT2",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	// 3ieme periode
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat);
	if ($moyenClassMatT3 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenClassMatT3",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	// Separateur
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat+2);
	$pdf->SetFillColor(220);
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1,$Ymat);
	$pdf->MultiCell(2,$hauteurMatiere,"",1,'C',1);
	$pdf->SetFillColor(255);
	// T1
	$xcoor0=$Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1;
	$ycoor0=$Ymat;
	$pdf->SetXY($xcoor0+=2,$ycoor0);
	if ($moyClasseT11 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyClasseT11",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// T2
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	if ($moyClasseT12 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyClasseT12",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
	// T3
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	if ($moyClasseT13 < 60) $pdf->SetFont($police,'BI',$policeT-3);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyClasseT13",1,'L',0);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);

	if (($moyClasseT11 >= 0) && ($moyClasseT11 != "" )) {
		$moyClassT111+=$moyClasseT11;
		$nbMoyClassT111++;
	}
	if (($moyClasseT12 >= 0) && ($moyClasseT12 != "" )) {
		$moyClassT111+=$moyClasseT12;
		$nbMoyClassT111++;
	}
	if (($moyClasseT13 >= 0) && ($moyClasseT13 != "" )) {
		$moyClassT111+=$moyClasseT13;
		$nbMoyClassT111++;
	}

	 if ($nbMoyClassT111 > 0) {
                $moyClassT111Total=$moyClassT111/$nbMoyClassT111;
                $moyClassT111Total=number_format($moyClassT111Total,1,'.','');
        }

	unset($nbMoyClassT111);	
	unset($moyClassT111);	
	
	// TOTAL
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyClassT111Total",1,'C',0);
	// fin de la ligne
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;   
	// -----------------------------------------------------------------------
	// ---------------
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->SetFont($police,'',$policeT-2);
	$profAff=ucwords($nomprof);
	$pdf->WriteHTML("Discipline ");
	$pdf->SetFont($police,'',$policeT-3);
	// MAXIMUM
	$pdf->SetXY($Xmat+$largeurMat,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,$coefBull*100,1,'L',0);
	// 1 carnet
	$pdf->SetXY($Xmat+$largeurMat+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireC1",1,'C',0);
	// 2 ieme periode
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireC2",1,'C',0);
	// 3ieme periode
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireC3",1,'C',0);
	// Separateur
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1,$Ymat+2);
	$pdf->SetFillColor(220);
	$pdf->SetXY($Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1,$Ymat);
	$pdf->MultiCell(2,$hauteurMatiere,'',1,'C',1);
	$pdf->SetFillColor(255);
	// T1
	$xcoor0=$Xmat+$largeurMat+$LR1+$LR1+$LR1+$LR1;
	$ycoor0=$Ymat;
	$pdf->SetXY($xcoor0+=2,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireT1",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
        $pdf->SetFont($police,'',$policeT-5);
        $pdf->MultiCell($LARGCOM,3,"$com_visa_scolaireT1",0,'L',0);
        $pdf->SetFont($police,'',$policeT-3);
        unset($com_visa_scolaireT1);
	// T2
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireT2",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
        $pdf->SetFont($police,'',$policeT-5);
        $pdf->MultiCell($LARGCOM,3,"$com_visa_scolaireT2",0,'L',0);
        $pdf->SetFont($police,'',$policeT-2);
        unset($com_visa_scolaireT2);
	// T3
	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireT3",1,'L',0);
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$pdf->MultiCell($LARGCOM,$hauteurMatiere,"",1,'C',0);
	$pdf->SetXY($xcoor0+0.5,$ycoor0+1);
        $pdf->SetFont($police,'',$policeT-5);
        $pdf->MultiCell($LARGCOM,3,"$com_visa_scolaireT3",0,'L',0);
        $pdf->SetFont($police,'',$policeT-2);
        unset($com_visa_scolaireT3);
	// TOTAL

	 if (($moyenVieScolaireT1 >= 0) && ($moyenVieScolaireT1 != "" )) {
                $moyT111+=$moyenVieScolaireT1;
                $nbT111++;
        }
        if (($moyenVieScolaireT2 >= 0) && ($moyenVieScolaireT2 != "" )) {
                $moyT111+=$moyenVieScolaireT2;
                $nbT111++;
        }
        if (($moyenVieScolaireT3 >= 0) && ($moyenVieScolaireT3 != "" )) {
                $moyT111+=$moyenVieScolaireT3;
                $nbT111++;
        }

         if ($nbT111 > 0) {
                $moyenVieScolaireTotal=$moyT111/$nbT111;
                $moyenVieScolaireTotal=number_format($moyenVieScolaireTotal,1,'.','');
        }

        unset($nbT111);
	unset($moyT111);



	$pdf->SetXY($xcoor0+=$LARGCOM,$ycoor0);
	$pdf->MultiCell($LR1,$hauteurMatiere,"$moyenVieScolaireTotal",1,'L',0);
	// fin de la ligne
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;   
	// -----------------------------------------------------------------------
	
	unset($noteMAXTotal);
	unset($totalCarnet1);
	unset($totalCarnet2);
	unset($totalCarnet3);
	unset($totalTrimestre1);
	unset($totalTrimestre2);
	unset($totalTrimestre3);
	unset($totalTotal);
	unset($totalMaxT1);
	unset($totalMaxT2);
	unset($totalMaxT3);
	unset($totalMaxCarnet1);
	unset($totalMaxCarnet2);
	unset($totalMaxCarnet3);


	// moyenne general
	$xcoor0=2;
	$ycoor0=$Ymat+5;

	$pdf->SetXY($xcoor0,$ycoor0);
        $pdf->MultiCell("43","5","Décision de fin d'année : ",0,'L',0);
	$pdf->SetXY($xcoor0+=40,$ycoor0);
	$pdf->MultiCell("43","5","Promu(e) : ",0,'R',0);
	$pdf->SetXY($xcoor0,$ycoor0+6);
	$pdf->MultiCell("43","5","Classe à reprendre : ",0,'R',0);
	$pdf->SetXY($xcoor0,$ycoor0+12);
	$pdf->MultiCell("43","5","Orientation Ailleurs : ",0,'R',0);
	$jtc_promu=0;
	$jtc_reprendre=0;
	$jtc_orientation=0;
	$dataJTC=recherchejtc($idEleve,'jtc',"annuel"); // jtc_promu,jtc_reprendre,jtc_orientation
	$jtc_promu=$dataJTC[0][0];
	$jtc_reprendre=$dataJTC[0][1];
	$jtc_orientation=$dataJTC[0][2];

	$pdf->SetFillColor(0);
	$pdf->SetXY($xcoor0+=44,$ycoor0);
	$pdf->MultiCell("5","5",'',1,'R',$jtc_promu);
	$pdf->SetXY($xcoor0,$ycoor0+6);
	$pdf->MultiCell("5","5",'',1,'R',$jtc_reprendre);
	$pdf->SetXY($xcoor0,$ycoor0+12);
	$pdf->MultiCell("5","5",'',1,'R',$jtc_orientation);
	$pdf->SetFillColor(255);
	$commentaire_direction=recherche_com($idEleve,"annuel","jtc");
	if ($commentaire_direction != "") {
		$pdf->SetXY($xcoor0+=10,$ycoor0);
		$pdf->MultiCell("240","5","La direction : ",0,'L',0);
		$pdf->SetXY($xcoor0+23,$ycoor0+1);
		$pdf->MultiCell("170","3.5","$commentaire_direction",0,'L',0);
	}else{
		$pdf->SetXY($xcoor0+=10,$ycoor0);
		$pdf->MultiCell("240","5","La direction : ______________________________________________________________________________ ",0,'L',0);
		$pdf->SetXY($xcoor0+22,$ycoor0+5);
		$pdf->MultiCell("240","5"," ______________________________________________________________________________ ",0,'L',0);
		$pdf->SetXY($xcoor0+22,$ycoor0+10);
		$pdf->MultiCell("240","5"," ______________________________________________________________________________ ",0,'L',0);
	}


	$Ymat+=$hauteurMatiere;	
	$Xmat=2;


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
	$cmd="gs -q -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=./data/pdf_bull/$classe_nom/liste_complete.pdf -dBATCH $listing";
	$null=system("$cmd",$retval);
}
include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/'.$classe_nom.'.zip';
$bttexte="Récupérer le fichier ZIP des bulletins";
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
}
Pgclose();
?>

<?php
}else {
?>
<br />
<center>
<font class="T2">
<?php print LANGMESS14?> <br>
<br><br>
<?php print LANGMESS15?><br>
<br>
<?php print LANGMESS16?><br>
</font>
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
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();
?>
