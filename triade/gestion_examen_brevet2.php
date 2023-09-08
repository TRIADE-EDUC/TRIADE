<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Fiche Scolaire Brevet Collège" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<?php 
$idclasse=$_POST["idclasse"];
$type_pdf=$_POST["type_pdf"];
$data=chercheClasse($idclasse);
$classe_nom=$data[0][1];
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
       $accademie=trim($data[$i][8]);
}


// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
$eleveT=recupEleve($idclasse); // recup liste eleve


for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, 
	// regime, civ_1, civ_2
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

	$pdf->AddPage();
	$pdf->SetTitle("Fiche Scolaire Brevet - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Fiche Scolaire Brevet"); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	// Debut création PDF
	// mise en place des coordonnées
	$policy0=12;
	$policy1=10;
	$policy2=13;
	$policy3=9;

	$pdf->SetFont('Arial','',$policy1);
	$xcoor=1;
	$ycoor=1;
	$hCadre1=31;

	$pdf->SetXY($xcoor,$ycoor);
	$pdf->MultiCell(208,$hCadre1,'',1,'L',0); // cadre du haut
	$ln=1; $pdf->SetXY($xcoor+2,$ycoor+$ln);
	$pdf->WriteHTML("ACADEMIE: $accademie ");
	$ln+=4; $pdf->SetXY($xcoor+2,$ycoor+$ln);
	$pdf->WriteHTML("Département: $postal ");
	$ln+=4; $pdf->SetXY($xcoor+2,$ycoor+$ln);
	$pdf->WriteHTML("NOM: $nomEleve ");
	$ln+=4; $pdf->SetXY($xcoor+2,$ycoor+$ln);
	$pdf->WriteHTML("Prénom(s): $prenomEleve ");
	$ln+=4; $pdf->SetXY($xcoor+2,$ycoor+$ln);
	$pdf->WriteHTML("Né(e) le : $datenaissance ");
	$ln+=4; $pdf->SetXY($xcoor+2,$ycoor+$ln);
