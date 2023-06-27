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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Commentaire des enseignants" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
$anneeScolaire=$_GET['annee_scolaire'];
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"visadirection")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	if (PROFPACCESVISADIRECTION == "oui") {
		validerequete("menuprof");
		verif_profp_class($_SESSION["id_pers"],$_GET["idclasse"]);
	}else{
		validerequete("menuadmin");
	}
}
$cnx=cnx();

$idclasse=$_GET["idclasse"];
$trimestre=$_GET["tri"];
$nomClasse=chercheClasse_nom($idclasse);

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');


$pdf=new PDF();  // declaration du constructeur

$pdf->AddPage();
$pdf->SetTitle("Commentaire -");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Commentaire de classe "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$X=0;
$Y=5;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(210,6,"PREPARATION AU CONSEIL DE CLASSE",0,'C',0);
$pdf->SetFont('Arial','',9);
$pdf->SetXY($X+=5,$Y+=15);
$pdf->SetFillColor(230,230,255);
$pdf->RoundedRect($X, $Y, 120, 25, 3.5, 'DF');
$pdf->SetFillColor(255);
$pdf->SetXY($X+2,$Y+=2);
$pdf->MultiCell(130,3,"Objet : Commentaire pour la classe $nomClasse ",0,'L',0);
$pdf->SetXY($X+2,$Y+=5);
$pdf->MultiCell(130,3,"Trimestre : $trimestre / $anneeScolaire ",0,'L',0);
$pdf->SetXY($X+2,$Y+=5);
$pdf->MultiCell(130,3,"Informations :  ",0,'L',0);
$pdf->SetXY($X+2,$Y+=5);

$Y+=15;
$hauteurMatiere=18;

$ordre=ordre_matiere_visubull($idclasse);

for($i=0;$i<count($ordre);$i++) {
	$idMatiere=$ordre[$i][0];
	$matiere=chercheMatiereNom($idMatiere);
        $profAff=recherche_personne($ordre[$i][1]);

	if (verifsousmatierebull($idMatiere)) { continue; }

	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(60,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($X+1,$Y+2);
	$pdf->MultiCell(60,3,$matiere,0,'L',0);
	
	$pdf->SetXY($X+60,$Y);
	$pdf->MultiCell(40,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($X+61,$Y+2);
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(40,3,$profAff,0,'L',0);

	$commentaire=cherche_com_classe_matiere($idMatiere,$trimestre,$idclasse,$anneeScolaire);
	$commentaire=preg_replace('/\n/'," ",$commentaire);
	$pdf->SetXY($X+100,$Y);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(100,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($X+101,$Y+1);
	$pdf->MultiCell(100,3,$commentaire,0,'L',0);

	$Y+=$hauteurMatiere;

	if ($Y >= 250) {
		$Y=10;
		$pdf->AddPage();
	}
}




$fichier="./data/pdf_bull/commentaire_${idClasse}_${trimestre}_.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;
?>
<br><br>
<center>
<?php 
$url="visu_pdf_scolaire.php";
?>
<table><tr><td><input type=button onclick="open('<?php print $url?>?id=<?php print $fichier?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td><td><script>buttonMagicRetour('visa_direction.php','_self')</script></td></tr></table>
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
