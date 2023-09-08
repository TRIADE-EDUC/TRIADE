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
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Evalution et notation du jury</font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();


if (isset($_POST["rien"])) {
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');
	$pdf=new PDF();  // declaration du constructeur
	$pdf->AddPage();
	$pdf->SetTitle("Evaluation et notation du jury");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Evaluation et notation du jury"); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	$annee=$_POST["annee"];
	$titre=html_vers_text($_POST["titre"]);
	$directeur=recherche_personne($_POST["directeur"]);
	$jury=html_vers_text($_POST["jury"]);
	$sujet=html_vers_text($_POST["sujet"]);
	$auteur=html_vers_text($_POST["auteur"]);
	$commentaire=html_vers_text($_POST["commentaire"]);
	$note=$_POST["note"];
	$listjury=explode(',',$jury);
	

	$X=10;
	$Y=40;
	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','B',7);
	$pdf->MultiCell(100,3,"Master en Stratégie et décision publique et politique",0,'L',0);
	$pdf->SetXY($X,$Y+=3);
	$pdf->MultiCell(100,3,"Année : $annee",0,'L',0);
	$pdf->SetXY($X,$Y+=3);
	$pdf->MultiCell(200,3,"$titre",0,'L',0);
	$pdf->SetXY($X,$Y+=3);
	$pdf->MultiCell(200,3,"Directeur : $directeur",0,'L',0);


	$pdf->SetFont('Arial','I',12);
	$pdf->SetXY(0,$Y+=15);
	$pdf->MultiCell(210,3,"Année $annee",0,'C',0);
	$pdf->SetXY(0,$Y+=5);
	$pdf->MultiCell(210,3,"$titre",0,'C',0);

	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY(0,$Y+=15);
	$pdf->MultiCell(210,3,"Evaluation et notation du jury",0,'C',0);

	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY($X,$Y+=15);
	$pdf->MultiCell(195,3,"Composition du jury :",0,'L',0);
	$pdf->SetFont('Arial','',10);
	foreach($listjury as $key=>$value) {
		$pdf->SetXY($X+10,$Y+=5);
		$value=trim($value);
		$pdf->MultiCell(195,3,"$value",0,'L',0);
	}

	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY($X,$Y+=15);
	$pdf->MultiCell(195,3,"Les auteurs : ",0,'L',0);
	$pdf->SetFont('Arial','I',10);
	$pdf->SetXY($X+23,$Y);
	$pdf->MultiCell(171,3,"$auteur",0,'L',0);

 
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY($X,$Y+=15);
	$pdf->MultiCell(195,3,"Appréciation et Evaluation du jury :",0,'L',0);

	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($X,$Y+=10);
	$commentaire=preg_replace('/\n/',' ',$commentaire);
	$pdf->MultiCell(195,4,"$commentaire",0,'L',0);

	$nbcar=strlen($commentaire);

	$nbligne=$nbcar/100;
	$nbY=$nbligne*3.3;

	$pdf->SetFont('Arial','',13);
	$pdf->SetXY($X,$Y+=10+$nbY);
	$pdf->MultiCell(185,3,"Notation : ",0,'R',0);
	$pdf->SetXY($X,$Y+=7);
	$pdf->MultiCell(185,3,"$note/20 ",0,'R',0);


	$fic="./data/pdf_bull/evaluation_".$_SESSION["id_pers"];
	$fichier="./data/pdf_bull/evaluation_".$_SESSION["id_pers"].$_SESSION["id_pers"].".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);
	$pdf->close();

	print "<center><input type=button onclick=\"open('visu_pdf_id.php?id=$fic','_blank','');\" value=\"Récuperation de l'évaluation\"  class='BUTTON'></center>";
}	



Pgclose();
?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