//	$pdf->WriteHTML("à :  ");

	$pdf->SetFont('Arial','',14);
	$pdf->SetXY($xcoor+80,$ycoor+2);  // emplacement du titre
	$pdf->WriteHTML("<b>FICHE SCOLAIRE BREVET</b>");
	$pdf->SetFont('Arial','',$policy1+3);
	$pdf->SetXY($xcoor+90,$ycoor+7);
	$pdf->WriteHTML("<b>Série Collège</b>");
	$pdf->SetFont('Arial','',$policy1);

	$pdf->SetXY($xcoor+160,$ycoor+12);  // emplacement session
	$pdf->WriteHTML("SESSION : Juin ".date("Y"));

	
	$pdf->SetXY($xcoor+50,$ycoor+20); 
	$pdf->MultiCell(158,11,'',1,'L',0); 
	$pdf->SetXY($xcoor+100,$ycoor+21);
	$pdf->WriteHTML("<b>Classe de troisième de collège</b>");
	$pdf->SetXY($xcoor+90,$ycoor+25);
	$pdf->WriteHTML("Etablissement fréquenté : $nom_etablissement ");

	$pdf->SetFont('Arial','',$policy0);

	$pdf->SetXY($xcoor,$ycoor+31);  // emplacement cadre disciplines
	$tDisc=50;
	$hDisc=20;
	$pdf->MultiCell($tDisc,$hDisc,'',1,'L',0); 
	$pdf->SetXY($xcoor+4,$ycoor+31+10); 
	$pdf->WriteHTML("DISCIPLINES");

	$nl=0;
	$pdf->SetXY($xcoor+$tDisc,$ycoor+$hCadre1);
	$pdf->MultiCell(20,$hDisc,'',1,'C',0);
	$pdf->SetFont('Arial','',$policy3);
	$pdf->SetXY($xcoor+$tDisc+2,$ycoor+$hCadre1);
	$pdf->WriteHTML("NOTE");
	$nl+=4;$pdf->SetXY($xcoor+$tDisc+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("moyenne ");
	$nl+=4;$pdf->SetXY($xcoor+$tDisc+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("de la ");
	$nl+=4;$pdf->SetXY($xcoor+$tDisc+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("classe ");
	$nl+=4;$pdf->SetXY($xcoor+$tDisc+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("(0 à 20)");

	$xcoord0=$xcoor+$tDisc+20;
	$nl=0;
	$pdf->SetXY($xcoord0,$ycoor+$hCadre1);
	$pdf->MultiCell(20,$hDisc,'',1,'C',0);
	$pdf->SetFont('Arial','',$policy3);
	$pdf->SetXY($xcoord0+2,$ycoor+$hCadre1);
	$pdf->WriteHTML("NOTE");
	$nl+=4;$pdf->SetXY($xcoord0+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("moyenne ");
	$nl+=4;$pdf->SetXY($xcoord0+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("de  ");
	$nl+=4;$pdf->SetXY($xcoord0+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("l'élève ");
	$nl+=4;$pdf->SetXY($xcoord0+2,$ycoor+$hCadre1+$nl);
	$pdf->WriteHTML("(0 à 20)");

	$xcoord0+=20;
	$pdf->SetXY($xcoord0,$ycoor+$hCadre1);  // com 4ieme
	$pdf->MultiCell(80,$hDisc,'',1,'C',0);

	$pdf->SetFont('Arial','',$policy0);
	$pdf->SetXY($xcoord0+1,$ycoor+$hCadre1+15);
	$pdf->WriteHTML("<b>APPRECIATION DES PROFESSEURS</b>");
	$pdf->SetFont('Arial','',$policy3);
	
	$pdf->SetXY($xcoord0+80,$ycoor+$hCadre1);
	$pdf->MultiCell(38,$hDisc,'',1,'C',0);
	$pdf->SetXY($xcoord0+80+2,$ycoor+$hCadre1);
	$pdf->WriteHTML("NOTE GLOBALE");
	$pdf->SetXY($xcoord0+80+2,$ycoor+$hCadre1+4);
	$pdf->WriteHTML("affectée du ");
	$pdf->SetXY($xcoord0+80+2,$ycoor+$hCadre1+8);
	$pdf->WriteHTML("coefficient");
	$pdf->SetXY($xcoord0+80+2,$ycoor+$hCadre1+12);
	$pdf->WriteHTML("3ième à option");
	$pdf->SetXY($xcoord0+80+2,$ycoor+$hCadre1+16);
	$pdf->WriteHTML("LV2");



	//--------- section matiere
	$ycoor+=$hCadre1+$hDisc;
	$tabmatiere = array(	
		"Français",
		"Mathématiques",
		"Premiere Langue vivante",
		"Sciences de la vie<br>et de la terre",
		"Physique-chimie",
		"Education physique <br> et sportive",
		"Arts plastiques",
		"Education musicale",
		"Technologie",
		"Deuxième langue vivante",
		"Option facultative",
		"Vie Scolaire"
	);
	foreach ($tabmatiere as $matiere) {
		$xcoord0=1;
		$hauteur=13;
		
		$pdf->SetXY($xcoord0,$ycoor);
		$pdf->MultiCell($tDisc,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0,$ycoor);
		$pdf->SetFont('Arial','',$policy1);
		$pdf->WriteHTML("$matiere");
		$pdf->SetXY($xcoord0+$tDisc,$ycoor);
		$pdf->MultiCell(20,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0+$tDisc+20,$ycoor);
		$pdf->MultiCell(20,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0+$tDisc+40,$ycoor);
		$pdf->MultiCell(80,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0+$tDisc+40+80,$ycoor);
		$pdf->MultiCell(38,$hauteur,'',1,'C',0);
		
		$ycoor+=$hauteur;
	}

	$pdf->SetXY($xcoord0,$ycoor);
	$pdf->MultiCell($tDisc+20+20+70+48,5,'',1,'C',0);
	$pdf->SetXY($xcoord0+3,$ycoor+0.5);
	$pdf->WriteHTML("<b>A titre indicatif</b>");

	$tabmatiere = array(	
		"Histoire-Géographie <br> Education Civique",
	);

	$ycoor+=5;
	foreach ($tabmatiere as $matiere) {
		$xcoord0=1;
		
		$pdf->SetXY($xcoord0,$ycoor);
		$pdf->MultiCell($tDisc,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0,$ycoor);
		$pdf->SetFont('Arial','',$policy1);
		$pdf->WriteHTML("Histoire-Géographie");
		$pdf->SetXY($xcoord0,$ycoor+5);
		$pdf->WriteHTML("Education Civique");
		$pdf->SetXY($xcoord0+$tDisc,$ycoor);
		$pdf->MultiCell(20,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0+$tDisc+20,$ycoor);
		$pdf->MultiCell(20,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0+$tDisc+40,$ycoor);
		$pdf->MultiCell(80,$hauteur,'',1,'C',0);
		$pdf->SetXY($xcoord0+$tDisc+40+80,$ycoor);
		$pdf->MultiCell(38,$hauteur,'',1,'C',0);
		$ycoor+=$hauteur;
	}

	$pdf->SetXY($xcoord0,$ycoor);
	$pdf->MultiCell($tDisc+20+20+70,35,'',1,'C',0);	

	$pdf->SetXY($xcoord0,$ycoor+2);
	$pdf->WriteHTML("<b>Avis du chef d'établissement : </b>");


	$pdf->SetXY($xcoord0+$tDisc+20+20+70,$ycoor);
	$pdf->MultiCell(48,35,'',1,'C',0);	
	$pdf->SetXY($xcoord0+$tDisc+20+20+70+2,$ycoor+2);
	$pdf->WriteHTML("<b>DECISION : </b>");



	$pdf->SetFont('Arial','',$policy0);



	if ($_POST["type_pdf"] == "pers"){
		$classe_nom=TextNoAccent($classe_nom);
		$classe_nom=TextNoCarac($classe_nom);
		$classe_nom=preg_replace('/\//',"_",$classe_nom);
		if (!is_dir("./data/pdf_bull/brevet_$classe_nom")) { mkdir("./data/pdf_bull/brevet_$classe_nom"); }
		$fichier=urlencode($fichier);
		$fichier="./data/pdf_bull/brevet_$classe_nom/brevet_".$nomEleve."_".$prenomEleve.".pdf";
		@unlink($fichier); // destruction avant creation
		$pdf->output('F',$fichier);
		$pdf->close();
		$pdf=new PDF();

	}



} // fin du for on passe à l'eleve suivant

if ($_POST["type_pdf"] == "global"){
	$classe_nom=TextNoAccent($classe_nom);
	$classe_nom=TextNoCarac($classe_nom);
	$classe_nom=preg_replace('/\//',"_",$classe_nom);
	$fichier="./data/pdf_bull/brevet_".$classe_nom.".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);
	$bttexte=LANGPARAM33;
}

if ($_POST["type_pdf"] == "pers"){
	include_once('./librairie_php/pclzip.lib.php');
	@unlink('./data/pdf_bull/brevet_'.$classe_nom.'.zip');
	$archive = new PclZip('./data/pdf_bull/brevet_'.$classe_nom.'.zip');
	$archive->create('./data/pdf_bull/brevet_'.$classe_nom);
	$fichier='./data/pdf_bull/brevet_'.$classe_nom.'.zip';
	$bttexte="Récupérer les fichiers PDF";
	@nettoyage_repertoire('./data/pdf_bull/brevet_'.$classe_nom);
	@rmdir('./data/pdf_bull/brevet_'.$classe_nom);
}
?>
<br />
<ul><font class=T2>
<?php print "Fiche Brevet Collège " ?> : <?php print $classe_nom?><br> <br>
</font>
<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul>

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
