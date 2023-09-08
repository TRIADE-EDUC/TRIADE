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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Relévé des notes d'examen" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
validerequete("menuadmin");
$cnx=cnx();

$idClasse=$_POST["saisie_classe"];
$nomClasse=chercheClasse_nom($idClasse);

$tri=$_POST["saisie_trimestre"];
$exam=$_POST["saisie_examen"];
$afficheNomEleve=$_POST["saisie_avec_nom"];
$afficheMatriculeEleve=$_POST["saisie_avec_matricule"];

config_param_ajout($afficheNomEleve,"affNomEleExam");
config_param_ajout($afficheMatriculeEleve,"affMatriEleExam");


$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
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
}
// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

config_param_ajout($_POST["hauteur"],"hauteuremarg");

$pdf=new PDF();  // declaration du constructeur

$hauteur=$_POST["hauteur"];


$textTrimestre=strtoupper($textTrimestre);

for($i=0;$i<count($ordre);$i++) {
	$matiere=ucwords(chercheMatiereNom($ordre[$i][0]));
	$idMatiere=$ordre[$i][0];
	$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
	$nomprof=ucwords(strtolower(recherche_personne2($ordre[$i][1])));

	$pdf->AddPage();
	$pdf->SetTitle("Releve des notes -");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Releve des notes"); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	$X=0;
	$Y=5;
	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(210,6,"$exam - $nomClasse",0,'C',0);
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($X+=5,$Y+=15);
	$pdf->SetFillColor(230,230,255);
	$pdf->RoundedRect($X, $Y, 140, 17, 2.5, 'DF');
	$pdf->SetFillColor(255);
	$pdf->SetXY($X+2,$Y+=2);
	$pdf->MultiCell(130,3,"Période : $textTrimestre ",0,'L',0);
	$pdf->SetXY($X+2,$Y+=5);
	$pdf->MultiCell(130,3,"Intitulé du cours : $matiere ",0,'L',0);
	$pdf->SetXY($X+2,$Y+=5);
	$pdf->MultiCell(130,3,"Enseignant : $nomprof ",0,'L',0);

	$Y+=10;
	$X+=5;
	$pdf->SetFont('Arial','',6);
	if ($afficheNomEleve == "oui") {
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(52,$hauteur,"Nom et prénom de l'étudiant(e)",1,'L',1);
		$X+=52;
	}
	if ($afficheMatriculeEleve == "oui") {
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(52,$hauteur,"Numéro de Matricule",1,'L',1);
		$X+=38;
	}
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(100,$hauteur,"Note(s)",1,'C',1);

	$Y+=$hauteur;

	$pdf->SetFillColor(255);

	$eleveT=recupEleve($idClasse); // nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone, numero_eleve
	for($j=0;$j<count($eleveT);$j++) {  
		// variable eleve
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);
	
		$nomEleve=strtoupper($eleveT[$j][0]);
		$prenomEleve=ucfirst($eleveT[$j][1]);
		$numeromatricule=$eleveT[$j][11];
		$idEleve=$eleveT[$j][4];
		$X=10;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',8);
		if ($afficheNomEleve == "oui") {
			$pdf->MultiCell(52,$hauteur,"$nomEleve $prenomEleve",1,'L',0);
			$pdf->SetXY($X+=52,$Y);
			
		}
		if ($afficheMatriculeEleve == "oui") {
			$pdf->MultiCell(38,$hauteur,"N° $numeromatricule",1,'L',0);
			$pdf->SetXY($X+=38,$Y);
		}
		$listingNote=listingNoteExam($idEleve,$idMatiere,$dateDebut,$dateFin,$exam,$idprof);
		$pdf->MultiCell(100,$hauteur,"",1,'C',0);
		$pdf->SetXY($X,$Y);
		for ($a=0;$a<count($listingNote);$a++) {
			$note=$listingNote[$a][0];
			if($note == '-1'){
				$note = 'ABS';
			} elseif ($note == '-2') {
				$note = 'DISP';
			} elseif ($note == '-3') {
				$note = '???';
			} elseif ($note == '-4') {
				$note = 'DNN';
			} elseif ($note == '-5') {
				$note = 'DNR';
			} elseif ($note == '-6') {
				$note = 'VAL';
			}else{
				$note=$listingNote[$a][0];
			}
			$pdf->MultiCell(10,$hauteur,"$note",0,'C',0);	
			$pdf->SetXY($X+=10,$Y);
		}

		
		$Y+=$hauteur;
		if ($Y >= 260) { $pdf->AddPage(); $Y=10; }
	}	
}	


$fichier="./data/pdf_bull/examen_".$idClasse.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;
?>
<br><br>
<center>
<table><tr><td><input type=button onclick="open('visu_pdf_scolaire.php?id=<?php print $fichier?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td><td><script language=JavaScript>buttonMagicRetour2("gestion_examen_listing.php","_self","<?php print "Retour" ?>");</script></td></tr></table>
</center>

<br /><br />
     </td></tr></table>
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
<?php PgClose(); ?>
